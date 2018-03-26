<?php

namespace ZekrBundle\Entity\Repository\VideoReport;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use AdminBundle\Classes\DataGrid as AdminDataGrid;

class DataGrid extends AdminDataGrid
{
    public function getGrid($paginator, $page)
    {
        $em = $this->getEntityManager();
        $parameters = [];
        $dql = "SELECT vr
        FROM ZekrBundle:VideoReport vr
        WHERE 1=1 ";

//        $elementValue = $this->getFormDataElement('name');
//        if($elementValue) {
//            $dql .= "AND pt.name LIKE :name ";
//            $parameters['name'] = "%".$elementValue."%";
//        }
//
//        $elementValue = $this->getFormDataElement('slug');
//        if($elementValue) {
//            $dql .= "AND (p.slug LIKE :slug OR p.plainSlug LIKE :slug ) ";
//            $parameters['slug'] = "%".$elementValue."%";
//        }
//
//        $dql .= "ORDER BY pt.name ";


        $query = $em->createQuery($dql);
        if(sizeof($parameters)) {
            $query->setParameters($parameters);
        }

        $pagination = $paginator->paginate(
            $query,
            $page,
            10
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