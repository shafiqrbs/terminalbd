<?php

namespace Appstore\Bundle\MedicineBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * MedicineDamage
 *
 * @ORM\Table("medicine_stock_adjustment")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\MedicineBundle\Repository\MedicineStockAdjustmentRepository")
 */
class MedicineStockAdjustment
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineConfig", inversedBy="stockAdjustment")
     **/
    private $medicineConfig;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineStock", inversedBy="stockAdjustment", cascade={"persist"} )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private $medicineStock;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineParticular", inversedBy="stockAdjustment", cascade={"persist"} )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private $adjustment;

    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="damage" )
     **/
    private  $createdBy;


    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer")
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
     * @ORM\Column(name="salePrice", type="float",nullable=true)
     */
    private $salePrice;

    /**
     * @var float
     *
     * @ORM\Column(name="subTotal", type="float",nullable=true)
     */
    private $subTotal;


    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;

    /**
     * @var string
     *
     * @ORM\Column(name="notes", type="string",  nullable=true)
     */
    private $notes;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="process", type="string", nullable=true)
	 */
	private $process = "Created";


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
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param mixed $createdBy
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param \DateTime $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /**
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param string $notes
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
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
     * @return MedicineStock
     */
    public function getMedicineStock()
    {
        return $this->medicineStock;
    }

    /**
     * @param MedicineStock $medicineStock
     */
    public function setMedicineStock($medicineStock)
    {
        $this->medicineStock = $medicineStock;
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
	 * @return string
	 */
	public function getProcess(){
		return $this->process;
	}

	/**
	 * @param string $process
	 */
	public function setProcess( string $process ) {
		$this->process = $process;
	}

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return float
     */
    public function getSalePrice()
    {
        return $this->salePrice;
    }

    /**
     * @param float $salePrice
     */
    public function setSalePrice($salePrice)
    {
        $this->salePrice = $salePrice;
    }

    /**
     * @return MedicineParticular
     */
    public function getAdjustment()
    {
        return $this->adjustment;
    }

    /**
     * @param MedicineParticular $adjustment
     */
    public function setAdjustment($adjustment)
    {
        $this->adjustment = $adjustment;
    }


}

