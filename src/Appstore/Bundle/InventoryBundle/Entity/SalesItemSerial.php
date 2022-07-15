<?php

namespace Appstore\Bundle\InventoryBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * SalesItemSerial
 *
 * @ORM\Table("inv_sales_item_serial")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\InventoryBundle\Repository\SalesItemSerialRepository")
 */
class SalesItemSerial
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\SalesItem", inversedBy="salesItemSerials" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $salesItem;

    /**
     * @ORM\OneToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\PurchaseItemSerial")
     **/
    private  $purchaseItemSerial;

    /**
     * @var string
     *
     * @ORM\Column(name="serialNo", type="text", length=255, nullable = true)
     */
    private $serialNo;


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
    public function getSalesItem()
    {
        return $this->salesItem;
    }

    /**
     * @param mixed $salesItem
     */
    public function setSalesItem($salesItem)
    {
        $this->salesItem = $salesItem;
    }

    /**
     * @return mixed
     */
    public function getPurchaseItemSerial()
    {
        return $this->purchaseItemSerial;
    }

    /**
     * @param mixed $purchaseItemSerial
     */
    public function setPurchaseItemSerial($purchaseItemSerial)
    {
        $this->purchaseItemSerial = $purchaseItemSerial;
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




}

