<?php

namespace ConfigBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * ConfigVariable
 *
 * @ORM\Table(name="config_variable", uniqueConstraints={@ORM\UniqueConstraint(name="inx_variable", columns={"variable"})}, indexes={@ORM\Index(name="inx_type", columns={"type"})})
 * @ORM\Entity(repositoryClass="ConfigBundle\Entity\Repository\ConfigVariable\Repository")
 * @UniqueEntity("variable")
 */
class ConfigVariable
{
    const NUMERIC = 'numeric';
    const STRING = 'string';
    const TEXT = 'text';
    const CHOICE = 'choice';
    const BOOLEAN = 'boolean';

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
     * @ORM\Column(name="variable", type="string", length=255, nullable=false, unique=true)
     * @Assert\NotBlank()
     */
    private $variable;

    /**
     * @var integer
     *
     * @ORM\Column(name="value", type="text", nullable=true)
     */
    private $value;


    /**
     * @var integer
     *
     * @ORM\Column(name="data", type="text", nullable=true)
     */
    private $data;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private $type;


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
     * Set variable
     *
     * @param string $variable
     *
     * @return ConfigVariable
     */
    public function setVariable($variable)
    {
        $this->variable = $variable;

        return $this;
    }

    /**
     * Get variable
     *
     * @return string
     */
    public function getVariable()
    {
        return $this->variable;
    }


    /**
     * Set value
     *
     * @param string $value
     *
     * @return ConfigVariable
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }


    /**
     * Set data
     *
     * @param string $data
     *
     * @return ConfigVariable
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }


    /**
     * Set type
     *
     * @param string $type
     *
     * @return ConfigVariable
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    public function __toString()
    {
        if ($variable = $this->getVariable()) {
            return $variable;
        }

        return '';
    }
}
