<?php

namespace Setting\Bundle\LocationBundle\Entity;

use Appstore\Bundle\EcommerceBundle\Entity\Order;
use Core\UserBundle\Entity\Profile;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Category
 *
 * @Gedmo\Tree(type="materializedPath")
 * @ORM\Table(name="locations")
 * @ORM\Entity(repositoryClass="Setting\Bundle\LocationBundle\Repository\LocationRepository")
 */
class Location
{
    /**
     * @var integer
     *
     * @Gedmo\TreePathSource
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="Syndicate\Bundle\ComponentBundle\Entity\Education", mappedBy="location")
     */
    protected $educations;


    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", mappedBy="location")
     */
    protected $globalOptions;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\Order", mappedBy="location")
     */
    protected $orders;

    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\ContentBundle\Entity\ContactPage", mappedBy="location")
     */
    protected $contactPages;

     /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\ContentBundle\Entity\ContactPage", mappedBy="thana")
     */
    protected $contactPageThanas;

    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\ContentBundle\Entity\ContactPage", mappedBy="district")
     */
    protected $contactPageDistricts;

    /**
     * @ORM\OneToMany(targetEntity="Syndicate\Bundle\ComponentBundle\Entity\Scholarship", mappedBy="location")
     */
    protected $scholarships;

    /**
     * @ORM\OneToMany(targetEntity="Syndicate\Bundle\ComponentBundle\Entity\StudyAbroad", mappedBy="location")
     */
    protected $studyAbroads;

    /**
     * @ORM\OneToMany(targetEntity="Syndicate\Bundle\ComponentBundle\Entity\Tutor", mappedBy="location")
     */
    protected $tutors;

    /**
     * @ORM\OneToMany(targetEntity="Syndicate\Bundle\ComponentBundle\Entity\Vendor", mappedBy="location")
     */
    protected $vendors;

    /**
     * @ORM\OneToMany(targetEntity="Core\UserBundle\Entity\Profile", mappedBy="location")
     */
    protected $profiles;

    /**
     * @ORM\OneToMany(targetEntity="Core\UserBundle\Entity\Profile", mappedBy="district")
     */
    protected $districtProfiles;

    /**
     * @ORM\OneToMany(targetEntity="Core\UserBundle\Entity\Profile", mappedBy="thana")
     */
    protected $thanaProfiles;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\DomainUserBundle\Entity\Customer", mappedBy="district")
     */
    protected $customerDistrict;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\DomainUserBundle\Entity\Customer", mappedBy="thana")
     */
    protected $customerThana;


    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;


    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Location", inversedBy="children")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parent", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     * })
     */
    private $parent;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="level", type="integer", nullable=true)
     */
    private $level;

    /**
     * @ORM\OneToMany(targetEntity="Location" , mappedBy="parent")
     * @ORM\OrderBy({"name" = "ASC"})
     **/
    private $children;

    /**
     * @Gedmo\TreePath(separator="/")
     * @ORM\Column(name="path", type="string", length=3000, nullable=true)
     */
    private $path;


    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * Set name
     *
     * @param string $name
     * @return Location
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
     * @return Location
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param mixed $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return mixed
     */
    public function getChildren()
    {
        return $this->children;
    }

    public function getLevel()
    {
        return $this->level;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getNestedLabel()
    {
        if ($this->getLevel() > 1) {
            return $this->formatLabel($this->getLevel() - 1, $this->getName());
        } else {
            return $this->getName();
        }
    }

    public function getParentIdByLevel($level = 1)
    {
        $parentsIds = explode("/", $this->getPath());

        return isset($parentsIds[$level - 1]) ? $parentsIds[$level - 1] : null;

    }

    private function formatLabel($level, $value)
    {
        return str_repeat("-", $level * 3) . str_repeat(">", $level) . $value;
    }

    /**
     * @return mixed
     */
    public function getEducations()
    {
        return $this->educations;
    }

    /**
     * @return mixed
     */
    public function getScholarships()
    {
        return $this->scholarships;
    }

    /**
     * @return mixed
     */
    public function getStudyAbroad()
    {
        return $this->studyAbroad;
    }

    /**
     * @return mixed
     */
    public function getTutors()
    {
        return $this->tutors;
    }

    /**
     * @return GlobalOption
     */
    public function getGlobalOptions()
    {
        return $this->globalOptions;
    }

    /**
     * @return Profile
     */
    public function getDistrictProfiles()
    {
        return $this->districtProfiles;
    }

    /**
     * @return Profile
     */
    public function getThanaProfiles()
    {
        return $this->thanaProfiles;
    }

    /**
     * @return mixed
     */
    public function getCustomerDistrict()
    {
        return $this->customerDistrict;
    }

    /**
     * @return mixed
     */
    public function getCustomerThana()
    {
        return $this->customerThana;
    }

    /**
     * @return Order
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * @return Profile
     */
    public function getProfiles()
    {
        return $this->profiles;
    }

}
