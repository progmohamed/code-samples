<?php

namespace ZekrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;


/**
 * MigrationCategoryTranslation
 *
 * @ORM\Table(name="category_translation", indexes={@ORM\Index(name="locale", columns={"locale"}), @ORM\Index(name="name", columns={"name"})})
 * @ORM\Entity
 */
class MigrationCategoryTranslation
{
    use ORMBehaviors\Translatable\Translation;
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;



    /**
     * Set name
     *
     * @param string $name
     *
     * @return MigrationCategoryTranslation
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
}
