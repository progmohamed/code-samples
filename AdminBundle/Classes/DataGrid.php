<?php

namespace AdminBundle\Classes;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

abstract class DataGrid
{
    protected $em;

    protected $formData;

    function __construct($em)
    {
        $this->em = $em;
    }

    public function getFilterArray($form)
    {
        $out = [];
        foreach($form as $element) {
            $elementName = $element->getName();
            $innerType = $element->getConfig()->getType()->getInnerType();
            if($innerType instanceof EntityType) {
                $out[$elementName] = $this->getFormFilterEntityIds($element->getData());
            }else{
                $out[$elementName] = $element->getData();
            }
        }
        return $out;
    }


    public function filterItemHasData()
    {
        return $this->em;
    }

    public function getEncodedFilterArray($form)
    {
        return $this->encodeFilterArray(
            $this->getFilterArray($form)
        );
    }

    public function encodeFilterArray($filterArray)
    {
        return base64_encode(serialize( $filterArray ));
    }

    public function decodeFilterArray($encodedFilerArray)
    {
        return unserialize(base64_decode( $encodedFilerArray ));
    }

    public function setFormFilterData($form, $formData)
    {
        $this->formData = $formData;
        foreach($formData as $elementName => $value) {
            $element = $form->get($elementName);
            $innerType = $element->getConfig()->getType()->getInnerType();
            if($innerType instanceof EntityType) {
                $dataClass = $element->getConfig()->getOption('class');
                $element->setData( $this->getEntityArrayFromIds($value, $dataClass) );
            }else {
                $element->setData($value);
            }
        }
        return $form;
    }

    private function getFormFilterEntityIds($array)
    {
        $out = [];
        foreach($array as $entity) {
            $out[] = $entity->getId();
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

    public function getFormData()
    {
        return $this->formData;
    }

    public function getFormDataElement($name)
    {
        $formData = $this->getFormData();
        if(is_array($formData)) {
            if(array_key_exists($name, $formData)) {
                $value = $formData[$name];
                if(is_array($value)) {
                    if(sizeof($value)) {
                        return $value;
                    }
                }else{
                    return $value;
                }
            }
        }
    }

    public function parseDateTime($dateTime)
    {
        try {
            if ($dateTime) {
                $dateTime = new \DateTime($dateTime);
                return $dateTime;
            }
        }catch(\Exception $e) {}
    }

    protected function commaDelimitedToArray($elementValue)
    {
        $ids = explode(",", $elementValue);
        $ids = array_map('trim', $ids);
        return $ids;
    }

    protected function getEntityManager()
    {
        return $this->em;
    }
}