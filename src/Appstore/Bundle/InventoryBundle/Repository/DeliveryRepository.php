<?php

namespace Appstore\Bundle\InventoryBundle\Repository;
use Appstore\Bundle\InventoryBundle\Entity\Delivery;
use Doctrine\ORM\EntityRepository;

/**
 * DeliveryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DeliveryRepository extends EntityRepository
{
    public function findWithSearch($inventory,$data)
    {

        $startDate = isset($data['startDate'])  ? $data['startDate'].' 00:00:00' :'';
        $endDate =   isset($data['endDate'])  ? $data['endDate'].' 23:59:59' :'';

        $item = isset($data['item'])? $data['item'] :'';
        $vendor = isset($data['vendor'])? $data['vendor'] :'';
        $qb = $this->createQueryBuilder('damage');
        $qb->where("damage.inventoryConfig = :inventory");
        $qb->setParameter('inventory', $inventory);

        if (!empty($startDate) and $startDate !="") {
            $qb->andWhere("damage.updated >= :startDate");
            $qb->setParameter('startDate', $startDate);
        }
        if (!empty($endDate)) {
            $qb->andWhere("damage.updated <= :endDate");
            $qb->setParameter('endDate', $endDate);
        }

        if (!empty($item)) {
            $qb->join('damage.item', 'item');
            $qb->andWhere("item.sku = :sku");
            $qb->setParameter('sku', $item);
        }

        if (!empty($vendor)) {
            $qb->join('damage.item.vendor', 'v');
            $qb->andWhere("v.companyName = :companyName");
            $qb->setParameter('companyName', $vendor);
        }

        $qb->orderBy('damage.id','DESC');
        $qb->getQuery();
        return  $qb;
    }

    public function updateDeliveryTotal(Delivery $entity)
    {
        $em = $this->_em;
        $total = $em->createQueryBuilder()
        ->from('InventoryBundle:DeliveryItem','e')
        ->select('sum(e.subTotal) as total , sum(e.quantity) as totalQuantity, count(e.id) as totalItem')
        ->where('e.delivery = :delivery')
        ->setParameter('delivery', $entity ->getId())
        ->getQuery()->getSingleResult();
        $entity->setTotalAmount($total['total']);
        $entity->setTotalQuantity($total['totalQuantity']);
        $entity->setTotalItem($total['totalItem']);
        $em->persist($entity);
        $em->flush();
    }

    public function getDeliveryItems(Delivery $delivery)
    {
        $entities = $delivery->getDeliveryItems();
        $data = '';
        $i = 1;
        foreach( $entities as $entity){

            $itemName = $entity->getItem()->getName();
            $data .=' <tr id="remove-'.$entity->getId().'">';
            $data .='<td class="numeric" >'.$i.'</td>';
            $data .='<td class="numeric" >'.$entity->getPurchaseItem()->getBarcode().'</td>';
            $data .='<td class="numeric" >'.$itemName.'</td>';
            $data .='<td class="numeric" >'.$entity->getQuantity().'</td>';
            $data .='<td class="numeric" >'.$entity->getSalesPrice().'</td>';
            $data .='<td class="numeric" >'.$entity->getSubTotal().'</td>';
            $data .='<td class="numeric" >
                     <a id="'.$entity->getId().'" title="Are you sure went to delete ?" rel="/inventory/delivery/'.$delivery->getId().'/'.$entity->getId().'/item/delete" href="javascript:" class="btn red mini delete" ><i class="icon-trash"></i></a>
                     </td>';
            $data .='</tr>';
            $i++;
        }
        return $data;
    }

}
