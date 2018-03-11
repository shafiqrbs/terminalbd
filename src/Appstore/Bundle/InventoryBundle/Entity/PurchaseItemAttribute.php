<?php

namespace Appstore\Bundle\InventoryBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * PurchaseItem
 *
 * @ORM\Table(name="inv_purchase_item_attribute")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\InventoryBundle\Repository\PurchaseItemAttributeRepository")
 */
class PurchaseItemAttribute
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Item", inversedBy="purchaseItemAttributes" )
     **/
    private  $item;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\PurchaseItem", inversedBy="purchaseItemAttributes")
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $purchaseItem;

    /**
     * @var string
     *
     * @ORM\Column(name="serialNo", type="text", length=255, nullable = true)
     */
    private $serialNo;

    /**
     * @var string
     *
     * @ORM\Column(name="assuranceType", type="string", length=50, nullable = true)
     */
    private $assuranceType;

    /**
     * @var string
     *
     * @ORM\Column(name="assuranceFromVendor", type="string", length=100, nullable = true)
     */
    private $assuranceFromVendor;

    /**
     * @var string
     *
     * @ORM\Column(name="assuranceToCustomer", type="string", length=100, nullable = true)
     */
    private $assuranceToCustomer;


    /**
     * @var datetime
     *
     * @ORM\Column(name="expiredDate", type="datetime", nullable=true)
     */
    private $expiredDate;


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
     * @return Item
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param mixed $item
     */
    public function setItem($item)
    {
        $this->item = $item;
    }


    /**
     * @return mixed
     */
    public function getPurchaseItem()
    {
        return $this->purchaseItem;
    }

    /**
     * @param mixed $purchaseItem
     */
    public function setPurchaseItem($purchaseItem)
    {
        $this->purchaseItem = $purchaseItem;
    }

    /**
     * @return string
     */
    public function getSerialNo()
    {
        return $this->serialNo;
    }

    /**
     * @param string $serialNo
     */
    public function setSerialNo($serialNo)
    {
        $this->serialNo = $serialNo;
    }

    /**
     * @return string
     */
    public function getAssuranceToCustomer()
    {
        return $this->assuranceToCustomer;
    }

    /**
     * @param string $assuranceToCustomer
     */
    public function setAssuranceToCustomer($assuranceToCustomer)
    {
        $this->assuranceToCustomer = $assuranceToCustomer;
    }

    /**
     * @return DateTime
     */
    public function getExpiredDate()
    {
        return $this->expiredDate;
    }

    /**
     * @param DateTime $expiredDate
     */
    public function setExpiredDate($expiredDate)
    {
        $this->expiredDate = $expiredDate;
    }

    /**
     * @return string
     */
    public function getAssuranceType()
    {
        return $this->assuranceType;
    }

    /**
     * @param string $assuranceType
     */
    public function setAssuranceType($assuranceType)
    {
        $this->assuranceType = $assuranceType;
    }

    /**
     * @return DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param DateTime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param DateTime $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /**
     * @return string
     */
    public function getAssuranceFromVendor()
    {
        return $this->assuranceFromVendor;
    }

    /**
     * @param string $assuranceFromVendor
     */
    public function setAssuranceFromVendor($assuranceFromVendor)
    {
        $this->assuranceFromVendor = $assuranceFromVendor;
    }


}

