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
     **/
    private $dmsInvoice;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\DmsBundle\Entity\DmsParticular", inversedBy="invoiceParticular")
     **/
    private $particular;

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
     * @return DmsParticular
     */
    public function getParticular()
    {
        return $this->particular;
    }

    /**
     * @param DmsParticular $particular
     */
    public function setParticular($particular)
    {
        $this->particular = $particular;
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


}

