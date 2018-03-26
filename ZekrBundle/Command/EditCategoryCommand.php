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

class EditCategoryCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('zekr:edit-category')
            ->setDescription('Edit Category')
            ->addArgument('id', InputArgument::REQUIRED, 'Category ID')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $id = $input->getArgument('id');
        if($id) {
            $em = $this->getContainer()->get('doctrine')->getManager();
            $repository = $em->getRepository('ZekrBundle:Category');
            $category = $repository->find($id);
            if($category) {
                $categoryIndexer = $this->getContainer()->get('search.service')->getIndexer()->getCategoryIndexer();
                $categoryIndexer->edit($category);
            }
        }
    }

}