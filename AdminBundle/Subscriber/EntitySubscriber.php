<?php

namespace AdminBundle\Subscriber;

use AdminBundle\Classes\AdminEvent;
use AdminBundle\Entity\User;
use Doctrine\Common\EventSubscriber;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\Common\Persistence\Event\PreUpdateEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;


class EntitySubscriber implements EventSubscriber, ContainerAwareInterface
{
    use ContainerAwareTrait;

    private static $preservedId;

    public function getSubscribedEvents()
    {
        return [
            'preRemove',
            'postRemove',
        ];
    }

    public function getContainer() 
    {
        return $this->container;
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if ($entity instanceof User) {
            self::$preservedId = $entity->getId();
            $this->userPreRemove($entity);
        }
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if ($entity instanceof User) {
            $this->userPostRemove($entity);
        }
    }

    private function userPreRemove(User $user)
    {
        if($user->getId() == $this->getUser()->getId()) {
            throw new \Exception('لا يمكنك حذف نفسك');
        }
        $count = 0;
        $service = $this->container->get('admin.service');
        foreach($service->getRelatedServices() as $service) {
            $count += $service->getAdmin()->getUserRestrictions($user);
        }
        if($count) {
            throw new \Exception('لم يتم حذف المستخدم ' . $user->getUsername() . ' لوجود بيانات مرتبطة به في أماكن أخرى');
        }
        return true;
    }

    private function userPostRemove(User $user)
    {
        $id = self::$preservedId;
        $eventDispatcher = $this->container->get('event_dispatcher');
        $event = new GenericEvent();
        $event->setArgument('id', $id);
        $eventDispatcher->dispatch('admin.remove_user', $event);

        $this->container->get('common.service')->log(
            $this->get('fixedpages.service')->getName(),
            'admin.log.user_has_been_deleted',
            ['%id%'=>$id, 'username' => $user->getUsername()],
            $this->getUser()->getId()
        );
    }


    private function getUser()
    {
        if (!$this->container->has('security.token_storage')) {
            throw new \LogicException('The SecurityBundle is not registered in your application.');
        }
        if (null === $token = $this->container->get('security.token_storage')->getToken()) {
            return;
        }
        if (!is_object($user = $token->getUser())) {
            // e.g. anonymous authentication
            return;
        }
        return $user;
    }



}