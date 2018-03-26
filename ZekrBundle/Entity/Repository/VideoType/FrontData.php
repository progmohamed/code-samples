<?php

namespace ZekrBundle\Entity\Repository\VideoType;

use Doctrine\ORM\EntityRepository;
use ZekrBundle\Entity\Video;
class FrontData
{
    protected $em;

    protected $formData;
    function __construct($em)
    {
        $this->em = $em;
    }

    public function getRandomType($locale)
    {
        $em = $this->getEntityManager();
        $dql = "SELECT t, tt
        FROM ZekrBundle:VideoType t
        INNER JOIN t.translations tt
        WHERE t.active = true
        AND tt.locale = :locale
        ORDER BY RAND() ";

        $parameters['locale'] = $locale;
        $query = $em->createQuery($dql)
            ->setMaxResults(1);

        if (sizeof($parameters)) {
            $query->setParameters($parameters);
        }

        $type = $query->getOneOrNullResult();
        $videos = $this->getListVideosForType($type, $locale, 7);
        $out['type'] = $type;
        $out['videos'] = $videos;
        return $out;

    }

    public function getListVideosForType($type, $locale, $maxResult = null)
    {
        $em = $this->getEntityManager();
        $dql = "SELECT v, vt
        FROM ZekrBundle:Video v
        INNER JOIN v.translations vt
        INNER JOIN v.videoType vtt
        WHERE v.tempVideo is null
        AND v.tempVideoFile is null
        AND v.active = true
        AND v.conversionStatus = :conversionStatus
        AND vt.locale = :locale
        AND vt.display = true
        AND vtt.active = TRUE
        AND vtt = :type ";

        $parameters['type'] = $type;
        $parameters['locale'] = $locale;
        $parameters['conversionStatus'] = Video::STATUS_DONE;
        $query = $em->createQuery($dql);

        if (sizeof($parameters)) {
            $query->setParameters($parameters);
        }

        if(!is_null($maxResult)){
            $query->setMaxResults($maxResult);
        }
        return $query->getResult();
    }


    public function getVideoTypeListForShow($locale, $sort, $search = null)
    {
        $em = $this->getEntityManager();
        $dql = "SELECT  vt, vtt
        FROM ZekrBundle:VideoType vt
        LEFT JOIN vt.translations vtt
        WHERE vtt.locale = :locale
        AND vt.active = true 
        
        ";
        $parameters['locale'] = "$locale";

        if (null != $search) {
            $dql .= " AND vtt.name LIKE :search ";
            $parameters['search'] = "%" . $search . "%";
        }

        if ($sort == 'name') {
            $dql .= ' ORDER By  vtt.name ASC' ;
        } elseif ('sum_videos') {
            $dql .= ' ORDER By vt.numActiveTopics DESC ';
        }

        $query = $em->createQuery($dql);
        if (sizeof($parameters)) {
            $query->setParameters($parameters);
        }
        return $query->getResult();
    }


    protected function getEntityManager()
    {
        return $this->em;
    }
}