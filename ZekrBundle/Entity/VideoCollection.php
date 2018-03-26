<?php

namespace ZekrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VideoCollection
 *
 * @ORM\Table(name="zekr_video_collection", uniqueConstraints={@ORM\UniqueConstraint(name="IDX_video_id", columns={"video_id", "collection_id"})}, indexes={@ORM\Index(name="IDX_sort_order", columns={"sort_order"}), @ORM\Index(name="collection_id", columns={"collection_id"}), @ORM\Index(name="IDX_BD72901129C1004E", columns={"video_id"})})
 * @ORM\Entity
 */
class VideoCollection
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
     * @var integer
     *
     * @ORM\Column(name="sort_order", type="integer", nullable=false)
     */
    private $sortOrder;

    /**
     * @var \Video
     *
     * @ORM\ManyToOne(targetEntity="Video", inversedBy="videoCollections")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="video_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $video;

    /**
     * @var \Collection
     *
     * @ORM\ManyToOne(targetEntity="Collection")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="collection_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $collection;



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
     * Set sortOrder
     *
     * @param integer $sortOrder
     *
     * @return VideoCollection
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
     * Set video
     *
     * @param \ZekrBundle\Entity\Video $video
     *
     * @return VideoCollection
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
     * Set collection
     *
     * @param \ZekrBundle\Entity\Collection $collection
     *
     * @return VideoCollection
     */
    public function setCollection(\ZekrBundle\Entity\Collection $collection = null)
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * Get collection
     *
     * @return \ZekrBundle\Entity\Collection
     */
    public function getCollection()
    {
        return $this->collection;
    }


}
