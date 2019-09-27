<?php

namespace Appstore\Bundle\TicketBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * BusinessParticular
 *
 * @ORM\Table( name = "ticket_form_builder")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\TicketBundle\Repository\TicketFormBuilderRepository")
 */
class TicketFormBuilder
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\TicketBundle\Entity\TicketConfig", inversedBy="builder" , cascade={"persist", "remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private $config;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\TicketBundle\Entity\Setting", inversedBy="builderProcessType" , cascade={"persist", "remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private $process;

    /**
     * @ORM\OneToOne(targetEntity="Appstore\Bundle\TicketBundle\Entity\Setting", inversedBy="builderProcessModule" , cascade={"persist", "remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private $module;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\TicketBundle\Entity\Setting", inversedBy="builderUserType" , cascade={"persist", "remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private $userType;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\TicketBundle\Entity\TicketBuilderItem", mappedBy="builder" , cascade={"persist", "remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private $builderItems;


     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\TicketBundle\Entity\Ticket", mappedBy="builder" , cascade={"persist", "remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private $tickets;


    /**
     * @Gedmo\Translatable
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=255, unique=true)
     */
    private $slug;


    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean" )
     */
    private $status= true;


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
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
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
     * @return bool
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param bool $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return TicketConfig
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param TicketConfig $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return Setting
     */
    public function getProcess()
    {
        return $this->process;
    }

    /**
     * @param Setting $process
     */
    public function setProcess($process)
    {
        $this->process = $process;
    }

    /**
     * @return Setting
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @param Setting $module
     */
    public function setModule($module)
    {
        $this->module = $module;
    }

    /**
     * @return Setting
     */
    public function getUserType()
    {
        return $this->userType;
    }

    /**
     * @param Setting $userType
     */
    public function setUserType($userType)
    {
        $this->userType = $userType;
    }

    /**
     * @return TicketBuilderItem
     */
    public function getBuilderItems()
    {
        return $this->builderItems;
    }


}

