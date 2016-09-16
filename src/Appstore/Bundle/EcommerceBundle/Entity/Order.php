<?php

namespace Appstore\Bundle\EcommerceBundle\Entity;

use Core\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\ToolBundle\Entity\PaymentType;

/**
 * Order
 *
 * @ORM\Table("orders")
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
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\PaymentType", inversedBy="orders")
     */
    protected $paymentType;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\BankAccount", inversedBy="orders")
     */
    protected $bankAccount;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\BkashAccount", inversedBy="orders")
     */

    protected $bkashAccount;

    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="orderProcess" )
     **/

    private $processBy;

    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="orderApproved")
     **/

    private $approvedBy;


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
    private $delivery = 'delivery';

    /**
     * @var \DateTime
     * @ORM\Column(name="deliveryDate", type="datetime" , nullable=true)
     */
    private $deliveryDate ;

    /**
     * @var text
     *
     * @ORM\Column(name="address", type="text", nullable=true)
     */
    private $address;

    /**
     * @var text
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;


    /**
     * @var string
     *
     * @ORM\Column(name="invoice", type="string", length=255 , nullable = true)
     */
    private $invoice;

    /**
     * @var float
     *
     * @ORM\Column(name="shippingCharge", type="float", nullable = true)
     */
    private $shippingCharge;

    /**
     * @var float
     *
     * @ORM\Column(name="totalAmount", type="float", nullable = true)
     */
    private $totalAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="grandTotalAmount", type="float", nullable = true)
     */
    private $grandTotalAmount;

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
     * @var integer
     *
     * @ORM\Column(name="item", type="integer" , nullable = true)
     */
    private $item;


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
     * @return int
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param int $item
     */
    public function setItem($item)
    {
        $this->item = $item;
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
    public function getGrandTotalAmount()
    {
        return $this->grandTotalAmount;
    }

    /**
     * @param float $grandTotalAmount
     */
    public function setGrandTotalAmount($grandTotalAmount)
    {
        $this->grandTotalAmount = $grandTotalAmount;
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
     * @return \DateTime
     */
    public function getDeliveryDate()
    {
        return $this->deliveryDate;
    }

    /**
     * @param \DateTime $deliveryDate
     */
    public function setDeliveryDate($deliveryDate)
    {
        $this->deliveryDate = $deliveryDate;
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
     * @return text
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param text $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

}

