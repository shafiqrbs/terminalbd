<?php

namespace Appstore\Bundle\ElectionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * ElectionMemberFamily
 *
 * @ORM\Table( name ="election_member_family")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\ElectionBundle\Repository\ElectionMemberFamilyRepository")
 */
class ElectionMemberFamily
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\ElectionBundle\Entity\ElectionMember", inversedBy="familyMembers" , cascade={"detach","merge"} )
     **/
    private  $electionMember;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\ElectionBundle\Entity\ElectionParticular", inversedBy="memberRelation" , cascade={"detach","merge"} )
     **/
    private  $relation;


	/**
	 * @var string
	 *
	 * @ORM\Column(name="name", type="string", length=100, nullable=true)
	 */
	private $name;


	/**
	 * @var string
	 *
	 * @ORM\Column(name="nid", type="string", length=50, nullable=true)
	 */
	private $nid;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="mobile", type="string", length=50, nullable=true)
	 */
	private $mobile;

	/**
	 * @var int
	 *
	 * @ORM\Column(name="age", type="smallint", length=3, nullable=true)
	 */
	private $age;



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

	/**
	 * @return ElectionParticular
	 */
	public function getRelation() {
		return $this->relation;
	}

	/**
	 * @param ElectionParticular $relation
	 */
	public function setRelation( $relation ) {
		$this->relation = $relation;
	}

	/**
	 * @return string
	 */
	public function getNid(){
		return $this->nid;
	}

	/**
	 * @param string $nid
	 */
	public function setNid( string $nid ) {
		$this->nid = $nid;
	}

	/**
	 * @return string
	 */
	public function getMobile() {
		return $this->mobile;
	}

	/**
	 * @param string $mobile
	 */
	public function setMobile( string $mobile ) {
		$this->mobile = $mobile;
	}

	/**
	 * @return int
	 */
	public function getAge(){
		return $this->age;
	}

	/**
	 * @param int $age
	 */
	public function setAge( int $age ) {
		$this->age = $age;
	}


}

