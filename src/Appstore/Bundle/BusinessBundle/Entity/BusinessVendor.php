<?php

namespace Appstore\Bundle\BusinessBundle\Entity;

use Appstore\Bundle\AccountingBundle\Entity\AccountPurchase;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
/**
 * BusinessVendor
 *
 * @ORM\Table(name="business_vendor")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\BusinessBundle\Repository\BusinessVendorRepository")
 */
class BusinessVendor
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\BusinessBundle\Entity\BusinessConfig", inversedBy="businessVendors")
     */
    protected $businessConfig;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\BusinessBundle\Entity\BusinessPurchase", mappedBy="businessVendor")
     */
    protected $businessPurchases;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountPurchase", mappedBy="businessVendor")
     */
    protected $accountPurchases;


    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="mode", type="string", length=30, nullable=true)
     */
    private $mode ='medicine';

    /**
     * @var string
     *
     * @ORM\Column(name="vendorCode", type="string", length=20, nullable=true)
     */
    private $vendorCode;

    /**
     * @var integer
     *
     * @ORM\Column(name="code", type="integer", nullable=true)
     */
    private $code;


    /**
     * @var string
     *
     * @ORM\Column(name="companyName", type="string", length=255, nullable=true)
     */
    private $companyName;

    /**
     * @Gedmo\Slug(fields={"companyName"})
     * @Doctrine\ORM\Mapping\Column(length=255)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="text", nullable=true, nullable=true)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="mobile", type="string", length=255, nullable=true)
     */
    private $mobile;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255 , nullable=true)
     */
    private $email;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean" )
     */
    private $status=true;


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
     * @return BusinessVendor
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
     * Set address
     *
     * @param string $address
     *
     * @return BusinessVendor
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
     * Set mobile
     *
     * @param string $mobile
     *
     * @return BusinessVendor
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
     * @return string
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * @param string $companyName
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;
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
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param mixed $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }



    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }


    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }


    /**
     * @return mixed
     */
    public function getSTRPadCode()
    {
        $code = str_pad($this->getCode(),3, '0', STR_PAD_LEFT);
        return $code;
    }


    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param string $mode
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    /**
     * @return BusinessConfig
     */
    public function getBusinessConfig()
    {
        return $this->businessConfig;
    }

    /**
     * @param BusinessConfig $businessConfig
     */
    public function setBusinessConfig($businessConfig)
    {
        $this->businessConfig = $businessConfig;
    }

    /**
     * @return string
     */
    public function getVendorCode()
    {
        return $this->vendorCode;
    }

    /**
     * @param string $vendorCode
     */
    public function setVendorCode($vendorCode)
    {
        $this->vendorCode = $vendorCode;
    }

    /**
     * @return BusinessPurchase
     */
    public function getBusinessPurchases()
    {
        return $this->businessPurchases;
    }

    /**
     * @return AccountPurchase
     */
    public function getAccountPurchases()
    {
        return $this->accountPurchases;
    }


}

