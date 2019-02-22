<?php

namespace Appstore\Bundle\MedicineBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * MedicineConfig
 *
 * @ORM\Table( name="medicine_config")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\MedicineBundle\Repository\MedicineConfigRepository")
 */
class MedicineConfig
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
     * @ORM\OneToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="medicineConfig" , cascade={"persist", "remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private $globalOption;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineStock", mappedBy="medicineConfig")
     **/
    private $medicineStock;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineMinimumStock", mappedBy="medicineConfig")
     **/
    private $medicineMinimumStock;

     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineReverse", mappedBy="medicineConfig")
     **/
    private $medicineReverses;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineParticular", mappedBy="medicineConfig")
     **/
    private $medicineParticulars;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineSales", mappedBy="medicineConfig")
     **/
    private $medicineSales;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineSalesReturn", mappedBy="medicineConfig")
     **/
    private $medicineSalesReturns;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineDamage", mappedBy="medicineConfig")
     **/
    private $medicineDamages;

     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineSalesTemporary", mappedBy="medicineConfig")
     **/
    private $medicineSalesTemporary;

     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicinePurchase", mappedBy="medicineConfig")
     **/
    private $medicinePurchases;

     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicinePrepurchase", mappedBy="medicineConfig")
     **/
    private $medicinePrepurchase;

     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineVendor", mappedBy="medicineConfig")
     * @ORM\OrderBy({"companyName" = "ASC"})
     **/
    private $medicineVendors;

    /**
     * @var string
     *
     * @ORM\Column(name="printer", type = "string", length = 50, nullable = true)
     */
    private $printer;

    /**
     * @var string
     *
     * @ORM\Column(name="invoicePrefix", type = "string", length = 50, nullable = true)
     */
    private $invoicePrefix;

    /**
     * @var boolean
     *
     * @ORM\Column(name="invoicePrintLogo", type="boolean",  nullable=true)
     */
    private $invoicePrintLogo = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="posPrint", type="boolean",  nullable=true)
     */
    private $posPrint = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isPrintHeader", type="boolean",  nullable=true)
     */
    private $isPrintHeader = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isPrintFooter", type="boolean",  nullable=true)
     */
    private $isPrintFooter = true;

    /**
     * @var smallint
     *
     * @ORM\Column(name="printLeftMargin", type="smallint", nullable = true)
     */
    private $printLeftMargin = 0;

    /**
     * @var smallint
     *
     * @ORM\Column(name="printMarginTop", type="smallint",  nullable=true)
     */
    private $printTopMargin = 0;

    /**
     * @var smallint
     *
     * @ORM\Column(name="vatPercentage", type = "smallint",  nullable=true)
     */
    private $vatPercentage;

    /**
     * @var float
     *
     * @ORM\Column(name="instantVendorPercentage", type = "float",  nullable=true)
     */
    private $instantVendorPercentage = 12.5;

    /**
     * @var float
     *
     * @ORM\Column(name="vendorPercentage", type = "float",  nullable=true)
     */
    private $vendorPercentage = 8;



    /**
     * @var boolean
     *
     * @ORM\Column(name="vatEnable", type="boolean",  nullable=true)
     */
    private $vatEnable = false;


    /**
     * @var boolean
     *
     * @ORM\Column(name="isBranch", type="boolean",  nullable=true)
     */
    private $isBranch = false;

    /**
     * @var string
     *
     * @ORM\Column(name="vatRegNo", type="string",  nullable=true)
     */
    private $vatRegNo;


    /**
     * @var boolean
     *
     * @ORM\Column(name="homeService",  type="boolean",  nullable=true)
     */
    private $homeService;



    /**
     * @var boolean
     *
     * @ORM\Column(name="customPrint",  type="boolean",  nullable=true)
     */
    private $customPrint;


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
     * @return bool
     */
    public function isVatEnable()
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
     * @return bool
     */
    public function isBranch()
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
    public function isHomeService()
    {
        return $this->homeService;
    }

    /**
     * @param bool $homeService
     */
    public function setHomeService($homeService)
    {
        $this->homeService = $homeService;
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
    public function setCustomPrint($customPrint)
    {
        $this->customPrint = $customPrint;
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
     * @return bool
     */
    public function isInvoicePrintLogo()
    {
        return $this->invoicePrintLogo;
    }

    /**
     * @param bool $invoicePrintLogo
     */
    public function setInvoicePrintLogo($invoicePrintLogo)
    {
        $this->invoicePrintLogo = $invoicePrintLogo;
    }

    /**
     * @return bool
     */
    public function isPrintHeader()
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
     * @return bool
     */
    public function isPrintFooter()
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
     * @return MedicineStock
     */
    public function getMedicineStock()
    {
        return $this->medicineStock;
    }

    /**
     * @return MedicineSales
     */
    public function getMedicineSales()
    {
        return $this->medicineSales;
    }

    /**
     * @return MedicinePurchase
     */
    public function getMedicinePurchases()
    {
        return $this->medicinePurchases;
    }

    /**
     * @return MedicineVendor
     */
    public function getMedicineVendors()
    {
        return $this->medicineVendors;
    }

    /**
     * @return MedicineParticular
     */
    public function getMedicineParticulars()
    {
        return $this->medicineParticulars;
    }

    /**
     * @return MedicineReverse
     */
    public function getMedicineReverses()
    {
        return $this->medicineReverses;
    }

    /**
     * @return MedicineSalesTemporary
     */
    public function getMedicineSalesTemporary()
    {
        return $this->medicineSalesTemporary;
    }

    /**
     * @return MedicineDamage
     */
    public function getMedicineDamages()
    {
        return $this->medicineDamages;
    }

    /**
     * @return MedicineSalesReturn
     */
    public function getMedicineSalesReturns()
    {
        return $this->medicineSalesReturns;
    }

    /**
     * @return float
     */
    public function getInstantVendorPercentage()
    {
        return $this->instantVendorPercentage;
    }

    /**
     * @param float $instantVendorPercentage
     */
    public function setInstantVendorPercentage($instantVendorPercentage)
    {
        $this->instantVendorPercentage = $instantVendorPercentage;
    }

    /**
     * @return int
     */
    public function getVendorPercentage()
    {
        return $this->vendorPercentage;
    }

    /**
     * @param float $vendorPercentage
     */
    public function setVendorPercentage($vendorPercentage)
    {
        $this->vendorPercentage = $vendorPercentage;
    }

    /**
     * @return MedicineMinimumStock
     */
    public function getMedicineMinimumStock()
    {
        return $this->medicineMinimumStock;
    }

	/**
	 * @return MedicinePrepurchase
	 */
	public function getMedicinePrepurchase() {
		return $this->medicinePrepurchase;
	}

    /**
     * @return bool
     */
    public function getPosPrint()
    {
        return $this->posPrint;
    }

    /**
     * @param bool $posPrint
     */
    public function setPosPrint($posPrint)
    {
        $this->posPrint = $posPrint;
    }

}

