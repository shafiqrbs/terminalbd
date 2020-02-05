<?php

namespace Setting\Bundle\AppearanceBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Gregwar\Image\Image;
use Setting\Bundle\AppearanceBundle\Entity\FeatureWidget;
use Setting\Bundle\AppearanceBundle\Entity\FeatureWidgetItem;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * FeatureWidgetRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FeatureWidgetRepository extends EntityRepository
{
    public function setListOrdering($data)
    {

        $i = 1;
        $em = $this->_em;
        $qb = $em->createQueryBuilder();

        foreach ($data as $key => $value){
            $qb->update('SettingAppearanceBundle:FeatureWidget', 'mg')
                ->set('mg.sorting', $i)
                ->where('mg.id = :id')
                ->setParameter('id', $value)
                ->getQuery()
                ->execute();
            $i++;
        }


    }

    public function setDivOrdering($data)
    {

        $i = 1;
        $em = $this->_em;
        $qb = $em->createQueryBuilder();

        foreach ($data as $key => $value){

            $qb->update('SettingAppearanceBundle:FeatureWidgetItem', 'mg')
                ->set('mg.sorting', $i)
                ->where('mg.id = :id')
                ->setParameter('id', $value)
                ->getQuery()
                ->execute();
            $i++;

        }


    }

    public function setDivResize($data)
    {

        $em = $this->_em;
        $width = $data['width'];
        if($width > 100 || $width > 96 ){
            $width = 100;
        }
        $qb = $em->createQueryBuilder();
        $qb->update('SettingAppearanceBundle:FeatureWidgetItem', 'mg')
            ->set('mg.divWidth',$width)
            ->set('mg.divHeight', $data['height'])
            ->where('mg.id = :id')
            ->setParameter('id', $data['id'])
            ->getQuery()
            ->execute();
    }

    public function getFeatureWidget(GlobalOption $option,$menu = "" )
    {
        $em = $this->_em;

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.menu', 'm');
        $qb->where('e.globalOption = :option');
        $qb->setParameter('option', $option);
        $qb->andWhere('e.position = :position');
        $qb->setParameter('position', "mobile");
        $qb->andWhere('m.menu = :menu');
        $qb->setParameter('menu', $menu);
        $result = $qb->getQuery()->getOneOrNullResult();
        return $result;

    }

    public function getFeatureSlider(FeatureWidget $feature)
    {

        $data = array();

        /* @var $row FeatureWidgetItem */

        $items = $feature->getFeatureWidgetItems();

        if ($items) {

            foreach ($items as $key => $row) {


                $url = "/android-api-ecommerce/home";
                if ($row->getFeature()->getTargetTo() == "Category") {
                    $url = "/android-api-ecommerce/product-search?category={$row->getFeature()->getCategory()->getId()}";
                } elseif ($row->getFeature()->getTargetTo() == "Promotion") {
                    $url = "/android-api-ecommerce/product-search?promotion={$row->getFeature()->getPromotion()->getId()}";
                } elseif ($row->getFeature()->getTargetTo() == "Tag") {
                    $url = "/android-api-ecommerce/product-search?tag={$row->getFeature()->getTag()->getId()}";
                } elseif ($row->getFeature()->getTargetTo() == "Discount") {
                    $url = "/android-api-ecommerce/product-search?discount={$row->getFeature()->getDiscount()->getId()}";
                } elseif ($row->getFeature()->getTargetTo() == "Brand") {
                    $url = "/android-api-ecommerce/product-search?brand={$row->getFeature()->getBrand()->getId()}";
                }

                $data[$key]['id'] = (int)$row->getId();
                $data[$key]['name'] = $row->getFeature()->getName();
                $data[$key]['url'] = $url;
                if ( $row->getFeature()->getPath()) {
                    $path = $this->resizeFilter("uploads/domain/{$feature->getGlobalOption()->getId()}/content/{$row->getFeature()->getPath()}");
                    $data[$key]['imagePath'] = $path;
                } else {
                    $data[$key]['imagePath'] = "";
                }
            }
        }
        return $data;
    }

    public function resizeFilter($pathToImage, $width = 720, $height = 720)
    {
        $path = '/' . Image::open(__DIR__.'/../../../../../web/' . $pathToImage)->cropResize($width, $height, 'transparent', 'top', 'left')->guess();
        return $_SERVER['HTTP_HOST'].$path;
    }

}
