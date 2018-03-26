<?php

namespace ZekrBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints as Assert;

class ApiProjectType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('plainSlug', null, array(
                'label' => 'معرف',
            ))
            ->add('name', null, array(
                'label' => 'الإسم',
            ))
            ->add('active', null, array(
                'label' => 'فعال',
                'attr' => array(
                    'class' => 'ace ace-switch ace-switch-5'
                )
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ZekrBundle\Entity\ApiProject',
        ));
    }

}
