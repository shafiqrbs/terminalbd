<?php

namespace Appstore\Bundle\InventoryBundle\Repository;
use Appstore\Bundle\EcommerceBundle\Entity\Discount;
use Appstore\Bundle\InventoryBundle\Entity\Purchase;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseVendorItem;
use Doctrine\ORM\EntityRepository;

/**
 * PurchaseVendorItemRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */


class PurchaseVendorItemRepository extends EntityRepository
{


    public function findFrontendProductWithSearch($inventory, $data , $limit = 0)
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
        $qb->andWhere("product.inventoryConfig = :inventory");
        $qb->setParameter('inventory', $inventory);

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
            $catIds = $this->_em->getRepository('ProductProductBundle:Category')->getChildIds($data['category']);
            $qb->andWhere("masterItem.category IN (:category)");
            $qb->setParameter('category', $catIds);
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


    public function getSliderFeatureProduct($inventory, $limit = 3)
    {

        $qb = $this->createQueryBuilder('product');
        $qb->where("product.isWeb = 1");
        $qb->expr()->isNotNull('product.promotion');
        $qb->andWhere("product.inventoryConfig = :inventory");
        $qb->setParameter('inventory', $inventory->getId());
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

    private function getNextPrevious(PurchaseVendorItem $entity)
    {


        /**
         * @var PurchaseVendorItem $entity
         */
        $em = $this->_em;
        $db = $em->createQueryBuilder();
        $db->select('e');
        $db->from('InventoryBundle:PurchaseVendorItem','e');
        $db->where($db->expr()->andX(
            $db->expr()->eq('e.isWeb',1),
            $db->expr()->eq('e.inventoryConfig',$entity->getInventoryConfig()->getId())
        ));
        $db->setMaxResults(1);
        return $db;

    }

    public function handleSearchBetween($qb,$data){

        $name           = isset($data['name'])? $data['name'] :'';
        $cat            = isset($data['category'])? $data['category'] :'';
        $brand          = isset($data['brand'])? $data['brand'] :'';
        $vendor         = isset($data['vendor'])? $data['vendor'] :'';
        $grn            = isset($data['grn'])? $data['grn'] :'';
        $receiveDate    = isset($data['receiveDate'])? $data['receiveDate'] :'';
        $memo           = isset($data['memo'])? $data['memo'] :'';

        if (!empty($vendor)) {
            $qb->join('purchase.vendor', 'v');
            $qb->andWhere("v.name = :vendor");
            $qb->setParameter('vendor', $vendor);
        }

        if (!empty($grn)) {
            $qb->join('purchase.vendor', 'v');
            $qb->andWhere("purchase.grn = :grn");
            $qb->setParameter('grn', $grn);
        }

        if (!empty($memo)) {
            $qb->andWhere("purchase.memo = :memo");
            $qb->setParameter('memo', $memo);
        }

        if (!empty($receiveDate)) {
            $qb->andWhere("purchase.receiveDate = :receiveDate");
            $qb->setParameter('receiveDate', $receiveDate);
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

    public function findWithSearch($inventory,$data,$limit=0)
    {

        $qb = $this->createQueryBuilder('item');
        $qb->join('item.purchase', 'purchase');
        $qb->where("item.source ='inventory' ");
        $qb->andWhere("purchase.approvedBy is not null");
        $qb->andWhere("purchase.inventoryConfig = :inventory");
        $qb->setParameter('inventory', $inventory);
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('item.updated','DESC');
        $qb->getQuery();
        return  $qb;

    }

    public function findFoodWithSearch($inventory,$data,$limit=0)
    {


        $qb = $this->createQueryBuilder('item');
        $qb->where("item.source = 'food'");
        $qb->andWhere("item.inventoryConfig = :inventory");
        $qb->setParameter('inventory', $inventory);
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

    public function findGoodsWithSearch($inventory,$data,$limit = 0)
    {

        $qb = $this->createQueryBuilder('product');
        $qb->where("product.isWeb = 1");
        $qb->andWhere("product.inventoryConfig = :inventory");
        $qb->setParameter('inventory', $inventory);
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('product.updated', 'DESC');
        $qb = $qb->getQuery();
        $result = $qb->getResult();
        return  $result;

    }

    public function findItemWithSearch($inventory,$data,$limit = 0)
    {

        $qb = $this->createQueryBuilder('item');
        $qb->join("item.masterItem",'masterItem');
        $qb->where("item.source = 'service'");
        $qb->andWhere("item.isWeb = 1");
        $qb->andWhere("item.inventoryConfig = :inventory");
        $qb->setParameter('inventory', $inventory);
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

    public function salesItemWithSearch($inventory)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->join("e.masterItem",'masterItem' );
        $qb->where("e.source = 'service'");
        $qb->andWhere("e.inventoryConfig = :inventory");
        $qb->setParameter('inventory', $inventory);
        $qb->orderBy('e.name','ASC');
        $qb->getQuery()->getResult();
        return  $qb;

    }

    public function getPurchaseVendorQuantitySum($purchase)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('sum(e.quantity)');
        $qb->from('InventoryBundle:PurchaseVendorItem','e');
        $qb->where("e.purchase = :purchase");
        $qb->setParameter('purchase', $purchase->getId());
        $sum = $qb->getQuery()->getSingleScalarResult();
        return $sum;
    }

    public function getPurchaseVendorItemQuantity($purchase)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('sum(e.quantity)  as totalQnt' , 'count(e.id)  as totalItem');
        $qb->from('InventoryBundle:PurchaseVendorItem','e');
        $qb->where("e.purchase = :purchase");
        $qb->setParameter('purchase', $purchase->getId());
        $query = $qb->getQuery()->getSingleResult();
        return $query;
    }

    public function insertPurchaseVendorItem($purchase,$data){

        $em = $this->_em;
        $i = 0;
        foreach($data['quantity'] as $row ){

            $entity = new PurchaseVendorItem();
            $entity->setPurchase($purchase);
            $entity->setName($data['vendorItemName'][$i]);
            $entity->setQuantity($data['quantity'][$i]);
            $entity->setPurchasePrice($data['purchasePrice'][$i]);
            $entity->setSalesPrice($data['salesPrice'][$i]);
            $entity->setWebPrice($data['webPrice'][$i]);
            $em->persist($entity);
            $i++;
        }
        $em->flush();
    }

    public function getVendorItemList($purchase)
    {
        $entities = $purchase->getPurchaseVendorItems();
        $data = '';
        foreach( $entities as $entity){

            if($entity->getMasterItem()){
                $masterItem = $entity->getMasterItem()->getName();
            }else{
                $masterItem = '';
            }

            $data .=' <tr id="remove-vendor-item-'.$entity->getId().'">';
            $data .='<td class="numeric" >'.$entity->getName().'</td>';
            $data .='<td class="numeric" >'.$masterItem.'</td>';
            $data .='<td class="numeric" >'.$entity->getQuantity().'</td>';
            $data .='<td class="numeric" >'.$entity->getPurchasePrice().'</td>';
            $data .='<td class="numeric" >'.number_format ($entity->getQuantity() * $entity->getPurchasePrice() ).'</td>';
            $data .='<td class="numeric" >'.$entity->getSalesPrice().'</td>';
            $data .='<td class="numeric" >'.number_format ($entity->getQuantity() * $entity->getSalesPrice()).'</td>';
            $data .='<td class="numeric" >
                     <a id="'.$entity->getId().'" title="Are you sure went to delete ?" rel="/inventory/purchasevendoritem/'.$entity->getId().'/delete" href="javascript:" class="btn red mini removeVendorItem" ><i class="icon-trash"></i></a>
                     </td>';
            $data .='</tr>';
        }
        return $data;

    }

    public function getCulculationDiscountPrice(PurchaseVendorItem $purchase , Discount $discount)
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
