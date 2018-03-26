<?php

namespace ZekrBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints as Assert;
use ZekrBundle\Entity\VideoReport;

class VideoReportType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('reason', ChoiceType::class, [
                'label' => 'zekr.form.reason',
                'choices'  => [
                    'zekr.form.contrary_content' => VideoReport::CONTRARY_CONTENT,
                    'zekr.form.privacy_infringement' => VideoReport::PRIVACY_INFRINGEMENT,
                    'zekr.form.copyright_infringement' => VideoReport::COPYRIGHT_INFRINGEMENT,
                    'zekr.form.other' => VideoReport::OTHER,
                ],
            ])
            ->add('message', null, array(
                'label' => 'zekr.form.message',
            ))
            ->add('video', HiddenType::class);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ZekrBundle\Entity\VideoReport',
        ));
    }

}
