<?php

namespace SearchBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * History
 *
 * @ORM\Table(name="search_history", uniqueConstraints={@ORM\UniqueConstraint(name="inx_string", columns={"string"})}, indexes={@ORM\Index(name="inx_sum", columns={"sum"})})
 * @ORM\Entity(repositoryClass="SearchBundle\Entity\Repository\History\Repository")
 * @UniqueEntity(fields = {"string"},
 *     message="هذه الكلمة موجودة مسبقاا"
 * )
 */
class History
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
     * @Assert\NotBlank()
     * @ORM\Column(name="string", type="string", length=255, nullable=false)
     */
    protected $string;

    /**
     * @var integer
     *
     * @ORM\Column(name="sum", type="integer", nullable=false)
     */
    private $sum;


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
     * Set string
     *
     * @param string $string
     *
     * @return History
     */
    public function setString($string)
    {
        $this->string = $string;

        return $this;
    }

    /**
     * Get string
     *
     * @return string
     */
    public function getString()
    {
        return $this->string;
    }

    /**
     * Set sum
     *
     * @param integer $sum
     *
     * @return History
     */
    public function setSum($sum)
    {
        $this->sum = $sum;

        return $this;
    }

    /**
     * Get sum
     *
     * @return integer
     */
    public function getSum()
    {
        return $this->sum;
    }

    public function __toString()
    {
        return '' . $this->getString();
    }

}
