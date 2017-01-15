<?php

namespace Appstore\Bundle\EcommerceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * EcommerceConfig
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Appstore\Bundle\EcommerceBundle\Repository\EcommerceConfigRepository")
 */
class EcommerceConfig
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
     * @ORM\OneToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="ecommerceConfig")
     **/

    private $globalOption;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\Template", mappedBy="ecommerceConfig"  , cascade={"persist", "remove"} )
     **/
    private  $templates;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\PreOrder", mappedBy="ecommerceConfig"  , cascade={"persist", "remove"} )
     **/
    private  $preOrders;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\Order", mappedBy="ecommerceConfig" , cascade={"persist", "remove"})
     */
    protected $orders;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\Discount", mappedBy="ecommerceConfig" , cascade={"persist", "remove"})
     */
    protected $discounts;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\Promotion", mappedBy="ecommerceConfig" , cascade={"persist", "remove"})
     */
    protected $promotions;

    /**
     * @var string
     *
     * @ORM\Column(name="pickupLocation", type="text", nullable = true)
     */
    private $pickupLocation;

    /**
     * @var float
     *
     * @ORM\Column(name="shippingCharge", type="float", nullable = true)
     */
    private $shippingCharge = 100;

    /**
     * @var integer
     *
     * @ORM\Column(name="perPage", type="smallint", nullable = true)
     */
     private $perPage = 16;

     /**
     * @var integer
     *
     * @ORM\Column(name="perColumn", type="smallint", nullable = true)
     */
     private $perColumn = 4;

    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="text",  length=2, nullable = true)
     */
     private $currency = "৳";

    /**
     * @var integer
     *
     * @ORM\Column(name="owlProductColumn", type="smallint", nullable = true)
     */
     private $owlProductColumn = 4;

    /**
     * @var boolean
     *
     * @ORM\Column(name="showMasterName", type="boolean")
     */
    private $showMasterName = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isPreorder", type="boolean")
     */
    private $isPreorder = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="cart", type="boolean")
     */
    private $cart = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isColor", type="boolean")
     */
    private $isColor = false;


    /**
     * @var boolean
     *
     * @ORM\Column(name="webProduct", type="boolean")
     */
    private $webProduct = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="promotion", type="boolean")
     */
    private $promotion = false;

    /**
     * @var float
     *
     * @ORM\Column(name="vat", type="float", nullable = true)
     */
    private $vat;

    /**
     * @var boolean
     *
     * @ORM\Column(name="vatEnable", type="boolean",  nullable=true)
     */
    private $vatEnable = false;



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
     * Set pickupLocation
     *
     * @param string $pickupLocation
     *
     * @return EcommerceConfig
     */
    public function setPickupLocation($pickupLocation)
    {
        $this->pickupLocation = $pickupLocation;

        return $this;
    }

    /**
     * Get pickupLocation
     *
     * @return string
     */
    public function getPickupLocation()
    {
        return $this->pickupLocation;
    }

    /**
     * Set isPreorder
     *
     * @param boolean $isPreorder
     *
     * @return EcommerceConfig
     */
    public function setIsPreorder($isPreorder)
    {
        $this->isPreorder = $isPreorder;

        return $this;
    }

    /**
     * Get isPreorder
     *
     * @return boolean
     */
    public function getIsPreorder()
    {
        return $this->isPreorder;
    }

    /**
     * Set cart
     *
     * @param boolean $cart
     *
     * @return EcommerceConfig
     */
    public function setCart($cart)
    {
        $this->cart = $cart;

        return $this;
    }

    /**
     * Get cart
     *
     * @return boolean
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * Set webProduct
     *
     * @param boolean $webProduct
     *
     * @return EcommerceConfig
     */
    public function setWebProduct($webProduct)
    {
        $this->webProduct = $webProduct;

        return $this;
    }

    /**
     * Get webProduct
     *
     * @return boolean
     */
    public function getWebProduct()
    {
        return $this->webProduct;
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
     * @return PreOrder
     */
    public function getPreOrders()
    {
        return $this->preOrders;
    }

    /**
     * @param PreOrder $preOrders
     */
    public function setPreOrders($preOrders)
    {
        $this->preOrders = $preOrders;
    }

    /**
     * @return Order
     */
    public function getOrders()
    {
        return $this->orders;
    }


    /**
     * @return boolean
     */
    public function isPromotion()
    {
        return $this->promotion;
    }

    /**
     * @param boolean $promotion
     */
    public function setPromotion($promotion)
    {
        $this->promotion = $promotion;
    }

    /**
     * @return int
     */
    public function getPerColumn()
    {
        return $this->perColumn;
    }

    /**
     * @param int $perColumn
     */
    public function setPerColumn($perColumn)
    {
        $this->perColumn = $perColumn;
    }

    /**
     * @return int
     */
    public function getOwlProductColumn()
    {
        return $this->owlProductColumn;
    }

    /**
     * @param int $owlProductColumn
     */
    public function setOwlProductColumn($owlProductColumn)
    {
        $this->owlProductColumn = $owlProductColumn;
    }


    /**
     * @return boolean
     */
    public function getIsColor()
    {
        return $this->isColor;
    }

    /**
     * @param boolean $isColor
     */
    public function setIsColor($isColor)
    {
        $this->isColor = $isColor;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return float
     */
    public function getShippingCharge()
    {
        return $this->shippingCharge;
    }

    /**
     * @param float $shippingCharge
     */
    public function setShippingCharge($shippingCharge)
    {
        $this->shippingCharge = $shippingCharge;
    }

    /**
     * @return float
     */
    public function getVat()
    {
        return $this->vat;
    }

    /**
     * @param float $vat
     */
    public function setVat($vat)
    {
        $this->vat = $vat;
    }

    /**
     * @return boolean
     */
    public function isVatEnable()
    {
        return $this->vatEnable;
    }

    /**
     * @param boolean $vatEnable
     */
    public function setVatEnable($vatEnable)
    {
        $this->vatEnable = $vatEnable;
    }

    /**
     * @return mixed
     */
    public function getTemplates()
    {
        return $this->templates;
    }

    /**
     * @param mixed $templates
     */
    public function setTemplates($templates)
    {
        $this->templates = $templates;
    }

    /**
     * @return int
     */
    public function getPerPage()
    {
        return $this->perPage;
    }

    /**
     * @param int $perPage
     */
    public function setPerPage($perPage)
    {
        $this->perPage = $perPage;
    }

    /**
     * @return boolean
     */
    public function getShowMasterName()
    {
        return $this->showMasterName;
    }

    /**
     * @param boolean $showMasterName
     */
    public function setShowMasterName($showMasterName)
    {
        $this->showMasterName = $showMasterName;
    }

}

