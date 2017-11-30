<?php

namespace Appstore\Bundle\DomainUserBundle\Entity;

use Core\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Gedmo\Mapping\Annotation as Gedmo;
/**
 * HrAttendanceMonth
 *
 * @ORM\Table(name="hr_attendance_month")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\DomainUserBundle\Repository\HrAttendanceMonthRepository")
 */
class HrAttendanceMonth
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
     * @var string
     *
     * @ORM\Column(name="PresentDay", type="smallint", length=2, nullable =true)
     */
    private $presentDay;


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
     * @ORM\Column(name="present", type="boolean")
     */
    private $present = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="in", type="boolean")
     */
    private $in = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="out", type="boolean")
     */
    private $out = false;

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
     * @return HrAttendance
     */
    public function getHrAttendance()
    {
        return $this->hrAttendance;
    }

    /**
     * @param HrAttendance $hrAttendance
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
     * @return bool
     */
    public function isIn()
    {
        return $this->in;
    }

    /**
     * @param bool $in
     */
    public function setIn($in)
    {
        $this->in = $in;
    }

    /**
     * @return bool
     */
    public function isOut()
    {
        return $this->out;
    }

    /**
     * @param bool $out
     */
    public function setOut($out)
    {
        $this->out = $out;
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
     * @return bool
     */
    public function isPresent()
    {
        return $this->present;
    }

    /**
     * @param bool $present
     */
    public function setPresent($present)
    {
        $this->present = $present;
    }

    /**
     * @return string
     */
    public function getPresentDay()
    {
        return $this->presentDay;
    }

    /**
     * @param string $presentDay
     */
    public function setPresentDay($presentDay)
    {
        $this->presentDay = $presentDay;
    }
}
