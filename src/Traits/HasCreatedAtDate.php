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
trait HasCreatedAtDate
{
    #[ORM\Column(type: 'datetime_immutable')]
    #[Visibility('index,view')]
    #[Cast(transformer: new DateTimeTransformer('D d M Y H:i'))]
    private DateTimeInterface $createdAt;

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): void
    {
        $this->createdAt = new DateTimeImmutable('now', new DateTimeZone('UTC'));
    }
}
