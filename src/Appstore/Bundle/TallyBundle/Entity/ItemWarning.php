<?php

namespace Appstore\Bundle\TallyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * ItemMetaAttribute
 *
 * @ORM\Table(name="tally_item_warning")
 * @ORM\Entity()
 */
class ItemWarning
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
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\TallyBundle\Entity\Item", mappedBy="itemWarning" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $item;


    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable = true)
     */
    private $name;

	/**
     * @var integer
     *
     * @ORM\Column(name="days", type="integer", length=10, nullable = true)
     */
    private $days;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="status", type="boolean")
	 */
	private $status = true;



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
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName( $name ) {
		$this->name = $name;
	}

	/**
	 * @return bool
	 */
	public function isStatus() {
		return $this->status;
	}

	/**
	 * @param bool $status
	 */
	public function setStatus( $status ) {
		$this->status = $status;
	}

	/**
	 * @return int
	 */
	public function getDays() {
		return $this->days;
	}

	/**
	 * @param int $days
	 */
	public function setDays( $days ) {
		$this->days = $days;
	}


    /**
     * @return Item
     */
    public function getItem()
    {
        return $this->item;
    }



}

