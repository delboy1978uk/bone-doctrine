<?php

declare(strict_types=1);

namespace Bone\BoneDoctrine\Traits;

use Del\Form\Field\Attributes\Field;
use Doctrine\ORM\Mapping as ORM;

trait HasURLSlug
{
    #[ORM\Column(length: 50)]
    #[Field('string|max:50')]
    private string $urlSlug = '';

    public function getUrlSlug(): string
    {
        return $this->urlSlug;
    }

    public function setUrlSlug(string $urlSlug): void
    {
        $this->urlSlug = $urlSlug;
    }
}
