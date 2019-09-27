<?php

namespace Appstore\Bundle\TicketBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Setting
 *
 * @ORM\Table( name = "ticket_setting_type")
 * @ORM\Entity(repositoryClass="")
 */
class SettingType
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\TicketBundle\Entity\TicketConfig", inversedBy="settingType" , cascade={"persist", "remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private $config;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\TicketBundle\Entity\Setting", mappedBy="type" , cascade={"persist", "remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private $settings;


     /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;


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
     * @return Setting
     */
    public function getSettings()
    {
        return $this->settings;
    }


}

