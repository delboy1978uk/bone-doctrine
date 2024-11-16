<?php

declare(strict_types=1);

namespace Bone\BoneDoctrine\Traits;

use Doctrine\ORM\Mapping as ORM;

trait HasTelephone
{
    #[ORM\Column(length: 20)]
    private string $telephone = '';

    public function getTelephone(): string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): void
    {
        $this->telephone = $telephone;
    }
}
