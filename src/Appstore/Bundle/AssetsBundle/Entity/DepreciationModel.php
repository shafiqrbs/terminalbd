<?php

namespace Appstore\Bundle\AssetsBundle\Entity;

use Appstore\Bundle\AccountingBundle\Entity\AccountHead;
use Appstore\Bundle\InventoryBundle\Entity\Item;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Product\Bundle\ProductBundle\Entity\Category;

/**
 * Depreciation
 *
 * @ORM\Table("assets_depreciation_model")
 * @ORM\Entity()
 */
class DepreciationModel
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
	 * @ORM\ManyToOne(targetEntity="Product\Bundle\ProductBundle\Entity\Category", inversedBy="depreciationModel" )
	 **/
	private  $category;

	/**
	 * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Item", inversedBy="depreciationModel" )
	 **/
	private  $item;

	/**
	 * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountHead", inversedBy="depreciationModelDebit" )
	 **/
	private  $accountHeadDebit;

	/**
	 * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountHead", inversedBy="depreciationModelDebit" )
	 **/
	private  $accountHeadCredit;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="name", type="string", length=255)
	 */
	private $name;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="assetsType", type="string", length=255)
	 */
	private $assetsType;


	/**
	 * @var float
	 *
	 * @ORM\Column(name="rate", type="float", nullable=true)
	 */
	private $rate;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="depreciationYear", type="float", nullable=true)
	 */
	private $depreciationYear;

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
	 * @return Category
	 */
	public function getCategory() {
		return $this->category;
	}

	/**
	 * @param Category $category
	 */
	public function setCategory( $category ) {
		$this->category = $category;
	}

	/**
	 * @return Item
	 */
	public function getItem() {
		return $this->item;
	}

	/**
	 * @param Item $item
	 */
	public function setItem( $item ) {
		$this->item = $item;
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
	 * @return float
	 */
	public function getRate() {
		return $this->rate;
	}

	/**
	 * @param float $rate
	 */
	public function setRate( $rate ) {
		$this->rate = $rate;
	}

	/**
	 * @return AccountHead
	 */
	public function getAccountHeadDebit() {
		return $this->accountHeadDebit;
	}

	/**
	 * @param AccountHead $accountHeadDebit
	 */
	public function setAccountHeadDebit( $accountHeadDebit ) {
		$this->accountHeadDebit = $accountHeadDebit;
	}

	/**
	 * @return AccountHead
	 */
	public function getAccountHeadCredit() {
		return $this->accountHeadCredit;
	}

	/**
	 * @param AccountHead $accountHeadCredit
	 */
	public function setAccountHeadCredit( $accountHeadCredit ) {
		$this->accountHeadCredit = $accountHeadCredit;
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
	 * @return string
	 */
	public function getAssetsType() {
		return $this->assetsType;
	}

	/**
	 * @param string $assetsType
	 */
	public function setAssetsType( $assetsType ) {
		$this->assetsType = $assetsType;
	}

	/**
	 * @return float
	 */
	public function getDepreciationYear() {
		return $this->depreciationYear;
	}

	/**
	 * @param float $depreciationYear
	 */
	public function setDepreciationYear( $depreciationYear ) {
		$this->depreciationYear = $depreciationYear;
	}


}

