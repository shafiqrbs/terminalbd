<?php

namespace Appstore\Bundle\TicketBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * BusinessParticular
 *
 * @ORM\Table( name = "ticket_builder_item")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\TicketBundle\Repository\TicketBuilderItemRepository")
 */
class TicketBuilderItem
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\TicketBundle\Entity\TicketFormBuilder", inversedBy="builderItems" , cascade={"persist", "remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private $builder;


    /**
     * @var string
     *
     * @ORM\Column(name="labelName", type="string", length=255, nullable=true)
     */
    private $labelName;


    /**
     * @var string
     *
     * @ORM\Column(name="fieldType", type="string", length=100, nullable=true)
     */
    private $fieldType;

    /**
     * @var string
     *
     * @ORM\Column(name="helpText", type="string", length=100, nullable=true)
     */
    private $helpText;


    /**
     * @var string
     *
     * @ORM\Column(name="defaultValue", type="text",  nullable=true)
     */
    private $defaultValue;



    /**
     * @Gedmo\Translatable
     * @Gedmo\Slug(fields={"labelName"})
     * @ORM\Column(length=255, unique=true)
     */
    private $slug;

    /**
     * @var boolean
     *
     * @ORM\Column(name="fieldRequired", type="boolean" )
     */
    private $fieldRequired = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean" )
     */
    private $status= true;

    /**
     * @var int
     *
     * @ORM\Column(name="sorting", type="integer", nullable=true )
     */
    private $sorting = 0;

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
     * @return string
     */
    public function getFieldType()
    {
        return $this->fieldType;
    }

    /**
     * @param string $fieldType
     */
    public function setFieldType($fieldType)
    {
        $this->fieldType = $fieldType;
    }

    /**
     * @return string
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @param string $defaultValue
     */
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;
    }

    /**
     * @return TicketFormBuilder
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     * @param TicketFormBuilder $builder
     */
    public function setBuilder($builder)
    {
        $this->builder = $builder;
    }

    /**
     * @return int
     */
    public function getSorting()
    {
        return $this->sorting;
    }

    /**
     * @param int $sorting
     */
    public function setSorting($sorting)
    {
        $this->sorting = $sorting;
    }

    /**
     * @return string
     */
    public function getLabelName()
    {
        return $this->labelName;
    }

    /**
     * @param string $labelName
     */
    public function setLabelName($labelName)
    {
        $this->labelName = $labelName;
    }

    /**
     * @return bool
     */
    public function isFieldRequired()
    {
        return $this->fieldRequired;
    }

    /**
     * @param bool $fieldRequired
     */
    public function setFieldRequired($fieldRequired)
    {
        $this->fieldRequired = $fieldRequired;
    }

    /**
     * @return string
     */
    public function getHelpText()
    {
        return $this->helpText;
    }

    /**
     * @param string $helpText
     */
    public function setHelpText($helpText)
    {
        $this->helpText = $helpText;
    }


}

