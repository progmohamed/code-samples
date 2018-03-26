<?php

namespace AdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

class ReloadMenuCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('admin:rebuild-menu')
            ->setDescription('Rebuilding back-end menu')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->truncateTables();
        $this->loadFixtrures();
    }

    private function truncateTables()
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $connection = $em->getConnection();
        $connection->beginTransaction();
        try {
            $connection->query('DELETE FROM admin_group');
            $connection->query('SET FOREIGN_KEY_CHECKS = 0');
            $connection->query('TRUNCATE TABLE admin_section_item_role');
            $connection->query('TRUNCATE TABLE admin_section_item');
            $connection->query('TRUNCATE TABLE admin_section');
            $connection->query('TRUNCATE TABLE admin_group');
            $connection->query('SET FOREIGN_KEY_CHECKS = 1');
            $connection->commit();
        }catch (\Exception $e) {
            $connection->rollback();
        }
    }

    private function loadFixtrures()
    {
        $container = $this->getContainer();
        $em = $container->get('doctrine')->getManager();
        $collectorService = $container->get('admin.backend.menu.service');

        $services = $collectorService->getServices();

        if(sizeof($services)) {
            $loader = new Loader();
            foreach($services as $fixture) {
                $fixture->setContainer( $this->getContainer() );
                $loader->addFixture( $fixture );
            }
            $executor = new ORMExecutor($em);
            $executor->execute($loader->getFixtures(), true);
        }
    }
}