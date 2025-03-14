<?php

declare(strict_types=1);

namespace Bone\BoneDoctrine\Collection;

use Doctrine\Common\Collections\ArrayCollection;
use Psr\Http\Message\UriInterface;

class ApiCollection extends ArrayCollection
{
    private int $page;
    private int $totalPages;
    private UriInterface $uri;

    public function __construct(array $elements = [], UriInterface $uri, int $page, int $totalPages, int  $totalRecords)
    {
        parent::__construct($elements);
        $this->page = $page;
        $this->totalPages = $totalPages;
        $this->uri = $uri;
    }

    public function getTotalPages(): int
    {
        return $this->totalPages;
    }

    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    public function getPage(): int
    {
        return $this->page;
    }
}
