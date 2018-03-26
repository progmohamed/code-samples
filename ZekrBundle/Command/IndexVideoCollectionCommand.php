<?php

namespace ZekrBundle\Command;

use ZekrBundle\Entity\Video;
use AdminBundle\Entity\Language;
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

class IndexVideoCollectionCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('zekr:index-video-collection')
            ->addArgument('collection', InputArgument::REQUIRED, 'collection ID')
            ->setDescription('Rebuilding the entire Solr index')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $collection = $input->getArgument('collection');

        $languages = $this->getContainer()->get('admin.admin_helper')->getLanguages();
        foreach ($languages as $language) {
            $this->generateTheXMLfile($language, $input, $output, $collection);
        }
    }

    private function generateTheXMLfile($language, InputInterface $input, OutputInterface $output, $collection)
    {

        $clientName = 'zekr_'.$language->getLocale();

        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->getConnection()->getConfiguration()->setSQLLogger(null);

        $repository = $em->getRepository('ZekrBundle:Video');
        $qb = $repository->createQueryBuilder('v')
                         ->innerJoin('v.translations', 'vt')
                         ->innerJoin('v.videoCollections', 'vc')
                         ->select('v')
                         ->where('v.deletedAt IS NULL')
                         ->andWhere('vt.locale = :locale')
                         ->andWhere('vt.display = TRUE')
                         ->andWhere('vc.collection = :collection')
                         ->orderBy('v.id');
        $query = $qb->getQuery();
        $query->setParameter('collection', $collection);
        $query->setParameter('locale', $language->getLocale());
        $query->setMaxResults(50);
        $result = $query->getResult();

        $cacheDir = $this->getContainer()->getParameter('kernel.cache_dir');

        $xmlFile = uniqid('solr_index_', true).'.xml';
        $xmlFilePath = $cacheDir.'/'.$xmlFile;
        try {
            $fhandler = fopen($xmlFilePath, 'w+');
            fputs($fhandler, "<add>\n");
            do {
                $maxId = null;
                foreach ($result as $video) {
                    $maxId = $video->getId();
                    $xmlEntry =  $this->getContainer()->get('search.service')->getIndexer()->getFullXMLEntry($video, $language);
                    fputs($fhandler, $xmlEntry);
                }
                $qb->where('v.id > :maxId')
                    ->andWhere('v.deletedAt IS NULL')
                    ->andWhere('vt.locale = :locale')
                    ->andWhere('vt.display = TRUE');
                $query = $qb->getQuery();
                $query->setParameter('locale', $language->getLocale());
                $query->setParameter('maxId', $maxId);
                $query->setMaxResults(50);
                $result = $query->getResult();
            } while (sizeof($result));

            fputs($fhandler, "</add>");
            fclose($fhandler);

            //$command = "D:/wamp64/solr-6.4.2/bin/post -c fatawa_ar -p 81 {$xmlFilePath}";
            //$command = "java -Durl=http://localhost:81/solr/fatawa_ar/update -Dtype=application/xml -jar D:/solr6/example/exampledocs/post.jar {$xmlFilePath} ";
            //$command = "java -Durl=http://admin:123@127.0.0.1:81/solr/fatawa_ar/update -Dtype=application/xml -jar D:/wamp64/solr-6.4.2/example/exampledocs/post.jar {$xmlFilePath} ";
            //$command = "java -Durl=http://admin:123@127.0.0.1:81/solr/".$clientName."/update -Dtype=application/xml -jar D:/wamp64/solr-6.4.2/example/exampledocs/post.jar {$xmlFilePath} ";
            $command = "java -Durl=http://127.0.0.1:81/solr/".$clientName."/update -Dtype=application/xml -jar D:/solr6/example/exampledocs/post.jar {$xmlFilePath} ";
            exec($command);
        }catch(\Exception $e) {
            //log error
        }
        if(file_exists($xmlFilePath)) {
            unlink($xmlFilePath);
        }
    }
}