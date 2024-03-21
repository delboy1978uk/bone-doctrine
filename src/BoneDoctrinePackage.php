<?php

namespace Bone\BoneDoctrine;

use Barnacle\Container;
use Barnacle\Exception\NotFoundException;
use Barnacle\RegistrationInterface;
use Bone\BoneDoctrine\Command\LoadFixturesCommand;
use Bone\Console\CommandRegistrationInterface;
use Bone\Console\ConsoleApplication;
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
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class BoneDoctrinePackage implements RegistrationInterface, CommandRegistrationInterface
{
    /**
     * @param Container $c
     * @throws \Doctrine\ORM\ORMException
     */
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
        $app->setHelperSet($helperSet);
        $migrationsDir = 'data/migrations';
        $meta = new TableMetadataStorageConfiguration();
        $meta->setTableName('Migration');
        $configuration = new Configuration();
        $configuration->addMigrationsDirectory('Migrations', $migrationsDir);
        $configuration->setMetadataStorageConfiguration($meta);

        $configLoader = new ExistingConfiguration($configuration);
        $emLoader = new ExistingEntityManager($em);
        $dependencyFactory = DependencyFactory::fromEntityManager($configLoader, $emLoader);

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
        $proxy = new GenerateProxiesCommand();
        $fixtures = new LoadFixturesCommand($em, $container->has('fixtures') ? $container->get('fixtures') : []);

        $diff->setName('migrant:diff');
        $dump->setName('migrant:dump');
        $exec->setName('migrant:execute');
        $gen->setName('migrant:generate');
        $latest->setName('migrant:latest');
        $list->setName('migrant:list');
        $migrate->setName('migrant:migrate');
        $rollup->setName('migrant:rollup');
        $status->setName('migrant:status');
        $sync->setName('migrant:sync');
        $ver->setName('migrant:version');
        $proxy->setName('migrant:generate-proxies');

        $commands = [$diff, $dump, $exec, $gen, $latest, $list, $migrate, $rollup, $status, $sync, $ver, $proxy, $fixtures];

        /** @var DoctrineCommand $command */
        foreach ($commands as $command) {
            $name = $command->getName();
            $name = str_replace(array('migrations:', 'orm:'), '', $name);
            $command->setName($name);
        }

        return $commands;
    }
}
