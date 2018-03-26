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
use ZekrBundle\Classes\FFmpeg;
use ZekrBundle\Entity\Video;
use ZekrBundle\Entity\TempVideo;

class ConvertAllVideoCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('zekr:convert-all-video')
            ->setDescription('convert all video');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ini_set('memory_limit', '99999999999999999999999999999999999999999999999999999999999999999999999999M');

//        $em = $this->getContainer()->get('doctrine')->getManager();
//        $repository = $em->getRepository('ZekrBundle:Video');
//        $entities = $repository->findAll();
//        $i = 1;
//        foreach ($entities as $entity) {
//            if ($entity->getHdFile() && file_exists($entity->getHdFileAbsolutePath())) {
//                $entity->setConversionStatus(Video::STATUS_WAITING);
//            } else {
//                $entity->setConversionStatus(Video::STATUS_FILES_MISSING);
//            }
//
//            if ($i % 200 == 0) {
//                $em->flush();
//            }
//
//            $output->writeln($i++);
//        }
//
//        $em->flush();


        $videoUploadDir = realpath($this->getContainer()->get('kernel')->getRootDir() . '/../web/upload/video') . '/';

        $em = $this->getContainer()->get('doctrine')->getManager();
        $repository = $em->getRepository('ZekrBundle:Video');
        $entities = $repository->findBy(['conversionStatus' => 1]);
        $i = 1;
        foreach ($entities as $entity) {
            $ext = pathinfo($entity->getHdFile(), PATHINFO_EXTENSION);
            if ('flv' == $ext) {
                $old_file = $entity->getHdFileAbsolutePath();
                if (file_exists($old_file)) {
                    $ffmpeg = new FFmpeg($old_file);
                    $ffmpeg->setFFmpegBin($this->getContainer()->getParameter('zekr.ffmpeg_binary'));
                    if ($ffmpeg->isValid()) {
                        $entity->setConversionStatus(Video::STATUS_IN_PROGRESS);
                        $em->flush();
                        
                        $hdFilename = sha1(uniqid(mt_rand(), true)) . '.mp4';
                        $ffmpeg->_exec('-i "' . $old_file . '"  ' . $videoUploadDir . $hdFilename);
                        $entity->setHdFile($hdFilename);
                        $entity->setConversionStatus(Video::STATUS_DONE);

                        @unlink($old_file);
                        $em->flush();
                    }
                }
            }

            $output->writeln($i++);
        }

    }
}