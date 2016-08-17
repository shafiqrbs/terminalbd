<?php

namespace Syndicate\Bundle\ComponentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Tutor
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Syndicate\Bundle\ComponentBundle\Repository\TutorRepository")
 */
class Tutor
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
     * @ORM\OneToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="tutor")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id", unique=true, onDelete="CASCADE")
     * })
     **/

    protected $user;


    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\LocationBundle\Entity\Location", inversedBy="tutors")
     **/

    protected $location;

    /**
     * @ORM\ManyToMany(targetEntity="Setting\Bundle\ToolBundle\Entity\Syndicate", inversedBy="tutors")
     **/

    protected $syndicates;


    /**
     * @ORM\OneToMany(targetEntity="Syndicate\Bundle\ComponentBundle\Entity\Academic", mappedBy="tutor" )
     **/

    protected $academics;

    /**
     * @ORM\OneToMany(targetEntity="Syndicate\Bundle\ComponentBundle\Entity\AcademicMeta", mappedBy="tutor" )
     **/

    protected $academicMeta;


    /**
     * @var string
     *
     * @ORM\Column(name="name", type="text", nullable=true)
     */
    private $name;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateOfBirth", type="date", nullable=true)
     */
    private $dateOfBirth;

    /**
     * @var string
     *
     * @ORM\Column(name="bloodGroup", type="text", nullable=true)
     */
    private $bloodGroup;

    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="text", nullable=true)
     */
    private $gender;

    /**
     * @var string
     *
     * @ORM\Column(name="nationality", type="text", nullable=true)
     */
    private $nationality;


    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255 , nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="mobile", type="string", length=255 , nullable=true)
     */
    private $mobile;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255,  nullable=true)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="job", type="string", length=255 , nullable = true)
     */
    private $job;


    /**
     * @var string
     *
     * @ORM\Column(name="skypeId", type="string", length=255 , nullable = true)
     */
    private $skypeId;

    /**
     * @var string
     *
     * @ORM\Column(name="linkedin", type="string", length=255, nullable=true)
     */
    private $linkedin;

    /**
     * @var string
     *
     * @ORM\Column(name="website", type="string", length=255,  nullable=true)
     */
    private $website;


    /**
     * @var string
     *
     * @ORM\Column(name="facebookId", type="string", length=255, nullable=true)
     */
    private $facebookId;


    /**
     * @var string
     *
     * @ORM\Column(name="twitterId", type="string", length=255, nullable=true)
     */
    private $twitterId;


    /**
     * @var string
     *
     * @ORM\Column(name="blogUrl", type="string", length=255, nullable=true)
     */
    private $blogUrl;



    /**
     * @var string
     *
     * @ORM\Column(name="permanentAddress", type="string", length=255, nullable=true)
     */
    private $permanentAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="presentAddress", type="string", length=255, nullable=true)
     */
    private $presentAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="postalCode", type="string", length=255,  nullable=true)
     */
    private $postalCode;


    /**
     * @var string
     *
     * @ORM\Column(name="currentPosition", type="string", length=255, nullable=true)
     */
    private $currentPosition;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;


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
     * Set name
     *
     * @param string $name
     *
     * @return Tutor
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Tutor
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set mobile
     *
     * @param string $mobile
     *
     * @return Tutor
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;

        return $this;
    }

    /**
     * Get mobile
     *
     * @return string
     */
    public function getMobile()
    {
        return $this->mobile;
    }


    /**
     * Sets file.
     *
     * @param Scholarship $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return Scholarship
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
            : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return 'uploads/files/'.$this->getUser();
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
        $this->getFile()->move(
            $this->getUploadRootDir(),
            $this->getFile()->getClientOriginalName()
        );

        // set the path property to the filename where you've saved the file
        $this->path = $this->getFile()->getClientOriginalName();

        // clean up the file property as you won't need it anymore
        $this->file = null;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
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
     * @return mixed
     */
    public function getSyndicates()
    {
        return $this->syndicates;
    }

    /**
     * @param mixed $syndicates
     */
    public function setSyndicates($syndicates)
    {
        $this->syndicates = $syndicates;
    }

    /**
     * @return mixed
     */
    public function getAcademics()
    {
        return $this->academics;
    }

    /**
     * @return string
     */
    public function getPermanentAddress()
    {
        return $this->permanentAddress;
    }

    /**
     * @param string $permanentAddress
     */
    public function setPermanentAddress($permanentAddress)
    {
        $this->permanentAddress = $permanentAddress;
    }

    /**
     * @return string
     */
    public function getPresentAddress()
    {
        return $this->presentAddress;
    }

    /**
     * @param string $presentAddress
     */
    public function setPresentAddress($presentAddress)
    {
        $this->presentAddress = $presentAddress;
    }

    /**
     * @return string
     */
    public function getCurrentPosition()
    {
        return $this->currentPosition;
    }

    /**
     * @param string $currentPosition
     */
    public function setCurrentPosition($currentPosition)
    {
        $this->currentPosition = $currentPosition;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * @param string $job
     */
    public function setJob($job)
    {
        $this->job = $job;
    }

    /**
     * @return string
     */
    public function getSkypeId()
    {
        return $this->skypeId;
    }

    /**
     * @param string $skypeId
     */
    public function setSkypeId($skypeId)
    {
        $this->skypeId = $skypeId;
    }

    /**
     * @return string
     */
    public function getLinkedin()
    {
        return $this->linkedin;
    }

    /**
     * @param string $linkedin
     */
    public function setLinkedin($linkedin)
    {
        $this->linkedin = $linkedin;
    }

    /**
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @param string $website
     */
    public function setWebsite($website)
    {
        $this->website = $website;
    }

    /**
     * @return string
     */
    public function getFacebookId()
    {
        return $this->facebookId;
    }

    /**
     * @param string $facebookId
     */
    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;
    }

    /**
     * @return string
     */
    public function getTwitterId()
    {
        return $this->twitterId;
    }

    /**
     * @param string $twitterId
     */
    public function setTwitterId($twitterId)
    {
        $this->twitterId = $twitterId;
    }

    /**
     * @return string
     */
    public function getBlogUrl()
    {
        return $this->blogUrl;
    }

    /**
     * @param string $blogUrl
     */
    public function setBlogUrl($blogUrl)
    {
        $this->blogUrl = $blogUrl;
    }

    /**
     * @return \DateTime
     */
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    /**
     * @param \DateTime $dateOfBirth
     */
    public function setDateOfBirth($dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;
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
     * @return mixed
     */
    public function getAcademicMeta()
    {
        return $this->academicMeta;
    }

    /**
     * @param mixed $academicMeta
     */
    public function setAcademicMeta($academicMeta)
    {
        $this->academicMeta = $academicMeta;
    }

    /**
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param string $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * @return string
     */
    public function getNationality()
    {
        return $this->nationality;
    }

    /**
     * @param string $nationality
     */
    public function setNationality($nationality)
    {
        $this->nationality = $nationality;
    }

}

