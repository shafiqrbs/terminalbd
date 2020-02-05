<?php

namespace Appstore\Bundle\TicketBundle\Entity;

use Appstore\Bundle\OfficeBundle\Entity\Ticket;
use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * TicketConfig
 *
 * @ORM\Table("ticket_config")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\TicketBundle\Repository\TicketConfigRepository")
 */
class TicketConfig
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
     * @ORM\OneToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="ticketConfig" , cascade={"persist", "remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private $globalOption;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\TicketBundle\Entity\Setting", mappedBy="config" )
     **/
    protected $settings;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\TicketBundle\Entity\Ticket", mappedBy="config" )
     **/
    protected $tickets;

     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\TicketBundle\Entity\TicketFormBuilder", mappedBy="config" )
     **/
    protected $builder;

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
     * @return Setting
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @return Ticket
     */
    public function getTicket()
    {
        return $this->ticket;
    }
}

