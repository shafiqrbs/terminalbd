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
        $data .='<li><strong>Purchase:</strong> '.number_format($item->getPurchasePrice()).'</li>';
        $data .='<li><strong>Sales:</strong> '.number_format($item->getSalesPrice()).'</li>';
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

        $qb = $this->createQueryBuilder('i');
        $qb->join('i.purchase', 'p');
        $qb->select('i.barcode as id');
        $qb->addSelect('i.barcode as text');
        $qb->addSelect('i.quantity as item_name');
        $qb->where($qb->expr()->like("i.barcode", "'%$item%'"  ));
        $qb->andWhere("p.inventoryConfig = :inventory");
        $qb->setParameter('inventory', $inventory->getId());
        $qb->orderBy('i.barcode', 'DESC');
        $qb->setMaxResults( '10' );
        return $qb->getQuery()->getResult();

    }

    public function itemPurchaseDetails($inventory,$invoice,$id)
    {
        $qb = $this->createQueryBuilder('purchaseItem');
        $qb->join('purchaseItem.item','item');
        $qb->join('purchaseItem.purchase','p');
        $qb->select('p.receiveDate as receiveDate');
        $qb->addSelect('item.sku as sku');
        $qb->addSelect('item.skuSlug as skuSlug');
        $qb->addSelect('p.memo as memo');
        $qb->addSelect('purchaseItem.id as id');
        $qb->addSelect('purchaseItem.barcode as barcode');
        $qb->addSelect('purchaseItem.quantity as quantity');
        $qb->addSelect('purchaseItem.purchasePrice as purchasePrice');
        $qb->addSelect('purchaseItem.salesPrice as salesPrice');
        $qb->where("p.inventoryConfig = :inventory");
        $qb->setParameter('inventory', $inventory);
        $qb->andWhere("purchaseItem.item = :item");
        $qb->setParameter('item', $id);
        $result = $qb->getQuery()->getResult();
        $data ='';

        foreach($result as $purchaseItem ) {
            //$strDate = strtotime($purchaseItem["receiveDate"]);
            $received = $purchaseItem["receiveDate"]->format('d-m-Y');
            $data .= '<tr>';
            $data .= '<td class="numeric" >'.$purchaseItem["barcode"].'</td>';
            $data .= '<td class="numeric" >'.$purchaseItem["sku"].'/'.$purchaseItem["skuSlug"].'</td>';
            $data .= '<td class="numeric" >'.$received.'/'.$purchaseItem["memo"] .'</td>';
            $data .= '<td class="numeric" >'.$purchaseItem["quantity"].'</td>';
            $data .= '<td class="numeric" >'.$purchaseItem["purchasePrice"].'</td>';
            $data .= '<td class="numeric" ><a class="editable" data-name="SalesPrice" href="#"  data-url="/inventory/purchaseitem/inline-update" data-type="text" data-pk="'.$purchaseItem["id"].'" data-original-title="Enter sales price">'.$purchaseItem["salesPrice"].'</a></td>';
            $data .= '<td class="numeric" ><a class="btn mini blue addSales" href="#" id="'.$purchaseItem["barcode"].'">Add Sales</a></td>';
            $data .= '</tr>';
        }

        return $data;

    }


}
