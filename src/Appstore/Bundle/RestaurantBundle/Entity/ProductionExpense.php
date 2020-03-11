<?php

namespace Appstore\Bundle\RestaurantBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Terminalbd\InventoryBundle\Entity\Item;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * ProductionElement
 *
 * @ORM\Table(name ="res_production_expense")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\RestaurantBundle\Repository\ProductionExpenseRepository")
 */
class ProductionExpense
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\RestaurantBundle\Entity\Particular", inversedBy="productionExpense" )
     **/
    private  $productionItem;


     /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\RestaurantBundle\Entity\ProductionBatch", inversedBy="productionExpense" )
     **/
    private  $productionBatch;


     /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\RestaurantBundle\Entity\InvoiceParticular", inversedBy="productionExpense" )
     **/
    private  $salesItem;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\RestaurantBundle\Entity\ProductionElement", inversedBy="productionExpenseItem" )
     **/
    private  $productionElement;

     /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\RestaurantBundle\Entity\Particular", inversedBy="productionExpenseItem" )
     **/
    private  $productionMaterial;

    /**
     * @var float
     *
     * @ORM\Column(name="quantity", type="float", nullable= true)
     */
    private $quantity;


    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;


    /**
     * Get id
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getProductionItem()
    {
        return $this->productionItem;
    }

    /**
     * @param mixed $productionItem
     */
    public function setProductionItem($productionItem)
    {
        $this->productionItem = $productionItem;
    }

    /**
     * @return mixed
     */
    public function getProductionBatch()
    {
        return $this->productionBatch;
    }

    /**
     * @param mixed $productionBatch
     */
    public function setProductionBatch($productionBatch)
    {
        $this->productionBatch = $productionBatch;
    }

    /**
     * @return mixed
     */
    public function getProductionElement()
    {
        return $this->productionElement;
    }

    /**
     * @param mixed $productionElement
     */
    public function setProductionElement($productionElement)
    {
        $this->productionElement = $productionElement;
    }

    /**
     * @return Particular
     */
    public function getProductionMaterial()
    {
        return $this->productionMaterial;
    }

    /**
     * @param Particular $productionMaterial
     */
    public function setProductionMaterial($productionMaterial)
    {
        $this->productionMaterial = $productionMaterial;
    }

    /**
     * @return InvoiceParticular
     */
    public function getSalesItem()
    {
        return $this->salesItem;
    }

    /**
     * @param InvoiceParticular $salesItem
     */
    public function setSalesItem($salesItem)
    {
        $this->salesItem = $salesItem;
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


}

