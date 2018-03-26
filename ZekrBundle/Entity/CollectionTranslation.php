<?php

namespace ZekrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * CollectionTranslation
 *
 * @ORM\Table(name="zekr_clollection_translation", uniqueConstraints={@ORM\UniqueConstraint(name="inx_collection_id", columns={"translatable_id", "locale"})}, indexes={@ORM\Index(name="inx_locale", columns={"locale"}), @ORM\Index(name="inx_name", columns={"name"}), @ORM\Index(name="translatable_id", columns={"translatable_id"})})
 * @ORM\Entity
 */
class CollectionTranslation
{
    use ORMBehaviors\Translatable\Translation;
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text", length=65535, nullable=true)
     */
    private $note;

    /**
     * @var boolean
     *
     * @ORM\Column(name="display", type="boolean", nullable=false)
     */
    private $display = true;


    /**
     * Set name
     *
     * @param string $name
     *
     * @return CollectionTranslation
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
     * Set note
     *
     * @param string $note
     *
     * @return CollectionTranslation
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set display
     *
     * @param boolean $display
     *
     * @return CollectionTranslation
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
