<?php

namespace AdminBundle\Classes;

use Doctrine\ORM\EntityRepository;

class AdminEntityRepository extends EntityRepository
{

    public function getFilterArray($formBuilder, $form)
    {
        $out = [];
        foreach($formBuilder as $element) {
            if(in_array($element->getType()->getName(), ['text'])) {
                $out[$element->getName()] = $form->get($element->getName())->getData();
            }elseif($element->getType()->getName() == 'entity') {
                $out[$element->getName()] = $this->getFormFilterEntityIds($form->get($element->getName())->getData());
            }
        }
        return $out;
    }

    public function setFormFilterData($formBuilder, $form, $formData)
    {
        foreach($formData as $key => $formDataElement) {
            $formElement = $formBuilder->get($key);
            if(in_array($formElement->getType()->getName(), ['text'])) {
                $form->get($key)->setData($formData[$key]);
            }elseif($formElement->getType()->getName() == 'entity') {
                $className = $formElement->getAttribute('data_collector/passed_options')['class'];
                $form->get($key)->setData( $this->getEntityArrayFromIds($formData[$key], $className) );
            }
        }
        return $form;
    }

    private function getFormFilterEntityIds($array)
    {
        $out = [];
        if(is_array($array)) {
            foreach ($array as $entity) {
                $out[] = $entity->getId();
            }
        }
        return $out;
    }

    private function getEntityArrayFromIds($ids, $className)
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