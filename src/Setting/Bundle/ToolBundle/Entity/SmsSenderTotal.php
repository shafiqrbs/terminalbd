<?php

namespace Setting\Bundle\ToolBundle\Entity;
use Doctrine\ORM\Mapping as ORM;


/**
 * SmsSenderTotal
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Setting\Bundle\ToolBundle\Repository\SmsSenderTotalRepository")
 */
class SmsSenderTotal
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
     * @ORM\OneToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="smsSenderTotal")
     **/
    protected $globalOption;

    /**
     * @var float
     *
     * @ORM\Column(name="purchase", type="float", nullable= true)
     */
    private $purchase;

    /**
     * @var string
     *
     * @ORM\Column(name="sending", type="float", nullable= true)
     */
    private $sending;

    /**
     * @var string
     *
     * @ORM\Column(name="remaining", type="float", nullable= true)
     */
    private $remaining;


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
     * @return float
     */
    public function getPurchase()
    {
        return $this->purchase;
    }

    /**
     * @param float $purchase
     */
    public function setPurchase($purchase)
    {
        $this->purchase = $purchase;
    }

    /**
     * @return string
     */
    public function getSending()
    {
        return $this->sending;
    }

    /**
     * @param string $sending
     */
    public function setSending($sending)
    {
        $this->sending = $sending;
    }

    /**
     * @return string
     */
    public function getRemaining()
    {
        return $this->remaining;
    }

    /**
     * @param string $remaining
     */
    public function setRemaining($remaining)
    {
        $this->remaining = $remaining;
    }


}

