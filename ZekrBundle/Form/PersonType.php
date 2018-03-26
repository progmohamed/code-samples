<?php

namespace ZekrBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints as Assert;

class PersonType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $photoConstraints = [];
        if(!$options['edit']) {
            $photoConstraints[] = new Assert\NotBlank();
        }
        $builder
            ->add('active', null, array(
                'label' => 'فعال',
                'attr' => array(
                    'class' => 'ace ace-switch ace-switch-5'
                )
            ))
            ->add('plainSlug', null, array(
                'label' => 'معرف ',
            ))
            ->add('photoFile', null, [
                'label' => 'الصورة',
                'required' => true,
                'constraints' => $photoConstraints
            ]);

        foreach($options['languages'] as $language) {
            $builder
                ->add('name_'.$language->getLocale(), TextType::class, array(
                    'mapped' => false,
                    'label' => "الإسم",
                    'constraints' => [
                        new Assert\NotBlank(),
                    ],
                ))
                ->add('resume_'.$language->getLocale(), TextareaType::class, array(
                    'mapped' => false,
                    'label' => "تفاصيل"
                ));
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ZekrBundle\Entity\Person',
            'languages' => [],
            'edit' => false
        ));
    }

}
