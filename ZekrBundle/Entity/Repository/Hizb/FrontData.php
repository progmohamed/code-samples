<?php

namespace ZekrBundle\Entity\Repository\Hizb;

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


    public function getTopVideos($from, $limit, $locale)
    {
        $em = $this->getEntityManager();
        $dql = "SELECT v, vt, h, ht
        FROM ZekrBundle:Video v
        INNER JOIN v.translations vt
        INNER JOIN v.hizb h
        INNER JOIN h.translations ht
        WHERE v.deletedAt is NULL
        AND v.tempVideo is NULL
        AND v.tempVideoFile is NULL
        AND v.conversionStatus = :conversionStatus
        AND v.active = TRUE
        AND vt.locale = :locale
        AND ht.locale = :locale
        AND vt.display = TRUE 
        GROUP BY v
        ORDER BY v.views DESC";

        $parameters['locale'] = $locale;
        $parameters['conversionStatus'] = Video::STATUS_DONE;
        $query = $em->createQuery($dql)
            ->setFirstResult($from)
            ->setMaxResults($limit);

        if (sizeof($parameters)) {
            $query->setParameters($parameters);
        }

        return $query->getResult();
    }

    public function getHizbListForShow($locale, $sort, $search = null)
    {
        $em = $this->getEntityManager();
        $dql = "SELECT  h, ht
        FROM ZekrBundle:Hizb h
        LEFT JOIN h.translations ht
        WHERE ht.locale = :locale 
        ";
        $parameters['locale'] = "$locale";

        if (null != $search) {
            $dql .= " AND ht.name LIKE :search ";
            $parameters['search'] = "%" . $search . "%";
        }

        if ($sort == 'occurrence') {
            $dql .= ' ORDER BY h.sortOrder ASC ';
        }elseif ($sort == 'name') {
            $dql .= ' ORDER By  ht.name ASC' ;
        } elseif ('sum_videos') {
            $dql .= ' ORDER By h.numActiveTopics DESC ';
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