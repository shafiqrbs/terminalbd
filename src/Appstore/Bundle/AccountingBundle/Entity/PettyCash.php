<?php

namespace Appstore\Bundle\AccountingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * PettyCash
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Appstore\Bundle\AccountingBundle\Repository\PettyCashRepository")
 */
class PettyCash
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
     * @ORM\ManyToOne(targetEntity="PettyCash", inversedBy="children", cascade={"detach","merge"})
     * @ORM\JoinColumn(name="parent", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="PettyCash" , mappedBy="parent")
     * @ORM\OrderBy({"updated" = "ASC"})
     **/
    private $children;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\InventoryConfig", inversedBy="pettyCash" )
     **/
    private  $inventoryConfig;


    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="pettyCash")
     **/

    protected $globalOption;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountHead", inversedBy="pettyCash" )
     **/
    private  $accountHead;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\Transaction", mappedBy="pettyCash" )
     **/
    private  $transactions;


    /**
     * @var string
     *
     * @ORM\Column(name="paymentMethod", type="string", length=50, nullable=true)
     */
    private $paymentMethod;

    /**
     * @var string
     *
     * @ORM\Column(name="toIncrease", type="string", length=255 , nullable=true)
     */
    private $toIncrease;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float", nullable=true)
     */
    private $amount;


    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="pettyCash" )
     **/
    private  $createdBy;


    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="pettyCashToUser" )
     **/
    private  $toUser;

    /**
     * @var string
     *
     * @ORM\Column(name="remark", type="string", length=255, nullable = true)
     */
    private $remark;

    /**
     * @var string
     *
     * @ORM\Column(name="process", type="string", length=255, nullable = true)
     */
    private $process;

    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="pettyCashApprove" )
     **/
    private  $approvedBy;


    /**
     * @var datetime
     *
     * @ORM\Column(name="receiveDate", type="datetime", nullable=true)
     */
    private $receiveDate;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return string
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * @param string $paymentMethod
     * Cash
     * Cheque
     * bkash
     * Payment Card
     * Invoice
     * Other
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
    }


    /**
     * Set amount
     *
     * @param float $amount
     *
     * @return PettyCash
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
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
     * @return mixed
     */
    public function getInventoryConfig()
    {
        return $this->inventoryConfig;
    }

    /**
     * @param mixed $inventoryConfig
     */
    public function setInventoryConfig($inventoryConfig)
    {
        $this->inventoryConfig = $inventoryConfig;
    }

    /**
     * @return mixed
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * @param mixed $transaction
     */
    public function setTransaction($transaction)
    {
        $this->transaction = $transaction;
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
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param mixed $createdBy
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     * @return datetime
     */
    public function getReceiveDate()
    {
        return $this->receiveDate;
    }

    /**
     * @param datetime $receiveDate
     */
    public function setReceiveDate($receiveDate)
    {
        $this->receiveDate = $receiveDate;
    }


    /**
     * @return mixed
     */
    public function getToUser()
    {
        return $this->toUser;
    }

    /**
     * @param mixed $toUser
     */
    public function setToUser($toUser)
    {
        $this->toUser = $toUser;
    }

    /**
     * @return string
     */
    public function getRemark()
    {
        return $this->remark;
    }

    /**
     * @param string $remark
     */
    public function setRemark($remark)
    {
        $this->remark = $remark;
    }

    /**
     * @return mixed
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
    public function getAccountHead()
    {
        return $this->accountHead;
    }

    /**
     * @param mixed $accountHead
     */
    public function setAccountHead($accountHead)
    {
        $this->accountHead = $accountHead;
    }

    /**
     * @return PattyCash
     */
    public function getChildren()
    {
        return $this->children;
    }

    public function  getReturnAmount()
    {
        $getReturnAmount = 0;
        foreach($this->children AS $child) {
            //if ($child->getParent()->getId() == $parent ) {
                $getReturnAmount += $child->getAmount();
            //}

        }
        return $getReturnAmount ;
    }

    /**
     * @return mixed
     */
    public function getApprovedBy()
    {
        return $this->approvedBy;
    }

    /**
     * @param mixed $approvedBy
     */
    public function setApprovedBy($approvedBy)
    {
        $this->approvedBy = $approvedBy;
    }

    /**
     * @return string
     */
    public function getProcess()
    {
        return $this->process;
    }

    /**
     * @param string $process
     */
    public function setProcess($process)
    {
        $this->process = $process;
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
    public function getTransactions()
    {
        return $this->transactions;
    }


}

