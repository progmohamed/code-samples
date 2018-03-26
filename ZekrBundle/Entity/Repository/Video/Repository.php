<?php

namespace ZekrBundle\Entity\Repository\Video;

use AdminBundle\Entity\Language;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use ZekrBundle\Entity\Category;
use ZekrBundle\Entity\Hizb;
use ZekrBundle\Entity\Juz;
use ZekrBundle\Entity\Person;
use ZekrBundle\Entity\Rewaya;
use ZekrBundle\Entity\Surah;
use ZekrBundle\Entity\TempVideo;
use ZekrBundle\Entity\Video;
use ZekrBundle\Entity\Collection;
use ZekrBundle\Entity\VideoCollection;
use ZekrBundle\Entity\VideoType;
use ZekrBundle\Service\Search\Indexer\Indexer;
use ZekrBundle\Classes\FFmpeg;

class Repository extends EntityRepository
{

    private $dataGrid;
    private $frontData;

    public function getDataGrid()
    {
        if (!$this->dataGrid) {
            $this->dataGrid = new DataGrid($this->getEntityManager());
        }
        return $this->dataGrid;
    }

    public function getFrontData()
    {
        if (!$this->frontData) {
            $this->frontData = new FrontData($this->getEntityManager());
        }
        return $this->frontData;
    }

    public function add(Video $entity, TempVideo $tempVideo, $languages, $form, $indexer, $ffmpegBinary)
    {
        $em = $this->getEntityManager();

        foreach ($languages as $language) {
            $entity->translate($language->getLocale())->setTitle($form->get('title_' . $language->getLocale())->getData());
            $entity->translate($language->getLocale())->setDescription($form->get('description_' . $language->getLocale())->getData());
            $entity->translate($language->getLocale())->setDisplay($form->get('display_' . $language->getLocale())->getData());
        }
        $entity->mergeNewTranslations();

        if ($tempVideo->getFileAbsolutePath() && file_exists($tempVideo->getFileAbsolutePath())) {
            $ffmpeg = new FFmpeg($tempVideo->getFileAbsolutePath());
            $ffmpeg->setFFmpegBin($ffmpegBinary);
            if ($ffmpeg->isValid()) {
                $thumbmnailFilename = sha1(uniqid(mt_rand(), true)) . '.jpg';
                $thumbnailFullPath = $entity->getThumbFilUploadRootDir() . $thumbmnailFilename;
                $ffmpeg->getThumbnail($thumbnailFullPath, $entity->getThumbnailTime());
                $entity->setThumbnailFile($thumbmnailFilename);
            }
        }

        $entity->setConversionStatus(Video::STATUS_WAITING);
        $entity->setTempVideoFile($tempVideo->getFile());
        $entity->setDuration($tempVideo->getDuration());
        $em->persist($entity);
        $em->remove($tempVideo);
        $em->flush();
        $this->assignVideoToCollections($entity, $form->get('collection')->getData());


        foreach ($entity->getSurah() as $surah) {
            $surah->setNumTopics($this->getCounters('surah', $surah->getId()));
            $surah->setNumActiveTopics($this->getCounters('surah', $surah->getId(), true));
        }

        foreach ($entity->getHizb() as $hizb) {
            $hizb->setNumTopics($this->getCounters('hizb', $hizb->getId()));
            $hizb->setNumActiveTopics($this->getCounters('hizb', $hizb->getId(), true));
        }

        foreach ($entity->getJuz() as $juz) {
            $juz->setNumTopics($this->getCounters('juz', $juz->getId()));
            $juz->setNumTopics($this->getCounters('juz', $juz->getId(), true));
        }

        foreach ($entity->getPerson() as $person) {
            $person->setNumTopics($this->getCounters('person', $person->getId()));
            $person->setNumActiveTopics($this->getCounters('person', $person->getId(), true));
        }

        foreach ($entity->getVideoType() as $videoType) {
            $videoType->setNumTopics($this->getCounters('videoType', $videoType->getId()));
            $videoType->setNumActiveTopics($this->getCounters('videoType', $videoType->getId(), true));
        }

        $collectionRepository = $em->getRepository("ZekrBundle:Collection");
        $collectionForVideo = $this->getCollectionForVideo($entity->getId());
        foreach ($collectionForVideo as $collection) {
            /** @var Collection $collection * */
            $collection->setNumTopics($this->getCollectionCounters($collection->getId()));
            $collection->setNumActiveTopics($this->getCollectionCounters($collection->getId(), true));
            $collectionRepository->setThumbForCollection($collection);

        }

        if ($entity->getRewaya()) {
            $entity->getRewaya()->setNumTopics($this->getCounters('rewaya', $entity->getRewaya()->getId()));
            $entity->getRewaya()->setNumActiveTopics($this->getCounters('rewaya', $entity->getRewaya()->getId(), true));
        }

        $em->flush();
        $em->getRepository('ZekrBundle:Category')->getBuilder()->updateCategoriesVideoCounters($entity->getCategory());

        $indexer->index($entity);
    }

