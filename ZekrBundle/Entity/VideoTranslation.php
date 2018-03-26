<?php

namespace ZekrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * VideoTranslation
 *
 * @ORM\Table(name="zekr_video_translation", uniqueConstraints={@ORM\UniqueConstraint(name="inx_translatable_locale", columns={"translatable_id", "locale"})}, indexes={@ORM\Index(name="inx_title", columns={"title"}), @ORM\Index(name="inx_locale", columns={"locale"}), @ORM\Index(name="inx_translatable_id", columns={"translatable_id"})})
 * @ORM\Entity
 */
class VideoTranslation
{
    use ORMBehaviors\Translatable\Translation;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var boolean
     *
     * @ORM\Column(name="display", type="boolean", nullable=false)
     */
    private $display = false;


    /**
     * Set title
     *
     * @param string $title
     *
     * @return VideoTranslation
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return VideoTranslation
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set display
     *
     * @param boolean $display
     *
     * @return VideoTranslation
     */
    public function setDisplay($display)
    {
        $this->display = $display;

        return $this;
    }

    /**
     * Get display
     *
     * @return boolean
     */
    public function getDisplay()
    {
        return $this->display;
    }

}
