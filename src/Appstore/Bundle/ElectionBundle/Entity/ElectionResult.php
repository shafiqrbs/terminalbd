<?php

namespace Appstore\Bundle\ElectionBundle\Entity;

use Appstore\Bundle\ElectionBundle\Form\ParticularType;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * ElectionEvent
 *
 * @ORM\Table( name ="election_result")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\ElectionBundle\Repository\ElectionParticularRepository")
 */
class ElectionResult
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\ElectionBundle\Entity\ElectionConfig", inversedBy="electionResult" , cascade={"detach","merge"} )
     **/
    private  $electionConfig;


	 /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\ElectionBundle\Entity\ElectionParticular", inversedBy="electionResult" , cascade={"detach","merge"} )
     **/
    private  $electionType;


	/**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=true)
     */
    private $name;

	/**
     * @var string
     *
     * @ORM\Column(name="contact", type="string", length=100, nullable=true)
     */
    private $contact;

	/**
	 * @Gedmo\Slug(fields={"name"})
	 * @Doctrine\ORM\Mapping\Column(length=100)
	 */
	private $slug;

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
	 * @return mixed
	 */
	public function getContent() {
		return $this->content;
	}

	/**
	 * @param mixed $content
	 */
	public function setContent( $content ) {
		$this->content = $content;
	}

	/**
	 * @return mixed
	 */
	public function getElectionConfig() {
		return $this->electionConfig;
	}

	/**
	 * @param mixed $electionConfig
	 */
	public function setElectionConfig( $electionConfig ) {
		$this->electionConfig = $electionConfig;
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
	 * @return \DateTime
	 */
	public function getStartDate(): \DateTime {
		return $this->startDate;
	}

	/**
	 * @param \DateTime $startDate
	 */
	public function setStartDate( \DateTime $startDate ) {
		$this->startDate = $startDate;
	}

	/**
	 * @return \DateTime
	 */
	public function getEndDate(): \DateTime {
		return $this->endDate;
	}

	/**
	 * @param \DateTime $endDate
	 */
	public function setEndDate( \DateTime $endDate ) {
		$this->endDate = $endDate;
	}

	/**
	 * @return string
	 */
	public function getContact(){
		return $this->contact;
	}

	/**
	 * @param string $contact
	 */
	public function setContact( string $contact ) {
		$this->contact = $contact;
	}

	/**
	 * @return string
	 */
	public function getEmail(){
		return $this->email;
	}

	/**
	 * @param string $email
	 */
	public function setEmail( string $email ) {
		$this->email = $email;
	}

	/**
	 * @return string
	 */
	public function getFacebookPage(): string {
		return $this->facebookPage;
	}

	/**
	 * @param string $facebookPage
	 */
	public function setFacebookPage( string $facebookPage ) {
		$this->facebookPage = $facebookPage;
	}


}

