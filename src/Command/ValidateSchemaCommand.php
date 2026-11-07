<?php

declare(strict_types=1);

namespace Bone\BoneDoctrine\Command;

use Bone\BoneDoctrine\BoneDoctrinePackage;
use Doctrine\DBAL\Schema\AbstractAsset;
use Doctrine\ORM\Tools\Console\Command\ValidateSchemaCommand as DoctrineValidateSchemaCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Doctrine's validate command, but blind to the migrations table, which is not an entity: otherwise the schema is
 * never in sync (it wants to DROP TABLE Migration). The table is hidden only while validating, because Doctrine
 * Migrations looks for it through the same schema manager and would try to create it again.
 */
class ValidateSchemaCommand extends DoctrineValidateSchemaCommand
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $config = $this->getEntityManager($input)->getConnection()->getConfiguration();
        $filter = $config->getSchemaAssetsFilter();
        $config->setSchemaAssetsFilter(self::hideMigrationsTable($filter));

        try {
            return parent::execute($input, $output);
        } finally {
            $config->setSchemaAssetsFilter($filter);
        }
    }

    /** Wraps a schema assets filter so it also rejects the migrations table (DBAL passes names, or assets in 3.x). */
    public static function hideMigrationsTable(callable $filter): callable
    {
        return static function (string|AbstractAsset $asset) use ($filter): bool {
            $name = $asset instanceof AbstractAsset ? $asset->getName() : $asset;

            return $name !== BoneDoctrinePackage::MIGRATIONS_TABLE && $filter($asset);
        };
    }
}
