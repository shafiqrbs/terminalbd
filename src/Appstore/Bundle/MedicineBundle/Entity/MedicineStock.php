<?php

namespace Appstore\Bundle\MedicineBundle\Entity;

use Appstore\Bundle\DmsBundle\Entity\DmsInvoiceMedicine;
use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Setting\Bundle\ToolBundle\Entity\ProductUnit;

/**
 * MedicineBrand
 *
 * @ORM\Table("medicine_stock")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\MedicineBundle\Repository\MedicineStockRepository")
 */
class MedicineStock
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineBrand", inversedBy="medicineStock")
     **/
    private $medicineBrand;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicinePurchaseItem", mappedBy="medicineStock")
     **/
    private $medicinePurchaseItems;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineSalesItem", mappedBy="medicineStock")
     **/
    private $medicineSalesItems;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineConfig", inversedBy="medicineStock")
     **/
    private $medicineConfig;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\ProductUnit", inversedBy="medicineStocks")
     **/
    private $unit;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineParticular", inversedBy="medicineStockRacks")
     **/
    private $rackNo;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255,nullable = true)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="code", type="integer", nullable=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="sku", type="string", length=50, nullable = true)
     */
    private $sku;


    /**
     * @var integer
     *
     * @ORM\Column(name="openingQuantity", type="integer", nullable=true)
     */
    private $openingQuantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="minQuantity", type="integer", nullable=true)
     */
    private $minQuantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="maxQuantity", type="integer", nullable=true)
     */
    private $maxQuantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="remainingQuantity", type="integer", nullable=true)
     */
    private $remainingQuantity=0;

    /**
     * @var integer
     *
     * @ORM\Column(name="purchaseQuantity", type="integer", nullable=true)
     */
    private $purchaseQuantity=0;

    /**
     * @var integer
     *
     * @ORM\Column(name="purchaseReturnQuantity", type="integer", nullable=true)
     */
    private $purchaseReturnQuantity=0;

    /**
     * @var integer
     *
     * @ORM\Column(name="salesQuantity", type="integer", nullable=true)
     */
    private $salesQuantity=0;

    /**
     * @var integer
     *
     * @ORM\Column(name="salesReturnQuantity", type="integer", nullable=true)
     */
    private $salesReturnQuantity=0;

    /**
     * @var integer
     *
     * @ORM\Column(name="damageQuantity", type="integer", nullable=true)
     */
    private $damageQuantity=0;

    /**
     * @var float
     *
     * @ORM\Column(name="purchasePrice", type="float", nullable=true)
     */
    private $purchasePrice;


    /**
     * @var float
     *
     * @ORM\Column(name="salesPrice", type="float",  nullable=true)
     */
    private $salesPrice;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean",  nullable=true)
     */
    private $status = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="noDiscount", type="boolean",  nullable=true)
     */
    private $noDiscount = false;


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
     * @return MedicineBrand
     */
    public function getMedicineBrand()
    {
        return $this->medicineBrand;
    }

    /**
     * @param MedicineBrand $medicineBrand
     */
    public function setMedicineBrand($medicineBrand)
    {
        $this->medicineBrand = $medicineBrand;
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
    public function setPurchasePrice($purchasePrice)
    {
        $this->purchasePrice = $purchasePrice;
    }

    /**
     * @return float
     */
    public function getSalesPrice()
    {
        return $this->salesPrice;
    }

    /**
     * @param float $salesPrice
     */
    public function setSalesPrice($salesPrice)
    {
        $this->salesPrice = $salesPrice;
    }

    /**
     * @return int
     */
    public function getOpeningQuantity()
    {
        return $this->openingQuantity;
    }

    /**
     * @param int $openingQuantity
     */
    public function setOpeningQuantity($openingQuantity)
    {
        $this->openingQuantity = $openingQuantity;
    }

    /**
     * @return int
     */
    public function getMinQuantity()
    {
        return $this->minQuantity;
    }

    /**
     * @param int $minQuantity
     */
    public function setMinQuantity($minQuantity)
    {
        $this->minQuantity = $minQuantity;
    }

    /**
     * @return int
     */
    public function getMaxQuantity()
    {
        return $this->maxQuantity;
    }

    /**
     * @param int $maxQuantity
     */
    public function setMaxQuantity($maxQuantity)
    {
        $this->maxQuantity = $maxQuantity;
    }

    /**
     * @return int
     */
    public function getRemainingQuantity()
    {
        return $this->remainingQuantity;
    }

    /**
     * @param int $remainingQuantity
     */
    public function setRemainingQuantity($remainingQuantity)
    {
        $this->remainingQuantity = $remainingQuantity;
    }

    /**
     * @return int
     */
    public function getPurchaseQuantity()
    {
        return $this->purchaseQuantity;
    }

    /**
     * @param int $purchaseQuantity
     */
    public function setPurchaseQuantity($purchaseQuantity)
    {
        $this->purchaseQuantity = $purchaseQuantity;
    }

    /**
     * @return int
     */
    public function getPurchaseReturnQuantity()
    {
        return $this->purchaseReturnQuantity;
    }

    /**
     * @param int $purchaseReturnQuantity
     */
    public function setPurchaseReturnQuantity($purchaseReturnQuantity)
    {
        $this->purchaseReturnQuantity = $purchaseReturnQuantity;
    }

    /**
     * @return int
     */
    public function getSalesQuantity()
    {
        return $this->salesQuantity;
    }

    /**
     * @param int $salesQuantity
     */
    public function setSalesQuantity($salesQuantity)
    {
        $this->salesQuantity = $salesQuantity;
    }

    /**
     * @return int
     */
    public function getSalesReturnQuantity()
    {
        return $this->salesReturnQuantity;
    }

    /**
     * @param int $salesReturnQuantity
     */
    public function setSalesReturnQuantity($salesReturnQuantity)
    {
        $this->salesReturnQuantity = $salesReturnQuantity;
    }

    /**
     * @return int
     */
    public function getDamageQuantity()
    {
        return $this->damageQuantity;
    }

    /**
     * @param int $damageQuantity
     */
    public function setDamageQuantity($damageQuantity)
    {
        $this->damageQuantity = $damageQuantity;
    }

    /**
     * @return mixed
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * @param mixed $sku
     */
    public function setSku($sku)
    {
        $this->sku = $sku;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return MedicineConfig
     */
    public function getMedicineConfig()
    {
        return $this->medicineConfig;
    }

    /**
     * @param MedicineConfig $medicineConfig
     */
    public function setMedicineConfig($medicineConfig)
    {
        $this->medicineConfig = $medicineConfig;
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
     * @return bool
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param bool $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return ProductUnit
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @param ProductUnit $unit
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;
    }

    /**
     * @return bool
     */
    public function isNoDiscount()
    {
        return $this->noDiscount;
    }

    /**
     * @param bool $noDiscount
     */
    public function setNoDiscount($noDiscount)
    {
        $this->noDiscount = $noDiscount;
    }

    /**
     * @return MedicinePurchaseItem
     */
    public function getMedicinePurchaseItems()
    {
        return $this->medicinePurchaseItems;
    }

    public function getMedicineStockSkuQuantity(){

        $medicineStockSkuQuantity = $this->getSku().'-'.$this->getName().'-'.$this->getRackNo()->getName().'('.$this->getRemainingQuantity().')';
            return $medicineStockSkuQuantity;

    }

    /**
     * @return MedicineParticular
     */
    public function getRackNo()
    {
        return $this->rackNo;
    }

    /**
     * @param MedicineParticular $rackNo
     */
    public function setRackNo($rackNo)
    {
        $this->rackNo = $rackNo;
    }

    /**
     * @return MedicineSalesItem
     */
    public function getMedicineSalesItems()
    {
        return $this->medicineSalesItems;
    }

}

