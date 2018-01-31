<?php

namespace Appstore\Bundle\DmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * Service
 *
 * @ORM\Table( name ="dms_teeth_plan")
 * @ORM\Entity(repositoryClass="")
 */
class DmsTeethPlan
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
     * @var string
     *
     * @ORM\Column(name="name", type="smallint", length=2, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="teethPosition", type="string", length=50, nullable=true)
     */
     private $teethPosition;

    /**
     * @var int
     *
     * @ORM\Column(name="sorting", type="smallint",  length=2, nullable=true)
     */
    private $sorting = 0;


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
     * @return string
     */
    public function getTeethPosition()
    {
        return $this->teethPosition;
    }

    /**
     * @param string $teethPosition
     */
    public function setTeethPosition($teethPosition)
    {
        $this->teethPosition = $teethPosition;
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


}

