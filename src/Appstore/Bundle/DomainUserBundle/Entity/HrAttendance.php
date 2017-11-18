<?php

namespace Appstore\Bundle\DomainUserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * HrBlackout
 *
 * @ORM\Table(name="hr_attendance")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\DomainUserBundle\Repository\HrAttendanceRepository")
 */
class HrAttendance
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
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="hrAttendance")
     **/
    protected $globalOption;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\DomainUserBundle\Entity\HrAttendanceMonth", mappedBy="hrAttendance")
     **/
    protected $hrAttendanceMonths;


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
     * @return HrAttendanceMonth
     */
    public function getHrAttendanceMonths()
    {
        return $this->hrAttendanceMonths;
    }
}
