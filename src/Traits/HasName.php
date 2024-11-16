<?php

declare(strict_types=1);

namespace Bone\BoneDoctrine\Traits;

use Doctrine\ORM\Mapping as ORM;

trait HasName
{
    #[ORM\Column()]
    private string $name = '';

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
