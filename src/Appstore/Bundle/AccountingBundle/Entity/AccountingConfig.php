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
     * @ORM\Column(name="accountClose", type="boolean",  nullable=true)
     */
    private $accountClose = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="purchase", type="boolean",  nullable=true)
     */
    private $purchase = false;


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
     * @return bool
     */
    public function isPurchase()
    {
        return $this->purchase;
    }

    /**
     * @param bool $purchase
     */
    public function setPurchase(bool $purchase)
    {
        $this->purchase = $purchase;
    }

    /**
     * @return bool
     */
    public function isAccountClose()
    {
        return $this->accountClose;
    }

    /**
     * @param bool $accountClose
     */
    public function setAccountClose(bool $accountClose)
    {
        $this->accountClose = $accountClose;
    }


}

