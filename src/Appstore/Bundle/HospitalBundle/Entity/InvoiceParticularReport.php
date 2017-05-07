<?php

namespace Appstore\Bundle\HospitalBundle\Entity;

use Core\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * InvoiceParticularReport
 *
 * @ORM\Table( name = "hms_invoice_particular_report")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\HospitalBundle\Repository\InvoiceParticularRepository")
 */
class InvoiceParticularReport
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\HospitalBundle\Entity\InvoiceParticular", inversedBy="invoiceParticularReports")
     **/
    private $invoiceParticular;

    /**
     * @var string
     *
     * @ORM\Column(name="metaKey", type="string", length=255, nullable=true)
     */
    private $metaKey;

    /**
     * @var string
     *
     * @ORM\Column(name="metaValue", type="text", nullable=true)
     */
    private $metaValue;



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
    public function getInvoiceParticular()
    {
        return $this->invoiceParticular;
    }

    /**
     * @param mixed $invoiceParticular
     */
    public function setInvoiceParticular($invoiceParticular)
    {
        $this->invoiceParticular = $invoiceParticular;
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


}

