<?php

namespace ZekrBundle\Entity\Repository\Juz;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use AdminBundle\Classes\DataGrid as AdminDataGrid;

class DataGrid extends AdminDataGrid
{
    public function getGrid()
    {
        $em = $this->getEntityManager();
        $parameters = array();
        $dql = "SELECT j
        FROM ZekrBundle:Juz j
        LEFT JOIN j.translations jt
        WHERE 1=1 ";

        $elementValue = $this->getFormDataElement('name');
        if($elementValue) {
            $dql .= "AND jt.name LIKE :name ";
            $parameters['name'] = "%".$elementValue."%";
        }

        $elementValue = $this->getFormDataElement('sortOrder');
        if($elementValue) {
            $dql .= "AND j.sortOrder LIKE :sortOrder ";
            $parameters['sortOrder'] = $elementValue;
        }

        $elementValue = $this->getFormDataElement('slug');
        if($elementValue) {
            $dql .= "AND (j.slug LIKE :slug OR j.plainSlug LIKE :slug ) ";
            $parameters['slug'] = "%".$elementValue."%";
        }

        $dql .= "ORDER BY j.sortOrder ";
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
                'label' => 'رقم الجزء'
            )) ->add('slug', TextType::class, array(
                'label' => 'المعرف'
            ))
            ;
        return $form->getForm();
    }
}