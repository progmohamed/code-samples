<?php

namespace ZekrBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints as Assert;

class SurahType extends AbstractType
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
            ->add('ayatSum', TextType::class, array(
                'label' => 'عدد الآيات',
            ))
            ->add('firstJuz', null, array(
                'label' => 'تبدأ في الجزء',
                'attr' => array(
                    'class' => 'width-50 chosen-select',
                    'data-placeholder' => 'اختر'
                )
            ))
            ->add('firstHizb', null, array(
                'label' => 'تبدأ في الحزب',
                'attr' => array(
                    'class' => 'width-50 chosen-select',
                    'data-placeholder' => 'اختر'
                )
            ));

        foreach($options['languages'] as $language) {
            $builder
                ->add('name_'.$language->getLocale(), TextType::class, array(
                    'mapped' => false,
                    'label' => "الإسم",
                    'constraints' => [
                        new Assert\NotBlank(),
                    ],
                ));
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ZekrBundle\Entity\Surah',
            'languages' => []
        ));
    }

}
