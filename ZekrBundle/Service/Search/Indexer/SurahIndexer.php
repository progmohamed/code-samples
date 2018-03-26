<?php

namespace ZekrBundle\Service\Search\Indexer;

use ZekrBundle\Entity\Video;
use AdminBundle\Entity\Language;
use ZekrBundle\Entity\Surah;

class SurahIndexer
{

    protected $container;

    public function __construct($container)
    {
        $this->setContainer($container);
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function setContainer($container)
    {
        $this->container = $container;
        return $this;
    }

    public function edit(Surah $surah)
    {
        $languages = $this->getContainer()->get('admin.admin_helper')->getLanguages();
        foreach ($languages as $language) {
            $this->editInCore($surah, $language);
        }
    }

    private function editInCore(Surah $surah, Language $language)
    {
        $clientName = 'zekr_' . $language->getLocale();

        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->getConnection()->getConfiguration()->setSQLLogger(null);

        $maxResults = 50;
        $repository = $em->getRepository('ZekrBundle:Video');
        $where ='v.deletedAt IS NULL AND s = :surah';
        $qb = $repository->createQueryBuilder('v')
            ->select('v')
            ->innerJoin('v.surah','s')
            ->where($where)
            ->orderBy('v.id');

        $parameters = [
            'surah' => $surah
        ];

        $query = $qb->getQuery();
        $query->setParameters($parameters);
        $query->setMaxResults($maxResults);
        $result = $query->getResult();

        $cacheDir = $this->getContainer()->getParameter('kernel.cache_dir');
        $xmlFile = uniqid('solr_index_', true) . '.xml';
        $xmlFilePath = $cacheDir . '/' . $xmlFile;
        try {
            $fhandler = fopen($xmlFilePath, 'w+');
            fputs($fhandler, "<add>\n");
            do {
                $maxId = null;
                foreach ($result as $video) {
                    $maxId = $video->getId();
                    $xmlEntry = $this->getEditXMLEntry($video, $language);
                    fputs($fhandler, $xmlEntry);
                }
                $qb->where($where);
                $qb->andWhere('v.id > :maxId');
                $query = $qb->getQuery();
                $parameters['maxId'] = $maxId;
                $query->setParameters($parameters);
                $query->setMaxResults($maxResults);
                $result = $query->getResult();
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

    private function getEditXMLEntry(Video $video, Language $language)
    {
        $active = $video->shouldBeActive() ? 'true' : 'false';

        $em = $this->getContainer()->get('doctrine')->getManager();
        $videoRepository = $em->getRepository('ZekrBundle:Video');

        $xml ="<doc>\n";
        $xml.='<field name="id">'.$video->getId()."</field>\n";
        $xml.='<field name="active" update="set">'.$active."</field>\n";

        $videoData = $videoRepository->videoDataToArray($video, $language);
        $xml.='<field name="video_data" update="set"><![CDATA['. serialize($videoData) ."]]></field>\n";
        $xml.= "</doc>\n";
        return $xml;
    }

}