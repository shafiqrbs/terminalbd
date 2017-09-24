<?php

namespace Appstore\Bundle\EcommerceBundle\Repository;
use Appstore\Bundle\EcommerceBundle\Entity\Order;
use Appstore\Bundle\EcommerceBundle\Entity\OrderItem;
use Appstore\Bundle\InventoryBundle\Entity\GoodsItem;
use Doctrine\ORM\EntityRepository;

/**
 * OnlineOrderItemRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class OrderItemRepository extends EntityRepository
{


    public function getItemOverview(Order $order){

        $qb = $this->createQueryBuilder('e');
        $qb->select('SUM(e.subTotal) AS totalAmount,SUM(e.quantity) AS totalQuantity');
        $qb->where("e.order = :order");
        $qb->andWhere("e.status = 1");
        $qb->setParameter('order', $order->getId());
        $result = $qb->getQuery()->getSingleResult();
        return $result;

    }

    
    public function totalItemAmount(Order $order){

        $qb = $this->createQueryBuilder('e');
        $qb->select('SUM(e.subTotal) AS totalAmount');
        $qb->where("e.order = :order");
        $qb->andWhere("e.status = 1");
        $qb->setParameter('order', $order->getId());
        $result = $qb->getQuery()->getSingleResult();
        $total = $result['totalAmount'];
        return $total;

    }

    public function insertOrderItem($order,$product,GoodsItem $subitem,$quantity)
    {
        $em = $this->_em;
        $entity = new OrderItem();
        $entity->setOrder($order);
        $entity->setPurchaseVendorItem($product);
        $entity->setGoodsItem($subitem);
        $entity->setQuantity($quantity);
        $entity->setPrice($subitem->getSalesPrice());
        $entity->setSubTotal($subitem->getSalesPrice() * $quantity );
        $em->persist($entity);
        $em->flush();
    }

    public function itemOrderUpdate($order,$data)
    {
       /* $em = $this->_em;
        $i = 0;
        foreach ($data['itemId'] as $row ){
            $entity = $em->getRepository('EcommerceBundle:OrderItem')->find($data['itemId'][$i]);
            $entity->setOrder($order);
            $entity->setQuantity($data['quantity'][$i]);
            if(isset($data['color'][$i]) and !empty($data['color'][$i])){
            $entity->setColor($em->getRepository('InventoryBundle:ItemColor')->find($data['color'][$i]));
            }
            $entity->setPrice($entity->getGoodsItem()->getSalesPrice());
            $entity->setSubTotal($entity->getPrice() * $data['quantity'][$i]);
            $em->persist($entity);
            $em->flush();
            $i++;
        }*/
    }

    public function itemOrderUpdateBarcode(Order $order)
    {
        $em = $this->_em;
        foreach ($order->getOrderItems() as $orderItem){

            $orderPurchaseItem = $this->getOrderPurchaseItem($orderItem);
            if($orderPurchaseItem){
            $orderItem->setPurchaseItem($orderPurchaseItem);
            $em->persist($orderItem);
            $em->flush();
            }
        }
    }

    public function getOrderPurchaseItem(OrderItem $orderItem)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->join('pvi.purchaseItems','pi');
        $qb->join('pi.item','i');
        $qb->select('pi.id');
        $qb->where("e.id = :orderItemId");
        $qb->setParameter('orderItemId', $orderItem);
        $qb->andWhere("pi.purchaseVendorItem = :purchaseVendorItem");
        $qb->setParameter('purchaseVendorItem', $orderItem->getPurchaseVendorItem());
        if(!empty($orderItem->getSize())){
            $qb->andWhere("i.size = :size");
            $qb->setParameter('size', $orderItem->getSize());
        }
        if(!empty($orderItem->getColor())){
            $qb->andWhere("i.color = :color");
            $qb->setParameter('color', $orderItem->getColor());
        }

        $result = $qb->getQuery()->getOneOrNullResult();
        if(!empty($result)){
            return $this->_em->getRepository('InventoryBundle:PurchaseItem')->find($result['id']);
        }else{
            return false;
        }


    }


}
