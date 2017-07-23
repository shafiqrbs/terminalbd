<?php

namespace Setting\Bundle\AppearanceBundle\Entity;

use Appstore\Bundle\EcommerceBundle\Entity\Discount;
use Appstore\Bundle\EcommerceBundle\Entity\Promotion;
use Appstore\Bundle\InventoryBundle\Entity\ItemBrand;
use Doctrine\ORM\Mapping as ORM;
use Product\Bundle\ProductBundle\Entity\Category;
use Setting\Bundle\ContentBundle\Entity\Page;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Setting\Bundle\ToolBundle\Entity\Module;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * FeatureWidget
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Setting\Bundle\AppearanceBundle\Repository\FeatureWidgetRepository")
 */
class FeatureWidget
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
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="featureWidgets" )
     **/
    private  $globalOption;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\Module", inversedBy="featureWidgets" )
     **/
    private  $module;

    /**
     * @ORM\ManyToMany(targetEntity="Setting\Bundle\ContentBundle\Entity\Page", inversedBy="featureWidgets")
     **/
    private $page;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\AppearanceBundle\Entity\JsFeature", inversedBy="featureWidgets" )
     **/
    private  $jsFeature;

    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\AppearanceBundle\Entity\FeatureWidgetItem", mappedBy="featureWidget" , cascade={"remove"})
     * @ORM\OrderBy({"sorting" = "ASC"})
     **/
    private  $featureWidgetItems;

    /**
     * @ORM\ManyToOne(targetEntity="Product\Bundle\ProductBundle\Entity\Category", inversedBy="featureWidgets" )
     **/
    private  $category;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\Promotion", inversedBy="featureWidgetPromotions" )
     **/
    private  $promotion;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\Promotion", inversedBy="featureWidgetTags" )
     **/
    private  $tag;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\Discount", inversedBy="featureWidgets" )
     **/
    private  $discount;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\ItemBrand", inversedBy="featureWidgets")
     */
    protected $brand;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\AppearanceBundle\Entity\Menu", inversedBy="featureWidgets")
     */
    protected $menu;


    /**
     * @var smallint
     *
     * @ORM\Column(name="sorting", type="smallint" , nullable=true)
     */
    private $sorting;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255 , nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="position", type="string", length=50 , nullable=true)
     */
    private $position;

    /**
     * @var string
     *
     * @ORM\Column(name="sliderFeature", type="string", length=30 , nullable=true)
     */
    private $sliderFeature;

    /**
     * @var string
     *
     * @ORM\Column(name="sliderFeaturePosition", type="string", length=10 , nullable=true)
     */
    private $sliderFeaturePosition;


    /**
     * @var string
     *
     * @ORM\Column(name="pageName", type="string", length = 255 , nullable=true)
     */
    private $pageName;


    /**
     * @var string
     *
     * @ORM\Column(name="widgetFor", type="string", length=50 , nullable=true)
     */
    private $widgetFor;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text" , nullable=true)
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(name="featureFor", type="string",nullable=true)
     */
    private $featureFor;

    /**
     * @var string
     *
     * @ORM\Column(name="pageShowType", type="string",length=15 ,nullable=true)
     */
    private $pageShowType;

    /**
     * @var string
     *
     * @ORM\Column(name="pageFeatureName", type="string", length=255 , nullable=true)
     */
    private $pageFeatureName;

    /**
     * @var string
     *
     * @ORM\Column(name="moduleFeatureName", type="string", length=255 , nullable=true)
     */
    private $moduleFeatureName;

     /**
     * @var string
     *
     * @ORM\Column(name="moduleShowType", type="string",length=15 ,nullable=true)
     */
    private $moduleShowType;

    /**
     * @var string
     *
     * @ORM\Column(name="moduleShowLimit", type="string",length=15 ,nullable=true)
     */
    private $moduleShowLimit;


    /**
     * @var boolean
     *
     * @ORM\Column(name="featureCategory", type="boolean")
     */
    private $featureCategory = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="featureBrand", type="boolean")
     */
    private $featureBrand = false;


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
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status = true;


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
     * @return FeatureWidget
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
     * Set content
     *
     * @param string $content
     * @return FeatureWidget
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set status
     *
     * @param boolean $status
     * @return FeatureWidget
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean 
     */
    public function getStatus()
    {
        return $this->status;
    }


    /**
     * @return datetime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param datetime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }


    /**
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param \DateTime $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /**
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param string $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
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
     * @return FeatureWidgetItem
     */
    public function getFeatureWidgetItems()
    {
        return $this->featureWidgetItems;
    }

    /**
     * @param FeatureWidgetItem $featureWidgetItems
     */
    public function setFeatureWidgetItems($featureWidgetItems)
    {
        $this->featureWidgetItems = $featureWidgetItems;
    }

    /**
     * @return string
     */
    public function getFeatureFor()
    {
        return $this->featureFor;
    }

    /**
     * @param string $featureFor
     */
    public function setFeatureFor($featureFor)
    {
        $this->featureFor = $featureFor;
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
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Category $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return mixed
     */
    public function getPromotion()
    {
        return $this->promotion;
    }

    /**
     * @param Promotion $promotion
     */
    public function setPromotion($promotion)
    {
        $this->promotion = $promotion;
    }

    /**
     * @return Promotion
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param Promotion $tag
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
    }

    /**
     * @return Discount
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @param mixed $discount
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;
    }

    /**
     * @return JsFeature
     */
    public function getJsFeature()
    {
        return $this->jsFeature;
    }

    /**
     * @param JsFeature $jsFeature
     */
    public function setJsFeature($jsFeature)
    {
        $this->jsFeature = $jsFeature;
    }

    /**
     * @return smallint
     */
    public function getSorting()
    {
        return $this->sorting;
    }

    /**
     * @param smallint $sorting
     */
    public function setSorting($sorting)
    {
        $this->sorting = $sorting;
    }

    /**
     * @return Module
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @param Module $module
     */
    public function setModule($module)
    {
        $this->module = $module;
    }

    /**
     * @return ItemBrand
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * @param ItemBrand $brand
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;
    }

    /**
     * @return bool
     */
    public function getFeatureCategory()
    {
        return $this->featureCategory;
    }

    /**
     * @param bool $featureCategory
     */
    public function setFeatureCategory($featureCategory)
    {
        $this->featureCategory = $featureCategory;
    }

    /**
     * @return bool
     */
    public function getFeatureBrand()
    {
        return $this->featureBrand;
    }

    /**
     * @param bool $featureBrand
     */
    public function setFeatureBrand($featureBrand)
    {
        $this->featureBrand = $featureBrand;
    }

    /**
     * @return string
     */
    public function getPageName()
    {
        return $this->pageName;
    }

    /**
     * @param string $pageName
     * Home
     * Brand
     * Promotion
     * Category
     * Tag
     * Discount
     */
    public function setPageName($pageName)
    {
        $this->pageName = $pageName;
    }

    /**
     * @return string
     */
    public function getSliderFeature()
    {
        return $this->sliderFeature;
    }

    /**
     * @param string $sliderFeature
     */
    public function setSliderFeature($sliderFeature)
    {
        $this->sliderFeature = $sliderFeature;
    }

    /**
     * @return string
     */
    public function getSliderFeaturePosition()
    {
        return $this->sliderFeaturePosition;
    }

    /**
     * @param string $sliderFeaturePosition
     */
    public function setSliderFeaturePosition($sliderFeaturePosition)
    {
        $this->sliderFeaturePosition = $sliderFeaturePosition;
    }

    /**
     * @return Menu
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * @param Menu $menu
     */
    public function setMenu($menu)
    {
        $this->menu = $menu;
    }

    /**
     * @return string
     */
    public function getWidgetFor()
    {
        return $this->widgetFor;
    }

    /**
     * @param string $widgetFor
     * website
     * e-commerce
     * institute
     */
    public function setWidgetFor($widgetFor)
    {
        $this->widgetFor = $widgetFor;
    }

    /**
     * @return string
     */
    public function getPageShowType()
    {
        return $this->pageShowType;
    }

    /**
     * @param string $pageShowType
     */
    public function setPageShowType($pageShowType)
    {
        $this->pageShowType = $pageShowType;
    }

    /**
     * @return string
     */
    public function getModuleShowType()
    {
        return $this->moduleShowType;
    }

    /**
     * @param string $moduleShowType
     */
    public function setModuleShowType($moduleShowType)
    {
        $this->moduleShowType = $moduleShowType;
    }

    /**
     * @return string
     */
    public function getModuleShowLimit()
    {
        return $this->moduleShowLimit;
    }

    /**
     * @param string $moduleShowLimit
     */
    public function setModuleShowLimit($moduleShowLimit)
    {
        $this->moduleShowLimit = $moduleShowLimit;
    }

    /**
     * @return string
     */
    public function getPageFeatureName()
    {
        return $this->pageFeatureName;
    }

    /**
     * @param string $pageFeatureName
     */
    public function setPageFeatureName($pageFeatureName)
    {
        $this->pageFeatureName = $pageFeatureName;
    }

    /**
     * @return string
     */
    public function getModuleFeatureName()
    {
        return $this->moduleFeatureName;
    }

    /**
     * @param string $moduleFeatureName
     */
    public function setModuleFeatureName($moduleFeatureName)
    {
        $this->moduleFeatureName = $moduleFeatureName;
    }
}
