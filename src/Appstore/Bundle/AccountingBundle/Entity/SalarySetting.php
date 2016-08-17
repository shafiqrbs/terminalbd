<?php

namespace Appstore\Bundle\AccountingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * SalarySetting
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Appstore\Bundle\AccountingBundle\Repository\SalarySettingRepository")
 */
class SalarySetting
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
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="salarySetting")
     **/

    protected $globalOption;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\PaymentSalary", mappedBy="salarySetting" )
     **/
    private  $paymentSalaries;


    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="employeeSalaries" )
     **/
    private  $user;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="process", type="string", length=50 , nullable=true)
     */
    private $process;

    /**
     * @var string
     *
     * @ORM\Column(name="salaryInfo", type="text" , nullable=true)
     */
    private $salaryInfo;

    /**
     * @var string
     *
     * @ORM\Column(name="effectedMonth", type="datetime" , nullable=true)
     */
    private $effectedMonth;

    /**
     * @var float
     *
     * @ORM\Column(name="basicAmount", type="float", nullable=true)
     */
    private $basicAmount = 0;


    /**
     * @var float
     *
     * @ORM\Column(name="bonusAmount", type="float" , nullable=true)
     */
    private $bonusAmount = 0;


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
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status=true;

    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="salarySetting" )
     **/
    private  $createdBy;


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
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="salarySettingApproved" )
     **/
    private  $approvedBy;



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
     * @return SalarySetting
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
     * Set bonus
     *
     * @param string $bonus
     *
     * @return SalarySetting
     */
    public function setBonus($bonus)
    {
        $this->bonus = $bonus;

        return $this;
    }

    /**
     * Get bonus
     *
     * @return string
     */
    public function getBonus()
    {
        return $this->bonus;
    }

    /**
     * Set bonusAmount
     *
     * @param float $bonusAmount
     *
     * @return SalarySetting
     */
    public function setBonusAmount($bonusAmount)
    {
        $this->bonusAmount = $bonusAmount;

        return $this;
    }

    /**
     * Get bonusAmount
     *
     * @return float
     */
    public function getBonusAmount()
    {
        return $this->bonusAmount;
    }


    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return SalarySetting
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
     * Set created
     *
     * @param \DateTime $created
     *
     * @return SalarySetting
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     *
     * @return SalarySetting
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
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
     * @return float
     */
    public function getBasicAmount()
    {
        return $this->basicAmount;
    }

    /**
     * @param float $basicAmount
     */
    public function setBasicAmount($basicAmount)
    {
        $this->basicAmount = $basicAmount;
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
     * @return string
     */
    public function getOther()
    {
        return $this->other;
    }

    /**
     * @param string $other
     */
    public function setOther($other)
    {
        $this->other = $other;
    }

    /**
     * @return string
     */
    public function getOtherAmount()
    {
        return $this->otherAmount;
    }

    /**
     * @param string $otherAmount
     */
    public function setOtherAmount($otherAmount)
    {
        $this->otherAmount = $otherAmount;
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
    public function getSalaryInfo()
    {
        return $this->salaryInfo;
    }

    /**
     * @param string $salaryInfo
     */
    public function setSalaryInfo($salaryInfo)
    {
        $this->salaryInfo = $salaryInfo;
    }

    /**
     * @return mixed
     */
    public function getPaymentSalaries()
    {
        return $this->paymentSalaries;
    }

    /**
     * @return string
     */
    public function getEffectedMonth()
    {
        return $this->effectedMonth;
    }

    /**
     * @param string $effectedMonth
     */
    public function setEffectedMonth($effectedMonth)
    {
        $this->effectedMonth = $effectedMonth;
    }
}

