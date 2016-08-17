<?php

namespace Appstore\Bundle\AccountingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AccountHead
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Appstore\Bundle\AccountingBundle\Repository\AccountHeadRepository")
 */
class AccountHead
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
     * @ORM\ManyToOne(targetEntity="AccountHead", inversedBy="children", cascade={"detach","merge"})
     * @ORM\JoinColumn(name="parent", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="AccountHead" , mappedBy="parent")
     * @ORM\OrderBy({"name" = "ASC"})
     **/
    private $children;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountPurchase", mappedBy="accountHead" )
     * @ORM\OrderBy({"id" = "DESC"})
     **/
    private  $accountPurchases;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountSales", mappedBy="accountHead" )
     * @ORM\OrderBy({"id" = "DESC"})
     **/
    private  $accountSales;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\PettyCash", mappedBy="accountHead" )
     * @ORM\OrderBy({"id" = "DESC"})
     **/
    private  $pettyCash;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\Expenditure", mappedBy="accountHead" )
     * @ORM\OrderBy({"id" = "DESC"})
     **/
    private  $expenditure;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountBank", mappedBy="accountHead" )
     * @ORM\OrderBy({"id" = "DESC"})
     **/
    private  $accountBank;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\PaymentSalary", mappedBy="accountHead" )
     * @ORM\OrderBy({"id" = "DESC"})
     **/
    private  $paymentSalary;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\Transaction", mappedBy="accountHead" )
     * @ORM\OrderBy({"id" = "DESC"})
     **/
    private  $transactions;



    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, nullable= true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="toIncrease", type="string", length=255)
     */
    private $toIncrease;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isParent", type="boolean")
     */
    private $isParent;

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
     * Set code
     *
     * @param string $code
     *
     * @return AccountHead
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return AccountHead
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
     * @return accountHead
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param mixed $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return boolean
     */
    public function getIsParent()
    {
        return $this->isParent;
    }

    /**
     * @param boolean $isParent
     */
    public function setIsParent($isParent)
    {
        $this->isParent = $isParent;
    }

    /**
     * @return string
     */
    public function getToIncrease()
    {
        return $this->toIncrease;
    }

    /**
     * @param string $toIncrease
     */
    public function setToIncrease($toIncrease)
    {
        $this->toIncrease = $toIncrease;
    }

    /**
     * @return mixed
     */
    public function getAccountPurchases()
    {
        return $this->accountPurchases;
    }

    /**
     * @return mixed
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return mixed
     */
    public function getTransactions()
    {
        return $this->transactions;
    }

    /**
     * @param mixed $transactions
     */
    public function setTransactions($transactions)
    {
        $this->transactions = $transactions;
    }

    /**
     * @return mixed
     */
    public function getAccountSales()
    {
        return $this->accountSales;
    }

}

