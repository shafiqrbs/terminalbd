<?php

namespace Syndicate\Bundle\ComponentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * StudyAbroad
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Syndicate\Bundle\ComponentBundle\Repository\StudyAbroadRepository")
 */
class StudyAbroad
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
     * @ORM\OneToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="studyAbroad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id", unique=true, onDelete="CASCADE")
     * })
     **/

    protected $user;


    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\LocationBundle\Entity\Location", inversedBy="studyAbroads")
     **/

    protected $location;

    /**
     * @ORM\ManyToMany(targetEntity="Setting\Bundle\ToolBundle\Entity\Syndicate", inversedBy="studyAbroads")
     **/

    protected $syndicates;



    /**
     * @var \DateTime
     *
     * @ORM\Column(name="establishment", type="date", nullable=true)
     */
    private $establishment;

    /**
     * @var string
     *
     * @ORM\Column(name="registrationNo", type="string", length=255 , nullable = true)
     */
    private $registrationNo;


    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text" , nullable=true)
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255,  nullable=true)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="mobile", type="string", length=255,  nullable=true)
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
     * @ORM\Column(name="fax", type="string", length=255,  nullable=true)
     */
    private $fax;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255,  nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="website", type="string", length=255,  nullable=true)
     */
    private $website;

    /**
     * @var string
     *
     * @ORM\Column(name="postalCode", type="string", length=255,  nullable=true)
     */
    private $postalCode;


    /**
     * @var string
     *
     * @ORM\Column(name="contactPerson", type="string", length=255 , nullable = true)
     */
    private $contactPerson;


    /**
     * @var string

     * @ORM\Column(name="contactPersonDesignation", type="string", length=255 , nullable = true)
     */
    private $contactPersonDesignation;


    /**
     * @var string
     *
     * @ORM\Column(name="skypeId", type="string", length=255 , nullable = true)
     */
    private $skypeId;

    /**
     * @var string
     *
     * @ORM\Column(name="startHour", type="string", length=255 , nullable = true)
     */
    private $startHour;

    /**
     * @var string
     *
     * @ORM\Column(name="endHour", type="string", length=255 , nullable = true)
     */
    private $endHour;

    /**
     * @var string
     *
     * @ORM\Column(name="weeklyOffDay", type="string", length=255 , nullable = true)
     */
    private $weeklyOffDay;



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
     * @return \DateTime
     */
    public function getEstablishment()
    {
        return $this->establishment;
    }

    /**
     * @param \DateTime $establishment
     */
    public function setEstablishment($establishment)
    {
        $this->establishment = $establishment;
    }

    /**
     * @return string
     */
    public function getRegistrationNo()
    {
        return $this->registrationNo;
    }

    /**
     * @param string $registrationNo
     */
    public function setRegistrationNo($registrationNo)
    {
        $this->registrationNo = $registrationNo;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }


    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
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
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * @param string $fax
     */
    public function setFax($fax)
    {
        $this->fax = $fax;
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
    public function getContactPerson()
    {
        return $this->contactPerson;
    }

    /**
     * @param string $contactPerson
     */
    public function setContactPerson($contactPerson)
    {
        $this->contactPerson = $contactPerson;
    }

    /**
     * @return string
     */
    public function getContactPersonDesignation()
    {
        return $this->contactPersonDesignation;
    }

    /**
     * @param string $contactPersonDesignation
     */
    public function setContactPersonDesignation($contactPersonDesignation)
    {
        $this->contactPersonDesignation = $contactPersonDesignation;
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
     * @return string
     */
    public function getStartHour()
    {
        return $this->startHour;
    }

    /**
     * @param string $startHour
     */
    public function setStartHour($startHour)
    {
        $this->startHour = $startHour;
    }

    /**
     * @return string
     */
    public function getEndHour()
    {
        return $this->endHour;
    }

    /**
     * @param string $endHour
     */
    public function setEndHour($endHour)
    {
        $this->endHour = $endHour;
    }

    /**
     * @return string
     */
    public function getWeeklyOffDay()
    {
        return $this->weeklyOffDay;
    }

    /**
     * @param string $weeklyOffDay
     */
    public function setWeeklyOffDay($weeklyOffDay)
    {
        $this->weeklyOffDay = $weeklyOffDay;
    }

}

