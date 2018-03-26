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

class EditSurahCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('zekr:edit-surah')
            ->setDescription('Edit Surah')
            ->addArgument('id', InputArgument::REQUIRED, 'Surah ID')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $id = $input->getArgument('id');
        if($id) {
            $em = $this->getContainer()->get('doctrine')->getManager();
            $repository = $em->getRepository('ZekrBundle:Surah');
            $surah = $repository->find($id);
            if($surah) {
                $surahIndexer = $this->getContainer()->get('search.service')->getIndexer()->getSurahIndexer();
                $surahIndexer->edit($surah);
            }
        }
    }

}