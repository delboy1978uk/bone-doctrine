<?php

namespace Bone\BoneDoctrine;

use Doctrine\ORM\EntityManager;

interface EntityManagerAwareInterface
{
    /**
     * @return EntityManager
     */
    public function getEntityManager(): EntityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function setEntityManager(EntityManager $entityManager): void;
}