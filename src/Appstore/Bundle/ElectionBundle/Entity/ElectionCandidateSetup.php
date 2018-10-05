<?php

namespace Appstore\Bundle\ElectionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * ElectionCandidateSetup
 *
 * @ORM\Table( name ="election_candidate_setup")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\ElectionBundle\Repository\ElectionCandidateSetupRepository")
 */
class ElectionCandidateSetup
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\ElectionBundle\Entity\ElectionParticular", inversedBy="electionCandidateParty" , cascade={"detach","merge"} )
     **/
    private  $politicalParty;


	/**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\ElectionBundle\Entity\ElectionParticular", inversedBy="electionCandidateMarka" , cascade={"detach","merge"} )
     **/
    private  $marka;


	/**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\ElectionBundle\Entity\ElectionVoteCount", mappedBy="candidate" , cascade={"detach","merge"} )
     **/
    private  $voterCounts;


	/**
	 * @var string
	 *
	 * @ORM\Column(name="name", type="string", length=100, nullable=true)
	 */
	private $name;


    /**
     * @var int
     *
     * @ORM\Column(name="totalVote", type="smallint",  length = 6, nullable=true)
     */
    private $totalVote = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="maleVote", type="smallint",  length = 6, nullable=true)
     */
    private $maleVote = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="femaleVote", type="smallint",  length = 6, nullable=true)
     */
    private $femaleVote = 0;

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
	 * @return mixed
	 */
	public function getPoliticalParty() {
		return $this->politicalParty;
	}

	/**
	 * @param mixed $politicalParty
	 */
	public function setPoliticalParty( $politicalParty ) {
		$this->politicalParty = $politicalParty;
	}

	/**
	 * @return mixed
	 */
	public function getMarka() {
		return $this->marka;
	}

	/**
	 * @param mixed $marka
	 */
	public function setMarka( $marka ) {
		$this->marka = $marka;
	}

	/**
	 * @return int
	 */
	public function getTotalVote(): int {
		return $this->totalVote;
	}

	/**
	 * @param int $totalVote
	 */
	public function setTotalVote( int $totalVote ) {
		$this->totalVote = $totalVote;
	}

	/**
	 * @return int
	 */
	public function getMaleVote(): int {
		return $this->maleVote;
	}

	/**
	 * @param int $maleVote
	 */
	public function setMaleVote( int $maleVote ) {
		$this->maleVote = $maleVote;
	}

	/**
	 * @return int
	 */
	public function getFemaleVote(): int {
		return $this->femaleVote;
	}

	/**
	 * @param int $femaleVote
	 */
	public function setFemaleVote( int $femaleVote ) {
		$this->femaleVote = $femaleVote;
	}

	/**
	 * @return ElectionVoteCount
	 */
	public function getVoterCounts() {
		return $this->voterCounts;
	}


}

