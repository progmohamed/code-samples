<?php

namespace ZekrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SelectedCollection
 *
 * @ORM\Table(name="zekr_selected_collection", uniqueConstraints={@ORM\UniqueConstraint(name="inx_collection", columns={"collection_id"})}, indexes={@ORM\Index(name="inx_sort_order", columns={"sort_order"})})
 * @ORM\Entity(repositoryClass="ZekrBundle\Entity\Repository\SelectedCollection\SelectedCollectionRepository")
 */
class SelectedCollection
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
     * @var integer
     *
     * @ORM\Column(name="sort_order", type="integer", nullable=false)
     */
    private $sortOrder;

    /**
     * @var \Collection
     *
     * @ORM\ManyToOne(targetEntity="ZekrBundle\Entity\Collection")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="collection_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $collection;



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
     * Set sortOrder
     *
     * @param integer $sortOrder
     *
     * @return SelectedCollection
     */
    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    /**
     * Get sortOrder
     *
     * @return integer
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    /**
     * Set collection
     *
     * @param \ZekrBundle\Entity\Collection $collection
     *
     * @return SelectedCollection
     */
    public function setCollection(\ZekrBundle\Entity\Collection $collection = null)
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * Get collection
     *
     * @return \ZekrBundle\Entity\Collection
     */
    public function getCollection()
    {
        return $this->collection;
    }
}
