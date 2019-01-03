<?php

namespace Appstore\Bundle\DomainUserBundle\Entity;

use Appstore\Bundle\AccountingBundle\Entity\AccountOnlineOrder;
use Appstore\Bundle\AccountingBundle\Entity\AccountVendor;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoice;
use Appstore\Bundle\DmsBundle\Entity\DmsInvoice;
use Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsInvoice;
use Appstore\Bundle\EcommerceBundle\Entity\Order;
use Appstore\Bundle\HospitalBundle\Entity\Invoice;
use Appstore\Bundle\HotelBundle\Entity\HotelInvoice;
use Appstore\Bundle\InventoryBundle\Entity\Sales;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSales;
use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\LocationBundle\Entity\Location;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * Customer
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Appstore\Bundle\DomainUserBundle\Repository\CustomerRepository")
 */
class Customer
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
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="customers")
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/

    protected $globalOption;

	/**
	 * @ORM\OneToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountVendor", mappedBy="customer")
	 * @ORM\OrderBy({"companyName" = "ASC"})
	 */
	protected $accountVendor;

	/**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\Order", mappedBy="customer")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $orders;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\Invoice", mappedBy="customer")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $hmsInvoices;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\DmsBundle\Entity\DmsInvoice", mappedBy="customer")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $dmsInvoices;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsInvoice", mappedBy="customer")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $dpsInvoices;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\BusinessBundle\Entity\BusinessInvoice", mappedBy="customer")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $businessInvoices;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\RestaurantBundle\Entity\Invoice", mappedBy="customer")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $restaurantInvoice;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineSales", mappedBy="customer")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $medicineSales;

    /**
     * @ORM\OneToOne(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineVendor", mappedBy="customer")
     * @ORM\OrderBy({"companyName" = "ASC"})
     */
    protected $medicineVendor;



    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountSales", mappedBy="customer" )
     * @ORM\OrderBy({"id" = "DESC"})
     **/
    private  $accountSales;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountOnlineOrder", mappedBy="customer" )
     * @ORM\OrderBy({"id" = "DESC"})
     **/
    private  $accountOnlineOrders;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountSalesReturn", mappedBy="customer" )
     * @ORM\OrderBy({"id" = "DESC"})
     **/
    private  $accountSalesReturn;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Sales", mappedBy="customer" , cascade={"remove"})
     * @ORM\OrderBy({"updated" = "DESC"})
     **/
    protected $sales;

     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HotelBundle\Entity\HotelInvoice", mappedBy="customer" , cascade={"remove"})
     * @ORM\OrderBy({"updated" = "DESC"})
     **/
    protected $hotelInvoices;


     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\ServiceSales", mappedBy="customer" , cascade={"persist", "remove"})
     * @ORM\OrderBy({"id" = "DESC"})
     **/
    private  $serviceSales;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\DomainUserBundle\Entity\CustomerInbox", mappedBy="customer")
     **/
    protected $customerInbox;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\DomainUserBundle\Entity\UserInbox", mappedBy="customer")
     **/
    protected $userInbox;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\LocationBundle\Entity\Location", inversedBy="customers")
     **/

    protected $location;

    /**
     * @var integer
     *
     * @ORM\Column(name="code", type="integer",  nullable=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="customerId", type="string",  nullable=true)
     */
    private $customerId;

    /**
     * @var string
     *
     * @ORM\Column(name="postalCode", type="string", length=30, nullable =true)
     */
    private $postalCode;

     /**
     * @var array
     *
     * @ORM\Column(name="namePrefix", type="array", nullable =true)
     */
    private $namePrefix;

     /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable =true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="firstName", type="string", length=100, nullable =true)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="lastName", type="string", length=100, nullable =true)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="company", type="string", length=255, nullable =true)
     */
    private $company;

    /**
     * @var string
     *
     * @ORM\Column(name="permanentAddress", type="text",  nullable =true)
     */
    private $permanentAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="fatherName", type="string", length=100, nullable =true)
     */
    private $fatherName;

    /**
     * @var string
     *
     * @ORM\Column(name="motherName", type="string", length=100, nullable =true)
     */
    private $motherName;

     /**
     * @var string
     *
     * @ORM\Column(name="religion", type="string", length=100, nullable =true)
     */
    private $religion ="Islam" ;

    /**
     * @var string
     *
     * @ORM\Column(name="profession", type="string", length=100, nullable =true)
     */
    private $profession;

    /**
     * @var string
     *
     * @ORM\Column(name="nationality", type="string", length=100, nullable =true)
     */
    private $nationality = 'Bangladeshi';

    /**
     * @var string
     *
     * @ORM\Column(name="customerType", type="string", length=15, nullable =true)
     */
    private $customerType;

    /**
     * @var string
     *
     * @ORM\Column(name="mobile", type="string", length=255, nullable =true)
     */
    private $mobile;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=100, nullable =true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="facebookId", type="string", length=100, nullable =true)
     */
    private $facebookId;

    /**
     * @var string
     *
     * @ORM\Column(name="remark", type="text", nullable =true)
     */
    private $remark;

     /**
     * @var string
     *
     * @ORM\Column(name="address", type="text", nullable =true)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="bloodGroup", type="string", length=20, nullable =true)
     */
    private $bloodGroup;

    /**
     * @var string
     *
     * @ORM\Column(name="dob", type="string", nullable =true)
     */
    private $dob;

    /**
     * @var string
     *
     * @ORM\Column(name="ageGroup", type="string",length=10 , nullable = true)
     */
    private $ageGroup;

    /**
     * @var string
     *
     * @ORM\Column(name="maritalStatus", type="string",length=30 , nullable = true)
     */
    private $maritalStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="alternativeContactPerson", type="string",length=200 , nullable = true)
     */
    private $alternativeContactPerson;

    /**
     * @var string
     *
     * @ORM\Column(name="alternativeContactMobile", type="string",length=50 , nullable = true)
     */
    private $alternativeContactMobile;


    /**
     * @var string
     *
     * @ORM\Column(name="alternativeRelation", type="string",length=100 , nullable = true)
     */
    private $alternativeRelation;


    /**
     * @var integer
     *
     * @ORM\Column(name="age", type="smallint",length=3, nullable = true)
     */
    private $age;

    /**
     * @var string
     *
     * @ORM\Column(name="weight", type="string",length=50, nullable = true)
     */
    private $weight;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="bloodPressure", type="string",length=20, nullable = true)
	 */
	private $bloodPressure;

    /**
	 * @var string
	 *
	 * @ORM\Column(name="diabetes", type="string",length=30, nullable = true)
	 */
	private $diabetes;

    /**
	 * @var string
	 *
	 * @ORM\Column(name="height", type="string",length=20, nullable = true)
	 */
	private $height;

    /**
     * @var string
     *
     * @ORM\Column(name="ageType", type="string", length=20, nullable = true)
     */
    private $ageType;


    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", length=10 , nullable = true)
     */
    private $gender;


    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isNew", type="boolean")
     */
    private $isNew = true;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;


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
     * @return Customer
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
     * Set mobile
     *
     * @param string $mobile
     *
     * @return Customer
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;

        return $this;
    }

    /**
     * Get mobile
     *
     * @return string
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    public function getNameMobile()
    {

	    $nameMobile = $this->getMobile().' - '.$this->getName();
    	return $nameMobile;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Customer
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return string
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set bloodGroup
     *
     * @param string $bloodGroup
     *
     * @return Customer
     */
    public function setBloodGroup($bloodGroup)
    {
        $this->bloodGroup = $bloodGroup;

        return $this;
    }

    /**
     * Get bloodGroup
     *
     * @return string
     */
    public function getBloodGroup()
    {
        return $this->bloodGroup;
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
     * @return mixed
     */
    public function getAccountSales()
    {
        return $this->accountSales;
    }

    /**
     * @param mixed $accountSales
     */
    public function setAccountSales($accountSales)
    {
        $this->accountSales = $accountSales;
    }

    /**
     * @return string
     */
    public function getCustomerType()
    {
        return $this->customerType;
    }

    /**
     * @param string $customerType
     *
     * offline
     * online
     * wholesale
     * distributor
     * pos
     * representative
     * sms
     * contact
     * email
     * billing
     * studentParent
     * apartment
     * appointment
     * hospital
     */
    public function setCustomerType($customerType)
    {
        $this->customerType = $customerType;
    }

    /**
     * @return boolean
     */
    public function getStatus()
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
     * @return boolean
     */
    public function getIsNew()
    {
        return $this->isNew;
    }

    /**
     * @param boolean $isNew
     */
    public function setIsNew($isNew)
    {
        $this->isNew = $isNew;
    }

    /**
     * @return mixed
     */
    public function getAccountSalesReturn()
    {
        return $this->accountSalesReturn;
    }

    /**
     * @return mixed
     */
    public function getServiceSales()
    {
        return $this->serviceSales;
    }

    /**
     * @return string
     */
    public function getAgeGroup()
    {
        return $this->ageGroup;
    }

    /**
     * @param string $ageGroup
     */
    public function setAgeGroup($ageGroup)
    {
        $this->ageGroup = $ageGroup;
    }

    /**
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param string $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * @return Order
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * @return Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param Location $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return Sales
     */
    public function getSales()
    {
        return $this->sales;
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
     * @return AccountOnlineOrder
     */
    public function getAccountOnlineOrders()
    {
        return $this->accountOnlineOrders;
    }

    /**
     * @return string
     */
    public function getFacebookId()
    {
        return $this->facebookId;
    }

    /**
     * @param string $facebookId
     */
    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;
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

    /**
     * @return string
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @param string $customerId
     */
    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;
    }

    /**
     * @return int
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * @param int $age
     */
    public function setAge($age)
    {
        $this->age = $age;
    }

    /**
     * @return Invoice
     */
    public function getHmsInvoices()
    {
        return $this->hmsInvoices;
    }

    /**
     * @return string
     */
    public function getAgeType()
    {
        return $this->ageType;
    }

    /**
     * @param string $ageType
     */
    public function setAgeType($ageType)
    {
        $this->ageType = $ageType;
    }

    /**
     * @return string
     */
    public function getPermanentAddress()
    {
        return $this->permanentAddress;
    }

    /**
     * @param string $permanentAddress
     */
    public function setPermanentAddress($permanentAddress)
    {
        $this->permanentAddress = $permanentAddress;
    }

    /**
     * @return string
     */
    public function getFatherName()
    {
        return $this->fatherName;
    }

    /**
     * @param string $fatherName
     */
    public function setFatherName($fatherName)
    {
        $this->fatherName = $fatherName;
    }

    /**
     * @return string
     */
    public function getMotherName()
    {
        return $this->motherName;
    }

    /**
     * @param string $motherName
     */
    public function setMotherName($motherName)
    {
        $this->motherName = $motherName;
    }

    /**
     * @return string
     */
    public function getReligion()
    {
        return $this->religion;
    }

    /**
     * @param string $religion
     */
    public function setReligion($religion)
    {
        $this->religion = $religion;
    }

    /**
     * @return string
     */
    public function getProfession()
    {
        return $this->profession;
    }

    /**
     * @param string $profession
     */
    public function setProfession($profession)
    {
        $this->profession = $profession;
    }

    /**
     * @return string
     */
    public function getNationality()
    {
        return $this->nationality;
    }

    /**
     * @param string $nationality
     */
    public function setNationality($nationality)
    {
        $this->nationality = $nationality;
    }

    /**
     * @return string
     */
    public function getAlternativeContactPerson()
    {
        return $this->alternativeContactPerson;
    }

    /**
     * @param string $alternativeContactPerson
     */
    public function setAlternativeContactPerson($alternativeContactPerson)
    {
        $this->alternativeContactPerson = $alternativeContactPerson;
    }

    /**
     * @return string
     */
    public function getAlternativeContactMobile()
    {
        return $this->alternativeContactMobile;
    }

    /**
     * @param string $alternativeContactMobile
     */
    public function setAlternativeContactMobile($alternativeContactMobile)
    {
        $this->alternativeContactMobile = $alternativeContactMobile;
    }

    /**
     * @return string
     */
    public function getMaritalStatus()
    {
        return $this->maritalStatus;
    }

    /**
     * @param string $maritalStatus
     */
    public function setMaritalStatus($maritalStatus)
    {
        $this->maritalStatus = $maritalStatus;
    }

    /**
     * @return string
     */
    public function getAlternativeRelation()
    {
        return $this->alternativeRelation;
    }

    /**
     * @param string $alternativeRelation
     */
    public function setAlternativeRelation($alternativeRelation)
    {
        $this->alternativeRelation = $alternativeRelation;
    }

    /**
     * @return string
     */
    public function getDob()
    {
        return $this->dob;
    }

    /**
     * @param string $dob
     */
    public function setDob($dob)
    {
        $this->dob = $dob;
    }

    /**
     * @return DmsInvoice
     */
    public function getDmsInvoices()
    {
        return $this->dmsInvoices;
    }

    /**
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param string $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param int $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    /**
     * @return MedicineSales
     */
    public function getMedicineSales()
    {
        return $this->medicineSales;
    }

    /**
     * @return DpsInvoice
     */
    public function getDpsInvoices()
    {
        return $this->dpsInvoices;
    }

    /**
     * @return BusinessInvoice
     */
    public function getBusinessInvoices()
    {
        return $this->businessInvoices;
    }

	/**
	 * @return string
	 */
	public function getBloodPressure() {
		return $this->bloodPressure;
	}

	/**
	 * @param string $bloodPressure
	 */
	public function setBloodPressure( string $bloodPressure ) {
		$this->bloodPressure = $bloodPressure;
	}

	/**
	 * @return string
	 */
	public function getHeight(){
		return $this->height;
	}

	/**
	 * @param string $height
	 */
	public function setHeight( string $height ) {
		$this->height = $height;
	}

	/**
	 * @return string
	 */
	public function getDiabetes(): string {
		return $this->diabetes;
	}

	/**
	 * @param string $diabetes
	 */
	public function setDiabetes( string $diabetes ) {
		$this->diabetes = $diabetes;
	}

	/**
	 * @return HotelInvoice
	 */
	public function getHotelInvoices() {
		return $this->hotelInvoices;
	}

	/**
	 * @return string
	 */
	public function getFirstName(): string {
		return $this->firstName;
	}

	/**
	 * @param string $firstName
	 */
	public function setFirstName( string $firstName ) {
		$this->firstName = $firstName;
	}

	/**
	 * @return string
	 */
	public function getLastName(): string {
		return $this->lastName;
	}

	/**
	 * @param string $lastName
	 */
	public function setLastName( string $lastName ) {
		$this->lastName = $lastName;
	}

	/**
	 * @return string
	 */
	public function getPostalCode(): string {
		return $this->postalCode;
	}

	/**
	 * @param string $postalCode
	 */
	public function setPostalCode( string $postalCode ) {
		$this->postalCode = $postalCode;
	}

	/**
	 * @return string
	 */
	public function getRemark(): string {
		return $this->remark;
	}

	/**
	 * @param string $remark
	 */
	public function setRemark( string $remark ) {
		$this->remark = $remark;
	}

	/**
	 * @return array
	 */
	public function getNamePrefix(): array {
		return $this->namePrefix;
	}

	/**
	 * @param array $namePrefix
	 */
	public function setNamePrefix( array $namePrefix ) {
		$this->namePrefix = $namePrefix;
	}

	/**
	 * @return AccountVendor
	 */
	public function getAccountVendor() {
		return $this->accountVendor;
	}

}

