<?php

namespace ZekrBundle\Entity\Repository\Rewaya;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use AdminBundle\Classes\DataGrid as AdminDataGrid;

class DataGrid extends AdminDataGrid
{
    public function getGrid($paginator, $page, $rowsInPage)
    {
        $em = $this->getEntityManager();
        $parameters = array();
        $dql = "SELECT r
        FROM ZekrBundle:Rewaya r
        LEFT JOIN r.translations rt
        WHERE r.deletedAt is NULL ";

        $elementValue = $this->getFormDataElement('name');
        if($elementValue) {
            $dql .= "AND pt.name LIKE :name ";
            $parameters['name'] = "%".$elementValue."%";
        }

        $elementValue = $this->getFormDataElement('slug');
        if($elementValue) {
            $dql .= "AND (r.slug LIKE :slug OR r.plainSlug LIKE :slug ) ";
            $parameters['slug'] = "%".$elementValue."%";
        }

        $dql .= "ORDER BY r.sortOrder ";

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
            ))
            ->add('slug', TextType::class, array(
                'label' => 'المعرف'
            ))
            ;
        return $form->getForm();
    }
}