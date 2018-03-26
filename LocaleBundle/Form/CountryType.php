<?php

namespace LocaleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;

class CountryType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        foreach($options['languages'] as $language) {
            $builder
                ->add('name_'.$language->getLocale(), TextType::class, [
                    'mapped' => false,
                    'label' => "admin.titles.country",
                    'constraints' => [
                        new Assert\NotBlank(),
                    ],
                ]);
        }

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'LocaleBundle\Entity\Country',
            'languages' => []
        ]);
    }

}
