<?php

namespace ZekrBundle\Service\Search\Indexer;

use ZekrBundle\Entity\Video;
use AdminBundle\Entity\Language;
use ZekrBundle\Entity\VideoType;

class videoTypeIndexer
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

    public function delete(VideoType $videoType)
    {
        $languages = $this->getContainer()->get('admin.admin_helper')->getLanguages();
        foreach ($languages as $language) {
            $this->deleteFromCore($videoType, $language);
        }
    }

    public function edit(VideoType $videoType)
    {
        $languages = $this->getContainer()->get('admin.admin_helper')->getLanguages();
        foreach ($languages as $language) {
            $this->editInCore($videoType, $language);
        }
    }

    private function deleteFromCore(VideoType $videoType, Language $language)
    {
        $clientName = 'zekr_' . $language->getLocale();

        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->getConnection()->getConfiguration()->setSQLLogger(null);

        $maxResults = 50;
        $repository = $em->getRepository('ZekrBundle:Video');
        $where ='v.deletedAt IS NULL AND vt = :videoType';
        $qb = $repository->createQueryBuilder('v')
            ->select('v')
            ->innerJoin('v.videoType','vt')
            ->where($where)
            ->orderBy('v.id');

        $parameters = [
            'videoType' => $videoType
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
                    $xmlEntry = $this->getDeleteXMLEntry($video, $videoType, $language);
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

            //$command = "D:/wamp64/solr-6.4.2/bin/post -c fatawa_ar -p 81 {$xmlFilePath}";
            //$command = "java -Durl=http://localhost:81/solr/fatawa_ar/update -Dtype=application/xml -jar D:/solr6/example/exampledocs/post.jar {$xmlFilePath} ";
            //$command = "java -Durl=http://admin:123@127.0.0.1:81/solr/fatawa_ar/update -Dtype=application/xml -jar D:/wamp64/solr-6.4.2/example/exampledocs/post.jar {$xmlFilePath} ";
            //$command = "java -Durl=http://admin:123@127.0.0.1:81/solr/".$clientName."/update -Dtype=application/xml -jar D:/wamp64/solr-6.4.2/example/exampledocs/post.jar {$xmlFilePath} ";
            $command = "java -Durl=http://127.0.0.1:81/solr/".$clientName."/update -Dtype=application/xml -jar D:/wamp64/solr-6.4.2/example/exampledocs/post.jar {$xmlFilePath} ";
            exec($command);
        }catch(\Exception $e) {
            //log error
        }
        if(file_exists($xmlFilePath)) {
            unlink($xmlFilePath);
        }
    }

    private function editInCore(VideoType $videoType, Language $language)
    {
        $clientName = 'zekr_' . $language->getLocale();

        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->getConnection()->getConfiguration()->setSQLLogger(null);

        $maxResults = 50;
        $repository = $em->getRepository('ZekrBundle:Video');
        $where ='v.deletedAt IS NULL AND vt = :videoType';
        $qb = $repository->createQueryBuilder('v')
            ->select('v')
            ->innerJoin('v.videoType','vt')
            ->where($where)
            ->orderBy('v.id');

        $parameters = [
            'videoType' => $videoType
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
                    $xmlEntry = $this->getEditXMLEntry($video, $videoType, $language);
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

    private function getDeleteXMLEntry(Video $video, VideoType $videoType, Language $language)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $videoRepository = $em->getRepository('ZekrBundle:Video');

        $xml ="<doc>\n";
        $xml.='<field name="id">'.$video->getId()."</field>\n";
        $xml.='<field name="video_type_id" update="remove">'.$videoType->getId()."</field>\n";

        $videoData = $videoRepository->videoDataToArray($video, $language);
        $xml.='<field name="video_data" update="set"><![CDATA['. \serialize($videoData) ."]]></field>\n";

        $xml.= "</doc>\n";
        return $xml;
    }

    private function getEditXMLEntry(Video $video, VideoType $videoType, Language $language)
    {
        $active = $videoType->getActive() && $video->shouldBeActive() ? 'true' : 'false';

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