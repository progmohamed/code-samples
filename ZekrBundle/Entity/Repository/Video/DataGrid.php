<?php

namespace ZekrBundle\Entity\Repository\Video;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use AdminBundle\Classes\DataGrid as AdminDataGrid;
use ZekrBundle\Entity\Video;

class DataGrid extends AdminDataGrid
{
    public function getGrid($paginator, $page, $rowsInPage)
    {
        $em = $this->getEntityManager();
        $parameters = array();
        $dql = "SELECT v
        FROM ZekrBundle:Video v
        LEFT JOIN v.translations vt
        LEFT JOIN v.category vc
        LEFT JOIN ZekrBundle:VideoCollection vco WITH vco.video = v
        LEFT JOIN v.surah vs
        LEFT JOIN v.juz vj
        LEFT JOIN v.hizb vh
        LEFT JOIN v.rewaya vr
        LEFT JOIN v.person vp
        LEFT JOIN v.videoType vtt
        LEFT JOIN v.apiProject va
        WHERE v.deletedAt IS NULL ";

        $elementValue = $this->getFormDataElement('id');
        if ($elementValue) {
            $dql .= "AND v.id = :id ";
            $parameters['id'] = $elementValue;
        }

        $elementValue = $this->getFormDataElement('title');
        if ($elementValue) {
            $dql .= "AND vt.title LIKE :title ";
            $parameters['title'] = "%" . $elementValue . "%";
        }

        $elementValue = $this->getFormDataElement('slug');
        if ($elementValue) {
            $dql .= "AND (v.slug LIKE :slug OR v.plainSlug LIKE :slug ) ";
            $parameters['slug'] = "%" . $elementValue . "%";
        }

        $elementValue = $this->getFormDataElement('active');
        if ($elementValue) {
            $dql .= "AND v.active IN(:active) ";
            $parameters['active'] = $elementValue;
        }

        $elementValue = $this->getFormDataElement('conversion_status');
        if ($elementValue) {
            $dql .= "AND v.conversionStatus IN(:conversionStatus) ";
            $parameters['conversionStatus'] = $elementValue;
        }

        $elementValue = $this->getFormDataElement('category');
        if ($elementValue) {
            $dql .= "AND vc.id IN(:category) ";
            $parameters['category'] = $elementValue;
        }

        $elementValue = $this->getFormDataElement('collection');
        if ($elementValue) {
            $dql .= "AND vco.collection IN(:collection) ";
            $parameters['collection'] = $elementValue;
        }

        $elementValue = $this->getFormDataElement('surah');
        if ($elementValue) {
            $dql .= "AND vs.id IN(:surah) ";
            $parameters['surah'] = $elementValue;
        }

        $elementValue = $this->getFormDataElement('juz');
        if ($elementValue) {
            $dql .= "AND vj.id IN(:juz) ";
            $parameters['juz'] = $elementValue;
        }

        $elementValue = $this->getFormDataElement('hizb');
        if ($elementValue) {
            $dql .= "AND vh.id IN(:hizb) ";
            $parameters['hizb'] = $elementValue;
        }

        $elementValue = $this->getFormDataElement('rewaya');
        if ($elementValue) {
            $dql .= "AND vr.id IN(:rewaya) ";
            $parameters['rewaya'] = $elementValue;
        }

        $elementValue = $this->getFormDataElement('person');
        if ($elementValue) {
            $dql .= "AND vp.id IN(:person) ";
            $parameters['person'] = $elementValue;
        }

        $elementValue = $this->getFormDataElement('video_type');
        if ($elementValue) {
            $dql .= "AND vtt.id IN(:video_type) ";
            $parameters['video_type'] = $elementValue;
        }

        $elementValue = $this->getFormDataElement('api_project');
        if ($elementValue) {
            $dql .= "AND va.id IN(:api_project) ";
            $parameters['api_project'] = $elementValue;
        }

        $query = $em->createQuery($dql);
        if (sizeof($parameters)) {
            $query->setParameters($parameters);
        }

        $pagination = $paginator->paginate(
            $query,
            $page,
            $rowsInPage,
            array('wrap-queries' => true)
        );
        return $pagination;
    }

    public function getFilterForm($formFactory, $formActionUrl, $data = null, $options = [])
    {
        $form = $formFactory->createBuilder(FormType::class, $data, $options);
        $form
            ->setMethod('POST')
            ->setAction($formActionUrl)
            ->add('id', TextType::class, array(
                'label' => 'ID'
            ))
            ->add('slug', TextType::class, array(
                'label' => 'المعرف'
            ))
            ->add('title', TextType::class, array(
                'label' => 'العنوان'
            ))
            ->add('active', ChoiceType::class, array(
                'label' => 'فعال',
                'choices' => array(
                    'فعال' => 1,
                    'غير فعال' => 0,
                ),
                'multiple' => true,
            ))
            ->add('conversion_status', ChoiceType::class, array(
                'label' => 'حالة الملف',
                'choices' => array(
                    'بإنتظار دورها' => Video::STATUS_WAITING,
                    'جاري' => Video::STATUS_IN_PROGRESS,
                    'تم' => Video::STATUS_DONE,
                    'الملف مفقود' => Video::STATUS_FILES_MISSING,
                ),
                'multiple' => true,
            ))
            ->add('category', EntityType::class, array(
                'label' => 'التصنيف الموضوعي',
                'class' => 'ZekrBundle:Category',
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('c')
                        ->where('c.deletedAt is null');
                },
                'multiple' => true,
            ))

            ->add('juz', EntityType::class, array(
                'label' => 'الأجزاء',
                'class' => 'ZekrBundle:Juz',
                'multiple' => true,
            ))
            ->add('hizb', EntityType::class, array(
                'label' => 'الأحزاب',
                'class' => 'ZekrBundle:Hizb',
                'multiple' => true,
            ))
            ->add('surah', EntityType::class, array(
                'label' => 'السور',
                'class' => 'ZekrBundle:Surah',
                'multiple' => true,
            ))
            ->add('rewaya', EntityType::class, array(
                'label' => 'الروايات',
                'class' => 'ZekrBundle:Rewaya',
                'multiple' => true,
            ))
            ->add('person', EntityType::class, array(
                'label' => 'الأشخاص',
                'class' => 'ZekrBundle:Person',
                'multiple' => true,
            ))
            ->add('video_type', EntityType::class, array(
                'label' => 'نوع الفيديو',
                'class' => 'ZekrBundle:VideoType',
                'multiple' => true,
            ))
            ->add('collection', EntityType::class, array(
                'label' => 'التجميعة',
                'class' => 'ZekrBundle:Collection',
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('c')
                        ->where('c.deletedAt is null');
                },
                'multiple' => true,
            ))

            ->add('api_project', EntityType::class, array(
                'label' => 'مضمنة في مشاريع API',
                'class' => 'ZekrBundle:ApiProject',
                'multiple' => true,
            ));
        return $form->getForm();
    }
}