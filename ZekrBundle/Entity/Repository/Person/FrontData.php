<?php

namespace ZekrBundle\Entity\Repository\Person;

use Doctrine\ORM\EntityRepository;

class FrontData
{
    protected $em;

    protected $formData;

    function __construct($em)
    {
        $this->em = $em;
    }

    public function getTopPersons($from, $limit, $locale)
    {
        $em = $this->getEntityManager();
        $dql = "SELECT p as person, pt, (SELECT sum(v.views) FROM ZekrBundle:Video v INNER JOIN v.person vp WHERE vp = p) as views
        FROM ZekrBundle:person p
        INNER JOIN p.translations pt
        WHERE p.active = TRUE
        AND pt.locale = :locale
        ORDER BY views DESC ";

        $parameters['locale'] = $locale;
        $query = $em->createQuery($dql)
            ->setFirstResult($from)
            ->setMaxResults($limit);

        if (sizeof($parameters)) {
            $query->setParameters($parameters);
        }

        return $query->getResult();
    }

    public function getPersonList($locale, $paginator, $page, $sort, $search = null)
    {
        $em = $this->getEntityManager();
        $dql = "SELECT  p as person, pt, (SELECT sum(v.views) FROM ZekrBundle:Video v INNER JOIN v.person vp WHERE vp = p) as views
        FROM ZekrBundle:Person p
        LEFT JOIN p.translations pt
        WHERE pt.locale = :locale
        AND p.active = true 
        
        ";

        $parameters['locale'] = $locale;

        if (null != $search) {
            $dql .= " AND pt.name LIKE :search ";
            $parameters['search'] = "%" . $search . "%";
        }

        if ($sort == 'name') {
            $dql .= ' ORDER By  pt.name ASC';
        } elseif ('sum_videos') {
            $dql .= ' ORDER By p.numActiveTopics DESC';
        } elseif ('views') {
            $dql .= ' ORDER By views DESC';
        }

        $query = $em->createQuery($dql);

        if (sizeof($parameters)) {
            $query->setParameters($parameters);
        }

        $pagination = $paginator->paginate(
            $query,
            $page,
            6,
            array('wrap-queries' => true)
        );

        return $pagination;
    }


    protected function getEntityManager()
    {
        return $this->em;
    }
}