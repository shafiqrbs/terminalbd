<?php

namespace Appstore\Bundle\AccountingBundle\Entity;

use Appstore\Bundle\DomainUserBundle\Entity\Branches;
use Appstore\Bundle\HospitalBundle\Entity\DoctorInvoice;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Setting\Bundle\ToolBundle\Entity\TransactionMethod;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ExpenditureItem
 *
 * @ORM\Table("expenditure_item")
 * @ORM\Entity(repositoryClass="")
 */
class ExpenditureItem
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\Expenditure", inversedBy="expenditureItems", cascade={"detach","merge"} )
     * @ORM\JoinColumn(name="expenditure_id", referencedColumnName="id", nullable=true, onDelete="cascade")
     **/
    private  $expenditure;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountPurchase", inversedBy="expenditureItems", cascade={"detach","merge"} )
     * @ORM\JoinColumn(name="purchase_id", referencedColumnName="id", nullable=true, onDelete="cascade")
     **/
    private  $purchase;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float")
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="particular", type="string", length=255, nullable = true)
     */
    private $particular;


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
     * Set amount
     *
     * @param float $amount
     *
     * @return Expenditure
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getParticular()
    {
        return $this->particular;
    }

    /**
     * @param string $particular
     */
    public function setParticular(string $particular)
    {
        $this->particular = $particular;
    }

    /**
     * @return Expenditure
     */
    public function getExpenditure()
    {
        return $this->expenditure;
    }

    /**
     * @param Expenditure $expenditure
     */
    public function setExpenditure($expenditure)
    {
        $this->expenditure = $expenditure;
    }

}

