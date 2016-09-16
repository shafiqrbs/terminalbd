<?php

namespace Appstore\Bundle\EcommerceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrderItem
 *
 * @ORM\Table("order_items")
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\Order", inversedBy="orderItems"  , cascade={"remove"} )
     **/
    private  $order;

     /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\PurchaseVendorItem", inversedBy="orderItems"  , cascade={"remove"} )
     **/
    private  $purchaseVendorItem;

     /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\GoodsItem", inversedBy="orderItems"  , cascade={"remove"} )
     **/
    private  $goodsItem;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="smallint")
     */
    private $quantity;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float")
     */
    private $price;

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
     * @return mixed
     */
    public function getPurchaseVendorItem()
    {
        return $this->purchaseVendorItem;
    }

    /**
     * @param mixed $purchaseVendorItem
     */
    public function setPurchaseVendorItem($purchaseVendorItem)
    {
        $this->purchaseVendorItem = $purchaseVendorItem;
    }

    /**
     * @return mixed
     */
    public function getGoodsItem()
    {
        return $this->goodsItem;
    }

    /**
     * @param mixed $goodsItem
     */
    public function setGoodsItem($goodsItem)
    {
        $this->goodsItem = $goodsItem;
    }


}

