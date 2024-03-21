<?php

declare(strict_types=1);

namespace Bone\BoneDoctrine\Traits;

use Doctrine\ORM\Mapping as ORM;

trait HasLocationCoordinates
{
    #[ORM\Column(type: 'float', precision: 17, scale: 15, nullable: true)]
    private float $longitude = 0.0;

    #[ORM\Column(type: 'float', precision: 17, scale: 15, nullable: true)]
    private float $latitude = 0.0;

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): void
    {
        $this->longitude = $longitude;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): void
    {
        $this->latitude = $latitude;
    }
}
