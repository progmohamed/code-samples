<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints as Assert;
use AdminBundle\Classes\Sex;

class UserType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $passwordConstraints = null;
        if(!$options['entity'] || (isset($options['entity']) && !$options['entity']->getId()) ) {
            $passwordConstraints = [
                new Assert\NotBlank([
                    'groups' => ['UserManagement'],
                ]),
            ];
        }

        $builder->add('username', null, [
            'label' => 'admin.titles.username',
            'constraints' => [
                new Assert\NotBlank([
                    'groups' => ['UserManagement'],
                ]),
            ],
        ]);

        $builder->add('email', null, [
            'label' => 'admin.titles.email',
            'constraints' => [
                new Assert\NotBlank([
                    'groups' => ['UserManagement'],
                ]),
                new Assert\Email([
                    'groups' => ['UserManagement'],
                ]),
            ],
        ]);

        $builder->add('realName',null, [
            'label' => 'admin.titles.real_name',
            'constraints' => [
                new Assert\NotBlank([
                    'groups' => ['UserManagement'],
                ]),
            ],
        ])
        ->add('dateOfBirth', null, [
            'label' => 'admin.titles.date_of_birth',
            'years' => range(date('Y') -80, date('Y')+10),
            'constraints' => [
                new Assert\NotBlank([
                    'groups' => ['UserManagement'],
                ]),
            ],
        ])
        ->add('phoneNumber', null, [
            'label' => 'admin.titles.phone_number',
            'constraints' => [
                new Assert\NotBlank([
                    'groups' => ['UserManagement'],
                ]),
            ],
        ])
        ->add('sex', ChoiceType::class, [
            'label' => 'admin.titles.gender',
            'expanded' => true,
            'multiple' => false,
            'choices'  => [
                    'admin.titles.male'   => Sex::MALE ,
                    'admin.titles.female' => Sex::FEMALE,
            ],
            'constraints' => [
                new Assert\NotBlank([
                    'groups' => ['UserManagement'],
                ]),
            ],
        ]);

        $builder->add('plainPassword', RepeatedType::class, [
            'type'        => PasswordType::class,
            'constraints' => $passwordConstraints,
            'first_options'  => [
                'label' => 'admin.titles.password',
            ],
            'second_options' => [
                'label' => 'admin.titles.confirm_password',
            ],
        ]);

        $builder->add('photoFile', null, [
            'label' => 'admin.titles.photo',
        ]);

        $builder->add('personalWebsite', null, [
            'label' => 'admin.titles.website',
        ]);

        $builder->add('resume', null, [
            'label' => 'admin.titles.resume',
        ]);

        $builder->add('countryId', ChoiceType::class,  [
            'label' => 'admin.titles.country',
            'choices' => array_flip($options['countries']),
        ]);

        if(!$options['profile']) {
            $builder->add('groups', null, [
                'label' => 'admin.titles.groups',
            ]);
        }
        
        if($options['entity']) {
            $builder->add('removePhoto', CheckboxType::class, [
                'label' => 'admin.titles.remove_photo',
                'mapped' => false
            ]);
        }

        if(!$options['profile']) {
            $builder->add('enabled', null, [
                'label' => 'admin.titles.active',
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'        => 'AdminBundle\Entity\User',
            'validation_groups' => ['UserManagement'],
            'entity'            => null,
            'profile'           => false,
            'countries'         => [],
        ]);
    }

}
