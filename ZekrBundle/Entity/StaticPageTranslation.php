<?php

namespace ZekrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * StaticPageTranslation
 *
 * @ORM\Table(name="zekr_static_page_translation", uniqueConstraints={@ORM\UniqueConstraint(name="inx_translatable_locale", columns={"translatable_id", "locale"})}, indexes={@ORM\Index(name="inx_locale", columns={"locale"}), @ORM\Index(name="inx_translatable_id", columns={"translatable_id"})})
 * @ORM\Entity
 */
class StaticPageTranslation
{
    use ORMBehaviors\Translatable\Translation;


    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private $title;



    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", length=65535, nullable=true)
     */
    protected $content;



    /**
     * Set title
     *
     * @param string $title
     *
     * @return StaticPageTranslation
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
     * Set resume
     *
     * @param string $content
     *
     * @return StaticPageTranslation
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

}
