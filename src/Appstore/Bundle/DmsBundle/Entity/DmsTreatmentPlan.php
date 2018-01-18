<?php

namespace Appstore\Bundle\DmsBundle\Entity;

use Appstore\Bundle\DmsBundle\Entity\DmsInvoice;
use Appstore\Bundle\DmsBundle\Entity\DmsParticular;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * HmsPurchaseItem
 *
 * @ORM\Table(name ="dms_treatment_plan")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\DmsBundle\Repository\DmsTreatmentPlanRepository")
 */
class DmsTreatmentPlan
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\DmsBundle\Entity\DmsParticular", inversedBy="dmsTreatmentPlans" )
     **/
    private  $dmsParticular;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\DmsBundle\Entity\DmsInvoice", inversedBy="dmsTreatmentPlans" )
     **/
    private  $dmsInvoice;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float", nullable=true)
     */
    private $amount;

    /**
     * @var float
     *
     * @ORM\Column(name="payment", type="float", nullable=true)
     */
    private $payment;

    /**
     * @var float
     *
     * @ORM\Column(name="balance", type="float", nullable=true)
     */
    private $balance;


    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean" )
     */
    private $status=true;



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
     * @return DmsParticular
     */
    public function getParticular()
    {
        return $this->particular;
    }

    /**
     * @param DmsParticular $particular
     */
    public function setParticular($particular)
    {
        $this->particular = $particular;
    }

    /**
     * @return bool
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param bool $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return DmsInvoice
     */
    public function getDmsInvoice()
    {
        return $this->dmsInvoice;
    }

    /**
     * @param DmsInvoice $dmsInvoice
     */
    public function setDmsInvoice($dmsInvoice)
    {
        $this->dmsInvoice = $dmsInvoice;
    }

    /**
     * @return DmsParticular
     */
    public function getDmsParticular()
    {
        return $this->dmsParticular;
    }

    /**
     * @param DmsParticular $dmsParticular
     */
    public function setDmsParticular($dmsParticular)
    {
        $this->dmsParticular = $dmsParticular;
    }


}

