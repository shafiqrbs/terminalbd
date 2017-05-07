<?php

namespace Appstore\Bundle\HospitalBundle\Entity;

use Appstore\Bundle\AccountingBundle\Entity\AccountBank;
use Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank;
use Appstore\Bundle\AccountingBundle\Entity\AccountSales;
use Appstore\Bundle\DomainUserBundle\Entity\Branches;
use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\ToolBundle\Entity\Bank;
use Setting\Bundle\ToolBundle\Entity\TransactionMethod;

/**
 * Invoice
 *
 * @ORM\Table( name ="hms_invoice")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\HospitalBundle\Repository\InvoiceRepository")
 */
class Invoice
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\HospitalBundle\Entity\HospitalConfig", inversedBy="invoices" , cascade={"persist", "remove"})
     **/
    private $hospitalConfig;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\HospitalBundle\Entity\Service", inversedBy="invoices" , cascade={"persist", "remove"})
     **/
    private $service;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\DomainUserBundle\Entity\Branches", inversedBy="hmsInvoice" )
     **/

    private  $branches;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\InvoiceParticular", mappedBy="invoice" , cascade={"remove"} )
     * @ORM\OrderBy({"id" = "ASC"})
     **/
    private  $invoiceParticulars;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountSales", mappedBy="hmsInvoice" )
     * @ORM\OrderBy({"id" = "DESC"})
     **/
    private  $accountSales;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\DomainUserBundle\Entity\Customer", inversedBy="hmsInvoices" ,cascade={"persist"} )
     **/
    private  $customer;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\HospitalBundle\Entity\Particular", inversedBy="hmsInvoice", cascade={"persist"}  )
     **/
    private  $referredDoctor;


    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="hmsInvoiceCreatedBy" )
     **/
    private  $createdBy;


    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="hmsInvoiceDeliveredBy" )
     **/
    private  $approvedBy;

    /**
     * @var string
     *
     * @ORM\Column(name="paymentMethod", type="string", length=30, nullable=true)
     */
    private $paymentMethod='Cash';


    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\TransactionMethod", inversedBy="hmsInvoice" )
     **/
    private  $transactionMethod;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\Bank", inversedBy="hmsInvoice" )
     **/
    private  $bank;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountBank", inversedBy="hmsInvoice" )
     **/
    private  $accountBank;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank", inversedBy="hmsInvoice" )
     **/
    private  $accountMobileBank;


    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\paymentCard", inversedBy="hmsInvoice" )
     **/
    private  $paymentCard;

    /**
     * @var string
     *
     * @ORM\Column(name="cardNo", type="string", length=100, nullable=true)
     */
    private $cardNo;

    /**
     * @var string
     *
     * @ORM\Column(name="paymentMobile", type="string", length=50, nullable=true)
     */
    private $paymentMobile;

    /**
     * @var string
     *
     * @ORM\Column(name="paymentInWord", type="string", length=255, nullable=true)
     */
    private $paymentInWord;

    /**
     * @var string
     *
     * @ORM\Column(name="process", type="string", length=50, nullable=true)
     */
    private $process ='In-progress';

    /**
     * @var string
     *
     * @ORM\Column(name="transactionId", type="string", length=100, nullable=true)
     */
    private $transactionId;

    /**
     * @var string
     *
     * @ORM\Column(name="invoice", type="string", length=50, nullable=true)
     */
    private $invoice;

    /**
     * @var integer
     *
     * @ORM\Column(name="code", type="integer",  nullable=true)
     */
    private $code;


    /**
     * @var string
     *
     * @ORM\Column(name="paymentStatus", type="string", length=50, nullable=true)
     */
    private $paymentStatus = "Pending";


    /**
     * @var string
     *
     * @ORM\Column(name="subTotal", type="decimal", nullable=true)
     */
    private $subTotal;


    /**
     * @var string
     *
     * @ORM\Column(name="referredCommission", type="decimal", nullable=true)
     */
    private $referredCommission;

    /**
     * @var string
     *
     * @ORM\Column(name="discount", type="decimal", nullable=true)
     */
    private $discount;

    /**
     * @var integer
     *
     * @ORM\Column(name="percentage", type="smallint" , length=3 , nullable=true)
     */
    private $percentage;


    /**
     * @var string
     *
     * @ORM\Column(name="vat", type="decimal", nullable=true)
     */
    private $vat;

    /**
     * @var string
     *
     * @ORM\Column(name="total", type="decimal", nullable=true)
     */
    private $total;

    /**
     * @var string
     *
     * @ORM\Column(name="netTotal", type="decimal", nullable=true)
     */
    private $netTotal;

    /**
     * @var string
     *
     * @ORM\Column(name="payment", type="decimal", nullable=true)
     */
    private $payment;


    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;

    /**
     * @var string
     *
     * @ORM\Column(name="due", type="decimal", nullable=true)
     */
    private $due;

    /**
     * @var string
     *
     * @ORM\Column(name="mobile", type="text", nullable=true)
     */
    private $mobile;

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
     * @var DateTime
     *
     * @ORM\Column(name="deliveryDate", type="datetime", nullable=true)
     */
    private $deliveryDate;

    /**
     * @var string
     *
     * @ORM\Column(name="deliveryTime", type="string", length=20, nullable=true)
     */
    private $deliveryTime;

    /**
     * @var string
     *
     * @ORM\Column(name="deliveryDateTime", type="string",  length=50, nullable=true)
     */
    private $deliveryDateTime;

    /**
     * @var string
     *
     * @ORM\Column(name="printFor", type="string",  length=50, nullable=true)
     */
    private $printFor ='Pathological';


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
     * @return HospitalConfig
     */
    public function getHospitalConfig()
    {
        return $this->hospitalConfig;
    }

    /**
     * @param HospitalConfig $hospitalConfig
     */
    public function setHospitalConfig($hospitalConfig)
    {
        $this->hospitalConfig = $hospitalConfig;
    }


    /**
     * @return Branches
     */
    public function getBranches()
    {
        return $this->branches;
    }

    /**
     * @param Branches $branches
     */
    public function setBranches($branches)
    {
        $this->branches = $branches;
    }

    /**
     * @return InvoiceParticular
     */
    public function getInvoiceParticulars()
    {
        return $this->invoiceParticulars;
    }

    /**
     * @param InvoiceParticular $invoiceParticulars
     */
    public function setInvoiceParticulars($invoiceParticulars)
    {
        $this->invoiceParticulars = $invoiceParticulars;
    }

    /**
     * @return string
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * @param string $paymentMethod
     * Cash
     * Cheque
     * Giftcard
     * Bkash
     * Payment Card
     * Other
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
    }

    /**
     * @return string
     */
    public function getSubTotal()
    {
        return $this->subTotal;
    }

    /**
     * @param string $subTotal
     */
    public function setSubTotal($subTotal)
    {
        $this->subTotal = $subTotal;
    }

    /**
     * @return string
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @param string $discount
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;
    }

    /**
     * @return string
     */
    public function getVat()
    {
        return $this->vat;
    }

    /**
     * @param string $vat
     */
    public function setVat($vat)
    {
        $this->vat = $vat;
    }

    /**
     * @return string
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param string $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
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
     * @return int
     */
    public function getTotalItem()
    {
        return $this->totalItem;
    }

    /**
     * @param int $totalItem
     */
    public function setTotalItem($totalItem)
    {
        $this->totalItem = $totalItem;
    }

    /**
     * @return Bank
     */
    public function getBank()
    {
        return $this->bank;
    }

    /**
     * @param Bank $bank
     */
    public function setBank($bank)
    {
        $this->bank = $bank;
    }

    /**
     * @return mixed
     */
    public function getPaymentCard()
    {
        return $this->paymentCard;
    }

    /**
     * @param mixed $paymentCard
     */
    public function setPaymentCard($paymentCard)
    {
        $this->paymentCard = $paymentCard;
    }

    /**
     * @return string
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * @param string $mobile
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
    }

    /**
     * @return string
     */
    public function getPaymentStatus()
    {
        return $this->paymentStatus;
    }

    /**
     * @param string $paymentStatus
     * Paid
     * Pending
     * Partial
     * Due
     * Other
     */
    public function setPaymentStatus($paymentStatus)
    {
        $this->paymentStatus = $paymentStatus;
    }


    /**
     * @return integer
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param integer $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * @param string $invoice
     */
    public function setInvoice($invoice)
    {
        $this->invoice = $invoice;
    }


    /**
     * @return string
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * @param string $payment
     */
    public function setPayment($payment)
    {
        $this->payment = $payment;
    }


    /**
     * @return AccountSales
     */
    public function getAccountSales()
    {
        return $this->accountSales;
    }

    /**
     * @param AccountSales $accountSales
     */
    public function setAccountSales($accountSales)
    {
        $this->accountSales = $accountSales;
    }

    /**
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param Customer $customer
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
    }

    /**
     * @return User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param User $createdBy
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     * @return User
     */
    public function getApprovedBy()
    {
        return $this->approvedBy;
    }

    /**
     * @param User $approvedBy
     */
    public function setApprovedBy($approvedBy)
    {
        $this->approvedBy = $approvedBy;
    }

    /**
     * @return AccountBank
     */
    public function getAccountBank()
    {
        return $this->accountBank;
    }

    /**
     * @param AccountBank $accountBank
     */
    public function setAccountBank($accountBank)
    {
        $this->accountBank = $accountBank;
    }

    /**
     * @return TransactionMethod
     */
    public function getTransactionMethod()
    {
        return $this->transactionMethod;
    }

    /**
     * @param TransactionMethod $transactionMethod
     */
    public function setTransactionMethod($transactionMethod)
    {
        $this->transactionMethod = $transactionMethod;
    }

    /**
     * @return AccountMobileBank
     */
    public function getAccountMobileBank()
    {
        return $this->accountMobileBank;
    }

    /**
     * @param AccountMobileBank $accountMobileBank
     */
    public function setAccountMobileBank($accountMobileBank)
    {
        $this->accountMobileBank = $accountMobileBank;
    }

    /**
     * @return string
     */
    public function getPaymentMobile()
    {
        return $this->paymentMobile;
    }

    /**
     * @param string $paymentMobile
     */
    public function setPaymentMobile($paymentMobile)
    {
        $this->paymentMobile = $paymentMobile;
    }

    /**
     * @return string
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * @param string $transactionId
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;
    }

    /**
     * @return string
     */
    public function getCardNo()
    {
        return $this->cardNo;
    }

    /**
     * @param string $cardNo
     */
    public function setCardNo($cardNo)
    {
        $this->cardNo = $cardNo;
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
     * @return string
     */
    public function getPaymentInWord()
    {
        return $this->paymentInWord;
    }

    /**
     * @param string $paymentInWord
     */
    public function setPaymentInWord($paymentInWord)
    {
        $this->paymentInWord = $paymentInWord;
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
    public function getDue()
    {
        return $this->due;
    }

    /**
     * @param string $due
     */
    public function setDue($due)
    {
        $this->due = $due;
    }

    /**
     * @return Service
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param Service $service
     */
    public function setService($service)
    {
        $this->service = $service;
    }

    /**
     * @return Particular
     */
    public function getReferredDoctor()
    {
        return $this->referredDoctor;
    }

    /**
     * @param Particular $referredDoctor
     */
    public function setReferredDoctor($referredDoctor)
    {
        $this->referredDoctor = $referredDoctor;
    }

    /**
     * @return DateTime
     */
    public function getDeliveryDate()
    {
        return $this->deliveryDate;
    }

    /**
     * @param DateTime $deliveryDate
     */
    public function setDeliveryDate($deliveryDate)
    {
        $this->deliveryDate = $deliveryDate;
    }

    /**
     * @return string
     */
    public function getDeliveryTime()
    {
        return $this->deliveryTime;
    }

    /**
     * @param string $deliveryTime
     */
    public function setDeliveryTime($deliveryTime)
    {
        $this->deliveryTime = $deliveryTime;
    }

    /**
     * @return int
     */
    public function getPercentage()
    {
        return $this->percentage;
    }

    /**
     * @param int $percentage
     */
    public function setPercentage($percentage)
    {
        $this->percentage = $percentage;
    }

    /**
     * @return string
     */
    public function getNetTotal()
    {
        return $this->netTotal;
    }

    /**
     * @param string $netTotal
     */
    public function setNetTotal($netTotal)
    {
        $this->netTotal = $netTotal;
    }

    /**
     * @return string
     */
    public function getReferredCommission()
    {
        return $this->referredCommission;
    }

    /**
     * @param string $referredCommission
     */
    public function setReferredCommission($referredCommission)
    {
        $this->referredCommission = $referredCommission;
    }

    /**
     * @return string
     */
    public function getDeliveryDateTime()
    {
        return $this->deliveryDateTime;
    }

    /**
     * @param string $deliveryDateTime
     */
    public function setDeliveryDateTime($deliveryDateTime)
    {
        $this->deliveryDateTime = $deliveryDateTime;
    }

    /**
     * @return string
     */
    public function getPrintFor()
    {
        return $this->printFor;
    }

    /**
     * @param string $printFor
     */
    public function setPrintFor($printFor)
    {
        $this->printFor = $printFor;
    }


}

