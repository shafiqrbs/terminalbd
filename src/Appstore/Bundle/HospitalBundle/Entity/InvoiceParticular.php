<?php

namespace Appstore\Bundle\HospitalBundle\Entity;

use Core\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * InvoiceParticular
 *
 * @ORM\Table( name = "hms_invoice_particular")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\HospitalBundle\Repository\InvoiceParticularRepository")
 */
class InvoiceParticular
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\HospitalBundle\Entity\Invoice", inversedBy="invoiceParticulars")
     **/
    private $hmsInvoice;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\HospitalBundle\Entity\Particular", inversedBy="invoiceParticular")
     **/
    private $particular;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\HospitalBundle\Entity\Particular", inversedBy="invoiceParticularDoctor")
     **/
    private $assignDoctor;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\InvoicePathologicalReport", mappedBy="invoiceParticular")
     **/
    private $invoicePathologicalReports;

    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="hmsInvoiceParticularDelivered" )
     **/
    private  $particularDeliveredBy;

    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="hmsInvoiceParticularPrepared" )
     **/
    private  $particularPreparedBy;

    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="hmsInvoiceParticularCollected" )
     **/
    private  $sampleCollectedBy;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="smallint")
     */
    private $quantity = 1;


    /**
     * @var float
     *
     * @ORM\Column(name="salesPrice", type="float")
     */
    private $salesPrice;


    /**
     * @var string
     *
     * @ORM\Column(name="estimatePrice", type="decimal")
     */
    private $estimatePrice;


    /**
     * @var boolean
     *
     * @ORM\Column(name="customPrice", type="boolean")
     */
    private $customPrice = false;

    /**
     * @var float
     *
     * @ORM\Column(name="subTotal", type="float")
     */
    private $subTotal;

    /**
     * @var string
     *
     * @ORM\Column(name="process", type="string", length=30, nullable=true)
     */
    private $process ='In-progress';

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;


    /**
     * @var \DateTime
     * @ORM\Column(name="collectionDate", type="datetime")
     */
    private $collectionDate;

     /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;


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
     * @return Particular
     */
    public function getParticular()
    {
        return $this->particular;
    }

    /**
     * @param Particular $particular
     */
    public function setParticular($particular)
    {
        $this->particular = $particular;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return float
     */
    public function getSalesPrice()
    {
        return $this->salesPrice;
    }

    /**
     * @param float $salesPrice
     */
    public function setSalesPrice($salesPrice)
    {
        $this->salesPrice = $salesPrice;
    }

    /**
     * @return string
     */
    public function getEstimatePrice()
    {
        return $this->estimatePrice;
    }

    /**
     * @param string $estimatePrice
     */
    public function setEstimatePrice($estimatePrice)
    {
        $this->estimatePrice = $estimatePrice;
    }

    /**
     * @return bool
     */
    public function isCustomPrice()
    {
        return $this->customPrice;
    }

    /**
     * @param bool $customPrice
     */
    public function setCustomPrice($customPrice)
    {
        $this->customPrice = $customPrice;
    }

    /**
     * @return float
     */
    public function getSubTotal()
    {
        return $this->subTotal;
    }

    /**
     * @param float $subTotal
     */
    public function setSubTotal($subTotal)
    {
        $this->subTotal = $subTotal;
    }

    /**
     * @return User
     */
    public function getParticularPreparedBy()
    {
        return $this->particularPreparedBy;
    }

    /**
     * @param User $particularPreparedBy
     */
    public function setParticularPreparedBy($particularPreparedBy)
    {
        $this->particularPreparedBy = $particularPreparedBy;
    }

    /**
     * @return User
     */
    public function getParticularDeliveredBy()
    {
        return $this->particularDeliveredBy;
    }

    /**
     * @param User $particularDeliveredBy
     */
    public function setParticularDeliveredBy($particularDeliveredBy)
    {
        $this->particularDeliveredBy = $particularDeliveredBy;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param \DateTime $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return string
     */
    public function getProcess()
    {
        return $this->process;
    }

    /**
     * @param string $process
     */
    public function setProcess($process)
    {
        $this->process = $process;
    }

    /**
     * @return Invoice
     */
    public function getHmsInvoice()
    {
        return $this->hmsInvoice;
    }

    /**
     * @param Invoice $hmsInvoice
     */
    public function setHmsInvoice($hmsInvoice)
    {
        $this->hmsInvoice = $hmsInvoice;
    }


    /**
     * @return Particular
     */
    public function getAssignDoctor()
    {
        return $this->assignDoctor;
    }

    /**
     * @param Particular $assignDoctor
     */
    public function setAssignDoctor($assignDoctor)
    {
        $this->assignDoctor = $assignDoctor;
    }

    /**
     * @return InvoicePathologicalReport
     */
    public function getInvoicePathologicalReports()
    {
        return $this->invoicePathologicalReports;
    }

    /**
     * @return User
     */
    public function getSampleCollectedBy()
    {
        return $this->sampleCollectedBy;
    }

    /**
     * @param User $sampleCollectedBy
     */
    public function setSampleCollectedBy($sampleCollectedBy)
    {
        $this->sampleCollectedBy = $sampleCollectedBy;
    }

    /**
     * @return \DateTime
     */
    public function getCollectionDate()
    {
        return $this->collectionDate;
    }

    /**
     * @param \DateTime $collectionDate
     */
    public function setCollectionDate($collectionDate)
    {
        $this->collectionDate = $collectionDate;
    }


}

