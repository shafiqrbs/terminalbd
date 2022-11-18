<?php

namespace Appstore\Bundle\ProcurementBundle\Entity;
use Appstore\Bundle\AccountingBundle\Entity\AccountVendor;
use Appstore\Bundle\AssetsBundle\Entity\Item;
use Appstore\Bundle\ProcurementBundle\Entity\PurchaseOrderItem;
use Core\UserBundle\Entity\User;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\ToolBundle\Entity\ItemWarning;


/**
 * VoucherItem
 *
 * @ORM\Table(name ="pro_requisition_item")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\ProcurementBundle\Repository\RequisitionItemRepository")
 */
class RequisitionItem
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\ProcurementBundle\Entity\ProcurementConfig")
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $config;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AssetsBundle\Entity\Item", inversedBy="purchaseItems" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $item;


     /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User")
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $approvedBy;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\ProcurementBundle\Entity\Requisition", inversedBy="requisitionItems" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $requisition;


    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="remark", type="text", nullable=true)
     */
    private $remark;


    /**
     * @var string
     *
     * @ORM\Column(name="mode", type="string", length=50, nullable=true)
     */
    private $mode = "requisition";

    /**
     * @var string
     *
     * @ORM\Column(name="process", type="string", length=50, nullable=true)
     */
    private $process = "In-progress";


    /**
     * @var float
     *
     * @ORM\Column(name="quantity", type="integer",nullable=true)
     */
    private $quantity;


    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float", nullable = true)
     */
    private $price;



    /**
     * @var float
     *
     * @ORM\Column(name="customsDuty", type="float", nullable=true)
     */
    private $customsDuty = 0.00;


    /**
     * @var float
     *
     * @ORM\Column(name="customsDutyPercent", type="float", nullable=true)
     */
    private $customsDutyPercent = 0.00;


    /**
     * @var float
     *
     * @ORM\Column(name="supplementaryDuty", type="float", nullable=true)
     */
    private $supplementaryDuty = 0.00;

    /**
     * @var float
     *
     * @ORM\Column(name="supplementaryDutyPercent", type="float", nullable=true)
     */
    private $supplementaryDutyPercent = 0.00;

    /**
     * @var float
     *
     * @ORM\Column(name="valueAddedTax", type="float", nullable=true)
     */
    private $valueAddedTax = 0.00;


     /**
     * @var float
     *
     * @ORM\Column(name="valueAddedTaxPercent", type="float", nullable=true)
     */
    private $valueAddedTaxPercent = 0.00;


    /**
     * @var float
     *
     * @ORM\Column(name="advanceIncomeTax", type="float", nullable=true)
     */
    private $advanceIncomeTax = 0.00;


    /**
     * @var float
     *
     * @ORM\Column(name="advanceIncomeTaxPercent", type="float", nullable=true)
     */
    private $advanceIncomeTaxPercent = 0.00;


    /**
     * @var float
     *
     * @ORM\Column(name="recurringDeposit", type="float", nullable=true)
     */
    private $recurringDeposit = 0.00;

    /**
     * @var float
     *
     * @ORM\Column(name="recurringDepositPercent", type="float", nullable=true)
     */
    private $recurringDepositPercent = 0.00;


    /**
     * @var float
     *
     * @ORM\Column(name="advanceTradeVat", type="float", nullable=true)
     */
    private $advanceTradeVat = 0.00;


    /**
     * @var float
     *
     * @ORM\Column(name="advanceTradeVatPercent", type="float", nullable=true)
     */
    private $advanceTradeVatPercent = 0.00;


    /**
     * @var float
     *
     * @ORM\Column(name="totalTaxIncidence", type="float", nullable=true)
     */
    private $totalTaxIncidence = 0.00;


    /**
     * @var float
     *
     * @ORM\Column(name="rebatePercent", type="float", nullable=true)
     */
    private $rebatePercent = 0.00;


    /**
     * @var float
     *
     * @ORM\Column(name="rebate", type="float", nullable=true)
     */
    private $rebate = 0.00;


    /**
     * @var integer
     *
     * @ORM\Column(name="code", type="integer", nullable = true)
     */
    private $code;


    /**
     * @var float
     *
     * @ORM\Column(name="subTotal", type="float", nullable = true)
     */
    private $subTotal;


    /**
     * @var float
     *
     * @ORM\Column(name="total", type="float", nullable = true)
     */
    private $total;


    /**
     * @var string
     *
     * @ORM\Column(name="barcode", type="string",  nullable = true)
     */
    private $barcode;


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
     * @return string
     */
    public function getBarcode()
    {
        return $this->barcode;
    }

    /**
     * @param string $barcode
     */
    public function setBarcode($barcode)
    {
        $this->barcode = $barcode;
    }

    /**
     * @return DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param DateTime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param DateTime $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param string $mode
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
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
     * @return Item
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param Item $item
     */
    public function setItem($item)
    {
        $this->item = $item;
    }

    /**
     * @return PurchaseOrderItem
     */
    public function getPurchaseOrderItem()
    {
        return $this->purchaseOrderItem;
    }


    /**
     * @return float
     */
    public function getCustomsDuty()
    {
        return $this->customsDuty;
    }

    /**
     * @param float $customsDuty
     */
    public function setCustomsDuty($customsDuty)
    {
        $this->customsDuty = $customsDuty;
    }

    /**
     * @return float
     */
    public function getSupplementaryDuty()
    {
        return $this->supplementaryDuty;
    }

    /**
     * @param float $supplementaryDuty
     */
    public function setSupplementaryDuty($supplementaryDuty)
    {
        $this->supplementaryDuty = $supplementaryDuty;
    }

    /**
     * @return float
     */
    public function getValueAddedTax()
    {
        return $this->valueAddedTax;
    }

    /**
     * @param float $valueAddedTax
     */
    public function setValueAddedTax($valueAddedTax)
    {
        $this->valueAddedTax = $valueAddedTax;
    }

    /**
     * @return float
     */
    public function getAdvanceIncomeTax()
    {
        return $this->advanceIncomeTax;
    }

    /**
     * @param float $advanceIncomeTax
     */
    public function setAdvanceIncomeTax($advanceIncomeTax)
    {
        $this->advanceIncomeTax = $advanceIncomeTax;
    }

    /**
     * @return float
     */
    public function getRecurringDeposit()
    {
        return $this->recurringDeposit;
    }

    /**
     * @param float $recurringDeposit
     */
    public function setRecurringDeposit($recurringDeposit)
    {
        $this->recurringDeposit = $recurringDeposit;
    }

    /**
     * @return float
     */
    public function getAdvanceTradeVat()
    {
        return $this->advanceTradeVat;
    }

    /**
     * @param float $advanceTradeVat
     */
    public function setAdvanceTradeVat($advanceTradeVat)
    {
        $this->advanceTradeVat = $advanceTradeVat;
    }

    /**
     * @return float
     */
    public function getTotalTaxIncidence()
    {
        return $this->totalTaxIncidence;
    }

    /**
     * @param float $totalTaxIncidence
     */
    public function setTotalTaxIncidence($totalTaxIncidence)
    {
        $this->totalTaxIncidence = $totalTaxIncidence;
    }

    /**
     * @return float
     */
    public function getAdvanceTradeVatPercent()
    {
        return $this->advanceTradeVatPercent;
    }

    /**
     * @param float $advanceTradeVatPercent
     */
    public function setAdvanceTradeVatPercent($advanceTradeVatPercent)
    {
        $this->advanceTradeVatPercent = $advanceTradeVatPercent;
    }

    /**
     * @return float
     */
    public function getRecurringDepositPercent()
    {
        return $this->recurringDepositPercent;
    }

    /**
     * @param float $recurringDepositPercent
     */
    public function setRecurringDepositPercent($recurringDepositPercent)
    {
        $this->recurringDepositPercent = $recurringDepositPercent;
    }

    /**
     * @return float
     */
    public function getAdvanceIncomeTaxPercent()
    {
        return $this->advanceIncomeTaxPercent;
    }

    /**
     * @param float $advanceIncomeTaxPercent
     */
    public function setAdvanceIncomeTaxPercent($advanceIncomeTaxPercent)
    {
        $this->advanceIncomeTaxPercent = $advanceIncomeTaxPercent;
    }

    /**
     * @return float
     */
    public function getValueAddedTaxPercent()
    {
        return $this->valueAddedTaxPercent;
    }

    /**
     * @param float $valueAddedTaxPercent
     */
    public function setValueAddedTaxPercent($valueAddedTaxPercent)
    {
        $this->valueAddedTaxPercent = $valueAddedTaxPercent;
    }

    /**
     * @return float
     */
    public function getSupplementaryDutyPercent()
    {
        return $this->supplementaryDutyPercent;
    }

    /**
     * @param float $supplementaryDutyPercent
     */
    public function setSupplementaryDutyPercent($supplementaryDutyPercent)
    {
        $this->supplementaryDutyPercent = $supplementaryDutyPercent;
    }

    /**
     * @return float
     */
    public function getCustomsDutyPercent()
    {
        return $this->customsDutyPercent;
    }

    /**
     * @param float $customsDutyPercent
     */
    public function setCustomsDutyPercent($customsDutyPercent)
    {
        $this->customsDutyPercent = $customsDutyPercent;
    }


    /**
     * @return float
     */
    public function getRebate()
    {
        return $this->rebate;
    }

    /**
     * @param float $rebate
     */
    public function setRebate($rebate)
    {
        $this->rebate = $rebate;
    }

    /**
     * @return float
     */
    public function getRebatePercent()
    {
        return $this->rebatePercent;
    }

    /**
     * @param float $rebatePercent
     */
    public function setRebatePercent($rebatePercent)
    {
        $this->rebatePercent = $rebatePercent;
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
     * @return mixed
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param mixed $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return mixed
     */
    public function getRequisition()
    {
        return $this->requisition;
    }

    /**
     * @param mixed $requisition
     */
    public function setRequisition($requisition)
    {
        $this->requisition = $requisition;
    }




}

