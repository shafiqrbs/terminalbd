<?php

namespace Setting\Bundle\ToolBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Setting\Bundle\AppearanceBundle\Entity\FeatureWidget;
use Setting\Bundle\AppearanceBundle\Entity\SidebarWidget;
use Setting\Bundle\AppearanceBundle\Entity\SidebarWidgetPanel;
use Setting\Bundle\ContentBundle\Entity\Page;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Setting\Bundle\ToolBundle\Repository\PortalConfigRepository")
 */
class PortalConfig
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="copyright", type="string", length=255 , unique=true)
     */
    private $copyright;

    /**
     * @var string
     *
     * @ORM\Column(name="poweredBy", type="string", length=255 , unique=true)
     */
    private $poweredBy;


    /**
     * @var string
     *
     * @ORM\Column(name="sponsorBy", type="string", length=255 , unique=true)
     */
    private $sponsorBy;


    /**
     * @var string
     *
     * @ORM\Column(name="companyName", type="string", length=255 , unique=true)
     */
    private $companyName;

    /**
     * @return string
     */
    public function getCopyright()
    {
        return $this->copyright;
    }

    /**
     * @param string $copyright
     */
    public function setCopyright($copyright)
    {
        $this->copyright = $copyright;
    }

    /**
     * @return string
     */
    public function getPoweredBy()
    {
        return $this->poweredBy;
    }

    /**
     * @param string $poweredBy
     */
    public function setPoweredBy($poweredBy)
    {
        $this->poweredBy = $poweredBy;
    }

    /**
     * @return string
     */
    public function getSponsorBy()
    {
        return $this->sponsorBy;
    }

    /**
     * @param string $sponsorBy
     */
    public function setSponsorBy($sponsorBy)
    {
        $this->sponsorBy = $sponsorBy;
    }

    /**
     * @return string
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * @param string $companyName
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;
    }


}
