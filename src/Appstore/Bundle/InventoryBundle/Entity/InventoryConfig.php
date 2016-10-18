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
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Purchase", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $purchases;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\PurchaseVendorItem", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $purchaseVendorItems;

    /**
     * @ORM\OneToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\ItemTypeGrouping", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $itemTypeGrouping;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\WareHouse", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $wareHouses;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Sales", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $sales;

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
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountPurchase", mappedBy="inventoryConfig", cascade={"persist", "remove"} )
     **/
    private  $accountPurchase;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountSales", mappedBy="inventoryConfig", cascade={"persist", "remove"} )
     **/
    private  $accountSales;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Damage", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $damages;


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
     * @var array
     *
     * @ORM\Column(name="deliveryProcess", type="array", nullable=true )
     */
    private $deliveryProcess;


    /**
     * @var boolean
     *
     * @ORM\Column(name="vatEnable", type="boolean",  nullable=true)
     */
    private $vatEnable = false;

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
     * @return mixed
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
     * @return mixed
     */
    public function getAccountSales()
    {
        return $this->accountSales;
    }

    /**
     * @return mixed
     */
    public function getPettyCash()
    {
        return $this->pettyCash;
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
     * @return mixed
     */
    public function getPurchaseVendorItems()
    {
        return $this->purchaseVendorItems;
    }


}

