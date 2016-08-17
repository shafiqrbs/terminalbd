<?php

namespace Appstore\Bundle\AccountingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * PaymentSalary
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Appstore\Bundle\AccountingBundle\Repository\PaymentSalaryRepository")
 */
class PaymentSalary
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
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="paymentSalary")
     **/

    protected $globalOption;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountHead", inversedBy="paymentSalary" )
     **/
    private  $accountHead;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\Transaction", mappedBy="paymentSalary" )
     **/
    private  $transactions;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\Bank", inversedBy="paymentSalaries" )
     **/
    private  $bank;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\SalarySetting", inversedBy="paymentSalaries" )
     **/
    private  $salarySetting;

    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="paymentSalaries" )
     **/
    private  $user;

    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="paymentSalary" )
     **/
    private  $createdBy;

    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="paymentSalaryApprove" )
     **/
    private  $approvedBy;


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
     * @var string
     *
     * @ORM\Column(name="salaryMonth", type="string", length=255 , nullable=true)
     */
    private $salaryMonth;



    /**
     * @var float
     *
     * @ORM\Column(name="payableAmount", type="float" , nullable=true)
     */
    private $payableAmount;


    /**
     * @var float
     *
     * @ORM\Column(name="paidAmount", type="float", nullable=true)
     */
    private $paidAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="dueAmount", type="float", nullable=true)
     */
    private $dueAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="advanceAmount", type="float", nullable=true)
     */
    private $advanceAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="otherAmount", type="float", nullable=true)
     */
    private $otherAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="totalAmount", type="float", nullable=true)
     */
    private $totalAmount = 0;




    /**
     * @var string
     *
     * @ORM\Column(name="accountNo", type="string", length=255, nullable = true)
     */
    private $accountNo;

    /**
     * @var boolean
     *
     * @ORM\Column(name="sendBank", type="boolean")
     */
    private $sendBank;

    /**
     * @var string
     *
     * @ORM\Column(name="paymentMethod", type="string", length=50, nullable=true)
     */
    private $paymentMethod;


    /**
     * @var string
     *
     * @ORM\Column(name="remark", type="string", length=255 , nullable=true)
     */
    private $remark;

    /**
     * @var string
     *
     * @ORM\Column(name="process", type="string", length=50 , nullable=true)
     */
    private $process;



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
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
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
     * @return float
     */
    public function getPayableAmount()
    {
        return $this->payableAmount;
    }

    /**
     * @param float $payableAmount
     */
    public function setPayableAmount($payableAmount)
    {
        $this->payableAmount = $payableAmount;
    }

    /**
     * @return float
     */
    public function getPaidAmount()
    {
        return $this->paidAmount;
    }

    /**
     * @param float $paidAmount
     */
    public function setPaidAmount($paidAmount)
    {
        $this->paidAmount = $paidAmount;
    }

    /**
     * @return float
     */
    public function getDueAmount()
    {
        return $this->dueAmount;
    }

    /**
     * @param float $dueAmount
     */
    public function setDueAmount($dueAmount)
    {
        $this->dueAmount = $dueAmount;
    }

    /**
     * @return float
     */
    public function getAdvanceAmount()
    {
        return $this->advanceAmount;
    }

    /**
     * @param float $advanceAmount
     */
    public function setAdvanceAmount($advanceAmount)
    {
        $this->advanceAmount = $advanceAmount;
    }

    /**
     * @return mixed
     */
    public function getSalarySetting()
    {
        return $this->salarySetting;
    }

    /**
     * @param mixed $salarySetting
     */
    public function setSalarySetting($salarySetting)
    {
        $this->salarySetting = $salarySetting;
    }

    /**
     * @return float
     */
    public function getTotalAmount()
    {
        return $this->totalAmount;
    }

    /**
     * @param float $totalAmount
     */
    public function setTotalAmount($totalAmount)
    {
        $this->totalAmount = $totalAmount;
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
     * @return float
     */
    public function getOtherAmount()
    {
        return $this->otherAmount;
    }

    /**
     * @param float $otherAmount
     */
    public function setOtherAmount($otherAmount)
    {
        $this->otherAmount = $otherAmount;
    }

    /**
     * @return string
     */
    public function getSalaryMonth()
    {
        return $this->salaryMonth;
    }

    /**
     * @param string $salaryMonth
     */
    public function setSalaryMonth($salaryMonth)
    {
        $this->salaryMonth = $salaryMonth;
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
     * @return string
     */
    public function getAccountNo()
    {
        return $this->accountNo;
    }

    /**
     * @param string $accountNo
     */
    public function setAccountNo($accountNo)
    {
        $this->accountNo = $accountNo;
    }

    /**
     * @return boolean
     */
    public function getSendBank()
    {
        return $this->sendBank;
    }

    /**
     * @param boolean $sendBank
     */
    public function setSendBank($sendBank)
    {
        $this->sendBank = $sendBank;
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
     * Payment Card
     * Other
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
    }

    /**
     * @return mixed
     */
    public function getTransactions()
    {
        return $this->transactions;
    }

}

