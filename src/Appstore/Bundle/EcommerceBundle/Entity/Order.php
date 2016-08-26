<?php

namespace Appstore\Bundle\EcommerceBundle\Entity;

use Core\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Order
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Appstore\Bundle\EcommerceBundle\Repository\OrderRepository")
 */
class Order
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\EcommerceConfig", inversedBy="orders")
     */
    protected $ecommerceConfig;

    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="orders" )
     **/
    private  $createdBy;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\OrderItem", mappedBy="order"  , cascade={"remove"} )
     **/
    private  $orderItems;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\PaymentMethod", inversedBy="orders" , cascade={"persist", "remove"})
     */
    protected $paymentMethod;

    /**
     * @var string
     *
     * @ORM\Column(name="invoice", type="string", length=255)
     */
    private $invoice;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float", nullable = true)
     */
    private $amount;

    /**
     * @var float
     *
     * @ORM\Column(name="paidAmount", type="float" , nullable = true)
     */
    private $paidAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="dueAmount", type="float" , nullable = true)
     */
    private $dueAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="commissionAmount", type="float" , nullable = true)
     */
    private $commissionAmount;


    /**
     * @var string
     *
     * @ORM\Column(name="process", type="string", length=255)
     */
    private $process;

    /**
     * @var integer
     *
     * @ORM\Column(name="item", type="smallint")
     */
    private $item;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="smallint")
     */
    private $quantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="code", type="integer",  nullable=true)
     */
    private $code;

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
     * Set invoice
     *
     * @param string $invoice
     *
     * @return Order
     */
    public function setInvoice($invoice)
    {
        $this->invoice = $invoice;

        return $this;
    }

    /**
     * Get invoice
     *
     * @return string
     */
    public function getInvoice()
    {
        return $this->invoice;
    }


    /**
     * Set process
     *
     * @param string $process
     *
     * @return Order
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
     * Set item
     *
     * @param integer $item
     *
     * @return Order
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
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return Order
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
    public function getOrderItems()
    {
        return $this->orderItems;
    }


    /**
     * @param mixed $customer
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
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
    public function getCommissionAmount()
    {
        return $this->commissionAmount;
    }

    /**
     * @param float $commissionAmount
     */
    public function setCommissionAmount($commissionAmount)
    {
        $this->commissionAmount = $commissionAmount;
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
     * @return mixed
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

}

