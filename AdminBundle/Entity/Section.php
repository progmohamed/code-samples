<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * Section
 *
 * @ORM\Table(name="admin_section", uniqueConstraints={@ORM\UniqueConstraint(name="uniq_identifier", columns={"identifier"})})
 * @ORM\Entity
 */
class Section
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
     * @ORM\Column(name="identifier", type="string", length=255, nullable=false)
     */
    private $identifier;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=255, nullable=true)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="description_sort", type="integer", nullable=true)
     */
    private $descriptionSort = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @ORM\OneToMany(targetEntity="AdminBundle\Entity\SectionItem", mappedBy="section", cascade={"persist", "remove"})
     */
    private $items;


    public function __construct()
    {
        $this->items = new ArrayCollection();
    }


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
     * Set identifier
     *
     * @param string $identifier
     *
     * @return Section
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Get identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Section
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }


    /**
     * Set description
     *
     * @param string $description
     *
     * @return Section
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }


    /**
     * Set descriptionSort
     *
     * @param integer $descriptionSort
     *
     * @return Section
     */
    public function setDescriptionSort($descriptionSort)
    {
        $this->descriptionSort = $descriptionSort;

        return $this;
    }

    /**
     * Get descriptionSort
     *
     * @return integer
     */
    public function getDescriptionSort()
    {
        return $this->descriptionSort;
    }


    /**
     * Set image
     *
     * @param string $image
     *
     * @return Section
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    public function getItems()
    {
        return $this->items;
    }

    public function addItem(SectionItem $item)
    {
        $item->setSection($this);
        $this->items->add($item);
        return $this;
    }

    public function removeItem(SectionItem $item)
    {
        $this->items->removeElement($item);
        return $this;
    }

    public function __toString()
    {
        return $this->getTitle();
    }
}
