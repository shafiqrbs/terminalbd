<?php

namespace Appstore\Bundle\BusinessBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * BusinessPurchaseReturnItem
 *
 * @ORM\Table(name ="business_purchase_return_item")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\BusinessBundle\Repository\BusinessPurchaseReturnItemRepository")
 */
class BusinessPurchaseReturnItem
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\BusinessBundle\Entity\BusinessPurchaseReturn", inversedBy="businessPurchaseReturnItems" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $businessPurchaseReturn;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\BusinessBundle\Entity\BusinessParticular", inversedBy="businessPurchaseReturnItems" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $businessParticular;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\BusinessBundle\Entity\BusinessPurchaseItem", inversedBy="businessPurchaseReturnItems" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $businessPurchaseItem;


    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer",nullable=true)
     */
    private $quantity;

    /**
     * @var float
     *
     * @ORM\Column(name="purchasePrice", type="float",nullable=true)
     */
    private $purchasePrice;

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
     * @return BusinessPurchaseReturn
     */
    public function getBusinessPurchaseReturn()
    {
        return $this->businessPurchaseReturn;
    }

    /**
     * @param BusinessPurchaseReturn $businessPurchaseReturn
     */
    public function setBusinessPurchaseReturn($businessPurchaseReturn)
    {
        $this->businessPurchaseReturn = $businessPurchaseReturn;
    }

    /**
     * @return BusinessPurchaseItem
     */
    public function getBusinessPurchaseItem()
    {
        return $this->businessPurchaseItem;
    }

    /**
     * @param BusinessPurchaseItem $businessPurchaseItem
     */
    public function setBusinessPurchaseItem($businessPurchaseItem)
    {
        $this->businessPurchaseItem = $businessPurchaseItem;
    }


}
