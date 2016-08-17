<?php

namespace Appstore\Bundle\EcommerceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * PreOrderItem
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Appstore\Bundle\EcommerceBundle\Repository\PreOrderItemRepository")
 */
class PreOrderItem
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\PreOrder", inversedBy="preOrderItems"  )
     **/
    private  $preOrder;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="currencyType", type="string", length=255, nullable = true)
     */
    private $currencyType;

    /**
     * @var text
     *
     * @ORM\Column(name="url", type="text", length=255)
     */
    private $url;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity = 1;

    /**
     * @var float
     *
     * @ORM\Column(name="dollar", type="float")
     */
    private $dollar;

    /**
     * @var float
     *
     * @ORM\Column(name="totalDollar", type="float")
     */
    private $totalDollar;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float")
     */
    private $price;

    /**
     * @var float
     *
     * @ORM\Column(name="total", type="float" , nullable = true)
     */
    private $total;


    /**
     * @var float
     *
     * @ORM\Column(name="shippingCharge", type="float", nullable = true)
     */
    private $shippingCharge;


    /**
     * @var string
     *
     * @ORM\Column(name="color", type="string", length=255, nullable = true)
     */
    private $color;

    /**
     * @var string
     *
     * @ORM\Column(name="details", type="text", nullable = true)
     */
    private $details;

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
     * @return PreOrderItem
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
     * Set url
     *
     * @param string $url
     *
     * @return PreOrderItem
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return PreOrderItem
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set price
     *
     * @param float $price
     *
     * @return PreOrderItem
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set color
     *
     * @param string $color
     *
     * @return PreOrderItem
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set details
     *
     * @param string $details
     *
     * @return PreOrderItem
     */
    public function setDetails($details)
    {
        $this->details = $details;

        return $this;
    }

    /**
     * Get details
     *
     * @return string
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * @return mixed
     */
    public function getPreOrder()
    {
        return $this->preOrder;
    }

    /**
     * @param mixed $preOrder
     */
    public function setPreOrder($preOrder)
    {
        $this->preOrder = $preOrder;
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
     * @return string
     */
    public function getCurrencyType()
    {
        return $this->currencyType;
    }

    /**
     * @param string $currencyType
     */
    public function setCurrencyType($currencyType)
    {
        $this->currencyType = $currencyType;
    }

    /**
     * @return float
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param float $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * @return float
     */
    public function getTotalDollar()
    {
        return $this->totalDollar;
    }

    /**
     * @param float $totalDollar
     */
    public function setTotalDollar($totalDollar)
    {
        $this->totalDollar = $totalDollar;
    }

    /**
     * @return float
     */
    public function getDollar()
    {
        return $this->dollar;
    }

    /**
     * @param float $dollar
     */
    public function setDollar($dollar)
    {
        $this->dollar = $dollar;
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


}

