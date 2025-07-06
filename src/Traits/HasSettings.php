<?php

declare(strict_types=1);

namespace Bone\BoneDoctrine\Traits;

use Doctrine\ORM\Mapping as ORM;
use function json_decode;

trait HasSettings
{
    #[ORM\Column(type: 'json')]
    protected string $settings = '{}';

    public function getSettings(): array
    {
        return json_decode($this->settings, true);
    }

    public function setSettings(array $settings): void
    {
        $this->settings = \json_encode($settings);
    }
}
