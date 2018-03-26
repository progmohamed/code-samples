<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SectionItemRole
 *
 * @ORM\Table(name="admin_section_item_role", indexes={@ORM\Index(name="inx_item_id", columns={"item_id"})})
 * @ORM\Entity
 */
class SectionItemRole
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
     * @var string
     *
     * @ORM\Column(name="role", type="string", length=255, nullable=false)
     */
    private $role;

    /**
     * @var \SectionItem
     *
     * @ORM\ManyToOne(targetEntity="SectionItem", inversedBy="roles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="item_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $item;



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
     * Set role
     *
     * @param string $role
     *
     * @return SectionItemRole
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set item
     *
     * @param \AdminBundle\Entity\SectionItem $item
     *
     * @return SectionItemRole
     */
    public function setItem(\AdminBundle\Entity\SectionItem $item = null)
    {
        $this->item = $item;

        return $this;
    }

    /**
     * Get item
     *
     * @return \AdminBundle\Entity\SectionItem
     */
    public function getItem()
    {
        return $this->item;
    }
}
