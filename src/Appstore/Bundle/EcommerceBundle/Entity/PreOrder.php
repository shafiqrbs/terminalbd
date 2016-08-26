<?php

namespace Appstore\Bundle\EcommerceBundle\Entity;

use Core\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\ToolBundle\Entity\PaymentType;

/**
 * PreOrder
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Appstore\Bundle\EcommerceBundle\Repository\PreOrderRepository")
 */
class PreOrder
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\EcommerceConfig", inversedBy="preOrders")
     */
    protected $ecommerceConfig;

    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="preOrders" )
     **/
    private  $createdBy;

    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="preOrderProcess" )
     **/

    private $processBy;

     /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="preOrderApproved")
     **/

    private $approvedBy;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\PreOrderItem", mappedBy="preOrder"  , cascade={"persist", "remove"} )
     * @ORM\OrderBy({"updated" = "DESC"})
     **/
    private  $preOrderItems;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\PaymentMethod", inversedBy="preOrders")
     */
    protected $paymentMethod;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\PaymentType", inversedBy="preOrders")
     */
    protected $paymentType;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\BankAccount", inversedBy="preOrders")
     */
    protected $bankAccount;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\BkashAccount", inversedBy="preOrders")
     */

    protected $bkashAccount;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer",  nullable=true)
     */
    private $quantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="item", type="integer",  nullable=true)
     */
    private $item;

    /**
     * @var float
     *
     * @ORM\Column(name="dollar", type="float",  nullable=true)
     */
    private $dollar;


    /**
     * @var float
     *
     * @ORM\Column(name="total", type="float",  nullable=true)
     */
    private $total;


    /**
     * @var float
     *
     * @ORM\Column(name="grandTotal", type="float",  nullable=true)
     */
    private $grandTotal;

    /**
     * @var float
     *
     * @ORM\Column(name="shippingCharge", type="float",  nullable=true)
     */
    private $shippingCharge;


    /**
     * @var float
     *
     * @ORM\Column(name="paidAmount", type="float",  nullable=true)
     */
    private $paidAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="advanceAmount", type="float",  nullable=true)
     */
    private $advanceAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="dueAmount", type="float",  nullable=true)
     */
    private $dueAmount;


    /**
     * @var string
     *
     * @ORM\Column(name="process", type="string", length=255,  nullable=true)
     */
    private $process = 'created';

    /**
     * @var string
     *
     * @ORM\Column(name="delivery", type="string", length=255,  nullable=true)
     */
    private $delivery = 'pickup';

    /**
     * @var text
     *
     * @ORM\Column(name="address", type="text", nullable=true)
     */
    private $address;


    /**
     * @var integer
     *
     * @ORM\Column(name="code", type="integer",  nullable=true)
     */
    private $code;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status = false;

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
     * @var \DateTime
      * @ORM\Column(name="deliveryDate", type="datetime" , nullable=true)
     */
    private $deliveryDate ;

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
     * @var string
     *
     * @ORM\Column(name="invoice", type="string", length=50, nullable=true)
     */
    private $invoice;

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return PreOrder
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set item
     *
     * @param integer $item
     *
     * @return PreOrder
     */
    public function setItem($item)
    {
        $this->item = $item;

        return $this;
    }

    /**
     * Get item
     *
     * @return integer
     */
    public function getItem()
    {
        return $this->item;
    }



    /**
     * Set advanceAmount
     *
     * @param float $advanceAmount
     *
     * @return PreOrder
     */
    public function setAdvanceAmount($advanceAmount)
    {
        $this->advanceAmount = $advanceAmount;

        return $this;
    }

    /**
     * Get advanceAmount
     *
     * @return float
     */
    public function getAdvanceAmount()
    {
        return $this->advanceAmount;
    }

    /**
     * Set dueAmount
     *
     * @param float $dueAmount
     *
     * @return PreOrder
     */
    public function setDueAmount($dueAmount)
    {
        $this->dueAmount = $dueAmount;

        return $this;
    }

    /**
     * Get dueAmount
     *
     * @return float
     */
    public function getDueAmount()
    {
        return $this->dueAmount;
    }


    /**
     * Set process
     *
     * @param string $process
     *
     * @return PreOrder
     */
    public function setProcess($process)
    {
        $this->process = $process;

        return $this;
    }

    /**
     * Get process
     *
     * @return string
     */
    public function getProcess()
    {
        return $this->process;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return PreOrder
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
     * @param mixed $customer
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
    }

    /**
     * @return mixed
     */
    public function getPreOrderItems()
    {
        return $this->preOrderItems;
    }

    /**
     * @return float
     */
    public function getPaidAmount()
    {
        return $this->paidAmount;
    }

    /**
     * @param float $paidAmount
     */
    public function setPaidAmount($paidAmount)
    {
        $this->paidAmount = $paidAmount;
    }

    /**
     * @return mixed
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * @param mixed $paymentMethod
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
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
    public function getProcessBy()
    {
        return $this->processBy;
    }

    /**
     * @param User $processBy
     */
    public function setProcessBy($processBy)
    {
        $this->processBy = $processBy;
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
     * @return float
     */
    public function getShippingCharge()
    {
        return $this->shippingCharge;
    }

    /**
     * @param float $shippingCharge
     */
    public function setShippingCharge($shippingCharge)
    {
        $this->shippingCharge = $shippingCharge;
    }

    /**
     * @return float
     */
    public function getDollar()
    {
        return $this->dollar;
    }

    /**
     * @param float $dollar
     */
    public function setDollar($dollar)
    {
        $this->dollar = $dollar;
    }

    /**
     * @return float
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param float $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * @return float
     */
    public function getGrandTotal()
    {
        return $this->grandTotal;
    }

    /**
     * @param float $grandTotal
     */
    public function setGrandTotal($grandTotal)
    {
        $this->grandTotal = $grandTotal;
    }

    /**
     * @return EcommerceConfig
     */
    public function getEcommerceConfig()
    {
        return $this->ecommerceConfig;
    }

    /**
     * @param EcommerceConfig $ecommerceConfig
     */
    public function setEcommerceConfig($ecommerceConfig)
    {
        $this->ecommerceConfig = $ecommerceConfig;
    }

    /**
     * @return string
     */
    public function getDelivery()
    {
        return $this->delivery;
    }

    /**
     * @param string $delivery
     */
    public function setDelivery($delivery)
    {
        $this->delivery = $delivery;
    }

    /**
     * @return text
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param text $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    public function getCheckRoleEcommercePreorder($role = NULL)
    {

       $roles = array(
            'ROLE_DOMAIN_INVENTORY_ECOMMERCE',
            'ROLE_DOMAIN_INVENTORY_ECOMMERCE_MANAGER',
            'ROLE_DOMAIN_INVENTORY_MANAGER',
            'ROLE_DOMAIN_INVENTORY_APPROVE',
            'ROLE_DOMAIN_MANAGER',
            'ROLE_DOMAIN'
        );

        if(in_array($role,$roles)){
            return true;
        }else{
            return false;
        }

    }

    /**
     * @return BankAccount
     */
    public function getBankAccount()
    {
        return $this->bankAccount;
    }

    /**
     * @param BankAccount $bankAccount
     */
    public function setBankAccount($bankAccount)
    {
        $this->bankAccount = $bankAccount;
    }

    /**
     * @return BkashAccount
     */
    public function getBkashAccount()
    {
        return $this->bkashAccount;
    }

    /**
     * @param BkashAccount $bkashAccount
     */
    public function setBkashAccount($bkashAccount)
    {
        $this->bkashAccount = $bkashAccount;
    }

    /**
     * @return PaymentType
     */
    public function getPaymentType()
    {
        return $this->paymentType;
    }

    /**
     * @param PaymentType $paymentType
     */
    public function setPaymentType($paymentType)
    {
        $this->paymentType = $paymentType;
    }

    /**
     * @return mixed
     */
    public function getDeliveryDate()
    {
        return $this->deliveryDate;
    }

    /**
     * @param mixed $deliveryDate
     */
    public function setDeliveryDate($deliveryDate)
    {
        $this->deliveryDate = $deliveryDate;
    }

}

