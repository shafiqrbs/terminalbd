<?php

namespace Appstore\Bundle\ElectionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * ElectionVoteCenterMember
 *
 * @ORM\Table( name ="election_vote_center_member")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\ElectionBundle\Repository\ElectionVoteCenterMemberRepository")
 */
class ElectionVoteCenterMember
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\ElectionBundle\Entity\ElectionMember", inversedBy="electionVoteCenterMembers" , cascade={"detach","merge"} )
     **/
    private  $electionMember;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isMaster", type="boolean" )
     */
    private $isMaster = true;

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
	 * @return bool
	 */
	public function isMaster(){
		return $this->isMaster;
	}

	/**
	 * @param bool $isMaster
	 */
	public function setIsMaster( bool $isMaster ) {
		$this->isMaster = $isMaster;
	}

	/**
	 * @return ElectionMember
	 */
	public function getElectionMember() {
		return $this->electionMember;
	}

	/**
	 * @param ElectionMember $electionMember
	 */
	public function setElectionMember( $electionMember ) {
		$this->electionMember = $electionMember;
	}


}

