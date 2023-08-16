<?php

namespace Bone\BoneDoctrine;

use Doctrine\ORM\EntityManagerInterface;

interface EntityManagerAwareInterface
{
    public function getEntityManager(): EntityManagerInterface;
    public function setEntityManager(EntityManagerInterface $entityManager): void;
}
