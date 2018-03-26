<?php

namespace ZekrBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use TaskManagerBundle\Entity\Task;

class EditJuzCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('zekr:edit-juz')
            ->setDescription('Edit Juz')
            ->addArgument('id', InputArgument::REQUIRED, 'Juz ID')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $id = $input->getArgument('id');
        if($id) {
            $em = $this->getContainer()->get('doctrine')->getManager();
            $repository = $em->getRepository('ZekrBundle:Juz');
            $juz = $repository->find($id);
            if($juz) {
                $juzIndexer = $this->getContainer()->get('search.service')->getIndexer()->getJuzIndexer();
                $juzIndexer->edit($juz);
            }
        }
    }

}