<?php

namespace ZekrBundle\Entity\Repository\SelectedCollection;

use Doctrine\ORM\EntityRepository;

class FrontData
{
    protected $em;

    protected $formData;

    function __construct($em)
    {
        $this->em = $em;
    }


    public function getSelectedCollection($from, $limit, $locale)
    {
        $em = $this->getEntityManager();
        $dql = "SELECT s, c, ct
        FROM ZekrBundle:SelectedCollection s
        INNER JOIN s.collection c
        INNER JOIN c.translations ct
        WHERE c.deletedAt is NULL
        AND c.active = TRUE
        AND ct.locale = :locale
        AND ct.display = TRUE 
        ORDER BY c.sortOrder";

        $parameters['locale'] = $locale;
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