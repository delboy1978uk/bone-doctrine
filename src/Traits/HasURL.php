<?php

declare(strict_types=1);

namespace Bone\BoneDoctrine\Traits;

use Del\Form\Field\Attributes\Field;
use Doctrine\ORM\Mapping as ORM;

trait HasURL
{
    #[ORM\Column(length: 100)]
    #[Field('string')]
    private string $url = '';

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }
}
