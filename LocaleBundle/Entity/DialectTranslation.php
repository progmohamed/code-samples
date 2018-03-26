<?php

namespace LocaleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * DialectTranslation
 *
 * @ORM\Table(name="locale_dialect_translation", uniqueConstraints={@ORM\UniqueConstraint(name="locale_dialect_translation_unique_translation", columns={"translatable_id", "locale"})}, indexes={@ORM\Index(name="inx_locale", columns={"locale"}), @ORM\Index(name="inx_translatable_id", columns={"translatable_id"}), @ORM\Index(name="inx_language_id", columns={"language_id"}), @ORM\Index(name="inx_created_at", columns={"created_at"}), @ORM\Index(name="inx_updated_at", columns={"updated_at"})})
 * @ORM\Entity
 */
class DialectTranslation
{
    use ORMBehaviors\Translatable\Translation;
    use \LocaleBundle\Classes\TranslationLanguageTrait;
    use ORMBehaviors\Timestampable\Timestampable;

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
     * @return DialectTranslation
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
