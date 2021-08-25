<?php

namespace Appstore\Bundle\BusinessBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * BusinessPurchaseReturnItem
 *
 * @ORM\Table(name ="business_sales_return_item")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\BusinessBundle\Repository\BusinessDistributionReturnItemRepository")
 */
class BusinessSalesReturnItem
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\BusinessBundle\Entity\BusinessConfig", inversedBy="businessPurchasesReturns" , cascade={"detach","merge"} )
     **/
    private  $businessConfig;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\BusinessBundle\Entity\BusinessParticular", inversedBy="businessSalesReturnItem" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $businessParticular;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\BusinessBundle\Entity\BusinessInvoice", inversedBy="businessSalesReturnItem" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $businessInvoice;


    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer",nullable=true)
     */
    private $quantity;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float",nullable=true)
     */
    private $price;

    /**
     * @var float
     *
     * @ORM\Column(name="subTotal", type="float",nullable=true)
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
     * @return BusinessPurchaseReturnItem
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
     * Set purchasePrice
     *
     * @param float $purchasePrice
     *
     * @return BusinessPurchaseReturnItem
     */
    public function setPurchasePrice($purchasePrice)
    {
        $this->purchasePrice = $purchasePrice;

        return $this;
    }

    /**
     * Get purchasePrice
     *
     * @return float
     */
    public function getPurchasePrice()
    {
        return $this->purchasePrice;
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
     * @return BusinessParticular
     */
    public function getBusinessParticular()
    {
        return $this->businessParticular;
    }

    /**
     * @param BusinessParticular $businessParticular
     */
    public function setBusinessParticular($businessParticular)
    {
        $this->businessParticular = $businessParticular;
    }



    /**
     * @return int
     */
    public function getSalesInvoiceItem()
    {
        return $this->salesInvoiceItem;
    }

    /**
     * @param int $salesInvoiceItem
     */
    public function setSalesInvoiceItem($salesInvoiceItem)
    {
        $this->salesInvoiceItem = $salesInvoiceItem;
    }

    /**
     * @return int
     */
    public function getSpoilQnt()
    {
        return $this->spoilQnt;
    }

    /**
     * @param int $spoilQnt
     */
    public function setSpoilQnt($spoilQnt)
    {
        $this->spoilQnt = $spoilQnt;
    }

    /**
     * @return int
     */
    public function getDamageQnt()
    {
        return $this->damageQnt;
    }

    /**
     * @param int $damageQnt
     */
    public function setDamageQnt($damageQnt)
    {
        $this->damageQnt = $damageQnt;
    }

    /**
     * @return int
     */
    public function getDeliverQnt()
    {
        return $this->deliverQnt;
    }

    /**
     * @param int $deliverQnt
     */
    public function setDeliverQnt($deliverQnt)
    {
        $this->deliverQnt = $deliverQnt;
    }

    /**
     * @return int
     */
    public function getRemainingQnt()
    {
        return $this->remainingQnt;
    }

    /**
     * @param int $remainingQnt
     */
    public function setRemainingQnt($remainingQnt)
    {
        $this->remainingQnt = $remainingQnt;
    }

    /**
     * @return BusinessConfig
     */
    public function getBusinessConfig()
    {
        return $this->businessConfig;
    }

    /**
     * @param BusinessConfig $businessConfig
     */
    public function setBusinessConfig($businessConfig)
    {
        $this->businessConfig = $businessConfig;
    }

    /**
     * @return int
     */
    public function getSalesInvoice()
    {
        return $this->salesInvoice;
    }

    /**
     * @param int $salesInvoice
     */
    public function setSalesInvoice($salesInvoice)
    {
        $this->salesInvoice = $salesInvoice;
    }

    /**
     * @return mixed
     */
    public function getBusinessInvoice()
    {
        return $this->businessInvoice;
    }

    /**
     * @param mixed $businessInvoice
     */
    public function setBusinessInvoice($businessInvoice)
    {
        $this->businessInvoice = $businessInvoice;
    }


}

