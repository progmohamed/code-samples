<?php

namespace LocaleBundle\Subscriber;

use AdminBundle\Classes\AdminEvent;
use AdminBundle\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\Common\Persistence\Event\PreUpdateEventArgs;
use LocaleBundle\Entity\Country;
use LocaleBundle\Entity\Dialect;
use LocaleBundle\Entity\Language;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\GenericEvent;


class EntitySubscriber implements EventSubscriber, ContainerAwareInterface
{

    use ContainerAwareTrait;

    private static $preservedId;

    public function getSubscribedEvents()
    {
        return [
            'prePersist',
            'preRemove',
            'postRemove',
        ];
    }

    public function getContainer() 
    {
        return $this->container;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if ($this->isTranslation($entity)) {
            $this->setLanguageId($entity);
        }
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        self::$preservedId = $entity->getId();
        if ($entity instanceof Language) {
            $this->languagePreRemove($entity);
        }
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if ($entity instanceof Language) {
            $this->languagePostRemove($entity);
        }elseif ($entity instanceof Country) {
            $this->countryPostRemove();
        }elseif ($entity instanceof Dialect) {
            $this->dialectPostRemove();
        }
    }

    ////////////////////////////////

    private function isTranslation($entity)
    {
        $class = new \ReflectionClass($entity);
        $traits = $class->getTraits();
        return array_key_exists('LocaleBundle\Classes\TranslationLanguageTrait', $traits);
    }

    private function setLanguageId($entity)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $languageRepository = $em->getRepository('LocaleBundle:Language');
        $id = $languageRepository->getLanguageIdByLocale($entity->getLocale());
        if($id) {
            $entity->setLanguage($id);
        }else{
            throw new \Exception('Language not found');
        }
    }

    private function languagePreRemove(Language $entity)
    {
        $count = 0;
        $service = $this->container->get('history.service');
        foreach($service->getRelatedServices() as $service) {
            $count += $service->getLocale()->getLanguageRestrictions($entity->getId());
        }
        if($count) {
            throw new \Exception('لم يتم حذف ' . $entity . ' لوجود بيانات مرتبطة به في أماكن أخرى');
        }
        return true;
    }

    private function languagePostRemove(Language $language)
    {
        $id = self::$preservedId;
        $eventDispatcher = $this->container->get('event_dispatcher');
        $event = new GenericEvent();
        $event->setArgument('id', $id);
        $eventDispatcher->dispatch('locale.remove_language', $event);
    }

    private function countryPostRemove()
    {
        $id = self::$preservedId;
        $eventDispatcher = $this->container->get('event_dispatcher');
        $event = new GenericEvent();
        $event->setArgument('id', $id);
        $eventDispatcher->dispatch('locale.remove_country', $event);
    }

    private function dialectPostRemove()
    {
        $id = self::$preservedId;
        $eventDispatcher = $this->container->get('event_dispatcher');
        $event = new GenericEvent();
        $event->setArgument('id', $id);
        $eventDispatcher->dispatch('locale.remove_dialect', $event);
    }



}