<?php

namespace ZekrBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints as Assert;


class CategoryType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder
            ->add('plainSlug', null, array(
                'label' => 'معرف ',
            ));
        foreach($options['languages'] as $language) {
            $builder
                ->add('name_'.$language->getLocale(), TextType::class, array(
                    'mapped' => false,
                    'label' => "الإسم",
                    'constraints' => [
                        new Assert\NotBlank(),
                    ],
                ))
                ->add('note_'.$language->getLocale(), TextareaType::class, array(
                    'mapped' => false,
                    'required' => false,
                    'label' => "الوصف",

                ));
        }
        $builder
            ->add('active', null, array(
                'label' => 'فعال',
                'attr' => array(
                    'class'=>'ace ace-switch ace-switch-5'
                )
            ));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ZekrBundle\Entity\Category',
            'languages' => []
        ));
    }

}
