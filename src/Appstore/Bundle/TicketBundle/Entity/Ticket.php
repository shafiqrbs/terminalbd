<?php

namespace Appstore\Bundle\TicketBundle\Entity;

use Appstore\Bundle\TicketBundle\Entity\TicketFormBuilder;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * BusinessParticular
 *
 * @ORM\Table( name = "ticket_open")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\TicketBundle\Repository\TicketRepository")
 */
class Ticket
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\TicketBundle\Entity\TicketConfig", inversedBy="tickets" , cascade={"persist", "remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private $config;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\TicketBundle\Entity\TicketFormBuilder", inversedBy="tickets" , cascade={"persist", "remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private $formBuilder;


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
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=100, nullable=true)
     */
    private $type;



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
     * @return mixed
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param mixed $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return TicketFormBuilder
     */
    public function getFormBuilder()
    {
        return $this->formBuilder;
    }

    /**
     * @param mixed $formBuilder
     */
    public function setFormBuilder($formBuilder)
    {
        $this->formBuilder = $formBuilder;
    }


}

