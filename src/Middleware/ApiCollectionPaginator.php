<?php

declare(strict_types=1);

namespace Bone\BoneDoctrine\Middleware;

use Doctrine\ORM\EntityManagerInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

abstract class ApiCollectionPaginator implements MiddlewareInterface
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
        $totalPages = (int) ceil($totalRecords / $limit);
        $uri = $request->getUri();

        $hal = [
            '_links' => [
                'self' => [
                    'href' => $uri->getScheme() . '://' . $uri->getHost() . $uri->getPath(),
                ],
                'first' => [
                    'href' => $uri->getScheme() . '://' . $uri->getHost() . $uri->getPath(),
                ],
            ],
        ];

        if ($page !== 1) {
            $hal['_links']['prev'] = [
                'href' => $uri->getScheme() . '://' . $uri->getHost() . $uri->getPath() . '?page=' . ($page - 1),
            ];
        }

        if ($page !== $totalPages) {
            $hal['_links']['next'] = [
                'href' => $uri->getScheme() . '://' . $uri->getHost() . $uri->getPath() . '?page=' . ($page + 1),
            ];
        }

        $hal['_links']['last'] = [
            'href' => $uri->getScheme() . '://' . $uri->getHost() . $uri->getPath() . '?page=' . $totalPages,
        ];

        $hal['_embedded'] = [];

        foreach ($entities as $entity) {
            $hal['_embedded'][] = $entity->toArray();
        }

        $hal['totalPages'] = $totalPages;
        $hal['totalRecords'] = $totalRecords;
        /** @todo add _self links for entities in collection */
        foreach ($hal['_embedded'] as $key => $value) {
            $hal['_embedded'][$key]['_links'] = [
                'self' => [
                    'href' => $uri->getScheme() . '://' . $uri->getHost() . $uri->getPath() . '/' . $value['id'],
                ],
            ];
        }

        $response = new JsonResponse($hal);
        $response = $response->withHeader('Content-Type', 'application/hal+json');

        return $response;
    }
}
