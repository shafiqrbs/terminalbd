<?php

namespace Appstore\Bundle\InventoryBundle\Repository;
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

    public function findWithSearch($inventory,$data,$limit=0)
    {

        $cat = isset($data['cat'])? $data['cat'] :'';

        $qb = $this->createQueryBuilder('item');
        $qb->join('item.purchase', 'purchase');
        $qb->where("purchase.approvedBy is not null");
        if (!empty($cat)) {
            $qb->andWhere("item.category = :category");
            $qb->setParameter('category', $cat);
        }

        /*
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
            $qb->orderBy('v.companyName','ASC');
        }

        if (!empty($brand)) {

            $qb->andWhere("b.name = :brand");
            $qb->setParameter('brand', $brand);
            $qb->orderBy('brand.name','ASC');
        }*/
        $qb->orderBy('item.updated','DESC');
/*        if($limit > 0)
        {
            $qb->setMaxResults($limit);
            $qb->setFirstResult(0);
        }*/

        $qb->getQuery();
        return  $qb;

    }

    public function findFoodWithSearch($inventory,$data,$limit=0)
    {

        $name = isset($data['name'])? $data['name'] :'';
        $cat = isset($data['category'])? $data['category'] :'';

        $qb = $this->createQueryBuilder('item');
        $qb->where("item.source = 'food'");
        $qb->andWhere("item.inventoryConfig = :inventory");
        $qb->setParameter('inventory', $inventory);
        if (!empty($cat)) {
            $qb->andWhere("item.category = :category");
            $qb->setParameter('category', $cat);
        }
        if (!empty($name)) {
            $qb->andWhere($qb->expr()->like("item.name", "'$name%'"  ));
        }

        /*

        if (!empty($size)) {

            $qb->join('item.size', 's');
            $qb->andWhere("s.name = :size");
            $qb->setParameter('size', $size);
        }
        if (!empty($vendor)) {

            $qb->join('item.vendor', 'v');
            $qb->andWhere("v.companyName = :vendor");
            $qb->setParameter('vendor', $vendor);
            $qb->orderBy('v.companyName','ASC');
        }

        if (!empty($brand)) {

            $qb->andWhere("b.name = :brand");
            $qb->setParameter('brand', $brand);
            $qb->orderBy('brand.name','ASC');
        }*/


        $qb->orderBy('item.updated','DESC');
        $qb->getQuery();
        return  $qb;

    }

    public function findGoodsWithSearch($inventory,$data,$limit=0)
    {

        $name = isset($data['name'])? $data['name'] :'';
        $cat = isset($data['category'])? $data['category'] :'';
        $brand = isset($data['brand'])? $data['brand'] :'';
        $order = isset($data['order'])? $data['order'] :'ASC';

        $qb = $this->createQueryBuilder('item');
        $qb->where("item.source = 'goods'");
        $qb->where("item.inventoryConfig = :inventory");
        $qb->setParameter('inventory', $inventory);
        if (!empty($cat)) {
            $qb->andWhere("item.category = :category");
            $qb->setParameter('category', $cat);
        }
         if (!empty($brand)) {
            $qb->andWhere("item.brand = :brand");
            $qb->setParameter('brand', $brand);
        }
        if (!empty($name)) {
            $qb->andWhere($qb->expr()->like("item.name", "'$name%'"  ));
        }
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
            $data .=' <tr id="remove-vendor-item-'.$entity->getId().'">';
            $data .='<td class="numeric" >'.$entity->getName().'</td>';
            $data .='<td class="numeric" >'.$entity->getMasterItem()->getName().'</td>';
            $data .='<td class="numeric" >'.$entity->getQuantity().'</td>';
            $data .='<td class="numeric" >'.$entity->getPurchasePrice().'</td>';
            $data .='<td class="numeric" >'.number_format ($entity->getQuantity() * $entity->getPurchasePrice() ).'</td>';
            $data .='<td class="numeric" >'.$entity->getSalesPrice().'</td>';
            $data .='<td class="numeric" >'.number_format ($entity->getQuantity() * $entity->getSalesPrice()).'</td>';
            $data .='<td class="numeric" >'.$entity->getWebPrice().'</td>';
            $data .='<td class="numeric" >'.number_format ($entity->getQuantity() * $entity->getWebPrice()).'</td>';
            $data .='<td class="numeric" >
                     <a id="'.$entity->getId().'" title="Are you sure went to delete ?" rel="/inventory/purchasevendoritem/'.$entity->getId().'/delete" href="javascript:" class="btn red mini removeVendorItem" ><i class="icon-trash"></i></a>
                     </td>';
            $data .='</tr>';
        }
        return $data;

    }

}
