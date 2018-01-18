<?php

namespace Appstore\Bundle\InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InventoryConfig
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Appstore\Bundle\InventoryBundle\Repository\InventoryConfigRepository")
 */
class InventoryConfig
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
     * @ORM\OneToMany(targetEntity="Product\Bundle\ProductBundle\Entity\Category", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $categories;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Product", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $products;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\StockItem", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $stockItems;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Vendor", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $vendors;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Item", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $items;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\ItemBrand", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $brand;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\ItemColor", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $color;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\ItemSize", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $size;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\PurchaseVendorItem", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $purchaseVendorItems;

    /**
     * @ORM\OneToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\ItemTypeGrouping", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $itemTypeGrouping;


     /**
     * @ORM\OneToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\ItemSizeGroup", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $sizeGroup;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\WareHouse", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $wareHouses;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Sales", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $sales;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Purchase", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $purchases;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Delivery", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $deliveries;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\DeliveryReturn", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $deliveryReturns;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\BranchInvoice", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $branchInvoices;

     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\ServiceSales", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $serviceSales;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\SalesImport", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $salesImports;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\SalesReturn", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $salesReturn;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\PurchaseReturn", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $purchaseReturn;


    /**
     * @ORM\OneToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="inventoryConfig" , cascade={"persist", "remove"})
     **/

    private $globalOption;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\ExcelImporter", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $excelImporters;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\Transaction", mappedBy="inventoryConfig", cascade={"persist", "remove"} )
     **/
    private  $transaction;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Damage", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $damages;

    /**
     * @var string
     *
     * @ORM\Column(name="shopName", type="string",  nullable=true)
     */
    private $shopName;


    /**
     * @var string
     *
     * @ORM\Column(name="printer", type="string", length=50,nullable = true)
     */
    private $printer;

    /**
     * @var string
     *
     * @ORM\Column(name="onlineSalesPrinter", type="string", length=50,nullable = true)
     */
    private $onlineSalesPrinter;

    /**
     * @var smallint
     *
     * @ORM\Column(name="salesReturnDayLimit", type="smallint",  nullable=true)
     */
    private $salesReturnDayLimit = 5;

    /**
     * @var smallint
     *
     * @ORM\Column(name="vatPercentage", type="smallint",  nullable=true)
     */
    private $vatPercentage;

    /**
     * @var string
     *
     * @ORM\Column(name="vatRegNo", type="string",  nullable=true)
     */
    private $vatRegNo;


    /**
     * @var array
     *
     * @ORM\Column(name="deliveryProcess", type="array", nullable=true )
     */
    private $deliveryProcess;


    /**
     * @var boolean
     *
     * @ORM\Column(name="isBranch", type="boolean",  nullable=true)
     */
    private $isBranch = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="barcodePrint", type="boolean",  nullable=true)
     */
    private $barcodePrint = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="invoicePrintUserMobile", type="boolean",  nullable=true)
     */
    private $invoicePrintUserMobile = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="invoicePrintLogo", type="boolean",  nullable=true)
     */
    private $invoicePrintLogo = true;



    /**
     * @var boolean
     *
     * @ORM\Column(name="isBranchInvoice", type="boolean",  nullable=true)
     */
    private $isBranchInvoice = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="vatEnable", type="boolean",  nullable=true)
     */
    private $vatEnable = false;

    /**
     * @var smallint
     *
     * @ORM\Column(name="printMarginTop", type="smallint",  nullable=true)
     */
    private $printTopMargin = 0;

    /**
     * @var smallint
     *
     * @ORM\Column(name="printMarginBottom", type="smallint",  nullable=true)
     */
    private $printMarginBottom = 0;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isPrintHeader", type="boolean",  nullable=true)
     */
    private $isPrintHeader = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isPrintFooter", type="boolean",  nullable=true)
     */
    private $isPrintFooter = true;


    /**
     * @var boolean
     *
     * @ORM\Column(name="isItem", type="boolean",  nullable=true)
     */
    private $isItem = true;


    /**
     * @var boolean
     *
     * @ORM\Column(name="isColor", type="boolean" ,  nullable=true)
     */
    private $isColor;


    /**
     * @var boolean
     *
     * @ORM\Column(name="isSize", type="boolean",  nullable=true)
     */
    private $isSize;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isModel", type="boolean",  nullable=true)
     */
    private $isModel;


    /**
     * @var boolean
     *
     * @ORM\Column(name="isVendor", type="boolean",  nullable=true)
     */
    private $isVendor;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isBrand", type="boolean",  nullable=true)
     */
    private $isBrand;

    /**
     * @var boolean
     *
     * @ORM\Column(name="barcodeColor", type="boolean" ,  nullable=true)
     */
    private $barcodeColor;


    /**
     * @var boolean
     *
     * @ORM\Column(name="barcodeSize", type="boolean",  nullable=true)
     */
    private $barcodeSize;


    /**
     * @var smallint
     *
     * @ORM\Column(name="barcodeBrandVendor", type="smallint",  nullable=true)
     */
    private $barcodeBrandVendor = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="barcodeText", type="string", length=255,nullable = true)
     */
    private $barcodeText;

    /**
     * @var smallint
     *
     * @ORM\Column(name="barcodeWidth", type="smallint", nullable = true)
     */
    private $barcodeWidth = 140;

    /**
     * @var smallint
     *
     * @ORM\Column(name="barcodeMargin", type="smallint", nullable = true)
     */
    private $barcodeMargin = 0;

    /**
     * @var smallint
     *
     * @ORM\Column(name="barcodeBorder", type="smallint", nullable = true)
     */
    private $barcodeBorder = 0;

    /**
     * @var smallint
     *
     * @ORM\Column(name="barcodePadding", type="smallint", nullable = true)
     */
    private $barcodePadding = 0;

    /**
     * @var smallint
     *
     * @ORM\Column(name="printLeftMargin", type="smallint", nullable = true)
     */
    private $printLeftMargin = 0;

    /**
     * @var smallint
     *
     * @ORM\Column(name="barcodeHeight", type="smallint", nullable = true)
     */
    private $barcodeHeight = 80;

    /**
     * @var smallint
     *
     * @ORM\Column(name="barcodeThickness", type="smallint", nullable = true)
     */
    private $barcodeThickness = 30;

    /**
     * @var smallint
     *
     * @ORM\Column(name="barcodeFontSize", type="smallint", nullable = true)
     */
    private $barcodeFontSize = 8;

    /**
     * @var smallint
     *
     * @ORM\Column(name="barcodeScale", type="smallint", nullable = true)
     */
    private $barcodeScale = 1;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        return $this->id = $id;
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
    public function getVendors()
    {
        return $this->vendors;
    }

    /**
     * @return mixed
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @return mixed
     */
    public function getItemTypeGrouping()
    {
        return $this->itemTypeGrouping;
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
    public function getStockItems()
    {
        return $this->stockItems;
    }

    /**
     * @return mixed
     */
    public function getWareHouses()
    {
        return $this->wareHouses;
    }

    /**
     * @return mixed
     */
    public function getProducts()
    {
        return $this->products;
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
    public function getPurchaseReturn()
    {
        return $this->purchaseReturn;
    }

    /**
     * @return smallint
     */
    public function getSalesReturnDayLimit()
    {
        return $this->salesReturnDayLimit;
    }

    /**
     * @param smallint $salesReturnDayLimit
     */
    public function setSalesReturnDayLimit($salesReturnDayLimit)
    {
        $this->salesReturnDayLimit = $salesReturnDayLimit;
    }

    /**
     * @return mixed
     */
    public function getExcelImporters()
    {
        return $this->excelImporters;
    }

    /**
     * @return mixed
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * @return mixed
     */
    public function getAccountPurchase()
    {
        return $this->accountPurchase;
    }


    /**
     * @return boolean
     */
    public function getIsItem()
    {
        return $this->isItem;
    }

    /**
     * @param boolean $isItem
     */
    public function setIsItem($isItem)
    {
        $this->isItem = $isItem;
    }

    /**
     * @return boolean
     */
    public function getIsColor()
    {
        return $this->isColor;
    }

    /**
     * @param boolean $isColor
     */
    public function setIsColor($isColor)
    {
        $this->isColor = $isColor;
    }

    /**
     * @return boolean
     */
    public function getIsSize()
    {
        return $this->isSize;
    }

    /**
     * @param boolean $isSize
     */
    public function setIsSize($isSize)
    {
        $this->isSize = $isSize;
    }

    /**
     * @return boolean
     */
    public function getIsVendor()
    {
        return $this->isVendor;
    }

    /**
     * @param boolean $isVendor
     */
    public function setIsVendor($isVendor)
    {
        $this->isVendor = $isVendor;
    }

    /**
     * @return smallint
     */
    public function getVatPercentage()
    {
        return $this->vatPercentage;
    }

    /**
     * @param smallint $vatPercentage
     */
    public function setVatPercentage($vatPercentage)
    {
        $this->vatPercentage = $vatPercentage;
    }

    /**
     * @return boolean
     */
    public function getVatEnable()
    {
        return $this->vatEnable;
    }

    /**
     * @param boolean $vatEnable
     */
    public function setVatEnable($vatEnable)
    {
        $this->vatEnable = $vatEnable;
    }


    /**
     * @return boolean
     */
    public function getIsBrand()
    {
        return $this->isBrand;
    }

    /**
     * @param boolean $isBrand
     */
    public function setIsBrand($isBrand)
    {
        $this->isBrand = $isBrand;
    }

    /**
     * @return array
     */
    public function getDeliveryProcess()
    {
        return $this->deliveryProcess;
    }

    /**
     * @param array $deliveryProcess
     */
    public function setDeliveryProcess($deliveryProcess)
    {
        $this->deliveryProcess = $deliveryProcess;
    }

    /**
     * @return Damage
     */
    public function getDamages()
    {
        return $this->damages;
    }

    /**
     * @return PurchaseVendorItem
     */
    public function getPurchaseVendorItems()
    {
        return $this->purchaseVendorItems;
    }

    /**
     * @return ServiceSales
     */
    public function getServiceSales()
    {
        return $this->serviceSales;
    }

    /**
     * @return boolean
     */
    public function getIsBranchInvoice()
    {
        return $this->isBranchInvoice;
    }

    /**
     * @param boolean $isBranchInvoice
     */
    public function setIsBranchInvoice($isBranchInvoice)
    {
        $this->isBranchInvoice = $isBranchInvoice;
    }

    /**
     * @return boolean
     */
    public function getIsBranch()
    {
        return $this->isBranch;
    }

    /**
     * @param boolean $isBranch
     */
    public function setIsBranch($isBranch)
    {
        $this->isBranch = $isBranch;
    }

    /**
     * @return Delivery
     */
    public function getDeliveries()
    {
        return $this->deliveries;
    }

    /**
     * @return boolean
     */
    public function getBarcodeColor()
    {
        return $this->barcodeColor;
    }

    /**
     * @param boolean $barcodeColor
     */
    public function setBarcodeColor($barcodeColor)
    {
        $this->barcodeColor = $barcodeColor;
    }

    /**
     * @return boolean
     */
    public function getBarcodeSize()
    {
        return $this->barcodeSize;
    }

    /**
     * @param boolean $barcodeSize
     */
    public function setBarcodeSize($barcodeSize)
    {
        $this->barcodeSize = $barcodeSize;
    }


    /**
     * @return string
     */
    public function getBarcodeText()
    {
        return $this->barcodeText;
    }

    /**
     * @param string $barcodeText
     */
    public function setBarcodeText($barcodeText)
    {
        $this->barcodeText = $barcodeText;
    }

    /**
     * @return smallint
     */
    public function getBarcodeWidth()
    {
        return $this->barcodeWidth;
    }

    /**
     * @param smallint $barcodeWidth
     */
    public function setBarcodeWidth($barcodeWidth)
    {
        $this->barcodeWidth = $barcodeWidth;
    }

    /**
     * @return smallint
     */
    public function getBarcodeHeight()
    {
        return $this->barcodeHeight;
    }

    /**
     * @param smallint $barcodeHeight
     */
    public function setBarcodeHeight($barcodeHeight)
    {
        $this->barcodeHeight = $barcodeHeight;
    }

    /**
     * @return smallint
     */
    public function getBarcodeBrandVendor()
    {
        return $this->barcodeBrandVendor;
    }

    /**
     * @param smallint $barcodeBrandVendor
     */
    public function setBarcodeBrandVendor($barcodeBrandVendor)
    {
        $this->barcodeBrandVendor = $barcodeBrandVendor;
    }

    /**
     * @return smallint
     */
    public function getBarcodeThickness()
    {
        return $this->barcodeThickness;
    }

    /**
     * @param smallint $barcodeThickness
     */
    public function setBarcodeThickness($barcodeThickness)
    {
        $this->barcodeThickness = $barcodeThickness;
    }

    /**
     * @return smallint
     */
    public function getBarcodeFontSize()
    {
        return $this->barcodeFontSize;
    }

    /**
     * @param smallint $barcodeFontSize
     */
    public function setBarcodeFontSize($barcodeFontSize)
    {
        $this->barcodeFontSize = $barcodeFontSize;
    }

    /**
     * @return smallint
     */
    public function getBarcodeScale()
    {
        return $this->barcodeScale;
    }

    /**
     * @param smallint $barcodeScale
     */
    public function setBarcodeScale($barcodeScale)
    {
        $this->barcodeScale = $barcodeScale;
    }

    /**
     * @return smallint
     */
    public function getBarcodeMargin()
    {
        return $this->barcodeMargin;
    }

    /**
     * @param smallint $barcodeMargin
     */
    public function setBarcodeMargin($barcodeMargin)
    {
        $this->barcodeMargin = $barcodeMargin;
    }

    /**
     * @return smallint
     */
    public function getBarcodePadding()
    {
        return $this->barcodePadding;
    }

    /**
     * @param smallint $barcodePadding
     */
    public function setBarcodePadding($barcodePadding)
    {
        $this->barcodePadding = $barcodePadding;
    }

    /**
     * @return smallint
     */
    public function getBarcodeBorder()
    {
        return $this->barcodeBorder;
    }

    /**
     * @param smallint $barcodeBorder
     */
    public function setBarcodeBorder($barcodeBorder)
    {
        $this->barcodeBorder = $barcodeBorder;
    }

    /**
     * @return smallint
     */
    public function getPrintLeftMargin()
    {
        return $this->printLeftMargin;
    }

    /**
     * @param smallint $printLeftMargin
     */
    public function setPrintLeftMargin($printLeftMargin)
    {
        $this->printLeftMargin = $printLeftMargin;
    }

    /**
     * @return string
     */
    public function getVatRegNo()
    {
        return $this->vatRegNo;
    }

    /**
     * @param string $vatRegNo
     */
    public function setVatRegNo($vatRegNo)
    {
        $this->vatRegNo = $vatRegNo;
    }

    /**
     * @return boolean
     */
    public function getIsModel()
    {
        return $this->isModel;
    }

    /**
     * @param boolean $isModel
     */
    public function setIsModel($isModel)
    {
        $this->isModel = $isModel;
    }

    /**
     * @return smallint
     */
    public function getPrintTopMargin()
    {
        return $this->printTopMargin;
    }

    /**
     * @param smallint $printTopMargin
     */
    public function setPrintTopMargin($printTopMargin)
    {
        $this->printTopMargin = $printTopMargin;
    }

    /**
     * @return smallint
     */
    public function getPrintMarginBottom()
    {
        return $this->printMarginBottom;
    }

    /**
     * @param smallint $printMarginBottom
     */
    public function setPrintMarginBottom($printMarginBottom)
    {
        $this->printMarginBottom = $printMarginBottom;
    }

    /**
     * @return boolean
     */
    public function getIsPrintHeader()
    {
        return $this->isPrintHeader;
    }

    /**
     * @param boolean $isPrintHeader
     */
    public function setIsPrintHeader($isPrintHeader)
    {
        $this->isPrintHeader = $isPrintHeader;
    }

    /**
     * @return boolean
     */
    public function getIsPrintFooter()
    {
        return $this->isPrintFooter;
    }

    /**
     * @param boolean $isPrintFooter
     */
    public function setIsPrintFooter($isPrintFooter)
    {
        $this->isPrintFooter = $isPrintFooter;
    }

    /**
     * @return bool
     */
    public function getBarcodePrint()
    {
        return $this->barcodePrint;
    }

    /**
     * @param bool $barcodePrint
     */
    public function setBarcodePrint($barcodePrint)
    {
        $this->barcodePrint = $barcodePrint;
    }

    /**
     * @return string
     */
    public function getPrinter()
    {
        return $this->printer;
    }

    /**
     * @param string $printer
     * Printer
     * Pos Printer
     * Save
     */
    public function setPrinter($printer)
    {
        $this->printer = $printer;
    }

    /**
     * @return string
     */
    public function getShopName()
    {
        return $this->shopName;
    }

    /**
     * @param string $shopName
     */
    public function setShopName($shopName)
    {
        $this->shopName = $shopName;
    }

    /**
     * @return DeliveryReturn
     */
    public function getDeliveryReturns()
    {
        return $this->deliveryReturns;
    }

    /**
     * @return string
     */
    public function getOnlineSalesPrinter()
    {
        return $this->onlineSalesPrinter;
    }

    /**
     * @param string $onlineSalesPrinter
     */
    public function setOnlineSalesPrinter($onlineSalesPrinter)
    {
        $this->onlineSalesPrinter = $onlineSalesPrinter;
    }

    /**
     * @return ItemSizeGroup
     */
    public function getSizeGroup()
    {
        return $this->sizeGroup;
    }

    /**
     * @return boolean
     */
    public function getInvoicePrintUserMobile()
    {
        return $this->invoicePrintUserMobile;
    }

    /**
     * @param boolean $invoicePrintUserMobile
     */
    public function setInvoicePrintUserMobile($invoicePrintUserMobile)
    {
        $this->invoicePrintUserMobile = $invoicePrintUserMobile;
    }

    /**
     * @return boolean
     */
    public function getInvoicePrintLogo()
    {
        return $this->invoicePrintLogo;
    }

    /**
     * @param boolean $invoicePrintLogo
     */
    public function setInvoicePrintLogo($invoicePrintLogo)
    {
        $this->invoicePrintLogo = $invoicePrintLogo;
    }


}

