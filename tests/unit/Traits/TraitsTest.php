<?php

namespace Bone\Test\BoneDoctrine\Command;

use Bone\BoneDoctrine\Traits\HasCreatedAtDate;
use Bone\BoneDoctrine\Traits\HasEmail;
use Bone\BoneDoctrine\Traits\HasEntityManagerTrait;
use Bone\BoneDoctrine\Traits\HasId;
use Bone\BoneDoctrine\Traits\HasSettings;
use Bone\BoneDoctrine\Traits\HasUpdatedAtDate;
use Codeception\Test\Unit;
use Doctrine\ORM\EntityManagerInterface;

class FakeClass
{
    use HasCreatedAtDate;
    use HasEmail;
    use HasEntityManagerTrait;
    use HasId;
    use HasSettings;
    use HasUpdatedAtDate;
}

class TraitsTest extends Unit
{
    public function testTraits()
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $class = new FakeClass();
        $class->setId(2014);
        $class->setEmail('man@work.com');
        $class->setEntityManager($entityManager);
        $class->setCreatedAt();
        $class->setUpdatedAt();
        $class->setSettings([
            'xxx' => 'yyy',
        ]);

        self::assertInstanceOf(\DateTimeInterface::class, $class->getCreatedAt());
        self::assertInstanceOf(\DateTimeInterface::class, $class->getUpdatedAt());
        self::assertInstanceOf(EntityManagerInterface::class, $class->getEntityManager());
        self::assertEquals(2014, $class->getId());
        self::assertEquals('man@work.com', $class->getEmail());
        self::assertIsArray($class->getSettings());
        self::assertArrayHasKey('xxx', $class->getSettings());
    }
}
