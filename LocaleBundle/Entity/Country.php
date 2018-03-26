<?php

namespace LocaleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;


/**
 * Country
 *
 * @ORM\Table(name="locale_country")
 * @ORM\Entity(repositoryClass="LocaleBundle\Entity\Repository\CountryRepository")
 */
class Country
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    function __toString()
    {
        return ''.$this->translate()->getName();
    }

}
