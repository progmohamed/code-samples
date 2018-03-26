<?php

namespace ZekrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation as Serializer;

/**
 * Surah
 *
 * @ORM\Table(name="zekr_surah", uniqueConstraints={@ORM\UniqueConstraint(name="inx_plain_slug", columns={"plain_slug"}), @ORM\UniqueConstraint(name="inx_slug", columns={"slug"})}, indexes={@ORM\Index(name="inx_num_topics", columns={"num_topics"}), @ORM\Index(name="inx_sort_order", columns={"sort_order"}), @ORM\Index(name="inx_num_active_topics", columns={"num_active_topics"})})
 * @ORM\Entity(repositoryClass="ZekrBundle\Entity\Repository\Surah\Repository")
 * @UniqueEntity("plainSlug")
 * @Serializer\ExclusionPolicy("all")
 */
class Surah
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
     * @ORM\Column(name="ayat_sum", type="integer", nullable=false)
     * @Assert\NotBlank()
     * @Serializer\Expose()
     * @Serializer\SerializedName("ayatSum")
     */
    private $ayatSum;


    /**
     * @var \Hizb
     *
     * @ORM\ManyToOne(targetEntity="Hizb")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="first_hizb", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $firstHizb = 1;

    /**
     * @var \Juz
     *
     * @ORM\ManyToOne(targetEntity="Juz")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="first_juz", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $firstJuz = 1;


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
     * @ORM\ManyToMany(targetEntity="Video", mappedBy="surah")
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
     * @return Surah
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
     * Set ayatSum
     *
     * @param integer $ayatSum
     *
     * @return Surah
     */
    public function setAyatSum($ayatSum)
    {
        $this->ayatSum = $ayatSum;

        return $this;
    }

    /**
     * Get ayatSum
     *
     * @return integer
     */
    public function getAyatSum()
    {
        return $this->ayatSum;
    }


    /**
     * Set firstHizb
     *
     * @param \ZekrBundle\Entity\Hizb $firstHizb
     *
     * @return Surah
     */
    public function setFirstHizb(\ZekrBundle\Entity\Hizb $firstHizb = null)
    {
        $this->firstHizb = $firstHizb;

        return $this;
    }

    /**
     * Get firstHizb
     *
     * @return \ZekrBundle\Entity\Hizb
     */
    public function getFirstHizb()
    {
        return $this->firstHizb;
    }


    /**
     * Set firstJuz
     *
     * @param \ZekrBundle\Entity\Juz $firstJuz
     *
     * @return Surah
     */
    public function setFirstJuz(\ZekrBundle\Entity\Juz $firstJuz = null)
    {
        $this->firstJuz = $firstJuz;

        return $this;
    }

    /**
     * Get firstJuz
     *
     * @return \ZekrBundle\Entity\Juz
     */
    public function getFirstJuz()
    {
        return $this->firstJuz;
    }

    /**
     * Set numTopics
     *
     * @param integer $numTopics
     *
     * @return Surah
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
     * @return Surah
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
     * @return Surah
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
