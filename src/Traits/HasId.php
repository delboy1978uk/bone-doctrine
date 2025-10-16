<?php

declare(strict_types=1);

namespace Bone\BoneDoctrine\Traits;

use Bone\BoneDoctrine\Attributes\Visibility;
use Doctrine\ORM\Mapping as ORM;

trait HasId
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    #[Visibility('index')]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
}
