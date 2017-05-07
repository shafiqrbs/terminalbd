<?php

namespace Appstore\Bundle\HospitalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * PathologicalReport
 *
 * @ORM\Table( name ="hms_particular_report")
 * @ORM\Entity(repositoryClass="")
 */
class PathologicalReport
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=10, nullable=true)
     */
    private $code;


    /**
     * @ORM\OneToOne(targetEntity="Appstore\Bundle\HospitalBundle\Entity\Particular", mappedBy="pathologicalReport")
     **/
    private $particular;

     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\InvoiceParticular", mappedBy="pathologicalReport")
     **/
    private $invoiceParticulars;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean" )
     */
    private $status= true;

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
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
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
     * @return mixed
     */
    public function getParticular()
    {
        return $this->particular;
    }

    /**
     * @return mixed
     */
    public function getInvoiceParticulars()
    {
        return $this->invoiceParticulars;
    }


}

