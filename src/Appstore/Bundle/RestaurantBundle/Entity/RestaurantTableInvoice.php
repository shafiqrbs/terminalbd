<?php

namespace Appstore\Bundle\RestaurantBundle\Entity;

use Core\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * RestaurantTemporarySales
 *
 * @ORM\Table( name = "restaurant_table_invoice")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\RestaurantBundle\Repository\RestaurantTableInvoiceRepository")
 */
class RestaurantTableInvoice
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\RestaurantBundle\Entity\RestaurantConfig", inversedBy="restaurantTemp" , cascade={"detach","merge"} )
     **/
    private  $restaurantConfig;

    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="restaurantTemps")
     **/
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="invoiceSalesBy" )
     **/
    private  $orderBy;


    /**
     * @var string
     *
     * @ORM\Column(name="process", type="string", length=50, nullable=true)
     */
    private $process ='Created';

    /**
     * @var array
     *
     * @ORM\Column(name="serveBy", type="json_array", length=50, nullable=true)
     */
    private $serveBy;

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
     * @var float
     *
     * @ORM\Column(name="subTotal", type="float")
     */
    private $subTotal;



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
     * @return float
     */
    public function getSubTotal()
    {
        return $this->subTotal;
    }

    /**
     * @param float $subTotal
     */
    public function setSubTotal($subTotal)
    {
        $this->subTotal = $subTotal;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
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



    /**
     * @return array
     */
    public function getServeBy()
    {
        return $this->serveBy;
    }

    /**
     * @param array $serveBy
     */
    public function setServeBy($serveBy)
    {
        $this->serveBy = $serveBy;
    }
}

