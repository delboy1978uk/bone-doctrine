<?php

declare(strict_types=1);

namespace Bone\BoneDoctrine\Command;

use Bone\Console\Command;
use Bone\Contracts\Container\FixtureProviderInterface;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class VendorFixturesCommand extends Command
{
    private EntityManager $entityManager;
    private array $fixtures;

    public function __construct(EntityManager $entityManager, array $fixtures = [])
    {
        parent::__construct('migrant:vendor-fxtures');
        $this->entityManager = $entityManager;
        $this->fixtures = $fixtures;
    }

    protected function configure(): void
    {
        $this->setDescription('[vendor-fixtures] Loads vendor package fixtures.');
        $this->setHelp('Loads data fixtures.');
        $this->addArgument('purge', InputArgument::OPTIONAL, 'Purge the database before seeding', false);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = $this->getIO($input, $output);
        $io->writeln('');
        $io->title('💀 Bone Framework database fixtures');
        $io->writeln('   Loading fixtures....');
        $io->writeln('');
        $loader = new Loader();
        $availableFixtures = [];

        if (\count($this->fixtures) === 0) {
            $io->writeln('🤷‍♂️ No fixtures found, exiting.');
            $io->writeln('');

            return Command::SUCCESS;
        }

        foreach ($this->fixtures as $fixture => $data) {
            $availableFixtures[] = $fixture;
        }

        $choices = $io->choice('Pick the fixtures to install', $availableFixtures, multiSelect: true);

        foreach ($choices as $fixtur) {
            $instance = new $fixture();

            if ($instance instanceof FixtureProviderInterface) {
                $io->writeln('   <info>Executing fixture ' . $fixture . '</info>');

                foreach ($instance->getFixtures() as $fixtureName) {
                    $fixture = new $fixtureName();
                    $loader->addFixture($fixture);
                }

            }
        }

        $io->writeln('');
        $executor = new ORMExecutor($this->entityManager, new ORMPurger());
        $executor->execute($loader->getFixtures(), !$input->getArgument('purge'));

        return Command::SUCCESS;
    }
}
