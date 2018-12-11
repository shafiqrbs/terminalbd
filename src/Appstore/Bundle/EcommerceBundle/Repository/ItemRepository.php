<?php

namespace Appstore\Bundle\EcommerceBundle\Repository;
use Appstore\Bundle\EcommerceBundle\Entity\Discount;
use Appstore\Bundle\configBundle\Entity\Purchase;
use Appstore\Bundle\configBundle\Entity\PurchaseItem;
use Appstore\Bundle\configBundle\Entity\PurchaseVendorItem;
use Appstore\Bundle\EcommerceBundle\Entity\Item;
use Doctrine\ORM\EntityRepository;

/**
 * PurchaseVendorItemRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */


class ItemRepository extends EntityRepository
{


    public function findFrontendProductWithSearch($config, $data , $limit = 0)
    {
        if (!empty($data['sortBy'])) {

            $sortBy = explode('=?=', $data['sortBy']);
            $sort = $sortBy[0];
            $order = $sortBy[1];
        }

        $qb = $this->createQueryBuilder('product');
        $qb->leftJoin("product.masterItem",'masterItem');
        $qb->leftJoin('product.brand','brand');
        $qb->where("product.isWeb = 1");
        $qb->andWhere("product.status = 1");
        $qb->andWhere("product.ecommerceConfig = :config");
        $qb->setParameter('config', $config);

        if (!empty($data['brand'])) {
            $qb->andWhere("product.brand = :brand");
            $qb->setParameter('brand', $data['brand']);
        }

        if (!empty($data['promotion'])) {
            $qb->andWhere("product.promotion = :promotion");
            $qb->setParameter('promotion', $data['promotion']);
        }

        if (!empty($data['tag'])) {
            $qb->leftJoin('product.tag','tag');
            $qb->andWhere("tag.id = :tagId");
            $qb->setParameter('tagId', $data['tag']);
        }

        if (!empty($data['discount'])) {
            $qb->andWhere("product.discount >= :discount");
            $qb->setParameter('discount', $data['discount']);
        }

        if (!empty($data['category'])) {
            $qb
                ->join('masterItem.category', 'category')
                ->andWhere(
                    $qb->expr()->orX(
                        $qb->expr()->like('category.path', "'". intval($data['category']) . "/%'"),
                        $qb->expr()->like('category.path', "'%/" . intval($data['category']) . "/%'")
                    )
                );
        }


        if (!empty($data['product'])) {
             $search = strtolower($data['product']);
             $qb->andWhere($qb->expr()->like("product.slug", "'%$search%'"  ));
        }

        if (empty($data['sortBy'])){
            $qb->orderBy('product.updated', 'DESC');
        }else{
            $qb->orderBy($sort ,$order);
        }
        if($limit > 0 ) {
            $qb->setMaxResults($limit);
        }
        $res = $qb->getQuery();
        return  $res;

    }

    public function filterFrontendProductWithSearch($config, $data , $limit = 0)
    {
        if (!empty($data['sortBy'])) {

            $sortBy = explode('=?=', $data['sortBy']);
            $sort = $sortBy[0];
            $order = $sortBy[1];
        }

        $qb = $this->createQueryBuilder('product');
        $qb->leftJoin("product.masterItem",'masterItem');
        $qb->leftJoin('product.goodsItems','goodsitems');
        $qb->where("product.isWeb = 1");
        $qb->andWhere("product.status = 1");
        $qb->andWhere("product.ecommerceConfig = :config");
        $qb->setParameter('config', $config);

        if (!empty($data['brand'])) {
            $qb->andWhere("product.brand IN(:brand)");
            $qb->setParameter('brand',$data['brand']);
        }

        if (!empty($data['size'])) {
            $qb->andWhere("goodsitems.size IN(:size)");
            $qb->setParameter('size',$data['size']);
        }

        if (!empty($data['color'])) {
            $qb->leftJoin('goodsitems.colors','colors');
            $qb->andWhere("colors.id IN(:color)");
            $qb->setParameter('color',$data['color']);
        }

        if (!empty($data['promotion'])) {
            $qb->andWhere("product.promotion IN(:promotion)");
            $qb->setParameter('promotion',$data['promotion']);
        }

        if (!empty($data['tag'])) {
            $qb->andWhere("product.tag IN(:tag)");
            $qb->setParameter('tag',$data['tag']);
        }

        if (!empty($data['discount'])) {
            $qb->andWhere("product.discount IN(:discount)");
            $qb->setParameter('discount',$data['discount']);
        }

        if (!empty($data['priceStart'])) {
            $qb->andWhere(' product.salesPrice >= :priceStart');
            $qb->setParameter('priceStart',$data['priceStart']);
        }

        if (!empty($data['priceEnd'])) {
            $qb->andWhere(' product.salesPrice <= :priceEnd');
            $qb->setParameter('priceEnd',$data['priceEnd']);
        }
        
        if (empty($data['sortBy'])){
            $qb->orderBy('product.updated', 'DESC');
        }else{
            $qb->orderBy($sort ,$order);
        }
        if($limit > 0 ) {
            $qb->setMaxResults($limit);
        }
        $res = $qb->getQuery();
        return  $res;

    }

