<?php

namespace ZekrBundle\Entity\Repository\Collection;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use AdminBundle\Classes\DataGrid as AdminDataGrid;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class DataGrid extends AdminDataGrid
{
    public function getGrid()
    {
        $em = $this->getEntityManager();
        $parameters = [];
        $dql = "SELECT c
        FROM ZekrBundle:Collection c
        LEFT JOIN c.translations ct
        LEFT JOIN c.apiProject ca
        WHERE c.deletedAt IS NULL ";

        $elementValue = $this->getFormDataElement('name');
        if($elementValue) {
            $dql .= "AND ct.name LIKE :name ";
            $parameters['name'] = "%".$elementValue."%";
        }
        $elementValue = $this->getFormDataElement('api_project');
        if ($elementValue) {
            $dql .= "AND ca.id IN(:api_project) ";
            $parameters['api_project'] = $elementValue;
        }
        $elementValue = $this->getFormDataElement('slug');
        if($elementValue) {
            $dql .= "AND (c.slug LIKE :slug OR c.plainSlug LIKE :slug ) ";
            $parameters['slug'] = "%".$elementValue."%";
        }

        $dql .= "ORDER BY c.sortOrder ";

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
            ))
            ->add('slug', TextType::class, array(
                'label' => 'المعرف'
            ))
            ->add('api_project', EntityType::class, array(
                'label' => 'مضمنة في مشاريع API',
                'class' => 'ZekrBundle:ApiProject',
                'multiple' => true,
            ))
            ;
        return $form->getForm();
    }
}