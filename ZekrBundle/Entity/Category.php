<?php

namespace ZekrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation as Serializer;

/**
 * Category
 *
 * @ORM\Table(name="zekr_category", uniqueConstraints={@ORM\UniqueConstraint(name="inx_plain_slug", columns={"plain_slug"}), @ORM\UniqueConstraint(name="inx_slug", columns={"slug"})}, indexes={@ORM\Index(name="inx_parent_id", columns={"parent_id"}), @ORM\Index(name="inx_sort_order", columns={"sort_order"}), @ORM\Index(name="inx_num_direct_children", columns={"num_direct_children"}), @ORM\Index(name="inx_num_all_children", columns={"num_all_children"}), @ORM\Index(name="inx_level", columns={"level"}), @ORM\Index(name="inx_deleted_at", columns={"deleted_at"}), @ORM\Index(name="inx_num_direct_topics", columns={"num_direct_topics"}), @ORM\Index(name="inx_num_all_topics", columns={"num_all_topics"}), @ORM\Index(name="inx_active", columns={"active"}), @ORM\Index(name="inx_num_direct_active_topics", columns={"num_direct_active_topics"}), @ORM\Index(name="inx_num_all_active_topics", columns={"num_all_active_topics"})}) * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="ZekrBundle\Entity\Repository\Category\Repository")
 * @UniqueEntity("plainSlug")
 * @Serializer\ExclusionPolicy("all")
 */