    public function insertCopyPurchaseItem(Item $entity, Item $copyEntity)
    {
        $em = $this->_em;
        $entity->setName($copyEntity->getName());
        $entity->setWebName($copyEntity->getWebName());
        $entity->setSubProduct(true);
        $entity->setQuantity($copyEntity->getQuantity());
        $entity->setMasterQuantity($copyEntity->getMasterQuantity());
        $entity->setPurchasePrice($copyEntity->getPurchase());
        $entity->setSalesPrice($copyEntity->getSalesPrice());
        $entity->setOverHeadCost($copyEntity->getOverHeadCost());
        $entity->setSize($copyEntity->getSize());
        $entity->setItemColors($copyEntity->getItemColors());
        $entity->setBrand($copyEntity->getBrand());
        $entity->setDiscount($copyEntity->getDiscount());
        $entity->setDiscountPrice($copyEntity->getDiscountPrice());
        $entity->setContent($copyEntity->getContent());
        $entity->setTag($copyEntity->getTag());
        $entity->setPromotion($copyEntity->getPromotion());
        $entity->setCountry($copyEntity->getCountry());
        $entity->setSource($copyEntity->getSource());
        $em->persist($entity);
        $em->flush();
    }


    public function getSliderFeatureProduct($config, $limit = 3)
    {

        $qb = $this->createQueryBuilder('product');
        $qb->where("product.isWeb = 1");
        $qb->expr()->isNotNull('product.promotion');
        $qb->andWhere("product.ecommerceConfig = :config");
        $qb->setParameter('config', $config->getId());
        if($limit > 0 ) {
            $qb->setMaxResults($limit);
        }
        $qb->orderBy('product.updated', 'DESC');
        $qb = $qb->getQuery();
        $result = $qb->getResult();
        return  $result;

    }

    public function frontendProductNext($entity){

        $db = $this->getNextPrevious($entity);
        return $db->andWhere($db->expr()->gt('e.id',$entity->getId()))->getQuery()->getOneOrNullResult();
    }
    public function frontendProductPrev($entity){
        $db = $this->getNextPrevious($entity);
        return $db->andWhere($db->expr()->lt('e.id',$entity->getId()))->getQuery()->getOneOrNullResult();
    }

    private function getNextPrevious(Item $entity)
    {

        /**
         * @var Item $entity
         */
        $em = $this->_em;
        $db = $em->createQueryBuilder();
        $db->select('e');
        $db->from('EcommerceBundle:Item','e');
        $db->where($db->expr()->andX(
            $db->expr()->eq('e.isWeb',1),
            $db->expr()->eq('e.status',1),
            $db->expr()->eq('e.ecommerceConfig',$entity->getecommerceConfig()->getId())
        ));
        $db->setMaxResults(1);
        return $db;

    }

