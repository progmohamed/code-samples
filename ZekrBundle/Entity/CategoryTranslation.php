<?php

namespace ZekrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * CategoryTranslation
 *
 * @ORM\Table(name="zekr_category_translation", uniqueConstraints={@ORM\UniqueConstraint(name="inx_translatable_locale", columns={"translatable_id", "locale"})}, indexes={@ORM\Index(name="inx_name", columns={"name"}), @ORM\Index(name="inx_locale", columns={"locale"}), @ORM\Index(name="inx_translatable_id", columns={"translatable_id"})})
 * @ORM\Entity
 */
class CategoryTranslation
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
     * Set name
     *
     * @param string $name
     *
     * @return CategoryTranslation
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
     * @return CategoryTranslation
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
}
