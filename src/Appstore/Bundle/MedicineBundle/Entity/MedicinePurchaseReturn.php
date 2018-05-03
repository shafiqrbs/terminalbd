<?php

namespace Appstore\Bundle\MedicineBundle\Entity;

use Appstore\Bundle\AccountingBundle\Entity\AccountBank;
use Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank;
use Appstore\Bundle\AccountingBundle\Entity\AccountPurchase;
use Core\UserBundle\Entity\User;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\ToolBundle\Entity\Bank;
use Setting\Bundle\ToolBundle\Entity\TransactionMethod;

/**
 * MedicinePurchaseReturn
 *
 * @ORM\Table( name ="medicine_purchase_return")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\MedicineBundle\Repository\MedicinePurchaseReturnRepository")
 */
class MedicinePurchaseReturn
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineConfig", inversedBy="medicinePurchases" , cascade={"detach","merge"} )
     **/
    private  $medicineConfig;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineVendor", inversedBy="medicinePurchases" , cascade={"detach","merge"} )
     **/
    private  $medicineVendor;


    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="medicinePurchaseReturn" )
     **/
    private  $createdBy;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicinePurchaseReturnItem", mappedBy="medicinePurchaseReturn")
     **/
    private  $medicinePurchaseReturnItems;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;


    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="medicinePurchasesReturnApprovedBy" )
     **/
    private  $approvedBy;


    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;

    /**
     * @var string
     *
     * @ORM\Column(name="invoice", type="string", length=255, nullable=true)
     */
    private $invoice;

    /**
     * @var integer
     *
     * @ORM\Column(name="code", type="integer",  nullable=true)
     */
    private $code;

    /**
     * @var float
     *
     * @ORM\Column(name="subTotal", type="float", nullable=true)
     */
    private $subTotal;

    /**
     * @var string
     *
     * @ORM\Column(name="process", type="string", nullable=true)
     */
    private $process = "created";

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
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }


    /**
     * @return User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param User $createdBy
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     * @return string
     */
    public function getProcess()
    {
        return $this->process;
    }

    /**
     * @param string $process
     * created
     * progress
     * complete
     * approved
     */
    public function setProcess($process)
    {
        $this->process = $process;
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
     * @return MedicineVendor
     */
    public function getMedicineVendor()
    {
        return $this->medicineVendor;
    }

    /**
     * @param MedicineVendor $medicineVendor
     */
    public function setMedicineVendor($medicineVendor)
    {
        $this->medicineVendor = $medicineVendor;
    }

    /**
     * @return MedicinePurchaseReturnItem
     */
    public function getMedicinePurchaseReturnItems()
    {
        return $this->medicinePurchaseReturnItems;
    }

    /**
     * @return User
     */
    public function getApprovedBy()
    {
        return $this->approvedBy;
    }

    /**
     * @param User $approvedBy
     */
    public function setApprovedBy($approvedBy)
    {
        $this->approvedBy = $approvedBy;
    }

    /**
     * @return string
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * @param string $invoice
     */
    public function setInvoice($invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param int $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }


}

