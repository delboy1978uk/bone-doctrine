<?php

namespace Bone\Test\BoneDoctrine\Command;

use Bone\BoneDoctrine\Traits\HasCreatedAtDate;
use Bone\BoneDoctrine\Traits\HasDeletedAtDate;
use Bone\BoneDoctrine\Traits\HasEmail;
use Bone\BoneDoctrine\Traits\HasEntityManagerTrait;
use Bone\BoneDoctrine\Traits\HasExpiryDate;
use Bone\BoneDoctrine\Traits\HasId;
use Bone\BoneDoctrine\Traits\HasImage;
use Bone\BoneDoctrine\Traits\HasName;
use Bone\BoneDoctrine\Traits\HasPrivacy;
use Bone\BoneDoctrine\Traits\HasSettings;
use Bone\BoneDoctrine\Traits\HasTelephone;
use Bone\BoneDoctrine\Traits\HasUpdatedAtDate;
use Bone\BoneDoctrine\Traits\HasURL;
use Bone\BoneDoctrine\Traits\HasURLSlug;
use Bone\BoneDoctrine\Traits\HasVisibility;
use Codeception\Test\Unit;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class FakeClass
{
    use HasCreatedAtDate;
    use HasDeletedAtDate;
    use HasEmail;
    use HasEntityManagerTrait;
    use HasId;
    use HasSettings;
    use HasUpdatedAtDate;
    use HasExpiryDate;
    use HasImage;
    use HasName;
    use HasPrivacy;
    use HasTelephone;
    use HasURL;
    use HasURLSlug;
    use HasVisibility;
}

class TraitsTest extends Unit
{
    public function testTraits()
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $class = new FakeClass();
        $class->setId(2014);
        $class->setEmail('man@work.com');
        $class->setUrl('https://cia.gov');
        $class->setUrlSlug('some-slug');
        $class->setTelephone('00441415568765');
        $class->setImage('/path/to/image');
        $class->setName('mr man');
        $class->setPrivate(true);
        $class->setVisible(true);
        $class->setEntityManager($entityManager);
        $class->setCreatedAt();
        $class->setUpdatedAt();
        $class->setDeletedAt();
        $class->setExpiryDate(new DateTime());
        $class->setSettings([
            'xxx' => 'yyy',
        ]);

        self::assertInstanceOf(\DateTimeInterface::class, $class->getCreatedAt());
        self::assertInstanceOf(\DateTimeInterface::class, $class->getUpdatedAt());
        self::assertInstanceOf(\DateTimeInterface::class, $class->getExpiryDate());
        self::assertInstanceOf(\DateTimeInterface::class, $class->getDeletedAt());
        self::assertInstanceOf(EntityManagerInterface::class, $class->getEntityManager());
        self::assertEquals(2014, $class->getId());
        self::assertEquals('man@work.com', $class->getEmail());
        self::assertEquals('/path/to/image', $class->getImage());
        self::assertEquals('https://cia.gov', $class->getUrl());
        self::assertEquals('some-slug', $class->getUrlSlug());
        self::assertEquals('mr man', $class->getName());
        self::assertTrue($class->isPrivate());
        self::assertTrue($class->isVisible());
        self::assertIsArray($class->getSettings());
        self::assertArrayHasKey('xxx', $class->getSettings());
    }
}
