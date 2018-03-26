<?php

namespace LocaleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class LanguageType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $flagConstraints = [];
        if(!$options['edit']) {
            $flagConstraints[] = new Assert\NotBlank();
        }

        $builder
            ->add('name', null, [
                'label' => 'locale.titles.language.lang_name'
            ])
            ->add('locale', null, [
                'label' => 'locale.titles.language.lang_code',
            ])
            ->add('direction', ChoiceType::class, [
                'label' => 'locale.titles.language.lang_direction',
                'expanded' => true,
                'multiple' => false,
                'choices'  => [
                    'locale.titles.language.lang_rtl'    => 'rtl' ,
                    'locale.titles.language.lang_ltr'    => 'ltr',
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('photoFile', null, [
                'label' => 'locale.titles.language.lang_flag',
                'required' => true,
                'constraints' => $flagConstraints
            ])
            ->add('switchFrontEnd', null, [
                'label' => 'locale.titles.language.lang_switch_frontEnd'
            ])
            ->add('switchBackEnd', null, [
                'label' => 'locale.titles.language.lang_switch_backEnd'
            ])
            ->add('translateContent', null, [
                'label' => 'locale.titles.language.lang_translate_content'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'LocaleBundle\Entity\Language',
            'edit' => false
        ]);
    }

}
