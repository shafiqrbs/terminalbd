<?php

namespace Appstore\Bundle\InventoryBundle\Repository;
use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Doctrine\ORM\EntityRepository;

/**
 * PurchaseItemRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PurchaseItemRepository extends EntityRepository
{


    public function findWithSearch($inventory,$data)
    {
        $item = isset($data['item'])? $data['item'] :'';
        $grn = isset($data['grn'])? $data['grn'] :'';
        $brand = isset($data['brand'])? $data['brand'] :'';

        $qb = $this->createQueryBuilder('pi');
        $qb->join("pi.purchase",'purchase');
        $qb->join("pi.item",'item');
        $qb->where("purchase.inventoryConfig = :inventoryConfig");
        $qb->setParameter('inventoryConfig', $inventory);

        if (!empty($item)) {

            $qb->join('item.masterItem', 'm');
            $qb->andWhere("m.name = :name");
            $qb->setParameter('name', $item);
        }
        if (!empty($brand)) {

            $qb->join('item.brand', 'b');
            $qb->andWhere("b.name = :brand");
            $qb->setParameter('brand', $brand);
        }

        if (!empty($grn)) {
            $qb->andWhere("purchase.grn = :grn");
            $qb->setParameter('grn', $grn);
        }
        $qb->orderBy('item.updated','DESC');
        $sql = $qb->getQuery();
        return $sql;

    }

    public function getPurchaseItemCount($item)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('count(pi.id)');
        $qb->from('InventoryBundle:PurchaseItem','pi');
        $qb->where("pi.item = :item");
        $qb->setParameter('item', $item->getId());
        $count = $qb->getQuery()->getSingleScalarResult();
        if($count > 0 ){
            return $count+1;
        }else{
            return 1;
        }
        return $item;
    }


    public function getPurchaseItemQuantity($purchase)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('sum(e.quantity)  as totalQnt' , 'count(e.id)  as totalItem');
        $qb->from('InventoryBundle:PurchaseItem','e');
        $qb->where("e.purchase = :purchase");
        $qb->setParameter('purchase', $purchase->getId());
        $qb = $qb->getQuery()->getSingleResult();
        return $qb;
    }

    public function findBarcode($item)
    {
        $avg = $this->getItemAveragePrice($item->getItem());
        $data  = '';
        $data .='<div class="span4">';
        $data .='<ul class="unstyled">';
        $data .='<li><strong>Vendor #:</strong> '.$item->getPurchase()->getVendor()->getCompanyName().'</li>';
        $data .='<li><strong>Received:</strong> xxx</li>';
        $data .='</ul>';
        $data .='</div>';
        $data .='<div class="span4">';
        $data .='<ul class="unstyled">';
        $data .='<li><strong>Purchase:</strong> '.$item->getPurchasePrice().'</li>';
        $data .='<li><strong>Sales:</strong> '.$item->getSalesPrice().'</li>';
        $data .='</ul>';
        $data .='</div>';
        $data .='<div class="span4">';
        $data .='<ul class="unstyled">';
        $data .='<li><strong>Purse.average:</strong> '.number_format($avg['purchaseAvg']).'</li>';
        $data .='<li><strong>Sls.average:</strong> '.number_format($avg['salesAvg']).'</li>';
        $data .='</ul>';
        $data .='</div>';

        return $data;
    }

    public function getItemAveragePrice($item)
    {
             return $qbAvg = $this->createQueryBuilder('pi')
            ->select("avg(pi.salesPrice) as salesAvg, avg(pi.purchasePrice) as purchaseAvg")
            ->where('pi.item = :item')
            ->setParameter('item', $item->getId())
            ->getQuery()->getSingleResult();
    }

    public function getItemList($purchase)
    {
        $entities = $purchase->getPurchaseItems();
        $data = '';
        foreach( $entities as $entity){
           $data .=' <tr id="remove-'.$entity->getId().'">';
            $data .='<td class="numeric" >'.$entity->getName().'</td>';
            $data .='<td class="numeric" >'.$entity->getItem()->getSkuSlug().'</td>';
            $data .='<td class="numeric" >'.$entity->getQuantity().'</td>';
            $data .='<td class="numeric" >'.$entity->getPurchasePrice().'</td>';
            $data .='<td class="numeric" >'.$entity->getPurchaseSubTotal().'</td>';
            $data .='<td class="numeric" >'.$entity->getSalesPrice().'</td>';
            $data .='<td class="numeric" >'.$entity->getSalesSubTotal().'</td>';
            $data .='<td class="numeric" >'.$entity->getWebPrice().'</td>';
            $data .='<td class="numeric" >'.$entity->getWebSubTotal().'</td>';
            $data .='<td class="numeric" >
                     <a id="'.$entity->getId().'" title="Are you sure went to delete ?" rel="/inventory/purchaseitem/'.$entity->getId().'/delete" href="javascript:" class="btn red mini delete" ><i class="icon-trash"></i></a>
                     </td>';
            $data .='</tr>';
        }
        return $data;

    }

    public function getBarcodeForPrint($inventory,$data)
    {

        $qb = $this->createQueryBuilder('pi');
        $qb->join('pi.purchase', 'purchase');
        $qb->join('purchase.inventoryConfig', 'ic');
        $qb->select('pi');
        $qb->where($qb->expr()->in("pi.id", $data ));
        $qb->andWhere("ic.id = :inventory");
        $qb->setParameter('inventory', $inventory->getId());
        return $qb->getQuery()->getResult();
    }

    public function getManualSalesItem($inventory,$data)
    {

        $qb = $this->createQueryBuilder('pi');
        $qb->join('pi.purchase', 'purchase');
        $qb->join('purchase.inventoryConfig', 'ic');
        $qb->select('pi');
        $qb->where($qb->expr()->in("pi.id", $data ));
        $qb->andWhere("ic.id = :inventory");
        $qb->setParameter('inventory', $inventory->getId());
        return $qb->getQuery()->getResult();

    }

    public function returnPurchaseItemDetails($inventory,$barcode)
    {

        $qb = $this->createQueryBuilder('i');
        $qb->join('i.purchase', 'p');
        $qb->select('i');
        $qb->where("i.barcode = :barcode" );;
        $qb->setParameter('barcode', $barcode);
        $qb->andWhere("p.inventoryConfig = :inventory");
        $qb->setParameter('inventory', $inventory->getId());
        return $qb->getQuery()->getSingleResult();

    }


    public function searchAutoComplete($item, InventoryConfig $inventory)
    {

        $qb = $this->createQueryBuilder('pi');
        $qb->join('pi.purchase', 'p');
        $qb->join('pi.stockItem', 'stockitem');
        $qb->select('pi.barcode as id');
        $qb->addSelect('pi.barcode as text');
        $qb->addSelect('SUM(stockitem.quantity) as item_name');
        $qb->where($qb->expr()->like("pi.barcode", "'$item%'"  ));
        $qb->andWhere("p.inventoryConfig = :inventory");
        $qb->setParameter('inventory', $inventory->getId());
        $qb->orderBy('p.updated', 'ASC');
        $qb->setMaxResults( '10' );
        return $qb->getQuery()->getResult();

    }

}
