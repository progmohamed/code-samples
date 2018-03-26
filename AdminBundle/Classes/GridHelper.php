<?php

namespace AdminBundle\Classes;

class GridHelper
{
    public function getFormFilterEntityIds($array)
    {
        $out = [];
        foreach($array as $entity) {
            $out[] = $entity->getId();
        }
        return $out;
    }

    public function getEntityArrayFromIds($ids, $className)
    {
        $em = $this->getEntityManager();
        $out = [];
        foreach ($ids as $id) {
            $entity = $em->getRepository($className)->find($id);
            $out[] = $entity;
        }
        return $out;
    }
}