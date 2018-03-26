<?php

namespace LocaleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * Dialect
 *
 * @ORM\Table(name="locale_dialect", indexes={@ORM\Index(name="inx_language_id", columns={"language_id"})})
 * @ORM\Entity(repositoryClass="LocaleBundle\Entity\Repository\Dialect\Repository")
 */
class Dialect
{

    use ORMBehaviors\Translatable\Translatable;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;


    /**
     * @var \Language
     *
     * @ORM\ManyToOne(targetEntity="Language", inversedBy="dialect")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="language_id", referencedColumnName="id")
     * })
     * @Assert\NotBlank()
     */
    private $language;


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
     * Set language
     *
     * @param \LocaleBundle\Entity\Language $language
     *
     * @return Dialect
     */
    public function setLanguage(\LocaleBundle\Entity\Language $language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language
     *
     * @return \LocaleBundle\Entity\Language
     */
    public function getLanguage()
    {
        return $this->language;
    }


    public function __toString()
    {
        return ''.$this->translate()->getName();
    }

}
