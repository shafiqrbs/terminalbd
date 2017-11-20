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
use Setting\Bundle\ToolBundle\Entity\PaymentCard;
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\HospitalBundle\Entity\HospitalConfig", inversedBy="hmsInvoices")
     **/
    private $hospitalConfig;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\InvoiceTransaction", mappedBy="hmsInvoice" , cascade={"remove"})
     * @ORM\OrderBy({"updated" = "DESC"})
     **/
    private $invoiceTransactions;

     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\DoctorInvoice", mappedBy="hmsInvoice" , cascade={"remove"})
     * @ORM\OrderBy({"updated" = "DESC"})
     **/
    private $doctorInvoices;

     /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\HospitalBundle\Entity\Service", inversedBy="hmsInvoices")
     **/
    private $service;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\InvoiceParticular", mappedBy="hmsInvoice" , cascade={"remove"} )
     * @ORM\OrderBy({"id" = "ASC"})
     **/
    private  $invoiceParticulars;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountSales", mappedBy="hmsInvoices" )
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\HospitalBundle\Entity\Particular", inversedBy="assignDoctorInvoices", cascade={"persist"}  )
     **/
    private  $assignDoctor;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\HospitalBundle\Entity\Particular", inversedBy="hmsInvoiceCabin", cascade={"persist"}  )
     **/
    private  $cabin;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\HospitalBundle\Entity\HmsCategory", inversedBy="hmsInvoices", cascade={"persist"}  )
     **/
    private  $department;


    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="hmsInvoiceCreatedBy" )
     **/
    private  $createdBy;


    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="hmsInvoiceDeliveredBy" )
     **/
    private  $deliveredBy;

    /**
     * @var string
     *
     * @ORM\Column(name="paymentMethod", type="string", length=30, nullable=true)
     */
    private $paymentMethod='Cash';


    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\TransactionMethod", inversedBy="hmsInvoices" )
     **/
    private  $transactionMethod;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\Bank", inversedBy="hmsInvoices" )
     **/
    private  $bank;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountBank", inversedBy="hmsInvoices" )
     **/
    private  $accountBank;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank", inversedBy="hmsInvoices" )
     **/
    private  $accountMobileBank;


    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\PaymentCard", inversedBy="hmsInvoices" )
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
    private $process ='Created';

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
     * @ORM\Column(name="cabinNo", type="string", length=50, nullable=true)
     */
    private $cabinNo;


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
     * @ORM\Column(name="grandTotal", type="decimal", nullable=true)
     */
    private $grandTotal;

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
     * @var float
     *
     * @ORM\Column(name="estimateCommission", type="float" , nullable=true)
     */
    private $estimateCommission;

    /**
     * @var string
     *
     * @ORM\Column(name="commission", type="decimal", nullable=true)
     */
    private $commission;


    /**
     * @var boolean
     *
     * @ORM\Column(name="commissionApproved", type="boolean" )
     */
    private $commissionApproved = false;


    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;

    /**
     * @var string
     *
     * @ORM\Column(name="disease", type="text", nullable=true)
     */
    private $disease;

    /**
     * @var string
     *
     * @ORM\Column(name="due", type="decimal", nullable=true)
     */
    private $due;

    /**
     * @var boolean
     *
     * @ORM\Column(name="revised", type="boolean" )
     */
    private $revised = false;


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
     * @ORM\Column(name="releaseDate", type="datetime")
     */
    private $releaseDate;

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
    private $printFor ='pathological';

    /**
     * @var string
     *
     * @ORM\Column(name="invoiceMode", type="string", length=50, nullable=true)
     */
    private $invoiceMode = 'pathological';

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
     * @param PaymentCard $paymentCard
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

    /**
     * @return Particular
     */
    public function getCabin()
    {
        return $this->cabin;
    }

    /**
     * @param mixed $cabin
     */
    public function setCabin($cabin)
    {
        $this->cabin = $cabin;
    }

    /**
     * @return string
     */
    public function getInvoiceMode()
    {
        return $this->invoiceMode;
    }

    /**
     * @param string $invoiceMode
     */
    public function setInvoiceMode($invoiceMode)
    {
        $this->invoiceMode = $invoiceMode;
    }

    /**
     * @return string
     */
    public function getDisease()
    {
        return $this->disease;
    }

    /**
     * @param string $disease
     */
    public function setDisease($disease)
    {
        $this->disease = $disease;
    }

    /**
     * @return Particular
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * @param Particular $department
     */
    public function setDepartment($department)
    {
        $this->department = $department;
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
     * @return InvoiceTransaction
     */
    public function getInvoiceTransactions()
    {
        return $this->invoiceTransactions;
    }

    /**
     * @return string
     */
    public function getCabinNo()
    {
        return $this->cabinNo;
    }

    /**
     * @param string $cabinNo
     */
    public function setCabinNo($cabinNo)
    {
        $this->cabinNo = $cabinNo;
    }

    /**
     * @return string
     */
    public function getGrandTotal()
    {
        return $this->grandTotal;
    }

    /**
     * @param string $grandTotal
     */
    public function setGrandTotal($grandTotal)
    {
        $this->grandTotal = $grandTotal;
    }

    /**
     * @return DoctorInvoice
     */
    public function getDoctorInvoices()
    {
        return $this->doctorInvoices;
    }

    /**
     * @return string
     */
    public function getCommission()
    {
        return $this->commission;
    }

    /**
     * @param string $commission
     */
    public function setCommission($commission)
    {
        $this->commission = $commission;
    }

    /**
     * @return bool
     */
    public function getCommissionApproved()
    {
        return $this->commissionApproved;
    }

    /**
     * @param bool $commissionApproved
     */
    public function setCommissionApproved($commissionApproved)
    {
        $this->commissionApproved = $commissionApproved;
    }

    /**
     * @return User
     */
    public function getDeliveredBy()
    {
        return $this->deliveredBy;
    }

    /**
     * @param User $deliveredBy
     */
    public function setDeliveredBy($deliveredBy)
    {
        $this->deliveredBy = $deliveredBy;
    }

    /**
     * @return boolean
     */
    public function getRevised()
    {
        return $this->revised;
    }

    /**
     * @param boolean $revised
     */
    public function setRevised($revised)
    {
        $this->revised = $revised;
    }

    /**
     * @return float
     */
    public function getEstimateCommission()
    {
        return $this->estimateCommission;
    }

    /**
     * @param float $estimateCommission
     */
    public function setEstimateCommission($estimateCommission)
    {
        $this->estimateCommission = $estimateCommission;
    }

    /**
     * @return \DateTime
     */
    public function getReleaseDate()
    {
        return $this->releaseDate;
    }

    /**
     * @param \DateTime $releaseDate
     */
    public function setReleaseDate($releaseDate)
    {
        $this->releaseDate = $releaseDate;
    }


}

