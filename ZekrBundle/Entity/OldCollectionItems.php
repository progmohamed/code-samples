<?php

namespace ZekrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OldCollectionItems
 *
 * @ORM\Table(name="old_collection_items")
 * @ORM\Entity
 */
class OldCollectionItems
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ci_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $ciId;

    /**
     * @var integer
     *
     * @ORM\Column(name="collection_id", type="bigint", nullable=false)
     */
    private $collectionId;

    /**
     * @var integer
     *
     * @ORM\Column(name="object_id", type="bigint", nullable=false)
     */
    private $objectId;

    /**
     * @var integer
     *
     * @ORM\Column(name="userid", type="bigint", nullable=false)
     */
    private $userid;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=10, nullable=false)
     */
    private $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_added", type="datetime", nullable=false)
     */
    private $dateAdded;



    /**
     * Get ciId
     *
     * @return integer
     */
    public function getCiId()
    {
        return $this->ciId;
    }

    /**
     * Set collectionId
     *
     * @param integer $collectionId
     *
     * @return OldCollectionItems
     */
    public function setCollectionId($collectionId)
    {
        $this->collectionId = $collectionId;

        return $this;
    }

    /**
     * Get collectionId
     *
     * @return integer
     */
    public function getCollectionId()
    {
        return $this->collectionId;
    }

    /**
     * Set objectId
     *
     * @param integer $objectId
     *
     * @return OldCollectionItems
     */
    public function setObjectId($objectId)
    {
        $this->objectId = $objectId;

        return $this;
    }

    /**
     * Get objectId
     *
     * @return integer
     */
    public function getObjectId()
    {
        return $this->objectId;
    }

    /**
     * Set userid
     *
     * @param integer $userid
     *
     * @return OldCollectionItems
     */
    public function setUserid($userid)
    {
        $this->userid = $userid;

        return $this;
    }

    /**
     * Get userid
     *
     * @return integer
     */
    public function getUserid()
    {
        return $this->userid;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return OldCollectionItems
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set dateAdded
     *
     * @param \DateTime $dateAdded
     *
     * @return OldCollectionItems
     */
    public function setDateAdded($dateAdded)
    {
        $this->dateAdded = $dateAdded;

        return $this;
    }

    /**
     * Get dateAdded
     *
     * @return \DateTime
     */
    public function getDateAdded()
    {
        return $this->dateAdded;
    }
}
