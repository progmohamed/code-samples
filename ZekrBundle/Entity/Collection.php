<?php

namespace ZekrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation as Serializer;

/**
 * Collection
 *
 * @ORM\Table(name="zekr_collection", uniqueConstraints={@ORM\UniqueConstraint(name="inx_plain_slug", columns={"plain_slug"}), @ORM\UniqueConstraint(name="inx_slug", columns={"slug"})}, indexes={@ORM\Index(name="inx_sort_order", columns={"sort_order"}), @ORM\Index(name="inx_num_topics", columns={"num_topics"}), @ORM\Index(name="inx_deleted_at", columns={"deleted_at"}), @ORM\Index(name="inx_active", columns={"active"}), @ORM\Index(name="inx_selected", columns={"selected"}), @ORM\Index(name="inx_views", columns={"views"}), @ORM\Index(name="inx_num_active_topics", columns={"num_active_topics"})})
 * @ORM\Entity(repositoryClass="ZekrBundle\Entity\Repository\Collection\Repository")
 * @UniqueEntity("plainSlug")
 * @Serializer\ExclusionPolicy("all")
 */
class Collection
{
    use ORMBehaviors\Translatable\Translatable;
    use ORMBehaviors\Sluggable\Sluggable;
    use ORMBehaviors\SoftDeletable\SoftDeletable;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Serializer\Expose()
     * @Serializer\Groups({"default"})
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
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    private $active = true;

    /**
     * @var integer
     *
     * @ORM\Column(name="sort_order", type="integer", nullable=false)
     * @Serializer\Expose()
     * @Serializer\SerializedName("sortOrder")
     * @Serializer\Groups({"default"})
     */
    private $sortOrder;

    /**
     * @var boolean
     *
     * @ORM\Column(name="selected", type="boolean", nullable=true)
     * @Serializer\Expose()
     * @Serializer\Groups({"default"})
     */
    private $selected;

    /**
     * @var integer
     *
     * @ORM\Column(name="num_topics", type="integer", nullable=false)
     */
    private $numTopics = 0;


    /**
     * @var integer
     *
     * @ORM\Column(name="num_active_topics", type="integer", nullable=false)
     * @Serializer\Expose()
     * @Serializer\SerializedName("numActiveTopics")
     * @Serializer\Groups({"default"})
     */
    private $numActiveTopics = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="views", type="integer", nullable=true)
     * @Serializer\Expose()
     * @Serializer\Groups({"default"})
     */
    private $views = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="thumbnail_file", type="string", length=255, nullable=true)
     * @Serializer\Expose()
     * @Serializer\SerializedName("thumbnailFile")
     * @Serializer\Groups({"default"})
     */
    private $thumbnailFile;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="ApiProject", inversedBy="collection")
     * @ORM\JoinTable(name="zekr_collection_api_project",
     *   joinColumns={
     *     @ORM\JoinColumn(name="collection_id", referencedColumnName="id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="project_id", referencedColumnName="id", onDelete="CASCADE")
     *   }
     * )
     */
    private $apiProject;


    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="ZekrBundle\Entity\VideoCollection", mappedBy="collection", fetch="EXTRA_LAZY")
     */
    private $videoCollections;


    public function __construct()
    {
        $this->apiProject = new \Doctrine\Common\Collections\ArrayCollection();
        $this->videoCollections = new \Doctrine\Common\Collections\ArrayCollection();

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

    public function getVideoCollections()
    {
        return $this->videoCollections;
    }

    /**
     * Set plainSlug
     *
     * @param string $plainSlug
     *
     * @return Juz
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
     * Set active
     *
     * @param boolean $active
     *
     * @return Collection
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
     * Set sortOrder
     *
     * @param integer $sortOrder
     *
     * @return Collection
     */
    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    /**
     * Get sortOrder
     *
     * @return integer
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    /**
     * Set selected
     *
     * @param boolean $selected
     *
     * @return Collection
     */
    public function setSelected($selected)
    {
        $this->selected = $selected;

        return $this;
    }

    /**
     * Get selected
     *
     * @return boolean
     */
    public function getSelected()
    {
        return $this->selected;
    }


    /**
     * Set numTopics
     *
     * @param integer $numTopics
     *
     * @return Collection
     */
    public function setNumTopics($numTopics)
    {
        $this->numTopics = $numTopics;

        return $this;
    }

    /**
     * Get numTopics
     *
     * @return integer
     */
    public function getNumTopics()
    {
        return $this->numTopics;
    }

    /**
     * Set numActiveTopics
     *
     * @param integer $numActiveTopics
     *
     * @return Collection
     */
    public function setNumActiveTopics($numActiveTopics)
    {
        $this->numActiveTopics = $numActiveTopics;

        return $this;
    }

    /**
     * Get numActiveTopics
     *
     * @return integer
     */
    public function getNumActiveTopics()
    {
        return $this->numActiveTopics;
    }

    /**
     * Add apiProject
     *
     * @param \ZekrBundle\Entity\ApiProject $apiProject
     *
     * @return Collection
     */
    public function addApiProject(\ZekrBundle\Entity\ApiProject $apiProject)
    {
        $this->apiProject[] = $apiProject;

        return $this;
    }

    /**
     * Remove apiProject
     *
     * @param \ZekrBundle\Entity\ApiProject $apiProject
     */
    public function removeApiProject(\ZekrBundle\Entity\ApiProject $apiProject)
    {
        $this->apiProject->removeElement($apiProject);
    }

    /**
     * Get apiProject
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getApiProject()
    {
        return $this->apiProject;
    }


    /**
     * Set views
     *
     * @param integer $views
     *
     * @return Collection
     */
    public function setViews($views)
    {
        $this->views = $views;

        return $this;
    }

    /**
     * Get views
     *
     * @return integer
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * Set thumbnailFile
     *
     * @param string $thumbnailFile
     *
     * @return Collection
     */
    public function setThumbnailFile($thumbnailFile)
    {
        $this->thumbnailFile = $thumbnailFile;

        return $this;
    }

    /**
     * Get thumbnailFile
     *
     * @return string
     */
    public function getThumbnailFile()
    {
        return $this->thumbnailFile;
    }

    /**
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("slug")
     * @Serializer\Groups({"default"})
     */
    public function getSerializedSlug()
    {
        return $this->getSlug();
    }

    /**
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("name")
     * @Serializer\Groups({"default"})
     */
    public function getSerializedName()
    {
        return $this->translate()->getName();
    }

    public function __toString()
    {
        if ($name = $this->translate()->getName()) {
            return $name;
        }
        return '';
    }
}
