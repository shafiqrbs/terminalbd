<?php

namespace Appstore\Bundle\AccountingBundle\Entity;

use Appstore\Bundle\EcommerceBundle\Entity\Order;
use Appstore\Bundle\EcommerceBundle\Entity\PreOrder;
use Appstore\Bundle\InventoryBundle\Entity\Sales;
use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\ToolBundle\Entity\Bank;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * CashReconciliationMeta
 *
 *
 * @ORM\Table(name="account_cash_reconciliation_meta")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\AccountingBundle\Repository\CashReconciliationRepository")
 */
class CashReconciliationMeta
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\CashReconciliation", inversedBy="cashReconciliationMetas")
     **/
    protected $cashReconciliation;


    /**
     * @var string
     *
     * @ORM\Column(name="metaKey", type="string", nullable=true)
     */
    private $metaKey;

    /**
     * @var string
     *
     * @ORM\Column(name="transactionMethod", type="string", length = 30, nullable=true)
     */
    private $transactionMethod;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float", nullable=true)
     */
    private $amount = 0;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isCustom", type="boolean")
     */
    private $isCustom = false;


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
     * @return CashReconciliation
     */
    public function getCashReconciliation()
    {
        return $this->cashReconciliation;
    }

    /**
     * @param CashReconciliation $cashReconciliation
     */
    public function setCashReconciliation($cashReconciliation)
    {
        $this->cashReconciliation = $cashReconciliation;
    }

    /**
     * @return bool
     */
    public function isCustom()
    {
        return $this->isCustom;
    }

    /**
     * @param bool $isCustom
     */
    public function setIsCustom(bool $isCustom)
    {
        $this->isCustom = $isCustom;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount(float $amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    public function getMetaKey()
    {
        return $this->metaKey;
    }

    /**
     * @param string $metaKey
     */
    public function setMetaKey(string $metaKey)
    {
        $this->metaKey = $metaKey;
    }

    /**
     * @return string
     */
    public function getTransactionMethod()
    {
        return $this->transactionMethod;
    }

    /**
     * @param string $transactionMethod
     */
    public function setTransactionMethod(string $transactionMethod)
    {
        $this->transactionMethod = $transactionMethod;
    }

}

