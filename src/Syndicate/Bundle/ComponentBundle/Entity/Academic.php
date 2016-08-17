<?php

namespace Syndicate\Bundle\ComponentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Academic
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Syndicate\Bundle\ComponentBundle\Repository\AcademicRepository")
 */
class Academic
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
     * @ORM\ManyToOne(targetEntity="Syndicate\Bundle\ComponentBundle\Entity\Tutor", inversedBy="academics" )
     **/

    protected $tutor;


    /**
     * @var string
     *
     * @ORM\Column(name="degree", type="string", length=255, nullable=true)
     */
    private $degree;

    /**
     * @var string
     *
     * @ORM\Column(name="course", type="string", length=255, nullable=true)
     */
    private $course;

    /**
     * @var string
     *
     * @ORM\Column(name="passingYear", type="string", length=255, nullable=true)
     */
    private $passingYear;

    /**
     * @var string
     *
     * @ORM\Column(name="result", type="string", length=255, nullable=true)
     */
    private $result;

    /**
     * @var string
     *
     * @ORM\Column(name="institute", type="string", length=255, nullable=true)
     */
    private $institute;

    /**
     * @var string
     *
     * @ORM\Column(name="remark", type="string", length=255, nullable=true)
     */
    private $remark;


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
     * Set degree
     *
     * @param string $degree
     *
     * @return Academic
     */
    public function setDegree($degree)
    {
        $this->degree = $degree;

        return $this;
    }

    /**
     * Get degree
     *
     * @return string
     */
    public function getDegree()
    {
        return $this->degree;
    }

    /**
     * Set course
     *
     * @param string $course
     *
     * @return Academic
     */
    public function setCourse($course)
    {
        $this->course = $course;

        return $this;
    }

    /**
     * Get course
     *
     * @return string
     */
    public function getCourse()
    {
        return $this->course;
    }

    /**
     * Set passingYear
     *
     * @param string $passingYear
     *
     * @return Academic
     */
    public function setPassingYear($passingYear)
    {
        $this->passingYear = $passingYear;

        return $this;
    }

    /**
     * Get passingYear
     *
     * @return string
     */
    public function getPassingYear()
    {
        return $this->passingYear;
    }

    /**
     * Set result
     *
     * @param string $result
     *
     * @return Academic
     */
    public function setResult($result)
    {
        $this->result = $result;

        return $this;
    }

    /**
     * Get result
     *
     * @return string
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Set institute
     *
     * @param string $institute
     *
     * @return Academic
     */
    public function setInstitute($institute)
    {
        $this->institute = $institute;

        return $this;
    }

    /**
     * Get institute
     *
     * @return string
     */
    public function getInstitute()
    {
        return $this->institute;
    }

    /**
     * Set remark
     *
     * @param string $remark
     *
     * @return Academic
     */
    public function setRemark($remark)
    {
        $this->remark = $remark;

        return $this;
    }

    /**
     * Get remark
     *
     * @return string
     */
    public function getRemark()
    {
        return $this->remark;
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
}

