<?php

namespace ZekrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * ApiProject
 *
 * @ORM\Table(name="zekr_api_project", uniqueConstraints={@ORM\UniqueConstraint(name="inx_plain_slug", columns={"plain_slug"}), @ORM\UniqueConstraint(name="inx_slug", columns={"slug"})}, indexes={@ORM\Index(name="inx_name", columns={"name"}), @ORM\Index(name="inx_active", columns={"active"})})
 * @ORM\Entity(repositoryClass="ZekrBundle\Entity\Repository\ApiProject\Repository")
 * @UniqueEntity("plainSlug")
 */
class ApiProject
{
    use ORMBehaviors\Sluggable\Sluggable;

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
     * @ORM\Column(name="plain_slug", type="string", length=255, nullable=false, unique=true)
     * @Assert\NotBlank()
     */
    private $plainSlug;


    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Collection", mappedBy="apiProject")
     */
    private $collection;


    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    private $active = true;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->collection = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set plainSlug
     *
     * @param string $plainSlug
     *
     * @return ApiProject
     */
    public function setPlainSlug($plainSlug)
    {
        $this->plainSlug = $plainSlug;

        return $this;
    }


    /**
     * Get plainSlug
     *
     * @return string
     */
    public function getPlainSlug()
    {
        return $this->plainSlug;
    }

    public function getSluggableFields()
    {
        return ['plainSlug'];
    }


    /**
     * Add collection
     *
     * @param \ZekrBundle\Entity\Collection $collection
     *
     * @return ApiProject
     */
    public function addCollection(\ZekrBundle\Entity\Collection $collection)
    {
        $this->collection[] = $collection;

        return $this;
    }

    /**
     * Remove collection
     *
     * @param \ZekrBundle\Entity\Collection $collection
     */
    public function removeVideo(\ZekrBundle\Entity\Collection $collection)
    {
        $this->collection->removeElement($collection);
    }

    /**
     * Get collection
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return ApiProject
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return ApiProject
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
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function __toString()
    {
        return '' . $this->getName();
    }
}
