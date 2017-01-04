<?php

namespace Appstore\Bundle\AccountingBundle\Entity;

use Appstore\Bundle\EcommerceBundle\Entity\Order;
use Appstore\Bundle\EcommerceBundle\Entity\PreOrder;
use Appstore\Bundle\InventoryBundle\Entity\Purchase;
use Appstore\Bundle\InventoryBundle\Entity\ServiceSales;
use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\ToolBundle\Entity\Bank;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * AccountBank
 *
 *
 * @ORM\Table(name="account_bank")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\AccountingBundle\Repository\AccountBankRepository")
 */
class AccountBank
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
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="accountBank")
     **/

    protected $globalOption;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\Bank", inversedBy="accountBanks" )
     **/
    private  $bank;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Purchase", mappedBy="accountBank" )
     **/
    private  $purchases;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Sales", mappedBy="accountBank" )
     **/
    private  $sales;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountJournal", mappedBy="accountBank"  )
     **/
    private  $accountJournals;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountCash", mappedBy="accountBank"  )
     **/
    private  $accountCashes;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountPurchase", mappedBy="accountBank"  )
     **/
    private  $accountPurchases;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountSales", mappedBy="accountBank"  )
     **/
    private  $accountSales;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\ServiceSales", mappedBy="accountBank" )
     * @ORM\OrderBy({"id" = "DESC"})
     **/
    private  $serviceSales;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\Expenditure", mappedBy="accountBank"  )
     **/
    private  $expendituries;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\PaymentSalary", mappedBy="accountBank"  )
     **/
    private  $paymentSalaries;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\PettyCash", mappedBy="accountBank"  )
     **/
    private  $pettyCash;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountOnlineOrder", mappedBy="accountBank"  )
     **/
    private  $accountOnlineOrders;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\Order", mappedBy="accountBank"  )
     **/
    private  $orders;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\PreOrder", mappedBy="accountBank"  )
     **/
    private  $preOrders;


    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;


    /**
     * @var string
     *
     * @ORM\Column(name="accountOwner", type="string", length=255, nullable=true)
     */
    private $accountOwner;

    /**
     * @var string
     *
     * @ORM\Column(name="branch", type="string", length=255, nullable=true)
     */
    private $branch;


    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     */
    private $address;


    /**
     * @var string
     *
     * @ORM\Column(name="accountNo", type="string", length=255)
     */
    private $accountNo;

    /**
     * @var string
     *
     * @ORM\Column(name="accountType", type="string", length=100)
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
     * Set accountNo
     *
     * @param string $accountNo
     *
     * @return AccountBank
     */
    public function setAccountNo($accountNo)
    {
        $this->accountNo = $accountNo;

        return $this;
    }

    /**
     * Get accountNo
     *
     * @return string mixed
     */
    public function getAccountNo()
    {
        return $this->accountNo;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return AccountBank
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
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
     * @return AccountJournal
     */
    public function getAccountJournals()
    {
        return $this->accountJournals;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getBranch()
    {
        return $this->branch;
    }

    /**
     * @param string $branch
     */
    public function setBranch($branch)
    {
        $this->branch = $branch;
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
     * @return mixed
     */
    public function getAccountSales()
    {
        return $this->accountSales;
    }

    /**
     * @return mixed
     */
    public function getSales()
    {
        return $this->sales;
    }

    /**
     * @return mixed
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
     * @return Order
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * @return PreOrder
     */
    public function getPreOrders()
    {
        return $this->preOrders;
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
     * @return AccountOnlineOrder
     */
    public function getAccountOnlineOrders()
    {
        return $this->accountOnlineOrders;
    }

}

