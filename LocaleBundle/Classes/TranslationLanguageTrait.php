<?php

namespace LocaleBundle\Classes;

/**
 * Translatable trait.
 *
 * Should be used inside entity, that needs to be translated.
 */
trait TranslationLanguageTrait
{
    /**
     * @var integer
     *
     * @ORM\Column(name="language_id", type="integer", nullable=false)
     */
    private $language;


    public function setLanguage($language = null)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get integer
     *
     * @return integer
     */
    public function getLanguage()
    {
        return $this->language;
    }
}
