<?php

declare(strict_types=1);

namespace Bone\BoneDoctrine\Service;

use Bone\BoneDoctrine\Collection\ApiCollection;
use Bone\BoneDoctrine\Traits\Pagination;
use Bone\Contracts\Service\RestServiceInterface;
use Bone\Exception;
use Del\Form\Exception\FormValidationException;
use Del\Form\Factory\FormFactory;
use Del\Form\FormInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Psr\Http\Message\ServerRequestInterface;

abstract class RestService implements RestServiceInterface
{
    use Pagination;

    protected ObjectRepository $repository;
    protected FormInterface $form;

    public function __construct(private EntityManagerInterface $entityManager)
    {
        $class = $this->getEntityClass();
        $this->repository = $entityManager->getRepository($class);
        $factory = new FormFactory();
        $entity = new $class();
        $this->form = $factory->createFromEntity('rest', $entity);
    }


    abstract public function getEntityClass(): string;

    public function index(ServerRequestInterface $request): ApiCollection
    {
        return $this->paginate($request, $this->repository);
    }

    public function post(array $data): mixed
    {
        $this->form->populate($data);
        $this->validateForm($this->form);
        $class = $this->getEntityClass();
        $entity = new $class();
        $entity->populate($this->form);
        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }

    public function get(ServerRequestInterface $request): mixed
    {
        $id = $request->getAttributes()['id'];
        $record = $this->repository->find($id);

        if (!$record) {
            throw new Exception(Exception::GHOST_SHIP, 404);
        }

        return $record;
    }

    public function patch(ServerRequestInterface $request): mixed
    {
        $record = $this->get($request);
        $this->form->populate($request->getParsedBody());
        $this->validateForm($this->form);
        $record->populate($this->form);
        $this->entityManager->flush();

        return $record;
    }

    public function delete(ServerRequestInterface $request): void
    {
        $record = $this->get($request);
        $this->entityManager->remove($record);
        $this->entityManager->flush();
    }

    private function validateForm(FormInterface $form): void
    {
        if ($form->isValid()) {
            return;
        }

        $errors = $form->getErrorMessages();

        throw new FormValidationException($errors);
    }
}
