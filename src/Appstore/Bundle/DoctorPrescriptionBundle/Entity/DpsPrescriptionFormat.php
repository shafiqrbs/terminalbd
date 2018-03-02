<?php

namespace Appstore\Bundle\DoctorPrescriptionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * Service
 *
 * @ORM\Table( name ="dps_prescription_format")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\DoctorPrescriptionBundle\Repository\DpsPrescriptionFormatRepository")
 */
class DpsPrescriptionFormat
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsConfig", inversedBy="dpsServices")
     **/
    private $dpsConfig;

   
    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsInvoiceParticular", mappedBy="dpsService")
     **/
    private $invoiceParticular;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsParticular", mappedBy="service")
     **/
    private $dpsParticulars;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsInvoice", mappedBy="service")
     **/
    private $dpsInvoices;


    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=200, nullable=true)
     */
    private $name;


    /**
     * @var string
     *
     * @ORM\Column(name="serviceFormat", type="string", length=50, nullable=true)
     */
    private $serviceFormat;


    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=50, nullable=true)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=10, nullable=true)
     */
    private $code;


    /**
     * @var int
     *
     * @ORM\Column(name="sorting", type="smallint",  length=2, nullable=true)
     */
    private $sorting = 0;

    /**
     * @var boolean
     *
     * @ORM\Column(name="hasQuantity", type="boolean" )
     */
    private $hasQuantity = false;

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
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
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
    public function getHasQuantity()
    {
        return $this->hasQuantity;
    }

    /**
     * @param bool $hasQuantity
     */
    public function setHasQuantity($hasQuantity)
    {
        $this->hasQuantity = $hasQuantity;
    }


    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return DpsInvoice
     */
    public function getDpsInvoices()
    {
        return $this->dpsInvoices;
    }

    /**
     * @return DpsParticular
     */
    public function getDpsParticulars()
    {
        return $this->dpsParticulars;
    }

    /**
     * @return DpsConfig
     */
    public function getDpsConfig()
    {
        return $this->dpsConfig;
    }

    /**
     * @param DpsConfig $dpsConfig
     */
    public function setDpsConfig($dpsConfig)
    {
        $this->dpsConfig = $dpsConfig;
    }


    /**
     * @return DentalService
     */
    public function getDentalService()
    {
        return $this->dentalService;
    }

    /**
     * @return mixed
     */
    public function getInvoiceParticular()
    {
        return $this->invoiceParticular;
    }

    /**
     * @return string
     */
    public function getServiceFormat()
    {
        return $this->serviceFormat;
    }

    /**
     * @param string $serviceFormat
     * Teeth Format
     * Checkbox with Text Field
     * Text Field
     * Checkbox
     * Text-area
     */

    public function setServiceFormat($serviceFormat)
    {
        $this->serviceFormat = $serviceFormat;
    }


}

