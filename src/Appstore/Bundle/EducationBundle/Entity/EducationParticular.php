<?php

namespace Appstore\Bundle\EducationBundle\Entity;

use Appstore\Bundle\EducationBundle\Form\ParticularType;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * ElectionParticular
 *
 * @ORM\Table( name ="education_particular")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\EducationBundle\Repository\ElectionParticularRepository")
 */
class EducationParticular
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\EducationBundle\Entity\EducationConfig", inversedBy="particulars" , cascade={"detach","merge"} )
     **/
    private  $educationConfig;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\EducationBundle\Entity\EducationParticularType", inversedBy="particulars" , cascade={"detach","merge"} )
     **/
    private  $particularType;


	/**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=true)
     */
    private $name;

	/**
     * @var string
     *
     * @ORM\Column(name="defineSlug", type="string", length=100, nullable=true)
     */
    private $defineSlug;

	/**
	 * @Gedmo\Slug(fields={"name"})
	 * @Doctrine\ORM\Mapping\Column(length=100)
	 */
	private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=10, nullable=true)
     */
    private $code;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="sku", type="string", nullable=true)
	 */
	private $sku;

	/**
     * @var int
     *
     * @ORM\Column(name="sorting", type="smallint",  length=2, nullable=true)
     */
    private $sorting = 0;


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
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
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
	public function getName(){
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName( string $name ) {
		$this->name = $name;
	}


	/**
	 * @return ParticularType
	 */
	public function getParticularType() {
		return $this->particularType;
	}

	/**
	 * @param ParticularType $particularType
	 */
	public function setParticularType( $particularType ) {
		$this->particularType = $particularType;
	}

	/**
	 * @return string
	 */
	public function getSku(){
		return $this->sku;
	}

	/**
	 * @param string $sku
	 */
	public function setSku( string $sku ) {
		$this->sku = $sku;
	}

	
	/**
	 * @return mixed
	 */
	public function getDefineSlug() {
		return $this->defineSlug;
	}

	/**
	 * @param mixed $defineSlug
	 */
	public function setDefineSlug( $defineSlug ) {
		$this->defineSlug = $defineSlug;
	}


}

