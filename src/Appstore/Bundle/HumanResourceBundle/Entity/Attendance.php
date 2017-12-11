<?php

namespace Appstore\Bundle\HumanResourceBundle\Entity;

use Core\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * HrBlackout
 *
 * @ORM\Table(name="hrb_attendance")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\HumanResourceBundle\Repository\AttendanceRepository")
 */
class Attendance
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
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="domainUser")
     **/
    protected $globalOption;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\DomainUserBundle\Entity\HrAttendance", inversedBy="hrAttendanceMonth")
     **/
    protected $hrAttendance;

    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="userAttendance")
     **/
    private  $user;

    /**
     * @var string
     *
     * @ORM\Column(name="year", type="string", length=20, nullable =true)
     */
    private $year;

    /**
     * @var string
     *
     * @ORM\Column(name="month", type="string", length=30, nullable =true)
     */
    private $month;

    /**
     * @var integer
     *
     * @ORM\Column(name="workingDay", type="integer", nullable =true)
     */
    private $workingDay;

    /**
     * @var integer
     *
     * @ORM\Column(name="present", type="integer", nullable =true)
     */
    private $present;

    /**
     * @var integer
     *
     * @ORM\Column(name="absence", type="integer", nullable =true)
     */
    private $absence;

    /**
     * @var integer
     *
     * @ORM\Column(name="casualLeave", type="integer", nullable =true)
     */
    private $casualLeave;

    /**
     * @var integer
     *
     * @ORM\Column(name="earnedLeave", type="integer", nullable =true)
     */
    private $earnedLeave;

    /**
     * @var integer
     *
     * @ORM\Column(name="sickLeave", type="integer", nullable =true)
     */
    private $sickLeave;

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
     * @return Attendance
     */
    public function getHrAttendance()
    {
        return $this->hrAttendance;
    }

    /**
     * @param Attendance $hrAttendance
     */
    public function setHrAttendance($hrAttendance)
    {
        $this->hrAttendance = $hrAttendance;
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
     * @return string
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param string $year
     */
    public function setYear($year)
    {
        $this->year = $year;
    }

    /**
     * @return string
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * @param string $month
     */
    public function setMonth($month)
    {
        $this->month = $month;
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
     * @return int
     */
    public function getPresent()
    {
        return $this->present;
    }

    /**
     * @param int $present
     */
    public function setPresent($present)
    {
        $this->present = $present;
    }

    /**
     * @return int
     */
    public function getAbsence()
    {
        return $this->absence;
    }

    /**
     * @param int $absence
     */
    public function setAbsence($absence)
    {
        $this->absence = $absence;
    }

    /**
     * @return int
     */
    public function getCasualLeave()
    {
        return $this->casualLeave;
    }

    /**
     * @param int $casualLeave
     */
    public function setCasualLeave($casualLeave)
    {
        $this->casualLeave = $casualLeave;
    }

    /**
     * @return int
     */
    public function getEarnedLeave()
    {
        return $this->earnedLeave;
    }

    /**
     * @param int $earnedLeave
     */
    public function setEarnedLeave($earnedLeave)
    {
        $this->earnedLeave = $earnedLeave;
    }

    /**
     * @return int
     */
    public function getSickLeave()
    {
        return $this->sickLeave;
    }

    /**
     * @param int $sickLeave
     */
    public function setSickLeave($sickLeave)
    {
        $this->sickLeave = $sickLeave;
    }

    /**
     * @return int
     */
    public function getWorkingDay()
    {
        return $this->workingDay;
    }

    /**
     * @param int $workingDay
     */
    public function setWorkingDay($workingDay)
    {
        $this->workingDay = $workingDay;
    }
}
