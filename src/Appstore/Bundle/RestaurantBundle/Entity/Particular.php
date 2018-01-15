<?php

namespace Appstore\Bundle\RestaurantBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Core\UserBundle\Entity\User;
use Setting\Bundle\LocationBundle\Entity\Location;
use Setting\Bundle\ToolBundle\Entity\ProductUnit;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * DmsParticular
 *
 * @ORM\Table( name = "restaurant_particular")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\RestaurantBundle\Repository\ParticularRepository")
 */
class Particular
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\RestaurantBundle\Entity\RestaurantConfig", inversedBy="particulars")
     **/
    private $restaurantConfig;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\RestaurantBundle\Entity\Service", inversedBy="particulars" )
     * @ORM\OrderBy({"sorting" = "ASC"})
     **/
    private $service;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\RestaurantBundle\Entity\Category", inversedBy="particulars" )
     * @ORM\OrderBy({"sorting" = "ASC"})
     **/
    private $category;

     /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\RestaurantBundle\Entity\ProductionElement", inversedBy="particulars" )
     * @ORM\OrderBy({"sorting" = "ASC"})
     **/
    private $productionElements;

    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="particularOperator" )
     **/
    private  $assignOperator;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\ProductUnit", inversedBy="particulars" )
     **/
    private  $unit;


    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;


    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="smallint", length=3, nullable=true)
     */
    private $quantity = 1;

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
     * @ORM\Column(name="purchaseQuantity", type="integer", nullable=true)
     */
    private $purchaseQuantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="salesQuantity", type="integer", nullable=true)
     */
    private $salesQuantity;

    /**
     * @var string
     *
     * @ORM\Column(name="purchaseAverage", type="decimal", nullable=true)
     */
    private $purchaseAverage;

    /**
     * @var string
     *
     * @ORM\Column(name="purchasePrice", type="decimal", nullable=true)
     */
    private $purchasePrice;

    /**
     * @var float
     *
     * @ORM\Column(name="overHead", type="float", nullable=true)
     */
    private $overHead;

    /**
     * @var float
     *
     * @ORM\Column(name="packaging", type="float", nullable=true)
     */
    private $packaging;

    /**
     * @var float
     *
     * @ORM\Column(name="utility", type="float", nullable=true)
     */
    private $utility;

 /**
     * @var float
     *
     * @ORM\Column(name="marketing", type="float", nullable=true)
     */
    private $marketing;


    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;


    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", nullable=true)
     */
    private $price;

    /**
     * @var string
     *
     * @ORM\Column(name="discountPrice", type="decimal", nullable=true)
     */
    private $discountPrice;

    /**
     * @var \string
     *
     * @ORM\Column(name="minimumPrice", type="decimal", nullable=true)
     */
    private $minimumPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="commission", type="decimal" , nullable=true)
     */
    private $commission;

    /**
     * @var string
     *
     * @ORM\Column(name="phoneNo", type="string", length=128, nullable=true)
     */
    private $phoneNo;

    /**
     * @var string
     *
     * @ORM\Column(name="startHour", type="string", length=10, nullable=true)
     */
    private $startHour;

    /**
     * @var string
     *
     * @ORM\Column(name="endHour", type="string", length=10, nullable=true)
     */
    private $endHour;

    /**
     * @var array
     *
     * @ORM\Column(name="weeklyOffDay", type="array", nullable=true)
     */
    private $weeklyOffDay;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=100, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="specialist", type="string", length=255, nullable=true)
     */
    private $specialist;

    /**
     * @var string
     *
     * @ORM\Column(name="educationalDegree", type="string", length=255, nullable=true)
     */
    private $educationalDegree;

    /**
     * @var string
     *
     * @ORM\Column(name="currentJob", type="string", length=256, nullable=true)
     */
    private $currentJob;

    /**
     * @var string
     *
     * @ORM\Column(name="designation", type="string", length=256, nullable=true)
     */
    private $designation;


    /**
     * @var integer
     *
     * @ORM\Column(name="code", type="integer",  nullable=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="particularCode", type="string", length=10, nullable=true)
     */
    private $particularCode;


    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="mobile", type="string", length=15, nullable=true)
     */
    private $mobile;

    /**
     * @var boolean
     *
     * @ORM\Column(name="testDuration", type="boolean" )
     */
    private $testDuration = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="reportFormat", type="boolean" )
     */
    private $reportFormat = false;


    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean" )
     */
    private $status= true;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $path;


    /**
     * @Assert\File(maxSize="8388608")
     */
    protected $file;

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
     * @return Particular
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

    public function getReferred(){

        return $this->particularCode.' - '.$this->name .' ('. $this->mobile .')/'.$this->getService()->getName();
    }

    public function getDoctor(){
        return $this->particularCode.' - '.$this->name;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Particular
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set price
     *
     * @param string $price
     *
     * @return Particular
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }



    /**
     * Set commission
     *
     * @param string $commission
     *
     * @return Particular
     */
    public function setCommission($commission)
    {
        $this->commission = $commission;

        return $this;
    }

    /**
     * Get commission
     *
     * @return string
     */
    public function getCommission()
    {
        return $this->commission;
    }

    /**
     * @return string
     */
    public function getMinimumPrice()
    {
        return $this->minimumPrice;
    }

    /**
     * @param string $minimumPrice
     */
    public function setMinimumPrice($minimumPrice)
    {
        $this->minimumPrice = $minimumPrice;
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
     * @return mixed
     */
    public function getAssignOperator()
    {
        return $this->assignOperator;
    }

    /**
     * @param User $assignOperator
     */
    public function setAssignOperator($assignOperator)
    {
        $this->assignOperator = $assignOperator;
    }

    /**
     * @return string
     */
    public function getParticularCode()
    {
        return $this->particularCode;
    }

    /**
     * @param string $particularCode
     */
    public function setParticularCode($particularCode)
    {
        $this->particularCode = $particularCode;
    }

    /**
     * @return Service
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param Service $service
     */
    public function setService($service)
    {
        $this->service = $service;
    }


    /**
     * @return HmsCategory
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param HmsCategory $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }


    /**
     * @return HmsCategory
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * @param HmsCategory $department
     */
    public function setDepartment($department)
    {
        $this->department = $department;
    }

    /**
     * @return string
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * @param string $room
     */
    public function setRoom($room)
    {
        $this->room = $room;
    }

    /**
     * @return string
     */
    public function getStartHour()
    {
        return $this->startHour;
    }

    /**
     * @param string $startHour
     */
    public function setStartHour($startHour)
    {
        $this->startHour = $startHour;
    }

    /**
     * @return string
     */
    public function getEndHour()
    {
        return $this->endHour;
    }

    /**
     * @param string $endHour
     */
    public function setEndHour($endHour)
    {
        $this->endHour = $endHour;
    }

    /**
     * @return array
     */
    public function getWeeklyOffDay()
    {
        return $this->weeklyOffDay;
    }

    /**
     * @param array $weeklyOffDay
     */
    public function setWeeklyOffDay($weeklyOffDay)
    {
        $this->weeklyOffDay = $weeklyOffDay;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getSpecialist()
    {
        return $this->specialist;
    }

    /**
     * @param string $specialist
     */
    public function setSpecialist($specialist)
    {
        $this->specialist = $specialist;
    }

    /**
     * @return string
     */
    public function getEducationalDegree()
    {
        return $this->educationalDegree;
    }

    /**
     * @param string $educationalDegree
     */
    public function setEducationalDegree($educationalDegree)
    {
        $this->educationalDegree = $educationalDegree;
    }


    /**
     * @return string
     */
    public function getPhoneNo()
    {
        return $this->phoneNo;
    }

    /**
     * @param string $phoneNo
     */
    public function setPhoneNo($phoneNo)
    {
        $this->phoneNo = $phoneNo;
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
     * @return InvoiceParticular
     */
    public function getInvoiceParticular()
    {
        return $this->invoiceParticular;
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
     * @return string
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * @param string $mobile
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * Sets file.
     *
     * @param Particular $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return Particular
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
        return 'uploads/domain/'.$this->getRestaurantConfig()->getGlobalOption()->getId().'/hms/';
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
     * @return PurchaseItem
     */
    public function getPurchaseItems()
    {
        return $this->purchaseItems;
    }


    /**
     * @param string $sepcimen
     */
    public function setSepcimen($sepcimen)
    {
        $this->sepcimen = $sepcimen;
    }

    /**
     * @return string
     */
    public function getDesignation()
    {
        return $this->designation;
    }

    /**
     * @param string $designation
     */
    public function setDesignation($designation)
    {
        $this->designation = $designation;
    }



    /**
     * @return string
     */
    public function getPurchaseAverage()
    {
        return $this->purchaseAverage;
    }

    /**
     * @param string $purchaseAverage
     */
    public function setPurchaseAverage($purchaseAverage)
    {
        $this->purchaseAverage = $purchaseAverage;
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
     * @return string
     */
    public function getPurchasePrice()
    {
        return $this->purchasePrice;
    }

    /**
     * @param string $purchasePrice
     */
    public function setPurchasePrice($purchasePrice)
    {
        $this->purchasePrice = $purchasePrice;
    }

    /**
     * @return RestaurantConfig
     */
    public function getRestaurantConfig()
    {
        return $this->restaurantConfig;
    }

    /**
     * @param RestaurantConfig $restaurantConfig
     */
    public function setRestaurantConfig($restaurantConfig)
    {
        $this->restaurantConfig = $restaurantConfig;
    }

    /**
     * @return string
     */
    public function getDiscountPrice()
    {
        return $this->discountPrice;
    }

    /**
     * @param string $discountPrice
     */
    public function setDiscountPrice($discountPrice)
    {
        $this->discountPrice = $discountPrice;
    }

    /**
     * @return float
     */
    public function getOverHead()
    {
        return $this->overHead;
    }

    /**
     * @param float $overHead
     */
    public function setOverHead($overHead)
    {
        $this->overHead = $overHead;
    }


    /**
     * @return float
     */
    public function getUtility()
    {
        return $this->utility;
    }

    /**
     * @param float $utility
     */
    public function setUtility($utility)
    {
        $this->utility = $utility;
    }

    /**
     * @return ProductionElement
     */
    public function getProductionElements()
    {
        return $this->productionElements;
    }

    /**
     * @return float
     */
    public function getPackaging()
    {
        return $this->packaging;
    }

    /**
     * @param float $packaging
     */
    public function setPackaging($packaging)
    {
        $this->packaging = $packaging;
    }

    /**
     * @return float
     */
    public function getMarketing()
    {
        return $this->marketing;
    }

    /**
     * @param float $marketing
     */
    public function setMarketing($marketing)
    {
        $this->marketing = $marketing;
    }


}

