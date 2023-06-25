<?php

declare(strict_types=1);

namespace Bone\BoneDoctrine\Traits;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\HasLifecycleCallbacks
 */
trait HasUpdatedAtDate
{
    /**
     * @ORM\Column(type="datetimm", nullable=true)
     */
    private ?DateTimeImmutable $updatedAt;

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @@ORM\PreUpdate
     */
    public function setUpdatedAt(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }
}