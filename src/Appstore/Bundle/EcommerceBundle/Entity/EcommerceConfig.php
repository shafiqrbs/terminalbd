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
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\PreOrder", mappedBy="ecommerceConfig"  , cascade={"persist", "remove"} )
     **/
    private  $preOrders;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\Order", mappedBy="ecommerceConfig" , cascade={"persist", "remove"})
     */
    protected $orders;


    /**
     * @var string
     *
     * @ORM\Column(name="pickupLocation", type="text", nullable = true)
     */
    private $pickupLocation;

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
    private $cart = false;

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
     * @param Order $orders
     */
    public function setOrders($orders)
    {
        $this->orders = $orders;
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


}

