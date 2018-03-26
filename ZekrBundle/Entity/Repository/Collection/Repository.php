<?php

namespace ZekrBundle\Entity\Repository\Collection;

use Doctrine\ORM\EntityRepository;
use ZekrBundle\Entity\Collection;

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

    public function getMaxOrder()
    {
        $em = $this->getEntityManager();
        $dql = "SELECT MAX(c.sortOrder)
        FROM ZekrBundle:Collection c";
        $query = $em->createQuery($dql);
        $max = $query->getSingleScalarResult();
        return $max;
    }

    public function resort(array $newSort)
    {
        $em = $this->getEntityManager();

        foreach ($newSort as $index => $collection) {
            $rb = $em->getRepository('ZekrBundle:Collection')->findOneBy(array('id' => $collection));
            $rb->setSortorder($index + 1);
            try {
                $em->flush();
            } catch (\Exception $e) {

            }
        }

    }

    public function resortVideo(array $newSort)
    {
        $em = $this->getEntityManager();

        foreach ($newSort as $index => $video) {
            $rb = $em->getRepository('ZekrBundle:VideoCollection')->findOneBy(array('id' => $video));
            $rb->setSortorder($index + 1);
        }
        try {
            $em->flush();
        } catch (\Exception $e) {

        }
    }


    public function getVideoForCollection($collection)
    {
        $em = $this->getEntityManager();
        $dql = "SELECT vc
        FROM ZekrBundle:Video v
        INNER JOIN ZekrBundle:VideoCollection vc WITH v = vc.video
        WHERE vc.collection = :collection
        ORDER BY vc.sortOrder";
        $query = $em->createQuery($dql);
        $query->setParameter('collection', $collection);
        return $query->getResult();

    }

    public function setThumbForCollection(Collection $collection)
    {
        $em = $this->getEntityManager();
        $dql = "SELECT v.thumbnailFile
        FROM ZekrBundle:Video v
        INNER JOIN ZekrBundle:VideoCollection vc WITH v = vc.video
        WHERE vc.collection = :collection
        AND v.active = TRUE
        AND v.deletedAt is NULL
        AND v.tempVideo is NULL
        AND v.tempVideoFile is NULL
        ";
        $query = $em->createQuery($dql);
        $query->setParameter('collection', $collection);
        $query->setMaxResults(1);
        $thumb = $query->getOneOrNullResult();


        $collection->setThumbnailFile($thumb['thumbnailFile']);
        $em->flush();
    }


    public function getCollections($paginator, $page, $limit, $projectSlug)
    {
        $em = $this->getEntityManager();
        $parameters['project_key'] = $projectSlug;
        $dql = "SELECT c, ct
        FROM ZekrBundle:Collection c
        LEFT JOIN c.translations ct
        INNER JOIN c.apiProject a
        WHERE c.deletedAt is NULL 
        AND a.active = TRUE
        AND a.slug = :project_key 
        AND c.active = 1 ";


        if (!empty($locale)) {
            $dql .= " AND ct.locale = :locale
            AND ct.display = TRUE ";
            $parameters['locale'] = $locale;
        }

        $dql .= "ORDER BY c.sortOrder ";
        $query = $em->createQuery($dql);

        if (!empty($parameters) && sizeof($parameters)) {
            $query->setParameters($parameters);
        }

        return $paginator->paginate(
            $query,
            $page,
            $limit
        );
    }


    public function getSelectedCollections($paginator, $page, $limit, $projectSlug)
    {
        $em = $this->getEntityManager();
        $parameters['project_key'] = $projectSlug;
        $dql = "SELECT c, ct
        FROM ZekrBundle:Collection c
        LEFT JOIN c.translations ct
        INNER JOIN c.apiProject a
        WHERE c.deletedAt is NULL
        AND c.selected = true
        AND a.active = true
        AND a.slug = :project_key 
        AND c.active = 1 ";


        if (!empty($locale)) {
            $dql .= " AND ct.locale = :locale
            AND ct.display = TRUE ";
            $parameters['locale'] = $locale;
        }

        $dql .= "ORDER BY c.sortOrder ";
        $query = $em->createQuery($dql);

        if (!empty($parameters) && sizeof($parameters)) {
            $query->setParameters($parameters);
        }

        return $paginator->paginate(
            $query,
            $page,
            $limit
        );
    }
}