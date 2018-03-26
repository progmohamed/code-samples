<?php

namespace CommonBundle\Service;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class OnFlushHelper implements ContainerAwareInterface
{

    use ContainerAwareTrait;

    public function getUpdatedEntities()
    {
        $em = $this->container->get('doctrine')->getManager();
        $unitOfWork = $em->getUnitOfWork();
        $updatedEntities = $unitOfWork->getScheduledEntityUpdates();
        return $updatedEntities;
    }

    public function getEntityChangeSet($entity)
    {
        $em = $this->container->get('doctrine')->getManager();
        $unitOfWork = $em->getUnitOfWork();
        $changeset = $unitOfWork->getEntityChangeSet($entity);
        return (array) $changeset;
    }

    public function isFieldChanged($changeset, $field)
    {
        return array_key_exists($field, $changeset);
    }

    public function getFieldChanges($changeset, $field)
    {
        $changes = [];
        if($this->isFieldChanged($changeset, $field)) {
            $changes = $changeset[$field];
        }
        return $changes;
    }

    public function getFieldOldValue($changeset, $field)
    {
        $changes = $this->getFieldChanges($changeset, $field);
        return array_key_exists(0, $changes) ? $changes[0] : null;
    }

    public function getFieldNewValue($changeset, $field)
    {
        $changes = $this->getFieldChanges($changeset, $field);
        return array_key_exists(1, $changes) ? $changes[1] : null;
    }

    public function computeEntityChangeSet($entity)
    {
        $em = $this->container->get('doctrine')->getManager();
        $unitOfWork = $em->getUnitOfWork();
        $metaData = $em->getClassMetadata(get_class($entity));
        $unitOfWork->computeChangeSet($metaData, $entity);
    }

}