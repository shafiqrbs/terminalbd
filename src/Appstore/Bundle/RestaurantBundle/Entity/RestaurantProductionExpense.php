<?php

namespace Appstore\Bundle\RestaurantBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * BusinessProductionElement
 *
 * @ORM\Table(name ="restaurant_production_expense")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\RestaurantBundle\Repository\RestaurantProductionExpenseRepository")
 */
class RestaurantProductionExpense
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\BusinessBundle\Entity\BusinessInvoiceParticular", inversedBy="businessProductionExpense" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $businessInvoiceParticular;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\BusinessBundle\Entity\BusinessProduction", inversedBy="businessProductionExpense" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $businessProduction;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\BusinessBundle\Entity\BusinessParticular", inversedBy="businessProductionExpense" )
     **/
    private  $productionItem;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\BusinessBundle\Entity\BusinessParticular", inversedBy="businessProductionExpenseItem" )
     **/
    private  $productionElement;

    /**
     * @var float
     *
     * @ORM\Column(name="quantity", type="float")
     */
    private $quantity;

    /**
     * @var float
     *
     * @ORM\Column(name="purchasePrice", type="float", nullable = true)
     */
    private $purchasePrice;


    /**
     * @var float
     *
     * @ORM\Column(name="salesPrice", type="float", nullable = true)
     */
    private $salesPrice;


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
     * @param integer $quantity
     */

    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
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
     * @param float $purchasePrice
     */
    public function setPurchasePrice($purchasePrice)
    {
        $this->purchasePrice = $purchasePrice;
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
     * Set salesPrice
     * @param float $salesPrice
     */
    public function setSalesPrice($salesPrice)
    {
        $this->salesPrice = $salesPrice;
    }

    /**
     * Get salesPrice
     *
     * @return float
     */
    public function getSalesPrice()
    {
        return $this->salesPrice;
    }

    /**
     * @return BusinessInvoiceParticular
     */
    public function getBusinessInvoiceParticular()
    {
        return $this->businessInvoiceParticular;
    }

    /**
     * @param BusinessInvoiceParticular $businessInvoiceParticular
     */
    public function setBusinessInvoiceParticular($businessInvoiceParticular)
    {
        $this->businessInvoiceParticular = $businessInvoiceParticular;
    }

    /**
     * @return BusinessParticular
     */
    public function getProductionItem()
    {
        return $this->productionItem;
    }

    /**
     * @param BusinessParticular $productionItem
     */
    public function setProductionItem($productionItem)
    {
        $this->productionItem = $productionItem;
    }

    /**
     * @return BusinessParticular
     */
    public function getProductionElement()
    {
        return $this->productionElement;
    }

    /**
     * @param BusinessParticular $productionElement
     */
    public function setProductionElement($productionElement)
    {
        $this->productionElement = $productionElement;
    }

	/**
	 * @return BusinessProduction
	 */
	public function getBusinessProduction() {
		return $this->businessProduction;
	}

	/**
	 * @param BusinessProduction $businessProduction
	 */
	public function setBusinessProduction( $businessProduction ) {
		$this->businessProduction = $businessProduction;
	}


}

