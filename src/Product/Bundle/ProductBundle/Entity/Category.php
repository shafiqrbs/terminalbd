<?php

namespace Product\Bundle\ProductBundle\Entity;

use Appstore\Bundle\InventoryBundle\Entity\Product;
use Appstore\Bundle\InventoryBundle\Entity\ItemAttribute;
use Appstore\Bundle\InventoryBundle\Entity\ItemSize;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseItem;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseVendorItem;
use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\ContentBundle\Entity\MallConnect;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Category
 *
 * @Gedmo\Tree(type="materializedPath")
 * @ORM\Table(name="categories")
 * @ORM\Entity(repositoryClass="Product\Bundle\ProductBundle\Entity\CategoryRepository")
 */
class Category
{
    /**
     * @var integer
     *
     * @Gedmo\TreePathSource
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\InventoryConfig", inversedBy="categories")
     **/
    protected $inventoryConfig;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\StockItem", mappedBy="category" )
     **/
    protected $stockItems;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Product", mappedBy="category" )
     **/
    protected $masterProducts;


    /**
     * @ORM\ManyToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\ItemSize", mappedBy="category" )
     **/
    protected $size;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\ItemAttribute", mappedBy="category" )
     * @ORM\OrderBy({"name" = "ASC"})
     **/
    protected $itemAttributes;

    /**
     * @ORM\ManyToMany(targetEntity="Product\Bundle\ProductBundle\Entity\Product", mappedBy="categories" )
     **/
    protected $products;

    /**
     * @ORM\ManyToMany(targetEntity="Setting\Bundle\ContentBundle\Entity\MallConnect", mappedBy="categories" )
     **/
    protected $mallConnects;


    /**
     * @ORM\OneToMany(targetEntity="Product\Bundle\ProductBundle\Entity\Product", mappedBy="parentCategory" )
     **/
    protected $parentProducts;

    /**
     * @ORM\ManyToMany(targetEntity="Product\Bundle\ProductBundle\Entity\CategoryGrouping", mappedBy="categories" )
     **/
    protected $categoryGrouping;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\EcommerceBundle\Entity\Template", mappedBy="category" )
     **/
    protected $template;

    /**
     * @ORM\ManyToMany(targetEntity="Setting\Bundle\ToolBundle\Entity\Branding", mappedBy="categories" )
     **/
    protected $branding;

    /**
     * @ORM\ManyToMany(targetEntity="Setting\Bundle\AppearanceBundle\Entity\MegaMenu", mappedBy="categories" )
     **/
    protected $megaMenu;


    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @Gedmo\Slug(handlers={
     *      @Gedmo\SlugHandler(class="Gedmo\Sluggable\Handler\TreeSlugHandler", options={
     *          @Gedmo\SlugHandlerOption(name="parentRelationField", value="parent"),
     *          @Gedmo\SlugHandlerOption(name="separator", value="-")
     *      })
     * }, fields={"name"})
     * @Doctrine\ORM\Mapping\Column(length=255, unique=true)
     */
    private $slug;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="children")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parent", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     * })
     */
    private $parent;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="level", type="integer", nullable=true)
     */
    private $level;

    /**
     * @ORM\OneToMany(targetEntity="Category" , mappedBy="parent")
     * @ORM\OrderBy({"name" = "ASC"})
     **/
    private $children;

    /**
     * @Gedmo\TreePath(separator="/")
     * @ORM\Column(name="path", type="string", length=3000, nullable=true)
     */
    private $path;


    /**
     * @var int
     *
     * @ORM\Column(name="sorting", type="smallint")
     */
    private $sorting = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="permission", type="string")
     */
    private $permission = 'public';

    /**
     * @var boolean
     *
     * @ORM\Column(name="feature", type="boolean")
     */
    private $feature = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status = true;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $imagePath;

    /**
     * @Assert\File(maxSize="8388608")
     */
    protected $file;



    public function setId($id){
        $this->id = $id;

        return $this;
    }

    /**
     * Get id

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
     * @return Category
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
     * @return Category
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
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param mixed $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return mixed
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return mixed
     */
    public function getProducts()
    {
        return $this->products;
    }

    public function getParentSlug() {

        if($this->getParent() == null) {
            return "";
        }

        $this->getParent()->getSlug();
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
     * @return $this
     */
    public function setSorting($sorting)
    {
        $this->sorting = $sorting;

        return $this;
    }

    /**
     * @return int
     */
    public function isFeature()
    {
        return $this->feature;
    }

    /**
     * @param boolean $feature
     */
    public function setFeature($feature)
    {
        $this->feature = $feature;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @return mixed
     */
    public function getBranding()
    {
        return $this->branding;
    }

    public function getNestedLabel()
    {
        if($this->getLevel() > 1) {
            return $this->formatLabel($this->getLevel() - 1, $this->getName());
        }else{
            return $this->getName();
        }
    }

    public function getParentIdByLevel($level = 1)
    {
        $parentsIds = explode("/", $this->getPath());

        return isset($parentsIds[$level - 1]) ? $parentsIds[$level - 1] : null;

    }

    private function formatLabel($level, $value) {
        return str_repeat("-", $level * 3) . str_repeat(">", $level) . $value;
    }

    /**
     * Sets file.
     *
     * @param Page $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return Page
     */
    public function getFile()
    {
        return $this->file;
    }



    public function getAbsolutePath()
    {
        return null === $this->imagePath
            ? null
            : $this->getUploadRootDir().'/'.$this->imagePath;
    }

    public function getWebPath()
    {
        return null === $this->imagePath
            ? null
            : $this->getUploadDir().'/'.$this->imagePath;
    }

    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return 'uploads/files/category';
    }

    public function upload()
    {
        // the file property can be empty if the field is not required
        if (null === $this->getFile()) {
            return;
        }

        // use the original file name here but you should
        // sanitize it at least to avoid any security issues

        // move takes the target directory and then the
        // target filename to move to
        $this->getFile()->move(
            $this->getUploadRootDir(),
            $this->getFile()->getClientOriginalName()
        );

        // set the path property to the filename where you've saved the file
        $this->imagePath = $this->getFile()->getClientOriginalName();

        // clean up the file property as you won't need it anymore
        $this->file = null;
    }

    /**
     * @return mixed
     */
    public function getParentProducts()
    {
        return $this->parentProducts;
    }


    /**
     * @return mixed
     */
    public function getInventoryConfig()
    {
        return $this->inventoryConfig;
    }


    /**
     * @return mixed
     */
    public function getStockItems()
    {
        return $this->stockItems;
    }

    /**
     * @return int
     */
    public function getPermission()
    {
        return $this->permission;
    }

    /**
     * @param int $permission
     * public for all
     * private for only specific domain
     */
    public function setPermission($permission)
    {
        $this->permission = $permission;
    }

    /**
     * @return ItemAttribute
     */
    public function getItemAttributes()
    {
        return $this->itemAttributes;
    }

    /**
     * @return MallConnect
     */
    public function getMallConnects()
    {
        return $this->mallConnects;
    }

    /**
     * @return ItemSize
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @return Product
     */
    public function getMasterProducts()
    {
        return $this->masterProducts;
    }

}
