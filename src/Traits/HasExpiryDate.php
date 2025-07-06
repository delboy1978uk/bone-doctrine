<?php

declare(strict_types=1);

namespace Bone\BoneDoctrine\Traits;

use DateTimeInterface;
use Del\Form\Field\Attributes\Field;
use Doctrine\ORM\Mapping as ORM;

trait HasExpiryDate
{
    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Field('datetime|required')]
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
