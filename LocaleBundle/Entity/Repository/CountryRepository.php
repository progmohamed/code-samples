<?php

namespace LocaleBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use LocaleBundle\Entity\Country;
use LocaleBundle\Service\LocaleService\LocaleService;

class CountryRepository extends EntityRepository
{

    public function getGrid()
    {
        $em = $this->getEntityManager();
        $dql = "SELECT c as data
        FROM LocaleBundle:Country c
        LEFT JOIN c.translations ct
        ORDER BY ct.name ";

        $query = $em->createQuery($dql);
        return $query->getResult();
    }


    public function getCountryArray($locale)
    {
        $em = $this->getEntityManager();

        $dql = "SELECT c, ct
        FROM LocaleBundle:Country c
        INNER JOIN c.translations ct
        WHERE ct.locale = :locale ";

        $query = $em->createQuery($dql);
        $query->setParameter('locale', $locale);
        return $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    public function getCountryArrayById($id, $locale)
    {
        $em = $this->getEntityManager();

        $dql = "SELECT c, ct
        FROM LocaleBundle:Country c
        INNER JOIN c.translations ct
        WHERE ct.locale = :locale 
        AND c.id = :id";
        $parameters['locale'] = $locale;
        $parameters['id'] = $id;

        $query = $em->createQuery($dql);
        if (sizeof($parameters)) {
            $query->setParameters($parameters);
        }
        return $query->getOneOrNullResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    public function getDeleteRestrectionsByIds(LocaleService $service, $ids)
    {
        $restrictions = [];
        $delets = [];
        if(is_array($ids)) {
            foreach($ids as $id) {
                $entity = $this->find($id);
                if($entity) {
                    $count = 0;
                    foreach($service->getRelatedServices() as $relatedService) {
                        $count += $relatedService->getLocale()->getCountryRestrictions($entity);
                    }
                    if($count) {
                        $restrictions[] = [
                            'entity'=> $entity,
                            'serviceName'=> $relatedService->getName(),
                            'count' => $count
                        ];
                    }else{
                        $delets[] = $entity;
                    }
                }
            }
        }
        return [
            'restrictions' => $restrictions,
            'delets' => $delets,
        ];
    }



}
