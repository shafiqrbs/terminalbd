<?php

namespace Appstore\Bundle\ElectionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\LocationBundle\Entity\Location;


/**
 * ElectionLocation
 *
 * @ORM\Table( name ="election_location")
 * @Gedmo\Tree(type="materializedPath")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\ElectionBundle\Repository\ElectionLocationRepository")
 */
class ElectionLocation
{

	/**
	 * @var integer
	 *
	 * @Gedmo\TreePathSource
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\ElectionBundle\Entity\ElectionConfig", inversedBy="electionLocations" , cascade={"detach","merge"} )
     **/
    private  $electionConfig;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\ElectionBundle\Entity\ElectionMember", mappedBy="location")
     * @ORM\OrderBy({"sorting" = "ASC"})
     **/
    private $electionMembers;

	/**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\ElectionBundle\Entity\ElectionMember", mappedBy="voteCenter")
     * @ORM\OrderBy({"sorting" = "ASC"})
     **/
    private $voters;

	/**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\LocationBundle\Entity\Location", inversedBy="electionLocations")
     * @ORM\OrderBy({"sorting" = "ASC"})
     **/
    private $district;

	/**
	 * @ORM\ManyToOne(targetEntity="Appstore\Bundle\ElectionBundle\Entity\ElectionParticular", inversedBy="locations")
	 * @ORM\OrderBy({"sorting" = "ASC"})
	 **/
	private $locationType;

	/**
	 * @Gedmo\TreeParent
	 * @ORM\ManyToOne(targetEntity="Appstore\Bundle\ElectionBundle\Entity\ElectionLocation", inversedBy="children")
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
	 * @ORM\OneToMany(targetEntity="Appstore\Bundle\ElectionBundle\Entity\ElectionLocation" , mappedBy="parent")
	 * @ORM\OrderBy({"name" = "ASC"})
	 **/
	private $children;

	/**
	 * @Gedmo\TreePath(separator="/")
	 * @ORM\Column(name="path", type="string", length=300, nullable=true)
	 */
	private $path;


    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=true)
     */
    private $name;


    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=50, nullable=true)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=10, nullable=true)
     */
    private $code;

    /**
     * @var int
     *
     * @ORM\Column(name="sorting", type="smallint",  length=2, nullable=true)
     */
    private $sorting = 0;


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
	 * @return ElectionMember
	 */
	public function getElectionMember() {
		return $this->electionMembers;
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
	 * @return ElectionMember
	 */
	public function getElectionMembers() {
		return $this->electionMembers;
	}

	/**
	 * @return ElectionLocation
	 */
	public function getParent() {
		return $this->parent;
	}

	/**
	 * @param ElectionLocation $parent
	 */
	public function setParent( $parent ) {
		$this->parent = $parent;
	}

	/**
	 * @return mixed
	 */
	public function getLevel() {
		return $this->level;
	}

	/**
	 * @param mixed $level
	 */
	public function setLevel( $level ) {
		$this->level = $level;
	}

	/**
	 * @return ElectionLocation
	 */
	public function getChildren() {
		return $this->children;
	}

	/**
	 * @return mixed
	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * @param mixed $path
	 */
	public function setPath( $path ) {
		$this->path = $path;
	}


	public function getNestedLabel()
	{
		if($this->getLevel() > 1) {
			return $this->formatLabel($this->getLevel() - 1, $this->getName());
		}else{
			return $this->getName();
		}
	}

	public function getParentIdByLevel($level = 1)
	{
		$parentsIds = explode("/", $this->getPath());

		return isset($parentsIds[$level - 1]) ? $parentsIds[$level - 1] : null;

	}

	private function formatLabel($level, $value) {
		return str_repeat("-", $level * 3) . str_repeat(">", $level) . $value;
	}


	/**
	 * @return ElectionParticular
	 */
	public function getLocationType() {
		return $this->locationType;
	}

	/**
	 * @param ElectionParticular $locationType
	 */
	public function setLocationType( $locationType ) {
		$this->locationType = $locationType;
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
	public function getVoters() {
		return $this->voters;
	}


}

