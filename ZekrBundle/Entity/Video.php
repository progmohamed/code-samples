<?php

namespace ZekrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation as Serializer;

/**
 * Video
 *
 * @ORM\Table(name="zekr_video", uniqueConstraints={@ORM\UniqueConstraint(name="inx_plain_slug", columns={"plain_slug"}), @ORM\UniqueConstraint(name="inx_slug", columns={"slug"})}, indexes={@ORM\Index(name="inx_hd_file", columns={"hd_file"}),@ORM\Index(name="inx_mp3_file", columns={"mp3_file"}), @ORM\Index(name="inx_mp3_file_size", columns={"mp3_file_size"}), @ORM\Index(name="inx_hd_file_size", columns={"hd_file_size"}), @ORM\Index(name="inx_sd_file", columns={"sd_file"}), @ORM\Index(name="inx_sd_file_size", columns={"sd_file_size"}), @ORM\Index(name="inx_duration", columns={"duration"}), @ORM\Index(name="inx_rewaya_id", columns={"rewaya_id"}), @ORM\Index(name="inx_downloads", columns={"downloads"}), @ORM\Index(name="inx_views", columns={"views"}), @ORM\Index(name="inx_selected", columns={"selected"}), @ORM\Index(name="inx_deleted_at", columns={"deleted_at"}), @ORM\Index(name="inx_active", columns={"active"})})
 * @ORM\Entity(repositoryClass="ZekrBundle\Entity\Repository\Video\Repository")
 * @ORM\HasLifecycleCallbacks()
 * @Serializer\ExclusionPolicy("all")
 */
class Video
{
    use ORMBehaviors\Translatable\Translatable;
    use ORMBehaviors\Sluggable\Sluggable;
    use ORMBehaviors\SoftDeletable\SoftDeletable;

    const STATUS_WAITING = 1;
    const STATUS_IN_PROGRESS = 2;
    const STATUS_DONE = 3;
    const STATUS_FILES_MISSING = 4;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Serializer\Expose()
     * @Serializer\Groups({"videoList"})
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
     * @var string
     *
     * @ORM\Column(name="mp3_file", type="string", length=200, nullable=true)
     * @Serializer\Expose()
     * @Serializer\SerializedName("mp3File")
     */
    private $mp3File;


    /**
     * @var string
     *
     * @ORM\Column(name="mp3_file_size", type="integer", nullable=true)
     * @Serializer\Expose()
     * @Serializer\SerializedName("mp3FileSize")
     */
    private $mp3FileSize;


    /**
     * @var string
     *
     * @ORM\Column(name="hd_file", type="string", length=200, nullable=true)
     * @Serializer\Expose()
     * @Serializer\SerializedName("hdFile")
     */
    private $hdFile;

    /**
     * @var string
     *
     * @ORM\Column(name="hd_file_size", type="integer", nullable=true)
     * @Serializer\Expose()
     * @Serializer\SerializedName("hdFileSize")
     */
    private $hdFileSize;

    /**
     * @var string
     *
     * @ORM\Column(name="sd_file", type="string", length=200, nullable=true)
     * @Serializer\Expose()
     * @Serializer\SerializedName("sdFile")
     */
    private $sdFile;

    /**
     * @var string
     *
     * @ORM\Column(name="sd_file_size", type="integer", nullable=true)
     * @Serializer\Expose()
     * @Serializer\SerializedName("sdFileSize")
     */
    private $sdFileSize;

    /**
     * @var string
     *
     * @ORM\Column(name="thumbnail_file", type="string", length=255, nullable=true)
     * @Serializer\Expose()
     * @Serializer\SerializedName("thumbnailFile")
     * @Serializer\Groups({"videoList"})
     */
    private $thumbnailFile;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    private $active = false;


    /**
     * @var boolean
     *
     * @ORM\Column(name="conversion", type="boolean", nullable=false)
     */
    private $conversion = true;

