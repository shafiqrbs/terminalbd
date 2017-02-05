<?php

namespace Appstore\Bundle\EcommerceBundle\Entity;

use Appstore\Bundle\InventoryBundle\Entity\ItemColor;
use Appstore\Bundle\InventoryBundle\Entity\ItemSize;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseItem;
use Doctrine\ORM\Mapping as ORM;

/**
 * OrderReturnItem
 *
 * @ORM\Table("order_return_items")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\EcommerceBundle\Repository\OrderItemReturnRepository")
 */
class OrderReturnItem
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\OrderReturn", inversedBy="returnItems"  )
     **/
    private  $orderReturn;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\StockItem", mappedBy="orderReturnItem" )
     **/
    private  $stockItems;


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
    public function getOrderReturn()
    {
        return $this->orderReturn;
    }

    /**
     * @param mixed $orderReturn
     */
    public function setOrderReturn($orderReturn)
    {
        $this->orderReturn = $orderReturn;
    }

    /**
     * @return mixed
     */
    public function getStockItems()
    {
        return $this->stockItems;
    }

    /**
     * @param mixed $stockItems
     */
    public function setStockItems($stockItems)
    {
        $this->stockItems = $stockItems;
    }


}

