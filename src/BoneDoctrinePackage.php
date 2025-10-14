<?php

namespace Bone\BoneDoctrine;

use Barnacle\Container;
use Barnacle\Exception\NotFoundException;
use Barnacle\RegistrationInterface;
use Bone\BoneDoctrine\Command\LoadFixturesCommand;
use Bone\BoneDoctrine\Command\VendorFixturesCommand;
use Bone\Console\CommandRegistrationInterface;
use Bone\Console\ConsoleApplication;
use Bone\View\ViewRegistrationInterface;
use Doctrine\Common\Cache\Psr6\DoctrineProvider;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\Configuration\Migration\ExistingConfiguration;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Metadata\Storage\TableMetadataStorageConfiguration;
use Doctrine\Migrations\Tools\Console\Command\DoctrineCommand;
use Doctrine\Migrations\Tools\Console\Command\DiffCommand;
use Doctrine\Migrations\Tools\Console\Command\DumpSchemaCommand;
use Doctrine\Migrations\Tools\Console\Command\ExecuteCommand;
use Doctrine\Migrations\Tools\Console\Command\GenerateCommand;
use Doctrine\Migrations\Tools\Console\Command\LatestCommand;
use Doctrine\Migrations\Tools\Console\Command\ListCommand;
use Doctrine\Migrations\Tools\Console\Command\MigrateCommand;
use Doctrine\Migrations\Tools\Console\Command\RollupCommand;
use Doctrine\Migrations\Tools\Console\Command\StatusCommand;
use Doctrine\Migrations\Tools\Console\Command\SyncMetadataCommand;
use Doctrine\Migrations\Tools\Console\Command\VersionCommand;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\Console\Command\GenerateProxiesCommand;
use Doctrine\ORM\Tools\Console\Command\InfoCommand;
use Doctrine\ORM\Tools\Console\Command\MappingDescribeCommand;
use Doctrine\ORM\Tools\Console\Command\RunDqlCommand;
use Doctrine\ORM\Tools\Console\Command\SchemaTool\CreateCommand;
use Doctrine\ORM\Tools\Console\Command\SchemaTool\DropCommand;
use Doctrine\ORM\Tools\Console\Command\ValidateSchemaCommand;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class BoneDoctrinePackage implements RegistrationInterface, CommandRegistrationInterface, ViewRegistrationInterface
{
    public function addToContainer(Container $c)
    {
        /** @var EntityManager $em */

        if (!$c->has('cache_dir')) {
            throw new NotFoundException('please set cache_dir in your config');
        }

        if (!$c->has('db')) {
            throw new NotFoundException('please set db connection details in your config');
        }

        if (!$c->has('entity_paths')) {
            throw new NotFoundException('please set entity_paths in your config');
        }

        if (!$c->has('proxy_dir')) {
            throw new NotFoundException('please set proxy_dir in your config');
        }

        $credentials = $c->get('db');
        $entityPaths = $c->get('entity_paths');
        $proxyDir =$c->get('proxy_dir');
        $cacheDir = $c->get('cache_dir');
        $isDevMode = $c->has('devMode') ? $c->get('devMode') : false;
        $cachePool = new FilesystemAdapter('', 60, $cacheDir);
        $config = ORMSetup::createAttributeMetadataConfiguration($entityPaths, $isDevMode, $proxyDir, $cachePool);
        $config->setProxyNamespace('DoctrineProxies');
        $config->enableNativeLazyObjects(true);
        $config->setQueryCache($cachePool);
        $connection = DriverManager::getConnection($credentials, $config);
        $entityManager = new EntityManager($connection, $config);

        $c[EntityManager::class] = $entityManager;
        $c[EntityManagerInterface::class] = $entityManager;
    }

    /**
     * @param Container $container
     * @return array
     */
    public function registerConsoleCommands(Container $container): array
    {
        /** @var EntityManager $em $em */
        $em = $container->get(EntityManager::class);
        $app = $container->get(ConsoleApplication::class);
        $migrationsDir = 'data/migrations';
        $meta = new TableMetadataStorageConfiguration();
        $meta->setTableName('Migration');
        $configuration = new Configuration();
        $configuration->addMigrationsDirectory('Migrations', $migrationsDir);
        $configuration->setMetadataStorageConfiguration($meta);

        $configLoader = new ExistingConfiguration($configuration);
        $emLoader = new ExistingEntityManager($em);
        $dependencyFactory = DependencyFactory::fromEntityManager($configLoader, $emLoader);
        $emProvider = new SingleManagerProvider($em);

        $diff = new DiffCommand($dependencyFactory);
        $dump = new DumpSchemaCommand($dependencyFactory);
        $exec = new ExecuteCommand($dependencyFactory);
        $gen = new GenerateCommand($dependencyFactory);
        $latest = new LatestCommand($dependencyFactory);
        $list = new ListCommand($dependencyFactory);
        $migrate = new MigrateCommand($dependencyFactory);
        $rollup = new RollupCommand($dependencyFactory);
        $status = new StatusCommand($dependencyFactory);
        $sync = new SyncMetadataCommand($dependencyFactory);
        $ver = new VersionCommand($dependencyFactory);
        $dropDb = new DropCommand($emProvider);
        $createDb = new CreateCommand($emProvider);
        $validate = new ValidateSchemaCommand($emProvider);
        $proxy = new GenerateProxiesCommand($emProvider);
        $info = new InfoCommand($emProvider);
        $mappingDescribe = new MappingDescribeCommand($emProvider);
        $runDql = new RunDqlCommand($emProvider);
        $fixtures = new LoadFixturesCommand($em, $container->has('fixtures') ? $container->get('fixtures') : []);
        $vendorFixtures = new VendorFixturesCommand($em, $container->has('vendorFixtures') ? $container->get('vendorFixtures') : []);

        $diff->setName('migrant:diff');
        $createDb->setName('migrant:create');
        $dropDb->setName('migrant:drop');
        $dump->setName('migrant:dump');
        $exec->setName('migrant:execute');
        $gen->setName('migrant:generate');
        $info->setName('migrant:info');
        $latest->setName('migrant:latest');
        $list->setName('migrant:list');
        $mappingDescribe->setName('migrant:describe');
        $migrate->setName('migrant:migrate');
        $runDql->setName('migrant:run-dql');
        $rollup->setName('migrant:rollup');
        $status->setName('migrant:status');
        $sync->setName('migrant:sync');
        $validate->setName('migrant:validate');
        $ver->setName('migrant:version');
        $proxy->setName('migrant:generate-proxies');


        $commands = [$createDb, $dropDb, $diff, $dump, $exec, $gen, $info, $latest, $list, $mappingDescribe, $migrate, $rollup, $runDql, $status, $sync, $validate, $ver, $proxy, $fixtures, $vendorFixtures];

        /** @var DoctrineCommand $command */
        foreach ($commands as $command) {
            $name = $command->getName();
            $name = str_replace(array('migrations:', 'orm:'), '', $name);
            $command->setName($name);
        }

        return $commands;
    }

    public function addViews(): array
    {
        return [
            'admin' => __DIR__ . '/View/admin',
        ];
    }

    public function addViewExtensions(Container $c): array
    {
        return [];
    }
}
