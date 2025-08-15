<?php

declare(strict_types=1);

namespace Bone\BoneDoctrine\Traits;

use Bone\BoneDoctrine\Attributes\Cast;
use Bone\BoneDoctrine\Attributes\Visibility;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Del\Form\Field\Transformer\DateTimeTransformer;
use Doctrine\ORM\Mapping as ORM;

#[ORM\HasLifecycleCallbacks]
trait HasUpdatedAtDate
{
    #[ORM\Column(type: 'datetime_immutable', nullable:true)]
    #[Visibility('index,view')]
    #[Cast(transformer: new DateTimeTransformer('D d M Y H:i'))]
    private ?DateTimeImmutable $updatedAt = null;

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    #[ORM\PreUpdate]
    public function setUpdatedAt(): void
    {
        $this->updatedAt = new DateTimeImmutable('now', new DateTimeZone('UTC'));
    }
}
