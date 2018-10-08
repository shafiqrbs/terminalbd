<?php

namespace Appstore\Bundle\ElectionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\LocationBundle\Entity\Location;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * CustomerConfig
 *
 * @ORM\Table("election_config")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\ElectionBundle\Repository\ElectionConfigRepository")
 */
class ElectionConfig
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
	 * @ORM\OneToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="electionConfig")
	 * @ORM\JoinColumn(onDelete="CASCADE")
	 **/
	protected $globalOption;


	/**
	 * @ORM\OneToMany(targetEntity="Appstore\Bundle\ElectionBundle\Entity\ElectionMemberImport", mappedBy="electionConfig")
	 **/
	private $imports;

	/**
	 * @ORM\OneToMany(targetEntity="Appstore\Bundle\ElectionBundle\Entity\ElectionLocation", mappedBy="electionConfig")
	 **/
	private $locations;


    /**
	 * @ORM\OneToMany(targetEntity="Appstore\Bundle\ElectionBundle\Entity\ElectionParticular", mappedBy="electionConfig")
	 **/
	private $electionParticulars;


	/**
	 * @ORM\ManyToOne(targetEntity="Appstore\Bundle\ElectionBundle\Entity\ElectionParticular", inversedBy="parliaments")
	 **/
	private $parliament;


	/**
	 * @ORM\OneToMany(targetEntity="Appstore\Bundle\ElectionBundle\Entity\ElectionSms", mappedBy="electionConfig")
	 **/
	private $sms;

	/**
	 * @ORM\OneToMany(targetEntity="Appstore\Bundle\ElectionBundle\Entity\ElectionCampaignAnalysis", mappedBy="electionConfig")
	 **/
	private $campaignAnalysis;

	/**
	 * @ORM\OneToMany(targetEntity="Appstore\Bundle\ElectionBundle\Entity\ElectionCandidate", mappedBy="electionConfig")
	 **/
	private $candidates;

	/**
	 * @ORM\OneToMany(targetEntity="Appstore\Bundle\ElectionBundle\Entity\ElectionSetup", mappedBy="electionConfig")
	 **/
	private $electionSetups;

	/**
	 * @ORM\OneToMany(targetEntity="Appstore\Bundle\ElectionBundle\Entity\ElectionCommittee", mappedBy="electionConfig")
	 **/
	private $committees;

	/**
	 * @ORM\OneToMany(targetEntity="Appstore\Bundle\ElectionBundle\Entity\ElectionEvent", mappedBy="electionConfig")
	 **/
	private $events;

	/**
	 * @ORM\OneToMany(targetEntity="Appstore\Bundle\ElectionBundle\Entity\ElectionMember", mappedBy="electionConfig")
	 **/
	private $members;

	/**
	 * @ORM\OneToMany(targetEntity="Appstore\Bundle\ElectionBundle\Entity\ElectionVoteCenter", mappedBy="electionConfig")
	 **/
	private $voteCenters;


	/**
	 * @ORM\ManyToOne(targetEntity="Setting\Bundle\LocationBundle\Entity\Location", inversedBy="election")
	 **/
	private $district;


    /**
     * @var boolean
     *
     * @ORM\Column(name="smsNotification", type="boolean")
     */
    private $smsNotification = false;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="address", type="text", nullable = true)
	 */
	private $address;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="removeImage", type="boolean")
	 */
	private $removeImage = false;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	protected $path;

	/**
	 * @Assert\File(maxSize="8388608")
	 */
	protected $file;




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
     * @return boolean
     */
    public function getSmsNotification()
    {
        return $this->smsNotification;
    }

    /**
     * @param boolean $smsNotification
     */
    public function setSmsNotification($smsNotification)
    {
        $this->smsNotification = $smsNotification;
    }

	/**
	 * @return ElectionMember
	 */
	public function getElectionMembers() {
		return $this->electionMembers;
	}

	/**
	 * @param ElectionMember $electionMembers
	 */
	public function setElectionMembers( $electionMembers ) {
		$this->electionMembers = $electionMembers;
	}

	/**
	 * @return ElectionParticular
	 */
	public function getElectionParticulars() {
		return $this->electionParticulars;
	}



	/**
	 * @return GlobalOption
	 */
	public function getGlobalOption() {
		return $this->globalOption;
	}

	/**
	 * @param GlobalOption $globalOption
	 */
	public function setGlobalOption( $globalOption ) {
		$this->globalOption = $globalOption;
	}


	/**
	 * Sets file.
	 *
	 * @param Page $file
	 */
	public function setFile(UploadedFile $file = null)
	{
		$this->file = $file;
	}

	/**
	 * Get file.
	 *
	 * @return Page
	 */
	public function getFile()
	{
		return $this->file;
	}

	public function getAbsolutePath()
	{
		return null === $this->path
			? null
			: $this->getUploadRootDir().'/'.$this->path;
	}

	public function getWebPath()
	{
		return null === $this->path
			? null
			: $this->getUploadDir().'/' . $this->path;
	}



	protected function getUploadRootDir()
	{
		return __DIR__.'/../../../../../web/'.$this->getUploadDir();
	}

	protected function getUploadDir()
	{
		return 'uploads/domain/'.$this->getGlobalOption()->getId().'/business';
	}

	public function removeUpload()
	{
		if ($file = $this->getAbsolutePath()) {
			unlink($file);
			$this->path = null ;
		}
	}

	public function upload()
	{
		// the file property can be empty if the field is not required
		if (null === $this->getFile()) {
			return;
		}

		// use the original file name here but you should
		// sanitize it at least to avoid any security issues

		// move takes the target directory and then the
		// target filename to move to
		$filename = date('YmdHmi') . "_" . $this->getFile()->getClientOriginalName();
		$this->getFile()->move(
			$this->getUploadRootDir(),
			$filename
		);

		// set the path property to the filename where you've saved the file
		$this->path = $filename ;

		// clean up the file property as you won't need it anymore
		$this->file = null;
	}

	/**
	 * @return boolean
	 */
	public function getRemoveImage()
	{
		return $this->removeImage;
	}

	/**
	 * @param boolean $removeImage
	 */
	public function setRemoveImage($removeImage)
	{
		$this->removeImage = $removeImage;
	}



	/**
	 * Set address
	 *
	 * @param mixed $address
	 * @return ElectionConfig
	 */
	public function setAddress($address)
	{
		$this->address = $address;

		return $this;
	}

	/**
	 * Get address
	 *
	 * @return mixed
	 */
	public function getAddress()
	{
		return $this->address;
	}

	/**
	 * @return ElectionMemberImport
	 */
	public function getImports() {
		return $this->imports;
	}



	/**
	 * @return ElectionParticular
	 */
	public function getParliament() {
		return $this->parliament;
	}

	/**
	 * @param ElectionParticular $parliament
	 */
	public function setParliament( $parliament ) {
		$this->parliament = $parliament;
	}

	/**
	 * @return Location
	 */
	public function getDistrict() {
		return $this->district;
	}

	/**
	 * @param Location $district
	 */
	public function setDistrict( $district ) {
		$this->district = $district;
	}

	/**
	 * @return ElectionMember
	 */
	public function getMembers() {
		return $this->members;
	}

	/**
	 * @return ElectionSms
	 */
	public function getSms() {
		return $this->sms;
	}

	/**
	 * @return ElectionCampaignAnalysis
	 */
	public function getCampaignAnalysis() {
		return $this->campaignAnalysis;
	}

	/**
	 * @return ElectionCandidate
	 */
	public function getCandidates() {
		return $this->candidates;
	}

	/**
	 * @return ElectionSetup
	 */
	public function getElectionSetups() {
		return $this->electionSetups;
	}

	/**
	 * @return ElectionCommittee
	 */
	public function getCommittees() {
		return $this->committees;
	}

	/**
	 * @return ElectionEvent
	 */
	public function getEvents() {
		return $this->events;
	}

	/**
	 * @return ElectionVoteCenter
	 */
	public function getVoteCenters() {
		return $this->voteCenters;
	}

	/**
	 * @return ElectionLocation
	 */
	public function getLocations() {
		return $this->locations;
	}


}

