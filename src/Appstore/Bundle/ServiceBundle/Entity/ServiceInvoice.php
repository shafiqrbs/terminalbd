<?php

namespace Appstore\Bundle\ServiceBundle\Entity;

use Appstore\Bundle\AssetsBundle\Entity\Product;
use Appstore\Bundle\DomainUserBundle\Entity\Branches;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseItem;
use Appstore\Bundle\InventoryBundle\Entity\SalesItem;
use Appstore\Bundle\InventoryBundle\Entity\Vendor;
use DateTime;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * ServiceInvoice
 *
 * @ORM\Table("service_invoice")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\ServiceBundle\Repository\ServiceInvoiceRepository")
 */
class ServiceInvoice
{


	/**
	 * @Gedmo\Blameable(on="create")
	 * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="serviceInvoicesCreateBy" )
	 **/
	private  $createdBy;

	/**
	 * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="serviceInvoices" , cascade={"detach","merge"} )
	 **/
	private  $assignBy;

	/**
	 * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Vendor", inversedBy="serviceInvoices" , cascade={"detach","merge"} )
	 **/
	private  $vendor;

	/**
	 * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AssetsBundle\Entity\Product", inversedBy="serviceInvoices" , cascade={"detach","merge"} )
	 **/
	private  $product;

	/**
	 * @ORM\ManyToOne(targetEntity="Appstore\Bundle\DomainUserBundle\Entity\Branches", inversedBy="serviceInvoices" , cascade={"detach","merge"} )
	 **/
	private  $branch;


