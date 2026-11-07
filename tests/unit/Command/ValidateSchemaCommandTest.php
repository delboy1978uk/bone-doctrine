<?php

namespace Bone\Test\BoneDoctrine\Command;

use Barnacle\Container;
use Bone\BoneDoctrine\BoneDoctrinePackage;
use Bone\BoneDoctrine\Command\ValidateSchemaCommand;
use Codeception\Test\Unit;
use Doctrine\DBAL\Schema\Table;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;
use Symfony\Component\Console\Tester\CommandTester;

class ValidateSchemaCommandTest extends Unit
{
    private EntityManager $entityManager;

    protected function _before()
    {
        $container = new Container();
        $container['cache_dir'] = './tests/data';
        $container['proxy_dir'] = './tests/data';
        $container['entity_paths'] = ['./tests/data'];
        $container['db'] = ['driver' => 'pdo_sqlite', 'memory' => true];
        $package = new BoneDoctrinePackage();
        $package->addToContainer($container);
        $this->entityManager = $container->get(EntityManager::class);
        $this->entityManager->getConnection()->executeStatement('CREATE TABLE ' . BoneDoctrinePackage::MIGRATIONS_TABLE . ' (version VARCHAR(191) NOT NULL)');
    }

    public function testTheMigrationsTableIsNotSchemaDrift()
    {
        $tester = new CommandTester(new ValidateSchemaCommand(new SingleManagerProvider($this->entityManager)));

        $this->assertSame(0, $tester->execute([]));
        $this->assertStringContainsString('in sync', $tester->getDisplay());
    }

    public function testOtherTablesAreStillDrift()
    {
        $this->entityManager->getConnection()->executeStatement('CREATE TABLE Stray (id INT NOT NULL)');
        $tester = new CommandTester(new ValidateSchemaCommand(new SingleManagerProvider($this->entityManager)));

        $this->assertSame(2, $tester->execute([]));
    }

    public function testTheFilterIsOnlyOnWhileValidating()
    {
        $config = $this->entityManager->getConnection()->getConfiguration();
        $before = $config->getSchemaAssetsFilter();
        (new CommandTester(new ValidateSchemaCommand(new SingleManagerProvider($this->entityManager))))->execute([]);

        $this->assertSame($before, $config->getSchemaAssetsFilter());
    }

    public function testTheFilterHidesOnlyTheMigrationsTable()
    {
        $filter = ValidateSchemaCommand::hideMigrationsTable(static fn ($asset): bool => true);

        $this->assertFalse($filter(BoneDoctrinePackage::MIGRATIONS_TABLE));
        $this->assertFalse($filter(new Table(BoneDoctrinePackage::MIGRATIONS_TABLE)));
        $this->assertTrue($filter('Ticket'));
        $this->assertTrue($filter(new Table('Ticket')));
    }

    public function testTheFilterKeepsTheExistingFilter()
    {
        $filter = ValidateSchemaCommand::hideMigrationsTable(static fn ($asset): bool => $asset !== 'Ignored');

        $this->assertFalse($filter('Ignored'));
        $this->assertTrue($filter('Ticket'));
    }
}
