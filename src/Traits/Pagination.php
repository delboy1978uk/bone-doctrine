<?php

declare(strict_types=1);

namespace Bone\BoneDoctrine\Traits;

use Bone\Application;
use Bone\BoneDoctrine\Collection\ApiCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectRepository;
use Psr\Http\Message\ServerRequestInterface;

trait Pagination
{
    public function paginate(ServerRequestInterface $request, ObjectRepository $repository,  array $where = [], $orderBy = []): ApiCollection
    {
        $params = $request->getQueryParams();
        $page = isset($params['page']) ? (int) $params['page'] : 1;
        $limit = isset($params['limit']) ? (int) $params['limit'] : 25;
        $offset = ($page *  $limit) - $limit;
        $collection = $repository->findBy($where, $orderBy, $limit, $offset);
        $totalRecords = $repository->count($where);
        $totalPages = (int) ceil($totalRecords / $limit);
        $uri = $request->getUri();

        return new ApiCollection($collection, $uri, $page, $totalPages, $totalRecords);
    }
}
