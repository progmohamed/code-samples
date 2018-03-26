<?php

namespace ZekrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CategoryDescendant
 *
 * @ORM\Table(name="zekr_category_descendant", uniqueConstraints={@ORM\UniqueConstraint(name="node_descendant", columns={"node_id", "descendant_id"})}, indexes={@ORM\Index(name="inx_active", columns={"active"}), @ORM\Index(name="inx_descendant_id", columns={"descendant_id"}), @ORM\Index(name="inx_node_id", columns={"node_id"})})
 * @ORM\Entity
 */
class CategoryDescendant
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    private $active;

    /**
     * @var \Category
     *
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="node_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $node;

    /**
     * @var \Category
     *
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="descendant_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $descendant;


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
     * Set active
     *
     * @param boolean $active
     *
     * @return CategoryDescendant
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set node
     *
     * @param \ZekrBundle\Entity\Category $node
     *
     * @return CategoryDescendant
     */
    public function setNode(\ZekrBundle\Entity\Category $node = null)
    {
        $this->node = $node;

        return $this;
    }

    /**
     * Get node
     *
     * @return \ZekrBundle\Entity\Category
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * Set descendant
     *
     * @param \ZekrBundle\Entity\Category $descendant
     *
     * @return CategoryDescendant
     */
    public function setDescendant(\ZekrBundle\Entity\Category $descendant = null)
    {
        $this->descendant = $descendant;

        return $this;
    }

    /**
     * Get descendant
     *
     * @return \ZekrBundle\Entity\Category
     */
    public function getDescendant()
    {
        return $this->descendant;
    }
}
