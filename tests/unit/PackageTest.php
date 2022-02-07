<?php

namespace Bone\Test\BoneDoctrine;

use Barnacle\Container;
use Bone\BoneDoctrine\BoneDoctrinePackage;
use Bone\Db\DbPackage;
use Codeception\Test\Unit;
use Doctrine\ORM\EntityManager;

class PackageTest extends Unit
{
    public function testPackage()
    {
        $container = new Container();
        $container['cache_dir'] = './tests/data';
        $container['proxy_dir'] = './tests/data';
        $container['entity_paths'] = ['./tests/data'];
        $container['db'] = [
            'driver' => 'pdo_mysql',
            'host' => '127.0.0.1',
            'dbname' => 'awesome',
            'user' => 'root',
            'password' => 'root'
        ];
        $package = new BoneDoctrinePackage();
        $package->addToContainer($container);

        $this->assertTrue($container->has(EntityManager::class));
        $this->assertInstanceOf(EntityManager::class, $container->get(EntityManager::class));
        $this->assertIsArray($package->registerConsoleCommands($container->get(EntityManager::class)));
    }
}