	/**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

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
     * @ORM\Column(name="invoice", type="string", length=50)
     */
    private $invoice;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="code", type="integer",  nullable=true)
	 */
	private $code;


	/**
     * @var string
     *
     * @ORM\Column(name="itemIdentifier", type="string", length= 100,nullable = true)
     */
    private $itemIdentifier;

	/**
     * @var string
     *
     * @ORM\Column(name="urgency", type="string", length=50,nullable = true)
     */
    private $urgency;

	/**
     * @var float
     *
     * @ORM\Column(name="serviceCost", type="float",nullable=true)
     */
    private $serviceCost;

	/**
     * @var float
     *
     * @ORM\Column(name="totalCost", type="float",nullable=true)
     */
    private $totalCost;

	/**
     * @var float
     *
     * @ORM\Column(name="accessoriesCost", type="float", nullable=true)
     */
    private $accessoriesCost;


	/**
     * @var string
     *
     * @ORM\Column(name="serviceType", type="string", length=50, nullable=true)
     */
    private $serviceType;


	/**
     * @var string
     *
     * @ORM\Column(name="serviceDescription", type="text", nullable=true)
     */
    private $serviceDescription;

	/**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="totalServiceHour", type="string", length=20,nullable=true)
	 */
	private $totalServiceHour;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="serviceStatus", type="string", length=50,nullable=true)
	 */
	private $serviceStatus;


	/**
	 * @var string
	 *
	 * @ORM\Column(name="responsiblePerson", type="string", length=200, nullable=true)
	 */
	private $responsiblePerson;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="responsibleMobile", type="string", length=200, nullable=true)
	 */
	private $responsibleMobile;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="assuranceType", type="string", length=50,nullable=true)
	 */
	private $assuranceType;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="process", type="string", length=25,nullable=true)
	 */
	private $process = 'Created';


	/**
	 * @var datetime
	 *
	 * @ORM\Column(name="initiationDate", type="date",nullable=true)
	 */
	private $initiationDate;

	/**
	 * @var datetime
	 *
	 * @ORM\Column(name="resolvedDate", type="date", nullable=true)
	 */
	private $resolvedDate;


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
     * @return ServiceInvoice
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
	 * @return string
	 */
	public function getUrgency() {
		return $this->urgency;
	}

	/**
	 * @param string $urgency
	 */
	public function setUrgency( $urgency ) {
		$this->urgency = $urgency;
	}

	/**
	 * @return float
	 */
	public function getServiceCost() {
		return $this->serviceCost;
	}

	/**
	 * @param float $serviceCost
	 */
	public function setServiceCost( $serviceCost ) {
		$this->serviceCost = $serviceCost;
	}

	/**
	 * @return float
	 */
	public function getTotalCost() {
		return $this->totalCost;
	}

	/**
	 * @param float $totalCost
	 */
	public function setTotalCost( $totalCost ) {
		$this->totalCost = $totalCost;
	}

	/**
	 * @return float
	 */
	public function getAccessoriesCost() {
		return $this->accessoriesCost;
	}

	/**
	 * @param float $accessoriesCost
	 */
	public function setAccessoriesCost( $accessoriesCost ) {
		$this->accessoriesCost = $accessoriesCost;
	}

	/**
	 * @return string
	 */
	public function getServiceType() {
		return $this->serviceType;
	}

	/**
	 * @param string $serviceType
	 */
	public function setServiceType( $serviceType ) {
		$this->serviceType = $serviceType;
	}

	/**
	 * @return string
	 */
	public function getServiceDescription() {
		return $this->serviceDescription;
	}

	/**
	 * @param string $serviceDescription
	 */
	public function setServiceDescription( $serviceDescription ) {
		$this->serviceDescription = $serviceDescription;
	}

	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * @param string $description
	 */
	public function setDescription( $description ) {
		$this->description = $description;
	}

	/**
	 * @return string
	 */
	public function getTotalServiceHour() {
		return $this->totalServiceHour;
	}

	/**
	 * @param string $totalServiceHour
	 */
	public function setTotalServiceHour( $totalServiceHour ) {
		$this->totalServiceHour = $totalServiceHour;
	}

	/**
	 * @return string
	 */
	public function getServiceStatus() {
		return $this->serviceStatus;
	}

	/**
	 * @param string $serviceStatus
	 */
	public function setServiceStatus( $serviceStatus ) {
		$this->serviceStatus = $serviceStatus;
	}

	/**
	 * @return string
	 */
	public function getResponsiblePerson() {
		return $this->responsiblePerson;
	}

	/**
	 * @param string $responsiblePerson
	 */
	public function setResponsiblePerson( $responsiblePerson ) {
		$this->responsiblePerson = $responsiblePerson;
	}



	/**
	 * @return DateTime
	 */
	public function getInitiationDate() {
		return $this->initiationDate;
	}

	/**
	 * @param DateTime $initiationDate
	 */
	public function setInitiationDate( $initiationDate ) {
		$this->initiationDate = $initiationDate;
	}

	/**
	 * @return DateTime
	 */
	public function getResolvedDate() {
		return $this->resolvedDate;
	}

	/**
	 * @param DateTime $resolvedDate
	 */
	public function setResolvedDate( $resolvedDate ) {
		$this->resolvedDate = $resolvedDate;
	}

	/**
	 * @return string
	 */
	public function getProcess() {
		return $this->process;
	}

	/**
	 * @param string $process
	 */
	public function setProcess( $process ) {
		$this->process = $process;
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
	 * @return string
	 */
	public function getResponsibleMobile() {
		return $this->responsibleMobile;
	}

	/**
	 * @param string $responsibleMobile
	 */
	public function setResponsibleMobile( $responsibleMobile ) {
		$this->responsibleMobile = $responsibleMobile;
	}

	/**
	 * @return User
	 */
	public function getAssignBy() {
		return $this->assignBy;
	}

	/**
	 * @param User $assignBy
	 */
	public function setAssignBy( $assignBy ) {
		$this->assignBy = $assignBy;
	}

	/**
	 * @return User
	 */
	public function getCreatedBy() {
		return $this->createdBy;
	}

	/**
	 * @param User $createdBy
	 */
	public function setCreatedBy( $createdBy ) {
		$this->createdBy = $createdBy;
	}

	/**
	 * @return string
	 */
	public function getItemIdentifier() {
		return $this->itemIdentifier;
	}

	/**
	 * @param string $itemIdentifier
	 */
	public function setItemIdentifier( $itemIdentifier ) {
		$this->itemIdentifier = $itemIdentifier;
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
	 * @return Product
	 */
	public function getProduct() {
		return $this->product;
	}

	/**
	 * @param Product $product
	 */
	public function setProduct( $product ) {
		$this->product = $product;
	}


}

