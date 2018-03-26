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
use ZekrBundle\Entity\Video;

class MigrationCollectionCommand extends ContainerAwareCommand
{


    protected function configure()
    {
        $this
            ->setName('zekr:collection-migration-data')
            ->setDescription('migration collection');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ini_set('memory_limit', '99999999999999999999999999999999999999999999999999999999999999999999999999M');
        $em = $this->getContainer()->get('doctrine')->getManager();
        $repository = $em->getRepository('ZekrBundle:Video');
        $entities = $repository->findAll();
        $cacheDir = $this->getContainer()->getParameter('kernel.cache_dir');

        $i = 1;
        foreach ($entities as $entity) {
            $getCollections = $em->getRepository('ZekrBundle:OldCollectionItems')->findBy(['objectId'=>$entity->getOldId()]);
            foreach ($getCollections as $collection){
                $entity->addVideoType($em->getRepository('ZekrBundle:VideoType')->findOneBy(['oldId'=>$collection->getCollectionId()]));
            }



            $output->writeln($i++);


            if ($i%100==0) {
                $em->flush();
            }
        }
        $em->flush();


    }

}