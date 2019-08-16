<?php

namespace Appstore\Bundle\TallyBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * SalesItem
 *
 * @ORM\Table("tally_sales_item")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\TallyBundle\Repository\SalesItemRepository")
 */
class SalesItem
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Item", inversedBy="salesItems" )
     **/
    private  $item;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\TallyBundle\Entity\WearHouse", inversedBy="salesItems" )
     **/
    private  $wearHouse;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\PurchaseItem", inversedBy="salesItems" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $purchaseItem;

    /**
     * @ORM\OneToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\SalesReturnItem", mappedBy="salesItem" )
     **/
    private  $salesReturnItem;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Sales", inversedBy="salesItems" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $sales;

    /**
     * @var string
     *
     * @ORM\Column(name="serialNo", type="text", length=255, nullable = true)
     */
    private $serialNo;

    /**
     * @var string
     *
     * @ORM\Column(name="assuranceType", type="string", length=50, nullable = true)
     */
    private $assuranceType;

    /**
     * @var string
     *
     * @ORM\Column(name="assuranceFromVendor", type="string", length=100, nullable = true)
     */
    private $assuranceFromVendor;

    /**
     * @var string
     *
     * @ORM\Column(name="assuranceToCustomer", type="string", length=100, nullable = true)
     */
    private $assuranceToCustomer;

    /**
     * @var datetime
     *
     * @ORM\Column(name="expiredDate", type="datetime", nullable=true)
     */
    private $expiredDate;


    /**
     * @var float
     *
     * @ORM\Column(name="quantity", type="float")
     */
    private $quantity;


    /**
     * @var float
     *
     * @ORM\Column(name="salesPrice", type="float")
     */
    private $salesPrice;

    /**
     * @var float
     *
     * @ORM\Column(name="vatPercentage", type="float", nullable=true)
     */
    private $vatPercentage;

    /**
     * @var float
     *
     * @ORM\Column(name="vatAmount", type="float", nullable=true)
     */
    private $vatAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="sdPercentage", type="float", nullable=true)
     */
    private $sdPercentage;

    /**
     * @var float
     *
     * @ORM\Column(name="sd", type="float", nullable=true)
     */
    private $sdAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="discountPrice", type="decimal", nullable=true)
     */
    private $discountPrice;


    /**
     * @var float
     *
     * @ORM\Column(name="purchasePrice", type="float", nullable=true)
     */
    private $purchasePrice;

    /**
     * @var string
     *
     * @ORM\Column(name="estimatePrice", type="decimal", nullable=true)
     */
    private $estimatePrice;


    /**
     * @var boolean
     *
     * @ORM\Column(name="customPrice", type="boolean")
     */
    private $customPrice = false;

    /**
     * @var float
     *
     * @ORM\Column(name="subTotal", type="float")
     */
    private $subTotal;


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
    public function getSalesPrice()
    {
        return $this->salesPrice;
    }

    /**
     * @param string $salesPrice
     */
    public function setSalesPrice($salesPrice)
    {
        $this->salesPrice = $salesPrice;
    }

    /**
     * @return string
     */
    public function getPurchasePrice()
    {
        return $this->purchasePrice;
    }

    /**
     * @param string $purchasePrice
     */
    public function setPurchasePrice($purchasePrice)
    {
        $this->purchasePrice = $purchasePrice;
    }

    /**
     * @return string
     */
    public function getEstimatePrice()
    {
        return $this->estimatePrice;
    }

    /**
     * @param string $estimatePrice
     */
    public function setEstimatePrice($estimatePrice)
    {
        $this->estimatePrice = $estimatePrice;
    }

    /**
     * @return boolean
     */
    public function isCustomPrice()
    {
        return $this->customPrice;
    }

    /**
     * @param boolean $customPrice
     */
    public function setCustomPrice($customPrice)
    {
        $this->customPrice = $customPrice;
    }

    /**
     * @return mixed
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param mixed $item
     */
    public function setItem($item)
    {
        $this->item = $item;
    }

    /**
     * @return mixed
     */
    public function getStockItem()
    {
        return $this->stockItem;
    }

    /**
     * @param mixed $stockItem
     */
    public function setStockItem($stockItem)
    {
        $this->stockItem = $stockItem;
    }

    /**
     * @return PurchaseItem.
     */
    public function getPurchaseItem()
    {
        return $this->purchaseItem;
    }

    /**
     * @param  $purchaseItem
     */
    public function setPurchaseItem($purchaseItem)
    {
        $this->purchaseItem = $purchaseItem;
    }

    /**
     * @return Sales
     */
    public function getSales()
    {
        return $this->sales;
    }

    /**
     * @param Sales $sales
     */
    public function setSales($sales)
    {
        $this->sales = $sales;
    }

    /**
     * @return string
     */
    public function getSubTotal()
    {
        return $this->subTotal;
    }

    /**
     * @param string $subTotal
     */
    public function setSubTotal($subTotal)
    {
        $this->subTotal = $subTotal;
    }

    /**
     * @return SalesItem
     */
    public function getSalesReturnItem()
    {
        return $this->salesReturnItem;
    }

    /**
     * @return float
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param float $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return string
     */
    public function getSerialNo()
    {
        return $this->serialNo;
    }

    /**
     * @param string $serialNo
     */
    public function setSerialNo($serialNo)
    {
        $this->serialNo = $serialNo;
    }

    /**
     * @return string
     */
    public function getAssuranceType()
    {
        return $this->assuranceType;
    }

    /**
     * @param string $assuranceType
     */
    public function setAssuranceType($assuranceType)
    {
        $this->assuranceType = $assuranceType;
    }

    /**
     * @return string
     */
    public function getAssuranceFromVendor()
    {
        return $this->assuranceFromVendor;
    }

    /**
     * @param string $assuranceFromVendor
     */
    public function setAssuranceFromVendor($assuranceFromVendor)
    {
        $this->assuranceFromVendor = $assuranceFromVendor;
    }

    /**
     * @return string
     */
    public function getAssuranceToCustomer()
    {
        return $this->assuranceToCustomer;
    }

    /**
     * @param string $assuranceToCustomer
     */
    public function setAssuranceToCustomer($assuranceToCustomer)
    {
        $this->assuranceToCustomer = $assuranceToCustomer;
    }

    /**
     * @return datetime
     */
    public function getExpiredDate()
    {
        return $this->expiredDate;
    }

    /**
     * @param datetime $expiredDate
     */
    public function setExpiredDate($expiredDate)
    {
        $this->expiredDate = $expiredDate;
    }

    /**
     * @return string
     */
    public function getDiscountPrice()
    {
        return $this->discountPrice;
    }

    /**
     * @param string $discountPrice
     */
    public function setDiscountPrice($discountPrice)
    {
        $this->discountPrice = $discountPrice;
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
    public function getVatPercentage()
    {
        return $this->vatPercentage;
    }

    /**
     * @param float $vatPercentage
     */
    public function setVatPercentage($vatPercentage)
    {
        $this->vatPercentage = $vatPercentage;
    }


    /**
     * @return float
     */
    public function getVatAmount()
    {
        return $this->vatAmount;
    }

    /**
     * @param float $vatAmount
     */
    public function setVatAmount($vatAmount)
    {
        $this->vatAmount = $vatAmount;
    }

    /**
     * @return float
     */
    public function getSdPercentage()
    {
        return $this->sdPercentage;
    }

    /**
     * @param float $sdPercentage
     */
    public function setSdPercentage($sdPercentage)
    {
        $this->sdPercentage = $sdPercentage;
    }

    /**
     * @return float
     */
    public function getSdAmount()
    {
        return $this->sdAmount;
    }

    /**
     * @param float $sdAmount
     */
    public function setSdAmount($sdAmount)
    {
        $this->sdAmount = $sdAmount;
    }


}

