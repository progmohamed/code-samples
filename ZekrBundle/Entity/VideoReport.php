<?php

namespace ZekrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VideoReport
 *
 * @ORM\Table(name="zekr_video_report", indexes={@ORM\Index(name="inx_video_id", columns={"video_id"}), @ORM\Index(name="inx_reason", columns={"reason"}), @ORM\Index(name="inx_date_time", columns={"date_time"})})
 * @ORM\Entity(repositoryClass="ZekrBundle\Entity\Repository\VideoReport\Repository")
 * @ORM\HasLifecycleCallbacks()
 */
class VideoReport
{

    const CONTRARY_CONTENT = 1;
    const PRIVACY_INFRINGEMENT = 2;
    const COPYRIGHT_INFRINGEMENT = 3;
    const OTHER = 4;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_time", type="datetime", nullable=false)
     */
    private $dateTime;

    /**
     * @var integer
     *
     * @ORM\Column(name="reason", type="integer", nullable=false)
     */
    private $reason = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text", length=65535, nullable=true)
     */
    private $message;

    /**
     * @var \Video
     *
     * @ORM\ManyToOne(targetEntity="Video")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="video_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $video;


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
     * Set dateTime
     *
     * @param \DateTime $dateTime
     *
     * @return VideoReport
     */
    public function setDateTime($dateTime)
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    /**
     * Get dateTime
     *
     * @return \DateTime
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * Set reason
     *
     * @param integer $reason
     *
     * @return VideoReport
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * Get reason
     *
     * @return integer
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Set message
     *
     * @param string $message
     *
     * @return VideoReport
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set video
     *
     * @param \ZekrBundle\Entity\Video $video
     *
     * @return VideoReport
     */
    public function setVideo(\ZekrBundle\Entity\Video $video = null)
    {
        $this->video = $video;

        return $this;
    }

    /**
     * Get video
     *
     * @return \ZekrBundle\Entity\Video
     */
    public function getVideo()
    {
        return $this->video;
    }

    /**
     * @ORM\PrePersist()
     */
    public function beforeInsert()
    {
        if (!$this->getDateTime()) {
            $this->setDateTime(new \DateTime());
        }
    }

}
