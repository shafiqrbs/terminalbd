<?php

namespace Appstore\Bundle\ElectionBundle\Entity;

use Core\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * ElectionSetup
 *
 * @ORM\Table( name ="election_setup")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\ElectionBundle\Repository\ElectionSetupRepository")
 */
class ElectionSetup
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
	 * @ORM\ManyToOne(targetEntity="Appstore\Bundle\ElectionBundle\Entity\electionLocation", inversedBy="electionSetup")
	 **/
	protected $location;

	/**
	 * @ORM\OneToMany(targetEntity="Appstore\Bundle\ElectionBundle\Entity\ElectionVoteMatrix", mappedBy="electionSetup")
	 **/
	protected $voteMatrix;

	/**
	 * @ORM\OneToMany(targetEntity="Appstore\Bundle\ElectionBundle\Entity\ElectionCandidate", mappedBy="electionSetup")
	 * @ORM\OrderBy({"totalVote" = "DESC"})
	 **/
	protected $candidates;

	/**
	 * @ORM\OneToMany(targetEntity="Appstore\Bundle\ElectionBundle\Entity\ElectionVoteCenter", mappedBy="electionSetup")
	 **/
	protected $votecenters;

	/**
	 * @ORM\ManyToOne(targetEntity="Appstore\Bundle\ElectionBundle\Entity\ElectionParticular", inversedBy="electionSetup")
	 **/
	protected $electionType;


	/**
	 * @var \DateTime
	 * @ORM\Column(name="electionDate", type="datetime", nullable=true)
	 */
	private $electionDate;


	/**
	 * @var string
	 *
	 * @ORM\Column(name="district", type="string", length=200, nullable = true)
	 */
	private $district;


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
	 * @return \DateTime
	 */
	public function getUpdated(){
		return $this->updated;
	}

	/**
	 * @param \DateTime $updated
	 */
	public function setUpdated( \DateTime $updated ) {
		$this->updated = $updated;
	}

	/**
	 * @return ElectionVoteMatrix
	 */
	public function getVoteMatrix() {
		return $this->voteMatrix;
	}

	/**
	 * @return ElectionVoteCenter
	 */
	public function getVotecenters() {
		return $this->votecenters;
	}

	/**
	 * @return ElectionCandidate
	 */
	public function getCandidates() {
		return $this->candidates;
	}

	public function getElectionName(){

		$year = $this->getElectionDate()->format('y');
		$type = $this->getElectionType()->getName();
		$data = $type.' - '.$year.' => '.$this->getLocation()->getName();
		return $data;

	}


}

