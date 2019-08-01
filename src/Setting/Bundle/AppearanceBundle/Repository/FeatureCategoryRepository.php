<?php

namespace Setting\Bundle\AppearanceBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Gregwar\Image\Image;
use Setting\Bundle\AppearanceBundle\Entity\FeatureCategory;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * FeatureCategoryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FeatureCategoryRepository extends EntityRepository
{



    public function getSliderFeatureCategory(GlobalOption $globalOption , $limit = 10){

        $qb = $this->createQueryBuilder('e');
        $qb->where("e.globalOption = :option");
        $qb->setParameter('option', $globalOption->getId());
        $qb->orderBy('e.id','DESC');
        $qb->setMaxResults($limit);
        $sql = $qb->getQuery();
        $result = $sql->getResult();
        return  $result;

    }

    public function getApiFeature(GlobalOption $option, $limit = 10)
    {

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.category','category');
        $qb->select('e.path as path');
        $qb->addSelect('category.id AS category_id','category.name AS name');
        $qb->where("e.globalOption = :option");
        $qb->setParameter('option', $option->getId());
        $qb->orderBy('e.id','DESC');
        $qb->setMaxResults($limit);
        $sql = $qb->getQuery();
        $result = $sql->getArrayResult();


        $data = array();

        /* @var $row FeatureCategory */

        foreach($result as $key => $row) {

            $data[$key]['category_id']    = (int) $row['category_id'];
            $data[$key]['name']           = $row['name'];
            if($row['path']){
                $path = $this->resizeFilter("uploads/domain/{$option->getId()}/feature-category/{$row['path']}");
                $data[$key]['imagePath']            =  $path;
            }else{
                $data[$key]['imagePath']            = "";
            }
        }
        return $data;
    }

    public function resizeFilter($pathToImage, $width = 256, $height = 256)
    {
        $path = '/' . Image::open(__DIR__.'/../../../../../web/' . $pathToImage)->cropResize($width, $height, 'transparent', 'top', 'left')->guess();
        return $_SERVER['HTTP_HOST'].$path;
    }

}
