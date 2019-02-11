<?php

namespace Appstore\Bundle\EducationBundle\Entity;

use Appstore\Bundle\EducationBundle\Form\ParticularType;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * ElectionParticular
 *
 * @ORM\Table( name ="education_fee")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\EducationBundle\Repository\EducationParticularRepository")
 */
class EducationFees
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\EducationBundle\Entity\EducationConfig", inversedBy="fees" , cascade={"detach","merge"} )
     **/
    private  $educationConfig;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EducationBundle\Entity\EducationFeesItem", mappedBy="fees" , cascade={"detach","merge"} )
     **/
    private  $feesItems;

    /**
     * @ORM\OneToOne(targetEntity="Appstore\Bundle\EducationBundle\Entity\EducationParticularPattern", inversedBy="fees" , cascade={"detach","merge"} )
     **/
    private  $pattern;


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
	 * @return EducationConfig
	 */
	public function getEducationConfig() {
		return $this->educationConfig;
	}

	/**
	 * @param EducationConfig $educationConfig
	 */
	public function setEducationConfig( $educationConfig ) {
		$this->educationConfig = $educationConfig;
	}

    /**
     * @return EducationParticularPattern
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * @param EducationParticularPattern $pattern
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;
    }


}

