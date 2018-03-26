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

class RebuildTheIndexCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('zekr:index-rebuild')
            ->setDescription('Rebuilding the entire Solr index')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $languages = $this->getContainer()->get('admin.admin_helper')->getLanguages();
        foreach ($languages as $language) {
            $this->purgeIndex($language);
            $this->generateTheXMLfile($language, $input, $output);
        }
    }

    private function purgeIndex(Language $language)
    {
        $searchService = $this->getContainer()->get('search.service');
        $indexer = $searchService->getIndexer();
        $indexer->setClientName('solarium.client.zekr_'.$language->getLocale());
        $indexer->purge();
    }

    private function generateTheXMLfile($language, InputInterface $input, OutputInterface $output)
    {

        $clientName = 'zekr_'.$language->getLocale();

        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->getConnection()->getConfiguration()->setSQLLogger(null);

        $maxResults = 30;
        $repository = $em->getRepository('ZekrBundle:Video');
        $where = "v.deletedAt IS NULL AND vt.locale = :locale AND vt.display = true";
        $parameters = ['locale' => $language->getLocale()];
        $qb = $repository->createQueryBuilder('v')
                         ->innerJoin('v.translations', 'vt')
                         ->select('v')
                         ->where($where)
                         ->orderBy('v.id');
        $query = $qb->getQuery();
        $query->setParameters($parameters);
        $query->setMaxResults($maxResults);
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
                    $xmlEntry = $this->getContainer()->get('search.service')->getIndexer()->getFullXMLEntry($video, $language);
                    fputs($fhandler, $xmlEntry);
                }
                $qb->where($where);
                $qb->andWhere('v.id > :maxId');
                $query = $qb->getQuery();
                $parameters['maxId'] = $maxId;
                $query->setParameters($parameters);
                $query->setMaxResults($maxResults);
                $result = $query->getResult();
                $em->clear();
            } while (sizeof($result));

            fputs($fhandler, "</add>");
            fclose($fhandler);

            $solrConfigService = $this->getContainer()->get('zekr.solrconfig.service');
            $command = $solrConfigService->getFullPostCommand($clientName, $xmlFilePath);
            exec($command);

        }catch(\Exception $e) {
            //log error
        }
        if(file_exists($xmlFilePath)) {
            unlink($xmlFilePath);
        }
    }
}