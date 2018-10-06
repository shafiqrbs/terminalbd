<?php

namespace Appstore\Bundle\ElectionBundle\Entity;

use Core\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * ElectionVoteCenter
 *
 * @ORM\Table( name ="election_vote_center")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\ElectionBundle\Repository\ElectionVoteCenterRepository")
 */
class ElectionVoteCenter
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
	 * @ORM\ManyToOne(targetEntity="Appstore\Bundle\ElectionBundle\Entity\electionLocation", inversedBy="votecenters")
	 **/
	protected $location;

	/**
	 * @ORM\ManyToOne(targetEntity="Appstore\Bundle\ElectionBundle\Entity\ElectionMember", inversedBy="votecenters")
	 **/
	protected $representative;

	/**
	 * @ORM\ManyToOne(targetEntity="Appstore\Bundle\ElectionBundle\Entity\ElectionSetup", inversedBy="votecenters")
	 **/
	protected $electionSetup;

	/**
	 * @ORM\OneToMany(targetEntity="Appstore\Bundle\ElectionBundle\Entity\ElectionVoteCenterMember", mappedBy="voteCenter")
	 **/
	protected $centerMembers;

	/**
	 * @ORM\OneToMany(targetEntity="Appstore\Bundle\ElectionBundle\Entity\ElectionVoteMatrix", mappedBy="voteCenter")
	 **/
	protected $voterMatrix;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="voteCenterName", type="string", length=200, nullable = true)
	 */
	private $voteCenterName;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="presiding", type="string", length=200, nullable = true)
	 */
	private $presiding;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="presidingDesignation", type="string", length=200, nullable = true)
	 */
	private $presidingDesignation;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="presidingMobile", type="string", length=200, nullable = true)
	 */
	private $presidingMobile;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="representativeMobile", type="string", length=200, nullable = true)
	 */
	private $representativeMobile;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="presidingAddress", type="string", length=200, nullable = true)
	 */
	private $presidingAddress;

	/**
	 * @var \DateTime
	 * @ORM\Column(name="electionDate", type="datetime", nullable=true)
	 */
	private $electionDate;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="ward", type="string", length=200, nullable = true)
	 */
	private $ward;


	/**
	 * @var string
	 *
	 * @ORM\Column(name="memberUnion", type="string", length=200, nullable = true)
	 */
	private $memberUnion;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="thana", type="string", length=200, nullable = true)
	 */
	private $thana;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="district", type="string", length=200, nullable = true)
	 */
	private $district;


	/**
	 * @Gedmo\Blameable(on="create")
	 * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="voteCenterCreatedBy" )
	 **/
	private  $createdBy;

	/**
	 * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="voteCenterApprovedBy" )
	 **/
	private  $approvedBy;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="address", type="text",  nullable=true)
	 */
	private $address;

	/**
     * @var int
     *
     * @ORM\Column(name="totalVoter", type="smallint",  length = 6, nullable=true)
     */
    private $totalVoter;

    /**
     * @var int
     *
     * @ORM\Column(name="maleVoter", type="smallint",  length = 6, nullable=true)
     */
    private $maleVoter;

    /**
     * @var int
     *
     * @ORM\Column(name="femaleVoter", type="smallint",  length = 6, nullable=true)
     */
    private $femaleVoter;

	/**
     * @var int
     *
     * @ORM\Column(name="otherVoter", type="smallint",  length = 6, nullable=true)
     */
    private $otherVoter;


    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean" )
     */
    private $status= true;


	/**
	 * @var \DateTime
	 * @Gedmo\Timestampable(on="create")
	 * @ORM\Column(name="created", type="datetime")
	 */
	private $created;

	/**
	 * @var \DateTime
	 * @Gedmo\Timestampable(on="update")
	 * @ORM\Column(name="updated", type="datetime")
	 */
	private $updated;

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
	 * @return int
	 */
	public function getTotalVoter() {
		return $this->totalVoter;
	}

	/**
	 * @param int $totalVoter
	 */
	public function setTotalVoter( $totalVoter ) {
		$this->totalVoter = $totalVoter;
	}

	/**
	 * @return int
	 */
	public function getMaleVoter(){
		return $this->maleVoter;
	}

	/**
	 * @param int $maleVoter
	 */
	public function setMaleVoter( int $maleVoter ) {
		$this->maleVoter = $maleVoter;
	}

	/**
	 * @return int
	 */
	public function getFemaleVoter(){
		return $this->femaleVoter;
	}

	/**
	 * @param int $femaleVoter
	 */
	public function setFemaleVoter( int $femaleVoter ) {
		$this->femaleVoter = $femaleVoter;
	}

	/**
	 * @return User
	 */
	public function getCreatedBy() {
		return $this->createdBy;
	}

	/**
	 * @param User $createdBy
	 */
	public function setCreatedBy( $createdBy ) {
		$this->createdBy = $createdBy;
	}

	/**
	 * @return User
	 */
	public function getApprovedBy() {
		return $this->approvedBy;
	}

	/**
	 * @param User $approvedBy
	 */
	public function setApprovedBy( $approvedBy ) {
		$this->approvedBy = $approvedBy;
	}

	/**
	 * @return mixed
	 */
	public function getMemberUnion() {
		return $this->memberUnion;
	}

	/**
	 * @param mixed $memberUnion
	 */
	public function setMemberUnion( $memberUnion ) {
		$this->memberUnion = $memberUnion;
	}

	/**
	 * @return string
	 */
	public function getThana(): string {
		return $this->thana;
	}

	/**
	 * @param string $thana
	 */
	public function setThana( string $thana ) {
		$this->thana = $thana;
	}

	/**
	 * @return string
	 */
	public function getDistrict(): string {
		return $this->district;
	}

	/**
	 * @param string $district
	 */
	public function setDistrict( string $district ) {
		$this->district = $district;
	}

	/**
	 * @return mixed
	 */
	public function getWard() {
		return $this->ward;
	}

	/**
	 * @param mixed $ward
	 */
	public function setWard( $ward ) {
		$this->ward = $ward;
	}

	/**
	 * @return mixed
	 */
	public function getVoteCenterName() {
		return $this->voteCenterName;
	}

	/**
	 * @param mixed $voteCenterName
	 */
	public function setVoteCenterName( $voteCenterName ) {
		$this->voteCenterName = $voteCenterName;
	}

	/**
	 * @return string
	 */
	public function getAddress(){
		return $this->address;
	}

	/**
	 * @param string $address
	 */
	public function setAddress( string $address ) {
		$this->address = $address;
	}

	/**
	 * @return ElectionLocation
	 */
	public function getLocation() {
		return $this->location;
	}

	/**
	 * @param ElectionLocation $location
	 */
	public function setLocation( $location ) {
		$this->location = $location;
	}

	/**
	 * @return ElectionParticular
	 */
	public function getElectionType() {
		return $this->electionType;
	}

	/**
	 * @param ElectionParticular $electionType
	 */
	public function setElectionType( $electionType ) {
		$this->electionType = $electionType;
	}

	/**
	 * @return \DateTime
	 */
	public function getElectionDate(){
		return $this->electionDate;
	}

	/**
	 * @param \DateTime $electionDate
	 */
	public function setElectionDate( \DateTime $electionDate ) {
		$this->electionDate = $electionDate;
	}

	/**
	 * @return ElectionMember
	 */
	public function getRepresentative() {
		return $this->representative;
	}

	/**
	 * @param ElectionMember $representative
	 */
	public function setRepresentative( $representative ) {
		$this->representative = $representative;
	}

	/**
	 * @return string
	 */
	public function getPresidingAddress(){
		return $this->presidingAddress;
	}

	/**
	 * @param string $presidingAddress
	 */
	public function setPresidingAddress( string $presidingAddress ) {
		$this->presidingAddress = $presidingAddress;
	}

	/**
	 * @return string
	 */
	public function getPresidingMobile() {
		return $this->presidingMobile;
	}

	/**
	 * @param string $presidingMobile
	 */
	public function setPresidingMobile( string $presidingMobile ) {
		$this->presidingMobile = $presidingMobile;
	}

	/**
	 * @return string
	 */
	public function getPresidingDesignation(){
		return $this->presidingDesignation;
	}

	/**
	 * @param string $presidingDesignation
	 */
	public function setPresidingDesignation( string $presidingDesignation ) {
		$this->presidingDesignation = $presidingDesignation;
	}

	/**
	 * @return string
	 */
	public function getPresiding(){
		return $this->presiding;
	}

	/**
	 * @param string $presiding
	 */
	public function setPresiding( string $presiding ) {
		$this->presiding = $presiding;
	}

	/**
	 * @return \DateTime
	 */
	public function getCreated(): \DateTime {
		return $this->created;
	}

	/**
	 * @param \DateTime $created
	 */
	public function setCreated( \DateTime $created ) {
		$this->created = $created;
	}

	/**
	 * @return \DateTime
	 */
	public function getUpdated(): \DateTime {
		return $this->updated;
	}

	/**
	 * @param \DateTime $updated
	 */
	public function setUpdated( \DateTime $updated ) {
		$this->updated = $updated;
	}

	/**
	 * @return string
	 */
	public function getRepresentativeMobile(){
		return $this->representativeMobile;
	}

	/**
	 * @param string $representativeMobile
	 */
	public function setRepresentativeMobile( string $representativeMobile ) {
		$this->representativeMobile = $representativeMobile;
	}

	/**
	 * @return ElectionVoteCenterMember
	 */
	public function getCenterMembers() {
		return $this->centerMembers;
	}

	/**
	 * @return int
	 */
	public function getOtherVoter(){
		return $this->otherVoter;
	}

	/**
	 * @param int $otherVoter
	 */
	public function setOtherVoter( int $otherVoter ) {
		$this->otherVoter = $otherVoter;
	}

	/**
	 * @return ElectionSetup
	 */
	public function getElectionSetup() {
		return $this->electionSetup;
	}

	/**
	 * @param ElectionSetup $electionSetup
	 */
	public function setElectionSetup( $electionSetup ) {
		$this->electionSetup = $electionSetup;
	}


}

