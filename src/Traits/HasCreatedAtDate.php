<?php

declare(strict_types=1);

namespace Bone\BoneDoctrine\Traits;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\HasLifecycleCallbacks
 */
trait HasCreatedAtDate
{
    /**
     * @ORM\Column(type="datetime")
     */
    private ?DateTimeInterface $createdAt;

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @@ORM\PrePersist
     */
    public function setCreatedAt(): void
    {
        $this->createdAt = new DateTimeImmutable();
    }
}
