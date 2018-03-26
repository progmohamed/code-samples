<?php

namespace SearchBundle\Entity\Repository\History;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use AdminBundle\Classes\DataGrid as AdminDataGrid;


class DataGrid extends AdminDataGrid
{
    public function getGrid($paginator, $page)
    {
        $em = $this->getEntityManager();
        $parameters = [];
        $dql = "SELECT h
        FROM SearchBundle:History h
        WHERE 1=1 ";

        $elementValue = $this->getFormDataElement('id');
        if($elementValue) {
            $dql .= "AND h.id IN(:ids) ";
            $parameters['ids'] = $this->commaDelimitedToArray($elementValue);
        }

        $elementValue = $this->getFormDataElement('string');
        if($elementValue) {
            $dql .= "AND h.string LIKE :string ";
            $parameters['string'] = "%".$elementValue."%";
        }


        $query = $em->createQuery($dql);
        if(sizeof($parameters)) {
            $query->setParameters($parameters);
        }

        $pagination = $paginator->paginate(
            $query,
            $page,
            10,
            ['wrap-queries'=>true]
        );
        return $pagination;
    }

    public function getFilterForm($formFactory, $formActionUrl, $data = null, $options = [])
    {
        $form = $formFactory->createBuilder(FormType::class, $data, $options);
        $form
            ->setMethod('POST')
            ->setAction($formActionUrl)
            ->add('id', TextType::class, [
                'label' => 'admin.titles.id'
            ])
            ->add('string', TextType::class, [
                'label' => 'search.history.string'
            ])
            ;
        return $form->getForm();
    }
}