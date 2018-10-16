<?php

namespace Appstore\Bundle\HotelBundle\Repository;
use Appstore\Bundle\HotelBundle\Entity\HotelPurchase;
use Appstore\Bundle\HotelBundle\Entity\HotelPurchaseItem;
use Appstore\Bundle\HotelBundle\Entity\HotelParticular;
use Doctrine\ORM\EntityRepository;


/**
 * HotelPurchaseItemRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class HotelPurchaseItemRepository extends EntityRepository
{

    public function getPurchaseAveragePrice(HotelParticular $particular)
    {

        $qb = $this->_em->createQueryBuilder();
        $qb->from('HotelBundle:HotelPurchaseItem','e');
        $qb->select('AVG(e.purchasePrice) AS avgPurchasePrice');
        $qb->where('e.businessParticular = :particular')->setParameter('particular', $particular) ;
        $res = $qb->getQuery()->getOneOrNullResult();
        if(!empty($res)){
            $particular->setPurchaseAverage($res['avgPurchasePrice']);
            $this->_em->persist($particular);
            $this->_em->flush($particular);
        }
    }

    public function insertPurchaseItems($invoice, $data)
    {

    	$particular = $this->_em->getRepository('HotelBundle:HotelParticular')->find($data['particularId']);
        $em = $this->_em;
	    $purchasePrice = (isset($data['price']) and !empty($data['price']))? $data['price']:0;
        $entity = new HotelPurchaseItem();
        $entity->setHotelPurchase($invoice);
        $entity->setHotelParticular($particular);
        if(!empty($particular->getPrice())){
	        $entity->setSalesPrice($particular->getPrice());
        }
        $entity->setPurchasePrice($purchasePrice);
        $entity->setActualPurchasePrice($purchasePrice);
        $entity->setQuantity($data['quantity']);
        $entity->setPurchaseSubTotal($data['quantity'] * $entity->getPurchasePrice());
        $em->persist($entity);
        $em->flush();
        $this->getPurchaseAveragePrice($particular);

    }

    public function getPurchaseItems(HotelPurchase $sales)
    {
        $entities = $sales->getPurchaseItems();
        $data = '';
        $i = 1;

        /* @var $entity HotelPurchaseItem */

        foreach ($entities as $entity) {

            $data .= "<tr id='remove-{$entity->getId()}'>";
            $data .= "<td>{$i}</td>";
            $data .= "<td>{$entity->getHotelParticular()->getParticularCode()}</td>";
            $data .= "<td>{$entity->getHotelParticular()->getName()}</td>";
            $data .= "<td>{$entity->getSalesPrice()}</td>";
            $data .= "<td>{$entity->getPurchasePrice()}</td>";
            $data .= "<td>{$entity->getQuantity()}</td>";
            $data .= "<td>{$entity->getPurchaseSubTotal()}</td>";
            $data .= "<td><a id='{$entity->getId()}'  data-url='/business/purchase/{$sales->getId()}/{$entity->getId()}/particular-delete' href='javascript:' class='btn red mini delete' ><i class='icon-trash'></i></a></td>";
            $data .= '</tr>';
            $i++;
        }
        return $data;
    }

    public function purchaseStockItemUpdate(HotelParticular $stockItem)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.businessPurchase', 'mp');
        $qb->select('SUM(e.quantity) AS quantity');
        $qb->where('e.businessParticular = :particular')->setParameter('particular', $stockItem->getId());
        $qb->andWhere('mp.process = :process')->setParameter('process', 'Approved');
        $qnt = $qb->getQuery()->getOneOrNullResult();
        return $qnt['quantity'];
    }

    public function updatePurchaseItemPrice(HotelPurchase $purchase)
    {
        /* @var HotelPurchaseItem $item */

        foreach ($purchase->getHotelPurchaseItems() as $item){

            $em = $this->_em;
            $percentage = $purchase->getDiscountCalculation();
            $purchasePrice = $this->stockPurchaseItemPrice($percentage,$item->getActualPurchasePrice());
            $item->setPurchasePrice($purchasePrice);
            $em->persist($item);
            $em->flush();
        }
    }

    public function stockPurchaseItemPrice($percentage,$price)
    {
        $discount = (($price * $percentage )/100);
        $purchasePrice = ($price - $discount);
        return $purchasePrice;

    }
}