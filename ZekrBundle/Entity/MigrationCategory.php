<?php

namespace ZekrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * MigrationCategory
 *
 * @ORM\Table(name="category", indexes={@ORM\Index(name="parent_id", columns={"parent_id"})})
 * @ORM\Entity
 */
class MigrationCategory
{
    use ORMBehaviors\Translatable\Translatable;
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \MigrationCategory
     *
     * @ORM\ManyToOne(targetEntity="MigrationCategory")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     * })
     */
    private $parent;



    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set parent
     *
     * @param \ZekrBundle\Entity\MigrationCategory $parent
     *
     * @return MigrationCategory
     */
    public function setParent(\ZekrBundle\Entity\MigrationCategory $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \ZekrBundle\Entity\MigrationCategory
     */
    public function getParent()
    {
        return $this->parent;
    }

    public function __toString() {
        return ''.$this->translate('en')->getName();
    }
}
