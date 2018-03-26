<?php

namespace ZekrBundle\Entity\Repository\StaticPage;

use Doctrine\ORM\EntityRepository;

class FrontData
{
    protected $em;

    protected $formData;

    function __construct($em)
    {
        $this->em = $em;
    }

    public function getStaticPage($locale, $slug)
    {
        $em = $this->getEntityManager();
        $dql = "SELECT s
        FROM ZekrBundle:StaticPage s
        INNER JOIN s.translations st
        WHERE st.locale = :locale 
        AND s.slug = :slug";

        $parameters['locale'] = $locale;
        $parameters['slug'] = $slug;

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