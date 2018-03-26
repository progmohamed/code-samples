<?php

namespace ZekrBundle\Entity\Repository\ApiProject;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use AdminBundle\Classes\DataGrid as AdminDataGrid;

class DataGrid extends AdminDataGrid
{
    public function getGrid($paginator, $page, $rowsInPage)
    {
        $em = $this->getEntityManager();
        $parameters = array();
        $dql = "SELECT a
        FROM ZekrBundle:ApiProject a
        WHERE 1=1 ";

        $elementValue = $this->getFormDataElement('name');
        if($elementValue) {
            $dql .= "AND a.name LIKE :name ";
            $parameters['name'] = "%".$elementValue."%";
        }


        $elementValue = $this->getFormDataElement('slug');
        if($elementValue) {
            $dql .= "AND (a.slug LIKE :slug OR a.plainSlug LIKE :slug ) ";
            $parameters['slug'] = "%".$elementValue."%";
        }

        $query = $em->createQuery($dql);
        if(sizeof($parameters)) {
            $query->setParameters($parameters);
        }

        $pagination = $paginator->paginate(
            $query,
            $page,
            $rowsInPage,
            array('wrap-queries'=>true)
        );
        return $pagination;
    }

    public function getFilterForm($formFactory, $formActionUrl, $data = null, $options = [])
    {
        $form = $formFactory->createBuilder(FormType::class, $data, $options);
        $form
            ->setMethod('POST')
            ->setAction($formActionUrl)
            ->add('name', TextType::class, array(
                'label' => 'الإسم'
            )) ->add('slug', TextType::class, array(
                'label' => 'المعرف'
            ))
            ;
        return $form->getForm();
    }
}