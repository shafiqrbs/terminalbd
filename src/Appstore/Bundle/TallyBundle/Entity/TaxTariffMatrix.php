<?php

namespace Appstore\Bundle\TallyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;


/**
 * AssetsItemBrand
 *
 * @ORM\Table("tally_tax_tariff_matrix")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\TallyBundle\Repository\TaxTariffMatrixRepository")
 */
class TaxTariffMatrix
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\TallyBundle\Entity\TaxTariff", inversedBy="taxTariffMatrixs")
     */
    protected $taxTariff;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\TallyBundle\Entity\Setting", inversedBy="taxTariffMatrixs")
     */
    protected $taxIncidence;


    /**
     * @var string
     *
     * @ORM\Column(name="tariffYear", type="string", length=100)
     */
    private $tariffYear;


    /**
     * @var float
     *
     * @ORM\Column(name="tariff", type="float", nullable = true)
     */
    private $tariff = 0.00;


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
     * @return string
     */
    public function getTariffYear()
    {
        return $this->tariffYear;
    }

    /**
     * @param string $tariffYear
     */
    public function setTariffYear($tariffYear)
    {
        $this->tariffYear = $tariffYear;
    }

    /**
     * @return float
     */
    public function getTariff()
    {
        return $this->tariff;
    }

    /**
     * @param float $tariff
     */
    public function setTariff($tariff)
    {
        $this->tariff = $tariff;
    }


}

