<?php

namespace Appstore\Bundle\BusinessBundle\Entity;

use Appstore\Bundle\HospitalBundle\Entity\InvoiceParticular;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * BusinessInvoiceParticular
 *
 * @ORM\Table( name = "business_invoice_particular")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\BusinessBundle\Repository\BusinessInvoiceParticularRepository")
 */
class BusinessInvoiceParticular
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\BusinessBundle\Entity\BusinessInvoice", inversedBy="invoiceParticulars")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @ORM\OrderBy({"id" = "ASC"})
     **/
    private $businessInvoice;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\BusinessBundle\Entity\BusinessParticular", inversedBy="invoiceParticular", cascade={"persist"} )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private $businessParticular;

    /**
     * @var string
     *
     * @ORM\Column(name="metaKey", type="string", length=225, nullable=true)
     */
    private $metaKey;

    /**
     * @var string
     *
     * @ORM\Column(name="metaValue", type="text", nullable=true)
     */
    private $metaValue;

    /**
     * @var boolean
     *
     * @ORM\Column(name="metaStatus", type="boolean", nullable=true)
     */
    private $metaStatus;



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
    public function getMetaKey()
    {
        return $this->metaKey;
    }

    /**
     * @param string $metaKey
     */
    public function setMetaKey($metaKey)
    {
        $this->metaKey = $metaKey;
    }

    /**
     * @return string
     */
    public function getMetaValue()
    {
        return $this->metaValue;
    }

    /**
     * @param string $metaValue
     */
    public function setMetaValue($metaValue)
    {
        $this->metaValue = $metaValue;
    }

    /**
     * @return bool
     */
    public function getMetaStatus()
    {
        return $this->metaStatus;
    }

    /**
     * @param bool $metaStatus
     */
    public function setMetaStatus($metaStatus)
    {
        $this->metaStatus = $metaStatus;
    }

    /**
     * @return BusinessParticular
     */
    public function getBusinessParticular()
    {
        return $this->businessParticular;
    }

    /**
     * @param BusinessParticular $businessParticular
     */
    public function setBusinessParticular($businessParticular)
    {
        $this->businessParticular = $businessParticular;
    }


}

