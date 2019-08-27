<?php

namespace Appstore\Bundle\ProcurementBundle\Entity;

use Appstore\Bundle\EcommerceBundle\Entity\OrderItem;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * PurchaseItem
 *
 * @ORM\Table(name="pro_purchase_requisition_item")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\ProcurementBundle\Repository\PurchaseRequisitionItemRepository")
 */
class PurchaseRequisitionItem
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\TallyBundle\Entity\Item", inversedBy="purchaseRequisitionItems" )
     **/
    private  $item;


     /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\ProcurementBundle\Entity\PurchaseOrder", inversedBy="purchaseRequisitionItems" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $purchaseOrder;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable = true)
     */
    private $name;


    /**
     * @var float
     *
     * @ORM\Column(name="quantity", type="float")
     */
    private $quantity;

    /**
     * @var float
     *
     * @ORM\Column(name="issueQuantity", type="float", nullable=true)
     */
    private $issueQuantity;

    /**
     * @var float
     *
     * @ORM\Column(name="purchasePrice", type="float", nullable=true)
     */
    private $purchasePrice;


    /**
     * @var integer
     *
     * @ORM\Column(name="code", type="integer", nullable = true)
     */
    private $code;

    /**
     * @var float
     *
     * @ORM\Column(name="purchaseSubTotal", type="float", nullable = true)
     */
    private $purchaseSubTotal;

    /**
     * @var float
     *
     * @ORM\Column(name="salesPrice", type="float", nullable = true)
     */
    private $salesPrice;
    /**
     * @var float
     *
     * @ORM\Column(name="salesSubTotal", type="float", nullable = true)
     */
    private $salesSubTotal;

    /**
     * @var float
     *
     * @ORM\Column(name="webPrice", type="float", nullable = true)
     */
    private $webPrice;
    /**
     * @var float
     *
     * @ORM\Column(name="process", type="string", nullable = true)
     */
    private $process= 'created';

    /**
     * @var string
     *
     * @ORM\Column(name="barcode", type="string",  nullable = true)
     */
    private $barcode;


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
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return PurchaseItem
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
     * @return Purchase
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
     * Set salesPrice
     *
     * @param float $salesPrice
     *
     * @return PurchaseItem
     */
    public function setSalesPrice($salesPrice)
    {
        $this->salesPrice = $salesPrice;

        return $this;
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
     * Set purchaseSubTotal
     *
     * @param float $purchaseSubTotal
     *
     * @return PurchaseItem
     */
    public function setPurchaseSubTotal($purchaseSubTotal)
    {
        $this->purchaseSubTotal = $purchaseSubTotal;

        return $this;
    }

    /**
     * Get purchaseSubTotal
     *
     * @return float
     */
    public function getPurchaseSubTotal()
    {
        return $this->purchaseSubTotal;
    }

    /**
     * Set salesSubTotal
     *
     * @param float $salesSubTotal
     *
     * @return PurchaseItem
     */
    public function setSalesSubTotal($salesSubTotal)
    {
        $this->salesSubTotal = $salesSubTotal;

        return $this;
    }

    /**
     * Get salesSubTotal
     *
     * @return float
     */
    public function getSalesSubTotal()
    {
        return $this->salesSubTotal;
    }

    /**
     * @return Item
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
     * @return float
     */
    public function getWebPrice()
    {
        return $this->webPrice;
    }

    /**
     * @param float $webPrice
     */
    public function setWebPrice($webPrice)
    {
        $this->webPrice = $webPrice;
    }

    /**
     * @return float
     */
    public function getWebSubTotal()
    {
        return $this->webSubTotal;
    }

    /**
     * @param float $webSubTotal
     */
    public function setWebSubTotal($webSubTotal)
    {
        $this->webSubTotal = $webSubTotal;
    }

    /**
     * @return PurchaseRequisition
     */
    public function getPurchase()
    {
        return $this->purchase;
    }

    /**
     * @param PurchaseRequisition $purchase
     */
    public function setPurchase($purchase)
    {
        $this->purchase = $purchase;
    }

    /**
     * @return string
     */
    public function getBarcode()
    {
        return $this->barcode;
    }

    /**
     * @param string $barcode
     */
    public function setBarcode($barcode)
    {
        $this->barcode = $barcode;
    }


    /**
     * @return StockItem
     */
    public function getStockItem()
    {
        return $this->stockItem;
    }

    /**
     * @return SalesItem
     */
    public function getSalesItems()
    {
        return $this->salesItems;
    }

    /**
     * @return PurchaseVendorItem
     */
    public function getPurchaseVendorItem()
    {
        return $this->purchaseVendorItem;
    }

    /**
     * @param PurchaseVendorItem $purchaseVendorItem
     */
    public function setPurchaseVendorItem($purchaseVendorItem)
    {
        $this->purchaseVendorItem = $purchaseVendorItem;
    }

    /**
     * @return PurchaseReturnItem
     */
    public function getPurchaseReturnItem()
    {
        return $this->purchaseReturnItem;
    }

    /**
     * @return integer
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param integer $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    public function  getItemStock()
    {
        $quantity = 0;
        $i = 0;
        if(!$this->stockItem->isEmpty()) {
            foreach ($this->stockItem AS $item) {
                $quantity += $item->getQuantity(); //$recipecost now $this->recipecost.
                $i++;
            }
            return $quantity;
        }
        return 0;
    }

    /**
     * @return Damage
     */
    public function getDamages()
    {
        return $this->damages;
    }

    /**
     * @return mixed
     */
    public function getBranchInvoiceItems()
    {
        return $this->branchInvoiceItems;
    }

    /**
     * @param mixed $branchInvoiceItems
     */
    public function setBranchInvoiceItems($branchInvoiceItems)
    {
        $this->branchInvoiceItems = $branchInvoiceItems;
    }

    public function getStockItemQuantity()
    {
        $stockQnt = 0;
        foreach ($this->getStockItem() as $stock){
            $stockQnt += $stock->getQuantity();
        }
        return $stockQnt;
    }

    /**
     * @return OrderItem
     */
    public function getOrderItem()
    {
        return $this->orderItem;
    }

    /**
     * @param OrderItem $orderItem
     */
    public function setOrderItem($orderItem)
    {
        $this->orderItem = $orderItem;
    }

	/**
	 * @return float
	 */
	public function getProcess() {
		return $this->process;
	}

	/**
	 * @param float $process
	 */
	public function setProcess( $process ) {
		$this->process = $process;
	}

	/**
	 * @return mixed
	 */
	public function getIssueQuantity() {
		return $this->issueQuantity;
	}

	/**
	 * @param mixed $issueQuantity
	 */
	public function setIssueQuantity( $issueQuantity ) {
		$this->issueQuantity = $issueQuantity;
	}

	/**
	 * @return mixed
	 */
	public function getPurchaseOrder() {
		return $this->purchaseOrder;
	}

	/**
	 * @param mixed $purchaseOrder
	 */
	public function setPurchaseOrder( $purchaseOrder ) {
		$this->purchaseOrder = $purchaseOrder;
	}


}
