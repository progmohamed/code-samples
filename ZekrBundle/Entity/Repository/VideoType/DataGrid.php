<?php

namespace ZekrBundle\Entity\Repository\VideoType;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use AdminBundle\Classes\DataGrid as AdminDataGrid;

class DataGrid extends AdminDataGrid
{
    public function getGrid($paginator, $page, $rowsInPage)
    {
        $em = $this->getEntityManager();
        $parameters = [];
        $dql = "SELECT vt
        FROM ZekrBundle:VideoType vt
        LEFT JOIN vt.translations vtt
        WHERE vt.deletedAt is NULL ";

        $elementValue = $this->getFormDataElement('name');
        if($elementValue) {
            $dql .= "AND vtt.name LIKE :name ";
            $parameters['name'] = "%".$elementValue."%";
        }

        $elementValue = $this->getFormDataElement('slug');
        if($elementValue) {
            $dql .= "AND (vt.slug LIKE :slug OR vt.plainSlug LIKE :slug ) ";
            $parameters['slug'] = "%".$elementValue."%";
        }

        $dql .= "ORDER BY vtt.name ";

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