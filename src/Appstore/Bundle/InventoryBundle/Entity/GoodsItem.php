<?php

namespace Appstore\Bundle\InventoryBundle\Entity;

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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\ItemSize", inversedBy="goodsItem")
     */
    protected $size;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\ItemColor", inversedBy="goodsItem")
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

}

