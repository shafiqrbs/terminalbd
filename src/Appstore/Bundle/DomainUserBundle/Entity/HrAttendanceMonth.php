<?php

namespace Appstore\Bundle\DomainUserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * HrBlackout
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\DomainUserBundle\Entity\HrAttendance", inversedBy="hrAttendanceMonth")
     **/
    protected $hrAttendance;

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
}
