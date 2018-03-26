<?php

namespace ZekrBundle\Entity\Repository\SelectedVideo;

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


    public function getSelectedVideo($from, $limit, $locale)
    {
        $em = $this->getEntityManager();
        $dql = "SELECT s, v, vt
        FROM ZekrBundle:SelectedVideo s
        INNER JOIN s.video v
        INNER JOIN v.translations vt
        WHERE v.deletedAt is NULL
        AND v.tempVideo is NULL
        AND v.tempVideoFile is NULL
        AND v.conversionStatus = :conversionStatus
        AND v.active = TRUE
        AND vt.locale = :locale
        AND vt.display = TRUE 
        ORDER BY s.sortOrder";

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


    protected function getEntityManager()
    {
        return $this->em;
    }
}