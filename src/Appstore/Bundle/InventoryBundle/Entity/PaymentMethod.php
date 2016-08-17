<?php

namespace Appstore\Bundle\InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PaymentMethod
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class PaymentMethod
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
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="paymentMethods" , cascade={"persist", "remove"})
     **/

    private $globalOption;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\PaymentType", inversedBy="paymentMethods" , cascade={"persist", "remove"})
     **/

    private $paymentType;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\Order", mappedBy="paymentMethod" , cascade={"persist", "remove"})
     */
    protected $orders;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\PreOrder", mappedBy="paymentMethod" , cascade={"persist", "remove"})
     */
    protected $preOrders;


    /**
     * @var string
     *
     * @ORM\Column(name="mobile", type="string", length=255)
     */
    private $mobile;

    /**
     * @var string
     *
     * @ORM\Column(name="bkashAccount", type="string", length=255)
     */
    private $bkashAccount;

    /**
     * @var string
     *
     * @ORM\Column(name="bkashAccountType", type="string", length=255)
     */
    private $bkashAccountType;

    /**
     * @var string
     *
     * @ORM\Column(name="addresss", type="string", length=255)
     */
    private $addresss;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="mobileBankAccount", type="string", length=255)
     */
    private $mobileBankAccount;


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
     * Set mobile
     *
     * @param string $mobile
     *
     * @return PaymentMethod
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
     * Set bkashAccount
     *
     * @param string $bkashAccount
     *
     * @return PaymentMethod
     */
    public function setBkashAccount($bkashAccount)
    {
        $this->bkashAccount = $bkashAccount;

        return $this;
    }

    /**
     * Get bkashAccount
     *
     * @return string
     */
    public function getBkashAccount()
    {
        return $this->bkashAccount;
    }

    /**
     * Set bkashAccountType
     *
     * @param string $bkashAccountType
     *
     * @return PaymentMethod
     */
    public function setBkashAccountType($bkashAccountType)
    {
        $this->bkashAccountType = $bkashAccountType;

        return $this;
    }

    /**
     * Get bkashAccountType
     *
     * @return string
     */
    public function getBkashAccountType()
    {
        return $this->bkashAccountType;
    }

    /**
     * Set addresss
     *
     * @param string $addresss
     *
     * @return PaymentMethod
     */
    public function setAddresss($addresss)
    {
        $this->addresss = $addresss;

        return $this;
    }

    /**
     * Get addresss
     *
     * @return string
     */
    public function getAddresss()
    {
        return $this->addresss;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return PaymentMethod
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
     * Set mobileBankAccount
     *
     * @param string $mobileBankAccount
     *
     * @return PaymentMethod
     */
    public function setMobileBankAccount($mobileBankAccount)
    {
        $this->mobileBankAccount = $mobileBankAccount;

        return $this;
    }

    /**
     * Get mobileBankAccount
     *
     * @return string
     */
    public function getMobileBankAccount()
    {
        return $this->mobileBankAccount;
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
    public function getPaymentType()
    {
        return $this->paymentType;
    }

    /**
     * @param mixed $paymentType
     */
    public function setPaymentType($paymentType)
    {
        $this->paymentType = $paymentType;
    }

    /**
     * @return mixed
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * @return mixed
     */
    public function getPreOrders()
    {
        return $this->preOrders;
    }
}

