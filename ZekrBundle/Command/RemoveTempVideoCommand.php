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
use ZekrBundle\Entity\TempVideo;

class RemoveTempVideoCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('zekr:remove-temp-video')
            ->setDescription('Remove temp video');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $dql = "SELECT t
            FROM ZekrBundle:TempVideo t
            WHERE t.insertedAt < :threeDays
            AND t.status = 1";
        $query = $em->createQuery($dql);
        $query->setParameter('threeDays', new \DateTime('-3 day'), \Doctrine\DBAL\Types\Type::DATETIME);
        $result = $query->getResult();

        foreach ($result as $tempVideo) {
            $tempVideo->deleteCurrentFile();
            $em->remove($tempVideo);
            $em->flush();
        }
    }

}