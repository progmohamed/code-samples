<?php

namespace LocaleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;

class DialectType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('language', null,  [
                 'label' => 'admin.titles.lang',
            ])
           ;

        foreach($options['languages'] as $language) {
            $builder
                ->add('name_'.$language->getLocale(),  TextType::class, [
                    'mapped' => false,
                    'label' => "locale.titles.dialect.dialect_name",
                    'constraints' => [
                        new Assert\NotBlank(),
                    ],
                ])
                ;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'LocaleBundle\Entity\Dialect',
            'languages' => [],
        ]);
    }

}
