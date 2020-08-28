<?php

namespace Bone\BoneDoctrine;

use Barnacle\Container;
use Barnacle\RegistrationInterface;
use Bone\BoneDoctrine\Command\DiffCommand;
use Bone\Console\CommandRegistrationInterface;
use Del\Common\Command\Migration;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Migrations\Configuration\Configuration;
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
        die('AYE'):
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
        die('NO'):
        $em = $container->get(EntityManager::class);
        $migrationsDir = 'data/migrations';
        $configuration = new Configuration($em->getConnection());
        $configuration->setMigrationsDirectory($migrationsDir);
        $configuration->setMigrationsNamespace('Migrations');
        $configuration->setMigrationsTableName('Migration');
        $configuration->registerMigrationsFromDirectory($migrationsDir);

        $diff = new DiffCommand('migrant:diff');
        $exec = new ExecuteCommand();
        $exec->setName('migrant:execute');
        $gen = new GenerateCommand();
        $gen->setName('migrant:generate');
        $vendorMigrate = new Migration();
        $vendorMigrate->setName('migrant:migrate');
        $status = new StatusCommand();
        $status->setName('migrant:status');
        $ver = new VersionCommand();
        $ver->setName('migrant:version');
        $proxy = new GenerateProxiesCommand();

        $diff->setMigrationConfiguration($configuration);
        $exec->setMigrationConfiguration($configuration);
        $gen->setMigrationConfiguration($configuration);
        $vendorMigrate->setMigrationConfiguration($configuration);
        $status->setMigrationConfiguration($configuration);
        $ver->setMigrationConfiguration($configuration);
        $ver->setName('migrant:generate-proxies');
        $proxy->setAliases('migrations');

        return [$diff, $exec, $gen, $vendorMigrate, $status, $ver, $proxy];
    }


}
