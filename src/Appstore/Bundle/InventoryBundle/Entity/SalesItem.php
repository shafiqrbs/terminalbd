<?php

namespace Appstore\Bundle\InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SalesItem
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Appstore\Bundle\InventoryBundle\Repository\SalesItemRepository")
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
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\StockItem", mappedBy="salesItem" )
     **/
    private  $stockItem;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\PurchaseItem", inversedBy="salesItems" )
     **/
    private  $purchaseItem;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\SalesReturnItem", mappedBy="salesItem" )
     **/
    private  $salesReturnItems;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Sales", inversedBy="salesItems" )
     **/
    private  $sales;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;


    /**
     * @var string
     *
     * @ORM\Column(name="salesPrice", type="decimal")
     */
    private $salesPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="purchasePrice", type="decimal")
     */
    private $purchasePrice;

    /**
     * @var string
     *
     * @ORM\Column(name="estimatePrice", type="decimal")
     */
    private $estimatePrice;


    /**
     * @var boolean
     *
     * @ORM\Column(name="customPrice", type="boolean")
     */
    private $customPrice = false;

    /**
     * @var string
     *
     * @ORM\Column(name="subTotal", type="decimal")
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
     * @return mixed
     */
    public function getSales()
    {
        return $this->sales;
    }

    /**
     * @param mixed $sales
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
     * @return mixed
     */
    public function getSalesReturnItems()
    {
        return $this->salesReturnItems;
    }


}

