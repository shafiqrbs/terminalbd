<?php

namespace Syndicate\Bundle\ComponentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AcademicMeta
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Syndicate\Bundle\ComponentBundle\Repository\AcademicMetaRepository")
 */
class AcademicMeta
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
     * @ORM\ManyToOne(targetEntity="Syndicate\Bundle\ComponentBundle\Entity\Tutor", inversedBy="academicMeta" )
     **/

    protected $tutor;

    /**
     * @var string
     *
     * @ORM\Column(name="metaKey", type="string", length=255, nullable=true)
     */
    private $metaKey;

    /**
     * @var string
     *
     * @ORM\Column(name="metaValue", type="text", nullable=true)
     */
    private $metaValue;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status = true ;


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
     * Set metaKey
     *
     * @param string $metaKey
     *
     * @return AcademicMeta
     */
    public function setMetaKey($metaKey)
    {
        $this->metaKey = $metaKey;

        return $this;
    }

    /**
     * Get metaKey
     *
     * @return string
     */
    public function getMetaKey()
    {
        return $this->metaKey;
    }

    /**
     * Set metaValue
     *
     * @param string $metaValue
     *
     * @return AcademicMeta
     */
    public function setMetaValue($metaValue)
    {
        $this->metaValue = $metaValue;

        return $this;
    }

    /**
     * Get metaValue
     *
     * @return string
     */
    public function getMetaValue()
    {
        return $this->metaValue;
    }



    /**
     * @return mixed
     */
    public function getTutor()
    {
        return $this->tutor;
    }

    /**
     * @param mixed $tutor
     */
    public function setTutor($tutor)
    {
        $this->tutor = $tutor;
    }

    /**
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param boolean $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }
}