    /**
     * @var integer
     *
     * @ORM\Column(name="conversion_status", type="integer", nullable=false)
     */
    private $conversionStatus = 1;

    /**
     * @var integer
     *
     * @ORM\Column(name="duration", type="integer", nullable=true)
     * @Serializer\Expose()
     * @Serializer\Groups({"videoList"})
     */
    private $duration = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="views", type="integer", nullable=true)
     * @Serializer\Expose()
     * @Serializer\Groups({"videoList"})
     */
    private $views = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="thumbnail_time", type="integer", nullable=true)
     */
    private $thumbnailTime = 1;

    /**
     * @var integer
     *
     * @ORM\Column(name="downloads", type="integer", nullable=true)
     * @Serializer\Expose()
     * @Serializer\Groups({"videoList"})
     */
    private $downloads = 0;


    /**
     * @var boolean
     *
     * @ORM\Column(name="selected", type="boolean", nullable=false)
     * @Serializer\Expose()
     */
    private $selected = false;


    /**
     * @var \TempVideo
     *
     * @ORM\OneToOne(targetEntity="TempVideo")
     * @ORM\JoinColumn(name="temp_video_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $tempVideo;


    /**
     * @var string
     *
     * @ORM\Column(name="temp_video_file", type="string", length=200, nullable=true)
     */
    private $tempVideoFile;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="inserted_at", type="datetime", nullable=false)
     * @Serializer\Expose()
     * @Serializer\SerializedName("insertedAt")
     * @Serializer\Groups({"videoList"})
     */
    private $insertedAt;


    /**
     * @var \Rewaya
     *
     * @ORM\ManyToOne(targetEntity="Rewaya")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="rewaya_id", referencedColumnName="id", onDelete="SET NULL")
     * })
     */
    private $rewaya;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Category", inversedBy="video")
     * @ORM\JoinTable(name="zekr_video_category",
     *   joinColumns={
     *     @ORM\JoinColumn(name="video_id", referencedColumnName="id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="category_id", referencedColumnName="id", onDelete="CASCADE")
     *   }
     * )
     */
    private $category;


    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Hizb", inversedBy="video")
     * @ORM\JoinTable(name="zekr_video_hizb",
     *   joinColumns={
     *     @ORM\JoinColumn(name="video_id", referencedColumnName="id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="hizb_id", referencedColumnName="id", onDelete="CASCADE")
     *   }
     * )
     */
    private $hizb;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Juz", inversedBy="video")
     * @ORM\JoinTable(name="zekr_video_juz",
     *   joinColumns={
     *     @ORM\JoinColumn(name="video_id", referencedColumnName="id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="juz_id", referencedColumnName="id", onDelete="CASCADE")
     *   }
     * )
     */
    private $juz;


    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Person", inversedBy="video")
     * @ORM\JoinTable(name="zekr_video_person",
     *   joinColumns={
     *     @ORM\JoinColumn(name="video_id", referencedColumnName="id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="person_id", referencedColumnName="id", onDelete="CASCADE")
     *   }
     * )
     */
    private $person;


    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="VideoType", inversedBy="video")
     * @ORM\JoinTable(name="zekr_video_video_type",
     *   joinColumns={
     *     @ORM\JoinColumn(name="video_id", referencedColumnName="id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="video_type_id", referencedColumnName="id", onDelete="CASCADE")
     *   }
     * )
     */
    private $videoType;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Surah", inversedBy="video")
     * @ORM\JoinTable(name="zekr_video_surah",
     *   joinColumns={
     *     @ORM\JoinColumn(name="video_id", referencedColumnName="id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="surah_id", referencedColumnName="id", onDelete="CASCADE")
     *   }
     * )
     */
    private $surah;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="ZekrBundle\Entity\VideoCollection", mappedBy="video")
     */
    private $videoCollections;


    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="ApiProject", inversedBy="video")
     * @ORM\JoinTable(name="zekr_video_api_project",
     *   joinColumns={
     *     @ORM\JoinColumn(name="video_id", referencedColumnName="id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="project_id", referencedColumnName="id", onDelete="CASCADE")
     *   }
     * )
     */
    private $apiProject;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->category = new ArrayCollection();
        $this->hizb = new ArrayCollection();
        $this->juz = new ArrayCollection();
        $this->person = new ArrayCollection();
        $this->videoType = new ArrayCollection();
        $this->surah = new ArrayCollection();
        $this->videoCollections = new ArrayCollection();
        $this->apiProject = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Video
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
     * Set hdFile
     *
     * @param string $hdFile
     *
     * @return Video
     */
    public function setHdFile($hdFile)
    {
        $this->hdFile = $hdFile;

        return $this;
    }

    /**
     * Get hdFile
     *
     * @return string
     */
    public function getHdFile()
    {
        return $this->hdFile;
    }

    /**
     * Set mp3File
     *
     * @param string $mp3File
     *
     * @return Video
     */
    public function setMp3File($mp3File)
    {
        $this->mp3File = $mp3File;

        return $this;
    }

    /**
     * Get mp3File
     *
     * @return string
     */
    public function getMp3File()
    {
        return $this->mp3File;
    }


    /**
     * Set mp3FileSize
     *
     * @param integer $mp3FileSize
     *
     * @return Video
     */
    public function setMp3FileSize($mp3FileSize)
    {
        $this->mp3FileSize = $mp3FileSize;

        return $this;
    }

    /**
     * Get mp3FileSize
     *
     * @return integer
     */
    public function getMp3FileSize()
    {
        return $this->mp3FileSize;
    }


    /**
     * Set tempVideoFile
     *
     * @param string $tempVideoFile
     *
     * @return Video
     */
    public function setTempVideoFile($tempVideoFile)
    {
        $this->tempVideoFile = $tempVideoFile;

        return $this;
    }

    /**
     * Get tempVideoFile
     *
     * @return string
     */
    public function getTempVideoFile()
    {
        return $this->tempVideoFile;
    }

    /**
     * Set hdFileSize
     *
     * @param integer $hdFileSize
     *
     * @return Video
     */
    public function setHdFileSize($hdFileSize)
    {
        $this->hdFileSize = $hdFileSize;

        return $this;
    }

    /**
     * Get hdFileSize
     *
     * @return integer
     */
    public function getHdFileSize()
    {
        return $this->hdFileSize;
    }

    /**
     * Set sdFile
     *
     * @param string $sdFile
     *
     * @return Video
     */
    public function setSdFile($sdFile)
    {
        $this->sdFile = $sdFile;

        return $this;
    }

    /**
     * Get sdFile
     *
     * @return string
     */
    public function getSdFile()
    {
        return $this->sdFile;
    }

    /**
     * Set sdFileSize
     *
     * @param integer $sdFileSize
     *
     * @return Video
     */
    public function setSdFileSize($sdFileSize)
    {
        $this->sdFileSize = $sdFileSize;

        return $this;
    }

    /**
     * Get sdFileSize
     *
     * @return integer
     */
    public function getSdFileSize()
    {
        return $this->sdFileSize;
    }

    /**
     * Set thumbnailFile
     *
     * @param string $thumbnailFile
     *
     * @return Video
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
     * Set active
     *
     * @param boolean $active
     *
     * @return Video
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


    public function shouldBeActive()
    {
        $all = [
            $this->active,
            $this->getConversionStatus() == self::STATUS_DONE ? true : false
        ];

        if ($this->getRewaya()) {
            $all[] = $this->getRewaya()->getActive();
        }

        $flag = true;
        /** @var Category $category */
        foreach ($this->getCategory() as $category) {
            if (!$category->isDeleted() && !$category->getActive()) {
                $flag = false;
                break;
            }
        }
        $all[] = $flag;

        $flag = true;
        /** @var Person $person */
        foreach ($this->getPerson() as $person) {
            if (!$person->getActive()) {
                $flag = false;
                break;
            }
        }
        $all[] = $flag;

        $flag = true;
        /** @var VideoType $videoType */
        foreach ($this->getVideoType() as $videoType) {
            if (!$videoType->getActive()) {
                $flag = false;
                break;
            }
        }
        $all[] = $flag;

        $flag = true;
        /** @var VideoCollection $videoCollection */
        foreach ($this->getVideoCollections() as $videoCollection) {
            if (!$videoCollection->getCollection()->getActive()) {
                $flag = false;
                break;
            }
        }
        $all[] = $flag;

        return !in_array(false, $all);
    }


    /**
     * Set conversion
     *
     * @param boolean $conversion
     *
     * @return Video
     */
    public function setConversion($conversion)
    {
        $this->conversion = $conversion;

        return $this;
    }

    /**
     * Get conversion
     *
     * @return boolean
     */
    public function getConversion()
    {
        return $this->conversion;
    }

    /**
     * Set duration
     *
     * @param integer $duration
     *
     * @return Video
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return integer
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set conversionStatus
     *
     * @param integer $conversionStatus
     *
     * @return Video
     */
    public function setConversionStatus($conversionStatus)
    {
        $this->conversionStatus = $conversionStatus;

        return $this;
    }

    /**
     * Get conversionStatus
     *
     * @return integer
     */
    public function getConversionStatus()
    {
        return $this->conversionStatus;
    }

    /**
     * Set views
     *
     * @param integer $views
     *
     * @return Video
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
     * Set thumbnailTime
     *
     * @param integer $thumbnailTime
     *
     * @return Video
     */
    public function setThumbnailTime($thumbnailTime)
    {
        $this->thumbnailTime = $thumbnailTime;

        return $this;
    }

    /**
     * Get thumbnailTime
     *
     * @return integer
     */
    public function getThumbnailTime()
    {
        return $this->thumbnailTime;
    }

    /**
     * Set oldId
     *
     * @param integer $oldId
     *
     * @return Video
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
     * Set downloads
     *
     * @param integer $downloads
     *
     * @return Video
     */
    public function setDownloads($downloads)
    {
        $this->downloads = $downloads;

        return $this;
    }

    /**
     * Get downloads
     *
     * @return integer
     */
    public function getDownloads()
    {
        return $this->downloads;
    }

    /**
     * Set selected
     *
     * @param boolean $selected
     *
     * @return Video
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
     * Set rewaya
     *
     * @param \ZekrBundle\Entity\Rewaya $rewaya
     *
     * @return Video
     */
    public function setRewaya(\ZekrBundle\Entity\Rewaya $rewaya = null)
    {
        $this->rewaya = $rewaya;

        return $this;
    }

    /**
     * Get rewaya
     *
     * @return \ZekrBundle\Entity\Rewaya
     */
    public function getRewaya()
    {
        return $this->rewaya;
    }

    /**
     * Set tempFile
     *
     * @param \ZekrBundle\Entity\TempVideo $tempVideo
     *
     * @return Video
     */
    public function setTempVideo(\ZekrBundle\Entity\TempVideo $tempVideo = null)
    {
        $this->tempVideo = $tempVideo;

        return $this;
    }

    /**
     * Get tempVideo
     *
     * @return \ZekrBundle\Entity\TempVideo
     */
    public function getTempVideo()
    {
        return $this->tempVideo;
    }

    /**
     * Add category
     *
     * @param \ZekrBundle\Entity\Category $category
     *
     * @return Video
     */
    public function addCategory(\ZekrBundle\Entity\Category $category)
    {

        if (!$this->category->contains($category)) {
            $this->category->add($category);
        }

        return $this;
    }

    /**
     * Remove category
     *
     * @param \ZekrBundle\Entity\Category $category
     */
    public function removeCategory(\ZekrBundle\Entity\Category $category)
    {
        $this->category->removeElement($category);
    }

    /**
     * Get category
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategory()
    {
        return $this->category;
    }


    /**
     * Add hizb
     *
     * @param \ZekrBundle\Entity\Hizb $hizb
     *
     * @return Video
     */
    public function addHizb(\ZekrBundle\Entity\Hizb $hizb)
    {
        $this->hizb[] = $hizb;

        return $this;
    }

    /**
     * Remove hizb
     *
     * @param \ZekrBundle\Entity\Hizb $hizb
     */
    public function removeHizb(\ZekrBundle\Entity\Hizb $hizb)
    {
        $this->hizb->removeElement($hizb);
    }

    /**
     * Get hizb
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHizb()
    {
        return $this->hizb;
    }

    /**
     * Add juz
     *
     * @param \ZekrBundle\Entity\Juz $juz
     *
     * @return Video
     */
    public function addJuz(\ZekrBundle\Entity\Juz $juz)
    {
        $this->juz[] = $juz;

        return $this;
    }

    /**
     * Remove juz
     *
     * @param \ZekrBundle\Entity\Juz $juz
     */
    public function removeJuz(\ZekrBundle\Entity\Juz $juz)
    {
        $this->juz->removeElement($juz);
    }

    /**
     * Get juz
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getJuz()
    {
        return $this->juz;
    }


    /**
     * Add person
     *
     * @param \ZekrBundle\Entity\Person $person
     *
     * @return Video
     */
    public function addPerson(\ZekrBundle\Entity\Person $person)
    {
        $this->person[] = $person;

        return $this;
    }

    /**
     * Remove person
     *
     * @param \ZekrBundle\Entity\Person $person
     */
    public function removePerson(\ZekrBundle\Entity\Person $person)
    {
        $this->person->removeElement($person);
    }

    /**
     * Get person
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Add videoType
     *
     * @param \ZekrBundle\Entity\VideoType $videoType
     *
     * @return Video
     */
    public function addVideoType(\ZekrBundle\Entity\VideoType $videoType)
    {
        $this->videoType[] = $videoType;

        return $this;
    }

    /**
     * Remove videoType
     *
     * @param \ZekrBundle\Entity\VideoType $videoType
     */
    public function removeVideoType(\ZekrBundle\Entity\VideoType $videoType)
    {
        $this->videoType->removeElement($videoType);
    }

    /**
     * Get videoType
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVideoType()
    {
        return $this->videoType;
    }


    /**
     * Add apiProject
     *
     * @param \ZekrBundle\Entity\ApiProject $apiProject
     *
     * @return Video
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
     * Add surah
     *
     * @param \ZekrBundle\Entity\Surah $surah
     *
     * @return Video
     */
    public function addSurah(\ZekrBundle\Entity\Surah $surah)
    {
        $this->surah[] = $surah;

        return $this;
    }

    /**
     * Remove surah
     *
     * @param \ZekrBundle\Entity\Surah $surah
     */
    public function removeSurah(\ZekrBundle\Entity\Surah $surah)
    {
        $this->surah->removeElement($surah);
    }

    /**
     * Get surah
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSurah()
    {
        return $this->surah;
    }


    public function getTempVideoFileAbsolutePath()
    {
        return __DIR__ . '/../../../web/upload/video/' . $this->getTempVideoFile();
    }

    public function getMp3FileAbsolutePath()
    {
        return __DIR__ . '/../../../web/upload/mp3/' . $this->getMp3File();
    }

    public function getHdFileAbsolutePath()
    {
        return __DIR__ . '/../../../web/upload/video/' . $this->getHdFile();
    }

    public function getsdFileAbsolutePath()
    {
        return __DIR__ . '/../../../web/upload/video/' . $this->getSdFile();
    }

    public function getThumbFilUploadRootDir()
    {
        return __DIR__ . '/../../../web/upload/video/video_thumbnail/';
    }

    public function getThumbFileAbsolutePath()
    {
        return __DIR__ . '/../../../web/upload/video/video_thumbnail/' . $this->getThumbnailFile();
    }

    public function deleteTempVideoFile()
    {

        if (file_exists($this->getTempVideoFileAbsolutePath())) {
            @unlink($this->getTempVideoFileAbsolutePath());
        }
        $this->setTempVideoFile(null);
    }

    public function deleteMp3File()
    {
        if (file_exists($this->getMp3FileAbsolutePath())) {
            @unlink($this->getMp3FileAbsolutePath());
        }
    }

    public function deleteHdFile()
    {
        if (file_exists($this->getHdFileAbsolutePath())) {
            @unlink($this->getHdFileAbsolutePath());
        }
    }

    public function deleteSdFile()
    {
        if (file_exists($this->getsdFileAbsolutePath())) {
            @unlink($this->getsdFileAbsolutePath());
        }
    }

    public function deleteThembFile()
    {
        if (file_exists($this->getThumbFileAbsolutePath())) {
            @unlink($this->getThumbFileAbsolutePath());
        }
    }

    /**
     * Set insertedAt
     *
     * @param \DateTime $insertedAt
     *
     * @return Video
     */
    public function setInsertedAt($insertedAt)
    {
        $this->insertedAt = $insertedAt;

        return $this;
    }

    /**
     * Get insertedAt
     *
     * @return \DateTime
     */
    public function getInsertedAt()
    {
        return $this->insertedAt;
    }

    public function getVideoCollections()
    {
        return $this->videoCollections;
    }

    /**
     * @ORM\PrePersist()
     */
    public function beforeInsert()
    {
        if (!$this->getInsertedAt()) {
            $this->setInsertedAt(new \DateTime());
        }
    }

    /**
     * @ORM\PostRemove()
     */
    public function afterRemove()
    {
        $this->deleteMp3File();
        $this->deleteHdFile();
        $this->deleteSdFile();
        $this->deleteThembFile();
    }


    /**
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("slug")
     * @Serializer\Groups({"videoList"})
     */
    public function getSerializedSlug()
    {
        return $this->getSlug();
    }

    /**
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("title")
     * @Serializer\Groups({"videoList"})
     */
    public function getSerializedTitle()
    {
        return $this->translate()->getTitle();
    }

    /**
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("description")
     */
    public function getSerializedDescription()
    {
        return $this->translate()->getDescription();
    }

    /**
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("rewaya")
     */
    public function getSerializedRewaya()
    {
        return $this->getRewaya()->getId();
    }

    /**
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("hizb")
     */
    public function getSerializedHizb()
    {
        $out = [];
        foreach ($this->getHizb() as $hizb) {
            $out[] = $hizb->getId();
        }

        return $out;
    }

    /**
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("juz")
     */
    public function getSerializedJuz()
    {
        $out = [];

        foreach ($this->getJuz() as $juz) {
            $out[] = $juz->getId();
        }

        return $out;
    }

    /**
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("surah")
     */
    public function getSerializedSurah()
    {
        $out = [];
        foreach ($this->getSurah() as $surah) {
            $out[] = $surah->getId();
        }

        return $out;
    }

    /**
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("person")
     */
    public function getSerializedPerson()
    {
        $out = [];

        foreach ($this->getPerson() as $person) {
            if($person->getActive()){
                $out[] = $person->getId();
            }
        }

        return $out;
    }

    /**
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("videoType")
     */
    public function getSerializedVideoType()
    {
        $out = [];
        foreach ($this->getVideoType() as $videoType) {
            if($videoType->getActive()){
                $out[] = $videoType->getId();
            }
        }

        return $out;
    }

    /**
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("categories")
     */
    public function getSerializedCategories()
    {
        $out = [];
        foreach ($this->getCategory() as $category) {
            if (!$category->isDeleted() && $category->getActive()) {
                $out[] = $category->getId();
            }
        }

        return $out;
    }



    public function __toString()
    {
        if ($title = $this->translate()->getTitle()) {
            return $title;
        }

        return '';
    }
}