    public function addFtpVideo(Video $entity, $videoInfo, $languages, $form, $indexer)
    {
        $em = $this->getEntityManager();

        foreach ($languages as $language) {
            $entity->translate($language->getLocale())->setTitle($videoInfo['title']);
            $entity->translate($language->getLocale())->setDisplay($form->get('display_' . $language->getLocale())->getData());
        }
        $entity->mergeNewTranslations();

        $entity->setConversionStatus(Video::STATUS_WAITING);
        $entity->setTempVideoFile($videoInfo['file']);
        $entity->setThumbnailFile($videoInfo['thumbnail']);
        $entity->setDuration($videoInfo['duration']);
        $em->persist($entity);
        $em->flush();
        $this->assignVideoToCollections($entity, $form->get('collection')->getData());


        foreach ($entity->getSurah() as $surah) {
            $surah->setNumTopics($this->getCounters('surah', $surah->getId()));
            $surah->setNumActiveTopics($this->getCounters('surah', $surah->getId(), true));
        }

        foreach ($entity->getHizb() as $hizb) {
            $hizb->setNumTopics($this->getCounters('hizb', $hizb->getId()));
            $hizb->setNumActiveTopics($this->getCounters('hizb', $hizb->getId(), true));
        }

        foreach ($entity->getJuz() as $juz) {
            $juz->setNumTopics($this->getCounters('juz', $juz->getId()));
            $juz->setNumActiveTopics($this->getCounters('juz', $juz->getId(), true));
        }

        foreach ($entity->getPerson() as $person) {
            $person->setNumTopics($this->getCounters('person', $person->getId()));
            $person->setNumActiveTopics($this->getCounters('person', $person->getId(), true));
        }

        foreach ($entity->getVideoType() as $videoType) {
            $videoType->setNumTopics($this->getCounters('videoType', $videoType->getId()));
            $videoType->setNumActiveTopics($this->getCounters('videoType', $videoType->getId(), true));
        }

        $collectionRepository = $em->getRepository("ZekrBundle:Collection");
        $collectionForVideo = $this->getCollectionForVideo($entity->getId());
        foreach ($collectionForVideo as $collection) {
            $collection->setNumTopics($this->getCollectionCounters($collection->getId()));
            $collection->setNumActiveTopics($this->getCollectionCounters($collection->getId(), true));
            $collectionRepository->setThumbForCollection($collection);

        }

        if ($entity->getRewaya()) {
            $entity->getRewaya()->setNumTopics($this->getCounters('rewaya', $entity->getRewaya()->getId()));
            $entity->getRewaya()->setNumActiveTopics($this->getCounters('rewaya', $entity->getRewaya()->getId(), true));
        }

        $em->flush();
        $em->getRepository('ZekrBundle:Category')->getBuilder()->updateCategoriesVideoCounters($entity->getCategory());

        $indexer->index($entity);
    }

