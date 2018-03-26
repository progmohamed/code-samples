<?php

namespace ConfigBundle\Entity\Repository\ConfigVariable;

use Doctrine\ORM\EntityRepository;

class Repository extends EntityRepository
{
    public function getVariableValue($variable, $default)
    {
        $em = $this->getEntityManager();
        $dql = "SELECT c.value
        FROM ConfigBundle:ConfigVariable c
        WHERE c.variable = :variable
        ";

        $parameters['variable'] = $variable;
        $query = $em->createQuery($dql);

        if (sizeof($parameters)) {
            $query->setParameters($parameters);
        }

        if(null !=  $query->getOneOrNullResult()){
            return  $query->getSingleScalarResult();
        }else{
            return $default;
        }
    }
}