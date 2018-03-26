<?php

namespace AdminBundle\Service;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class HardDeleteHelper implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    protected $originalEventListeners = [];

    public function disableSoftDeleteListeners()
    {
        $em = $this->container->get('doctrine')->getManager();
        foreach ($em->getEventManager()->getListeners() as $eventName => $listeners) {
            foreach ($listeners as $listener) {
                if ($listener instanceof \Knp\DoctrineBehaviors\ORM\SoftDeletable\SoftDeletableSubscriber) {
                    $this->originalEventListeners[$eventName] = $listener;
                    $em->getEventManager()->removeEventListener($eventName, $listener);
                }
            }
        }
    }

    public function reenableSoftDeleteListeners()
    {
        $em = $this->container->get('doctrine')->getManager();
        foreach ($this->originalEventListeners as $eventName => $listener) {
            $em->getEventManager()->addEventListener($eventName, $listener);
        }
    }
}