<?php

namespace Appstore\Bundle\HumanResourceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LeavePolicy
 *
 * @ORM\Table(name="hr_leave_policy")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\HumanResourceBundle\Repository\LeavePolicyRepository")
 */
class LeavePolicy
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
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HumanResourceBundle\Entity\LeaveSetup", mappedBy="leavePolicy")
     **/
    protected $leaveSetup;


    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status;

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
     * @return LeavePolicy
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
     * Set status
     *
     * @param boolean $status
     *
     * @return LeavePolicy
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
     * @return LeaveSetup
     */
    public function getLeaveSetup()
    {
        return $this->leaveSetup;
    }
}

