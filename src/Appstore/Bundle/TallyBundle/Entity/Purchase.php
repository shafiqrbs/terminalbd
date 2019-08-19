<?php

namespace Appstore\Bundle\TallyBundle\Entity;


use Appstore\Bundle\AccountingBundle\Entity\AccountBank;
use Appstore\Bundle\AccountingBundle\Entity\AccountPurchase;
use Appstore\Bundle\AccountingBundle\Entity\AccountVendor;
use Appstore\Bundle\ProcurementBundle\Entity\PurchaseOrder;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\ToolBundle\Entity\TransactionMethod;

/**
 * Purchase
 *
 * @ORM\Table(name="tally_purchase")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\TallyBundle\Repository\PurchaseRepository")
 */
    class Purchase
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
         * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="tallyPurchase")
         **/
        protected $globalOption;


        /**
         * @ORM\ManyToOne(targetEntity="Appstore\Bundle\TallyBundle\Entity\TallyConfig", inversedBy="purchase" )
         * @ORM\JoinColumn(onDelete="CASCADE")
         **/
        private $config;


        /**
         * @ORM\OneToMany(targetEntity="Appstore\Bundle\TallyBundle\Entity\PurchaseItem", mappedBy="purchase"  )
         **/
        private  $purchaseItems;


         /**
         * @ORM\OneToMany(targetEntity="Appstore\Bundle\ProcurementBundle\Entity\PurchaseOrder", mappedBy="purchase"  )
         **/
        private  $purchaseOrder;


        /**
         * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountVendor", inversedBy="tallyPurchase" , cascade={"detach","merge"} )
         **/
        private  $vendor;


         /**
         * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountPurchase", mappedBy="tallyPurchase" , cascade={"detach","merge"} )
         **/
        private  $accountPurchase;


        /**
         * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountBank", inversedBy="accountPurchases" )
         **/
        private  $accountBank;

        /**
         * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank", inversedBy="accountPurchases" )
         **/
        private  $accountMobileBank;

        /**
         * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\TransactionMethod", inversedBy="accountPurchases" )
         **/
        private  $transactionMethod;


        /**
         * @Gedmo\Blameable(on="create")
         * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="accountPurchases" )
         **/
        private  $createdBy;


        /**
         * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="purchasesToUser" )
         **/
        private  $toUser;

        /**
         * @var float
         *
         * @ORM\Column(name="subTotal", type="float", nullable=true)
         */
        private $subTotal = 0;

        /**
         * @var float
         *
         * @ORM\Column(name="netTotal", type="float", nullable=true)
         */
        private $netTotal = 0;

        /**
         * @var float
         *
         * @ORM\Column(name="customsDuty", type="float", nullable=true)
         */
        private $customsDuty = 0.00;


        /**
         * @var float
         *
         * @ORM\Column(name="supplementaryDuty", type="float", nullable=true)
         */
        private $supplementaryDuty = 0.00;

        /**
         * @var float
         *
         * @ORM\Column(name="valueAddedTax", type="float", nullable=true)
         */
        private $valueAddedTax = 0.00;


        /**
         * @var float
         *
         * @ORM\Column(name="advanceIncomeTax", type="float", nullable=true)
         */
        private $advanceIncomeTax = 0.00;


        /**
         * @var float
         *
         * @ORM\Column(name="recurringDeposit", type="float", nullable=true)
         */
        private $recurringDeposit = 0.00;


        /**
         * @var float
         *
         * @ORM\Column(name="advanceTradeVat", type="float", nullable=true)
         */
        private $advanceTradeVat = 0.00;


        /**
         * @var float
         *
         * @ORM\Column(name="totalTaxIncidence", type="float", nullable=true)
         */
        private $totalTaxIncidence = 0.00;


        /**
         * @var float
         *
         * @ORM\Column(name="payment", type="float", nullable=true)
         */
        private $payment = 0;


        /**
         * @var float
         *
         * @ORM\Column(name="rebate", type="float", nullable=true)
         */
        private $rebate = 0;

         /**
         * @var float
         *
         * @ORM\Column(name="vatDeductionSource", type="float", nullable=true)
         */
        private $vatDeductionSource = 0;

         /**
         * @var float
         *
         * @ORM\Column(name="discount", type="float", nullable=true)
         */
        private $discount = 0;

        /**
         * @var string
         *
         * @ORM\Column(name="grn", type="string", length = 50, nullable=true)
         */
        private $grn;

        /**
         * @var string
         *
         * @ORM\Column(name="challanNo", type="string", length = 50, nullable=true)
         */
        private $challanNo;


        /**
         * @var string
         *
         * @ORM\Column(name="lcNo", type="string", length = 50, nullable=true)
         */
        private $lcNo;


        /**
         * @var integer
         *
         * @ORM\Column(name="code", type="integer",  nullable=true)
         */
        private $code;

        /**
         * @var string
         *
         * @ORM\Column(name="remark", type="text", nullable = true)
         */
        private $remark;

        /**
         * @var \DateTime
         *
         * @ORM\Column(name="poDate", type="date", nullable = true)
         */
        private $poDate;


        /**
         * @var \DateTime
         * @Gedmo\Timestampable(on="create")
         * @ORM\Column(name="receiveDate", type="datetime")
         */
        private $receiveDate;

        /**
         * @var \DateTime
         * @Gedmo\Timestampable(on="create")
         * @ORM\Column(name="created", type="datetime")
         */
        private $created;

        /**
         * @var \DateTime
         * @ORM\Column(name="updated", type="datetime", nullable = true)
         */
        private $updated;


        /**
         * @var string
         *
         * @ORM\Column(name="process", type="string", length=50, nullable = true)
         */
        private $process;


        /**
         * @var string
         *
         * @ORM\Column(name="processType", type="string", length=50, nullable = true)
         */
        private $processType;


        /**
         * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="purchaseApprove" )
         **/
        private  $approvedBy;



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
         * @return \DateTime
         */
        public function getCreated()
        {
            return $this->created;
        }

        /**
         * @param \DateTime $created
         */
        public function setCreated($created)
        {
            $this->created = $created;
        }

        /**
         * @return \DateTime
         */
        public function getUpdated()
        {
            return $this->updated;
        }

        /**
         * @param \DateTime $updated
         */
        public function setUpdated($updated)
        {
            $this->updated = $updated;
        }


        /**
         * @return mixed
         */
        public function getCreatedBy()
        {
            return $this->createdBy;
        }

        /**
         * @param mixed $createdBy
         */
        public function setCreatedBy($createdBy)
        {
            $this->createdBy = $createdBy;
        }

        /**
         * @return \DateTime
         */
        public function getReceiveDate()
        {
            return $this->receiveDate;
        }

        /**
         * @param \DateTime $receiveDate
         */
        public function setReceiveDate($receiveDate)
        {
            $this->receiveDate = $receiveDate;
        }


        /**
         * @return mixed
         */
        public function getApprovedBy()
        {
            return $this->approvedBy;
        }

        /**
         * @param mixed $approvedBy
         */
        public function setApprovedBy($approvedBy)
        {
            $this->approvedBy = $approvedBy;
        }

        /**
         * @return string
         */
        public function getProcess()
        {
            return $this->process;
        }

        /**
         * @param string $process
         */
        public function setProcess($process)
        {
            $this->process = $process;
        }


        /**
         * Local
         * Foreign
         * Service
         * @return string
         */
        public function getProcessType()
        {
            return $this->processType;
        }

        /**
         * @param string $processType
         */
        public function setProcessType($processType)
        {
            $this->processType = $processType;
        }

        /**
         * @return mixed
         */
        public function getGlobalOption()
        {
            return $this->globalOption;
        }

        /**
         * @param mixed $globalOption
         */
        public function setGlobalOption($globalOption)
        {
            $this->globalOption = $globalOption;
        }


        /**
         * @return float
         */
        public function getPayment()
        {
            return $this->payment;
        }

        /**
         * @param float $payment
         */
        public function setPayment($payment)
        {
            $this->payment = $payment;
        }



        /**
         * @return int
         */
        public function getCode()
        {
            return $this->code;
        }

        /**
         * @param int $code
         */
        public function setCode($code)
        {
            $this->code = $code;
        }


        /**
         * @return TransactionMethod
         */
        public function getTransactionMethod()
        {
            return $this->transactionMethod;
        }

        /**
         * @param TransactionMethod $transactionMethod
         */
        public function setTransactionMethod($transactionMethod)
        {
            $this->transactionMethod = $transactionMethod;
        }

        /**
         * @return AccountBank
         */
        public function getAccountBank()
        {
            return $this->accountBank;
        }

        /**
         * @param AccountBank $accountBank
         */
        public function setAccountBank($accountBank)
        {
            $this->accountBank = $accountBank;
        }


        /**
         * @return string
         */
        public function getRemark()
        {
            return $this->remark;
        }

        /**
         * @param string $remark
         */
        public function setRemark($remark)
        {
            $this->remark = $remark;
        }


        /**
         * @return mixed
         */
        public function getAccountMobileBank()
        {
            return $this->accountMobileBank;
        }

        /**
         * @param mixed $accountMobileBank
         */
        public function setAccountMobileBank($accountMobileBank)
        {
            $this->accountMobileBank = $accountMobileBank;
        }


        /**
         * @return PurchaseItem
         */
        public function getPurchaseItems()
        {
            return $this->purchaseItems;
        }

        /**
         * @return float
         */
        public function getTotalTaxIncidence()
        {
            return $this->totalTaxIncidence;
        }

        /**
         * @param float $totalTaxIncidence
         */
        public function setTotalTaxIncidence($totalTaxIncidence)
        {
            $this->totalTaxIncidence = $totalTaxIncidence;
        }

        /**
         * @return float
         */
        public function getAdvanceTradeVat()
        {
            return $this->advanceTradeVat;
        }

        /**
         * @param float $advanceTradeVat
         */
        public function setAdvanceTradeVat($advanceTradeVat)
        {
            $this->advanceTradeVat = $advanceTradeVat;
        }

        /**
         * @return float
         */
        public function getRecurringDeposit()
        {
            return $this->recurringDeposit;
        }

        /**
         * @param float $recurringDeposit
         */
        public function setRecurringDeposit($recurringDeposit)
        {
            $this->recurringDeposit = $recurringDeposit;
        }

        /**
         * @return float
         */
        public function getAdvanceIncomeTax()
        {
            return $this->advanceIncomeTax;
        }

        /**
         * @param float $advanceIncomeTax
         */
        public function setAdvanceIncomeTax($advanceIncomeTax)
        {
            $this->advanceIncomeTax = $advanceIncomeTax;
        }

        /**
         * @return float
         */
        public function getValueAddedTax()
        {
            return $this->valueAddedTax;
        }

        /**
         * @param float $valueAddedTax
         */
        public function setValueAddedTax($valueAddedTax)
        {
            $this->valueAddedTax = $valueAddedTax;
        }

        /**
         * @return float
         */
        public function getSupplementaryDuty()
        {
            return $this->supplementaryDuty;
        }

        /**
         * @param float $supplementaryDuty
         */
        public function setSupplementaryDuty($supplementaryDuty)
        {
            $this->supplementaryDuty = $supplementaryDuty;
        }

        /**
         * @return float
         */
        public function getCustomsDuty()
        {
            return $this->customsDuty;
        }

        /**
         * @param float $customsDuty
         */
        public function setCustomsDuty($customsDuty)
        {
            $this->customsDuty = $customsDuty;
        }

        /**
         * @return float
         */
        public function getNetTotal()
        {
            return $this->netTotal;
        }

        /**
         * @param float $netTotal
         */
        public function setNetTotal($netTotal)
        {
            $this->netTotal = $netTotal;
        }

        /**
         * @return float
         */
        public function getSubTotal()
        {
            return $this->subTotal;
        }

        /**
         * @param float $subTotal
         */
        public function setSubTotal($subTotal)
        {
            $this->subTotal = $subTotal;
        }

        /**
         * @return float
         */
        public function getRebate()
        {
            return $this->rebate;
        }

        /**
         * @param float $rebate
         */
        public function setRebate($rebate)
        {
            $this->rebate = $rebate;
        }

        /**
         * @return float
         */
        public function getVatDeductionSource()
        {
            return $this->vatDeductionSource;
        }

        /**
         * @param float $vatDeductionSource
         */
        public function setVatDeductionSource($vatDeductionSource)
        {
            $this->vatDeductionSource = $vatDeductionSource;
        }

        /**
         * @return float
         */
        public function getDiscount()
        {
            return $this->discount;
        }

        /**
         * @param float $discount
         */
        public function setDiscount($discount)
        {
            $this->discount = $discount;
        }

        /**
         * @return string
         */
        public function getGrn()
        {
            return $this->grn;
        }

        /**
         * @param string $grn
         */
        public function setGrn($grn)
        {
            $this->grn = $grn;
        }

        /**
         * @return string
         */
        public function getChallanNo()
        {
            return $this->challanNo;
        }

        /**
         * @param string $challanNo
         */
        public function setChallanNo($challanNo)
        {
            $this->challanNo = $challanNo;
        }

        /**
         * @return string
         */
        public function getLcNo()
        {
            return $this->lcNo;
        }

        /**
         * @param string $lcNo
         */
        public function setLcNo($lcNo)
        {
            $this->lcNo = $lcNo;
        }

        /**
         * @return mixed
         */
        public function getPoDate()
        {
            return $this->poDate;
        }

        /**
         * @param mixed $poDate
         */
        public function setPoDate($poDate)
        {
            $this->poDate = $poDate;
        }

        /**
         * @return AccountPurchase
         */
        public function getAccountPurchase()
        {
            return $this->accountPurchase;
        }

        /**
         * @return PurchaseOrder
         */
        public function getPurchaseOrder()
        {
            return $this->purchaseOrder;
        }

        /**
         * @param PurchaseOrder $purchaseOrder
         */
        public function setPurchaseOrder($purchaseOrder)
        {
            $this->purchaseOrder = $purchaseOrder;
        }

        /**
         * @return TallyConfig
         */
        public function getConfig()
        {
            return $this->config;
        }

        /**
         * @param TallyConfig $config
         */
        public function setConfig($config)
        {
            $this->config = $config;
        }

        /**
         * @return AccountVendor
         */
        public function getVendor()
        {
            return $this->vendor;
        }

        /**
         * @param AccountVendor $vendor
         */
        public function setVendor($vendor)
        {
            $this->vendor = $vendor;
        }

    }

