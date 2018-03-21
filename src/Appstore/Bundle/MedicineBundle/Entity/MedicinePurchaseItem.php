<?php

namespace Appstore\Bundle\MedicineBundle\Entity;

use Appstore\Bundle\MedicineBundle\Entity\MedicineStock;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * MedicinePurchaseItem
 *
 * @ORM\Table(name ="medicine_purchase_item")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\MedicineBundle\Repository\MedicinePurchaseItemRepository")
 */
class MedicinePurchaseItem
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineStock", inversedBy="medicinePurchaseItems" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $medicineStock;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicinePurchase", inversedBy="medicinePurchaseItems" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $medicinePurchase;


    /**
     * @var float
     *
     * @ORM\Column(name="quantity", type="float")
     */
    private $quantity;

    /**
     * @var float
     *
     * @ORM\Column(name="purchasePrice", type="float")
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
     * @ORM\Column(name="salesPrice", type="float", nullable = true)
     */
    private $salesPrice;

    /**
     * @var float
     *
     * @ORM\Column(name="purchaseSubTotal", type="float", nullable = true)
     */
    private $purchaseSubTotal;


    /**
     * @var string
     *
     * @ORM\Column(name="barcode", type="string",  nullable = true)
     */
    private $barcode;

    /**
     * @var datetime
     *
     * @ORM\Column(name="expirationDate", type="datetime", nullable=true)
     */
    private $expirationDate;



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
     * @return MedicinePurchaseItem
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
     * @return MedicinePurchase
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
     * @return MedicinePurchaseItem
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


    /**
     * @return float
     */
    public function getPurchaseSubTotal()
    {
        return $this->purchaseSubTotal;
    }

    /**
     * @param float $purchaseSubTotal
     */
    public function setPurchaseSubTotal($purchaseSubTotal)
    {
        $this->purchaseSubTotal = $purchaseSubTotal;
    }

    /**
     * @return MedicinePurchase
     */
    public function getMedicinePurchase()
    {
        return $this->medicinePurchase;
    }

    /**
     * @param MedicinePurchase $medicinePurchase
     */
    public function setMedicinePurchase($medicinePurchase)
    {
        $this->medicinePurchase = $medicinePurchase;
    }



    /**
     * @return mixed
     */
    public function getMedicineStock()
    {
        return $this->medicineStock;
    }

    /**
     * @param mixed $medicineStock
     */
    public function setMedicineStock($medicineStock)
    {
        $this->medicineStock = $medicineStock;
    }

    /**
     * @return DateTime
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * @param DateTime $expirationDate
     */
    public function setExpirationDate($expirationDate)
    {
        $this->expirationDate = $expirationDate;
    }


}

