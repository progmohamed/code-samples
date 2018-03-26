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

class extractThumbnailCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('zekr:extract-thumbnail')
            ->setDescription('extract video thumbnail');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ini_set('memory_limit', '99999999999999999999999999999999999999999999999999999999999999999999999999M');

        $em = $this->getContainer()->get('doctrine')->getManager();
        $dql = "SELECT v
            FROM ZekrBundle:Video v
            INNER JOIN v.category c
            WHERE v.deletedAt is NULL
            AND c.deletedAt is NULL";
        $query = $em->createQuery($dql);
        $entities = $query->getResult();
        $i = 1;
        foreach ($entities as $entity) {
            // get new thumbnail
            if (null != $entity->getTempVideoFile() && $entity->getTempVideoFileAbsolutePath() && file_exists($entity->getTempVideoFileAbsolutePath())) {
                $fileAbsolutePath = $entity->getTempVideoFileAbsolutePath();
            } elseif (null != $entity->getHdFile() && $entity->getHdFileAbsolutePath() && file_exists($entity->getHdFileAbsolutePath())) {
                $fileAbsolutePath = $entity->getHdFileAbsolutePath();
            }

            if(!empty($fileAbsolutePath)) {
                $ffmpeg = new FFmpeg($fileAbsolutePath);
                $ffmpeg->setFFmpegBin($this->getContainer()->getParameter('zekr.ffmpeg_binary'));
                if ($ffmpeg->isValid()) {
                    $thumbmnailFilename = sha1(uniqid(mt_rand(), true)) . '.jpg';
                    $thumbnailFullPath = $entity->getThumbFilUploadRootDir() . $thumbmnailFilename;
                    $entity->deleteThembFile();
                    $ffmpeg->getThumbnail($thumbnailFullPath);
                    $entity->setThumbnailFile($thumbmnailFilename);
                }
            }
            if ($i % 200 == 0) {
                $em->flush();
            }
            $output->writeln($i++);
        }
        $em->flush();

    }
}