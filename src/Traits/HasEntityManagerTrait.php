<?php

namespace Bone\BoneDoctrine\Traits;

use Doctrine\ORM\EntityManagerInterface;

trait HasEntityManagerTrait
{
    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function setEntityManager(EntityManagerInterface $entityManager): void
    {
        $this->entityManager = $entityManager;
    }
}
