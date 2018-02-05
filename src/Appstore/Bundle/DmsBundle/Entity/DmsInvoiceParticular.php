<?php

namespace Appstore\Bundle\DmsBundle\Entity;

use Core\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * DmsInvoiceParticular
 *
 * @ORM\Table( name = "dms_invoice_particular")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\DmsBundle\Repository\DmsInvoiceParticularRepository")
 */
class DmsInvoiceParticular
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\DmsBundle\Entity\DmsInvoice", inversedBy="invoiceParticulars")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @ORM\OrderBy({"id" = "ASC"})
     **/
    private $dmsInvoice;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\DmsBundle\Entity\DmsParticular", inversedBy="invoiceParticular", cascade={"persist"} )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private $dmsParticular;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\DmsBundle\Entity\DmsService", inversedBy="invoiceParticular", cascade={"persist"} )
     **/
    private $dmsService;

    /**
     * @var string
     *
     * @ORM\Column(name="metaKey", type="string", length=225, nullable=true)
     */
    private $metaKey;

    /**
     * @var string
     *
     * @ORM\Column(name="metaValue", type="text", nullable=true)
     */
    private $metaValue;

    /**
     * @var string
     *
     * @ORM\Column(name="teethPosition", type="string", length=25, nullable=true)
     */
    private $teethPosition;

    /**
     * @var array
     *
     * @ORM\Column(name="teethNo", type="array", nullable=true)
     */
    private $teethNo;


    /**
     * @var boolean
     *
     * @ORM\Column(name="metaStatus", type="boolean", nullable=true)
     */
    private $metaStatus;


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
     * @return DmsInvoice
     */
    public function getDmsInvoice()
    {
        return $this->dmsInvoice;
    }

    /**
     * @param DmsInvoice $dmsInvoice
     */
    public function setDmsInvoice($dmsInvoice)
    {
        $this->dmsInvoice = $dmsInvoice;
    }

    /**
     * @return string
     */
    public function getMetaKey()
    {
        return $this->metaKey;
    }

    /**
     * @param string $metaKey
     */
    public function setMetaKey($metaKey)
    {
        $this->metaKey = $metaKey;
    }

    /**
     * @return string
     */
    public function getMetaValue()
    {
        return $this->metaValue;
    }

    /**
     * @param string $metaValue
     */
    public function setMetaValue($metaValue)
    {
        $this->metaValue = $metaValue;
    }

    /**
     * @return bool
     */
    public function getMetaStatus()
    {
        return $this->metaStatus;
    }

    /**
     * @param bool $metaStatus
     */
    public function setMetaStatus($metaStatus)
    {
        $this->metaStatus = $metaStatus;
    }

    /**
     * @return DmsParticular
     */
    public function getDmsParticular()
    {
        return $this->dmsParticular;
    }

    /**
     * @param DmsParticular $dmsParticular
     */
    public function setDmsParticular($dmsParticular)
    {
        $this->dmsParticular = $dmsParticular;
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
     * @return array
     */
    public function getTeethNo()
    {
        return $this->teethNo;
    }

    /**
     * @param array $teethNo
     */
    public function setTeethNo($teethNo)
    {
        $this->teethNo = $teethNo;
    }

    /**
     * @return DmsService
     */
    public function getDmsService()
    {
        return $this->dmsService;
    }

    /**
     * @param DmsService $dmsService
     */
    public function setDmsService($dmsService)
    {
        $this->dmsService = $dmsService;
    }


}

