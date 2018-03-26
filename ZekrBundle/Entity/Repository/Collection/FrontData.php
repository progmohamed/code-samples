<?php

namespace ZekrBundle\Entity\Repository\Collection;

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


    public function getRandomCollection($locale)
    {
        $em = $this->getEntityManager();
        $dql = "SELECT c, ct
        FROM ZekrBundle:Collection c
        INNER JOIN c.translations ct
        WHERE c.deletedAt is NULL
        AND c.active = TRUE
        AND ct.locale = :locale
        AND ct.display = TRUE
        ORDER BY RAND()
        ";

        $parameters['locale'] = $locale;
        $query = $em->createQuery($dql)
            ->setMaxResults(1);

        if (sizeof($parameters)) {
            $query->setParameters($parameters);
        }

        $collection = $query->getOneOrNullResult();
        $videos = $this->getListVideosForCollection($collection, $locale, 4);
        $out['collection'] = $collection;
        $out['videos'] = $videos;
        return $out;
    }


    public function getCollectionList($locale, $paginator, $page)
    {
        $em = $this->getEntityManager();
        $dql = "SELECT  c, ct
        FROM ZekrBundle:Collection c
        INNER JOIN c.translations ct
        WHERE c.deletedAt is NULL
        AND c.active = TRUE
        AND ct.locale = :locale
        AND ct.display = TRUE 
        ORDER BY c.sortOrder 
        ";
        $query = $em->createQuery($dql);
        $query->setParameter('locale', $locale);

        $pagination = $paginator->paginate(
            $query,
            $page,
            15,
            array('wrap-queries' => true)
        );

        return $pagination;

    }

    public function getListVideosForCollection($collection, $locale, $maxResult = null)
    {
        $em = $this->getEntityManager();
        $dql = "SELECT v, vt
        FROM ZekrBundle:Video v
        INNER JOIN v.translations vt
        INNER JOIN ZekrBundle:VideoCollection vc WITH v = vc.video
        INNER JOIN vc.collection c
        INNER JOIN c.translations ct
        WHERE v.tempVideo is null
        AND v.tempVideoFile is null
        AND v.conversionStatus = :conversionStatus
        AND v.active = true
        AND vt.locale = :locale
        AND vt.display = true
        AND c.deletedAt is NULL
        AND c.active = TRUE
        AND ct.locale = :locale
        AND ct.display = TRUE
        AND c = :collection
        ORDER BY vc.sortOrder ";

        $parameters['collection'] = $collection;
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

    public function getCollection($slug, $locale)
    {
        $em = $this->getEntityManager();
        $dql = "SELECT  c, ct
        FROM ZekrBundle:Collection c
        INNER JOIN c.translations ct
        WHERE c.deletedAt is NULL
        AND c.active = TRUE
        AND c.slug = :slug
        AND ct.locale = :locale
        AND ct.display = TRUE 
        ORDER BY c.sortOrder 
        ";
        $parameters['slug'] = $slug;
        $parameters['locale'] = $locale;
        $query = $em->createQuery($dql);

        if (sizeof($parameters)) {
            $query->setParameters($parameters);
        }
        return $query->getOneOrNullResult();

    }

    protected function getEntityManager()
    {
        return $this->em;
    }
}