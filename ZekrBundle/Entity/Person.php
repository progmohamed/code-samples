<?php

namespace ZekrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation as Serializer;

/**
 * Person
 *
 * @ORM\Table(name="zekr_person", uniqueConstraints={@ORM\UniqueConstraint(name="inx_plain_slug", columns={"plain_slug"}), @ORM\UniqueConstraint(name="inx_slug", columns={"slug"})}, indexes={@ORM\Index(name="inx_active", columns={"active"}), @ORM\Index(name="inx_num_active_topics", columns={"num_active_topics"})})
 * @ORM\Entity(repositoryClass="ZekrBundle\Entity\Repository\Person\Repository")
 * @UniqueEntity("plainSlug")
 * @ORM\HasLifecycleCallbacks
 * @Serializer\ExclusionPolicy("all")
 */
class Person
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
     * @var string
     *
     * @ORM\Column(name="photo", type="string", length=200, nullable=true)
     * @Serializer\Expose()
     * @Serializer\Groups({"default"})
     */
    private $photo;
    private $tempPhoto;


    /**
     * @Assert\File(
     *  maxSize="3M",
     *  mimeTypes = {"image/jpg", "image/jpeg"}
     * )
     */
    private $photoFile;


    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    private $active = true;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Video", mappedBy="person")
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
     * @return Person
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
     * @return Person
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
     * @return Person
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
     * Set active
     *
     * @param boolean $active
     *
     * @return Person
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
     * @return Person
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
     * Set photo
     *
     * @param string $image
     *
     * @return Person
     */
    public function setPhoto($image)
    {
        $this->photo = $image;
        return $this;
    }

    /**
     * Get photo
     *
     * @return string
     */
    public function getPhoto()
    {
        return $this->photo;
    }



    public function setPhotoFile(UploadedFile $photoFile = null)
    {
        $this->photoFile = $photoFile;
        // check if we have an old image path
        if (isset($this->photo)) {
            // store the old name to delete after the update
            $this->tempPhoto = $this->photo;
            $this->photo = null;
        } else {
            $this->photo = 'initial';
        }
    }

    public function getPhotoFile()
    {
        return $this->photoFile;
    }

    public function getPhotoAbsolutePath()
    {
        return null === $this->photo
            ? null
            : $this->getPhotoUploadRootDir().'/'.$this->photo;
    }

    public function getPhotoWebPath()
    {
        return $this->getPhotoUploadDir().'/'.$this->getPhoto();
    }

    public function getDefaultPhotoWebPath()
    {
        return $this->getPhotoUploadDir().'/'.$this->getDefaultPhoto();
    }

    public function getPhotoUploadRootDir()
    {
        return __DIR__.'/../../../web/'.$this->getPhotoUploadDir();
    }

    public function getPhotoUploadDir()
    {
        return 'upload/person';
    }


    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->getPhotoFile()) {
            // do whatever you want to generate a unique name
            $filename = sha1(uniqid(mt_rand(), true));
            $this->photo = $filename.'.jpg';
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload() {
        if (null === $this->getPhotoFile()) {
            return;
        }
        $this->getPhotoFile()->move($this->getPhotoUploadRootDir(), $this->photo);
        $this->photoFile = null;
    }


    public function deleteCurrentPhoto()
    {
        $fullPath = $this->getPhotoAbsolutePath();
        $this->setPhoto(null);
        if($fullPath) {
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeImageFile()
    {
        $this->deleteCurrentPhoto();
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


    public function __toString() {
        if( $name = $this->translate()->getName() ) {
            return $name;
        }

        return '';
    }


}
