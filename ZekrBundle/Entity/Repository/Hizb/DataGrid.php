<?php

namespace ZekrBundle\Entity\Repository\Hizb;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use AdminBundle\Classes\DataGrid as AdminDataGrid;

class DataGrid extends AdminDataGrid
{
    public function getGrid()
    {
        $em = $this->getEntityManager();
        $parameters = array();
        $dql = "SELECT h
        FROM ZekrBundle:Hizb h
        LEFT JOIN h.translations ht
        WHERE 1=1 ";

        $elementValue = $this->getFormDataElement('name');
        if($elementValue) {
            $dql .= "AND ht.name LIKE :name ";
            $parameters['name'] = "%".$elementValue."%";
        }

        $elementValue = $this->getFormDataElement('sortOrder');
        if($elementValue) {
            $dql .= "AND h.sortOrder LIKE :sortOrder ";
            $parameters['sortOrder'] = $elementValue;
        }

        $elementValue = $this->getFormDataElement('slug');
        if($elementValue) {
            $dql .= "AND (h.slug LIKE :slug OR h.plainSlug LIKE :slug ) ";
            $parameters['slug'] = "%".$elementValue."%";
        }

        $dql .= "ORDER BY h.sortOrder ";
        $query = $em->createQuery($dql);
        if(sizeof($parameters)) {
            $query->setParameters($parameters);
        }

        return $query->getResult();
    }

    public function getFilterForm($formFactory, $formActionUrl, $data = null, $options = [])
    {
        $form = $formFactory->createBuilder(FormType::class, $data, $options);
        $form
            ->setMethod('POST')
            ->setAction($formActionUrl)
            ->add('name', TextType::class, array(
                'label' => 'الإسم'
            )) ->add('sortOrder', TextType::class, array(
                'label' => 'رقم السورة'
            )) ->add('slug', TextType::class, array(
                'label' => 'المعرف'
            ))
            ;
        return $form->getForm();
    }
}