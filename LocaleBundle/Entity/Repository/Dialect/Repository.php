<?php

namespace LocaleBundle\Entity\Repository\Dialect;

use Doctrine\ORM\EntityRepository;
use LocaleBundle\Service\LocaleService\LocaleService;

class Repository extends EntityRepository
{

    private static $dataGrid;

    public function getDataGrid()
    {
        if(!self::$dataGrid) {
            self::$dataGrid = new DataGrid($this->getEntityManager());
        }
        return self::$dataGrid;
    }

    public function getDeleteRestrectionsByIds(LocaleService $service, $ids)
    {
        $restrictions = [];
        $delets = [];
        if(is_array($ids)) {
            foreach($ids as $id) {
                $entity = $this->find($id);
                if($entity) {
                    $totalCount = 0;
                    foreach($service->getRelatedServices() as $relatedService) {
                        $count = $relatedService->getLocale()->getDialectRestrictions($entity);
                        if($count) {
                            $restrictions[] = [
                                'entity'=> $entity,
                                'serviceName'=> $relatedService->getName(),
                                'count' => $count
                            ];
                        }
                        $totalCount += $count;
                    }
                    if(0 == $totalCount) {
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


    public function getDialectArray($languageId, $locale)
    {
        $em = $this->getEntityManager();

        $dql = "SELECT d, dt
        FROM LocaleBundle:Dialect d
        LEFT JOIN d.translations dt
        INNER JOIN d.language dl
        WHERE dt.locale = :locale 
        AND dl.id = :languageId ";

        $parameters['locale'] = $locale;
        $parameters['languageId'] = $languageId;
        $query = $em->createQuery($dql);
        if (sizeof($parameters)) {
            $query->setParameters($parameters);
        }
        return $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

}