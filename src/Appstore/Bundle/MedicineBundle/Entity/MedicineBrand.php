<?php

namespace Appstore\Bundle\MedicineBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MedicineBrand
 *
 * @ORM\Table("medicine_brand")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\MedicineBundle\Repository\MedicineBrandRepository")
 */
class MedicineBrand
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineGeneric", inversedBy="medicineBrands")
     **/
    private $medicineGeneric;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineCompany", inversedBy="medicineBrands")
     **/
    private $medicineCompany;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=150)
     */
    private $name;


    /**
     * @var string
     *
     * @ORM\Column(name="brand_id", type="string", length=255)
     */
    private $brandId;

/**
     * @var string
     *
     * @ORM\Column(name="generic_id", type="string", length=255)
     */
    private $genericId;

/**
     * @var string
     *
     * @ORM\Column(name="company_id", type="string", length=255)
     */
    private $companyId;


    /**
     * @var string
     *
     * @ORM\Column(name="medicineForm", type="string", length=255)
     */
    private $medicineForm;


    /**
     * @var string
     *
     * @ORM\Column(name="strength", type="string", length=100)
     */
    private $strength;


    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float", length=255)
     */
    private $price;


    /**
     * @var string
     *
     * @ORM\Column(name="packSize", type="string", length=100)
     */
    private $packSize;




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
     * Set name
     *
     * @param string $name
     *
     * @return MedicineCompany
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getInteraction()
    {
        return $this->interaction;
    }

    /**
     * @param string $interaction
     */
    public function setInteraction($interaction)
    {
        $this->interaction = $interaction;
    }

    /**
     * @return string
     */
    public function getModeOfAction()
    {
        return $this->modeOfAction;
    }

    /**
     * @param string $modeOfAction
     */
    public function setModeOfAction($modeOfAction)
    {
        $this->modeOfAction = $modeOfAction;
    }

    /**
     * @return string
     */
    public function getSideEffect()
    {
        return $this->sideEffect;
    }

    /**
     * @param string $sideEffect
     */
    public function setSideEffect($sideEffect)
    {
        $this->sideEffect = $sideEffect;
    }

    /**
     * @return string
     */
    public function getDose()
    {
        return $this->dose;
    }

    /**
     * @param string $dose
     */
    public function setDose($dose)
    {
        $this->dose = $dose;
    }

    /**
     * @return string
     */
    public function getContraIndication()
    {
        return $this->contraIndication;
    }

    /**
     * @param string $contraIndication
     */
    public function setContraIndication($contraIndication)
    {
        $this->contraIndication = $contraIndication;
    }

    /**
     * @return string
     */
    public function getIndication()
    {
        return $this->indication;
    }

    /**
     * @param string $indication
     */
    public function setIndication($indication)
    {
        $this->indication = $indication;
    }

    /**
     * @return string
     */
    public function getPrecaution()
    {
        return $this->precaution;
    }

    /**
     * @param string $precaution
     */
    public function setPrecaution($precaution)
    {
        $this->precaution = $precaution;
    }



    /**
     * @return string
     */
    public function getMedicineForm()
    {
        return $this->medicineForm;
    }

    /**
     * @param string $medicineForm
     */
    public function setMedicineForm($medicineForm)
    {
        $this->medicineForm = $medicineForm;
    }

    /**
     * @return string
     */
    public function getStrength()
    {
        return $this->strength;
    }

    /**
     * @param string $strength
     */
    public function setStrength($strength)
    {
        $this->strength = $strength;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return string
     */
    public function getPackSize()
    {
        return $this->packSize;
    }

    /**
     * @param string $packSize
     */
    public function setPackSize($packSize)
    {
        $this->packSize = $packSize;
    }

    /**
     * @return MedicineGeneric
     */
    public function getMedicineGeneric()
    {
        return $this->medicineGeneric;
    }

    /**
     * @param MedicineGeneric $medicineGeneric
     */
    public function setMedicineGeneric($medicineGeneric)
    {
        $this->medicineGeneric = $medicineGeneric;
    }

    /**
     * @return string
     */
    public function getBrandId()
    {
        return $this->brandId;
    }

    /**
     * @param string $brandId
     */
    public function setBrandId($brandId)
    {
        $this->brandId = $brandId;
    }

    /**
     * @return string
     */
    public function getGenericId()
    {
        return $this->genericId;
    }

    /**
     * @param string $genericId
     */
    public function setGenericId($genericId)
    {
        $this->genericId = $genericId;
    }

    /**
     * @return string
     */
    public function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     * @param string $companyId
     */
    public function setCompanyId($companyId)
    {
        $this->companyId = $companyId;
    }

    /**
     * @return MedicineCompany
     */
    public function getMedicineCompany()
    {
        return $this->medicineCompany;
    }

    /**
     * @param MedicineCompany $medicineCompany
     */
    public function setMedicineCompany($medicineCompany)
    {
        $this->medicineCompany = $medicineCompany;
    }
}

