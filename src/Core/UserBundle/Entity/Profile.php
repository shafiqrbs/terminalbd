<?php

/*
 * This file is part of the Docudex project.
 *
 * (c) Devnet Limited <http://www.devnetlimited.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core\UserBundle\Entity;

use Appstore\Bundle\DomainUserBundle\Entity\Branches;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**

 * @ORM\Table(name="user_profiles")
 * @ORM\Entity(repositoryClass="Core\UserBundle\Repository\ProfileRepository")
 * @UniqueEntity(fields="mobile",message="User mobile no already existing,Please try again.")
 * @ORM\HasLifecycleCallbacks
 */
class Profile
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

    /**
     * @ORM\OneToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="profile")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id", unique=true, onDelete="CASCADE")
     * })
     */

    protected $user;


    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\LocationBundle\Entity\Location", inversedBy="profiles")
     **/

    protected $location;


     /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\LocationBundle\Entity\Location", inversedBy="districtProfiles")
     **/

    protected $district;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\LocationBundle\Entity\Location", inversedBy="thanaProfiles")
     **/

    protected $thana;

    /**
     * @ORM\OneToOne(targetEntity="Appstore\Bundle\DomainUserBundle\Entity\DomainUser", inversedBy="profile" )
     */
    protected $domainUser;

     /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\DomainUserBundle\Entity\Branches", inversedBy="profiles" )
     */
    protected $branches;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", nullable=true)
     */
    private $name;


    /**
     * @var string
     *
     * @ORM\Column(name="mobile", type="string", length=15, unique=true )
     */
    private $mobile;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", nullable=true)
     */
    private $email;


    /**
     * @var string
     *
     * @ORM\Column(name="designation", type="string", length=100, nullable=true)
     */
    private $designation;

     /**
     * @var string
     *
     * @ORM\Column(name="profession", type="string", length=100, nullable=true)
     */
    private $profession;

     /**
     * @var text
     *
     * @ORM\Column(name="about", type="text", nullable=true)
     */
    private $about;

    /**
     * @var text
     *
     * @ORM\Column(name="address", type="text", nullable=true)
     */
    private $address;


    /**
     * @var text
     *
     * @ORM\Column(name="permanentAddress", type="text", nullable=true)
     */
    private $permanentAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="postalCode", type="string", nullable=true)
     */
    private $postalCode;

    /**
     * @var string
     *
     * @ORM\Column(name="additionalPhone", type="string", nullable=true)
     */
    private $additionalPhone;


    /**
     * @var string
     *
     * @ORM\Column(name="nid", type="string", nullable=true,unique=true )
     */
    private $nid;


    /**
     * @var datetime
     *
     * @ORM\Column(name="dob", type="datetime", nullable=true)
     */
    private $dob;

    /**
     * @var string
     *
     * @ORM\Column(name="bloodGroup", type="string", nullable=true)
     */
    private $bloodGroup;


     /**
     * @var string
     *
     * @ORM\Column(name="interest", type="string", nullable=true)
     */
    private $interest;

    /**
     * @var string
     *
     * @ORM\Column(name="joiningDate", type="datetime", nullable=true)
     */
    private $joiningDate;

    /**
     * @var string
     *
     * @ORM\Column(name="leaveDate", type="datetime", nullable=true)
     */
    private $leaveDate;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\Bank", inversedBy="profile" )
     **/
    private  $bank;

    /**
     * @var string
     *
     * @ORM\Column(name="accountNo", type="string", length=255, nullable = true)
     */
    private $accountNo;

    /**
     * @var string
     *
     * @ORM\Column(name="branch", type="string", length=255, nullable = true)
     */
    private $branch;

    /**
     * @var boolean
     *
     * @ORM\Column(name="termsConditionAccept", type="boolean", nullable=true)
     */
    private $termsConditionAccept = true;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $path;

    /**
     * @Assert\File(maxSize="5M")
     */
    public $file;

    public $temp;

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->getFile()) {
            // do whatever you want to generate a unique name
            $filename = sha1(uniqid(mt_rand(), true));
            $this->path = $filename . '.' . $this->getFile()->guessExtension();
        }
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
        // check if we have an old image path
        if (isset($this->path)) {
            // store the old name to delete after the update
            $this->temp = $this->path;
            $this->path = null;
        } else {
            $this->path = 'initial';
        }
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }



    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }

        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $this->getFile()->move($this->getUploadRootDir(), $this->path);

        // check if we have an old image
        if (isset($this->temp)) {
            // delete the old image
            unlink($this->getUploadRootDir() . '/' . $this->temp);
            // clear the temp image path
            $this->temp = null;
        }
        $this->file = null;
    }

    public function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
        if(!file_exists( $this->getUploadDir())){
            mkdir( $this->getUploadDir(), 0777, true);
        }
    }

    public function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/domain/'.$this->getUser()->getGlobalOption()->getId().'/user/';

    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
    }

    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir() . '/' . $this->path;
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir() . '/' . $this->path;
    }


    /**
     * Set designation
     *
     * @param string $designation
     * @return Profile
     */
    public function setDesignation($designation)
    {
        $this->designation = $designation;

        return $this;
    }

    /**
     * Get designation
     *
     * @return string
     */
    public function getDesignation()
    {
        return $this->designation;
    }

    /**
     * Set user
     *
     * @param User $user
     * @return Profile
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * @param string $mobile
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return text
     */
    public function getAbout()
    {
        return $this->about;
    }

    /**
     * @param text $about
     */
    public function setAbout($about)
    {
        $this->about = $about;
    }

    /**
     * @return string
     */
    public function getProfession()
    {
        return $this->profession;
    }

    /**
     * @param string $profession
     */
    public function setProfession($profession)
    {
        $this->profession = $profession;
    }

    /**
     * @return text
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param text $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return datetime
     */
    public function getDob()
    {
        return $this->dob;
    }

    /**
     * @param datetime $dob
     */
    public function setDob($dob)
    {
        $this->dob = $dob;
    }

    /**
     * @return string
     */
    public function getBloodGroup()
    {
        return $this->bloodGroup;
    }

    /**
     * @param string $bloodGroup
     */
    public function setBloodGroup($bloodGroup)
    {
        $this->bloodGroup = $bloodGroup;
    }

    /**
     * @return string
     */
    public function getInterest()
    {
        return $this->interest;
    }

    /**
     * @param string $interest
     */
    public function setInterest($interest)
    {
        $this->interest = $interest;
    }



    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getNid()
    {
        return $this->nid;
    }

    /**
     * @param string $nid
     */
    public function setNid($nid)
    {
        $this->nid = $nid;
    }


    /**
     * @return text
     */
    public function getPermanentAddress()
    {
        return $this->permanentAddress;
    }

    /**
     * @param text $permanentAddress
     */
    public function setPermanentAddress($permanentAddress)
    {
        $this->permanentAddress = $permanentAddress;
    }

    /**
     * @return mixed
     */
    public function getDomainUser()
    {
        return $this->domainUser;
    }

    /**
     * @param mixed $domainUser
     */
    public function setDomainUser($domainUser)
    {
        $this->domainUser = $domainUser;
    }

    /**
     * @return mixed
     */
    public function getDistrict()
    {
        return $this->district;
    }

    /**
     * @param mixed $district
     */
    public function setDistrict($district)
    {
        $this->district = $district;
    }

    /**
     * @return mixed
     */
    public function getThana()
    {
        return $this->thana;
    }

    /**
     * @param mixed $thana
     */
    public function setThana($thana)
    {
        $this->thana = $thana;
    }

    /**
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * @param string $postalCode
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;
    }

    /**
     * @return string
     */
    public function getJoiningDate()
    {
        return $this->joiningDate;
    }

    
    /**
     * @param string $joiningDate
     */
    public function setJoiningDate($joiningDate)
    {
        $this->joiningDate = $joiningDate;
    }

    /**
     * @return string
     */
    public function getLeaveDate()
    {
        return $this->leaveDate;
    }

    /**
     * @param string $leaveDate
     */
    public function setLeaveDate($leaveDate)
    {
        $this->leaveDate = $leaveDate;
    }

    /**
     * @return string
     */
    public function getAdditionalPhone()
    {
        return $this->additionalPhone;
    }

    /**
     * @param string $additionalPhone
     */
    public function setAdditionalPhone($additionalPhone)
    {
        $this->additionalPhone = $additionalPhone;
    }

    /**
     * @return mixed
     */
    public function getBank()
    {
        return $this->bank;
    }

    /**
     * @param mixed $bank
     */
    public function setBank($bank)
    {
        $this->bank = $bank;
    }

    /**
     * @return string
     */
    public function getAccountNo()
    {
        return $this->accountNo;
    }

    /**
     * @param string $accountNo
     */
    public function setAccountNo($accountNo)
    {
        $this->accountNo = $accountNo;
    }

    /**
     * @return string
     */
    public function getBranch()
    {
        return $this->branch;
    }

    /**
     * @param string $branch
     */
    public function setBranch($branch)
    {
        $this->branch = $branch;
    }

    /**
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param mixed $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return boolean
     */
    public function isTermsConditionAccept()
    {
        return $this->termsConditionAccept;
    }

    /**
     * @param boolean $termsConditionAccept
     */
    public function setTermsConditionAccept($termsConditionAccept)
    {
        $this->termsConditionAccept = $termsConditionAccept;
    }

    /**
     * @return Branches
     */
    public function getBranches()
    {
        return $this->branches;
    }

    /**
     * @param Branches $branches
     */
    public function setBranches($branches)
    {
        $this->branches = $branches;
    }


}