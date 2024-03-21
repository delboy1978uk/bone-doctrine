<?php

namespace Bone\Test\BoneDoctrine;

use Bone\BoneDoctrine\Traits\HasEntityManagerTrait;
use Codeception\Test\Unit;
use Doctrine\ORM\EntityManager;

class HasEntityManagerTraitTest extends Unit
{
    public function testPackage()
    {
        $class = new class {
            use HasEntityManagerTrait;
        };
        $em = $this->makeEmpty(EntityManager::class);
        $class->setEntityManager($em);
        $this->assertInstanceOf(EntityManager::class, $class->getEntityManager());
    }
}
