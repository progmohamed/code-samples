<?php

namespace ZekrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation as Serializer;

/**
 * Juz
 *
 * @ORM\Table(name="zekr_juz", uniqueConstraints={@ORM\UniqueConstraint(name="inx_plain_slug", columns={"plain_slug"}), @ORM\UniqueConstraint(name="inx_slug", columns={"slug"})}, indexes={@ORM\Index(name="inx_num_topics", columns={"num_topics"}), @ORM\Index(name="inx_sort_order", columns={"sort_order"}), @ORM\Index(name="inx_num_active_topics", columns={"num_active_topics"})})
 * @ORM\Entity(repositoryClass="ZekrBundle\Entity\Repository\Juz\Repository")
 * @UniqueEntity("plainSlug")
 * @Serializer\ExclusionPolicy("all")
 */
class Juz
{
    use ORMBehaviors\Translatable\Translatable;
    use ORMBehaviors\Sluggable\Sluggable;

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
     * @ORM\Column(name="num_topics", type="integer", nullable=false)
     */
    private $numTopics = 0;


    /**
     * @var integer
     *
     * @ORM\Column(name="num_active_topics", type="integer", nullable=false)
     * @Serializer\Expose()
     * @Serializer\SerializedName("numActiveTopics")
     */
    private $numActiveTopics = 0;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Video", mappedBy="juz")
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
     * @return Juz
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
     * Set numTopics
     *
     * @param integer $numTopics
     *
     * @return Juz
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
     * @return Juz
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
     * Add video
     *
     * @param \ZekrBundle\Entity\Video $video
     *
     * @return Juz
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

    public function __toString() {
        if( $name = $this->translate()->getName() ) {
            return $name;
        }

        return '';
    }
}
