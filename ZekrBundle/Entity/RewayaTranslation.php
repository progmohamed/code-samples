<?php

namespace ZekrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * RewayaTranslation
 *
 * @ORM\Table(name="zekr_rewaya_translation", uniqueConstraints={@ORM\UniqueConstraint(name="inx_translatable_locale", columns={"translatable_id", "locale"})}, indexes={@ORM\Index(name="inx_locale", columns={"locale"}), @ORM\Index(name="inx_name", columns={"name"}), @ORM\Index(name="inx_translatable_id", columns={"translatable_id"})})
 * @ORM\Entity
 */
class RewayaTranslation
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
     * @return RewayaTranslation
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
