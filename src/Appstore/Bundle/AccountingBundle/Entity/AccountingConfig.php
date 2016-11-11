<?php

namespace Appstore\Bundle\AccountingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * AccountingConfig
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Appstore\Bundle\AccountingBundle\Repository\AccountingConfigRepository")
 */
class AccountingConfig
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
     * @ORM\OneToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="accountingConfig" , cascade={"persist", "remove"})
     **/

    private $globalOption;

    /**
     * @var boolean
     *
     * @ORM\Column(name="autoPurchase", type="boolean",  nullable=true)
     */
    private $autoPurchase = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="autoPurchaseReturn", type="boolean",  nullable=true)
     */
    private $autoPurchaseReturn = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="autoSales", type="boolean",  nullable=true)
     */
    private $autoSales = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="autoSalesReturn", type="boolean",  nullable=true)
     */
    private $autoSalesReturn = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="autoDamage", type="boolean",  nullable=true)
     */
    private $autoDamage = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="autoOnlineOrder", type="boolean",  nullable=true)
     */
    private $autoOnlineOrder = true;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        return $this->id = $id;
    }

    /**
     * @return GlobalOption
     */
    public function getGlobalOption()
    {
        return $this->globalOption;
    }

    /**
     * @param GlobalOption $globalOption
     */
    public function setGlobalOption($globalOption)
    {
        $this->globalOption = $globalOption;
    }

    /**
     * @return boolean
     */
    public function getAutoPurchase()
    {
        return $this->autoPurchase;
    }

    /**
     * @param boolean $autoPurchase
     */
    public function setAutoPurchase($autoPurchase)
    {
        $this->autoPurchase = $autoPurchase;
    }

    /**
     * @return boolean
     */
    public function getAutoPurchaseReturn()
    {
        return $this->autoPurchaseReturn;
    }

    /**
     * @param boolean $autoPurchaseReturn
     */
    public function setAutoPurchaseReturn($autoPurchaseReturn)
    {
        $this->autoPurchaseReturn = $autoPurchaseReturn;
    }

    /**
     * @return boolean
     */
    public function getAutoSales()
    {
        return $this->autoSales;
    }

    /**
     * @param boolean $autoSales
     */
    public function setAutoSales($autoSales)
    {
        $this->autoSales = $autoSales;
    }

    /**
     * @return boolean
     */
    public function getAutoSalesReturn()
    {
        return $this->autoSalesReturn;
    }

    /**
     * @param boolean $autoSalesReturn
     */
    public function setAutoSalesReturn($autoSalesReturn)
    {
        $this->autoSalesReturn = $autoSalesReturn;
    }

    /**
     * @return boolean
     */
    public function getAutoDamage()
    {
        return $this->autoDamage;
    }

    /**
     * @param boolean $autoDamage
     */
    public function setAutoDamage($autoDamage)
    {
        $this->autoDamage = $autoDamage;
    }

    /**
     * @return boolean
     */
    public function getAutoOnlineOrder()
    {
        return $this->autoOnlineOrder;
    }

    /**
     * @param boolean $autoOnlineOrder
     */
    public function setAutoOnlineOrder($autoOnlineOrder)
    {
        $this->autoOnlineOrder = $autoOnlineOrder;
    }


}

