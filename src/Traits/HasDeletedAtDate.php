<?php

declare(strict_types=1);

namespace Bone\BoneDoctrine\Traits;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;

#[ORM\HasLifecycleCallbacks]
trait HasDeletedAtDate
{
    #[ORM\Column(type: 'datetime_immutable', nullable:true)]
    private ?DateTimeImmutable $deletedAt = null;

    public function getDeletedAt(): DateTimeInterface
    {
        return $this->deletedAt;
    }

    #[ORM\PreUpdate]
    public function setDeletedAt(): void
    {
        $this->deletedAt = new DateTimeImmutable('now', new DateTimeZone('UTC'));
    }
}
