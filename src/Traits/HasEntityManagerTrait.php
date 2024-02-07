<?php

namespace Bone\BoneDoctrine\Traits;

use Doctrine\ORM\EntityManagerInterface;

trait HasEntityManagerTrait
{
    private EntityManagerInterface $entityManager;

    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    public function setEntityManager(EntityManagerInterface $entityManager): void
    {
        $this->entityManager = $entityManager;
    }
}
