<?php

namespace ZekrBundle\Entity\Repository\ContactUs;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AdminBundle\Classes\DataGrid as AdminDataGrid;

class DataGrid extends AdminDataGrid
{
    public function getGrid($paginator, $page, $rowsInPage)
    {
        $em = $this->getEntityManager();
        $parameters = [];
        $dql = "SELECT c
        FROM ZekrBundle:ContactUs c
        WHERE 1=1 ";

        $elementValue = $this->getFormDataElement('title');
        if($elementValue) {
            $dql .= "AND c.title LIKE :title ";
            $parameters['title'] = "%".$elementValue."%";
        }
        $elementValue = $this->getFormDataElement('name');
        if($elementValue) {
            $dql .= "AND c.name LIKE :name ";
            $parameters['name'] = "%".$elementValue."%";
        }
        $elementValue = $this->getFormDataElement('email');
        if($elementValue) {
            $dql .= "AND c.email LIKE :email ";
            $parameters['email'] = "%".$elementValue."%";
        }

        $elementValue = $this->getFormDataElement('locale');
        if ($elementValue) {
            $dql .= "AND c.locale IN(:locale) ";
            $parameters['locale'] = $elementValue;
        }

        $dql .= "ORDER BY c.name ";

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
            ->add('title', TextType::class, array(
                'label' => 'عنوان الرسالة'
            ))
            ->add('email', TextType::class, array(
                'label' => 'البريد الإلكتروني'
            ))
            ->add('locale', EntityType::class, array(
                'label' => 'اللغة',
                'class' => 'AdminBundle:Language',
                'choice_value'=>'locale',
                'multiple' => true,
            ));
            ;
        return $form->getForm();
    }
}