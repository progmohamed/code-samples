<?php

namespace LocaleBundle\Entity;

use CommonBundle\Lib\ImageConverter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Language
 *
 * @ORM\Table(name="locale_language", uniqueConstraints={@ORM\UniqueConstraint(name="inx_locale", columns={"locale"})}, indexes={@ORM\Index(name="inx_name", columns={"name"}), @ORM\Index(name="inx_direction", columns={"direction"}), @ORM\Index(name="inx_switch_front_end", columns={"switch_front_end"}), @ORM\Index(name="inx_translate_content", columns={"translate_content"}), @ORM\Index(name="inx_switch_back_end", columns={"switch_back_end"})})
 * @ORM\Entity(repositoryClass="LocaleBundle\Entity\Repository\LanguageRepository")
 * @UniqueEntity("name")
 * @UniqueEntity("locale")
 * @ORM\HasLifecycleCallbacks
 */
class Language
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
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="locale", type="string", length=2, nullable=false)
     * @Assert\Regex("/[a-z][a-z]/")
     */
    private $locale;

    /**
     * @var string
     *
     * @ORM\Column(name="direction", type="string", length=3, nullable=false)
     */
    private $direction;

    /**
     * @var boolean
     *
     * @ORM\Column(name="switch_front_end", type="boolean", nullable=false)
     */
    private $switchFrontEnd;

    /**
     * @var boolean
     *
     * @ORM\Column(name="switch_back_end", type="boolean", nullable=false)
     */
    private $switchBackEnd;

    /**
     * @var boolean
     *
     * @ORM\Column(name="translate_content", type="boolean", nullable=false)
     */
    private $translateContent;



    /**
     * @var string
     *
     * @ORM\Column(name="photo", type="string", length=200, nullable=true)
     */
    private $photo;
    private $tempPhoto;



    /**
     * @Assert\File(
     *  maxSize="3M",
     *  mimeTypes = {"image/jpg", "image/jpeg", "image/gif", "image/png"}
     * )
     */
    private $photoFile;


    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Dialect", mappedBy="language")
     */
    private $dialect;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->dialect = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return Language
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
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

    /**
     * Get dialect
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDialect()
    {
        return $this->dialect;
    }

    /**
     * Set locale
     *
     * @param string $locale
     *
     * @return Language
     */
    public function setLocale($locale)
    {
        $this->locale = strtolower($locale);

        return $this;
    }

    /**
     * Get locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set direction
     *
     * @param string $direction
     *
     * @return Language
     */
    public function setDirection($direction)
    {
        $this->direction = strtolower($direction);

        return $this;
    }

    /**
     * Get direction
     *
     * @return string
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * Set switchFrontEnd
     *
     * @param boolean $switchFrontEnd
     *
     * @return Language
     */
    public function setSwitchFrontEnd($switchFrontEnd)
    {
        $this->switchFrontEnd = $switchFrontEnd;

        return $this;
    }

    /**
     * Get switchFrontEnd
     *
     * @return boolean
     */
    public function getSwitchFrontEnd()
    {
        return $this->switchFrontEnd;
    }

    /**
     * Set switchBackEnd
     *
     * @param boolean $switchBackEnd
     *
     * @return Language
     */
    public function setSwitchBackEnd($switchBackEnd)
    {
        $this->switchBackEnd = $switchBackEnd;

        return $this;
    }

    /**
     * Get switchBackEnd
     *
     * @return boolean
     */
    public function getSwitchBackEnd()
    {
        return $this->switchBackEnd;
    }

    /**
     * Set translateContent
     *
     * @param boolean $translateContent
     *
     * @return Language
     */
    public function setTranslateContent($translateContent)
    {
        $this->translateContent = $translateContent;

        return $this;
    }

    /**
     * Get translateContent
     *
     * @return boolean
     */
    public function getTranslateContent()
    {
        return $this->translateContent;
    }


    public function getAlign()
    {
        return $this->direction == 'rtl' ? 'right' : 'left';
    }

    public function getOppositeDirection()
    {
        return $this->direction == 'rtl' ? 'ltr' : 'rtl';
    }

    public function getOppositeAlign()
    {
        return $this->direction == 'rtl' ? 'left' : 'right';
    }

    /**
     * Set photo
     *
     * @param string $image
     *
     * @return Language
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
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../web/'.$this->getPhotoUploadDir();
    }

    public function getPhotoUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'upload/langs';
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
    public function upload()
    {
        if (null === $this->getPhotoFile()) {
            return;
        }
        $this->getPhotoFile()->move($this->getPhotoUploadRootDir(), $this->photo);

        $jpgFile = $this->getPhotoAbsolutePath();
        $imageConverter = new ImageConverter();
        $jpgFileContents = $imageConverter->imageStringToJPEG( file_get_contents($jpgFile) );
        $newJpgFileContents = $imageConverter->generateThumbnailFromJPGString($jpgFileContents, 18, 12, true);
        file_put_contents($jpgFile, $newJpgFileContents);

        // check if we have an old image
        if (isset($this->tempPhoto)) {
            // delete the old image
            @unlink($this->getPhotoUploadRootDir().'/'.$this->tempPhoto);
            // clear the temp image path
            $this->tempPhoto = null;
        }
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


    public function __toString()
    {
        return $this->getName();
    }
}
