<?php

namespace Appstore\Bundle\DmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * DMSConfig
 *
 * @ORM\Table( name ="dms_config")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\DmsBundle\Repository\DmsConfigRepository")
 */
class DmsConfig
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
     * @ORM\OneToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="dmsConfig" , cascade={"persist", "remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private $globalOption;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\DmsBundle\Entity\DmsInvoice", mappedBy="dmsConfig")
     **/
    private $dmsInvoices;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\DmsBundle\Entity\DmsReverse", mappedBy="dmsConfig")
     **/
    private $dmsReverses;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\DmsBundle\Entity\DmsDoctorInvoice", mappedBy="dmsConfig")
     **/
    private $dmsDoctorInvoices;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\DmsBundle\Entity\DmsVendor", mappedBy="dmsConfig")
     **/
    private $dmsVendors;

     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\DmsBundle\Entity\DmsPurchase", mappedBy="dmsConfig")
     **/
    private $dmsPurchases;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\DmsBundle\Entity\DmsParticular", mappedBy="dmsConfig")
     **/
    private $dmsParticulars;


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
     * @ORM\Column(name="fontSizeValue", type="smallint",  nullable=true)
     */
    private $fontSizeValue;

    /**
     * @var string
     *
     * @ORM\Column(name="vatRegNo", type="string",  nullable=true)
     */
    private $vatRegNo;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isBranch", type="boolean",  nullable=true)
     */
    private $isBranch = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="barcodePrint", type="boolean",  nullable=true)
     */
    private $barcodePrint = false;


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
     * @return DmsInvoice
     */
    public function getDmsInvoices()
    {
        return $this->dmsInvoices;
    }

    /**
     * @return DmsDoctorInvoice
     */
    public function getDmsDoctorInvoices()
    {
        return $this->dmsDoctorInvoices;
    }

    /**
     * @return DmsVendor
     */
    public function getDmsVendors()
    {
        return $this->dmsVendors;
    }

    /**
     * @return DmsPurchase
     */
    public function getDmsPurchases()
    {
        return $this->dmsPurchases;
    }

    /**
     * @return DmsParticular
     */
    public function getDmsParticulars()
    {
        return $this->dmsParticulars;
    }


}

