<?php

declare(strict_types=1);

namespace Bone\BoneDoctrine\Http\Controller;

use Bone\Application;
use Bone\BoneDoctrine\Attributes\Cast;
use Bone\BoneDoctrine\Attributes\Visibility;
use Bone\BoneDoctrine\Traits\Pagination;
use Bone\Contracts\Service\RestServiceInterface;
use Bone\Exception;
use Bone\Http\Response\HtmlResponse;
use Bone\View\Helper\AlertBox;
use Bone\View\Helper\Paginator;
use Bone\View\ViewEngineInterface;
use Del\Form\Factory\FormFactory;
use Del\Form\Field\Submit;
use Del\Form\Form;
use Del\Form\Renderer\HorizontalFormRenderer;
use Del\SessionManager;
use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\InflectorFactory;
use Doctrine\ORM\EntityManagerInterface;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;

use function end;
use function explode;
use function implode;
use function is_array;

abstract class AdminController
{
    use Pagination;

    const NUM_PER_PAGE = 20;

    private EntityManagerInterface $entityManager;
    private Inflector $inflector;
    private RestServiceInterface $service;
    private ViewEngineInterface $view;
    private Paginator $paginator;

    public function __construct()
    {
        $container = Application::ahoy()->getContainer();
        $serviceClass = $this->getServiceClass();

        if (!$container->has($serviceClass)) {
            throw new Exception('Service not found');
        }

        $this->inflector = InflectorFactory::create()->build();
        $this->service = $container->get($serviceClass);
        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->view = $container->get(ViewEngineInterface::class);
        $this->paginator = new Paginator();
    }

    private function getProperties(): array
    {
        $className = $this->getEntityClass();

        return (new ReflectionClass($className))->getProperties();
    }

    private function getTableColumns(string $view): array
    {
        $properties = $this->getProperties();
        $columns = [];

        foreach ($properties as $property) {
            $fieldName = $property->getName();
            $attributes = $property->getAttributes(Visibility::class);

            if (count($attributes) > 0) {
                $rules = $attributes[0]->newInstance()->rules;

                if ($rules === $view) {
                    $columns[] = $fieldName;
                } else {
                    $rules = explode(',', $rules);

                    if (array_intersect($rules, ['all', $view])) {
                        $columns[] = $fieldName;
                    }
                }
            }
        }

        return $columns;
    }

    private function getPrefixes(): array
    {
        return $this->getFixes('prefix');
    }

    private function getSuffixes(): array
    {
        return $this->getFixes('suffix');
    }

    private function getTransformers(): array
    {
        return $this->getFixes('transformer');
    }

    private function getFixes(string $prefixOrSuffix): array
    {
        $properties = $this->getProperties();
        $fixes = [];

        foreach ($properties as $property) {
            $name = $property->getName();
            $attributes = $property->getAttributes(Cast::class);

            if (count($attributes) > 0) {
                $fix = $attributes[0]->newInstance()->$prefixOrSuffix;
                $fixes[$name] = null;

                if ($fix !== null) {
                    $fixes[$name] = $fix;
                }
            }
        }

        return $fixes;
    }

    private function getTitle(string $view): string
    {
        $className = $this->getEntityClass();
        $array = explode('\\', $className);
        $entityName = end($array);

        switch ($view) {
            case 'view':
                $title = 'View ' . $entityName;
                break;
            case 'edit':
                $title = 'Edit ' . $entityName;
                break;
            case 'create':
                $title = 'Create ' . $entityName;
                break;
            case 'delete':
                $title = 'Delete ' . $entityName;
                break;
            case 'index':
            default:
                $title = $this->inflector->pluralize($entityName);
                break;
        }

        return $title;
    }

    private function setFlashMessage(string $message, string $class): void
    {
        SessionManager::getInstance()->set('flash_message', [$message, $class]);
    }

    private function getFlashMessage(): string
    {
        $sm = SessionManager::getInstance();
        $message = $sm->get('flash_message');
        $sm->unset('flash_message');

        return $message ? $this->alertBox($message[0], $message[1]) : '';
    }

    private function getUrl(ServerRequestInterface $request): string
    {
        return $request->getUri()->getPath();
    }

    private function alertBox(string|array $message, string $class): string
    {
        if (is_array($message)) {
            $message = implode('<br />', $message);
        }

        return (new AlertBox())->alertBox([
            'message' => $message,
            'class' => $class,
        ]);
    }

    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $params = $request->getQueryParams();
        $limit = isset($params['limit']) ? (int) $params['limit'] : self::NUM_PER_PAGE;
        $request = $request->withQueryParams($params);
        $url = $this->getUrl($request);
        $this->paginator->setUrl($url .'?page=:page&limit=:limit');
        $this->paginator->setLimit($limit);
        $records = $this->service->index($request);
        $total = $records->getTotalRecords();
        $params = $request->getQueryParams();
        $page = array_key_exists('page', $params) ? (int) $params['page'] : 1;
        $this->paginator->setCurrentPage($page);
        $this->paginator->setPageCountByTotalRecords($total, $limit);

