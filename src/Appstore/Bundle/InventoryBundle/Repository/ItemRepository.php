<?php

namespace Appstore\Bundle\InventoryBundle\Repository;
use Appstore\Bundle\EcommerceBundle\Entity\Order;
use Appstore\Bundle\EcommerceBundle\Entity\OrderItem;
use Appstore\Bundle\InventoryBundle\Entity\Damage;
use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Appstore\Bundle\InventoryBundle\Entity\Sales;
use Appstore\Bundle\InventoryBundle\Entity\SalesReturn;
use Doctrine\ORM\EntityRepository;

/**
 * ItemTypeGroupingRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ItemRepository extends EntityRepository
{

    public  function getSumPurchaseItem($inventory){

        $qb = $this->createQueryBuilder('item');
        $qb->join('item.purchaseItems', 'pItem');
        $qb->select('item.id as id');
        $qb->addSelect('SUM(pItem.quantity) as quantity ');
        $qb->where("item.inventoryConfig = :inventory");
        $qb->setParameter('inventory', $inventory);
        $qb->groupBy('item.id');
        $result = $qb->getQuery()->getResult();
        foreach ($result as $row ){
            $entity = $this->find($row['id']);
            $entity->setPurchaseQuantity($row['quantity']);
            $this->_em->persist($entity);
        }
        $this->_em->flush();

    }

    public function checkDuplicateSKU(InventoryConfig $inventory,$data)
    {


        $masterItem = $data['appstore_bundle_inventorybundle_item']['masterItem'];
        $vendor = isset($data['appstore_bundle_inventorybundle_item']['vendor']) ? $data['appstore_bundle_inventorybundle_item']['vendor'] :'NULL';
        $itemColor = isset ($data['appstore_bundle_inventorybundle_item']['color']) ? $data['appstore_bundle_inventorybundle_item']['color']:'NULL';
        $itemSize = isset($data['appstore_bundle_inventorybundle_item']['size']) ? $data['appstore_bundle_inventorybundle_item']['size'] : 'NULL';
        $itemBrand = isset($data['appstore_bundle_inventorybundle_item']['brand'])?$data['appstore_bundle_inventorybundle_item']['brand']:'NULL';

        $qb = $this->createQueryBuilder('item');
        $qb->join('item.masterItem', 'm');
        $qb->select('COUNT(item.id) AS totalNumber');
        $qb->where("item.inventoryConfig = :inventory");
        $qb->setParameter('inventory', $inventory);

        $qb->andWhere('item.masterItem = :masterId');
        $qb->setParameter('masterId', $masterItem);
        if($inventory->getIsSize() == 1) {
            $qb->andWhere('item.size = :itemSize');
            $qb->setParameter('itemSize', $itemSize);
        }
        if($inventory->getIsColor() == 1) {
            $qb->andWhere('item.color = :itemColor');
            $qb->setParameter('itemColor', $itemColor);
        }
        if($inventory->getIsVendor() == 1) {
            $qb->andWhere('item.vendor = :vendor');
            $qb->setParameter('vendor', $vendor);
        }
        if($inventory->getIsBrand() == 1) {
            $qb->andWhere('item.brand = :itemBrand');
            $qb->setParameter('itemBrand', $itemBrand);
        }
        $count = $qb->getQuery()->getArrayResult();
        $result = $count[0]['totalNumber'];
        return $result;

    }

    public function findWithSearch($inventory,$data)
    {

        $item = isset($data['item'])? $data['item'] :'';
        $color = isset($data['color'])? $data['color'] :'';
        $size = isset($data['size'])? $data['size'] :'';
        $vendor = isset($data['vendor'])? $data['vendor'] :'';
        $brand = isset($data['brand'])? $data['brand'] :'';

        $qb = $this->createQueryBuilder('item');
        $qb->where("item.inventoryConfig = :inventory");
        $qb->setParameter('inventory', $inventory);
        if (!empty($item)) {

            $qb->join('item.masterItem', 'm');
            $qb->andWhere("m.name = :name");
            $qb->setParameter('name', $item);
        }
        if (!empty($color)) {

            $qb->join('item.color', 'c');
            $qb->andWhere("c.name = :color");
            $qb->setParameter('color', $color);
        }
        if (!empty($size)) {

            $qb->join('item.size', 's');
            $qb->andWhere("s.name = :size");
            $qb->setParameter('size', $size);
        }
        if (!empty($vendor)) {

            $qb->join('item.vendor', 'v');
            $qb->andWhere("v.companyName = :vendor");
            $qb->setParameter('vendor', $vendor);
        }

        if (!empty($brand)) {

            $qb->join('item.brand', 'b');
            $qb->andWhere("b.name = :brand");
            $qb->setParameter('brand', $brand);

        }
        $qb->orderBy('item.updated','DESC');
        $qb->getQuery();
        return  $qb;

    }
    public function getLastId($inventory)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('count(item.id)');
        $qb->from('InventoryBundle:Item','item');
        $qb->where("item.inventoryConfig = :inventory");
        $qb->setParameter('inventory', $inventory);
        $count = $qb->getQuery()->getSingleScalarResult();
        if($count > 0 ){
         return $count+1;
        }else{
         return 1;
        }

    }

    public function searchAutoComplete($item, InventoryConfig $inventory)
    {
        $query = $this->createQueryBuilder('i');
        $query->join('i.inventoryConfig', 'ic');
        $query->leftJoin('i.stockItems', 'stockItem');
        $query->leftJoin('i.vendor', 'vendor');
        $query->leftJoin('i.masterItem', 'masterItem');
        $query->leftJoin('i.color', 'color');
        $query->leftJoin('i.size', 'size');
        $query->select('i.id as id');
        $query->addSelect('i.skuSlug as text');
        $query->addSelect('i.sku as sku');
        $query->addSelect('vendor.name as vendorName');
        $query->addSelect('masterItem.name as masterItemName');
        $query->addSelect('color.name as colorName');
        $query->addSelect('size.name as sizeName');
        $query->addSelect('SUM(stockItem.quantity) as remainingQuantity');
        $query->where($query->expr()->like("i.skuSlug", "'%$item%'"  ));
        $query->andWhere("ic.id = :inventory");
        $query->setParameter('inventory', $inventory->getId());
        $query->groupBy('i.id');
        $query->orderBy('i.sku', 'ASC');
        $query->setMaxResults( '30' );
        return $query->getQuery()->getResult();

    }

    public function getItemUpdatePriceQnt($purchase){

        $em = $this->_em;
        foreach($purchase->getPurchaseItems() as $purchaseItem ){

            $entity = $purchaseItem->getItem();
            $qnt = ($entity->getPurchaseQuantity() + $purchaseItem->getQuantity());
            $entity->setPurchaseQuantity($qnt);
            $entity->setUpdated($purchase->getCreated());
            $em->persist($entity);
        }

        $em->flush();
    }
    public function getItemPurchaseReturn($purchaseReturn){

        $em = $this->_em;
        foreach($purchaseReturn->getPurchaseReturnItems() as $purchaseReturnItem ){

            $entity = $purchaseReturnItem->getPurchaseItem()->getItem();
            $qnt = ($entity->getPurchaseQuantityReturn() + $purchaseReturnItem->getQuantity());
            $entity->setPurchaseQuantityReturn($qnt);
            $entity->setUpdated($purchaseReturn->getCreated());
            $em->persist($entity);
        }
        $em->flush();

    }

    public function getItemPurchaseReplace($purchaseReplaceItem){

        $em = $this->_em;
            $entity = $purchaseReplaceItem->getPurchaseItem()->getItem();
            $qnt = ($entity->getPurchaseQuantityReplace() + $purchaseReplaceItem->getReplaceQuantity());
            $entity->setPurchaseQuantityReturn($qnt);
            $em->persist($entity);
            $em->flush();

    }

    public function getItemSalesUpdate(Sales $sales){

        $em = $this->_em;
        foreach($sales->getSalesItems() as $salesItem ){

            $entity = $salesItem->getItem();
            $qnt = ($entity->getSalesQuantity() + $salesItem->getQuantity());
            $entity->setSalesQuantity($qnt);
            $em->persist($entity);
        }
        $em->flush();

    }

    public function getItemSalesReturnUpdate(SalesReturn $sales){

        $em = $this->_em;
        foreach($sales->getSalesReturnItems() as $salesItem ){

            $entity = $salesItem->getSalesItem()->getItem();
            $qnt = ($entity->getSalesQuantityReturn() + $salesItem->getQuantity());
            $entity->setSalesQuantityReturn($qnt);
            $em->persist($entity);
        }
        $em->flush();

    }

    public function onlineOrderUpdate(Order $order){

        $em = $this->_em;
        foreach($order->getOrderItems() as $orderItem ) {

            $entity = $orderItem->getPurchaseItem()->getItem();
            $qnt = ($entity->getOnlineOrderQuantity() + $orderItem->getQuantity());
            $entity->setOnlineOrderQuantity($qnt);
            $em->persist($entity);
            $em->flush();
        }

    }



    public function itemDamageUpdate(Damage $damage){

        $em = $this->_em;

            $entity = $damage->getItem();
            $qnt = ($entity->getDamageQuantity() + $damage->getQuantity());
            $entity->setDamageQuantity($qnt);
            $em->persist($entity);
            $em->flush();

    }




}
