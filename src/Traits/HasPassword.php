<?php

declare(strict_types=1);

namespace Bone\BoneDoctrine\Traits;

use Bone\BoneDoctrine\Attributes\Visibility;
use Del\Form\Field\Attributes\Field;
use Doctrine\ORM\Mapping as ORM;

trait HasPassword
{
    #[ORM\Column()]
    #[Field('string|required')]
    #[Visibility('noindex')]
    private string $password = '';

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }
}