    public function edit($entity, $languages, $editForm, $indexer, $currentEntites = null, $ffmpegBinary)
    {
        $em = $this->getEntityManager();
        foreach ($languages as $language) {
            $entity->translate($language->getLocale(), false)->setTitle($editForm->get('title_' . $language->getLocale())->getData());
            $entity->translate($language->getLocale(), false)->setDescription($editForm->get('description_' . $language->getLocale())->getData());
            $entity->translate($language->getLocale(), false)->setDisplay($editForm->get('display_' . $language->getLocale())->getData());
        }
        $entity->mergeNewTranslations();

        if ($currentEntites['currentThumbnailTime'] != $entity->getThumbnailTime()) {
            // get new thumbnail
            if (null != $entity->getTempVideoFile() && $entity->getTempVideoFileAbsolutePath() && file_exists($entity->getTempVideoFileAbsolutePath())) {
                $fileAbsolutePath = $entity->getTempVideoFileAbsolutePath();
            } elseif (null != $entity->getHdFile() && $entity->getHdFileAbsolutePath() && file_exists($entity->getHdFileAbsolutePath())) {
                $fileAbsolutePath = $entity->getHdFileAbsolutePath();
            }

            $ffmpeg = new FFmpeg($fileAbsolutePath);
            $ffmpeg->setFFmpegBin($ffmpegBinary);
            if ($ffmpeg->isValid()) {
                $thumbmnailFilename = sha1(uniqid(mt_rand(), true)) . '.jpg';
                $thumbnailFullPath = $entity->getThumbFilUploadRootDir() . $thumbmnailFilename;
                $entity->deleteThembFile();
                $ffmpeg->getThumbnail($thumbnailFullPath, $entity->getThumbnailTime());
                $entity->setThumbnailFile($thumbmnailFilename);
            }
        }

        $this->assignVideoToCollections($entity, $editForm->get('collection')->getData());
        $em->flush();
        $em->getRepository('ZekrBundle:Category')->getBuilder()->updateCategoriesVideoCounters($entity->getCategory());


        $this->reculculateCurentEntities($currentEntites);

        foreach ($entity->getSurah() as $surah) {
            $surah->setNumTopics($this->getCounters('surah', $surah->getId()));
            $surah->setNumActiveTopics($this->getCounters('surah', $surah->getId(), true));
        }

        foreach ($entity->getHizb() as $hizb) {
            $hizb->setNumTopics($this->getCounters('hizb', $hizb->getId()));
            $hizb->setNumActiveTopics($this->getCounters('hizb', $hizb->getId(), true));
        }

        foreach ($entity->getJuz() as $juz) {
            $juz->setNumTopics($this->getCounters('juz', $juz->getId()));
            $juz->setNumActiveTopics($this->getCounters('juz', $juz->getId(), true));
        }

        foreach ($entity->getPerson() as $person) {
            $person->setNumTopics($this->getCounters('person', $person->getId()));
            $person->setNumActiveTopics($this->getCounters('person', $person->getId(), true));
        }

        foreach ($entity->getVideoType() as $videoType) {
            $videoType->setNumTopics($this->getCounters('videoType', $videoType->getId()));
            $videoType->setNumActiveTopics($this->getCounters('videoType', $videoType->getId(), true));
        }
        $collectionRepository = $em->getRepository("ZekrBundle:Collection");
        $collectionForVideo = $this->getCollectionForVideo($entity->getId());
        foreach ($collectionForVideo as $collection) {
            $collection->setNumTopics($this->getCollectionCounters($collection->getId()));
            $collection->setNumActiveTopics($this->getCollectionCounters($collection->getId(), true));
            $collectionRepository->setThumbForCollection($collection);
        }

        if ($entity->getRewaya()) {
            $entity->getRewaya()->setNumTopics($this->getCounters('rewaya', $entity->getRewaya()->getId()));
            $entity->getRewaya()->setNumActiveTopics($this->getCounters('rewaya', $entity->getRewaya()->getId(), true));
        }

        $em->flush();

        $indexer->index($entity);
    }


    public function delete($entity, $indexer)
    {
        /** @var Indexer $indexer */

        $videoCategories = $entity->getCategory();

        $em = $this->getEntityManager();

        $randomValue = sha1(uniqid(time(), true));
        $entity->setPlainSlug($randomValue);

        $em->remove($entity);
        $em->flush();

        $em->getRepository('ZekrBundle:Category')->getBuilder()->updateCategoriesVideoCounters($videoCategories);

        foreach ($entity->getSurah() as $surah) {
            $surah->setNumTopics($this->getCounters('surah', $surah->getId()));
            $surah->setNumActiveTopics($this->getCounters('surah', $surah->getId(), true));
        }

        foreach ($entity->getHizb() as $hizb) {
            $hizb->setNumTopics($this->getCounters('hizb', $hizb->getId()));
            $hizb->setNumActiveTopics($this->getCounters('hizb', $hizb->getId(), true));
        }

        foreach ($entity->getJuz() as $juz) {
            $juz->setNumTopics($this->getCounters('juz', $juz->getId()));
            $juz->setNumActiveTopics($this->getCounters('juz', $juz->getId(), true));
        }

        foreach ($entity->getPerson() as $person) {
            $person->setNumTopics($this->getCounters('person', $person->getId()));
            $person->setNumActiveTopics($this->getCounters('person', $person->getId(), true));
        }

        foreach ($entity->getVideoType() as $videoType) {
            $videoType->setNumTopics($this->getCounters('videoType', $videoType->getId()));
            $videoType->setNumActiveTopics($this->getCounters('videoType', $videoType->getId(), true));
        }

        $collectionRepository = $em->getRepository("ZekrBundle:Collection");
        $collectionForVideo = $this->getCollectionForVideo($entity->getId());
        foreach ($collectionForVideo as $collection) {
            $collection->setNumTopics($this->getCollectionCounters($collection->getId()));
            $collection->setNumActiveTopics($this->getCollectionCounters($collection->getId(), true));
            $collectionRepository->setThumbForCollection($collection);
        }


        if ($entity->getRewaya()) {
            $entity->getRewaya()->setNumTopics($this->getCounters('rewaya', $entity->getRewaya()->getId()));
            $entity->getRewaya()->setNumActiveTopics($this->getCounters('rewaya', $entity->getRewaya()->getId(), true));
        }

        $em->flush();
        $indexer->removeIndex($entity->getId());
    }


