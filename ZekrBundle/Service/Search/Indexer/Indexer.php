<?php

namespace ZekrBundle\Service\Search\Indexer;

use ZekrBundle\Entity\Video;
use AdminBundle\Entity\Language;

class Indexer extends IndexerAbstract
{

    protected $categoryIndexer;
    protected $surahIndexer;
    protected $juzIndexer;
    protected $hizbIndexer;
    protected $videoTypeIndexer;
    protected $collectionIndexer;
    protected $rewayaIndexer;
    protected $personIndexer;

    public function getCategoryIndexer()
    {
        if(!$this->categoryIndexer) {
            $this->categoryIndexer = new CategoryIndexer($this->getContainer());
        }
        return $this->categoryIndexer;
    }

    public function getSurahIndexer()
    {
        if(!$this->surahIndexer) {
            $this->surahIndexer = new SurahIndexer($this->getContainer());
        }
        return $this->surahIndexer;
    }

    public function getJuzIndexer()
    {
        if(!$this->juzIndexer) {
            $this->juzIndexer = new JuzIndexer($this->getContainer());
        }
        return $this->juzIndexer;
    }

    public function getHizbIndexer()
    {
        if(!$this->hizbIndexer) {
            $this->hizbIndexer = new HizbIndexer($this->getContainer());
        }
        return $this->hizbIndexer;
    }

    public function getVideoTypeIndexer()
    {
        if(!$this->videoTypeIndexer) {
            $this->videoTypeIndexer = new videoTypeIndexer($this->getContainer());
        }
        return $this->videoTypeIndexer;
    }

    public function getCollectionIndexer()
    {
        if(!$this->collectionIndexer) {
            $this->collectionIndexer = new CollectionIndexer($this->getContainer());
        }
        return $this->collectionIndexer;
    }

    public function getRewayaIndexer()
    {
        if(!$this->rewayaIndexer) {
            $this->rewayaIndexer = new RewayaIndexer($this->getContainer());
        }
        return $this->rewayaIndexer;
    }

    public function getPersonIndexer()
    {
        if(!$this->personIndexer) {
            $this->personIndexer = new PersonIndexer($this->getContainer());
        }
        return $this->personIndexer;
    }

    public function generateVideoDocument(Video $video, Language $language)
    {
        $locale = $language->getLocale();
        $update = $this->getUpdate();
        $document = $update->createDocument();
        $document->id = $video->getId();
        $document->active = $video->shouldBeActive();
        $document->slug = $video->getSlug();
        $document->thumbnail_file = $video->getThumbnailFile();
        $document->duration = $video->getDuration();
        $document->inserted_at = $video->getInsertedAt();
        $document->title = $video->translate($locale)->getTitle();
        $document->description = $video->translate($locale)->getDescription();

        $document->hd_file = $video->getHdFile();
        $document->hd_file_size = $video->getHdFileSize();
        $document->sd_file = $video->getSdFile();
        $document->sd_file_size = $video->getSdFileSize();
        $document->mp3_file = $video->getMp3File();
        $document->mp3_file_size = $video->getMp3FileSize();

        $em = $this->getContainer()->get('doctrine')->getManager();
        $videoRepository = $em->getRepository('ZekrBundle:Video');
        $videoData = $videoRepository->videoDataToArray($video, $language);
        $document->video_data = serialize($videoData);

        if (null !== $video->getRewaya()) {
            $document->rewaya_id = $video->getRewaya()->getId();
        }
        foreach ($video->getCategory() as $category) {
            $document->addField('category_id', $category->getId());
        }
        foreach ($video->getVideoCollections() as $videoCollection) {
            $collection = $videoCollection->getCollection();
            $document->addField('collection_id', $collection->getId());
        }
        foreach ($video->getHizb() as $hizb) {
            $document->addField('hizb_id', $hizb->getId());
        }
        foreach ($video->getJuz() as $juz) {
            $document->addField('juz_id', $juz->getId());
        }
        foreach ($video->getPerson() as $person) {
            $document->addField('person_id', $person->getId());
        }
        foreach ($video->getVideoType() as $videoType) {
            $document->addField('video_type_id', $videoType->getId());
        }
        foreach ($video->getSurah() as $surah) {
            $document->addField('surah_id', $surah->getId());
        }
        return $document;
    }

