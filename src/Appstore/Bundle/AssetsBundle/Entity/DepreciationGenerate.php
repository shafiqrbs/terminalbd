<?php

namespace Appstore\Bundle\AssetsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Product\Bundle\ProductBundle\Entity\Category;

/**
 * DepreciationGenerate
 *
 * @ORM\Table("assets_depreciation_generate")
 * @ORM\Entity()
 */
class DepreciationGenerate
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }



}