    public function videoDataToArray(Video $video, Language $language)
    {
        $locale = $language->getLocale();

        $out = [];

        /** @var Rewaya $rewaya */
        $rewaya = $video->getRewaya();
        if (null !== $rewaya) {
            $out['rewaya'] = (object)[
                'id' => $rewaya->getId(),
                'slug' => $rewaya->getSlug(),
                'name' => $rewaya->translate($locale)->getName(),
            ];
        }

        /** @var Person $person */
        foreach ($video->getPerson() as $person) {
            $out['person'][] = (object)[
                'id' => $person->getId(),
                'slug' => $person->getPlainSlug(),
                'name' => $person->translate($locale)->getName(),
            ];
        }

        /** @var Category $category */
        foreach ($video->getCategory() as $category) {
            $out['category'][] = (object)[
                'id' => $category->getId(),
                'slug' => $category->getSlug(),
                'name' => $category->translate($locale)->getName(),
            ];
        }

        /** @var VideoCollection $videoCollection */
        foreach ($video->getVideoCollections() as $videoCollection) {
            $collection = $videoCollection->getCollection();
            $out['collection'][] = (object)[
                'id' => $collection->getId(),
                'slug' => $collection->getSlug(),
                'name' => $collection->translate($locale)->getName(),
            ];
        }

        /** @var Hizb $hizb */
        foreach ($video->getHizb() as $hizb) {
            $out['hizb'][] = (object)[
                'id' => $hizb->getId(),
                'slug' => $hizb->getSlug(),
                'name' => $hizb->translate($locale)->getName(),
            ];
        }

        /** @var Juz $juz */
        foreach ($video->getJuz() as $juz) {
            $out['juz'][] = (object)[
                'id' => $juz->getId(),
                'slug' => $juz->getSlug(),
                'name' => $juz->translate($locale)->getName(),
            ];
        }

        /** @var Surah $surah */
        foreach ($video->getSurah() as $surah) {
            $out['surah'][] = (object)[
                'id' => $surah->getId(),
                'slug' => $surah->getSlug(),
                'name' => $surah->translate($locale)->getName(),
            ];
        }

        /** @var VideoType $videoType */
        foreach ($video->getVideoType() as $videoType) {
            $out['video_type'][] = (object)[
                'id' => $videoType->getId(),
                'slug' => $videoType->getSlug(),
                'name' => $videoType->translate($locale)->getName(),
            ];
        }
        return $out;
    }


    public function assignVideoToCollections(Video $video, ArrayCollection $collections)
    {
        $em = $this->getEntityManager();
        $videoCollectionRepository = $em->getRepository("ZekrBundle:VideoCollection");
        $previousCollections = $videoCollectionRepository->findBy(['video' => $video]);

        $idsForCollectionFromForm = [];

        //from form
        foreach ($collections as $collection) {
            $VideoCollection = $videoCollectionRepository->findOneBy(['video' => $video, 'collection' => $collection]);
            if (!$VideoCollection) {
                $videoCollection = new VideoCollection();
                $videoCollection->setVideo($video)
                    ->setCollection($collection)
                    ->setSortOrder($this->getMaxOrderForVideosCollection($collection) + 1);
                $em->persist($videoCollection);
            }
            $idsForCollectionFromForm[] = $collection->getId();
        }
        $em->flush();


        //from database
        foreach ($previousCollections as $dbcollection) {
            if (!in_array($dbcollection->getCollection()->getId(), $idsForCollectionFromForm)) {
                $em->remove($dbcollection);
            }

        }

    }

    public function getCollectionForVideo($videoId)
    {
        $em = $this->getEntityManager();
        $dql = "SELECT c
        FROM ZekrBundle:Collection c
        INNER JOIN ZekrBundle:VideoCollection vc WITH c.id = vc.collection
        WHERE vc.video = :video";
        $query = $em->createQuery($dql);
        $query->setParameter('video', $videoId);
        return $query->getResult();

    }

