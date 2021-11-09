<?php

namespace Appstore\Bundle\ProcurementBundle\Entity;

use Appstore\Bundle\AccountingBundle\Entity\AccountBank;
use Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank;
use Appstore\Bundle\AccountingBundle\Entity\AccountPurchase;
use Appstore\Bundle\DomainUserBundle\Entity\Branches;
use Appstore\Bundle\InventoryBundle\Entity\Purchase;
use Appstore\Bundle\InventoryBundle\Entity\Vendor;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Mapping\Fixture\User;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Setting\Bundle\ToolBundle\Entity\TransactionMethod;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Purchase
 *
 * @ORM\Table(name="pro_purchase_order")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\ProcurementBundle\Repository\PurchaseOrderRepository")
 */
class PurchaseOrder
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\ProcurementBundle\Entity\ProcurementConfig", inversedBy="purchaseOrders" )
     **/
    private  $config;

    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User")
     **/
    private  $createdBy;

    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User")
     **/
    private  $checkedBy;

	/**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User")
     **/
    private  $approvedBy;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\ProcurementBundle\Entity\PurchaseOrderItem", mappedBy="purchase" , cascade={"remove"})
     * @ORM\OrderBy({"id" = "ASC"})
     **/
    private  $purchaseOrderItems;


	 /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\ProcurementBundle\Entity\PurchaseRequisitionItem", mappedBy="purchaseOrder" , cascade={"remove"})
     * @ORM\OrderBy({"id" = "ASC"})
     **/
    private  $purchaseRequisitionItems;


	/**
	 * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Vendor", inversedBy="purchases" , cascade={"detach","merge"} )
	 **/
	private  $vendor;


	/**
	 * @ORM\OneToMany(targetEntity="Appstore\Bundle\ProcurementBundle\Entity\ProcurementAttachFile", mappedBy="purchaseOrder" , cascade={"detach","merge"} )
	 **/
	private  $attachFiles;


    /**
     * @var string
     *
     * @ORM\Column(name="purchaseTo", type="string", length=50, nullable=true)
     */
    private $purchaseTo;


    /**
     * @var string
     *
     * @ORM\Column(name="vendorQuotation", type="string", length = 50, nullable=true)
     */
    private $vendorQuotation;


    /**
     * @var string
     *
     * @ORM\Column(name="paymentType", type="string", length = 50, nullable=true)
     */
    private $paymentType;


	/**
	 * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\TransactionMethod", inversedBy="purchase" )
	 **/
	private  $transactionMethod;

	/**
     * @var string
     *
     * @ORM\Column(name="invoice", type="string", length=255, nullable=true)
     */
    private $invoice;

    /**
     * @var string
     *
     * @ORM\Column(name="refNo", type="string", length=255, nullable=true)
     */
    private $refNo;


    /**
     * @var datetime
     *
     * @ORM\Column(name="receiveDate", type="datetime", nullable=true)
     */
    private $receiveDate;


	/**
     * @var datetime
     *
     * @ORM\Column(name="orderDate", type="datetime", nullable=true)
     */
    private $orderDate;


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
     * @var float
     *
     * @ORM\Column(name="estimateTotal", type="float", nullable=true)
     */
    private $estimateTotal;

    /**
     * @var float
     *
     * @ORM\Column(name="totalAmount", type="float", nullable=true)
     */
    private $totalAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="grandTotal", type="float", nullable=true)
     */
    private $grandTotal;

    /**
     * @var float
     *
     * @ORM\Column(name="discount", type="float", nullable=true)
     */
    private $discount;

    /**
     * @var float
     *
     * @ORM\Column(name="paymentAmount", type="float", nullable=true)
     */
    private $paymentAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="dueAmount", type="float", nullable=true)
     */
    private $dueAmount;


    /**
     * @var float
     *
     * @ORM\Column(name="advanceAmount", type="float", nullable=true)
     */
    private $advanceAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="vatAmount", type="float", nullable=true)
     */
    private $vatAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="taxAmount", type="float", nullable=true)
     */
    private $taxAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="commissionAmount", type="float", nullable=true)
     */
    private $commissionAmount;

    /**
     * @var integer
     *
     * @ORM\Column(name="totalQnt", type="integer", nullable=true)
     */
    private $totalQnt;

    /**
     * @var integer
     *
     * @ORM\Column(name="totalItem", type="integer", nullable=true)
     */
    private $totalItem;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status=true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="computation", type="boolean")
     */
    private $computation = true;

    /**
     * @var string
     *
     * @ORM\Column(name="process", type="string", nullable=true)
     */
    private $process = "created";

    /**
     * @var string
     *
     * @ORM\Column(name="approve", type="string", nullable=true)
     */
    private $approve = "in-progress";

    /**
     * @var string
     *
     * @ORM\Column(name="grn", type="string", nullable=true)
     */
    private $grn;

    /**
     * @var integer
     *
     * @ORM\Column(name="code", type="integer",  nullable=true)
     */
    private $code;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $path;

    /**
     * @Assert\File(maxSize="8388608")
     */
    protected $file;


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
     * Set invoice
     *
     * @param string $invoice
     *
     * @return Purchase
     */
    public function setInvoice($invoice)
    {
        $this->invoice = $invoice;

        return $this;
    }

    /**
     * Get invoice
     *
     * @return string
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * Set memo
     *
     * @param string $memo
     *
     * @return Purchase
     */
    public function setMemo($memo)
    {
        $this->memo = $memo;

        return $this;
    }

    /**
     * Get memo
     *
     * @return string
     */
    public function getMemo()
    {
        return $this->memo;
    }

    /**
     * Set paymentType
     *
     * @param string $paymentType
     *
     * @return Purchase
     */
    public function setPaymentType($paymentType)
    {
        $this->paymentType = $paymentType;

        return $this;
    }

    /**
     * Get paymentType
     *
     * @return string
     */
    public function getPaymentType()
    {
        return $this->paymentType;
    }

    /**
     * Set receiveDate
     *
     * @param string $receiveDate
     *
     * @return Purchase
     */
    public function setReceiveDate($receiveDate)
    {
        $this->receiveDate = $receiveDate;

        return $this;
    }

    /**
     * Get receiveDate
     *
     * @return string
     */
    public function getReceiveDate()
    {
        return $this->receiveDate;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Purchase
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
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
     * @return float
     */
    public function getPaymentAmount()
    {
        return $this->paymentAmount;
    }

    /**
     * @param float $paymentAmount
     */
    public function setPaymentAmount($paymentAmount)
    {
        $this->paymentAmount = $paymentAmount;
    }

    /**
     * @return float
     */
    public function getDueAmount()
    {
        return $this->dueAmount;
    }

    /**
     * @param float $dueAmount
     */
    public function setDueAmount($dueAmount)
    {
        $this->dueAmount = $dueAmount;
    }

    /**
     * @return float
     */
    public function getAdvanceAmount()
    {
        return $this->advanceAmount;
    }

    /**
     * @param float $advanceAmount
     */
    public function setAdvanceAmount($advanceAmount)
    {
        $this->advanceAmount = $advanceAmount;
    }

    /**
     * @return float
     */
    public function getVatAmount()
    {
        return $this->vatAmount;
    }

    /**
     * @param float $vatAmount
     */
    public function setVatAmount($vatAmount)
    {
        $this->vatAmount = $vatAmount;
    }

    /**
     * @return float
     */
    public function getTaxAmount()
    {
        return $this->taxAmount;
    }

    /**
     * @param float $taxAmount
     */
    public function setTaxAmount($taxAmount)
    {
        $this->taxAmount = $taxAmount;
    }

    /**
     * @return int
     */
    public function getTotalQnt()
    {
        return $this->totalQnt;
    }

    /**
     * @param int $totalQnt
     */
    public function setTotalQnt($totalQnt)
    {
        $this->totalQnt = $totalQnt;
    }

    /**
     * @return int
     */
    public function getTotalItem()
    {
        return $this->totalItem;
    }

    /**
     * @param int $totalItem
     */
    public function setTotalItem($totalItem)
    {
        $this->totalItem = $totalItem;
    }


    /**
     * @return $inventoryConfig
     */
    public function getInventoryConfig()
    {
        return $this->inventoryConfig;
    }

    /**
     * @param mixed $inventoryConfig
     */
    public function setInventoryConfig($inventoryConfig)
    {
        $this->inventoryConfig = $inventoryConfig;
    }



    /**
     * @return string
     */
    public function getChalan()
    {
        return $this->chalan;
    }

    /**
     * @param string $chalan
     */
    public function setChalan($chalan)
    {
        $this->chalan = $chalan;
    }

    /**
     * @return float
     */
    public function getTotalAmount()
    {
        return $this->totalAmount;
    }

    /**
     * @param float $totalAmount
     */
    public function setTotalAmount($totalAmount)
    {
        $this->totalAmount = $totalAmount;
    }

    /**
     * @return float
     */
    public function getCommissionAmount()
    {
        return $this->commissionAmount;
    }

    /**
     * @param float $commissionAmount
     */
    public function setCommissionAmount($commissionAmount)
    {
        $this->commissionAmount = $commissionAmount;
    }

    /**
     * Sets file.
     *
     * @param Purchase $file
     */

    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return Purchase
     */
    public function getFile()
    {
        return $this->file;
    }

    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return 'uploads/domain/purchase-order/';
    }

    public function upload()
    {
        // the file property can be empty if the field is not required
        if (null === $this->getFile()) {
            return;
        }

        // use the original file name here but you should
        // sanitize it at least to avoid any security issues

        // move takes the target directory and then the
        // target filename to move to
        $this->getFile()->move(
            $this->getUploadRootDir(),
            $this->getFile()->getClientOriginalName()
        );

        // set the path property to the filename where you've saved the file
        $this->path = $this->getFile()->getClientOriginalName();

        // clean up the file property as you won't need it anymore
        $this->file = null;
    }

    /**
     * @return boolean
     */
    public function isStatus()
    {
        return $this->status;
    }

    /**
     * @param boolean $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return $purchaseItems
     */
    public function getPurchaseItems()
    {
        return $this->purchaseItems;
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
     * @return mixed
     */
    public function getApprovedBy()
    {
        return $this->approvedBy;
    }

    /**
     * @param mixed $approvedBy
     */
    public function setApprovedBy($approvedBy)
    {
        $this->approvedBy = $approvedBy;
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
     * @return string
     */
    public function getGrn()
    {
        return $this->grn;
    }

    /**
     * @param string $grn
     */
    public function setGrn($grn)
    {
        $this->grn = $grn;
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
    public function getPurchaseTo()
    {
        return $this->purchaseTo;
    }

    /**
     * @param string $purchaseTo
     */
    public function setPurchaseTo($purchaseTo)
    {
        $this->purchaseTo = $purchaseTo;
    }

    /**
     * @return bool
     */
    public function getAsInvestment()
    {
        return $this->asInvestment;
    }

    /**
     * @param bool $asInvestment
     */
    public function setAsInvestment($asInvestment)
    {
        $this->asInvestment = $asInvestment;
    }

    public function  getPurchaseItemSum()
    {
        $quantity = 0;
        foreach($this->purchaseItems AS $item) {
            $quantity += $item->getQuantity(); //$recipecost now $this->recipecost.
        }
        return $quantity ;
    }

	/**
	 * @return User
	 */
	public function getCheckedBy() {
		return $this->checkedBy;
	}

	/**
	 * @param User $checkedBy
	 */
	public function setCheckedBy( $checkedBy ) {
		$this->checkedBy = $checkedBy;
	}

	/**
	 * @return Branches
	 */
	public function getBranch() {
		return $this->branch;
	}

	/**
	 * @param Branches $branch
	 */
	public function setBranch( $branch ) {
		$this->branch = $branch;
	}

	/**
	 * @return string
	 */
	public function getApprove() {
		return $this->approve;
	}

	/**
	 * @param string $approve
	 */
	public function setApprove( $approve ) {
		$this->approve = $approve;
	}

	/**
	 * @return PurchaseOrder
	 */
	public function getPurchaseRequisitionItems() {
		return $this->purchaseRequisitionItems;
	}

	/**
	 * @return float
	 */
	public function getEstimateTotal() {
		return $this->estimateTotal;
	}

	/**
	 * @param float $estimateTotal
	 */
	public function setEstimateTotal( $estimateTotal ) {
		$this->estimateTotal = $estimateTotal;
	}

	/**
	 * @return datetime
	 */
	public function getOrderDate() {
		return $this->orderDate;
	}

	/**
	 * @param datetime $orderDate
	 */
	public function setOrderDate( $orderDate ) {
		$this->orderDate = $orderDate;
	}

	/**
	 * @return Vendor
	 */
	public function getVendor() {
		return $this->vendor;
	}

	/**
	 * @param Vendor $vendor
	 */
	public function setVendor( $vendor ) {
		$this->vendor = $vendor;
	}

	/**
	 * @return string
	 */
	public function getVendorQuotation() {
		return $this->vendorQuotation;
	}

	/**
	 * @param string $vendorQuotation
	 */
	public function setVendorQuotation( $vendorQuotation ) {
		$this->vendorQuotation = $vendorQuotation;
	}

	/**
	 * @return TransactionMethod
	 */
	public function getTransactionMethod() {
		return $this->transactionMethod;
	}

	/**
	 * @param TransactionMethod $transactionMethod
	 */
	public function setTransactionMethod( $transactionMethod ) {
		$this->transactionMethod = $transactionMethod;
	}

	/**
	 * @return string
	 */
	public function getRefNo() {
		return $this->refNo;
	}

	/**
	 * @param string $refNo
	 */
	public function setRefNo( $refNo ) {
		$this->refNo = $refNo;
	}

	/**
	 * @return bool
	 */
	public function isComputation() {
		return $this->computation;
	}

	/**
	 * @param bool $computation
	 */
	public function setComputation( $computation ) {
		$this->computation = $computation;
	}

	/**
	 * @return ProcurementAttachFile
	 */
	public function getAttachFiles() {
		return $this->attachFiles;
	}

	/**
	 * @return float
	 */
	public function getGrandTotal() {
		return $this->grandTotal;
	}

	/**
	 * @param float $grandTotal
	 */
	public function setGrandTotal( $grandTotal ) {
		$this->grandTotal = $grandTotal;
	}

	/**
	 * @return float
	 */
	public function getDiscount() {
		return $this->discount;
	}

	/**
	 * @param float $discount
	 */
	public function setDiscount( $discount ) {
		$this->discount = $discount;
	}

	/**
	 * @return PurchaseOrder
	 */
	public function getPurchases() {
		return $this->purchases;
	}

    /**
     * @return GlobalOption
     */
    public function getGlobalOption()
    {
        return $this->globalOption;
    }

    /**
     * @param GlobalOption $globalOption
     */
    public function setGlobalOption($globalOption)
    {
        $this->globalOption = $globalOption;
    }

    /**
     * @return PurchaseOrderItem
     */
    public function getPurchaseOrderItems()
    {
        return $this->purchaseOrderItems;
    }

    /**
     * @return PurchaseOrder
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param PurchaseOrder $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

}

