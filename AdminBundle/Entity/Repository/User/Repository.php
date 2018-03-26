<?php

namespace AdminBundle\Entity\Repository\User;

use AdminBundle\Entity\User;
use AdminBundle\Service\AdminService\AdminService;
use Doctrine\ORM\EntityRepository;

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

    public function getUsers()
    {
        $em = $this->getEntityManager();
        $dql = "SELECT u
        FROM AdminBundle:User u
        ";
        $query = $em->createQuery($dql);
        return $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    public function getUserById($id)
    {
        $em = $this->getEntityManager();
        $dql = "SELECT u
        FROM AdminBundle:User u
        WHERE u.id = :id
        ";
        $query = $em->createQuery($dql);
        $query->setParameter('id',$id);
        return $query->getOneOrNullResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    public function getDeleteRestrectionsByIds(AdminService $service, $ids)
    {
        $restrictions = [];
        $delets = [];
        if(is_array($ids)) {
            foreach($ids as $id) {
                $entity = $this->find($id);
                if($entity) {
                    $count = 0;
                    foreach($service->getRelatedServices() as $relatedService) {
                        $count += $relatedService->getAdmin()->getUserRestrictions($entity);
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


    public function getUserCountryCount($country)
    {
        $em = $this->getEntityManager();
        $dql = "SELECT COUNT(u.id) FROM AdminBundle:User u WHERE u.countryId = :country ";
        $query = $em->createQuery($dql);
        $query->setParameter('country', $country);
        return $query->getSingleScalarResult();
    }

}