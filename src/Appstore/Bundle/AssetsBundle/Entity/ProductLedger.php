<?php

namespace Appstore\Bundle\AssetsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Product\Bundle\ProductBundle\Entity\Category;

/**
 * Depreciation
 *
 * @ORM\Table("assets_ledger")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\AssetsBundle\Repository\ProductLedgerRepository")
 */
class ProductLedger
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
	 * @ORM\ManyToOne(targetEntity="Appstore\Bundle\DomainUserBundle\Entity\Branches", inversedBy="ledgers" )
	 **/
	private  $branch;

	/**
	 * @ORM\ManyToOne(targetEntity="Product\Bundle\ProductBundle\Entity\Category", inversedBy="ledgers" )
	 **/
	private  $category;

	/**
	 * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Item", inversedBy="ledgers" )
	 **/
	private  $item;

	/**
	 * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AssetsBundle\Entity\Product", inversedBy="ledgers" )
	 **/
	private  $product;


	/**
	 * @var float
	 *
	 * @ORM\Column(name="debit", type="float", nullable=true)
	 */
	private $debit;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="credit", type="float", nullable=true)
	 */
	private $credit;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="balance", type="float", nullable=true)
	 */
	private $balance;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="narration", type="text", nullable=true)
	 */
	private $narration;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="process", type="string", nullable=true)
	 */
	private $process;


	/**
	 * @var \DateTime
	 * @Gedmo\Timestampable(on="create")
	 * @ORM\Column(name="created", type="datetime")
	 */
	private $created;

	/**
	 * @var \DateTime
	 * @Gedmo\Timestampable(on="update")
	 * @ORM\Column(name="updated", type="datetime")
	 */
	private $updated;


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
	public function getBranch() {
		return $this->branch;
	}

	/**
	 * @param mixed $branch
	 */
	public function setBranch( $branch ) {
		$this->branch = $branch;
	}

	/**
	 * @return mixed
	 */
	public function getCategory() {
		return $this->category;
	}

	/**
	 * @param mixed $category
	 */
	public function setCategory( $category ) {
		$this->category = $category;
	}

	/**
	 * @return mixed
	 */
	public function getItem() {
		return $this->item;
	}

	/**
	 * @param mixed $item
	 */
	public function setItem( $item ) {
		$this->item = $item;
	}

	/**
	 * @return mixed
	 */
	public function getProduct() {
		return $this->product;
	}

	/**
	 * @param mixed $product
	 */
	public function setProduct( $product ) {
		$this->product = $product;
	}

	/**
	 * @return string
	 */
	public function getNarration() {
		return $this->narration;
	}

	/**
	 * @param string $narration
	 */
	public function setNarration( $narration ) {
		$this->narration = $narration;
	}

	/**
	 * @return string
	 */
	public function getProcess() {
		return $this->process;
	}

	/**
	 * @param string $process
	 */
	public function setProcess( $process ) {
		$this->process = $process;
	}

	/**
	 * @return mixed
	 */
	public function getDebit() {
		return $this->debit;
	}

	/**
	 * @param mixed $debit
	 */
	public function setDebit( $debit ) {
		$this->debit = $debit;
	}

	/**
	 * @return mixed
	 */
	public function getCredit() {
		return $this->credit;
	}

	/**
	 * @param mixed $credit
	 */
	public function setCredit( $credit ) {
		$this->credit = $credit;
	}

	/**
	 * @return float
	 */
	public function getBalance() {
		return $this->balance;
	}

	/**
	 * @param float $balance
	 */
	public function setBalance( $balance ) {
		$this->balance = $balance;
	}

	/**
	 * @return \DateTime
	 */
	public function getCreated() {
		return $this->created;
	}

	/**
	 * @param \DateTime $created
	 */
	public function setCreated( $created ) {
		$this->created = $created;
	}

	/**
	 * @return \DateTime
	 */
	public function getUpdated() {
		return $this->updated;
	}

	/**
	 * @param \DateTime $updated
	 */
	public function setUpdated( $updated ) {
		$this->updated = $updated;
	}


}