class Category
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
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="old_id", type="integer", nullable=false)
     */
    private $oldId = 0;


    /**
     * @var string
     *
     * @ORM\Column(name="plain_slug", type="string", length=255, nullable=false, unique=true)
     * @Assert\NotBlank()
     */
    private $plainSlug;

    /**
     * @var integer
     *
     * @ORM\Column(name="sort_order", type="integer", nullable=false)
     * @Serializer\Expose()
     * @Serializer\SerializedName("sortOrder")
     */
    private $sortOrder;

    /**
     * @var integer
     *
     * @ORM\Column(name="num_direct_children", type="integer", nullable=false)
     * @Serializer\Expose()
     * @Serializer\SerializedName("numDirectChildren")
     */
    private $numDirectChildren = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="num_all_children", type="integer", nullable=false)
     * @Serializer\Expose()
     * @Serializer\SerializedName("numAllChildren")
     */
    private $numAllChildren = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="num_direct_topics", type="integer", nullable=false)
     */
    private $numDirectTopics = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="num_direct_active_topics", type="integer", nullable=false)
     * @Serializer\Expose()
     * @Serializer\SerializedName("numDirectActiveTopics")
     */
    private $numDirectActiveTopics = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="num_all_topics", type="integer", nullable=false)
     */
    private $numAllTopics = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="num_all_active_topics", type="integer", nullable=false)
     * @Serializer\Expose()
     * @Serializer\SerializedName("numAllActiveTopics")
     */
    private $numAllActiveTopics = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="level", type="integer", nullable=false)
     * @Serializer\Expose()
     */
    private $level;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    private $active = true;

    /**
     * @var \Category
     *
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $parent;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Video", mappedBy="category")
     */
    private $video;


    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parent", cascade={"all"})
     * @Serializer\Expose()
     */
    private $children;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->video = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set oldId
     *
     * @param integer $oldId
     *
     * @return Category
     */
    public function setOldId($oldId)
    {
        $this->oldId = $oldId;

        return $this;
    }

    /**
     * Get oldId
     *
     * @return integer
     */
    public function getOldId()
    {
        return $this->oldId;
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
        return [ 'plainSlug' ];
    }


    /**
     * Set sortOrder
     *
     * @param integer $sortOrder
     *
     * @return Category
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
     * Set numDirectChildren
     *
     * @param integer $numDirectChildren
     *
     * @return Category
     */
    public function setNumDirectChildren($numDirectChildren)
    {
        $this->numDirectChildren = $numDirectChildren;

        return $this;
    }

    /**
     * Get numDirectChildren
     *
     * @return integer
     */
    public function getNumDirectChildren()
    {
        return $this->numDirectChildren;
    }

    /**
     * Set numAllChildren
     *
     * @param integer $numAllChildren
     *
     * @return Category
     */
    public function setNumAllChildren($numAllChildren)
    {
        $this->numAllChildren = $numAllChildren;

        return $this;
    }

    /**
     * Get numAllChildren
     *
     * @return integer
     */
    public function getNumAllChildren()
    {
        return $this->numAllChildren;
    }

    /**
     * Set numDirectTopics
     *
     * @param integer $numDirectTopics
     *
     * @return Category
     */
    public function setNumDirectTopics($numDirectTopics)
    {
        $this->numDirectTopics = $numDirectTopics;

        return $this;
    }

    /**
     * Get numDirectTopics
     *
     * @return integer
     */
    public function getNumDirectTopics()
    {
        return $this->numDirectTopics;
    }

    /**
     * Set numDirectActiveTopics
     *
     * @param integer $numDirectActiveTopics
     *
     * @return Category
     */
    public function setNumDirectActiveTopics($numDirectActiveTopics)
    {
        $this->numDirectActiveTopics = $numDirectActiveTopics;

        return $this;
    }

    /**
     * Get numDirectActiveTopics
     *
     * @return integer
     */
    public function getNumDirectActiveTopics()
    {
        return $this->numDirectActiveTopics;
    }


    /**
     * Set numAllTopics
     *
     * @param integer $numAllTopics
     *
     * @return Category
     */
    public function setNumAllTopics($numAllTopics)
    {
        $this->numAllTopics = $numAllTopics;

        return $this;
    }

    /**
     * Get numAllTopics
     *
     * @return integer
     */
    public function getNumAllTopics()
    {
        return $this->numAllTopics;
    }

    /**
     * Set numAllActiveTopics
     *
     * @param integer $numAllActiveTopics
     *
     * @return Category
     */
    public function setNumAllActiveTopics($numAllActiveTopics)
    {
        $this->numAllActiveTopics = $numAllActiveTopics;

        return $this;
    }

    /**
     * Get numAllActiveTopics
     *
     * @return integer
     */
    public function getNumAllActiveTopics()
    {
        return $this->numAllActiveTopics;
    }

    /**
     * Set level
     *
     * @param integer $level
     *
     * @return Category
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return integer
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Category
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
     * Set parent
     *
     * @param \ZekrBundle\Entity\Category $parent
     *
     * @return Category
     */
    public function setParent(\ZekrBundle\Entity\Category $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \ZekrBundle\Entity\Category
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add video
     *
     * @param \ZekrBundle\Entity\Video $video
     *
     * @return Category
     */
    public function addVideo(\ZekrBundle\Entity\Video $video)
    {
        $this->video[] = $video;

        return $this;
    }

    /**
     * Remove video
     *
     * @param \ZekrBundle\Entity\Video $video
     */
    public function removeVideo(\ZekrBundle\Entity\Video $video)
    {
        $this->video->removeElement($video);
    }

    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Get video
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVideo()
    {
        return $this->video;
    }


    public function getCalculatedLevel()
    {
        $level = 1;
        $parent = $this->getParent();
        while($parent) {
            $parent = $parent->getParent();
            $level ++;
        }
        return $level;
    }

    /**
     * @ORM\PrePersist()
     */
    public function validateForEm()
    {
        $this->setLevel($this->getCalculatedLevel());
    }


    /**
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("slug")
     */
    public function getSerializedSlug()
    {
        return $this->getSlug();
    }

    /**
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("name")
     */
    public function getSerializedName()
    {
        return $this->translate()->getName();
    }

    /**
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("note")
     */
    public function getSerializedNote()
    {
        return $this->translate()->getNote();
    }

    /**
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("parentId")
     */
    public function getSerializedparentId()
    {
        return ($this->getParent()) ? $this->getParent()->getId() : null ;
    }


    public function __toString() {
        if( $name = $this->translate()->getName() ) {
            return $name;
        }

        return '';
    }
}
