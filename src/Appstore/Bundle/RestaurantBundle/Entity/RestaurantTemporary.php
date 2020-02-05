<?php

namespace Appstore\Bundle\RestaurantBundle\Entity;

use Core\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * RestaurantTemporarySales
 *
 * @ORM\Table( name = "restaurant_temporary")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\RestaurantBundle\Repository\RestaurantTemporaryRepository")
 */
class RestaurantTemporary
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\RestaurantBundle\Entity\RestaurantConfig", inversedBy="restaurantTemp" , cascade={"detach","merge"} )
     **/
    private  $restaurantConfig;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\RestaurantBundle\Entity\Particular", inversedBy="restaurantTemp")
     **/
    private $particular;

    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="restaurantTemps")
     **/
    private $user;


    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="smallint")
     */
    private $quantity = 1;

    /**
     * @var float
     *
     * @ORM\Column(name="salesPrice", type="float")
     */
    private $salesPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="estimatePrice", type="decimal", nullable=true)
     */
    private $estimatePrice;


    /**
     * @var boolean
     *
     * @ORM\Column(name="customPrice", type="boolean")
     */
    private $customPrice = false;

    /**
     * @var float
     *
     * @ORM\Column(name="subTotal", type="float")
     */
    private $subTotal;


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
     * @return float
     */
    public function getSalesPrice()
    {
        return $this->salesPrice;
    }

    /**
     * @param float $salesPrice
     */
    public function setSalesPrice($salesPrice)
    {
        $this->salesPrice = $salesPrice;
    }

    /**
     * @return string
     */
    public function getEstimatePrice()
    {
        return $this->estimatePrice;
    }

    /**
     * @param string $estimatePrice
     */
    public function setEstimatePrice($estimatePrice)
    {
        $this->estimatePrice = $estimatePrice;
    }

    /**
     * @return bool
     */
    public function isCustomPrice()
    {
        return $this->customPrice;
    }

    /**
     * @param bool $customPrice
     */
    public function setCustomPrice($customPrice)
    {
        $this->customPrice = $customPrice;
    }

    /**
     * @return float
     */
    public function getSubTotal()
    {
        return $this->subTotal;
    }

    /**
     * @param float $subTotal
     */
    public function setSubTotal($subTotal)
    {
        $this->subTotal = $subTotal;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
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
     * @return Particular
     */
    public function getParticular()
    {
        return $this->particular;
    }

    /**
     * @param Particular $particular
     */
    public function setParticular($particular)
    {
        $this->particular = $particular;
    }
}

