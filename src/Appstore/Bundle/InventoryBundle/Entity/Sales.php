<?php

namespace Appstore\Bundle\InventoryBundle\Entity;

use Appstore\Bundle\AccountingBundle\Entity\AccountBank;
use Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\ToolBundle\Entity\Bank;
use Setting\Bundle\ToolBundle\Entity\TransactionMethod;

/**
 * Sales
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Appstore\Bundle\InventoryBundle\Repository\SalesRepository")
 */
class Sales
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
         * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\InventoryConfig", inversedBy="sales" )
         **/
        private  $inventoryConfig;

        /**
         * @ORM\ManyToOne(targetEntity="Appstore\Bundle\DomainUserBundle\Entity\Branches", inversedBy="sales" )
         **/

        private  $branches;


        /**
         * @ORM\OneToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\SalesImport", inversedBy="sales" )
         **/
        private  $salesImport;

        /**
         * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\SalesItem", mappedBy="sales" , cascade={"remove"} )
         * @ORM\OrderBy({"id" = "DESC"})
         **/
        private  $salesItems;

        /**
         * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\SalesReturn", mappedBy="sales" , cascade={"remove"} )
         **/
        private  $salesReturn;

        /**
         * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\SalesReturn", mappedBy="salesAdjustmentInvoice" , cascade={"remove"} )
         **/
        private  $salesReturnAdjustment;

        /**
         * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountSales", mappedBy="sales" )
         * @ORM\OrderBy({"id" = "DESC"})
         **/
        private  $accountSales;

        /**
         * @ORM\ManyToOne(targetEntity="Appstore\Bundle\DomainUserBundle\Entity\Customer", inversedBy="sales"  )
         **/
        private  $customer;

        /**
         * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="salesUser" )
         **/
        private  $salesBy;

        /**
         * @Gedmo\Blameable(on="create")
         * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="sales" )
         **/
        private  $createdBy;


        /**
         * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="salesApprovedBy" )
         **/
        private  $approvedBy;

        /**
         * @var string
         *
         * @ORM\Column(name="paymentMethod", type="string", length=50, nullable=true)
         */
        private $paymentMethod='Cash';

        /**
         * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\TransactionMethod", inversedBy="sales" )
         **/
        private  $transactionMethod;

        /**
         * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\Bank", inversedBy="sales" )
         **/
        private  $bank;

        /**
         * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountBank", inversedBy="sales" )
         **/
        private  $accountBank;


        /**
         * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank", inversedBy="sales" )
         **/
        private  $accountMobileBank;


        /**
         * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\paymentCard", inversedBy="sales" )
         **/
        private  $paymentCard;

        /**
         * @var string
         *
         * @ORM\Column(name="cardNo", type="string", length=100, nullable=true)
         */
        private $cardNo;

        /**
         * @var string
         *
         * @ORM\Column(name="paymentMobile", type="string", length=50, nullable=true)
         */
        private $paymentMobile;

        /**
         * @var string
         *
         * @ORM\Column(name="paymentInWord", type="string", length=255, nullable=true)
         */
        private $paymentInWord;

        /**
         * @var string
         *
         * @ORM\Column(name="courierInvoice", type="string", length=50, nullable=true)
         */
        private $courierInvoice;

        /**
         * @var string
         *
         * @ORM\Column(name="process", type="string", length=50, nullable=true)
         */
        private $process ='In-progress';

        /**
         * @var string
         *
         * @ORM\Column(name="transactionId", type="string", length=100, nullable=true)
         */
        private $transactionId;

        /**
         * @var string
         *
         * @ORM\Column(name="invoice", type="string", length=50, nullable=true)
         */
        private $invoice;

        /**
         * @var integer
         *
         * @ORM\Column(name="code", type="integer",  nullable=true)
         */
        private $code;

        /**
         * @var integer
         *
         * @ORM\Column(name="totalItem", type="smallint", length=2, nullable=true)
         */
        private $totalItem;


        /**
         * @var string
         *
         * @ORM\Column(name="paymentStatus", type="string", length=50, nullable=true)
         */
        private $paymentStatus = "Pending";


        /**
         * @var string
         *
         * @ORM\Column(name="subTotal", type="decimal", nullable=true)
         */
        private $subTotal;

        /**
         * @var string
         *
         * @ORM\Column(name="discount", type="decimal", nullable=true)
         */
        private $discount;

        /**
         * @var string
         *
         * @ORM\Column(name="vat", type="decimal", nullable=true)
         */
        private $vat;

        /**
         * @var string
         *
         * @ORM\Column(name="total", type="decimal", nullable=true)
         */
        private $total;


        /**
         * @var string
         *
         * @ORM\Column(name="payment", type="decimal", nullable=true)
         */
        private $payment;


        /**
         * @var string
         *
         * @ORM\Column(name="comment", type="text", nullable=true)
         */
        private $comment;

        /**
         * @var string
         *
         * @ORM\Column(name="due", type="decimal", nullable=true)
         */
        private $due;

        /**
         * @var string
         *
         * @ORM\Column(name="mobile", type="text", nullable=true)
         */
        private $mobile;

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
         * @return int
         */
        public function getId()
        {
         return $this->id;
        }

        /**
         * @param int $id
         */
        public function setId($id)
        {
         $this->id = $id;
        }

        /**
         * @return string
         */
        public function getPaymentMethod()
        {
         return $this->paymentMethod;
        }

        /**
         * @param string $paymentMethod
         * Cash
         * Cheque
         * Giftcard
         * Bkash
         * Payment Card
         * Other
         */
        public function setPaymentMethod($paymentMethod)
        {
         $this->paymentMethod = $paymentMethod;
        }

        /**
         * @return string
         */
        public function getSubTotal()
        {
         return $this->subTotal;
        }

        /**
         * @param string $subTotal
         */
        public function setSubTotal($subTotal)
        {
         $this->subTotal = $subTotal;
        }

        /**
         * @return string
         */
        public function getDiscount()
        {
         return $this->discount;
        }

        /**
         * @param string $discount
         */
        public function setDiscount($discount)
        {
         $this->discount = $discount;
        }

        /**
         * @return string
         */
        public function getVat()
        {
         return $this->vat;
        }

        /**
         * @param string $vat
         */
        public function setVat($vat)
        {
         $this->vat = $vat;
        }

        /**
         * @return string
         */
        public function getTotal()
        {
         return $this->total;
        }

        /**
         * @param string $total
         */
        public function setTotal($total)
        {
         $this->total = $total;
        }

        /**
         * @return string
         */
        public function getComment()
        {
         return $this->comment;
        }

        /**
         * @param string $comment
         */
        public function setComment($comment)
        {
         $this->comment = $comment;
        }

        /**
         * @return string
         */
        public function getDue()
        {
         return $this->due;
        }

        /**
         * @param string $due
         */
        public function setDue($due)
        {
         $this->due = $due;
        }



        /**
         * @return salesItem
         */
        public function getSalesItems()
        {
         return $this->salesItems;
        }


        /**
         * @return mixed
         */
        public function getInventoryConfig()
        {
         return $this->inventoryConfig;
        }

        /**
         * @param mixed $inventoryConfig
         */
        public function setInventoryConfig($inventoryConfig)
        {
         $this->inventoryConfig = $inventoryConfig;
        }

        /**
         * @return string
         */
        public function getPaymentStatus()
        {
         return $this->paymentStatus;
        }

        /**
         * @param string $paymentStatus
         * Paid
         * Pending
         * Partial
         * Due
         * Other
         */
        public function setPaymentStatus($paymentStatus)
        {
         $this->paymentStatus = $paymentStatus;
        }

        /**
         * @return mixed
         */
        public function getCustomer()
        {
         return $this->customer;
        }

        /**
         * @param mixed $customer
         */
        public function setCustomer($customer)
        {
         $this->customer = $customer;
        }

        /**
         * @return mixed
         */
        public function getSalesBy()
        {
         return $this->salesBy;
        }

        /**
         * @param mixed $salesBy
         */
        public function setSalesBy($salesBy)
        {
         $this->salesBy = $salesBy;
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
         * @return int
         */
        public function getTotalItem()
        {
         return $this->totalItem;
        }

        /**
         * @param int $totalItem
         */
        public function setTotalItem($totalItem)
        {
         $this->totalItem = $totalItem;
        }

        /**
         * @return Bank
         */
        public function getBank()
        {
         return $this->bank;
        }

        /**
         * @param Bank $bank
         */
        public function setBank($bank)
        {
         $this->bank = $bank;
        }

        /**
         * @return mixed
         */
        public function getPaymentCard()
        {
         return $this->paymentCard;
        }

        /**
         * @param mixed $paymentCard
         */
        public function setPaymentCard($paymentCard)
        {
         $this->paymentCard = $paymentCard;
        }

        /**
         * @return string
         */
        public function getMobile()
        {
         return $this->mobile;
        }

        /**
         * @param string $mobile
         */
        public function setMobile($mobile)
        {
         $this->mobile = $mobile;
        }

        /**
         * @return mixed
         */
        public function getSalesReturn()
        {
         return $this->salesReturn;
        }

        /**
         * @param mixed $salesReturn
         */
        public function setSalesReturn($salesReturn)
        {
         $this->salesReturn = $salesReturn;
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
         * @return integer
         */
        public function getCode()
        {
         return $this->code;
        }

        /**
         * @param integer $code
         */
        public function setCode($code)
        {
         $this->code = $code;
        }

        /**
         * @return string
         */
        public function getInvoice()
        {
         return $this->invoice;
        }

        /**
         * @param string $invoice
         */
        public function setInvoice($invoice)
        {
         $this->invoice = $invoice;
        }


        /**
         * @return string
         */
        public function getPayment()
        {
         return $this->payment;
        }

        /**
         * @param string $payment
         */
        public function setPayment($payment)
        {
         $this->payment = $payment;
        }


        /**
         * @return mixed
         */
        public function getAccountSales()
        {
         return $this->accountSales;
        }

        /**
         * @return mixed
         */
        public function getSalesImport()
        {
                return $this->salesImport;
        }

        /**
         * @param mixed $salesImport
         */
        public function setSalesImport($salesImport)
        {
                $this->salesImport = $salesImport;
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
         * @return AccountMobileBank
         */
        public function getAccountMobileBank()
        {
                return $this->accountMobileBank;
        }

        /**
         * @param AccountMobileBank $accountMobileBank
         */
        public function setAccountMobileBank($accountMobileBank)
        {
                $this->accountMobileBank = $accountMobileBank;
        }

        /**
         * @return string
         */
        public function getPaymentMobile()
        {
                return $this->paymentMobile;
        }

        /**
         * @param string $paymentMobile
         */
        public function setPaymentMobile($paymentMobile)
        {
                $this->paymentMobile = $paymentMobile;
        }

        /**
         * @return string
         */
        public function getTransactionId()
        {
                return $this->transactionId;
        }

        /**
         * @param string $transactionId
         */
        public function setTransactionId($transactionId)
        {
                $this->transactionId = $transactionId;
        }

        /**
         * @return string
         */
        public function getCardNo()
        {
                return $this->cardNo;
        }

        /**
         * @param string $cardNo
         */
        public function setCardNo($cardNo)
        {
                $this->cardNo = $cardNo;
        }

        /**
         * @return mixed
         */
        public function getBranches()
        {
                return $this->branches;
        }

        /**
         * @param mixed $branches
         */
        public function setBranches($branches)
        {
                $this->branches = $branches;
        }

        /**
         * @return mixed
         */
        public function getSalesReturnAdjustment()
        {
                return $this->salesReturnAdjustment;
        }

        /**
         * @return string
         */
        public function getCourierInvoice()
        {
                return $this->courierInvoice;
        }

        /**
         * @param string $courierInvoice
         */
        public function setCourierInvoice($courierInvoice)
        {
                $this->courierInvoice = $courierInvoice;
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
         * @return string
         */
        public function getPaymentInWord()
        {
                return $this->paymentInWord;
        }

        /**
         * @param string $paymentInWord
         */
        public function setPaymentInWord($paymentInWord)
        {
                $this->paymentInWord = $paymentInWord;
        }


}

