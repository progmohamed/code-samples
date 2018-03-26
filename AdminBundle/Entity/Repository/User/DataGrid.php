<?php

namespace AdminBundle\Entity\Repository\User;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use AdminBundle\Classes\DataGrid as AdminDataGrid;

class DataGrid extends AdminDataGrid
{
    public function getGrid($paginator, $page, $locale)
    {
        $em = $this->getEntityManager();
        $parameters = [];
        $dql = "SELECT t as user, (SELECT ct.name FROM LocaleBundle:Country c LEFT JOIN c.translations ct WHERE t.countryId = c.id AND ct.locale = :locale ) as country
        FROM AdminBundle:User t
        WHERE 1=1 ";
        $parameters['locale'] = $locale;


        $elementValue = $this->getFormDataElement('id');
        if($elementValue) {
            $dql .= "AND t.id IN(:ids) ";
            $parameters['ids'] = $this->commaDelimitedToArray($elementValue);
        }

        $elementValue = $this->getFormDataElement('username');
        if($elementValue) {
            $dql .= "AND t.username LIKE :username ";
            $parameters['username'] = "%".$elementValue."%";
        }

        $elementValue = $this->getFormDataElement('realName');
        if($elementValue) {
            $dql .= "AND t.realName LIKE :realName ";
            $parameters['realName'] = "%".$elementValue."%";
        }

        $elementValue = $this->getFormDataElement('email');
        if($elementValue) {
            $dql .= "AND t.email LIKE :email ";
            $parameters['email'] = "%".$elementValue."%";
        }

        $elementValue = $this->getFormDataElement('country');
        if($elementValue) {
            $dql .= "AND t.countryId IN(:country) ";
            $parameters['country'] = $elementValue;
        }

        $elementValue = $this->getFormDataElement('phone');
        if($elementValue) {
            $dql .= "AND t.phoneNumber LIKE :phone ";
            $parameters['phone'] = "%".$elementValue."%";
        }

        $elementValue = $this->getFormDataElement('sex');
        if($elementValue) {
            $dql .= "AND t.sex IN(:sex) ";
            $parameters['sex'] = $elementValue;
        }

        $elementValue = $this->getFormDataElement('dateFrom');
        if($elementValue) {
            $dateTime = $this->parseDateTime($elementValue);
            if($dateTime) {
                $dql .= "AND t.registrationDateTime >= :dateFrom ";
                $parameters['dateFrom'] = $dateTime;
            }
        }

        $elementValue = $this->getFormDataElement('dateTo');
        if($elementValue) {
            $dateTime = $this->parseDateTime($elementValue);
            if($dateTime) {
                $dql .= "AND t.registrationDateTime <= :dateTo ";
                $parameters['dateTo'] = $dateTime;
            }
        }

        $dql .= " GROUP BY t.id ORDER BY t.username ";
        $query = $em->createQuery($dql);
        if(sizeof($parameters)) {
            $query->setParameters($parameters);
        }

        $pagination = $paginator->paginate(
            $query,
            $page,
            10,
            ['wrap-queries' => true]
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
            ->add('username', TextType::class, [
                'label' => 'admin.titles.username'
            ])
            ->add('realName', TextType::class, [
                'label' => 'admin.titles.real_name'
            ])
            ->add('email', TextType::class, [
                'label' => 'admin.titles.email'
            ])
            ->add('country', EntityType::class, [
                'label' => 'admin.titles.country',
                'class' => 'LocaleBundle:Country',
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('c');
                },
                'multiple' => true,
            ])
            ->add('phone', TextType::class, [
                'label' => 'admin.titles.phone_number'
            ])
            ->add('dateFrom', TextType::class, [
                'label' => 'admin.titles.registration_dateTime',
                 'attr' => ['class' => 'date'],
            ])
            ->add('dateTo', TextType::class, [
                'label' => 'admin.titles.registration_dateTime',
                'attr' => ['class' => 'date'],
            ])
            ->add('sex', ChoiceType::class, [
                'label' => 'admin.titles.gender',
                'choices'  => [
                    'admin.titles.male' => 1,
                    'admin.titles.female' => 2,
                ],
                'multiple' => true,
            ]);
        return $form->getForm();
    }
}