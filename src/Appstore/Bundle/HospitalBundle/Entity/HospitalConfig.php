<?php

namespace Appstore\Bundle\HospitalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * HospitalConfig
 *
 * @ORM\Table( name ="hms_config")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\HospitalBundle\Repository\HospitalConfigRepository")
 */
class HospitalConfig
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
     * @ORM\OneToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="hospitalConfig" , cascade={"persist", "remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private $globalOption;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\Invoice", mappedBy="hospitalConfig")
     **/
    private $hmsInvoices;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\HmsInvoiceReturn", mappedBy="hospitalConfig")
     **/
    private $hmsInvoiceReturns;

     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\HmsStockOut", mappedBy="hospitalConfig")
     **/
    private $stockOuts;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\HmsCommission", mappedBy="hospitalConfig")
     **/
    private $hmsCommissions;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\HmsServiceGroup", mappedBy="hospitalConfig")
     **/
    private $hmsServiceGroup;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\HmsReverse", mappedBy="hospitalConfig")
     **/
    private $hmsReverses;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\DoctorInvoice", mappedBy="hospitalConfig")
     **/
    private $doctorInvoices;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\PathologicalReport", mappedBy="hospitalConfig")
     **/
    private $pathologicalReport;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\HmsVendor", mappedBy="hospitalConfig")
     **/
    private $vendors;

     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\HmsPurchase", mappedBy="hospitalConfig")
     **/
    private $purchases;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\Particular", mappedBy="hospitalConfig")
     **/
    private $particulars;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\HmsInvoiceTemporaryParticular", mappedBy="hospitalConfig")
     **/
    private $hmsInvoiceTemporaryParticular;


    /**
     * @var string
     *
     * @ORM\Column(name="printer", type="string", length=50,nullable = true)
     */
    private $printer;

    /**
     * @var smallint
     *
     * @ORM\Column(name="vatPercentage", type="smallint",  nullable=true)
     */
    private $vatPercentage;

    /**
     * @var smallint
     *
     * @ORM\Column(name="fontSizeLabel", type="smallint",  nullable=true)
     */
    private $fontSizeLabel;

    /**
     * @var smallint
     *
     * @ORM\Column(name="fontSizeValue", type="smallint",  nullable = true)
     */
    private $fontSizeValue;

    /**
     * @var string
     *
     * @ORM\Column(name="vatRegNo", type="string",  nullable = true)
     */
    private $vatRegNo;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isBranch", type="boolean",  nullable = true)
     */
    private $isBranch = false;

     /**
     * @var boolean
     *
     * @ORM\Column(name="isInventory", type="boolean",  nullable = true)
     */
    private $isInventory = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="initialDiagnosticShow", type="boolean",  nullable = true)
     */
    private $initialDiagnosticShow = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="barcodePrint", type="boolean",  nullable = true)
     */
    private $barcodePrint = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="customPrint", type="boolean",  nullable = true)
     */
    private $customPrint = false;


    /**
     * @var boolean
     *
     * @ORM\Column(name="commissionAutoApproved", type="boolean",  nullable = true)
     */
    private $commissionAutoApproved = false;


    /**
     * @var boolean
     *
     * @ORM\Column(name="isBranchInvoice", type="boolean",  nullable=true)
     */
    private $isBranchInvoice = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="vatEnable", type="boolean",  nullable=true)
     */
    private $vatEnable = false;

    /**
     * @var smallint
     *
     * @ORM\Column(name="printMarginTop", type="smallint",  nullable=true)
     */
    private $printTopMargin = 0;


    /**
     * @var smallint
     *
     * @ORM\Column(name="printMarginBottom", type="smallint",  nullable=true)
     */
    private $printMarginBottom = 0;

    /**
     * @var smallint
     *
     * @ORM\Column(name="printMarginReportTop", type="smallint",  nullable=true)
     */
    private $printMarginReportTop = 0;

    /**
     * @var smallint
     *
     * @ORM\Column(name="printMarginReportLeft", type="smallint",  nullable=true)
     */
    private $printMarginReportLeft = 0;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isPrintHeader", type="boolean",  nullable=true)
     */
    private $isPrintHeader = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isInvoiceTitle", type="boolean",  nullable=true)
     */
    private $isInvoiceTitle = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isPrintFooter", type="boolean",  nullable=true)
     */
    private $isPrintFooter = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isPrintReportHeader", type="boolean",  nullable=true)
     */
    private $isPrintReportHeader = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="barcodeColor", type="boolean" ,  nullable=true)
     */
    private $barcodeColor;

    /**
     * @var boolean
     *
     * @ORM\Column(name="barcodeSize", type="boolean",  nullable=true)
     */
    private $barcodeSize;


    /**
     * @var string
     *
     * @ORM\Column(name="barcodeText", type="string", length=255,nullable = true)
     */
    private $barcodeText;

    /**
     * @var string
     *
     * @ORM\Column(name="invoicePrefix", type="string", length=10,nullable = true)
     */
    private $invoicePrefix;

    /**
     * @var array
     *
     * @ORM\Column(name="invoiceProcess", type="array", nullable = true)
     */
    private $invoiceProcess;

    /**
     * @var string
     *
     * @ORM\Column(name="customerPrefix", type="string", length=10,nullable = true)
     */
    private $customerPrefix;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="text", length=10,nullable = true)
     */
    private $address;

    /**
     * @var smallint
     *
     * @ORM\Column(name="barcodeWidth", type="smallint", nullable = true)
     */
    private $barcodeWidth = 140;

    /**
     * @var smallint
     *
     * @ORM\Column(name="barcodeMargin", type="smallint", nullable = true)
     */
    private $barcodeMargin = 0;

    /**
     * @var smallint
     *
     * @ORM\Column(name="barcodeBorder", type="smallint", nullable = true)
     */
    private $barcodeBorder = 0;

    /**
     * @var smallint
     *
     * @ORM\Column(name="barcodePadding", type="smallint", nullable = true)
     */
    private $barcodePadding = 0;

    /**
     * @var smallint
     *
     * @ORM\Column(name="printLeftMargin", type="smallint", nullable = true)
     */
    private $printLeftMargin = 0;

    /**
     * @var smallint
     *
     * @ORM\Column(name="barcodeHeight", type="smallint", nullable = true)
     */
    private $barcodeHeight = 80;

    /**
     * @var integer
     *
     * @ORM\Column(name="invoiceHeight", type="integer", nullable = true)
     */
    private $invoiceHeight = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="reportHeight", type="integer", nullable = true)
     */
    private $reportHeight = 0;


    /**
     * @var smallint
     *
     * @ORM\Column(name="barcodeThickness", type="smallint", nullable = true)
     */
    private $barcodeThickness = 30;

    /**
     * @var smallint
     *
     * @ORM\Column(name="barcodeFontSize", type="smallint", nullable = true)
     */
    private $barcodeFontSize = 8;

    /**
     * @var smallint
     *
     * @ORM\Column(name="barcodeScale", type="smallint", nullable = true)
     */
    private $barcodeScale = 1;

    /**
     * @var boolean
     *
     * @ORM\Column(name="invoicePrintLogo", type="boolean",  nullable=true)
     */
    private $invoicePrintLogo = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="printInstruction", type="boolean",  nullable=true)
     */
    private $printInstruction = true;


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
     * @return Particular
     */
    public function getParticulars()
    {
        return $this->particulars;
    }

    /**
     * @return ReferredDoctor
     */
    public function getReferredDoctors()
    {
        return $this->referredDoctors;
    }

    /**
     * @return Invoice
     */
    public function getInvoices()
    {
        return $this->invoices;
    }

    /**
     * @return smallint
     */
    public function getBarcodeScale()
    {
        return $this->barcodeScale;
    }

    /**
     * @param smallint $barcodeScale
     */
    public function setBarcodeScale($barcodeScale)
    {
        $this->barcodeScale = $barcodeScale;
    }

    /**
     * @return smallint
     */
    public function getBarcodeFontSize()
    {
        return $this->barcodeFontSize;
    }

    /**
     * @param smallint $barcodeFontSize
     */
    public function setBarcodeFontSize($barcodeFontSize)
    {
        $this->barcodeFontSize = $barcodeFontSize;
    }

    /**
     * @return smallint
     */
    public function getBarcodeThickness()
    {
        return $this->barcodeThickness;
    }

    /**
     * @param smallint $barcodeThickness
     */
    public function setBarcodeThickness($barcodeThickness)
    {
        $this->barcodeThickness = $barcodeThickness;
    }

    /**
     * @return smallint
     */
    public function getBarcodeHeight()
    {
        return $this->barcodeHeight;
    }

    /**
     * @param smallint $barcodeHeight
     */
    public function setBarcodeHeight($barcodeHeight)
    {
        $this->barcodeHeight = $barcodeHeight;
    }

    /**
     * @return smallint
     */
    public function getPrintLeftMargin()
    {
        return $this->printLeftMargin;
    }

    /**
     * @param smallint $printLeftMargin
     */
    public function setPrintLeftMargin($printLeftMargin)
    {
        $this->printLeftMargin = $printLeftMargin;
    }

    /**
     * @return smallint
     */
    public function getBarcodePadding()
    {
        return $this->barcodePadding;
    }

    /**
     * @param smallint $barcodePadding
     */
    public function setBarcodePadding($barcodePadding)
    {
        $this->barcodePadding = $barcodePadding;
    }

    /**
     * @return smallint
     */
    public function getBarcodeBorder()
    {
        return $this->barcodeBorder;
    }

    /**
     * @param smallint $barcodeBorder
     */
    public function setBarcodeBorder($barcodeBorder)
    {
        $this->barcodeBorder = $barcodeBorder;
    }

    /**
     * @return smallint
     */
    public function getBarcodeMargin()
    {
        return $this->barcodeMargin;
    }

    /**
     * @param smallint $barcodeMargin
     */
    public function setBarcodeMargin($barcodeMargin)
    {
        $this->barcodeMargin = $barcodeMargin;
    }

    /**
     * @return smallint
     */
    public function getBarcodeWidth()
    {
        return $this->barcodeWidth;
    }

    /**
     * @param smallint $barcodeWidth
     */
    public function setBarcodeWidth($barcodeWidth)
    {
        $this->barcodeWidth = $barcodeWidth;
    }

    /**
     * @return string
     */
    public function getBarcodeText()
    {
        return $this->barcodeText;
    }

    /**
     * @param string $barcodeText
     */
    public function setBarcodeText($barcodeText)
    {
        $this->barcodeText = $barcodeText;
    }

    /**
     * @return smallint
     */
    public function getBarcodeBrandVendor()
    {
        return $this->barcodeBrandVendor;
    }

    /**
     * @param smallint $barcodeBrandVendor
     */
    public function setBarcodeBrandVendor($barcodeBrandVendor)
    {
        $this->barcodeBrandVendor = $barcodeBrandVendor;
    }

    /**
     * @return bool
     */
    public function isBarcodeSize()
    {
        return $this->barcodeSize;
    }

    /**
     * @param bool $barcodeSize
     */
    public function setBarcodeSize($barcodeSize)
    {
        $this->barcodeSize = $barcodeSize;
    }

    /**
     * @return bool
     */
    public function isBarcodeColor()
    {
        return $this->barcodeColor;
    }

    /**
     * @param bool $barcodeColor
     */
    public function setBarcodeColor($barcodeColor)
    {
        $this->barcodeColor = $barcodeColor;
    }

    /**
     * @return bool
     */
    public function isIsPrintFooter()
    {
        return $this->isPrintFooter;
    }

    /**
     * @param bool $isPrintFooter
     */
    public function setIsPrintFooter($isPrintFooter)
    {
        $this->isPrintFooter = $isPrintFooter;
    }

    /**
     * @return bool
     */
    public function isIsPrintHeader()
    {
        return $this->isPrintHeader;
    }

    /**
     * @param bool $isPrintHeader
     */
    public function setIsPrintHeader($isPrintHeader)
    {
        $this->isPrintHeader = $isPrintHeader;
    }

    /**
     * @return smallint
     */
    public function getPrintMarginBottom()
    {
        return $this->printMarginBottom;
    }

    /**
     * @param smallint $printMarginBottom
     */
    public function setPrintMarginBottom($printMarginBottom)
    {
        $this->printMarginBottom = $printMarginBottom;
    }

    /**
     * @return string
     */
    public function getPrinter()
    {
        return $this->printer;
    }

    /**
     * @param string $printer
     */
    public function setPrinter($printer)
    {
        $this->printer = $printer;
    }

    /**
     * @return smallint
     */
    public function getVatPercentage()
    {
        return $this->vatPercentage;
    }

    /**
     * @param smallint $vatPercentage
     */
    public function setVatPercentage($vatPercentage)
    {
        $this->vatPercentage = $vatPercentage;
    }

    /**
     * @return string
     */
    public function getVatRegNo()
    {
        return $this->vatRegNo;
    }

    /**
     * @param string $vatRegNo
     */
    public function setVatRegNo($vatRegNo)
    {
        $this->vatRegNo = $vatRegNo;
    }

    /**
     * @return bool
     */
    public function isIsBranch()
    {
        return $this->isBranch;
    }

    /**
     * @param bool $isBranch
     */
    public function setIsBranch($isBranch)
    {
        $this->isBranch = $isBranch;
    }

    /**
     * @return bool
     */
    public function isBarcodePrint()
    {
        return $this->barcodePrint;
    }

    /**
     * @param bool $barcodePrint
     */
    public function setBarcodePrint($barcodePrint)
    {
        $this->barcodePrint = $barcodePrint;
    }

    /**
     * @return bool
     */
    public function getIsBranchInvoice()
    {
        return $this->isBranchInvoice;
    }

    /**
     * @param bool $isBranchInvoice
     */
    public function setIsBranchInvoice($isBranchInvoice)
    {
        $this->isBranchInvoice = $isBranchInvoice;
    }

    /**
     * @return bool
     */
    public function getVatEnable()
    {
        return $this->vatEnable;
    }

    /**
     * @param bool $vatEnable
     */
    public function setVatEnable($vatEnable)
    {
        $this->vatEnable = $vatEnable;
    }

    /**
     * @return smallint
     */
    public function getPrintTopMargin()
    {
        return $this->printTopMargin;
    }

    /**
     * @param smallint $printTopMargin
     */
    public function setPrintTopMargin($printTopMargin)
    {
        $this->printTopMargin = $printTopMargin;
    }

    /**
     * @return HmsPurchase
     */
    public function getPurchases()
    {
        return $this->purchases;
    }

    /**
     * @return HmsVendor
     */
    public function getVendors()
    {
        return $this->vendors;
    }

    /**
     * @return PathologicalReport
     */
    public function getPathologicalReport()
    {
        return $this->pathologicalReport;
    }

    /**
     * @return DoctorInvoice
     */
    public function getDoctorInvoices()
    {
        return $this->doctorInvoices;
    }

    /**
     * @return Invoice
     */
    public function getHmsInvoices()
    {
        return $this->hmsInvoices;
    }

    /**
     * @return HmsReverse
     */
    public function getHmsReverses()
    {
        return $this->hmsReverses;
    }

    /**
     * @return boolean
     */
    public function isInvoicePrintLogo()
    {
        return $this->invoicePrintLogo;
    }

    /**
     * @param boolean $invoicePrintLogo
     */
    public function setInvoicePrintLogo($invoicePrintLogo)
    {
        $this->invoicePrintLogo = $invoicePrintLogo;
    }

    /**
     * @return bool
     */
    public function getPrintInstruction()
    {
        return $this->printInstruction;
    }

    /**
     * @param bool $printInstruction
     */
    public function setPrintInstruction($printInstruction)
    {
        $this->printInstruction = $printInstruction;
    }

    /**
     * @return string
     */
    public function getInvoicePrefix()
    {
        return $this->invoicePrefix;
    }

    /**
     * @param string $invoicePrefix
     */
    public function setInvoicePrefix($invoicePrefix)
    {
        $this->invoicePrefix = $invoicePrefix;
    }

    /**
     * @return string
     */
    public function getCustomerPrefix()
    {
        return $this->customerPrefix;
    }

    /**
     * @param string $customerPrefix
     */
    public function setCustomerPrefix($customerPrefix)
    {
        $this->customerPrefix = $customerPrefix;
    }

    /**
     * @return smallint
     */
    public function getPrintMarginReportTop()
    {
        return $this->printMarginReportTop;
    }

    /**
     * @param smallint $printMarginReportTop
     */
    public function setPrintMarginReportTop($printMarginReportTop)
    {
        $this->printMarginReportTop = $printMarginReportTop;
    }

    /**
     * @return bool
     */
    public function getIsInvoiceTitle()
    {
        return $this->isInvoiceTitle;
    }

    /**
     * @param bool $isInvoiceTitle
     */
    public function setIsInvoiceTitle($isInvoiceTitle)
    {
        $this->isInvoiceTitle = $isInvoiceTitle;
    }


    /**
     * @return array
     */
    public function getInvoiceProcess()
    {
        return $this->invoiceProcess;
    }

    /**
     * @param array $invoiceProcess
     */
    public function setInvoiceProcess($invoiceProcess)
    {
        $this->invoiceProcess = $invoiceProcess;
    }

    /**
     * @return HmsServiceGroup
     */
    public function getHmsServiceGroup()
    {
        return $this->hmsServiceGroup;
    }

    /**
     * @return HmsCommission
     */
    public function getHmsCommissions()
    {
        return $this->hmsCommissions;
    }

    /**
     * @return int
     */
    public function getInvoiceHeight()
    {
        return $this->invoiceHeight;
    }

    /**
     * @param int $invoiceHeight
     */
    public function setInvoiceHeight($invoiceHeight)
    {
        $this->invoiceHeight = $invoiceHeight;
    }

    /**
     * @return int
     */
    public function getReportHeight()
    {
        return $this->reportHeight;
    }

    /**
     * @param int $reportHeight
     */
    public function setReportHeight($reportHeight)
    {
        $this->reportHeight = $reportHeight;
    }

    /**
     * @return smallint
     */
    public function getPrintMarginReportLeft()
    {
        return $this->printMarginReportLeft;
    }

    /**
     * @param smallint $printMarginReportLeft
     */
    public function setPrintMarginReportLeft($printMarginReportLeft)
    {
        $this->printMarginReportLeft = $printMarginReportLeft;
    }

    /**
     * @return smallint
     */
    public function getFontSizeLabel()
    {
        return $this->fontSizeLabel;
    }

    /**
     * @param smallint $fontSizeLabel
     */
    public function setFontSizeLabel($fontSizeLabel)
    {
        $this->fontSizeLabel = $fontSizeLabel;
    }

    /**
     * @return smallint
     */
    public function getFontSizeValue()
    {
        return $this->fontSizeValue;
    }

    /**
     * @param smallint $fontSizeValue
     */
    public function setFontSizeValue($fontSizeValue)
    {
        $this->fontSizeValue = $fontSizeValue;
    }

    /**
     * @return bool
     */
    public function getInitialDiagnosticShow()
    {
        return $this->initialDiagnosticShow;
    }

    /**
     * @param bool $initialDiagnosticShow
     */
    public function setInitialDiagnosticShow($initialDiagnosticShow)
    {
        $this->initialDiagnosticShow = $initialDiagnosticShow;
    }

    /**
     * @return HmsInvoiceTemporaryParticular
     */
    public function getHmsInvoiceTemporaryParticular()
    {
        return $this->hmsInvoiceTemporaryParticular;
    }

	/**
	 * @return HmsStockOut
	 */
	public function getStockOuts() {
		return $this->stockOuts;
	}

    /**
     * @return bool
     */
    public function isCustomPrint()
    {
        return $this->customPrint;
    }

    /**
     * @param bool $customPrint
     */
    public function setCustomPrint(bool $customPrint)
    {
        $this->customPrint = $customPrint;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address)
    {
        $this->address = $address;
    }

    /**
     * @return bool
     */
    public function isPrintReportHeader()
    {
        return $this->isPrintReportHeader;
    }

    /**
     * @param bool $isPrintReportHeader
     */
    public function setIsPrintReportHeader(bool $isPrintReportHeader)
    {
        $this->isPrintReportHeader = $isPrintReportHeader;
    }

    /**
     * @return bool
     */
    public function isInventory()
    {
        return $this->isInventory;
    }

    /**
     * @param bool $isPrintReportHeader
     */
    public function setisInventory(bool $isInventory)
    {
        $this->isInventory = $isInventory;
    }



    /**
     * @param smallint $printReportTopMargin
     */
    public function setPrintReportTopMargin($printReportTopMargin)
    {
        $this->printReportTopMargin = $printReportTopMargin;
    }

    /**
     * @return HmsInvoiceReturn
     */
    public function getHmsInvoiceReturns()
    {
        return $this->hmsInvoiceReturns;
    }

    /**
     * @return bool
     */
    public function isCommissionAutoApproved()
    {
        return $this->commissionAutoApproved;
    }

    /**
     * @param bool $commissionAutoApproved
     */
    public function setCommissionAutoApproved($commissionAutoApproved)
    {
        $this->commissionAutoApproved = $commissionAutoApproved;
    }


}

