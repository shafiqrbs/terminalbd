<?php

namespace Appstore\Bundle\BusinessBundle\Entity;

use Appstore\Bundle\AccountingBundle\Entity\AccountVendor;
use Appstore\Bundle\HospitalBundle\Entity\InvoiceParticular;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * BusinessInvoiceParticular
 *
 * @ORM\Table( name = "business_invoice_particular")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\BusinessBundle\Repository\BusinessInvoiceParticularRepository")
 */
class BusinessInvoiceParticular
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\BusinessBundle\Entity\BusinessInvoice", inversedBy="businessInvoiceParticulars")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @ORM\OrderBy({"id" = "ASC"})
     **/
    private $businessInvoice;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\BusinessBundle\Entity\BusinessParticular", inversedBy="businessInvoiceParticulars", cascade={"persist"} )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private $businessParticular;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountVendor", inversedBy="businessInvoiceParticulars", cascade={"persist"} )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private $vendor;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\BusinessBundle\Entity\BusinessProductionExpense", mappedBy="businessInvoiceParticular", cascade={"persist"} )
     **/
    private $businessProductionExpense;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\BusinessBundle\Entity\BusinessVendorStockItem", inversedBy="businessInvoiceParticular", cascade={"persist"} )
     **/
    private $vendorStockItem;

    /**
     * @var string
     *
     * @ORM\Column(name="unit", type="string", length=225, nullable=true)
     */
    private $unit;

    /**
     * @var string
     *
     * @ORM\Column(name="particular", type="text", nullable=true)
     */
    private $particular;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="smallint", length = 5, nullable=true)
     */
    private $quantity = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float", nullable=true)
     */
    private $price;

    /**
     * @var float
     *
     * @ORM\Column(name="height", type="float", nullable=true)
     */
    private $height = 0;


    /**
     * @var float
     *
     * @ORM\Column(name="width", type="float", nullable=true)
     */
    private $width = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="subQuantity", type="float", nullable=true)
     */
    private $subQuantity = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="totalQuantity", type="float", nullable=true)
     */
    private $totalQuantity = 0;


    /**
     * @var float
     *
     * @ORM\Column(name="purchasePrice", type="float", nullable=true)
     */
    private $purchasePrice = 0;


    /**
     * @var float
     *
     * @ORM\Column(name="subTotal", type="float", nullable=true)
     */
    private $subTotal = 0;


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
     * @return BusinessInvoice
     */
    public function getBusinessInvoice()
    {
        return $this->businessInvoice;
    }

    /**
     * @param BusinessInvoice $businessInvoice
     */
    public function setBusinessInvoice($businessInvoice)
    {
        $this->businessInvoice = $businessInvoice;
    }

    /**
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @param string $unit
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;
    }

    /**
     * @return string
     */
    public function getParticular()
    {
        return $this->particular;
    }

    /**
     * @param string $particular
     */
    public function setParticular($particular)
    {
        $this->particular = $particular;
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
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
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
     * @return float
     */
    public function getPurchasePrice()
    {
        return $this->purchasePrice;
    }

    /**
     * @param float $purchasePrice
     */
    public function setPurchasePrice(float $purchasePrice)
    {
        $this->purchasePrice = $purchasePrice;
    }

    /**
     * @return BusinessProductionExpense
     */
    public function getBusinessProductionExpense()
    {
        return $this->businessProductionExpense;
    }

    /**
     * @return float
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param float $height
     */
    public function setHeight(float $height)
    {
        $this->height = $height;
    }

    /**
     * @return float
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param float $width
     */
    public function setWidth(float $width)
    {
        $this->width = $width;
    }

    /**
     * @return float
     */
    public function getSubQuantity()
    {
        return $this->subQuantity;
    }

    /**
     * @param float $subQuantity
     */
    public function setSubQuantity(float $subQuantity)
    {
        $this->subQuantity = $subQuantity;
    }

    /**
     * @return float
     */
    public function getTotalQuantity()
    {
        return $this->totalQuantity;
    }

    /**
     * @param float $totalQuantity
     */
    public function setTotalQuantity(float $totalQuantity)
    {
        $this->totalQuantity = $totalQuantity;
    }

    /**
     * @return AccountVendor
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * @param AccountVendor $vendor
     */
    public function setVendor($vendor)
    {
        $this->vendor = $vendor;
    }

    /**
     * @return BusinessVendorStockItem
     */
    public function getVendorStockItem()
    {
        return $this->vendorStockItem;
    }

    /**
     * @param BusinessVendorStockItem $vendorStockItem
     */
    public function setVendorStockItem($vendorStockItem)
    {
        $this->vendorStockItem = $vendorStockItem;
    }


}

