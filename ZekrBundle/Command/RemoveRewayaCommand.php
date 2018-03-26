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

class RemoveRewayaCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('zekr:remove-rewaya')
            ->setDescription('Remove Rewaya')
            ->addArgument('id', InputArgument::REQUIRED, 'Rewaya ID')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $id = $input->getArgument('id');
        if($id) {
            $em = $this->getContainer()->get('doctrine')->getManager();
            $repository = $em->getRepository('ZekrBundle:Rewaya');
            $rewaya = $repository->find($id);
            if($rewaya) {
                $rewayaIndexer = $this->getContainer()->get('search.service')->getIndexer()->getRewayaIndexer();
                $rewayaIndexer->delete($rewaya);

                $hardDeleteHelper = $this->getContainer()->get('admin.hard_delete_helper');
                $hardDeleteHelper->disableSoftDeleteListeners();
                $em->remove($rewaya);
                $em->flush();
                $hardDeleteHelper->reenableSoftDeleteListeners();
            }
        }
    }

}