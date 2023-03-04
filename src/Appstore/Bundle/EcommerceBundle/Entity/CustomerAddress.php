<?php

namespace Appstore\Bundle\EcommerceBundle\Entity;
use Doctrine\ORM\Mapping as ORM;


/**
 * ItemBrand
 *
 * @ORM\Table("ecommerce_customer_address")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\EcommerceBundle\Repository\TimePeriodRepository")
 */
class CustomerAddress
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\EcommerceConfig", inversedBy="brands" )
     **/
    private  $ecommerceConfig;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\Order", mappedBy="timePeriod")
     **/
    private $orders;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\DomainUserBundle\Entity\Customer", inversedBy="orders")
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private $customer;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="text")
     */
    private $address;

     /**
     * @var string
     *
     * @ORM\Column(name="addressMode", type="string", length=255)
     */
    private $addressMode;


    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status = true;



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
     * @return ItemBrand
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
     * Set status1
     *
     * @param boolean $status1
     *
     * @return ItemBrand
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
	 * @return EcommerceConfig
	 */
	public function getEcommerceConfig() {
		return $this->ecommerceConfig;
	}

	/**
	 * @param EcommerceConfig $ecommerceConfig
	 */
	public function setEcommerceConfig( $ecommerceConfig ) {
		$this->ecommerceConfig = $ecommerceConfig;
	}

    /**
     * @return Order
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * @return mixed
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param mixed $customer
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
    }

    /**
     * @return string
     */
    public function getAddressMode()
    {
        return $this->addressMode;
    }

    /**
     * @param string $addressMode
     */
    public function setAddressMode($addressMode)
    {
        $this->addressMode = $addressMode;
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




}