    public function getMaxOrderForVideosCollection($collection)
    {
        $em = $this->getEntityManager();
        $dql = "SELECT MAX(vc.sortOrder)
        FROM ZekrBundle:VideoCollection vc
        WHERE vc.collection = :collection";
        $query = $em->createQuery($dql);
        $query->setParameter('collection', $collection);
        $max = $query->getSingleScalarResult();
        return $max;
    }

    public function reculculateCurentEntities($currentEntites)
    {
        $em = $this->getEntityManager();
        if (!empty($currentEntites['currentSurah'])) {
            $currentSurah = $this->getCurrentEntitiesByIds('Surah', $currentEntites['currentSurah']);
            foreach ($currentSurah as $surah) {
                $surah->setNumTopics($this->getCounters('surah', $surah->getId()));
                $surah->setNumActiveTopics($this->getCounters('surah', $surah->getId(), true));
            }
        }
        if (!empty($currentEntites['currentHizb'])) {
            $currentHizb = $this->getCurrentEntitiesByIds('Hizb', $currentEntites['currentHizb']);
            foreach ($currentHizb as $hizb) {
                $hizb->setNumTopics($this->getCounters('hizb', $hizb->getId()));
                $hizb->setNumActiveTopics($this->getCounters('hizb', $hizb->getId(), true));
            }
        }
        if (!empty($currentEntites['currentJuz'])) {
            $currentJuz = $this->getCurrentEntitiesByIds('Juz', $currentEntites['currentJuz']);
            foreach ($currentJuz as $juz) {
                $juz->setNumTopics($this->getCounters('juz', $juz->getId()));
                $juz->setNumActiveTopics($this->getCounters('juz', $juz->getId(), true));
            }
        }

        if (!empty($currentEntites['currentPerson'])) {
            $currentPerson = $this->getCurrentEntitiesByIds('Person', $currentEntites['currentPerson']);
            foreach ($currentPerson as $person) {
                $person->setNumTopics($this->getCounters('person', $person->getId()));
                $person->setNumActiveTopics($this->getCounters('person', $person->getId(), true));
            }
        }

        if (!empty($currentEntites['currentVideoType'])) {
            $currentVideoType = $this->getCurrentEntitiesByIds('VideoType', $currentEntites['currentVideoType']);
            foreach ($currentVideoType as $videoType) {
                $videoType->setNumTopics($this->getCounters('videoType', $videoType->getId()));
                $videoType->setNumActiveTopics($this->getCounters('videoType', $videoType->getId(), true));
            }
        }

        if (!empty($currentEntites['currentCollection'])) {
            $collectionRepository = $em->getRepository("ZekrBundle:Collection");
            $currentCollection = $this->getCurrentEntitiesByIds('Collection', $currentEntites['currentCollection']);
            foreach ($currentCollection as $collection) {
                $collection->setNumTopics($this->getCollectionCounters($collection->getId()));
                $collection->setNumActiveTopics($this->getCollectionCounters($collection->getId(), true));
                $collectionRepository->setThumbForCollection($collection);
            }
        }
        if (!empty($currentEntites['currentRewaya'])) {
            $currentRewaya = $this->getCurrentEntitiesByIds('Rewaya', $currentEntites['currentRewaya']);
            foreach ($currentRewaya as $rewaya) {
                $rewaya->setNumTopics($this->getCounters('rewaya', $rewaya->getId()));
                $rewaya->setNumActiveTopics($this->getCounters('rewaya', $rewaya->getId(), true));
            }
        }

    }


    private function getCurrentEntitiesByIds($table, $ids)
    {
        $em = $this->getEntityManager();
        $dql = "SELECT p
        FROM ZekrBundle:$table p
        WHERE p.id IN (:ids)";

        $query = $em->createQuery($dql);
        $query->setParameter('ids', $ids);
        return $query->getResult();

    }

    private function getCounters($table, $id, $active = null)
    {
        $em = $this->getEntityManager();

        $dql = "SELECT COUNT(v.id)
        FROM ZekrBundle:Video v
        INNER JOIN v.$table p
        WHERE v.deletedAt is NULL 
        AND p.id = :id ";

        $parameters['id'] = $id;
        if ($active) {
            $dql .= "AND v.active = true ";
        }
        $query = $em->createQuery($dql);
        if (sizeof($parameters)) {
            $query->setParameters($parameters);
        }
        return $query->getSingleScalarResult();

    }

