<?php

namespace Setting\Bundle\AppearanceBundle\Entity;

use Appstore\Bundle\EcommerceBundle\Entity\Discount;
use Appstore\Bundle\EcommerceBundle\Entity\Promotion;
use Appstore\Bundle\InventoryBundle\Entity\ItemBrand;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Product\Bundle\ProductBundle\Entity\Category;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;


/**
 * EcommerceMenu
 *
 * @ORM\Table(name="ecommoerce_menu")
 * @ORM\Entity(repositoryClass="Setting\Bundle\AppearanceBundle\Entity\EcommerceMenuRepository")
 */
class EcommerceMenu
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @Doctrine\ORM\Mapping\Column(length=255, unique = true, nullable = true )
     */
    private $slug;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status = true;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="ecommerceMenus")
     **/

    private $globalOption;

    /**
     * @ORM\ManyToMany(targetEntity="Product\Bundle\ProductBundle\Entity\Category", inversedBy="ecommerceMenus")
     **/

    private $categories;

    /**
     * @ORM\ManyToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\Promotion", inversedBy="ecommerceMenuPromotion" )
     **/
    private  $promotions;

    /**
     * @ORM\ManyToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\Promotion", inversedBy="ecommerceMenuTag")
     * @ORM\JoinTable(name="ecommerce_tag",
     *      joinColumns={@ORM\JoinColumn(name="ecommerce_id", referencedColumnName="id",onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id")}
     * )
     */
    private  $tags;

    /**
     * @ORM\ManyToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\Discount", inversedBy="ecommerceMenu" )
     **/
    private  $discounts;

    /**
     * @ORM\ManyToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\ItemBrand", inversedBy="ecommerceMenu")
     */
    protected $brands;

    /**
     * @var smallint
     *
     * @ORM\Column(name="sorting", type="smallint" , nullable=true)
     */
    private $sorting;


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
     * @return EcommerceMenu
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
     * Set slug
     *
     * @param string $slug
     * @return EcommerceMenu
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set status
     *
     * @param boolean $status
     * @return EcommerceMenu
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
     * @return Category
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param Category $categories
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
    }

    /**
     * @return \Setting\Bundle\AppearanceBundle\Entity\smallint
     */
    public function getSorting()
    {
        return $this->sorting;
    }

    /**
     * @param \Setting\Bundle\AppearanceBundle\Entity\smallint $sorting
     */
    public function setSorting($sorting)
    {
        $this->sorting = $sorting;
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
     * @return Promotion
     */
    public function getPromotions()
    {
        return $this->promotions;
    }

    /**
     * @param Promotion $promotions
     */
    public function setPromotions($promotions)
    {
        $this->promotions = $promotions;
    }

    /**
     * @return Promotion
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param Promotion $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * @return Discount
     */
    public function getDiscounts()
    {
        return $this->discounts;
    }

    /**
     * @param Discount $discounts
     */
    public function setDiscounts($discounts)
    {
        $this->discounts = $discounts;
    }

    /**
     * @return ItemBrand
     */
    public function getBrands()
    {
        return $this->brands;
    }

    /**
     * @param ItemBrand $brands
     */
    public function setBrands($brands)
    {
        $this->brands = $brands;
    }


}
