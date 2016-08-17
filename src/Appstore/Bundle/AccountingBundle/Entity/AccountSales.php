<?php

namespace Appstore\Bundle\AccountingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * AccountSales
 *
 * @ORM\Table()
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountHead", inversedBy="accountSales" )
     **/
    private  $accountHead;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\Transaction", mappedBy="accountSales" )
     * @ORM\OrderBy({"id" = "DESC"})
     **/
    private  $transactions;


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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\SalesReturn", inversedBy="accountSales" )
     **/
    private  $salesReturn;


    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\Bank", inversedBy="accountSales" )
     **/
    private  $bank;

    /**
     * @var string
     *
     * @ORM\Column(name="paymentMethod", type="string", length=50, nullable=true)
     */
    private $paymentMethod;


    /**
     * @var string
     *
     * @ORM\Column(name="toIncrease", type="string", length=255)
     */
    private $toIncrease;

    /**
     * @var string
     *
     * @ORM\Column(name="accountNo", type="string", length=255, nullable = true)
     */
    private $accountNo;

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
     * @ORM\Column(name="dueAmount", type="float", nullable=true)
     */
    private $dueAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="returnAmount", type="float", nullable=true)
     */
    private $returnAmount;



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
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * @param mixed $transaction
     */
    public function setTransaction($transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * @return mixed
     */
    public function getBank()
    {
        return $this->bank;
    }

    /**
     * @param mixed $bank
     */
    public function setBank($bank)
    {
        $this->bank = $bank;
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
    public function getAccountSales()
    {
        return $this->AccountSales;
    }

    /**
     * @param mixed $AccountSales
     */
    public function setAccountSales($AccountSales)
    {
        $this->AccountSales = $AccountSales;
    }

    /**
     * @return string
     * Debit
     * Credit
     */
    public function getToIncrease()
    {
        return $this->toIncrease;
    }

    /**
     * @param string $toIncrease
     */
    public function setToIncrease($toIncrease)
    {
        $this->toIncrease = $toIncrease;
    }

    /**
     * @return string
     */
    public function getAccountNo()
    {
        return $this->accountNo;
    }

    /**
     * @param string $accountNo
     */
    public function setAccountNo($accountNo)
    {
        $this->accountNo = $accountNo;
    }

    /**
     * @return mixed
     */
    public function getAccountHead()
    {
        return $this->accountHead;
    }

    /**
     * @param mixed $accountHead
     */
    public function setAccountHead($accountHead)
    {
        $this->accountHead = $accountHead;
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
     * @return mixed
     */
    public function getSalesReturn()
    {
        return $this->salesReturn;
    }

    /**
     * @param mixed $salesReturn
     */
    public function setSalesReturn($salesReturn)
    {
        $this->salesReturn = $salesReturn;
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
     * @param float $totalAmount
     */
    public function setTotalAmount($totalAmount)
    {
        $this->totalAmount = $totalAmount;
    }

    /**
     * @return float
     */
    public function getDueAmount()
    {
        return $this->dueAmount;
    }

    /**
     * @param float $dueAmount
     */
    public function setDueAmount($dueAmount)
    {
        $this->dueAmount = $dueAmount;
    }

    /**
     * @return float
     */
    public function getReturnAmount()
    {
        return $this->returnAmount;
    }

    /**
     * @param float $returnAmount
     */
    public function setReturnAmount($returnAmount)
    {
        $this->returnAmount = $returnAmount;
    }

    /**
     * @return mixed
     */
    public function getGlobalOption()
    {
        return $this->globalOption;
    }

    /**
     * @param mixed $globalOption
     */
    public function setGlobalOption($globalOption)
    {
        $this->globalOption = $globalOption;
    }

    /**
     * @return mixed
     */
    public function getTransactions()
    {
        return $this->transactions;
    }
}

