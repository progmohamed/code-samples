<?php

namespace ZekrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation as Serializer;

/**
 * StaticPage
 *
 * @ORM\Table(name="zekr_static_page", uniqueConstraints={@ORM\UniqueConstraint(name="inx_plain_slug", columns={"plain_slug"}), @ORM\UniqueConstraint(name="inx_slug", columns={"slug"})})
 * @ORM\Entity(repositoryClass="ZekrBundle\Entity\Repository\StaticPage\Repository")
 * @UniqueEntity("plainSlug")
 * @Serializer\ExclusionPolicy("all")
 */
class StaticPage
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
     * @return StaticPage
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
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("slug")
     */
    public function getSerializedSlug()
    {
        return $this->getSlug();
    }

    /**
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("title")
     */
    public function getSerializedTitle()
    {
        return $this->translate()->getTitle();
    }

    /**
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("content")
     */
    public function getSerializedContent()
    {
        return $this->translate()->getContent();
    }

    public function __toString() {
        return ''.$this->getSlug();
    }


}