    public function getCollectionCounters($id, $active = null)
    {
        $em = $this->getEntityManager();

        $dql = "SELECT COUNT(v.id)
        FROM ZekrBundle:Video v
        INNER JOIN ZekrBundle:VideoCollection vc WITH v.id = vc.video
        WHERE v.deletedAt is NULL 
        AND vc.collection = :id ";

        $parameters['id'] = $id;
        if ($active) {
            $dql .= "AND v.active = true ";
        }

        $query = $em->createQuery($dql);
        if (sizeof($parameters)) {
            $query->setParameters($parameters);
        }
        return $query->getSingleScalarResult();

    }

    public function getVideoForShow($slug, $locale)
    {
        $em = $this->getEntityManager();
        $dql = "SELECT  v, vt, j, jt, h, ht, s, st, p, pt, r, rt, c, ct, vtt, vttt
        FROM ZekrBundle:Video v
        INNER JOIN v.translations vt
        LEFT JOIN v.juz j
        LEFT JOIN j.translations jt
        
        LEFT JOIN v.hizb h
        LEFT JOIN h.translations ht
        
        LEFT JOIN v.surah s
        LEFT JOIN s.translations st
        
        LEFT JOIN v.person p
        LEFT JOIN p.translations pt
        
        LEFT JOIN v.rewaya r
        LEFT JOIN r.translations rt
        
        LEFT JOIN v.category c
        LEFT JOIN c.translations ct
        
        LEFT JOIN v.videoType vtt
        LEFT JOIN vtt.translations vttt
        
        WHERE v.slug = :slug  
        AND v.deletedAt is NULL
        AND v.tempVideo is NULL
        AND v.tempVideoFile is NULL
        AND v.active = TRUE
        AND vt.locale = :locale
        AND vt.display = TRUE 
        ";

        $query = $em->createQuery($dql);
        $query->setParameter('slug', $slug);
        $query->setParameter('locale', $locale);
        return $query->getSingleResult();
    }


    public function getConcatenateValue()
    {
        $em = $this->getEntityManager();
        $dql = "SELECT MAX(v.id)
        FROM ZekrBundle:Video v";
        $query = $em->createQuery($dql);
        $max = $query->getSingleScalarResult();
        return $max;
    }

    public function saveVideoCategories(Video $video, array $categoryIds, $indexer)
    {
        $em = $this->getEntityManager();
        $categoryRepository = $em->getRepository('ZekrBundle:Category');
        $categories = [];
        foreach ($categoryIds as $categoryId) {
            $category = $categoryRepository->find($categoryId);
            if ($category) {
                $categories[] = $category;
            }
        }
        $remainingCategoryIds = [];
        $deletedCategories = [];
        foreach ($video->getCategory() as $videoCategory) {
            $categoryId = $videoCategory->getId();
            if (!in_array($categoryId, $categoryIds)) {
                $deletedCategories[] = $videoCategory;
                $video->removeCategory($videoCategory);
            } else {
                $remainingCategoryIds[] = $categoryId;
            }
        }
        $em->flush();
        foreach ($categories as $category) {
            if (!in_array($category->getId(), $remainingCategoryIds)) {
                $video->addCategory($category);
            }
        }
        $em->flush();
        $em->getRepository('ZekrBundle:Category')->getBuilder()->updateCategoriesVideoCounters($deletedCategories);
        $em->getRepository('ZekrBundle:Category')->getBuilder()->updateCategoriesVideoCounters($video->getCategory());
        $indexer->index($video);

    }

    public function saveVideoVideoTypes(Video $video, array $typesIds, $indexer)
    {
        $em = $this->getEntityManager();
        $videoTypeRepository = $em->getRepository('ZekrBundle:VideoType');
        $types = [];

        foreach ($typesIds as $typeId) {
            $type = $videoTypeRepository->find($typeId);
            if ($type) {
                $types[] = $type;
            }
        }

        $remainingTypesIds = [];
        $deletedTypes['currentVideoType'] = [];
        foreach ($video->getVideoType() as $videoType) {
            $typeId = $videoType->getId();
            if (!in_array($typeId, $typesIds)) {
                $deletedTypes['currentVideoType'][] = $typeId;
                $video->removeVideoType($videoType);
            } else {
                $remainingTypesIds[] = $typeId;
            }
        }

        foreach ($types as $type) {
            if (!in_array($type->getId(), $remainingTypesIds)) {
                $video->addVideoType($type);
            }
        }
        $em->flush();

        foreach ($video->getVideoType() as $videoType) {
            $videoType->setNumTopics($this->getCounters('videoType', $videoType->getId()));
            $videoType->setNumActiveTopics($this->getCounters('videoType', $videoType->getId(), true));
        }
        $this->reculculateCurentEntities($deletedTypes);
        $em->flush();
        $indexer->index($video);
    }


