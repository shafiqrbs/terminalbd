<?php

namespace Appstore\Bundle\AccountingBundle\Entity;

use Appstore\Bundle\EcommerceBundle\Entity\Order;
use Appstore\Bundle\EcommerceBundle\Entity\PreOrder;
use Appstore\Bundle\EcommerceBundle\Entity\PreOrderPayment;
use Appstore\Bundle\HospitalBundle\Entity\DoctorInvoice;
use Appstore\Bundle\HospitalBundle\Entity\Invoice;
use Appstore\Bundle\HospitalBundle\Entity\InvoiceTransaction;
use Appstore\Bundle\InventoryBundle\Entity\Purchase;
use Appstore\Bundle\InventoryBundle\Entity\Sales;
use Appstore\Bundle\InventoryBundle\Entity\ServiceSales;
use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\ToolBundle\Entity\Bank;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * AccountMobileBank
 *
 *
 * @ORM\Table(name="account_mobile_bank")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\AccountingBundle\Repository\AccountMobileBankRepository")
 */
class AccountMobileBank
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
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="accountMobileBank")
     **/
    protected $globalOption;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountJournal", mappedBy="accountMobileBank"  )
     **/
    private  $accountJournals;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountCash", mappedBy="accountMobileBank"  )
     **/
    private  $accountCashes;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountPurchase", mappedBy="accountMobileBank"  )
     **/
    private  $accountPurchases;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountSales", mappedBy="accountMobileBank"  )
     **/
    private  $accountSales;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Sales", mappedBy="accountMobileBank"  )
     **/
    private  $sales;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Purchase", mappedBy="accountMobileBank"  )
     **/
    private  $purchases;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\ServiceSales", mappedBy="accountMobileBank" )
     * @ORM\OrderBy({"id" = "DESC"})
     **/
    private  $serviceSales;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\PaymentSalary", mappedBy="accountMobileBank"  )
     **/
    private  $paymentSalaries;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\Expenditure", mappedBy="accountMobileBank"  )
     **/
    private  $expendituries;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\PettyCash", mappedBy="accountMobileBank"  )
     **/
    private  $pettyCash;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountOnlineOrder", mappedBy="accountMobileBank"  )
     **/
    private  $accountOnlineOrders;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\OrderPayment", mappedBy="accountMobileBank"  )
     **/
    private  $orderPayments;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\PreOrderPayment", mappedBy="accountMobileBank"  )
     **/
    private  $preOrderPayments;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\Invoice", mappedBy="accountMobileBank" , cascade={"persist", "remove"})
     */
    protected $hmsInvoices;

     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\DoctorInvoice", mappedBy="accountMobileBank" , cascade={"persist", "remove"})
     */
    protected $doctorInvoices;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\InvoiceTransaction", mappedBy="accountMobileBank" )
     */
    protected $invoiceTransactions;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\RestaurantBundle\Entity\Invoice", mappedBy="accountMobileBank" )
     */
    protected $restaurantInvoices;

   /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\RestaurantBundle\Entity\Purchase", mappedBy="accountMobileBank" )
     */
    protected $restaurantPurchase;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\DmsBundle\Entity\DmsInvoice", mappedBy="accountMobileBank" )
     */
    protected $dmsInvoices;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\DmsBundle\Entity\DmsDoctorInvoice", mappedBy="accountMobileBank" )
     */
    protected $dmsDoctorInvoices;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\DmsBundle\Entity\DmsPurchase", mappedBy="accountMobileBank" )
     */
    protected $dmsPurchases;

    /**
     * @var string
     *
     * @ORM\Column(name="accountOwner", type="string", length=255, nullable=true)
     */
    private $accountOwner;


    /**
     * @var string
     *
     * @ORM\Column(name="authorised", type="string", length=255, nullable=true)
     */
    private $authorised;

    /**
     * @var string
     *
     * @ORM\Column(name="serviceName", type="string", length=255, nullable=true)
     */
    private $serviceName;


    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="mobile", type="string", length=255, nullable=true)
     */
    private $mobile;


    /**
     * @var string
     *
     * @ORM\Column(name="accountType", type="string", length=255, nullable=true)
     */
    private $accountType;


    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status = true;


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
     * @return string
     */
    public function getAccountType()
    {
        return $this->accountType;
    }

    /**
     * @param string $accountType
     */
    public function setAccountType($accountType)
    {
        $this->accountType = $accountType;
    }

    /**
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param boolean $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return AccountJournal
     */
    public function getAccountJournals()
    {
        return $this->accountJournals;
    }

    /**
     * @return AccountCash
     */
    public function getAccountCashes()
    {
        return $this->accountCashes;
    }

    /**
     * @return AccountPurchase
     */
    public function getAccountPurchases()
    {
        return $this->accountPurchases;
    }

    /**
     * @return AccountSales
     */
    public function getAccountSales()
    {
        return $this->accountSales;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return Expenditure
     */
    public function getExpendituries()
    {
        return $this->expendituries;
    }

    /**
     * @return PaymentSalary
     */
    public function getPaymentSalaries()
    {
        return $this->paymentSalaries;
    }

    /**
     * @param PaymentSalary $paymentSalaries
     */
    public function setPaymentSalaries($paymentSalaries)
    {
        $this->paymentSalaries = $paymentSalaries;
    }

    /**
     * @return PettyCash
     */
    public function getPettyCash()
    {
        return $this->pettyCash;
    }

    /**
     * @param PettyCash $pettyCash
     */
    public function setPettyCash($pettyCash)
    {
        $this->pettyCash = $pettyCash;
    }

    /**
     * @return GlobalOption
     */
    public function getGlobalOption()
    {
        return $this->globalOption;
    }

    /**
     * @param GlobalOption $globalOption
     */
    public function setGlobalOption($globalOption)
    {
        $this->globalOption = $globalOption;
    }

    /**
     * @return Sales
     */
    public function getSales()
    {
        return $this->sales;
    }

    /**
     * @return string
     */
    public function getAccountOwner()
    {
        return $this->accountOwner;
    }

    /**
     * @param string $accountOwner
     */
    public function setAccountOwner($accountOwner)
    {
        $this->accountOwner = $accountOwner;
    }

    /**
     * @return string
     */
    public function getAuthorised()
    {
        return $this->authorised;
    }

    /**
     * @param string $authorised
     */
    public function setAuthorised($authorised)
    {
        $this->authorised = $authorised;
    }

    /**
     * @return string
     */
    public function getServiceName()
    {
        return $this->serviceName;
    }

    /**
     * @param string $serviceName
     */
    public function setServiceName($serviceName)
    {
        $this->serviceName = $serviceName;
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
     * @return ServiceSales
     */
    public function getServiceSales()
    {
        return $this->serviceSales;
    }

    /**
     * @return Purchase
     */
    public function getPurchases()
    {
        return $this->purchases;
    }

    /**
     * @return mixed
     */
    public function getAccountOnlineOrders()
    {
        return $this->accountOnlineOrders;
    }

    /**
     * @return Invoice
     */
    public function getHmsInvoices()
    {
        return $this->hmsInvoices;
    }

    /**
     * @return DoctorInvoice
     */
    public function getDoctorInvoices()
    {
        return $this->doctorInvoices;
    }

    /**
     * @return InvoiceTransaction
     */
    public function getInvoiceTransactions()
    {
        return $this->invoiceTransactions;
    }

    /**
     * @return PreOrderPayment
     */
    public function getPreOrderPayments()
    {
        return $this->preOrderPayments;
    }

    /**
     * @return OrderPayment
     */
    public function getOrderPayments()
    {
        return $this->orderPayments;
    }

    /**
     * @return mixed
     */
    public function getRestaurantInvoices()
    {
        return $this->restaurantInvoices;
    }


}

