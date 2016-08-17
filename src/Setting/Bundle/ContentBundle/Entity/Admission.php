<?php

namespace Setting\Bundle\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * Admission
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Setting\Bundle\ContentBundle\Repository\AdmissionRepository")
 */
class Admission
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="contactPerson", type="string", length=255, nullable=true)
     */
    private $contactPerson;

    /**
     * @var string
     *
     * @ORM\Column(name="mobile", type="string", length=255, nullable=true)
     */
    private $mobile;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var datetime
     *
     * @ORM\Column(name="startDate", type="datetime", nullable=true)
     */
    private $startDate;

    /**
     * @var datetime
     *
     * @ORM\Column(name="endDate", type="datetime", nullable=true)
     */
    private $endDate;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255 , nullable=true)
     */
    private $email;

    /**
     * @var text
     *
     * @ORM\Column(name="address", type="text",  nullable=true)
     */
    private $address;


    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status = true;

    /**
     * @Gedmo\Translatable
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=255, unique=true)
     */

    private $slug;


    /**
     * @ORM\Column(type="string", name="phone", nullable=true)
     */
    protected $phone;

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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $path;

    /**
     * @Assert\File(maxSize="8388608")
     */
    protected $file;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="admissions")
     **/

    protected $globalOption;

    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="admissions")
     **/
    protected $user;

    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\ContentBundle\Entity\AdmissionComment", mappedBy="admission")
     */
    protected $comments;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\CourseLevel", inversedBy="admissions")
     */
    protected $courseLevel;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\Course", inversedBy="admissions")
     */
    protected $course;

     /**
     * @ORM\ManyToMany(targetEntity="Setting\Bundle\ContentBundle\Entity\Branch", inversedBy="admissions")
     */
    protected $branches;

    /**
     * @var string
     * @ORM\Column(type="string", name="qualification", nullable=true)
     */
    protected $qualification;

    /**
     * @var string
     * @ORM\Column(type="string", name="coursePeriod", nullable=true)
     */
    protected $coursePeriod;

    /**
     * @var string
     * @ORM\Column(type="string", name="classDuration", nullable=true)
     */
    protected $classDuration;

    /**
     * @var string
     * @ORM\Column(type="string", name="tuitionFee", nullable=true)
     */
    protected $tuitionFee;

    /**
     * @var array
     * @ORM\Column(type="array", name="shifting", nullable=true)
     */
    protected $shifting;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", name="isOnlineRegistration")
     */
    protected $isOnlineRegistration;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", name="isPayment")
     */
    protected $isPayment;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isPromotion", type="boolean")
     */
    private $isPromotion = true;

    /**
     * @var datetime
     *
     * @ORM\Column(name="promotionStartDate", type="datetime", nullable=true)
     */
    private $promotionStartDate;

    /**
     * @var datetime
     *
     * @ORM\Column(name="promotionEndDate", type="datetime", nullable=true)
     */
    private $promotionEndDate;

    /**
     * @var string
     * @ORM\Column(type="string", name="position", nullable=true)
     */
    protected $position;

    /**
     * @var string
     * @ORM\Column(type="string", name="amount", nullable=true)
     */
    protected $amount;

    /**
     * @var string
     * @ORM\Column(name="paymentStatus", type="string", nullable=true)
     */
    protected $paymentStatus;

    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="admissionPromotions")
     **/
    protected $createUser;



    public function __construct() {
        $this->branches = new ArrayCollection();
    }

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
     * @return \Setting\Bundle\ContentBundle\Entity\datetime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param \Setting\Bundle\ContentBundle\Entity\datetime $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return \Setting\Bundle\ContentBundle\Entity\datetime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param \Setting\Bundle\ContentBundle\Entity\datetime $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
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
     * @return \Setting\Bundle\ContentBundle\Entity\text
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param \Setting\Bundle\ContentBundle\Entity\text $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
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
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param boolean $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
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
            : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return 'uploads/files/'.$this->getGlobalOption().'/content';
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
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param mixed $comments
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
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
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
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
     * @return mixed
     */
    public function getCourseLevel()
    {
        return $this->courseLevel;
    }

    /**
     * @param mixed $courseLevel
     */
    public function setCourseLevel($courseLevel)
    {
        $this->courseLevel = $courseLevel;
    }

    /**
     * @return mixed
     */
    public function getCourse()
    {
        return $this->course;
    }

    /**
     * @param mixed $course
     */
    public function setCourse($course)
    {
        $this->course = $course;
    }

    /**
     * @return mixed
     */
    public function getQualification()
    {
        return $this->qualification;
    }

    /**
     * @param mixed $qualification
     */
    public function setQualification($qualification)
    {
        $this->qualification = $qualification;
    }

    /**
     * @return mixed
     */
    public function getCoursePeriod()
    {
        return $this->coursePeriod;
    }

    /**
     * @param mixed $coursePeriod
     */
    public function setCoursePeriod($coursePeriod)
    {
        $this->coursePeriod = $coursePeriod;
    }

    /**
     * @return mixed
     */
    public function getClassDuration()
    {
        return $this->classDuration;
    }

    /**
     * @param mixed $classDuration
     */
    public function setClassDuration($classDuration)
    {
        $this->classDuration = $classDuration;
    }

    /**
     * @return mixed
     */
    public function getTuitionFee()
    {
        return $this->tuitionFee;
    }

    /**
     * @param mixed $tuitionFee
     */
    public function setTuitionFee($tuitionFee)
    {
        $this->tuitionFee = $tuitionFee;
    }

    /**
     * @return mixed
     */
    public function getShifting()
    {
        return $this->shifting;
    }

    public function getShiftingArray()
    {
        return $this->shifting->toArray();
    }
    /**
     * @param  array  $shifting
     */
    public function setShifting($shifting)
    {
        if (!empty($shifting) && $shifting === $this->shifting) {
            reset($shifting);
            $key = key($shifting);
            $items[$key] = clone $shifting[$key];
        }
        $this->shifting = $shifting;
    }

    /**
     * @return boolean
     */
    public function getIsOnlineRegistration()
    {
        return $this->isOnlineRegistration;
    }

    /**
     * @param boolean $isOnlineRegistration
     */
    public function setIsOnlineRegistration($isOnlineRegistration)
    {
        $this->isOnlineRegistration = $isOnlineRegistration;
    }

    /**
     * @return boolean
     */
    public function getIsPayment()
    {
        return $this->isPayment;
    }

    /**
     * @param boolean $isPayment
     */
    public function setIsPayment($isPayment)
    {
        $this->isPayment = $isPayment;
    }

    /**
     * @return mixed
     */
    public function getBranches()
    {
        return $this->branches;
    }

    /**
     * @param mixed $branches
     */
    public function setBranches($branches)
    {
        $this->branches = $branches;
    }

    /**
     * @return mixed
     */
    public function getCreateUser()
    {
        return $this->createUser;
    }

    /**
     * @param mixed $createUser
     */
    public function setCreateUser($createUser)
    {
        $this->createUser = $createUser;
    }

    /**
     * @return string
     */
    public function getPaymentStatus()
    {
        return $this->paymentStatus;
    }

    /**
     * @param string $paymentStatus
     */
    public function setPaymentStatus($paymentStatus)
    {
        $this->paymentStatus = $paymentStatus;
    }

    /**
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param string $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param string $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return datetime
     */
    public function getPromotionEndDate()
    {
        return $this->promotionEndDate;
    }

    /**
     * @param datetime $promotionEndDate
     */
    public function setPromotionEndDate($promotionEndDate)
    {
        $this->promotionEndDate = $promotionEndDate;
    }

    /**
     * @return datetime
     */
    public function getPromotionStartDate()
    {
        return $this->promotionStartDate;
    }

    /**
     * @param datetime $promotionStartDate
     */
    public function setPromotionStartDate($promotionStartDate)
    {
        $this->promotionStartDate = $promotionStartDate;
    }

    /**
     * @return boolean
     */
    public function isIsPromotion()
    {
        return $this->isPromotion;
    }

    /**
     * @param boolean $isPromotion
     */
    public function setIsPromotion($isPromotion)
    {
        $this->isPromotion = $isPromotion;
    }

    /**
     * @return mixed
     */
    public function getGlobalOption()
    {
        return $this->globalOption;
    }

    /**
     * @param mixed $globalOption
     */
    public function setGlobalOption($globalOption)
    {
        $this->globalOption = $globalOption;
    }
}
