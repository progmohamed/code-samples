<?php

namespace ZekrBundle\Subscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\Common\Persistence\Event\PreUpdateEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use ZekrBundle\Entity\Category;
use ZekrBundle\Entity\CategoryTranslation;
use ZekrBundle\Entity\Collection;
use ZekrBundle\Entity\CollectionTranslation;
use ZekrBundle\Entity\Hizb;
use ZekrBundle\Entity\HizbTranslation;
use ZekrBundle\Entity\Juz;
use ZekrBundle\Entity\JuzTranslation;
use ZekrBundle\Entity\Person;
use ZekrBundle\Entity\PersonTranslation;
use ZekrBundle\Entity\Rewaya;
use ZekrBundle\Entity\RewayaTranslation;
use ZekrBundle\Entity\Surah;
use ZekrBundle\Entity\SurahTranslation;
use ZekrBundle\Entity\Video;
use Knp\DoctrineBehaviors\Model\Sluggable\Transliterator;
use ZekrBundle\Entity\VideoType;
use ZekrBundle\Entity\VideoTypeTranslation;


class EntitySubscriber implements EventSubscriber
{
    private $container;


    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getSubscribedEvents()
    {
        return [
            'prePersist',
            'postUpdate',
            'postRemove'
        ];
    }

    public function getContainer() 
    {
        return $this->container;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if ($entity instanceof Video) {
            $this->VideoPrePersist($entity);
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if ($entity instanceof CategoryTranslation) {
            $taskmanager = $this->container->get('taskmanager.service');
            $taskmanager->addTaskQueued('zekr:edit-category', ['id' => $entity->getTranslatable()->getId()], 'edit category');
        }elseif ($entity instanceof CollectionTranslation) {
            $taskmanager = $this->container->get('taskmanager.service');
            $taskmanager->addTaskQueued('zekr:edit-collection', ['id' => $entity->getTranslatable()->getId()], 'edit collection');
        }elseif ($entity instanceof HizbTranslation) {
            $taskmanager = $this->container->get('taskmanager.service');
            $taskmanager->addTaskQueued('zekr:edit-hizb', ['id' => $entity->getTranslatable()->getId()], 'edit hizb');
        }elseif ($entity instanceof JuzTranslation) {
            $taskmanager = $this->container->get('taskmanager.service');
            $taskmanager->addTaskQueued('zekr:edit-juz', ['id' => $entity->getTranslatable()->getId()], 'edit juz');
        }elseif ($entity instanceof PersonTranslation) {
            $taskmanager = $this->container->get('taskmanager.service');
            $taskmanager->addTaskQueued('zekr:edit-person', ['id' => $entity->getTranslatable()->getId()], 'edit person');
        }elseif ($entity instanceof RewayaTranslation) {
            $taskmanager = $this->container->get('taskmanager.service');
            $taskmanager->addTaskQueued('zekr:edit-rewaya', ['id' => $entity->getTranslatable()->getId()], 'edit rewaya');
        }elseif ($entity instanceof SurahTranslation) {
            $taskmanager = $this->container->get('taskmanager.service');
            $taskmanager->addTaskQueued('zekr:edit-surah', ['id' => $entity->getTranslatable()->getId()], 'edit surah');
        }elseif ($entity instanceof VideoTypeTranslation) {
            $taskmanager = $this->container->get('taskmanager.service');
            $taskmanager->addTaskQueued('zekr:edit-video-type', ['id' => $entity->getTranslatable()->getId()], 'edit video-type');
        }
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if ($entity instanceof CategoryTranslation) {
            $taskmanager = $this->container->get('taskmanager.service');
            $taskmanager->addTaskQueued('zekr:edit-category', ['id' => $entity->getTranslatable()->getId()], 'edit category');
        }elseif ($entity instanceof CollectionTranslation) {
            $taskmanager = $this->container->get('taskmanager.service');
            $taskmanager->addTaskQueued('zekr:edit-collection', ['id' => $entity->getTranslatable()->getId()], 'edit collection');
        }elseif ($entity instanceof PersonTranslation) {
            $taskmanager = $this->container->get('taskmanager.service');
            $taskmanager->addTaskQueued('zekr:edit-person', ['id' => $entity->getTranslatable()->getId()], 'edit person');
        }elseif ($entity instanceof RewayaTranslation) {
            $taskmanager = $this->container->get('taskmanager.service');
            $taskmanager->addTaskQueued('zekr:edit-rewaya', ['id' => $entity->getTranslatable()->getId()], 'edit rewaya');
        }elseif ($entity instanceof VideoTypeTranslation) {
            $taskmanager = $this->container->get('taskmanager.service');
            $taskmanager->addTaskQueued('zekr:edit-video-type', ['id' => $entity->getTranslatable()->getId()], 'edit video-type');
        }
    }

    private function VideoPrePersist(Video $video)
    {
        $plainSlug = $video->getPlainSlug();
        $em = $this->container->get('doctrine')->getManager();
        $videoRepository = $em->getRepository('ZekrBundle:Video');

        $concatenateValue = ($videoRepository->getConcatenateValue()+1).'_';
        $video->setPlainSlug($concatenateValue.$plainSlug);

        $sluggableText = $video->getPlainSlug();

        $transliterator = new Transliterator;
        $sluggableText = $transliterator->transliterate($sluggableText, $this->getSlugDelimiter());

        $urlized = strtolower( trim( preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $sluggableText ), $this->getSlugDelimiter() ) );
        $urlized = preg_replace("/[\/_|+ -]+/", $this->getSlugDelimiter(), $urlized);

        $video->setSlug($urlized);

    }

    private function getSlugDelimiter()
    {
        return '-';
    }



}