<?php

namespace Appstore\Bundle\AssetsBundle\Entity;

use Appstore\Bundle\DomainUserBundle\Entity\Branches;
use Appstore\Bundle\InventoryBundle\Entity\Item;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseItem;
use Appstore\Bundle\InventoryBundle\Entity\SalesItem;
use Appstore\Bundle\InventoryBundle\Entity\Vendor;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Product\Bundle\ProductBundle\Entity\Category;

/**
 * Product
 *
 * @ORM\Table("assets_product")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\AssetsBundle\Repository\ProductRepository")
 */
class Product
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
	 * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AssetsBundle\Entity\DepreciationModel", inversedBy="products" )
	 **/
	private  $depreciation;

	/**
	 * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AssetsBundle\Entity\Particular", inversedBy="products" )
	 **/
	private  $depreciationStatus;

	/**
	 * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Item", inversedBy="products" )
	 **/
	private  $item;

	/**
	 * @ORM\OneToMany(targetEntity="Appstore\Bundle\AssetsBundle\Entity\Distribution", mappedBy="product" )
	 **/
	private  $distributions;

	/**
	 * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\SalesItem", inversedBy="products" )
	 **/
	private  $salesItem;

	/**
	 * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\PurchaseItem", inversedBy="products" )
	 **/
	private  $purchaseItem;


	/**
	 * @ORM\ManyToOne(targetEntity="Appstore\Bundle\DomainUserBundle\Entity\Branches", inversedBy="products" )
	 **/
	private  $branch;

	/**
	 * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Vendor", inversedBy="products" )
	 **/
	private  $vendor;

	/**
	 * @ORM\ManyToOne(targetEntity="Product\Bundle\ProductBundle\Entity\Category", inversedBy="assetProducts" )
	 **/
	private  $category;

	/**
	 * @ORM\ManyToOne(targetEntity="Product\Bundle\ProductBundle\Entity\Category", inversedBy="childProducts" )
	 **/
	private  $parentCategory;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="purchaseRequisition", type="string", length=50, nullable=true)
	 */
	private $purchaseRequisition;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="serialNo", type="string", length=100, nullable=true)
	 */
	private $serialNo;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="branchSerialNo", type="string", length=100, nullable=true)
	 */
	private $branchSerialNo;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="tags", type="string", length=100, nullable=true)
	 */
	private $tags;


	/**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;


    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255, nullable=true)
     */
    private $slug;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="quantity", type="integer", nullable=true)
	 */
	private $quantity;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="assuranceType", type="string", nullable=true)
	 */
	private $assuranceType;

	/**
	 * @var datetime
	 *
	 * @ORM\Column(name="expiredDate", type="date", nullable=true)
	 */
	private $expiredDate;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="purchasePrice", type="float", nullable=true)
	 */
	private $purchasePrice;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="servicePrice", type="float", nullable=true)
	 */
	private $servicePrice;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="depreciationValue", type="float", nullable=true)
	 */
	private $depreciationValue;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="bookValue", type="float", nullable=true)
	 */
	private $bookValue;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="salvageValue", type="float", nullable=true)
	 */
	private $salvageValue;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="straightLineValue", type="float", nullable=true)
	 */
	private $straightLineValue;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="straightLinePercentage", type="float", nullable=true)
	 */
	private $straightLinePercentage;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="reducingBalancePercentage", type="float", nullable=true)
	 */
	private $reducingBalancePercentage;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="depreciationRate", type="float", nullable=true)
	 */
	private $depreciationRate;


	/**
	 * @var integer
	 *
	 * @ORM\Column(name="code", type="integer",  nullable=true)
	 */
	private $code;

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
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status = true;

	/**
     * @var boolean
     *
     * @ORM\Column(name="customDepreciation", type="boolean")
     */
    private $customDepreciation = false;


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
     * Set name
     *
     * @param string $name
     *
     * @return Product
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Product
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return Product
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

	/**
	 * @return DepreciationModel
	 */
	public function getDepreciation() {
		return $this->depreciation;
	}

	/**
	 * @param DepreciationModel $depreciation
	 */
	public function setDepreciation( $depreciation ) {
		$this->depreciation = $depreciation;
	}

	/**
	 * @return Particular
	 */
	public function getDepreciationStatus() {
		return $this->depreciationStatus;
	}

	/**
	 * @param Particular $depreciationStatus
	 */
	public function setDepreciationStatus( $depreciationStatus ) {
		$this->depreciationStatus = $depreciationStatus;
	}

	/**
	 * @return Item
	 */
	public function getItem() {
		return $this->item;
	}

	/**
	 * @param Item $item
	 */
	public function setItem( $item ) {
		$this->item = $item;
	}

	/**
	 * @return SalesItem
	 */
	public function getSalesItem() {
		return $this->salesItem;
	}

	/**
	 * @param SalesItem $salesItem
	 */
	public function setSalesItem( $salesItem ) {
		$this->salesItem = $salesItem;
	}

	/**
	 * @return PurchaseItem
	 */
	public function getPurchaseItem() {
		return $this->purchaseItem;
	}

	/**
	 * @param PurchaseItem $purchaseItem
	 */
	public function setPurchaseItem( $purchaseItem ) {
		$this->purchaseItem = $purchaseItem;
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
	 * @return Category
	 */
	public function getCategory() {
		return $this->category;
	}

	/**
	 * @param Category $category
	 */
	public function setCategory( $category ) {
		$this->category = $category;
	}

	/**
	 * @return Category
	 */
	public function getParentCategory() {
		return $this->parentCategory;
	}

	/**
	 * @param Category $parentCategory
	 */
	public function setParentCategory( $parentCategory ) {
		$this->parentCategory = $parentCategory;
	}

	/**
	 * @return string
	 */
	public function getPurchaseRequisition() {
		return $this->purchaseRequisition;
	}

	/**
	 * @param string $purchaseRequisition
	 */
	public function setPurchaseRequisition( $purchaseRequisition ) {
		$this->purchaseRequisition = $purchaseRequisition;
	}

	/**
	 * @return string
	 */
	public function getSerialNo() {
		return $this->serialNo;
	}

	/**
	 * @param string $serialNo
	 */
	public function setSerialNo( $serialNo ) {
		$this->serialNo = $serialNo;
	}

	/**
	 * @return string
	 */
	public function getBranchSerialNo() {
		return $this->branchSerialNo;
	}

	/**
	 * @param string $branchSerialNo
	 */
	public function setBranchSerialNo( $branchSerialNo ) {
		$this->branchSerialNo = $branchSerialNo;
	}

	/**
	 * @return string
	 */
	public function getTags() {
		return $this->tags;
	}

	/**
	 * @param string $tags
	 */
	public function setTags( $tags ) {
		$this->tags = $tags;
	}

	/**
	 * @return float
	 */
	public function getQuantity() {
		return $this->quantity;
	}

	/**
	 * @param float $quantity
	 */
	public function setQuantity( $quantity ) {
		$this->quantity = $quantity;
	}

	/**
	 * @return string
	 */
	public function getAssuranceType() {
		return $this->assuranceType;
	}

	/**
	 * @param string $assuranceType
	 */
	public function setAssuranceType( $assuranceType ) {
		$this->assuranceType = $assuranceType;
	}

	/**
	 * @return DateTime
	 */
	public function getExpiredDate() {
		return $this->expiredDate;
	}

	/**
	 * @param DateTime $expiredDate
	 */
	public function setExpiredDate( $expiredDate ) {
		$this->expiredDate = $expiredDate;
	}

	/**
	 * @return float
	 */
	public function getPurchasePrice() {
		return $this->purchasePrice;
	}

	/**
	 * @param float $purchasePrice
	 */
	public function setPurchasePrice( $purchasePrice ) {
		$this->purchasePrice = $purchasePrice;
	}

	/**
	 * @return float
	 */
	public function getServicePrice() {
		return $this->servicePrice;
	}

	/**
	 * @param float $servicePrice
	 */
	public function setServicePrice( $servicePrice ) {
		$this->servicePrice = $servicePrice;
	}

	/**
	 * @return float
	 */
	public function getBookValue() {
		return $this->bookValue;
	}

	/**
	 * @param float $bookValue
	 */
	public function setBookValue( $bookValue ) {
		$this->bookValue = $bookValue;
	}

	/**
	 * @return float
	 */
	public function getSalvageValue() {
		return $this->salvageValue;
	}

	/**
	 * @param float $salvageValue
	 */
	public function setSalvageValue( $salvageValue ) {
		$this->salvageValue = $salvageValue;
	}

	/**
	 * @return DateTime
	 */
	public function getCreated() {
		return $this->created;
	}

	/**
	 * @param DateTime $created
	 */
	public function setCreated( $created ) {
		$this->created = $created;
	}

	/**
	 * @return DateTime
	 */
	public function getUpdated() {
		return $this->updated;
	}

	/**
	 * @param DateTime $updated
	 */
	public function setUpdated( $updated ) {
		$this->updated = $updated;
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
	 * @return int
	 */
	public function getCode() {
		return $this->code;
	}

	/**
	 * @param int $code
	 */
	public function setCode( $code ) {
		$this->code = $code;
	}

	/**
	 * @return float
	 */
	public function getDepreciationValue() {
		return $this->depreciationValue;
	}

	/**
	 * @param float $depreciationValue
	 */
	public function setDepreciationValue( $depreciationValue ) {
		$this->depreciationValue = $depreciationValue;
	}

	/**
	 * @return Distribution
	 */
	public function getDistributions() {
		return $this->distributions;
	}

	public function productItem()
	{
		return $product = $this->getItem()->getName().' ('.$this->getName().')';
	}

	public function productItemSerial()
	{
		return $product = $this->getItem()->getName().' ('.$this->getName().') - '.$this->getSerialNo() .'=> BDT.'.$this->getBookValue();
	}

	/**
	 * @return bool
	 */
	public function isCustomDepreciation() {
		return $this->customDepreciation;
	}

	/**
	 * @param bool $customDepreciation
	 */
	public function setCustomDepreciation( $customDepreciation ) {
		$this->customDepreciation = $customDepreciation;
	}

	/**
	 * @return float
	 */
	public function getStraightLineValue() {
		return $this->straightLineValue;
	}

	/**
	 * @param float $straightLineValue
	 */
	public function setStraightLineValue( $straightLineValue ) {
		$this->straightLineValue = $straightLineValue;
	}

	/**
	 * @return float
	 */
	public function getStraightLinePercentage() {
		return $this->straightLinePercentage;
	}

	/**
	 * @param float $straightLinePercentage
	 */
	public function setStraightLinePercentage( $straightLinePercentage ) {
		$this->straightLinePercentage = $straightLinePercentage;
	}

	/**
	 * @return float
	 */
	public function getReducingBalancePercentage() {
		return $this->reducingBalancePercentage;
	}

	/**
	 * @param float $reducingBalancePercentage
	 */
	public function setReducingBalancePercentage( $reducingBalancePercentage ) {
		$this->reducingBalancePercentage = $reducingBalancePercentage;
	}

	/**
	 * @return float
	 */
	public function getDepreciationRate() {
		return $this->depreciationRate;
	}

	/**
	 * @param float $depreciationRate
	 */
	public function setDepreciationRate( $depreciationRate ) {
		$this->depreciationRate = $depreciationRate;
	}


}

