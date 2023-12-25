<?php

declare(strict_types=1);

namespace Bone\BoneDoctrine\Traits;

use DateTimeImmutable;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\HasLifecycleCallbacks
 */
trait HasUpdatedAtDate
{
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTimeImmutable $updatedAt;

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdatedAt(): void
    {
        $this->updatedAt = new DateTimeImmutable('now', new DateTimeZone('UTC'));
    }
}
