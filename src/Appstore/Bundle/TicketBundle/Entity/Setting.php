<?php

namespace Appstore\Bundle\TicketBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Setting
 *
 * @ORM\Table( name = "ticket_setting")
 * @ORM\Entity(repositoryClass="")
 */
class Setting
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\TicketBundle\Entity\TicketConfig", inversedBy="settings" , cascade={"persist", "remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private $config;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\TicketBundle\Entity\SettingType", inversedBy="settings" , cascade={"persist", "remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private $type;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\TicketBundle\Entity\TicketFormBuilder", mappedBy="process")
     **/
    private $builderProcessType;


     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\TicketBundle\Entity\TicketFormBuilder", mappedBy="userType")
     **/
    private $builderUserType;


      /**
     * @ORM\OneToOne(targetEntity="Appstore\Bundle\TicketBundle\Entity\TicketFormBuilder", mappedBy="module")
     **/
    private $builderProcessModule;


     /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="shortCode", type="string", length=50, nullable=true)
     */
    private $shortCode;


    /**
     * @Gedmo\Translatable
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=255, unique = true)
     */
    private $slug;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="timestamp", type="time")
     */
    private $timestamp;


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
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
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
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param \DateTime $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return string
     */
    public function getShortCode()
    {
        return $this->shortCode;
    }

    /**
     * @param string $shortCode
     */
    public function setShortCode($shortCode)
    {
        $this->shortCode = $shortCode;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }


}