        $messages = $this->getFlashMessage();
        $body = $this->view->render('admin::index', [
            'records' => $records,
            'tableColumns' => $this->getTableColumns('index'),
            'title' => $this->getTitle('index'),
            'prefixes' => $this->getPrefixes(),
            'suffixes' => $this->getSuffixes(),
            'transformers' => $this->getTransformers(),
            'url' => $url,
            'messages' => $messages,
            'paginator' => $this->paginator->render(),
        ]);

        return new HtmlResponse($body, 200, ['layout' => 'layouts::bone']);
    }

    public function view(ServerRequestInterface $request): ResponseInterface
    {
        $data = [
            'record' => $this->service->get($request),
            'tableColumns' => $this->getTableColumns('view'),
            'title' => $this->getTitle('view'),
            'prefixes' => $this->getPrefixes(),
            'suffixes' => $this->getSuffixes(),
            'transformers' => $this->getTransformers(),
            'url' => $this->getUrl($request),
        ];
        $body = $this->view->render('admin::view', $data);

        return new HtmlResponse($body, 200, ['layout' => 'layouts::bone']);
    }

    public function edit(ServerRequestInterface $request): ResponseInterface
    {
        $entity = $this->service->get($request);
        $form = (new FormFactory())->createFromEntity('update', $entity);
        $form->setFormRenderer(new HorizontalFormRenderer());
        $message = '';

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();
            $form->populate($data);

            if ($form->isValid()) {
                $entity->populate($form);
                $this->entityManager->flush();
                $url = $this->getUrl($request);
                $url = str_replace('/' . $entity->getId() . '/edit', '', $url);
                $this->setFlashMessage('Successfully updated ' .$this->getEntityClass() . '.', 'success');

                return new RedirectResponse($url);
            }

            $message = $this->alertBox('There were errors with the form.', 'danger');
        }

        $body = $this->view->render('admin::edit', [
            'form' => $form,
            'message' => $message,
            'title' => $this->getTitle('edit'),
            'url' => $this->getUrl($request),
        ]);

        return new HtmlResponse($body, 200, ['layout' => 'layouts::bone']);
    }

    public function create(ServerRequestInterface $request): ResponseInterface
    {
        $entityClass = $this->getEntityClass();
        $entity = new $entityClass();
        $form = (new FormFactory())->createFromEntity('create', $entity);
        $form->setFormRenderer(new HorizontalFormRenderer());
        $message = '';

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();
            $form->populate($data);
            if ($form->isValid()) {
                $filteredData = $form->getValues();
                $this->service->post($filteredData);
                $url = $this->getUrl($request);
                $url = str_replace('/create', '', $url);
                $this->setFlashMessage('Successfully created ' .$entityClass . '.', 'success');

                return new RedirectResponse($url);
            }
            $message = $this->alertBox('There were errors with the form.', 'danger');
        }

        $body = $this->view->render('admin::create', [
            'form' => $form,
            'message' => $message,
            'title' => $this->getTitle('create'),
            'url' => $this->getUrl($request),
        ]);

        return new HtmlResponse($body, 200, ['layout' => 'layouts::bone']);
    }

    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        if ($request->getMethod() === 'POST') {
            $this->service->delete($request);
            $url = $this->getUrl($request);
            $url = \preg_replace('#\/\d+\/delete$#', '', $url);
            $this->setFlashMessage('Successfully deleted ' .$this->getEntityClass() . '.', 'success');

            return new RedirectResponse($url);
        }

        $form = new Form('delete');
        $submit = new Submit('Delete');
        $submit->setClass('btn btn-danger');
        $form->addField($submit);
        $body = $this->view->render('admin::delete', [
            'record' => $this->service->get($request),
            'tableColumns' => $this->getTableColumns('delete'),
            'title' => $this->getTitle('delete'),
            'prefixes' => $this->getPrefixes(),
            'suffixes' => $this->getSuffixes(),
            'transformers' => $this->getTransformers(),
            'url' => $this->getUrl($request),
            'form' => $form->render(),
        ]);

        return new HtmlResponse($body, 200, ['layout' => 'layouts::bone']);
    }

    abstract public function getEntityClass(): string;

    abstract public function getServiceClass(): string;
}
