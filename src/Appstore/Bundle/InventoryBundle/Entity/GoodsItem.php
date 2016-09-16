<?php

namespace Appstore\Bundle\InventoryBundle\Entity;

use Appstore\Bundle\EcommerceBundle\Entity\OrderItem;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * GoodsItem
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Appstore\Bundle\InventoryBundle\Repository\GoodsItemRepository")
 */
class GoodsItem
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\PurchaseVendorItem", inversedBy="goodsItems" )
     **/
    private  $purchaseVendorItem;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\OrderItem", inversedBy="goodsItem" )
     **/
    private  $orderItems;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\ItemSize", inversedBy="goodsItems")
     */
    protected $size;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\ItemColor", inversedBy="goodsItems")
     */
    protected $color;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", nullable = true)
     */
    private $name;


    /**
     * @var string
     *
     * @ORM\Column(name="quantity", type="integer", nullable = true)
     */
    private $quantity;

     /**
     * @var string
     *
     * @ORM\Column(name="salesPrice", type="decimal", nullable = true)
     */
    private $salesPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="webPrice", type="decimal", nullable = true)
     */
    private $webPrice;

    /**
     * @var boolean
     *
     * @ORM\Column(name="masterItem", type="boolean", nullable=true)
     */
    private $masterItem = false;


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
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param mixed $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * @return mixed
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param mixed $color
     */
    public function setColor($color)
    {
        $this->color = $color;
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
    public function getWebPrice()
    {
        return $this->webPrice;
    }

    /**
     * @param string $webPrice
     */
    public function setWebPrice($webPrice)
    {
        $this->webPrice = $webPrice;
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
     * @return string
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param string $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return boolean
     */
    public function getMasterItem()
    {
        return $this->masterItem;
    }

    /**
     * @param boolean $masterItem
     */
    public function setMasterItem($masterItem)
    {
        $this->masterItem = $masterItem;
    }

    /**
     * @return OrderItem
     */
    public function getOrderItems()
    {
        return $this->orderItems;
    }


}