    public function handleSearchBetween($qb,$data){

        $name           = isset($data['name'])? $data['name'] :'';
        $cat            = isset($data['category'])? $data['category'] :'';
        $brand          = isset($data['brand'])? $data['brand'] :'';
        $vendor         = isset($data['vendor'])? $data['vendor'] :'';

        if (!empty($vendor)) {
            $qb->join('purchase.vendor', 'v');
            $qb->andWhere("v.name = :vendor");
            $qb->setParameter('vendor', $vendor);
        }

        if (!empty($cat)) {
            $qb->join('product.masterItem', 'masterItem');
            $qb->andWhere("masterItem.category = :category");
            $qb->setParameter('category', $cat);
        }
        if (!empty($brand)) {
            $qb->andWhere("item.brand = :brand");
            $qb->setParameter('brand', $brand);
        }
        if (!empty($name)) {
            $qb->andWhere($qb->expr()->like("item.name", "'% $name %'"  ));
        }
    }

    public function findWithSearch($config,$data,$limit=0)
    {

        $qb = $this->createQueryBuilder('item');
        $qb->join('item.purchase', 'purchase');
        $qb->where("item.source ='config' ");
        $qb->setParameter('config', $config);
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('item.updated','DESC');
        $qb->getQuery();
        return  $qb;

    }

    public function findFoodWithSearch($config,$data,$limit=0)
    {


        $qb = $this->createQueryBuilder('item');
        $qb->where("item.source = 'food'");
        $qb->andWhere("item.ecommerceConfig = :config");
        $qb->setParameter('config', $config);
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('item.updated','DESC');
        $qb->getQuery();
        return  $qb;

    }

    public function findAllProductWithSearch($data,$limit=0)
    {


        $order = isset($data['order'])? $data['order'] :'ASC';
        $qb = $this->createQueryBuilder('item');
        $qb->leftJoin("item.masterItem",'masterItem' );
        $qb->where("item.isWeb = 1");
        $this->handleSearchBetween($qb,$data);
        if(!empty($order)){

            if($order == "ASC"){
                $qb->orderBy('item.salesPrice','ASC');
            }else{
                $qb->orderBy('item.salesPrice','DESC');
            }

        }else{

            $qb->orderBy('item.updated','DESC');
        }
        $qb->getQuery();
        return  $qb;

    }

    public function findGoodsWithSearch($config,$data,$limit = 0)
    {

        $qb = $this->createQueryBuilder('product');
        $qb->where("product.isWeb = 1");
        $qb->andWhere("product.ecommerceConfig = :config");
        $qb->setParameter('config', $config);
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('product.updated', 'DESC');
        $qb = $qb->getQuery();
        $result = $qb->getResult();
        return  $result;

    }


    public function findItemWithSearch($config,$data,$limit = 0)
    {

        $qb = $this->createQueryBuilder('item');
        $qb->where("item.source = 'service'");
        $qb->andWhere("item.isWeb = 1");
        $qb->andWhere("item.ecommerceConfig = :config");
        $qb->setParameter('config', $config);
        $this->handleSearchBetween($qb,$data);
        if(!empty($order)){

            if($order == "ASC"){
                $qb->orderBy('item.salesPrice','ASC');
            }else{
                $qb->orderBy('item.salesPrice','DESC');
            }

        }else{

            $qb->orderBy('item.updated','DESC');
        }
        if($limit > 0 ){
            $qb->getMaxResults($limit);
        }
        $qb->getQuery();
        return  $qb;

    }

    public function salesItemWithSearch($config)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->join("e.masterItem",'masterItem' );
        $qb->where("e.source = 'service'");
        $qb->andWhere("e.ecommerceConfig = :config");
        $qb->setParameter('config', $config);
        $qb->orderBy('e.name','ASC');
        $qb->getQuery()->getResult();
        return  $qb;

    }


    public function getCulculationDiscountPrice(Item $purchase , Discount $discount)
    {
        if($discount->getType() == 'percentage'){
            $price = ( ($purchase->getSalesPrice() * (int)$discount->getDiscountAmount())/100 );
            $discountPrice = $purchase->getSalesPrice() - $price;
        }else{
            $discountPrice = ( $purchase->getSalesPrice() - (int)$discount->getDiscountAmount());
        }

        return $discountPrice;

    }



}
