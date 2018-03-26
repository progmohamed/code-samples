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

class ConvertVideoCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('zekr:convert-video')
            ->setDescription('convert video')
            ->addArgument('id', InputArgument::REQUIRED, 'Temp Video Id');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $id = $input->getArgument('id');
        if ($id) {


            $videoUploadDir = realpath($this->getContainer()->get('kernel')->getRootDir() . '/../web/upload/video') . '/';
            $mp3UploadDir = realpath($this->getContainer()->get('kernel')->getRootDir() . '/../web/upload/mp3') . '/';

            $em = $this->getContainer()->get('doctrine')->getManager();
            $repository = $em->getRepository('ZekrBundle:Video');
            $entity = $repository->find($id);
            if ($entity) {
                if ($entity->getTempVideoFileAbsolutePath() && file_exists($entity->getTempVideoFileAbsolutePath())) {

                    $ffmpeg = new FFmpeg($entity->getTempVideoFileAbsolutePath());


                    $ffmpeg->setFFmpegBin($this->getContainer()->getParameter('zekr.ffmpeg_binary'));
                    if ($ffmpeg->isValid()) {

                        $entity->setConversionStatus(Video::STATUS_IN_PROGRESS);

                        $em->flush();

                        if (false == $entity->getConversion()) {
                            $hdFilename = sha1(uniqid(mt_rand(), true)) . '_no_convert.mp4';
                            copy($entity->getTempVideoFileAbsolutePath(), $videoUploadDir . $hdFilename);

                        } else {
                            $hdFilename = sha1(uniqid(mt_rand(), true)) . '.mp4';
                            //HD
                            $ffmpeg->_exec('-i "' . $entity->getTempVideoFileAbsolutePath() . '" -codec:v libx264 -profile:v high -preset slower -b:v 1000k -vf scale=-1:720 -threads 0 -b:a 196k ' . $videoUploadDir . $hdFilename);
                        }

                        $sdFilename = sha1(uniqid(mt_rand(), true)) . '.mp4';
                        // SD 360p
                        $ffmpeg->_exec('-i "' . $entity->getTempVideoFileAbsolutePath() . '" -codec:v libx264 -profile:v baseline -preset slow -b:v 250k -vf scale=-1:360 -threads 0 -b:a 96k ' . $videoUploadDir . $sdFilename);
                        $entity->setSdFile($sdFilename);
                        $entity->setSdFileSize(filesize($videoUploadDir . $sdFilename));


                        $mp3Filename = sha1(uniqid(mt_rand(), true)) . '.mp3';
                        // mp3
                        $ffmpeg->_exec('-i "' . $entity->getTempVideoFileAbsolutePath() . '" -b:a 192K -vn ' . $mp3UploadDir . $mp3Filename);


                        $entity->setHdFile($hdFilename);
                        $entity->setHdFileSize(filesize($videoUploadDir . $hdFilename));

                        $entity->setMp3File($mp3Filename);
                        $entity->setMp3FileSize(filesize($mp3UploadDir . $mp3Filename));

                        $entity->setConversionStatus(Video::STATUS_DONE);

                        $entity->deleteTempVideoFile();

                        $em->flush();

                        $indexer = $this->getContainer()->get('search.service')->getIndexer();
                        $indexer->index($entity);
                    }
                }


            }
        }
    }

}