<?php

namespace Appstore\Bundle\RestaurantBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * RestaurantCategory
 *
 * @ORM\Table( name ="restaurant_product_category")
 * @ORM\Entity(repositoryClass="")
 */
class Category
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\RestaurantBundle\Entity\RestaurantConfig", inversedBy="categories")
     **/
    private $restaurantConfig;



    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\RestaurantBundle\Entity\Particular", mappedBy="category")
     **/
    private $particulars;


    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=true)
     */
    private $name;

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
     * @var int
     *
     * @ORM\Column(name="sorting", type="smallint",  length=2, nullable=true)
     */
    private $sorting = 0;

    /**
     * @var boolean
     *
     * @ORM\Column(name="hasQuantity", type="boolean" )
     */
    private $hasQuantity = false;

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
     * @return Category
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param Category $name
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
     * @return bool
     */
    public function getHasQuantity()
    {
        return $this->hasQuantity;
    }

    /**
     * @param bool $hasQuantity
     */
    public function setHasQuantity($hasQuantity)
    {
        $this->hasQuantity = $hasQuantity;
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
     * @return Particular
     */
    public function getParticulars()
    {
        return $this->particulars;
    }

    /**
     * @return RestaurantConfig
     */
    public function getRestaurantConfig()
    {
        return $this->restaurantConfig;
    }

    /**
     * @param RestaurantConfig $restaurantConfig
     */
    public function setRestaurantConfig($restaurantConfig)
    {
        $this->restaurantConfig = $restaurantConfig;
    }


}

