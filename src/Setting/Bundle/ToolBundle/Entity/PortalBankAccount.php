<?php

namespace Setting\Bundle\ToolBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PortalBankAccount
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Setting\Bundle\ToolBundle\Repository\PortalBankAccountRepository")
 */
class PortalBankAccount
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
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\Bank", inversedBy="portalBankAccount" )
     **/
    private  $bank;

    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\ToolBundle\Entity\InvoiceSmsEmail", mappedBy="portalBankAccount" )
     **/
    private  $invoiceSmsEmails;

    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\ToolBundle\Entity\InvoiceModule", mappedBy="portalBankAccount" )
     **/
    private  $invoiceModules;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="branch", type="string", length=255)
     */
    private $branch;

    /**
     * @var string
     *
     * @ORM\Column(name="accountNo", type="string", length=255)
     */
    private $accountNo;

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
     * Set accountNo
     *
     * @param string $accountNo
     *
     * @return PortalBankAccount
     */
    public function setAccountNo($accountNo)
    {
        $this->accountNo = $accountNo;

        return $this;
    }

    /**
     * Get accountNo
     *
     * @return string
     */
    public function getAccountNo()
    {
        return $this->accountNo;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return PortalBankAccount
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
     * @return mixed
     */
    public function getBank()
    {
        return $this->bank;
    }

    /**
     * @param mixed $bank
     */
    public function setBank($bank)
    {
        $this->bank = $bank;
    }

    /**
     * @return mixed
     */
    public function getInvoiceSmsEmails()
    {
        return $this->invoiceSmsEmails;
    }

    /**
     * @return string
     */
    public function getBranch()
    {
        return $this->branch;
    }

    /**
     * @param string $branch
     */
    public function setBranch($branch)
    {
        $this->branch = $branch;
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
     * @return mixed
     */
    public function getInvoiceModules()
    {
        return $this->invoiceModules;
    }
}

