<?php

declare(strict_types=1);

namespace Bone\BoneDoctrine\Traits;

use Del\Form\Field\Attributes\Field;
use Doctrine\ORM\Mapping as ORM;

trait HasPrivacy
{
    #[ORM\Column(type: 'boolean')]
    #[Field('checkbox')]
    private bool $private = false;

    public function isPrivate(): bool
    {
        return $this->private;
    }

    public function setPrivate(bool $private): void
    {
        $this->private = $private;
    }
}
