<?php

namespace Appstore\Bundle\TallyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * PageMeta
 *
 * @ORM\Table(name="tally_category_meta")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\TallyBundle\Repository\CategoryMetaRepository")
 */
class CategoryMeta
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\TallyBundle\Entity\Category", inversedBy="categoryMetas" )
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=true, onDelete="cascade")
     **/
    private  $category;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\TallyBundle\Entity\ItemMetaAttribute", mappedBy="categoryMeta" )
     **/
    protected $itemMetaAttributes;

    /**
     * @var string
     *
     * @ORM\Column(name="metaLang", type="string", nullable= true)
     */
    private $metaLang;

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
     * @return string
     */
    public function getMetaLang()
    {
        return $this->metaLang;
    }

    /**
     * @param string $metaLang
     */
    public function setMetaLang($metaLang)
    {
        $this->metaLang = $metaLang;
    }

    /**
     * @return AssetsCategory
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param AssetsCategory $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

}
