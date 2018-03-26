<?php

namespace ZekrBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class FtpVideoType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('juz', null, array(
                'label' => 'الجزء',
                'attr' => array(
                    'class' => 'width-50 chosen-select tag-input-style',
                    'data-placeholder' => 'اختر'
                )
            ))
            ->add('hizb', null, array(
                'label' => 'الحزب',
                'attr' => array(
                    'class' => 'width-50 chosen-select tag-input-style',
                    'data-placeholder' => 'اختر'
                )
            ))
            ->add('surah', null, array(
                'label' => 'السورة',
                'attr' => array(
                    'class' => 'width-50 chosen-select tag-input-style',
                    'data-placeholder' => 'اختر'
                )
            ))
            ->add('person', null, array(
                'label' => 'الشخص',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->where('p.deletedAt is null');
                },
                'attr' => array(
                    'class' => 'width-50 chosen-select tag-input-style',
                    'data-placeholder' => 'اختر'
                )
            ))
            ->add('videoType', null, array(
                'label' => 'نوع الفيديو',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('vt')
                        ->where('vt.deletedAt is null');
                },
                'attr' => array(
                    'class' => 'width-50 chosen-select tag-input-style',
                    'data-placeholder' => 'اختر'
                )
            ))
            ->add('rewaya', null, array(
                'label' => 'الرواية',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('r')
                        ->where('r.deletedAt is null');
                },
                'attr' => array(
                    'class' => 'width-50 chosen-select',
                    'data-placeholder' => 'اختر'
                )
            ))
            ->add('collection', EntityType::class, array(
                'class' => 'ZekrBundle:Collection',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->where('c.deletedAt is null')
                        ->orderBy('c.sortOrder', 'ASC');
                },
                'multiple' => true,
                'mapped' => false,
                'label' => 'التجميعة',
                'attr' => array(
                    'class' => 'width-50 chosen-select tag-input-style',
                    'data-placeholder' => 'اختر'
                )
            ))
            ->add('apiProject', null, array(
                'label' => 'تضمين في مشاريع ال API',
                'attr' => array(
                    'class' => 'width-50 chosen-select tag-input-style',
                    'data-placeholder' => 'اختر'
                )
            ))
            ->add('category', null, array(
                'label' => 'التصنيف الموضوعي',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->where('c.deletedAt is null');
                },
                'attr' => array(
                    'class' => 'width-50 chosen-select tag-input-style',
                    'data-placeholder' => 'اختر'
                )
            ))
            ->add('active', null, array(
                'label' => 'فعال',
                'attr' => array(
                    'class' => 'ace ace-switch ace-switch-5'
                )
            ))
            ->add('conversion', null, array(
                'label' => 'تحويل',
                'attr' => array(

//                    'disabled' => ($options['edit']) ? true : false,
                    'class' => 'ace ace-switch ace-switch-5'
                )
            ))
            ->add('videos', ChoiceType::class, [
                'label' => 'الفيديوهات',
                'choices' => $options['files'],
                'mapped' => false,
                'expanded' => false,
                'multiple' => true,
                'attr' => array(
                    'class' => 'width-50 chosen-select tag-input-style',
                    'data-placeholder' => 'اختر',
                )
            ]);

        foreach ($options['languages'] as $language) {
            $builder
                ->add('display_' . $language->getLocale(), CheckboxType::class, array(
                    'label' => 'عرض',
                    'mapped' => false,
                    'attr' => array(
                        'class' => 'ace ace-switch ace-switch-5',
                    )
                ));
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ZekrBundle\Entity\Video',
            'languages' => [],
            'files' => []
        ));
    }

}
