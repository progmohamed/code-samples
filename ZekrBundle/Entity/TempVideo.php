<?php

namespace ZekrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * TempVideo
 *
 * @ORM\Table(name="zekr_temp_video")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class TempVideo
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
     * @ORM\Column(name="file", type="string", length=200, nullable=true)
     */
    private $file;


    /**
     * @var string
     *
     * @ORM\Column(name="uuid", type="string", length=200, nullable=true)
     */
    private $uuid;

    /**
     * @var string
     *
     * @ORM\Column(name="original_name", type="string", length=200, nullable=true)
     */
    private $originalName;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", length=200, nullable=true)
     */
    private $status;


    /**
     * @var integer
     *
     * @ORM\Column(name="duration", type="integer", nullable=true)
     */
    private $duration = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="inserted_at", type="datetime", nullable=false)
     */
    private $insertedAt;

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
     * Set file
     *
     * @param string $file
     *
     * @return TempVideo
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set uuid
     *
     * @param string $uuid
     *
     * @return TempVideo
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Get uuid
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set originalName
     *
     * @param string $originalName
     *
     * @return TempVideo
     */
    public function setOriginalName($originalName)
    {
        $this->originalName = $originalName;

        return $this;
    }

    /**
     * Get originalName
     *
     * @return string
     */
    public function getOriginalName()
    {
        return $this->originalName;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return TempVideo
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }


    public function getFileUploadDir()
    {
        return 'upload/video';
    }

    public function getFileUploadRootDir()
    {
        return __DIR__ . '/../../../web/' . $this->getFileUploadDir();
    }


    public function getFileAbsolutePath()
    {
        return null === $this->getFile()
            ? null
            : $this->getFileUploadRootDir() . '/' . $this->getFile();
    }

    public function deleteCurrentFile()
    {
        $fileName = $this->getFile();
        if ($fileName) {
            $this->setFile(null);
            $fullPath = $this->getFileUploadRootDir().'/'.$fileName;
            if (file_exists($fullPath)) {
                @unlink($fullPath);
            }
        }
    }

    /**
     * Set duration
     *
     * @param integer $duration
     *
     * @return TempVideo
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
     * Set insertedAt
     *
     * @param \DateTime $insertedAt
     *
     * @return TempVideo
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


    /**
     * @ORM\PrePersist()
     */
    public function beforeInsert()
    {
        if (!$this->getInsertedAt()) {
            $this->setInsertedAt(new \DateTime());
        }
    }

    public function __toString()
    {
        return '' . $this->getFile();
    }

}

