<?php

namespace Appstore\Bundle\ElectionBundle\Entity;

use Appstore\Bundle\ElectionBundle\Form\ParticularType;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * ElectionParticular
 *
 * @ORM\Table( name ="election_particular")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\ElectionBundle\Repository\ElectionParticularRepository")
 */
class ElectionParticular
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\ElectionBundle\Entity\ElectionConfig", inversedBy="electionParticulars" , cascade={"detach","merge"} )
     **/
    private  $electionConfig;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\ElectionBundle\Entity\ElectionConfig", mappedBy="parliament" , cascade={"detach","merge"} )
     **/
    private  $parliaments;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\ElectionBundle\Entity\ElectionParticularType", inversedBy="particulars" , cascade={"detach","merge"} )
     **/
    private  $particularType;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\ElectionBundle\Entity\ElectionMember", mappedBy="electionParticular")
     * @ORM\OrderBy({"sorting" = "ASC"})
     **/
    private $electionMember;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\ElectionBundle\Entity\ElectionMember", mappedBy="politicalStatus")
     * @ORM\OrderBy({"sorting" = "ASC"})
     **/
    private $memberPoliticalStatus;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\ElectionBundle\Entity\ElectionMember", mappedBy="politicalDesignation")
     * @ORM\OrderBy({"sorting" = "ASC"})
     **/
    private $memberPoliticalDesignation;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\ElectionBundle\Entity\ElectionMember", mappedBy="profession")
     * @ORM\OrderBy({"sorting" = "ASC"})
     **/
    private $memberProfession;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\ElectionBundle\Entity\ElectionMember", mappedBy="education")
     * @ORM\OrderBy({"sorting" = "ASC"})
     **/
    private $memberEducation;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\ElectionBundle\Entity\ElectionMember", mappedBy="oldPoliticalParty")
     * @ORM\OrderBy({"sorting" = "ASC"})
     **/
    private $memberPoliticalParty;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\ElectionBundle\Entity\ElectionLocation", mappedBy="locationType")
     * @ORM\OrderBy({"sorting" = "ASC"})
     **/
    private $locations;

	/**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\ElectionBundle\Entity\ElectionCommittee", mappedBy="committeeType")
     * @ORM\OrderBy({"sorting" = "ASC"})
     **/
    private $electionCommittees;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\ElectionBundle\Entity\ElectionCandidateSetup", mappedBy="politicalParty")
     * @ORM\OrderBy({"sorting" = "ASC"})
     **/
    private $electionCandidateParty;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\ElectionBundle\Entity\ElectionCandidateSetup", mappedBy="marka")
     * @ORM\OrderBy({"sorting" = "ASC"})
     **/
    private $electionCandidateMarka;

	/**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\ElectionBundle\Entity\ElectionMemberFamily", mappedBy="relation")
     * @ORM\OrderBy({"sorting" = "ASC"})
     **/
    private $memberRelation;


    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=true)
     */
    private $name;

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
	 * @return ElectionMember
	 */
	public function getElectionMember() {
		return $this->electionMember;
	}

	/**
	 * @return ElectionConfig
	 */
	public function getElectionConfig() {
		return $this->electionConfig;
	}

	/**
	 * @param ElectionConfig $electionConfig
	 */
	public function setElectionConfig( $electionConfig ) {
		$this->electionConfig = $electionConfig;
	}

	/**
	 * @return ElectionCommittee
	 */
	public function getElectionCommittees() {
		return $this->electionCommittees;
	}

	/**
	 * @return ElectionCandidateSetup
	 */
	public function getElectionCandidateParty() {
		return $this->electionCandidateParty;
	}

	/**
	 * @return ElectionCandidateSetup
	 */
	public function getElectionCandidateMarka() {
		return $this->electionCandidateMarka;
	}

	/**
	 * @return ElectionMemberFamily
	 */
	public function getMemberRelation() {
		return $this->memberRelation;
	}

	/**
	 * @return mixed
	 */
	public function getParticularType() {
		return $this->particularType;
	}

	/**
	 * @param ParticularType $particularType
	 * ParliamentSeat
	 * Committee
	 * PoliticalStatus
	 * Designation
	 * Profession
	 * PoliticalParty
	 * Marka
	 * Relation
	 * Religion
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
	 * @return ElectionLocation
	 */
	public function getLocations() {
		return $this->locations;
	}

	/**
	 * @return ElectionConfig
	 */
	public function getParliaments() {
		return $this->parliaments;
	}

	/**
	 * @return ElectionMember
	 */
	public function getMemberPoliticalStatus() {
		return $this->memberPoliticalStatus;
	}

	/**
	 * @return ElectionMember
	 */
	public function getMemberPoliticalDesignation() {
		return $this->memberPoliticalDesignation;
	}

	/**
	 * @return ElectionMember
	 */
	public function getMemberPoliticalParty() {
		return $this->memberPoliticalParty;
	}

	/**
	 * @return ElectionMember
	 */
	public function getMemberEducation() {
		return $this->memberEducation;
	}

	/**
	 * @param ElectionMember $memberEducation
	 */
	public function setMemberEducation( $memberEducation ) {
		$this->memberEducation = $memberEducation;
	}

	/**
	 * @return ElectionMember
	 */
	public function getMemberProfession() {
		return $this->memberProfession;
	}

	/**
	 * @param ElectionMember $memberProfession
	 */
	public function setMemberProfession( $memberProfession ) {
		$this->memberProfession = $memberProfession;
	}


}

