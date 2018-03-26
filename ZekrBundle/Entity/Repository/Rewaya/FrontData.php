<?php

namespace ZekrBundle\Entity\Repository\Rewaya;

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
        $dql = "SELECT v, vt, r, rt
        FROM ZekrBundle:Video v
        INNER JOIN v.translations vt
        INNER JOIN v.rewaya r
        INNER JOIN r.translations rt
        WHERE v.deletedAt is NULL
        AND v.tempVideo is NULL
        AND v.tempVideoFile is NULL
        AND v.conversionStatus = :conversionStatus
        AND v.active = TRUE
        AND vt.locale = :locale
        AND rt.locale = :locale
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

    public function getRewayaListForShow($locale, $sort, $search = null)
    {
        $em = $this->getEntityManager();
        $dql = "SELECT  r, rt
        FROM ZekrBundle:Rewaya r
        LEFT JOIN r.translations rt
        WHERE rt.locale = :locale
        AND r.active = true 
        
        ";
        $parameters['locale'] = $locale;

        if (null != $search) {
            $dql .= " AND rt.name LIKE :search ";
            $parameters['search'] = "%" . $search . "%";
        }

        if ($sort == 'occurrence') {
            $dql .= ' ORDER BY r.sortOrder ASC ';
        }elseif ($sort == 'name') {
            $dql .= ' ORDER By  rt.name ASC' ;
        } elseif ('sum_videos') {
            $dql .= ' ORDER By r.numActiveTopics DESC ';
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