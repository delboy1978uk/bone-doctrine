<?php

declare(strict_types=1);

namespace Bone\App\Http;

use Bone\Application;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ObjectRepository;

trait Pagination
{
    public function paginate(ObjectRepository $repository,  array $where = [], $orderBy = []): Collection
    {
        $request = Application::ahoy()->getGlobalRequest();
        $params = $request->getQueryParams();
        $page = isset($params['page']) ? (int) $params['page'] : 1;
        $limit = isset($params['limit']) ? (int) $params['limit'] : 25;
        $offset = ($page *  $limit) - $limit;

        return new ArrayCollection($repository->findBy($where, $orderBy, $limit, $offset));
    }
}
