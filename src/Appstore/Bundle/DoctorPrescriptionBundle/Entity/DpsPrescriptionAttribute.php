<?php

namespace Appstore\Bundle\DoctorPrescriptionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * Service
 *
 * @ORM\Table( name ="dps_prescription_attribute")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\DoctorPrescriptionBundle\Repository\DpsPrescriptionAttributeRepository")
 */
class DpsPrescriptionAttribute
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
     * @ORM\Column(name="name", type="string", length=50, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="nameBn", type="string", length=50, nullable=true)
     */
    private $nameBn;

    /**
     * @var string
     *
     * @ORM\Column(name="parent", type="string", length=50, nullable=true)
     */
    private $parent;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean" )
     */
    private $status= true;

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
     * @return string
     */
    public function getNameBn()
    {
        return $this->nameBn;
    }

    /**
     * @param string $nameBn
     */
    public function setNameBn($nameBn)
    {
        $this->nameBn = $nameBn;
    }

    /**
     * @return mixed
     */
    public function getDpsInvoiceMedicine()
    {
        return $this->dpsInvoiceMedicine;
    }

    /**
     * @param mixed $dpsInvoiceMedicine
     */
    public function setDpsInvoiceMedicine($dpsInvoiceMedicine)
    {
        $this->dpsInvoiceMedicine = $dpsInvoiceMedicine;
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param string $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }



}

