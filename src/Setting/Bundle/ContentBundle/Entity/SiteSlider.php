<?php

namespace Setting\Bundle\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 *
 * @ORM\Table()
 * @ORM\Entity()
 * @Gedmo\Uploadable(pathMethod="getUploadRootDir", allowOverwrite=false, appendNumber=true)
 */
class SiteSlider
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
     * @var string
     *
     * @ORM\Column(name="businessSector", type="string", length=255)
     */
    private $businessSector;


    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text" , nullable = true)
     */
    private $content;


    /**
     * @var string
     * @Gedmo\UploadableFileName
     * @ORM\Column(name="path", type="string", length=255, nullable=true)
     */
    private $path;

    /**
     * @Assert\File(maxSize="8388608")
     */
    protected $file;

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
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }



    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir(). DIRECTORY_SEPARATOR . $this->path;
    }

    protected function getUploadRootDir()
    {
        return WEB_PATH . DIRECTORY_SEPARATOR . $this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return 'uploads/files/slider';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
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

    /**
     * @return string
     */
    public function getBusinessSector()
    {
        return $this->businessSector;
    }

    /**
     * @param string $businessSector
     */
    public function setBusinessSector($businessSector)
    {
        $this->businessSector = $businessSector;
    }
}
