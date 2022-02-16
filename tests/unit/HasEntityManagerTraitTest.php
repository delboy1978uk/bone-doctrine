<?php

namespace Bone\Test\BoneDoctrine;

use Barnacle\Container;
use Barnacle\Exception\NotFoundException;
use Bone\BoneDoctrine\BoneDoctrinePackage;
use Bone\BoneDoctrine\HasEntityManagerTrait;
use Bone\Console\ConsoleApplication;
use Bone\Console\ConsolePackage;
use Bone\Db\DbPackage;
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
