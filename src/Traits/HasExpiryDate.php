<?php

declare(strict_types=1);

namespace Bone\BoneDoctrine\Traits;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

trait HasExpiryDate
{
    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTimeInterface $expiryDate = null;

    public function getExpiryDate(): ?DateTimeInterface
    {
        return $this->expiryDate;
    }

    public function setExpiryDate(?DateTimeInterface $expiryDate): void
    {
        $this->expiryDate = $expiryDate;
    }
}
