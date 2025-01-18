<?php

declare(strict_types=1);

namespace Bone\BoneDoctrine\Middleware;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

abstract class HtmlCollectionPaginator implements MiddlewareInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    abstract public function getEntityClass(): string;
    abstract public function getWhereCriteria(): array;
    abstract public function getOrderByCriteria(): array;
    abstract public function getRequestAttributeName(): string;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $params = $request->getQueryParams();
        $page = isset($params['page']) ? (int) $params['page'] : 1;
        $limit = isset($params['limit']) ? (int) $params['limit'] : 25;
        $offset = ($page *  $limit) - $limit;
        $entityClass = $this->getEntityClass();
        $whereCriteria = $this->getWhereCriteria();
        $orderByCriteria = $this->getWhereCriteria();
        $requestAttributeName = $this->getRequestAttributeName();
        $entities = $this->entityManager->getRepository($entityClass)->findBy($whereCriteria, $orderByCriteria, $limit, $offset);
        $totalRecords = $this->entityManager->getRepository($entityClass)->count($whereCriteria);
        $uri = $request->getUri();
        $url = $uri->getScheme() . '://' . $uri->getHost() . $uri->getPath() . '?page=:page';
        $paginator = new HtmlPaginatorRenderer($page, $url, $totalRecords, $limit);

        $request = $request->withAttribute($requestAttributeName, $entities);
        $request = $request->withAttribute('page', $page);
        $request = $request->withAttribute('totalPages', $page);
        $request = $request->withAttribute('totalRecords', $page);
        $request = $request->withAttribute('paginator', $paginator);

        return $handler->handle($request);
    }
}
