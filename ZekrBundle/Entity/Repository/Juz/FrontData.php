<?php

namespace ZekrBundle\Entity\Repository\Juz;

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
        $dql = "SELECT v, vt, j, jt
        FROM ZekrBundle:Video v
        INNER JOIN v.translations vt
        INNER JOIN v.juz j
        INNER JOIN j.translations jt
        WHERE v.deletedAt is NULL
        AND v.tempVideo is NULL
        AND v.tempVideoFile is NULL
        AND v.conversionStatus = :conversionStatus
        AND v.active = TRUE
        AND vt.locale = :locale
        AND jt.locale = :locale
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


    public function getJuzListForShow($locale, $sort, $search = null)
    {
        $em = $this->getEntityManager();
        $dql = "SELECT  j, jt
        FROM ZekrBundle:Juz j
        LEFT JOIN j.translations jt
        WHERE jt.locale = :locale 
        
        ";
        $parameters['locale'] = "$locale";

        if (null != $search) {
            $dql .= " AND jt.name LIKE :search ";
            $parameters['search'] = "%" . $search . "%";
        }

        if ($sort == 'occurrence') {
            $dql .= ' ORDER BY j.sortOrder ASC ';
        }elseif ($sort == 'name') {
            $dql .= ' ORDER By  jt.name ASC' ;
        } elseif ('sum_videos') {
            $dql .= ' ORDER By j.numActiveTopics DESC ';
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