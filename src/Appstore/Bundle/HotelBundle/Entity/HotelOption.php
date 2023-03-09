<?php

namespace Appstore\Bundle\HotelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * HotelOption
 *
 * @ORM\Table( name = "hotel_option")
 * @ORM\Entity(repositoryClass="")
 */
class HotelOption
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
	 * @ORM\ManyToOne(targetEntity="Appstore\Bundle\HotelBundle\Entity\HotelConfig", inversedBy="hotelOptions" )
	 **/
	private  $hotelConfig;

	/**
	 * @ORM\ManyToOne(targetEntity="Appstore\Bundle\HotelBundle\Entity\HotelParticularType", inversedBy="hotelOptions" )
	 **/
	private $hotelParticularType;

	/**
	 * @ORM\OneToMany(targetEntity="Appstore\Bundle\HotelBundle\Entity\HotelParticular", mappedBy="roomCategory" )
	 **/
	private $particularCategories;

	/**
	 * @ORM\OneToMany(targetEntity="Appstore\Bundle\HotelBundle\Entity\HotelParticular", mappedBy="roomType" )
	 **/
	private $particularRoomTypes;

	/**
	 * @ORM\OneToMany(targetEntity="Appstore\Bundle\HotelBundle\Entity\HotelParticular", mappedBy="viewPosition" )
     * @ORM\JoinColumn(onDelete="CASCADE")
	 **/
	private $particularViewPositions;

	/**
	 * @ORM\ManyToMany(targetEntity="Appstore\Bundle\HotelBundle\Entity\HotelParticular", mappedBy="complimentary" )
     * @ORM\JoinColumn(onDelete="CASCADE")
	 **/
	private $particularComplimentary;

	/**
	 * @ORM\ManyToMany(targetEntity="Appstore\Bundle\HotelBundle\Entity\HotelParticular", mappedBy="amenities" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
	private $particularAmenities;

	/**
	 * @ORM\OneToMany(targetEntity="Appstore\Bundle\HotelBundle\Entity\HotelParticular", mappedBy="roomFloor",cascade={"remove","merge"} )
     * @ORM\JoinColumn(onDelete="CASCADE")
	 **/
	private $particularRoomFloors;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="name", type="string", length=255, nullable=true)
	 */
	private $name;

	/**
	 * @Gedmo\Translatable
	 * @Gedmo\Slug(fields={"name"})
	 * @ORM\Column(length=255, unique=true)
	 */
	private $slug;

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
	 * Set name
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
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
	 * @return HotelConfig
	 */
	public function getHotelConfig() {
		return $this->hotelConfig;
	}

	/**
	 * @param HotelConfig $hotelConfig
	 */
	public function setHotelConfig( $hotelConfig ) {
		$this->hotelConfig = $hotelConfig;
	}

	/**
	 * @return HotelParticularType
	 */
	public function getHotelParticularType() {
		return $this->hotelParticularType;
	}

	/**
	 * @param HotelParticularType $hotelParticularType
	 */
	public function setHotelParticularType( $hotelParticularType ) {
		$this->hotelParticularType = $hotelParticularType;
	}

	/**
	 * @return HotelParticular
	 */
	public function getParticularRoomTypes() {
		return $this->particularRoomTypes;
	}

	/**
	 * @return HotelParticular
	 */
	public function getParticularCategories() {
		return $this->particularCategories;
	}

	/**
	 * @return HotelParticular
	 */
	public function getParticularViewPositions() {
		return $this->particularViewPositions;
	}

	/**
	 * @return HotelParticular
	 */
	public function getParticularComplimentary() {
		return $this->particularComplimentary;
	}

	/**
	 * @return HotelParticular
	 */
	public function getParticularAmenities() {
		return $this->particularAmenities;
	}

    /**
     * @return mixed
     */
    public function getParticularRoomFloors()
    {
        return $this->particularRoomFloors;
    }


}

