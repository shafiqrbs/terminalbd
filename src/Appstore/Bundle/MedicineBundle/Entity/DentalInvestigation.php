<?php

namespace Appstore\Bundle\MedicineBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * DentalInvestigation
 *
 * @ORM\Table( name ="medicine_dental_investigation")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\MedicineBundle\Repository\DentalInvestigationRepository")
 */
class DentalInvestigation
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="DentalInvestigation", inversedBy="children", cascade={"detach","merge"})
     * @ORM\JoinColumn(name="parent", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="DentalInvestigation" , mappedBy="parent")
     * @ORM\OrderBy({"sorting" = "ASC"})
     **/
    private $children;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=200, nullable=true)
     */
    private $name;


    /**
     * @var int
     *
     * @ORM\Column(name="sorting", type="smallint", length=2, nullable=true)
     */
    private $sorting;


    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean" )
     */
    private $status= true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isParent", type="boolean" )
     */
    private $isParent = true;



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
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }


    /**
     * @return bool
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param bool $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }


    /**
     * @return DentalInvestigation
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param DentalInvestigation $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return DentalInvestigation
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return int
     */
    public function getSorting()
    {
        return $this->sorting;
    }

    /**
     * @param int $sorting
     */
    public function setSorting($sorting)
    {
        $this->sorting = $sorting;
    }

    /**
     * @return bool
     */
    public function isParent()
    {
        return $this->isParent;
    }

    /**
     * @param bool $isParent
     */
    public function setIsParent($isParent)
    {
        $this->isParent = $isParent;
    }


}

