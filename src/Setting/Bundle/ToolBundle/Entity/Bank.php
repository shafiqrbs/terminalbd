<?php

namespace Setting\Bundle\ToolBundle\Entity;

use Appstore\Bundle\AccountingBundle\Entity\PaymentSalary;
use Appstore\Bundle\InventoryBundle\Entity\Sales;
use Doctrine\ORM\Mapping as ORM;

/**
 * Bank
 *
 * @ORM\Table(name="banks")
 * @ORM\Entity
 */
class Bank
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
     * @ORM\OneToMany(targetEntity="Setting\Bundle\ToolBundle\Entity\PortalBankAccount", mappedBy="bank")
     */
    protected $portalBankAccount;

    /**
     * @ORM\OneToMany(targetEntity="Core\UserBundle\Entity\Profile", mappedBy="bank")
     */
    protected $profile;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountBank", mappedBy="bank")
     */
    protected $accountBanks;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\PaymentSalary", mappedBy="bank")
     */
    protected $paymentSalaries;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Sales", mappedBy="bank")
     */
    protected $sales;


    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;


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
     * @return Bank
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
     * @return mixed
     */
    public function getAccountBanks()
    {
        return $this->accountBanks;
    }


    /**
     * @return mixed
     */
    public function getPortalBankAccount()
    {
        return $this->portalBankAccount;
    }

    /**
     * @return Sales
     */
    public function getSales()
    {
        return $this->sales;
    }

    /**
     * @return PaymentSalary
     */
    public function getPaymentSalaries()
    {
        return $this->paymentSalaries;
    }
}

