<?php

namespace ZekrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * Newsletter
 *
 * @ORM\Table(name="zekr_newsletter", uniqueConstraints={@ORM\UniqueConstraint(name="inx_email", columns={"email"})}, indexes={@ORM\Index(name="inx_active", columns={"active"})}, indexes={@ORM\Index(name="inx_name", columns={"name"})}, indexes={@ORM\Index(name="inx_locale", columns={"locale"})})
 * @ORM\Entity(repositoryClass="ZekrBundle\Entity\Repository\Newsletter\Repository")
 * @UniqueEntity("email")
 */
class Newsletter
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank(message = "front.newsletter.name_not_blank",)
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     *
     * @Assert\Email(
     *     message = "front.newsletter.email_not_valid",
     *     checkMX = true
     * )
     */
    protected $email;


    /**
     * @var string
     *
     * @ORM\Column(name="locale", type="string", length=2, nullable=false)
     * @Assert\Regex("/[a-z][a-z]/")
     */
    private $locale;


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
     * Set name
     *
     * @param string $name
     *
     * @return Newsletter
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
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set name
     *
     * @param string $email
     *
     * @return Newsletter
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }


    /**
     * Set locale
     *
     * @param string $locale
     *
     * @return Language
     */
    public function setLocale($locale)
    {
        $this->locale = strtolower($locale);

        return $this;
    }

    /**
     * Get locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    public function __toString()
    {
        return ''.$this->getName();
    }


}
