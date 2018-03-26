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

class MigrationVideoCommand extends ContainerAwareCommand
{

    public $file = 'video_not_exists_in_hard.json';

    protected function configure()
    {
        $this
            ->setName('zekr:video-migration-data')
            ->setDescription('migration collection');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ini_set('memory_limit', '99999999999999999999999999999999999999999999999999999999999999999999999999M');
        $em = $this->getContainer()->get('doctrine')->getManager();
        $repository = $em->getRepository('ZekrBundle:OldVideo');
        $entities = $repository->findAll();
        $cacheDir = $this->getContainer()->getParameter('kernel.cache_dir');

        $jsonFile = file_get_contents($cacheDir . '/files.json');
        $filesName = json_decode($jsonFile, true);

        $videoNames = [];
        $i = 1;
        foreach ($entities as $entity) {
            $newVideo = new Video();
            $files = [];
            foreach ($filesName as $name) {
                $fileName = pathinfo($entity->getFileName(), PATHINFO_FILENAME); //preg_replace('/\\.[^.\\s]{3,4}$/', '', );
                $ext = pathinfo($entity->getFileName(), PATHINFO_EXTENSION);

                if ($name == $fileName . '-hd.mp4') {

                    $files['hd'] = $fileName . '-hd.mp4';
                }
                if ($name == $fileName . '-sd.mp4') {
                    $files['sd'] = $fileName . '-sd.mp4';
                }

                if ($name == $fileName . '.flv') {
                    $files['flv'] = $fileName . '.flv';
                }

                if ($name == $fileName . '.mp4') {
                    $files['mp4'] = $fileName . '.mp4';
                }
            }


            if (!empty($files['hd']) && !empty($files['sd'])) {

                $newVideo->setHdFile($files['hd'])
                    ->setSdFile($files['sd']);

            } elseif (!empty($files['mp4'])) {

                $newVideo->setHdFile($files['mp4']);

            } elseif (!empty($files['flv'])) {

                $newVideo->setHdFile($files['flv']);

            } else {
                $newVideo->setHdFile($entity->getFileName())
                    ->setConversionStatus(Video::STATUS_FILES_MISSING);
                //TODO(Recalculate sum of video for Juz, Hizb, surah, rewaya and person )
            }

            $newVideo->setViews($entity->getViews())
                ->setPlainSlug($entity->getTitle() . ' - ' . $entity->getVideoid())
                ->setInsertedAt($entity->getDateAdded())
                ->setOldId($entity->getVideoid())
                ->setActive(1)
                ->setSelected(0)
                ->setRewaya($em->getRepository('ZekrBundle:Rewaya')->find($entity->getNarrative()))
                ->addJuz($em->getRepository('ZekrBundle:Juz')->find($entity->getJoz()))
                ->addHizb($em->getRepository('ZekrBundle:Hizb')->find($entity->getHizb()))
                ->addSurah($em->getRepository('ZekrBundle:Surah')->find($entity->getSura()))
                ->addPerson($em->getRepository('ZekrBundle:Person')->find($entity->getReciter()));

            $languages = $this->getContainer()->get('admin.admin_helper')->getLanguages();
            foreach ($languages as $language) {
                $newVideo->translate($language->getLocale())->setTitle($entity->getTitle());
                $newVideo->translate($language->getLocale())->setDescription($entity->getDescription());
                $newVideo->translate($language->getLocale())->setDisplay(1);
            }
            $newVideo->mergeNewTranslations();


            $output->writeln($i++);

            $em->persist($newVideo);

            if ($i%100==0) {
                $em->flush();
            }
        }
        $em->flush();

//            if(count($files) == 0){
//                $videoNames[] = ['id'=>$entity->getVideoid(), 'fileName'=>$entity->getFileName(), 'num'=>count($files)];
//            }

//        $fs = new \Symfony\Component\Filesystem\Filesystem();
//        try {
//            $fs->dumpFile($cacheDir . '/' . $this->file, json_encode($videoNames));
//        } catch (IOException $e) {
//        }


    }

}