    public function saveVideoClassifications(Video $video, array $ids, $indexer)
    {
        $this->saveVideoSurahs($video, $ids['surah']);
        $this->saveVideoHizb($video, $ids['hizb']);
        $this->saveVideoJuz($video, $ids['juz']);
        $indexer->index($video);
    }

    public function saveVideoSurahs(Video $video, array $surahsIds)
    {
        $em = $this->getEntityManager();
        $surahRepository = $em->getRepository('ZekrBundle:Surah');
        $surahs = [];

        foreach ($surahsIds as $surahId) {
            $surah = $surahRepository->find($surahId);
            if ($surah) {
                $surahs[] = $surah;
            }
        }

        $remainingSurahsIds = [];
        $deletedSurahs['currentSurah'] = [];
        foreach ($video->getSurah() as $surah) {
            $surahId = $surah->getId();
            if (!in_array($surahId, $surahsIds)) {
                $deletedSurahs['currentSurah'][] = $surahId;
                $video->removeSurah($surah);
            } else {
                $remainingSurahsIds[] = $surahId;
            }
        }

        foreach ($surahs as $surah) {
            if (!in_array($surah->getId(), $remainingSurahsIds)) {
                $video->addSurah($surah);
            }
        }
        $em->flush();

        foreach ($video->getSurah() as $surah) {
            $surah->setNumTopics($this->getCounters('surah', $surah->getId()));
            $surah->setNumActiveTopics($this->getCounters('surah', $surah->getId(), true));
        }
        $this->reculculateCurentEntities($deletedSurahs);
        $em->flush();
    }

    public function saveVideoHizb(Video $video, array $hizbsIds)
    {
        $em = $this->getEntityManager();
        $hizbRepository = $em->getRepository('ZekrBundle:Hizb');
        $hizbs = [];

        foreach ($hizbsIds as $hizbId) {
            $hizb = $hizbRepository->find($hizbId);
            if ($hizb) {
                $hizbs[] = $hizb;
            }
        }

        $remainingHizbsIds = [];
        $deletedHizbs['currentHizb'] = [];
        foreach ($video->getHizb() as $hizb) {
            $hizbId = $hizb->getId();
            if (!in_array($hizbId, $hizbsIds)) {
                $deletedHizbs['currentHizb'][] = $hizbId;
                $video->removeHizb($hizb);
            } else {
                $remainingHizbsIds[] = $hizbId;
            }
        }

        foreach ($hizbs as $hizb) {
            if (!in_array($hizb->getId(), $remainingHizbsIds)) {
                $video->addHizb($hizb);
            }
        }
        $em->flush();

        foreach ($video->getHizb() as $hizb) {
            $hizb->setNumTopics($this->getCounters('hizb', $hizb->getId()));
            $hizb->setNumActiveTopics($this->getCounters('hizb', $hizb->getId(), true));
        }
        $this->reculculateCurentEntities($deletedHizbs);
        $em->flush();
    }

    public function saveVideoJuz(Video $video, array $juzsIds)
    {
        $em = $this->getEntityManager();
        $juzRepository = $em->getRepository('ZekrBundle:Juz');
        $juzs = [];

        foreach ($juzsIds as $juzId) {
            $juz = $juzRepository->find($juzId);
            if ($juz) {
                $juzs[] = $juz;
            }
        }

        $remainingJuzsIds = [];
        $deletedJuzs['currentJuz'] = [];
        foreach ($video->getJuz() as $juz) {
            $juzId = $juz->getId();
            if (!in_array($juzId, $juzsIds)) {
                $deletedJuzs['currentJuz'][] = $juzId;
                $video->removeJuz($juz);
            } else {
                $remainingJuzsIds[] = $juzId;
            }
        }

        foreach ($juzs as $juz) {
            if (!in_array($juz->getId(), $remainingJuzsIds)) {
                $video->addJuz($juz);
            }
        }
        $em->flush();

        foreach ($video->getJuz() as $juz) {
            $juz->setNumTopics($this->getCounters('juz', $juz->getId()));
            $juz->setNumActiveTopics($this->getCounters('juz', $juz->getId(), true));
        }
        $this->reculculateCurentEntities($deletedJuzs);
        $em->flush();
    }

