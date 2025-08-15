<?php

declare(strict_types=1);

namespace Bone\BoneDoctrine\Traits;

use Bone\BoneDoctrine\Attributes\Cast;
use Bone\BoneDoctrine\Attributes\Visibility;
use DateTimeInterface;
use Del\Form\Field\Attributes\Field;
use Del\Form\Field\Transformer\DateTimeTransformer;
use Doctrine\ORM\Mapping as ORM;

trait HasExpiryDate
{
    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Field('datetime|required')]
    #[Visibility('all')]
    #[Cast(transformer: new DateTimeTransformer('D d M Y H:i'))]
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
