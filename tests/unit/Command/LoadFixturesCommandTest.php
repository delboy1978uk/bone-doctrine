<?php

namespace Bone\Test\BoneDoctrine\Command;

use Bone\BoneDoctrine\Command\LoadFixturesCommand;
use Codeception\Test\Unit;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ObjectManager;
use ReflectionClass;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FakeFixture implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        // do nothing!
    }
}

class LoadFixturesCommandTest extends Unit
{
    public function testNoFixtures()
    {
        $em = $this->createMock(EntityManager::class);
        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);
        $command = new LoadFixturesCommand($em);
        $mirror = new ReflectionClass($command);
        $method = $mirror->getMethod('execute');
        $method->setAccessible(true);
        $result = $method->invoke($command, $input, $output);
        $this->assertEquals(0, $result);
    }

    public function testWithFixtures()
    {
        $eventManager = $this->createMock(EventManager::class);
        $em = $this->createMock(EntityManager::class);
        $em->method('getEventManager')->willReturn($eventManager);
        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);
        $command = new LoadFixturesCommand($em, [FakeFixture::class]);
        $mirror = new ReflectionClass($command);
        $method = $mirror->getMethod('execute');
        $method->setAccessible(true);
        $result = $method->invoke($command, $input, $output);
        $this->assertEquals(0, $result);
    }
}
