<?php

namespace Appstore\Bundle\EcommerceBundle\Entity;

use Appstore\Bundle\InventoryBundle\Entity\ItemColor;
use Appstore\Bundle\InventoryBundle\Entity\ItemSize;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseItem;
use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\ToolBundle\Entity\ProductColor;
use Setting\Bundle\ToolBundle\Entity\ProductSize;

/**
 * OrderItem
 *
 * @ORM\Table("ems_order_item")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\EcommerceBundle\Repository\OrderItemRepository")
 */
class OrderItem
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\Order", inversedBy="orderItems")
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $order;


     /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\Item", inversedBy="orderItems")
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $item;

     /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\ItemSub", inversedBy="orderItems")
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $itemSub;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\ProductSize", inversedBy="orderItem")
     **/
    private  $size;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\ProductColor", inversedBy="orderItem")
     **/
    private  $color;

    /**
     * @var string
     *
     * @ORM\Column(name="itemSource", type="string" , length = 30, nullable=true)
     */
    private $itemSource = 'online';

     /**
     * @var string
     *
     * @ORM\Column(name="itemName", type="string", nullable=true)
     */
    private $itemName;

     /**
     * @var string
     *
     * @ORM\Column(name="brandName", type="string", nullable=true)
     */
    private $brandName;

    /**
     * @var string
     *
     * @ORM\Column(name="categoryName", type="string", nullable=true)
     */
    private $categoryName;

    /**
     * @var string
     *
     * @ORM\Column(name="unitName", type="string" , nullable=true)
     */
    private $unitName;


    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="smallint" ,nullable=true)
     */
    private $quantity;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float", nullable=true)
     */
    private $price;

    /**
     * @var float
     *
     * @ORM\Column(name="discountPrice", type="float", nullable=true)
     */
    private $discountPrice;

    /**
     * @var float
     *
     * @ORM\Column(name="subTotal", type="float",nullable=true)
     */
    private $subTotal;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="smallint" ,length=1 ,nullable = true)
     */
    private $status = 0;


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
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return OrderItem
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
     * Set price
     *
     * @param float $price
     *
     * @return OrderItem
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set subTotal
     *
     * @param float $subTotal
     *
     * @return OrderItem
     */
    public function setSubTotal($subTotal)
    {
        $this->subTotal = $subTotal;

        return $this;
    }

    /**
     * Get subTotal
     *
     * @return float
     */
    public function getSubTotal()
    {
        return $this->subTotal;
    }

    /**
     * @return mixed
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param mixed $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }


    /**
     * @return ProductColor
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param ProductColor $color
     */
    public function setColor($color)
    {
        $this->color = $color;
    }



    /**
     * @return ProductSize
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param ProductSize $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }



    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return ItemSub
     */
    public function getItemSub()
    {
        return $this->itemSub;
    }

    /**
     * @param ItemSub $itemSub
     */
    public function setItemSub($itemSub)
    {
        $this->itemSub = $itemSub;
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
     * @return string
     */
    public function getItemSource()
    {
        return $this->itemSource;
    }

    /**
     * @param string $itemSource
     * online
     * stock
     * dims
     * anonymous
     */

    public function setItemSource($itemSource)
    {
        $this->itemSource = $itemSource;
    }

    /**
     * @return string
     */
    public function getItemName()
    {
        return $this->itemName;
    }

    /**
     * @param string $itemName
     */
    public function setItemName($itemName)
    {
        $this->itemName = $itemName;
    }

    /**
     * @return string
     */
    public function getBrandName()
    {
        return $this->brandName;
    }

    /**
     * @param string $brandName
     */
    public function setBrandName($brandName)
    {
        $this->brandName = $brandName;
    }

    /**
     * @return string
     */
    public function getCategoryName()
    {
        return $this->categoryName;
    }

    /**
     * @param string $categoryName
     */
    public function setCategoryName($categoryName)
    {
        $this->categoryName = $categoryName;
    }

    /**
     * @return string
     */
    public function getUnitName()
    {
        return $this->unitName;
    }

    /**
     * @param string $unitName
     */
    public function setUnitName($unitName)
    {
        $this->unitName = $unitName;
    }

    /**
     * @return float
     */
    public function getDiscountPrice()
    {
        return $this->discountPrice;
    }

    /**
     * @param float $discountPrice
     */
    public function setDiscountPrice($discountPrice)
    {
        $this->discountPrice = $discountPrice;
    }


}

