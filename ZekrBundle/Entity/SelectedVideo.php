<?php

namespace ZekrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * SelectedVideo
 *
 * @ORM\Table(name="zekr_selected_video", uniqueConstraints={@ORM\UniqueConstraint(name="inx_video", columns={"video_id"})}, indexes={@ORM\Index(name="inx_sort_order", columns={"sort_order"})})
 * @ORM\Entity(repositoryClass="ZekrBundle\Entity\Repository\SelectedVideo\SelectedVideoRepository")
 */
class SelectedVideo
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @var integer
     *
     * @ORM\Column(name="sort_order", type="integer")
     */
    private $sortOrder;


    /**
     * @ORM\OneToOne(targetEntity="ZekrBundle\Entity\Video")
     * @ORM\JoinColumn(name="video_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $video;


    /**
     * Set sortOrder
     *
     * @param integer $sortOrder
     *
     * @return SelectedVideo
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get Video
     *
     * @return \ZekrBundle\Entity\Video
     */

    public function getVideo()
    {
        return $this->video;
    }

    /**
     * Set Video
     *
     * @param \ZekrBundle\Entity\Video $Video
     *
     * @return SelectedVideo
     */
    public function setVideo($video)
    {
        $this->video = $video;
    }



}

