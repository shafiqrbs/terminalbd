<?php

namespace Appstore\Bundle\InventoryBundle\EventListener;

use Appstore\Bundle\InventoryBundle\Entity\Item;
use Doctrine\Common\Util\Debug;
use Doctrine\ORM\Event\LifecycleEventArgs;

class ItemListener
{
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->createCode($args);
    }

    public function createCode(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        // perhaps you only want to act on some "Purchase" entity
        if ($entity instanceof Item) {

            $lastCode = $this->getLastCode($args,$entity);
            $entity->setCode((int)$lastCode+1);
            $codes = $this->getItemCodes($entity);
            $entity->setName($codes['name']);
            $entity->setSku($codes['sku']);
            $entity->setSkuSlug($codes['skuSlug']);
            $entity->setSkuWebSlug($codes['skuWeb']);

        }
    }

    /**
     * @param LifecycleEventArgs $args
     * @param $entity
     * @return int|mixed
     */
    public function getLastCode(LifecycleEventArgs $args,Item $entity)
    {

        $entityManager = $args->getEntityManager();
        $qb = $entityManager->getRepository('InventoryBundle:Item')->createQueryBuilder('s');

        $qb
            ->select('MAX(s.code)')
            ->where('s.inventoryConfig = :inventory')
            ->setParameter('inventory', $entity->getInventoryConfig());
            $lastCode = $qb->getQuery()->getSingleScalarResult();

        if (empty($lastCode)) {
            return 0;
        }

        return $lastCode;
    }

    /**
     * @param @entity
     */

    public function getItemCodes(Item $entity){

        /*if(!is_object($entity->getColor())) {
            Debug::dump($entity);
            exit;
        }if(!is_object($entity->getSize())) {
            Debug::dump($entity);
            exit;
        }*/

        $masterItem         = $this->getStrPad($entity->getMasterItem()->getCode(),3);
        $masterSlug         = $entity->getMasterItem()->getSlug();
        $masterName         = $entity->getMasterItem()->getName();



        $color ='';
        $colorSlug ='';
        $colorName ='';

        if(!empty($entity->getInventoryConfig()->getIsColor()) and $entity->getInventoryConfig()->getIsColor() == 1 ){
            $color              = '-C'.$this->getStrPad($entity->getColor()->getCode(),3);
            $colorSlug          = '_'.$entity->getColor()->getSlug();
            $colorName          = '-'.$entity->getColor()->getName();
        }

        $size ='';
        $sizeSlug = '';
        $sizeName = '';

        if(!empty($entity->getInventoryConfig()->getIsSize()) and $entity->getInventoryConfig()->getIsSize() == 1){
            $size               = '-S'.$this->getStrPad($entity->getSize()->getCode(),3);
            $sizeSlug           = '_'.$entity->getSize()->getSlug();
            $sizeName           = '-'.$entity->getSize()->getName();
        }

        $brand ='';
        $brandSlug = '';
        $brandName = '';

        if(!empty($entity->getInventoryConfig()->getIsBrand()) and $entity->getInventoryConfig()->getIsBrand() == 1){
            $brand               = '-B'.$this->getStrPad($entity->getBrand()->getCode(),2);
            $brandSlug           = '_'.$entity->getBrand()->getSlug();
            $brandName           = '-'.$entity->getBrand()->getName();
        }


        $vendor ='';
        $vendorSlug ='';
        $vendorName ='';

        if(!empty($entity->getInventoryConfig()->getIsVendor()) and $entity->getInventoryConfig()->getIsVendor() == 1 ){
            $vendor             = '-V'.$this->getStrPad($entity->getVendor()->getCode(),3);
            $vendorSlug         = '_'.strtolower($entity->getVendor()->getSlug());
            $vendorName         = '-'.$entity->getVendor()->getVendorCode();
        }


        $sku            = $masterItem.$color.$size.$brand.$vendor;
        $name           = $masterName.$colorName.$sizeName.$brandName.$vendorName;
        $skuSlug        = $masterSlug.$colorSlug.$sizeSlug.$brandSlug.$vendorSlug;


        $domainSlug     = $entity->getInventoryConfig()->getGlobalOption()->getSlug();
        $skuWeb         = $skuSlug.'_'.$domainSlug;


        $data = array('name'=> $name,'sku'=> $sku,'skuSlug'=> $skuSlug,'skuWeb'=> $skuWeb);
        return $data;
    }

    private  function getStrPad($lastCode,$limit)
    {
        $data = str_pad($lastCode,$limit, '0', STR_PAD_LEFT);
        return $data;
    }
}