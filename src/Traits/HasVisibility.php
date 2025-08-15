<?php

declare(strict_types=1);

namespace Bone\BoneDoctrine\Traits;

use Bone\BoneDoctrine\Attributes\Visibility;
use Del\Form\Field\Attributes\Field;
use Doctrine\ORM\Mapping as ORM;

trait HasVisibility
{
    #[ORM\Column(type: 'boolean')]
    #[Field('checkbox')]
    #[Visibility('all')]
    private bool $visible = true;

    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function setVisible(bool $visible): void
    {
        $this->visible = $visible;
    }
}
