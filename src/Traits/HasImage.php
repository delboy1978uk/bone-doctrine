<?php

declare(strict_types=1);

namespace Bone\BoneDoctrine\Traits;

use Del\Form\Field\Attributes\Field;
use Doctrine\ORM\Mapping as ORM;

trait HasImage
{
    #[ORM\Column()]
    #[Field('string')]
    private string $imagePath = '';

    public function getImagePath(): string
    {
        return $this->imagePath;
    }

    public function setImagePath(string $imagePath): void
    {
        $this->imagePath = $imagePath;
    }
}
