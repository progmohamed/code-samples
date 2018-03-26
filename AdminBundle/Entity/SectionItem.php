<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * SectionItem
 *
 * @ORM\Table(name="admin_section_item", indexes={@ORM\Index(name="inx_section_id", columns={"section_id"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class SectionItem
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
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="href", type="text", length=65535, nullable=false)
     */
    private $href;

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
     * @ORM\Column(name="image", type="string", length=255, nullable=false)
     */
    private $image;


    /**
     * @var \Section
     *
     * @ORM\ManyToOne(targetEntity="Section", inversedBy="items")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="section_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $section;
    
    /**
     * @ORM\OneToMany(targetEntity="AdminBundle\Entity\SectionItemRole", mappedBy="item", cascade={"persist", "remove"})
     */
    private $roles;
    
    private $route;

    private $routeParameters;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
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
     * Set title
     *
     * @param string $title
     *
     * @return SectionItem
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
     * Set href
     *
     * @param string $href
     *
     * @return SectionItem
     */
    private function setHref($href)
    {
        $this->href = $href;

        return $this;
    }

    /**
     * Get href
     *
     * @return string
     */
    private function getHref()
    {
        return $this->href;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return SectionItem
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


    /**
     * Set section
     *
     * @param \AdminBundle\Entity\Section $section
     *
     * @return SectionItem
     */
    public function setSection(\AdminBundle\Entity\Section $section = null)
    {
        $this->section = $section;

        return $this;
    }

    /**
     * Get section
     *
     * @return \AdminBundle\Entity\Section
     */
    public function getSection()
    {
        return $this->section;
    }

    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    public function getRoute()
    {
        return $this->route;
    }

    public function setRouteParameters(array $routeParameters)
    {
        $this->routeParameters = $routeParameters;

        return $this;
    }

    public function getRouteParameters()
    {
        return $this->routeParameters;
    }
    
    public function addRole(\AdminBundle\Entity\SectionItemRole $role)
    {
        $role->setItem($this);
        $this->roles->add($role);
        return $this;
    }
    
    public function addNewRoleByRoleName($roleName)
    {
        $role = new \AdminBundle\Entity\SectionItemRole();
        $role->setRole($roleName);
        $role->setItem($this);
        $this->roles->add($role);
        return $this;
    }
    
    public function getRoles()
    {
        return $this->roles;
    }
    
    public function getRoleArray()
    {
        $roles = [];
        foreach($this->getRoles() as $role) {
            $roles[] = $role->getRole();
        }
        return $roles;
    }

    /**
     * @ORM\PrePersist
     */
    public function beforeSave()
    {
        $data = [
            'route' => $this->getRoute(),
            'parameters' => (array) $this->getRouteParameters(),
        ];
        $this->setHref( serialize($data) );
    }

    /**
     * @ORM\PostLoad()
     */
    public function afterLoad()
    {
        $href = $this->getHref();
        if($href) {
            $data = unserialize($href);
            $this->setRoute($data['route']);
            $this->setRouteParameters($data['parameters']);
        }
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return SectionItem
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
     * @return SectionItem
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


    public function __toString()
    {
        return $this->getTitle();
    }
}
