<?php

namespace Appstore\Bundle\TallyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * WearHouse
 *
 * @ORM\Table( name ="tally_wearhouse")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\TallyBundle\Repository\WearHouseRepository")
 */
class WearHouse
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\TallyBundle\Entity\TallyConfig", inversedBy="wearHouse" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private $config;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\TallyBundle\Entity\PurchaseItem", mappedBy="wearHouse")
     * @ORM\OrderBy({"id" = "ASC"})
     **/
    private $purchaseItems;


    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="shortCode", type="string", length=5, nullable=true)
     */
    private $shortCode;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=50, nullable=true)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=10, nullable=true)
     */
    private $code;

     /**
     * @var string
     *
     * @ORM\Column(name="wearHouseCode", type="string", length=10, nullable=true)
     */
    private $wearHouseCode;

    /**
     * @var int
     *
     * @ORM\Column(name="sorting", type="smallint",  length=2, nullable=true)
     */
    private $sorting = 0;


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
     * @return WearHouse
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param WearHouse $name
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
     * @return int
     */
    public function getSorting()
    {
        return $this->sorting;
    }

    /**
     * @param int $sorting
     */
    public function setSorting($sorting)
    {
        $this->sorting = $sorting;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }


	/**
	 * @return string
	 */
	public function getShortCode(){
		return $this->shortCode;
	}

	/**
	 * @param string $shortCode
	 */
	public function setShortCode( string $shortCode ) {
		$this->shortCode = $shortCode;
	}

	/**
	 * @return string
	 */
	public function getWearHouseCode(){
		return $this->wearHouseCode;
	}

	/**
	 * @param string $wearHouseCode
	 */
	public function setWearHouseCode( string $wearHouseCode ) {
		$this->wearHouseCode = $wearHouseCode;
	}

    /**
     * @return WearHouse
     */
    public function getPurchaseItems()
    {
        return $this->purchaseItems;
    }

    /**
     * @param WearHouse $purchaseItems
     */
    public function setPurchaseItems($purchaseItems)
    {
        $this->purchaseItems = $purchaseItems;
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
     * @return SalesItem
     */
    public function getSalesItems()
    {
        return $this->salesItems;
    }

    /**
     * @param SalesItem $salesItems
     */
    public function setSalesItems($salesItems)
    {
        $this->salesItems = $salesItems;
    }

    /**
     * @return TallyConfig
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param TallyConfig $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }


}

