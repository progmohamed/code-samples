<?php

namespace LocaleBundle\Entity\Repository\Dialect;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use AdminBundle\Classes\DataGrid as AdminDataGrid;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;


class DataGrid extends AdminDataGrid
{
    public function getGrid($paginator, $page)
    {
        $em = $this->getEntityManager();
        $parameters = [];
        $dql = "SELECT d
        FROM LocaleBundle:Dialect d
        LEFT JOIN d.translations dt
        INNER JOIN d.language dl
        WHERE 1=1 ";

        $elementValue = $this->getFormDataElement('id');
        if($elementValue) {
            $dql .= "AND d.id IN(:ids) ";
            $parameters['ids'] = $this->commaDelimitedToArray($elementValue);
        }

        $elementValue = $this->getFormDataElement('name');
        if($elementValue) {
            $dql .= "AND dt.name LIKE :name ";
            $parameters['name'] = "%".$elementValue."%";
        }

        $elementValue = $this->getFormDataElement('language');
        if ($elementValue) {
            $dql .= "AND dl.id IN(:language) ";
            $parameters['language'] = $elementValue;
        }

        $dql .= "ORDER BY dt.name ";

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
            ->add('name', TextType::class, [
                'label' => 'admin.titles.name'
            ])
            ->add('language', EntityType::class, [
                'label' => 'admin.titles.lang',
                'class' => 'LocaleBundle:Language',
                'multiple' => true,
            ]);
        return $form->getForm();
    }
}