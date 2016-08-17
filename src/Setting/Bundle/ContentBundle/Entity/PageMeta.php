<?php

namespace Setting\Bundle\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\ToolBundle\Entity\Module;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * PageMeta
 *
 * @ORM\Table(name="PageMeta")
 * @ORM\Entity(repositoryClass="Setting\Bundle\ContentBundle\Repository\PageMetaRepository")
 */
class PageMeta
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
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ContentBundle\Entity\Page", inversedBy="pageMetas" )
     **/
    protected $page;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ContentBundle\Entity\Portfolio", inversedBy="pageMetas" )
     **/
    protected $portfolio;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ContentBundle\Entity\Service", inversedBy="pageMetas" )
     **/
    protected $service;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ContentBundle\Entity\TradeItem", inversedBy="pageMetas" )
     **/
    protected $tradeItem;

    /**
     * @var string
     *
     * @ORM\Column(name="metaKey", type="string", nullable= true)
     */
    private $metaKey;

    /**
     * @var string
     *
     * @ORM\Column(name="metaValue", type="string", nullable= true)
     */
    private $metaValue;



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
     * @return Page
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param Page $page
     */
    public function setPage($page)
    {
        $this->page = $page;
    }


    /**
     * @param int $showLimit
     */
    public function setShowLimit($showLimit)
    {
        $this->showLimit = $showLimit;
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
     * @return mixed
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param mixed $service
     */
    public function setService($service)
    {
        $this->service = $service;
    }

    /**
     * @return mixed
     */
    public function getPortfolio()
    {
        return $this->portfolio;
    }

    /**
     * @param mixed $portfolio
     */
    public function setPortfolio($portfolio)
    {
        $this->portfolio = $portfolio;
    }

    /**
     * @return mixed
     */
    public function getTradeItem()
    {
        return $this->tradeItem;
    }

    /**
     * @param mixed $tradeItem
     */
    public function setTradeItem($tradeItem)
    {
        $this->tradeItem = $tradeItem;
    }


}
