<?php

namespace ZekrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * PersonTranslation
 *
 * @ORM\Table(name="zekr_person_translation", uniqueConstraints={@ORM\UniqueConstraint(name="inx_translatable_locale", columns={"translatable_id", "locale"})}, indexes={@ORM\Index(name="inx_name", columns={"name"}), @ORM\Index(name="inx_locale", columns={"locale"}), @ORM\Index(name="inx_translatable_id", columns={"translatable_id"})})
 * @ORM\Entity
 */
class PersonTranslation
{

    use ORMBehaviors\Translatable\Translation;


    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="resume", type="text", length=65535, nullable=true)
     */
    protected $resume;


    /**
     * Set name
     *
     * @param string $name
     *
     * @return PersonTranslation
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
     * Set resume
     *
     * @param string $resume
     *
     * @return PersonTranslation
     */
    public function setResume($resume)
    {
        $this->resume = $resume;

        return $this;
    }

    /**
     * Get resume
     *
     * @return string
     */
    public function getResume()
    {
        return $this->resume;
    }

}
