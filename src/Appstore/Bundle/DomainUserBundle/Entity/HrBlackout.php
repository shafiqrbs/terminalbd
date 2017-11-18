<?php

namespace Appstore\Bundle\DomainUserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * HrBlackout
 *
 * @ORM\Table(name="hr_blackout")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\DomainUserBundle\Repository\HrBlackoutRepository")
 */
class HrBlackout
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
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="blackout")
     **/

    protected $globalOption;

    /**
     * @var array
     *
     * @ORM\Column(name="blackoutDate", type="array" , nullable=true)
     */
    private $blackoutDate;

    /**
     * @var text
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;

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
     * Set blackoutDate
     *
     * @param string $blackoutDate
     * @return Blackout
     */
    public function setBlackoutDate($blackoutDate)
    {
        $this->blackoutDate = $blackoutDate;
        return $this;
    }

    /**
     * Get blackoutDate
     *
     * @return string 
     */
    public function getBlackoutDate()
    {
        return $this->blackoutDate;
    }

    /**
     * Get blackoutDate
     *
     * @return string
     */
    public function getOffBlackoutDate()
    {
        return explode(',' , $this->blackoutDate);
    }

    /**
     * @return text
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param  $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }


    /**
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param boolean $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
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
}
