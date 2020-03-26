<?php

namespace Bone\BoneDoctrine;

use Barnacle\Container;
use Barnacle\RegistrationInterface;
use Bone\Console\CommandRegistrationInterface;
use Del\Common\Command\Migration;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\Tools\Console\Command\AbstractCommand;
use Doctrine\Migrations\Tools\Console\Command\DiffCommand;
use Doctrine\Migrations\Tools\Console\Command\ExecuteCommand;
use Doctrine\Migrations\Tools\Console\Command\GenerateCommand;
use Doctrine\Migrations\Tools\Console\Command\StatusCommand;
use Doctrine\Migrations\Tools\Console\Command\VersionCommand;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\Command\GenerateProxiesCommand;
use Doctrine\ORM\Tools\Setup;

class BoneDoctrinePackage implements RegistrationInterface, CommandRegistrationInterface
{
    /**
     * @param Container $c
     * @throws \Doctrine\ORM\ORMException
     */
    public function addToContainer(Container $c)
    {
        /** @var EntityManager $em */
        $credentials = $c->get('db');
        $entityPaths = $c->get('entity_paths');
        $isDevMode = false;


        $config = Setup::createAnnotationMetadataConfiguration($entityPaths, $isDevMode, null, null, false);
        $config->setProxyDir($c->get('proxy_dir'));
        $config->setProxyNamespace('DoctrineProxies');
        $config->setQueryCacheImpl(new ArrayCache()); /** @todo allow other implementations */

        $entityManager = EntityManager::create($credentials, $config);

        // The Doctrine Entity Manager
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
        $migrationsDir = 'data/migrations';
        $configuration = new Configuration($em->getConnection());
        $configuration->setMigrationsDirectory($migrationsDir);
        $configuration->setMigrationsNamespace('Migrations');
        $configuration->setMigrationsTableName('Migration');
        $configuration->registerMigrationsFromDirectory($migrationsDir);

        $diff = new DiffCommand();
        $exec = new ExecuteCommand();
        $gen = new GenerateCommand();
        $vendormigrate = new Migration();
        $status = new StatusCommand();
        $ver = new VersionCommand();
        $proxy = new GenerateProxiesCommand();

        $diff->setName('migrant:diff');
        $exec->setName('migrant:execute');
        $gen->setName('migrant:generate');
        $vendormigrate->setName('migrant:migrate');
        $status->setName('migrant:status');
        $ver->setName('migrant:version');
        $proxy->setName('migrant:generate-proxies');

        $diff->setMigrationConfiguration($configuration);
        $exec->setMigrationConfiguration($configuration);
        $gen->setMigrationConfiguration($configuration);
        $vendormigrate->setMigrationConfiguration($configuration);
        $status->setMigrationConfiguration($configuration);
        $ver->setMigrationConfiguration($configuration);

        $commands = [$diff, $exec, $gen, $vendormigrate, $status, $ver, $proxy];

        /** @var AbstractCommand $command */
        foreach ($commands as $command) {
            $name = $command->getName();
            $name = str_replace(array('migrations:', 'orm:'), '', $name);
            $command->setName($name);
        }

        return $commands;
    }


}
