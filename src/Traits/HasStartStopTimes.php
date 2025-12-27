<?php

declare(strict_types=1);

namespace Bone\BoneDoctrine\Traits;

use Bone\BoneDoctrine\Attributes\Cast;
use Bone\BoneDoctrine\Attributes\Visibility;
use DateTimeInterface;
use Del\Form\Field\Transformer\DateTimeTransformer;
use Doctrine\ORM\Mapping as ORM;

trait HasStartStopTimes
{
    #[ORM\Column(type: 'datetime_immutable')]
    #[Visibility('index,view')]
    #[Cast(transformer: new DateTimeTransformer('D d M Y H:i'))]
    private DateTimeInterface $startedAt;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Visibility('index,view')]
    #[Cast(transformer: new DateTimeTransformer('D d M Y H:i'))]
    private DateTimeInterface $stoppedAt;

    public function getStartedAt(): DateTimeInterface
    {
        return $this->startedAt;
    }

    public function setStartedAt(DateTimeInterface $startedAt): void
    {
        $this->startedAt = $startedAt;
    }

    public function getStoppedAt(): DateTimeInterface
    {
        return $this->stoppedAt;
    }

    public function setStoppedAt(DateTimeInterface $stoppedAt): void
    {
        $this->stoppedAt = $stoppedAt;
    }
}
