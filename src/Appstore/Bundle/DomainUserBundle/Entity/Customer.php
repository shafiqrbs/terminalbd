<?php

namespace Appstore\Bundle\DomainUserBundle\Entity;

use Appstore\Bundle\EcommerceBundle\Entity\Order;
use Appstore\Bundle\InventoryBundle\Entity\Sales;
use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\LocationBundle\Entity\Location;

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
     **/

    protected $globalOption;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\Order", mappedBy="customers")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $orders;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountSales", mappedBy="customer" )
     * @ORM\OrderBy({"id" = "DESC"})
     **/
    private  $accountSales;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountSalesReturn", mappedBy="customer" )
     * @ORM\OrderBy({"id" = "DESC"})
     **/
    private  $accountSalesReturn;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Sales", mappedBy="customer" , cascade={"persist", "remove"})
     * @ORM\OrderBy({"updated" = "DESC"})
     **/
    protected $sales;

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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable =true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="customerType", type="string", length=255, nullable =true)
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
     * @ORM\Column(name="email", type="string", length=255, nullable =true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="text", nullable =true)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="bloodGroup", type="string", length=255, nullable =true)
     */
    private $bloodGroup;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dob", type="datetime", nullable =true)
     */
    private $dob;

    /**
     * @var string
     *
     * @ORM\Column(name="ageGroup", type="string", nullable = true)
     */
    private $ageGroup;


    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", length=255 , nullable = true)
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
     * @return Customer
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
     * Set dob
     *
     * @param \DateTime $dob
     *
     * @return Customer
     */
    public function setDob($dob)
    {
        $this->dob = $dob;

        return $this;
    }

    /**
     * Get dob
     *
     * @return \DateTime
     */
    public function getDob()
    {
        return $this->dob;
    }

    /**
     * @return mixed
     */
    public function getGlobalOption()
    {
        return $this->globalOption;
    }

    /**
     * @param mixed $globalOption
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

}