    public function index(Video $video)
    {
        $languages = $this->getContainer()->get('admin.admin_helper')->getLanguages();
        foreach ($languages as $language) {
            $this->setClientName('solarium.client.zekr_' . $language->getLocale());
            if ($video->translate($language->getLocale())->getDisplay()) {
                $document = $this->generateVideoDocument($video, $language);
                $this->indexDocument($document);
            }else{
                $this->deleteDocument($video->getId());
            }
        }
    }

    public function removeIndex($id)
    {
        $languages = $this->getContainer()->get('admin.admin_helper')->getLanguages();
        foreach ($languages as $language) {
            $this->setClientName('solarium.client.zekr_' . $language->getLocale());
            $this->deleteDocument($id);
        }
    }

    public function getFullXMLEntry(Video $video, Language $language)
    {
        $locale = $language->getLocale();

        $xml = "<doc>\n";
        $xml.='<field name="id">'.$video->getId()."</field>\n";
        $xml.='<field name="active">'.($video->shouldBeActive() ? 'true':'false' )."</field>\n";
        $xml.='<field name="slug"><![CDATA['.$video->getSlug()."]]></field>\n";
        if($video->getThumbnailFile()) {
            $xml .= '<field name="thumbnail_file"><![CDATA[' . $video->getThumbnailFile() . "]]></field>\n";
        }
        $xml.='<field name="duration"><![CDATA['.$video->getDuration()."]]></field>\n";
        $xml.='<field name="inserted_at"><![CDATA['.$video->getInsertedAt()->format('Y-m-d\TH:i:s\Z')."]]></field>\n";
        $xml.='<field name="title"><![CDATA['.$video->translate($locale)->getTitle()."]]></field>\n";
        $xml.='<field name="description"><![CDATA['.$video->translate($locale)->getDescription()."]]></field>\n";

        if($video->getHdFile()) {
            $xml .= '<field name="hd_file"><![CDATA[' . $video->getHdFile() . "]]></field>\n";
        }
        if($video->getHdFileSize()) {
            $xml .= '<field name="hd_file_size">' . $video->getHdFileSize() . "</field>\n";
        }

        if($video->getSdFile()) {
            $xml .= '<field name="sd_file"><![CDATA[' . $video->getSdFile() . "]]></field>\n";
        }
        if($video->getSdFileSize()) {
            $xml .= '<field name="sd_file_size">' . $video->getSdFileSize() . "</field>\n";
        }

        if($video->getMp3File()) {
            $xml .= '<field name="mp3_file"><![CDATA[' . $video->getMp3File() . "]]></field>\n";
        }
        if($video->getMp3FileSize()) {
            $xml .= '<field name="mp3_file_size">' . $video->getMp3FileSize() . "</field>\n";
        }

        $em = $this->getContainer()->get('doctrine')->getManager();
        $videoRepository = $em->getRepository('ZekrBundle:Video');
        $videoData = $videoRepository->videoDataToArray($video, $language);
        $xml.='<field name="video_data"><![CDATA['.serialize($videoData)."]]></field>\n";

        if (null !== $video->getRewaya()) {
            $xml.='<field name="rewaya_id">'.$video->getRewaya()->getId()."</field>\n";
        }
        foreach ($video->getCategory() as $category) {
            $xml.='<field name="category_id">'.$category->getId()."</field>\n";
        }
        foreach ($video->getVideoCollections() as $videoCollection) {
            $collection = $videoCollection->getCollection();
            $xml.='<field name="collection_id">'.$collection->getId()."</field>\n";
        }
        foreach ($video->getHizb() as $hizb) {
            $xml.='<field name="hizb_id">'.$hizb->getId()."</field>\n";
        }
        foreach ($video->getJuz() as $juz) {
            $xml.='<field name="juz_id">'.$juz->getId()."</field>\n";
        }
        foreach ($video->getPerson() as $person) {
            $xml.='<field name="person_id">'.$person->getId()."</field>\n";
        }
        foreach ($video->getVideoType() as $videoType) {
            $xml.='<field name="video_type_id">'.$videoType->getId()."</field>\n";
        }
        foreach ($video->getSurah() as $surah) {
            $xml.='<field name="surah_id">'.$surah->getId()."</field>\n";
        }
        $xml.= "</doc>\n";

        $xml = mb_ereg_replace("\x00",'', $xml);


        return $xml;
    }
}
