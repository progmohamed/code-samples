<?php

namespace ZekrBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AdminBundle\Entity\Section;
use AdminBundle\Entity\SectionItem;

class LoadMenu implements FixtureInterface, ContainerAwareInterface
{
    public $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $section = new Section();
        $section->setIdentifier('zekr')
            ->setTitle('zekr.menu.zekr')
            ->setImage('bundles/zekr/images/dashboard/small/zekr.png')
            ->setDescription('zekr.hints.zekr')
            ->setDescriptionSort(6);

        $item = new SectionItem();
        $item->setTitle('zekr.menu.videos')
            ->setRoute('zekr_video_list')
            ->setImage('bundles/zekr/images/dashboard/video.png')
            ->addNewRoleByRoleName('ROLE_ADMIN_USER_LIST')
            ->setDescription('zekr.hints.videos')
            ->setDescriptionSort(7);
        $section->addItem($item);

        $item = new SectionItem();
        $item->setTitle('zekr.menu.selected_videos')
            ->setRoute('zekr_selected_video_list')
            ->setImage('bundles/zekr/images/dashboard/selected_videos.png')
            ->addNewRoleByRoleName('ROLE_ADMIN_USER_LIST')
            ->setDescription('zekr.hints.selected_videos')
            ->setDescriptionSort(8);
        $section->addItem($item);

        $item = new SectionItem();
        $item->setTitle('zekr.menu.selected_collections')
            ->setRoute('zekr_selected_collection_list')
            ->setImage('bundles/zekr/images/dashboard/selected_collections.png')
            ->addNewRoleByRoleName('ROLE_ADMIN_USER_LIST')
            ->setDescription('zekr.hints.selected_collections')
            ->setDescriptionSort(9);
        $section->addItem($item);


        $item = new SectionItem();
        $item->setTitle('zekr.menu.report')
            ->setRoute('zekr_videoreport_list')
            ->setImage('bundles/zekr/images/dashboard/report.png')
            ->addNewRoleByRoleName('ROLE_ADMIN_USER_LIST')
            ->setDescription('zekr.hints.report')
            ->setDescriptionSort(10);
        $section->addItem($item);


        $item = new SectionItem();
        $item->setTitle('zekr.menu.category')
            ->setRoute('zekr_category_list')
            ->setImage('bundles/zekr/images/dashboard/category.png')
            ->addNewRoleByRoleName('ROLE_SUPER_ADMIN')
            ->setDescription('zekr.hints.category')
            ->setDescriptionSort(11);
        $section->addItem($item);

        
        $item = new SectionItem();
        $item->setTitle('zekr.menu.collection')
            ->setRoute('zekr_collection_list')
            ->setImage('bundles/zekr/images/dashboard/collection.png')
            ->addNewRoleByRoleName('ROLE_ADMIN_USER_LIST')
            ->setDescription('zekr.hints.collection')
            ->setDescriptionSort(12);
        $section->addItem($item);


        $item = new SectionItem();
        $item->setTitle('zekr.menu.video_type')
            ->setRoute('zekr_video_type_list')
            ->setImage('bundles/zekr/images/dashboard/video_type.png')
            ->addNewRoleByRoleName('ROLE_ADMIN_USER_LIST')
            ->setDescription('zekr.hints.video_type')
            ->setDescriptionSort(13);
        $section->addItem($item);

        $item = new SectionItem();
        $item->setTitle('zekr.menu.person')
            ->setRoute('zekr_person_list')
            ->setImage('bundles/zekr/images/dashboard/person.png')
            ->addNewRoleByRoleName('ROLE_ADMIN_USER_LIST')
            ->setDescription('zekr.hints.person')
            ->setDescriptionSort(14);
        $section->addItem($item);


        $item = new SectionItem();
        $item->setTitle('zekr.menu.juz')
            ->setRoute('zekr_juz_list')
            ->setImage('bundles/zekr/images/dashboard/juz.png')
            ->addNewRoleByRoleName('ROLE_ADMIN_USER_LIST')
            ->setDescription('zekr.hints.juz')
            ->setDescriptionSort(15);
        $section->addItem($item);

        $item = new SectionItem();
        $item->setTitle('zekr.menu.hizb')
            ->setRoute('zekr_hizb_list')
            ->setImage('bundles/zekr/images/dashboard/hizb.png')
            ->addNewRoleByRoleName('ROLE_ADMIN_USER_LIST')
            ->setDescription('zekr.hints.hizb')
            ->setDescriptionSort(16);
        $section->addItem($item);

        $item = new SectionItem();
        $item->setTitle('zekr.menu.surah')
            ->setRoute('zekr_surah_list')
            ->setImage('bundles/zekr/images/dashboard/surah.png')
            ->addNewRoleByRoleName('ROLE_ADMIN_USER_LIST')
            ->setDescription('zekr.hints.surah')
            ->setDescriptionSort(17);
        $section->addItem($item);


        $item = new SectionItem();
        $item->setTitle('zekr.menu.rewaya')
            ->setRoute('zekr_rewaya_list')
            ->setImage('bundles/zekr/images/dashboard/rewaya.png')
            ->addNewRoleByRoleName('ROLE_ADMIN_USER_LIST')
            ->setDescription('zekr.hints.rewaya')
            ->setDescriptionSort(18);
        $section->addItem($item);


        $item = new SectionItem();
        $item->setTitle('zekr.menu.api_project')
            ->setRoute('zekr_api_project_list')
            ->setImage('bundles/zekr/images/dashboard/api_project.png')
            ->addNewRoleByRoleName('ROLE_ADMIN_USER_LIST')
            ->setDescription('zekr.hints.api_project')
            ->setDescriptionSort(19);
        $section->addItem($item);

        $item = new SectionItem();
        $item->setTitle('zekr.menu.newsletter')
            ->setRoute('zekr_newsletter_list')
            ->setImage('bundles/zekr/images/dashboard/newsletter.png')
            ->addNewRoleByRoleName('ROLE_ADMIN_USER_LIST')
            ->setDescription('zekr.hints.newsletter')
            ->setDescriptionSort(20);
        $section->addItem($item);

        $item = new SectionItem();
        $item->setTitle('zekr.menu.contact_us')
            ->setRoute('zekr_contact_us_list')
            ->setImage('bundles/zekr/images/dashboard/contact_us.png')
            ->addNewRoleByRoleName('ROLE_ADMIN_USER_LIST')
            ->setDescription('zekr.hints.contact_us')
            ->setDescriptionSort(21);
        $section->addItem($item);

        $item = new SectionItem();
        $item->setTitle('zekr.menu.static_page')
            ->setRoute('zekr_static_page_list')
            ->setImage('bundles/zekr/images/dashboard/static_page.png')
            ->addNewRoleByRoleName('ROLE_ADMIN_USER_LIST')
            ->setDescription('zekr.hints.static_page')
            ->setDescriptionSort(22);
        $section->addItem($item);


        $item = new SectionItem();
        $item->setTitle('zekr.menu.cache')
            ->setRoute('zekr_cache_index')
            ->setImage('bundles/zekr/images/dashboard/cache.png')
            ->addNewRoleByRoleName('ROLE_ADMIN_USER_LIST')
            ->setDescription('zekr.hints.cache')
            ->setDescriptionSort(23);
        $section->addItem($item);


        $manager->persist($section);
        $manager->flush();

    }
}