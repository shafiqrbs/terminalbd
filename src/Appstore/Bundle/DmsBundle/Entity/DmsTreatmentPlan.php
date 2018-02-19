<?php

namespace Appstore\Bundle\DmsBundle\Entity;

use Appstore\Bundle\DmsBundle\Entity\DmsInvoice;
use Appstore\Bundle\DmsBundle\Entity\DmsParticular;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * HmsPurchaseItem
 *
 * @ORM\Table(name ="dms_treatment_plan")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\DmsBundle\Repository\DmsTreatmentPlanRepository")
 */
class DmsTreatmentPlan
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\DmsBundle\Entity\DmsParticular", inversedBy="dmsTreatmentPlans" )
     **/
    private  $dmsParticular;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\DmsBundle\Entity\DmsInvoice", inversedBy="dmsTreatmentPlans" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $dmsInvoice;

    /**
     * @var float
     *
     * @ORM\Column(name="estimatePrice", type="float", nullable=true)
     */
    private $estimatePrice;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float", nullable=true)
     */
    private $price;

    /**
     * @var float
     *
     * @ORM\Column(name="discount", type="float", nullable=true)
     */
    private $discount;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="smallint", nullable=true)
     */
    private $quantity;

    /**
     * @var float
     *
     * @ORM\Column(name="subTotal", type="float", nullable=true)
     */
    private $subTotal;

    /**
     * @var float
     *
     * @ORM\Column(name="payment", type="float", nullable=true)
     */
    private $payment;

    /**
     * @var float
     *
     * @ORM\Column(name="balance", type="float", nullable=true)
     */
    private $balance;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="appointmentDate",  type="datetime", nullable=true)
     */
    private $appointmentDate;

    /**
     * @var string
     *
     * @ORM\Column(name="appointmentTime", type="string", nullable=true)
     */
    private $appointmentTime;


    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean" )
     */
    private $status=false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="sendSms", type="boolean" )
     */
    private $sendSms = false;




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
     * @return float
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * @param float $payment
     */
    public function setPayment($payment)
    {
        $this->payment = $payment;
    }

    /**
     * @return float
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @param float $balance
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;
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
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return float
     */
    public function getEstimatePrice()
    {
        return $this->estimatePrice;
    }

    /**
     * @param float $estimatePrice
     */
    public function setEstimatePrice($estimatePrice)
    {
        $this->estimatePrice = $estimatePrice;
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
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @param float $discount
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;
    }


    /**
     * @return string
     */
    public function getAppointmentTime()
    {
        return $this->appointmentTime;
    }

    /**
     * @param string $appointmentTime
     */
    public function setAppointmentTime($appointmentTime)
    {
        $this->appointmentTime = $appointmentTime;
    }

    /**
     * @return bool
     */
    public function isSendSms()
    {
        return $this->sendSms;
    }

    /**
     * @param bool $sendSms
     */
    public function setSendSms($sendSms)
    {
        $this->sendSms = $sendSms;
    }

    /**
     * @return \DateTime
     */
    public function getAppointmentDate()
    {
        return $this->appointmentDate;
    }

    /**
     * @param \DateTime $appointmentDate
     */
    public function setAppointmentDate($appointmentDate)
    {
        $this->appointmentDate = $appointmentDate;
    }


}

