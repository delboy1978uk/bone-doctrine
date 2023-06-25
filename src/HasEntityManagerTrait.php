<?php

namespace Bone\BoneDoctrine;

use Doctrine\ORM\EntityManager;

/**
 * @deprecated use Bone\BoneDoctrine\Traits\HasEntityManagerTrait
 */
trait HasEntityManagerTrait
{
    /** @var EntityManager $entityManager */
    private $entityManager;

    /**
     * @return EntityManager
     */
    public function getEntityManager(): EntityManager
    {
        return $this->entityManager;
    }

    /**
     * @param EntityManager $entityManager
     */
    public function setEntityManager(EntityManager $entityManager): void
    {
        $this->entityManager = $entityManager;
    }
}
