<?php

namespace Bone\BoneDoctrine;

use Barnacle\Container;
use Barnacle\RegistrationInterface;
use Bone\Console\CommandRegistrationInterface;
use Bone\Console\ConsoleApplication;
use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\Tools\Console\Command\AbstractCommand;
use Doctrine\Migrations\Tools\Console\Command\DiffCommand;
use Doctrine\Migrations\Tools\Console\Command\ExecuteCommand;
use Doctrine\Migrations\Tools\Console\Command\GenerateCommand;
use Doctrine\Migrations\Tools\Console\Command\MigrateCommand;
use Doctrine\Migrations\Tools\Console\Command\StatusCommand;
use Doctrine\Migrations\Tools\Console\Command\VersionCommand;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\Command\GenerateProxiesCommand;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Setup;
use Exception;
use Symfony\Component\Console\Helper\QuestionHelper;
use Doctrine\Common\Cache\Psr6\DoctrineProvider;
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
            throw new Exception('please set cache_dir in your config');
        }

        if (!$c->has('db')) {
            throw new Exception('please set db connection details in your config');
        }

        if (!$c->has('entity_paths')) {
            throw new Exception('please set entity_paths in your config');
        }

        if (!$c->has('proxy_dir')) {
            throw new Exception('please set proxy_dir in your config');
        }

        $credentials = $c->get('db');
        $entityPaths = $c->get('entity_paths');
        $proxyDir =$c->get('proxy_dir');
        $cacheDir = $c->get('cache_dir');
        $isDevMode = false;
        $config = Setup::createAnnotationMetadataConfiguration($entityPaths, $isDevMode, null, null, false);
        $config->setProxyDir($proxyDir);
        $config->setProxyNamespace('DoctrineProxies');
        $cachePool = new FilesystemAdapter('', 60, $cacheDir);
        $cache = DoctrineProvider::wrap($cachePool);
        $config->setQueryCacheImpl($cache);
        $entityManager = EntityManager::create($credentials, $config);
        $c[EntityManager::class] = $entityManager;
    }

    /**
     * @param Container $container
     * @return array
     */
    public function registerConsoleCommands(Container $container): array
    {
        /** @var EntityManager $em $em */
        $em = $container->get(EntityManager::class);
        $helperSet = ConsoleRunner::createHelperSet($em);
        $helperSet->set(new QuestionHelper(), 'dialog');
        $app = $container->get(ConsoleApplication::class);
        $app->setHelperSet($helperSet);
        $migrationsDir = 'data/migrations';
        $configuration = new Configuration($em->getConnection());
        $configuration->setMigrationsDirectory($migrationsDir);
        $configuration->setMigrationsNamespace('Migrations');
        $configuration->setMigrationsTableName('Migration');
        $configuration->registerMigrationsFromDirectory($migrationsDir);

        $diff = new DiffCommand();
        $exec = new ExecuteCommand();
        $gen = new GenerateCommand();
        $migrate = new MigrateCommand();
        $status = new StatusCommand();
        $ver = new VersionCommand();
        $proxy = new GenerateProxiesCommand();

        $diff->setName('migrant:diff');
        $exec->setName('migrant:execute');
        $gen->setName('migrant:generate');
        $migrate->setName('migrant:migrate');
        $status->setName('migrant:status');
        $ver->setName('migrant:version');
        $proxy->setName('migrant:generate-proxies');

        $diff->setMigrationConfiguration($configuration);
        $exec->setMigrationConfiguration($configuration);
        $gen->setMigrationConfiguration($configuration);
        $migrate->setMigrationConfiguration($configuration);
        $status->setMigrationConfiguration($configuration);
        $ver->setMigrationConfiguration($configuration);

        $commands = [$diff, $exec, $gen, $migrate, $status, $ver, $proxy];

        /** @var AbstractCommand $command */
        foreach ($commands as $command) {
            $name = $command->getName();
            $name = str_replace(array('migrations:', 'orm:'), '', $name);
            $command->setName($name);
        }

        return $commands;
    }
}
