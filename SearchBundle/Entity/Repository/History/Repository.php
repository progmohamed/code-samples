<?php

namespace SearchBundle\Entity\Repository\History;

use Doctrine\ORM\EntityRepository;
use SearchBundle\Service\SearchService\HistoryService;


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

    public function getDeleteRestrectionsByIds(HistoryService $service, $ids)
    {
        $restrictions = [];
        $delets = [];
        if(is_array($ids)) {
            foreach($ids as $id) {
                $entity = $this->find($id);
                if($entity) {
                    $totalCount = 0;
                    foreach($service->getRelatedServices() as $relatedService) {
                        $count = $relatedService->getSearch()->getSearchHistoryRestrictions($entity->getId());
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

}