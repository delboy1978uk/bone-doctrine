<?php

namespace Bone\Test\BoneDoctrine;

use Barnacle\Container;
use Barnacle\Exception\NotFoundException;
use Bone\BoneDoctrine\BoneDoctrinePackage;
use Bone\Console\ConsoleApplication;
use Bone\Console\ConsolePackage;
use Bone\Db\DbPackage;
use Codeception\Test\Unit;
use Doctrine\DBAL\Schema\Table;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class PackageTest extends Unit
{
    public function testPackage()
    {
        $container = new Container();
        $container['cache_dir'] = './tests/data';
        $container['proxy_dir'] = './tests/data';
        $container['entity_paths'] = ['./tests/data'];
        $container['consoleCommands'] = [];
        $container['devMode'] = true;
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

    public function testDevModeCachesInMemory()
    {
        $this->assertInstanceOf(ArrayAdapter::class, $this->metadataCache(true));
    }

    public function testWithoutDevModeCachesOnDisk()
    {
        $this->assertInstanceOf(FilesystemAdapter::class, $this->metadataCache(false));
        $this->assertInstanceOf(FilesystemAdapter::class, $this->metadataCache(null));
    }

    private function metadataCache(?bool $devMode): object
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

        if ($devMode !== null) {
            $container['devMode'] = $devMode;
        }

        $package = new BoneDoctrinePackage();
        $package->addToContainer($container);

        return $container->get(EntityManager::class)->getConfiguration()->getMetadataCache();
    }

    public function testMigrationsTableIsHiddenFromTheSchemaTool()
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
        $filter = $container->get(EntityManager::class)->getConnection()->getConfiguration()->getSchemaAssetsFilter();

        $this->assertFalse($filter(BoneDoctrinePackage::MIGRATIONS_TABLE));
        $this->assertFalse($filter(new Table(BoneDoctrinePackage::MIGRATIONS_TABLE)));
        $this->assertTrue($filter('Ticket'));
        $this->assertTrue($filter(new Table('Ticket')));
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
