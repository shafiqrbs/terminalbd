<?php

namespace Appstore\Bundle\TallyBundle\Entity;

use Appstore\Bundle\AccountingBundle\Entity\AccountPurchase;
use Appstore\Bundle\ProcurementBundle\Entity\PurchaseOrderItem;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Setting\Bundle\ToolBundle\Event\Glo;

/**
 * VoucherItem
 *
 * @ORM\Table(name ="tally_purchase_item")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\TallyBundle\Repository\PurchaseItemRepository")
 */
class PurchaseItem
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\TallyBundle\Entity\Item", inversedBy="purchaseItems" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $item;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\ProcurementBundle\Entity\PurchaseOrderItem", inversedBy="purchaseItems" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $purchaseOrderItem;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\TallyBundle\Entity\Purchase", inversedBy="purchaseItems" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $purchase;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\TallyBundle\Entity\WearHouse", inversedBy="purchaseItems" )
     **/
    private  $wearHouse;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\TallyBundle\Entity\ItemWarning", inversedBy="purchaseItems")
     **/
    private  $itemWarning;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\TallyBundle\Entity\ItemMetaAttribute", mappedBy="purchaseItem" , cascade={"remove"}  )
     **/
    private  $itemMetaAttributes;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\TallyBundle\Entity\ItemKeyValue", inversedBy="purchaseItems" , cascade={"remove"}  )
     * @ORM\OrderBy({"sorting" = "ASC"})
     **/
    private  $itemKeyValues;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="purchaseItems" )
     **/
    private  $globalOption;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\TallyBundle\Entity\TallyConfig", inversedBy="purchaseItem" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private $config;


    /**
     * @var string
     *
     * @ORM\Column(name="assuranceType", type="string", length=50, nullable = true)
     */
    private $assuranceType;


    /**
     * @var datetime
     *
     * @ORM\Column(name="effectedDate", type="datetime", nullable=true)
     */
    private $effectedDate;

    /**
     * @var datetime
     *
     * @ORM\Column(name="expiredDate", type="datetime", nullable=true)
     */
    private $expiredDate;

    /**
     * @var array
     *
     * @ORM\Column(name="internalSerial", type="simple_array",  nullable = true)
     */
    private $internalSerial;

    /**
     * @var string
     *
     * @ORM\Column(name="externalSerial", type="text",  nullable = true)
     */
    private $externalSerial;


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
    private $mode = "purchase";

    /**
     * @var string
     *
     * @ORM\Column(name="process", type="string", length=50, nullable=true)
     */
    private $process = "In-progress";


    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer",nullable=true)
     */
    private $quantity;


    /**
     * @var integer
     *
     * @ORM\Column(name="salesQuantity", type="integer",nullable=true)
     */
    private $salesQuantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="salesReturnQuantity", type="integer",nullable=true)
     */
    private $salesReturnQuantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="salesReplaceQuantity", type="integer",nullable=true)
     */
    private $salesReplaceQuantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="purchaseReturnQuantity", type="integer",nullable=true)
     */
    private $purchaseReturnQuantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="damageQuantity", type="integer",nullable=true)
     */
    private $damageQuantity;


    /**
     * @var integer
     *
     * @ORM\Column(name="remainingQuantity", type="integer",nullable=true)
     */
    private $remainingQuantity;


    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float", nullable = true)
     */
    private $price;


    /**
     * @var float
     *
     * @ORM\Column(name="purchasePrice", type="float", nullable = true)
     */
    private $purchasePrice;


    /**
     * @var float
     *
     * @ORM\Column(name="salesPrice", type="float", nullable = true)
     */
    private $salesPrice;


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
    public function getSalesQuantity()
    {
        return $this->salesQuantity;
    }

    /**
     * @param int $salesQuantity
     */
    public function setSalesQuantity($salesQuantity)
    {
        $this->salesQuantity = $salesQuantity;
    }

    /**
     * @return int
     */
    public function getSalesReturnQuantity()
    {
        return $this->salesReturnQuantity;
    }

    /**
     * @param int $salesReturnQuantity
     */
    public function setSalesReturnQuantity($salesReturnQuantity)
    {
        $this->salesReturnQuantity = $salesReturnQuantity;
    }

    /**
     * @return int
     */
    public function getSalesReplaceQuantity()
    {
        return $this->salesReplaceQuantity;
    }

    /**
     * @param int $salesReplaceQuantity
     */
    public function setSalesReplaceQuantity($salesReplaceQuantity)
    {
        $this->salesReplaceQuantity = $salesReplaceQuantity;
    }

    /**
     * @return int
     */
    public function getPurchaseReturnQuantity()
    {
        return $this->purchaseReturnQuantity;
    }

    /**
     * @param int $purchaseReturnQuantity
     */
    public function setPurchaseReturnQuantity($purchaseReturnQuantity)
    {
        $this->purchaseReturnQuantity = $purchaseReturnQuantity;
    }

    /**
     * @return int
     */
    public function getDamageQuantity()
    {
        return $this->damageQuantity;
    }

    /**
     * @param int $damageQuantity
     */
    public function setDamageQuantity($damageQuantity)
    {
        $this->damageQuantity = $damageQuantity;
    }

    /**
     * @return int
     */
    public function getRemainingQuantity()
    {
        return $this->remainingQuantity;
    }

    /**
     * @param int $remainingQuantity
     */
    public function setRemainingQuantity($remainingQuantity)
    {
        $this->remainingQuantity = $remainingQuantity;
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
     * @return ItemWarning
     */
    public function getItemWarning() {
        return $this->itemWarning;
    }

    /**
     * @param ItemWarning $itemWarning
     */
    public function setItemWarning( $itemWarning ) {
        $this->itemWarning = $itemWarning;
    }

    /**
     * @return string
     */
    public function getAssuranceType() {
        return $this->assuranceType;
    }

    /**
     * @param string $assuranceType
     */
    public function setAssuranceType( $assuranceType ) {
        $this->assuranceType = $assuranceType;
    }

    /**
     * @return string
     */
    public function getExpiredDate() {
        return $this->expiredDate;
    }

    /**
     * @param string $expiredDate
     */
    public function setExpiredDate( $expiredDate ) {
        $this->expiredDate = $expiredDate;
    }

    /**
     * @return array
     */
    public function getInternalSerial() {
        return $this->internalSerial;
    }

    /**
     * @param array $internalSerial
     */
    public function setInternalSerial( $internalSerial ) {
        $this->internalSerial = $internalSerial;
    }

    /**
     * @return string
     */
    public function getExternalSerial() {
        return $this->externalSerial;
    }

    /**
     * @param string $externalSerial
     */
    public function setExternalSerial( $externalSerial ) {
        $this->externalSerial = $externalSerial;
    }

    /**
     * @return string
     */
    public function getEffectedDate() {
        return $this->effectedDate;
    }

    /**
     * @param string $effectedDate
     */
    public function setEffectedDate( $effectedDate ) {
        $this->effectedDate = $effectedDate;
    }

    /**
     * @return ItemMetaAttribute
     */
    public function getItemMetaAttributes() {
        return $this->itemMetaAttributes;
    }

    /**
     * @param ItemMetaAttribute $itemMetaAttributes
     */
    public function setItemMetaAttributes( $itemMetaAttributes ) {
        $this->itemMetaAttributes = $itemMetaAttributes;
    }

    /**
     * @return ItemKeyValue
     */
    public function getItemKeyValues() {
        return $this->itemKeyValues;
    }

    /**
     * @param ItemKeyValue $itemKeyValues
     */
    public function setItemKeyValues( $itemKeyValues ) {
        $this->itemKeyValues = $itemKeyValues;
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
     * @return WearHouse
     */
    public function getWearHouse()
    {
        return $this->wearHouse;
    }

    /**
     * @param WearHouse $wearHouse
     */
    public function setWearHouse($wearHouse)
    {
        $this->wearHouse = $wearHouse;
    }

    /**
     * @return float
     */
    public function getPurchasePrice()
    {
        return $this->purchasePrice;
    }

    /**
     * @param float $purchasePrice
     */
    public function setPurchasePrice($purchasePrice)
    {
        $this->purchasePrice = $purchasePrice;
    }

    /**
     * @return float
     */
    public function getSalesPrice()
    {
        return $this->salesPrice;
    }

    /**
     * @param float $salesPrice
     */
    public function setSalesPrice($salesPrice)
    {
        $this->salesPrice = $salesPrice;
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
     * @return Purchase
     */
    public function getPurchase()
    {
        return $this->purchase;
    }

    /**
     * @param Purchase $purchase
     */
    public function setPurchase($purchase)
    {
        $this->purchase = $purchase;
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
     * @return TallyConfig
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param TallyConfig $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }


}

