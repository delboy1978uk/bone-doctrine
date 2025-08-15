<?php

declare(strict_types=1);

namespace Bone\BoneDoctrine\Traits;

use Bone\BoneDoctrine\Attributes\Visibility;
use Del\Form\Field\Attributes\Field;
use Doctrine\ORM\Mapping as ORM;

trait HasImage
{
    #[ORM\Column()]
    #[Field('string')]
    #[Visibility('noindex')]
    private string $image = '';

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $imagePath): void
    {
        $this->image = $imagePath;
    }
}
