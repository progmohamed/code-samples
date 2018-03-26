<?php

namespace ZekrBundle\Entity\Repository\Video;

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

    public function getVideoList($from, $limit, $locale, $sortOrder = 'v.insertedAt')
    {
        $em = $this->getEntityManager();
        $dql = "SELECT  v, vt
        FROM ZekrBundle:Video v
        INNER JOIN v.translations vt
        WHERE v.deletedAt is NULL
        AND v.tempVideo is NULL
        AND v.tempVideoFile is NULL
        AND v.active = TRUE
        AND v.conversionStatus = :conversionStatus
        AND vt.locale = :locale
        AND vt.display = TRUE 
        ORDER BY $sortOrder DESC 
        ";

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