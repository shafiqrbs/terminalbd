<?php

namespace Appstore\Bundle\AccountingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Setting\Bundle\ToolBundle\Entity\TransactionMethod;

/**
 * AccountSales
 *
 * @ORM\Table(name="account_sales")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\AccountingBundle\Repository\AccountSalesRepository")
 */
class AccountSales
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\InventoryConfig", inversedBy="accountSales" )
     **/
    private  $inventoryConfig;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="accountSales")
     **/

    protected $globalOption;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\TransactionMethod", inversedBy="accountSales" )
     **/
    private  $transactionMethod;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountBank", inversedBy="accountSales" )
     **/
    private  $accountBank;

    /**
     * @ORM\OneToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountCash", mappedBy="accountSales" )
     **/
    private  $accountCash;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountBkash", inversedBy="accountSales" )
     **/
    private  $accountBkash;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\DomainUserBundle\Entity\Customer", inversedBy="accountSales" , cascade={"detach","merge"} )
     **/
    private  $customer;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Sales", inversedBy="accountSales" )
     **/
    private  $sales;

    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="accountSales" )
     **/
    private  $createdBy;

    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="salesApprove" )
     **/
    private  $approvedBy;


    /**
     * @var string
     *
     * @ORM\Column(name="processHead", type="string", length=255, nullable = true)
     */
    private $processHead;


    /**
     * @var float
     *
     * @ORM\Column(name="totalAmount", type="float", nullable=true)
     */
    private $totalAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float" , nullable=true)
     */
    private $amount;


    /**
     * @var float
     *
     * @ORM\Column(name="balance", type="float", nullable=true)
     */
    private $balance = 0;


    /**
     * @var string
     *
     * @ORM\Column(name="accountRefNo", type="string", length=50, nullable=true)
     */
    private $accountRefNo;

    /**
     * @var integer
     *
     * @ORM\Column(name="code", type="integer",  nullable=true)
     */
    private $code;



   /**
     * @var datetime
     *
     * @ORM\Column(name="receiveDate", type="datetime", nullable=true)
     */
    private $receiveDate;

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
     * @var string
     *
     * @ORM\Column(name="process", type="string", length=255, nullable = true)
     */
    private $process;

    /**
     * @var string
     *
     * @ORM\Column(name="remark", type="text",  nullable = true)
     */
    private $remark;



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
     * Set amount
     *
     * @param float $amount
     *
     * @return AccountSales
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
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
     * @return mixed
     */
    public function getInventoryConfig()
    {
        return $this->inventoryConfig;
    }

    /**
     * @param mixed $inventoryConfig
     */
    public function setInventoryConfig($inventoryConfig)
    {
        $this->inventoryConfig = $inventoryConfig;
    }

    /**
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param mixed $createdBy
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     * @return datetime
     */
    public function getReceiveDate()
    {
        return $this->receiveDate;
    }

    /**
     * @param datetime $receiveDate
     */
    public function setReceiveDate($receiveDate)
    {
        $this->receiveDate = $receiveDate;
    }

    /**
     * @return mixed
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param mixed $customer
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
    }

    /**
     * @return mixed
     */
    public function getSales()
    {
        return $this->sales;
    }

    /**
     * @param mixed $sales
     */
    public function setSales($sales)
    {
        $this->sales = $sales;
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
     * @return mixed
     */
    public function getApprovedBy()
    {
        return $this->approvedBy;
    }

    /**
     * @param mixed $approvedBy
     */
    public function setApprovedBy($approvedBy)
    {
        $this->approvedBy = $approvedBy;
    }

    /**
     * @return float
     */
    public function getTotalAmount()
    {
        return $this->totalAmount;
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
     * @return string
     */
    public function getAccountRefNo()
    {
        return $this->accountRefNo;
    }

    /**
     * @param string $accountRefNo
     */
    public function setAccountRefNo($accountRefNo)
    {
        $this->accountRefNo = $accountRefNo;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param int $code
     */
    public function setCode($code)
    {
        $this->code = $code;
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
     * @return AccountCash
     */
    public function getAccountCash()
    {
        return $this->accountCash;
    }

    /**
     * @param AccountCash $accountCash
     */
    public function setAccountCash($accountCash)
    {
        $this->accountCash = $accountCash;
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
     * @return AccountBkash
     */
    public function getAccountBkash()
    {
        return $this->accountBkash;
    }

    /**
     * @param AccountBkash $accountBkash
     */
    public function setAccountBkash($accountBkash)
    {
        $this->accountBkash = $accountBkash;
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
     * @param float $totalAmount
     */
    public function setTotalAmount($totalAmount)
    {
        $this->totalAmount = $totalAmount;
    }

    /**
     * @return string
     */
    public function getProcessHead()
    {
        return $this->processHead;
    }

    /**
     * @param string $processHead
     */
    public function setProcessHead($processHead)
    {
        $this->processHead = $processHead;
    }

    /**
     * @return string
     */
    public function getRemark()
    {
        return $this->remark;
    }

    /**
     * @param string $remark
     */
    public function setRemark($remark)
    {
        $this->remark = $remark;
    }
}

