<?php

namespace ZekrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation as Serializer;

/**
 * VideoType
 *
 * @ORM\Table(name="zekr_video_type", uniqueConstraints={@ORM\UniqueConstraint(name="inx_plain_slug", columns={"plain_slug"}), @ORM\UniqueConstraint(name="inx_slug", columns={"slug"})}, indexes={@ORM\Index(name="inx_active", columns={"active"}), @ORM\Index(name="inx_num_active_topics", columns={"num_active_topics"})})
 * @ORM\Entity(repositoryClass="ZekrBundle\Entity\Repository\VideoType\Repository")
 * @UniqueEntity("plainSlug")
 * @Serializer\ExclusionPolicy("all")
 */
class VideoType
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
     * @ORM\Column(name="views", type="integer", nullable=true)
     * @Serializer\Expose()
     * @Serializer\Groups({"default"})
     */
    private $views = 0;

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
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    private $active = true;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Video", mappedBy="videoType", fetch="EXTRA_LAZY")
     */
    private $video;


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
     * @return VideoType
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
     * @return VideoType
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
     * Set numTopics
     *
     * @param integer $numTopics
     *
     * @return VideoType
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
     * @return VideoType
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
     * Set views
     *
     * @param integer $views
     *
     * @return VideoType
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
     * Set active
     *
     * @param boolean $active
     *
     * @return VideoType
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
     * Add video
     *
     * @param \ZekrBundle\Entity\Video $video
     *
     * @return VideoType
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

    /**
     * Get video
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVideo()
    {
        return $this->video;
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
