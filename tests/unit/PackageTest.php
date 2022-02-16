<?php

namespace Bone\Test\BoneDoctrine;

use Barnacle\Container;
use Barnacle\Exception\NotFoundException;
use Bone\BoneDoctrine\BoneDoctrinePackage;
use Bone\Console\ConsoleApplication;
use Bone\Console\ConsolePackage;
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
        $container['consoleCommands'] = [];
        $container['db'] = [
            'driver' => 'pdo_mysql',
            'host' => '127.0.0.1',
            'dbname' => 'awesome',
            'user' => 'root',
            'password' => 'root'
        ];
        $container[ConsoleApplication::class] = $this->makeEmpty(ConsoleApplication::class, []);
        $package = new BoneDoctrinePackage();
        $package->addToContainer($container);

        $this->assertTrue($container->has(EntityManager::class));
        $this->assertInstanceOf(EntityManager::class, $container->get(EntityManager::class));
        $this->assertIsArray($package->registerConsoleCommands($container));
    }

    public function testPackageMissingProxyDir()
    {
        $container = new Container();
        $container['cache_dir'] = './tests/data';
        $container['entity_paths'] = ['./tests/data'];
        $container['consoleCommands'] = [];
        $container['db'] = [
            'driver' => 'pdo_mysql',
            'host' => '127.0.0.1',
            'dbname' => 'awesome',
            'user' => 'root',
            'password' => 'root'
        ];
        $container[ConsoleApplication::class] = $this->makeEmpty(ConsoleApplication::class, []);
        $package = new BoneDoctrinePackage();
        $this->expectException(NotFoundException::class);
        $package->addToContainer($container);
    }

    public function testPackageMissingEntityPaths()
    {
        $container = new Container();
        $container['cache_dir'] = './tests/data';
        $container['proxy_dir'] = './tests/data';
        $container['consoleCommands'] = [];
        $container['db'] = [
            'driver' => 'pdo_mysql',
            'host' => '127.0.0.1',
            'dbname' => 'awesome',
            'user' => 'root',
            'password' => 'root'
        ];
        $container[ConsoleApplication::class] = $this->makeEmpty(ConsoleApplication::class, []);
        $package = new BoneDoctrinePackage();
        $this->expectException(NotFoundException::class);
        $package->addToContainer($container);
    }

    public function testPackageMissingDbCredentials()
    {
        $container = new Container();
        $container['cache_dir'] = './tests/data';
        $container['proxy_dir'] = './tests/data';
        $container['entity_paths'] = ['./tests/data'];
        $container['consoleCommands'] = [];
        $container[ConsoleApplication::class] = $this->makeEmpty(ConsoleApplication::class, []);
        $package = new BoneDoctrinePackage();
        $this->expectException(NotFoundException::class);
        $package->addToContainer($container);
    }

    public function testMissingCacheDir()
    {
        $container = new Container();
        $container['proxy_dir'] = './tests/data';
        $container['entity_paths'] = ['./tests/data'];
        $container['consoleCommands'] = [];
        $container['db'] = [
            'driver' => 'pdo_mysql',
            'host' => '127.0.0.1',
            'dbname' => 'awesome',
            'user' => 'root',
            'password' => 'root'
        ];
        $container[ConsoleApplication::class] = $this->makeEmpty(ConsoleApplication::class, []);
        $package = new BoneDoctrinePackage();
        $this->expectException(NotFoundException::class);
        $package->addToContainer($container);
    }
}
