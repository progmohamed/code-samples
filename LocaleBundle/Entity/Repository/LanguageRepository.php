<?php

namespace LocaleBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use LocaleBundle\Service\LocaleService\LocaleService;

class LanguageRepository extends EntityRepository
{
    public function getGrid()
    {
        $em = $this->getEntityManager();

        $dql = "SELECT t AS data
        FROM LocaleBundle:Language t
        GROUP BY t.id
        ORDER BY t.name ";

        $query = $em->createQuery($dql);
        return $query->getResult();
    }

    public function getLanguageIdByLocale($locale)
    {
        $entity = $this->findOneByLocale($locale);
        if($entity) {
            return $entity->getId();
        }
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
                        $count += $relatedService->getLocale()->getLanguageRestrictions($entity->getId());
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
