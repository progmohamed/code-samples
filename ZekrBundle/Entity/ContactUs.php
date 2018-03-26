<?php

namespace ZekrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * ContactUs
 *
 * @ORM\Table(name="zekr_contact_us", indexes={@ORM\Index(name="inx_title", columns={"title"})}, indexes={@ORM\Index(name="inx_email", columns={"email"})}, indexes={@ORM\Index(name="inx_active", columns={"active"})}, indexes={@ORM\Index(name="inx_name", columns={"name"})}, indexes={@ORM\Index(name="inx_locale", columns={"locale"})})
 * @ORM\Entity(repositoryClass="ZekrBundle\Entity\Repository\ContactUs\Repository")
 */
class ContactUs
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
     * @Assert\NotBlank(message = "front.contact_us.name_not_blank",)
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     *
     * @Assert\Email(
     *     message = "front.contact_us.email_not_valid",
     *     checkMX = true
     * )
     */
    protected $email;

    /**
     * @var string
     *
     * @Assert\NotBlank(message = "front.contact_us.title_not_blank",)
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    protected $title;

    /**
     * @var string
     *
     * @Assert\NotBlank(message = "front.contact_us.message_not_blank",)
     * @ORM\Column(name="message", type="text", length=65535, nullable=false)
     */
    protected $message;

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
     * @return ContactUs
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
     * Set title
     *
     * @param string $title
     *
     * @return ContactUs
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set message
     *
     * @param string $message
     *
     * @return ContactUs
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
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
     * @return ContactUs
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
