<?php

declare(strict_types=1);

namespace Bone\BoneDoctrine\Command;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class LoadFixturesCommand extends Command
{
    private EntityManager $entityManager;
    private array $fixtures;

    public function __construct(EntityManager $entityManager, array $fixtures = [])
    {
        parent::__construct('migrant:fixtures');
        $this->entityManager = $entityManager;
        $this->fixtures = $fixtures;
    }

    protected function configure()
    {
        $this->setDescription('[fixtures] Loads data fixtures.');
        $this->setHelp('Loads data fixtures.');
        $this->addOption('purge', 'p', InputOption::VALUE_OPTIONAL, 'Purge the database before seeding', false);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('');
        $output->writeln('💀 Bone Framework database fixtures');
        $output->writeln('   Loading fixtures....');
        $output->writeln('');
        $loader = new Loader();

        if (\count($this->fixtures) === 0) {
            $output->writeln('🤷‍♂️ No fixtures found, exiting.');
            $output->writeln('');

            return Command::SUCCESS;
        }

        foreach ($this->fixtures as $fixture) {
            $instance = new $fixture();

            if ($instance instanceof FixtureInterface) {
                $output->writeln('   <info>Executing fixture ' . $fixture . '</info>');
                $loader->addFixture($instance);
            }
        }
        $output->writeln('');

        $executor = new ORMExecutor($this->entityManager, new ORMPurger());
        $executor->execute($loader->getFixtures(), $input->getOption('purge'));

        return Command::SUCCESS;
    }
}