    public function getVideo($locale, $projectSlug, $videoId)
    {
        $em = $this->getEntityManager();

        $dql = "SELECT v
        FROM ZekrBundle:Video v
        INNER JOIN v.translations vt 
        INNER JOIN v.apiProject  va 
        WHERE v.id = :videoId
        AND va.slug = :project_key 
        AND va.active = true 
        AND v.deletedAt is null
        AND v.active = true 
        AND v.tempVideo is null
        AND v.tempVideoFile is null 
        AND vt.locale = :locale
        AND vt.display = true
        ";

        $parameters['videoId'] = $videoId;
        $parameters['project_key'] = $projectSlug;
        $parameters['locale'] = $locale;

        $query = $em->createQuery($dql);
        if (sizeof($parameters)) {
            $query->setParameters($parameters);
        }

        $result = $query->getOneOrNullResult();
        /**
         * @var $result Video
         */
        if ($result && true == $result->shouldBeActive()) {
            $data = $result;
        } else {
            $data = null;
        }
        return $data;
    }

    public function getVideoList($locale, $paginator, $projectSlug, $page, $limit, $sortField, $sortDirection, $type = null, $typeId = null)
    {
        $em = $this->getEntityManager();

        if ($type == 'video_type') {
            $type = 'videoType';
        }

        $dql = "SELECT v
        FROM ZekrBundle:Video v
        INNER JOIN v.translations vt 
        INNER JOIN v.apiProject  va ";
        if (null !== $type) {
            $dql .= "  INNER JOIN v.$type t ";
        }
        $dql .= "WHERE va.slug = :project_key 
        AND v.deletedAt is null
        AND v.active = true 
        AND v.tempVideo is null
        AND v.tempVideoFile is null 
        AND vt.locale = :locale
        AND vt.display = true
        ";
        if (null !== $typeId) {
            $dql .= " AND t.id = :typeId ";
            $parameters['typeId'] = $typeId;
        }

        if ($sortField == 'id') {
            $dql .= ' ORDER BY v.id ' . $sortDirection;
        } elseif ($sortField == 'title') {
            $dql .= ' ORDER By  vt.title ' . $sortDirection;
        } else {
            $dql .= ' ORDER By v.insertedAt ' . $sortDirection;
        }

        $parameters['project_key'] = $projectSlug;
        $parameters['locale'] = $locale;

        $query = $em->createQuery($dql);

        if (!empty($parameters) && sizeof($parameters)) {
            $query->setParameters($parameters);
        }

        return $paginator->paginate(
            $query,
            $page,
            $limit,
            array('wrap-queries' => true)
        );
    }

    public function getSelectedVideoList($locale, $paginator, $projectSlug, $page, $limit)
    {
        $em = $this->getEntityManager();
        $dql = "SELECT v
        FROM ZekrBundle:Video v
        INNER JOIN v.translations vt 
        INNER JOIN v.apiProject  va 
        WHERE va.slug = :project_key
        AND v.selected = true 
        AND v.deletedAt is null
        AND v.active = true 
        AND v.tempVideo is null
        AND v.tempVideoFile is null 
        AND vt.locale = :locale
        AND vt.display = true
        ORDER By v.insertedAt
        ";

        $parameters['project_key'] = $projectSlug;
        $parameters['locale'] = $locale;

        $query = $em->createQuery($dql);

        if (!empty($parameters) && sizeof($parameters)) {
            $query->setParameters($parameters);
        }

        return $paginator->paginate(
            $query,
            $page,
            $limit,
            array('wrap-queries' => true)
        );
    }


    public function getVideoListForCollection($locale, $projectSlug, $collectionId)
    {
        $em = $this->getEntityManager();
        $parameters['project_key'] = $projectSlug;
        $dql = "SELECT  v
        FROM ZekrBundle:Video v
        INNER JOIN v.translations vt
        INNER JOIN ZekrBundle:VideoCollection vc WITH vc.video = v 
        INNER JOIN vc.collection c 
        INNER JOIN c.translations ct 
        INNER JOIN c.apiProject ca
        
        WHERE c.active = true
        AND c.deletedAt is null
        AND c.id = :collectionId 
        
        AND ct.locale = :locale
        AND ct.display = true
        
        AND ca.active = true
        AND ca.slug = :project_key 
        
        AND v.deletedAt is null
        AND v.active = true 
        AND v.tempVideo is null
        AND v.tempVideoFile is null 
        AND vt.locale = :locale
        AND vt.display = true
        
        ORDER BY vc.sortOrder 
         ";
        $parameters['collectionId'] = $collectionId;
        $parameters['locale'] = $locale;

        $query = $em->createQuery($dql);
        if (sizeof($parameters)) {
            $query->setParameters($parameters);
        }
        return $query->getResult();
    }
}