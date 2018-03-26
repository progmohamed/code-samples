<?php

namespace AdminBundle\Entity;

use CommonBundle\Lib\ImageConverter;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use AdminBundle\Classes\Sex;

/**
 * @ORM\Table(name="admin_user", uniqueConstraints={@ORM\UniqueConstraint(name="inx_username_canonical", columns={"username_canonical"}), @ORM\UniqueConstraint(name="inx_email_canonical", columns={"email_canonical"})}, indexes={@ORM\Index(name="inx_confirmation_token", columns={"confirmation_token"}), @ORM\Index(name="inx_enabled", columns={"enabled"}), @ORM\Index(name="inx_registration_date_time", columns={"registration_date_time"}), @ORM\Index(name="inx_is_admin", columns={"is_admin"}), @ORM\Index(name="inx_real_name", columns={"real_name"}), @ORM\Index(name="inx_sex", columns={"sex"}), @ORM\Index(name="inx_date_of_birth", columns={"date_of_birth"}), @ORM\Index(name="inx_phone_number", columns={"phone_number"}), @ORM\Index(name="inx_country_id", columns={"country_id"})})
 * @ORM\Entity(repositoryClass="AdminBundle\Entity\Repository\User\Repository")
 * @UniqueEntity(fields="email", groups={"UserManagement"})
 * @UniqueEntity(fields="username", groups={"UserManagement"})
 * @ORM\HasLifecycleCallbacks()
 */
class User extends BaseUser
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="real_name", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private $realName;


    /**
     * @var integer
     *
     * @ORM\Column(name="country_id", type="integer", nullable=true)
     */
    private $countryId;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="registration_date_time", type="datetime", nullable=false)
     */
    private $registrationDateTime;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_admin", type="boolean", nullable=false)
     */
    private $isAdmin = false;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_of_birth", type="date", nullable=false)
     * @Assert\NotBlank()
     */
    private $dateOfBirth;

    /**
     * @var integer
     *
     * @ORM\Column(name="sex", type="integer", nullable=false)
     * @Assert\NotBlank()
     */
    private $sex;

    /**
     * @var string
     *
     * @ORM\Column(name="phone_number", type="string", length=50, nullable=false)
     * @Assert\NotBlank()
     */
    private $phoneNumber;


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
     *  mimeTypes = {"image/jpg", "image/jpeg", "image/gif", "image/png"},
     *  groups={"Registration","Profile","UserManagement"}
     * )
     */
    private $photoFile;

    /**
     * @var string
     *
     * @ORM\Column(name="personal_website", type="text", length=65535, nullable=true)
     * @Assert\Url()
     */
    private $personalWebsite;

    /**
     * @var string
     *
     * @ORM\Column(name="resume", type="text", length=65535, nullable=true)
     */
    private $resume;



    /**
     * @ORM\ManyToMany(targetEntity="Group")
     * @ORM\JoinTable(name="admin_user_user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     * @ORM\OrderBy({"name" = "ASC"})
     */
    protected $groups;


    /**
     * Set dateOfBirth
     *
     * @param \DateTime $dateOfBirth
     *
     * @return User
     */
    public function setDateOfBirth($dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    /**
     * Get dateOfBirth
     *
     * @return \DateTime
     */
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    /**
     * Set sex
     *
     * @param integer $sex
     *
     * @return User
     */
    public function setSex($sex)
    {
        $this->sex = $sex;

        return $this;
    }

    /**
     * Get sex
     *
     * @return integer
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * Set phoneNumber
     *
     * @param string $phoneNumber
     *
     * @return User
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * Get phoneNumber
     *
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Set realName
     *
     * @param string $realName
     *
     * @return User
     */
    public function setRealName($realName)
    {
        $this->realName = $realName;

        return $this;
    }

    /**
     * Get realName
     *
     * @return string
     */
    public function getRealName()
    {
        return $this->realName;
    }


    /**
     * Set registrationDateTime
     *
     * @param \DateTime $registrationDateTime
     *
     * @return User
     */
    public function setRegistrationDateTime($registrationDateTime)
    {
        $this->registrationDateTime = $registrationDateTime;

        return $this;
    }

    /**
     * Get registrationDateTime
     *
     * @return \DateTime
     */
    public function getRegistrationDateTime()
    {
        return $this->registrationDateTime;
    }

    /**
     * Set isAdmin
     *
     * @param boolean $isAdmin
     *
     * @return User
     */
    public function setIsAdmin($isAdmin)
    {
        $this->isAdmin = $isAdmin;

        return $this;
    }

    /**
     * Get isAdmin
     *
     * @return boolean
     */
    public function getIsAdmin()
    {
        return $this->isAdmin;
    }

//    public function getRoles()
//    {
//        $roles = array(static::ROLE_DEFAULT);
//        if($this->getIsAdmin()) {
//            $roles[] = 'ROLE_ADMIN';
//        }
//        return array_unique($roles);
//    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Category
     */
    public function setPhoto($image)
    {
        $this->photo = $image;
        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * Set personalWebsite
     *
     * @param string $personalWebsite
     *
     * @return AdminUser
     */
    public function setPersonalWebsite($personalWebsite)
    {
        $this->personalWebsite = $personalWebsite;

        return $this;
    }

    /**
     * Get personalWebsite
     *
     * @return string
     */
    public function getPersonalWebsite()
    {
        return $this->personalWebsite;
    }

    /**
     * Set resume
     *
     * @param string $resume
     *
     * @return AdminUser
     */
    public function setResume($resume)
    {
        $this->resume = $resume;

        return $this;
    }

    /**
     * Get resume
     *
     * @return string
     */
    public function getResume()
    {
        return $this->resume;
    }


    /**
     * @return int
     */
    public function getCountryId()
    {
        return $this->countryId;
    }

    /**
     * @param int $countryId
     */
    public function setCountryId($countryId)
    {
        $this->countryId = $countryId;
    }





    public function getTitleAndRealName()
    {
        $str = '';
        $str.= $this->getRealName();
        return $str;
    }
    

    /**
     * @ORM\PrePersist()
     */
    public function setUserRegistrationDateTime()
    {
        $this->setRegistrationDateTime(new \DateTime());
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function validateBeforeSave()
    {
        $sex = new Sex();
        if(! in_array($this->getSex(), $sex->getSexes())) {
            throw new \Exception('Invalid Sex value');
        }
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
        return 'upload/admin/user';
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
        $newJpgFileContents = $imageConverter->generateThumbnailFromJPGString($jpgFileContents, 150, 150);
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

    private function imageStringToJPEG($imageString)
    {
        try{
            $im = imagecreatefromstring($imageString);
            if($im !== false) {
                ob_start();
                imagejpeg($im);
                $jpg = ob_get_contents();
                ob_end_clean();
                return $jpg;
            }else{
                return false;
            }
        }catch(\Exception $e) {
            return false;
        }
    }

    public function __toString()
    {
        return $this->getTitleAndRealName();
    }

}