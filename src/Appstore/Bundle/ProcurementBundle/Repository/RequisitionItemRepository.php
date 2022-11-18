<?php

namespace Appstore\Bundle\ProcurementBundle\Repository;
use Appstore\Bundle\AssetsBundle\Entity\Purchase;
use Appstore\Bundle\AssetsBundle\Entity\PurchaseItem;
use Appstore\Bundle\AssetsBundle\Entity\Item;
use Appstore\Bundle\AssetsBundle\Entity\AssetsConfig;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;



/**
 * ExpenditureItemRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class RequisitionItemRepository extends EntityRepository
{

    protected function handleSearchBetween($qb,$data)
    {

        $grn = isset($data['grn'])? $data['grn'] :'';
        $vendor = isset($data['vendor'])? $data['vendor'] :'';
        $business = isset($data['name'])? $data['name'] :'';
        $brand = isset($data['brandName'])? $data['brandName'] :'';
        $process = isset($data['process'])? $data['process'] :'';
        $vendorId = isset($data['vendorId'])? $data['vendorId'] :'';
        $startDate = isset($data['startDate'])? $data['startDate'] :'';
        $endDate = isset($data['endDate'])? $data['endDate'] :'';

        if (!empty($grn)) {
            $qb->andWhere($qb->expr()->like("e.grn", "'%$grn%'"  ));
        }
        if(!empty($business)){
            $qb->andWhere($qb->expr()->like("ms.name", "'%$business%'"  ));
        }
        if(!empty($brand)){
            $qb->andWhere($qb->expr()->like("ms.brandName", "'%$brand%'"  ));
        }
        if(!empty($process)){
            $qb->andWhere($qb->expr()->like("e.process", "'%$process%'"  ));
        }
        if(!empty($vendor)){
            $qb->join('e.vendor','v');
            $qb->andWhere($qb->expr()->like("v.companyName", "'%$vendor%'"  ));
        }
        if(!empty($vendorId)){
            $qb->join('e.vendor','v');
            $qb->andWhere("v.id = :vendorId")->setParameter('vendorId', $vendorId);
        }
        if (!empty($startDate) ) {
            $datetime = new \DateTime($data['startDate']);
            $start = $datetime->format('Y-m-d 00:00:00');
            $qb->andWhere("e.receiveDate >= :startDate");
            $qb->setParameter('startDate', $start);
        }

        if (!empty($endDate)) {
            $datetime = new \DateTime($data['endDate']);
            $end = $datetime->format('Y-m-d 23:59:59');
            $qb->andWhere("e.receiveDate <= :endDate");
            $qb->setParameter('endDate', $end);
        }
    }

    public function findWithSearch($config, $mode , $data)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->where('e.config = :config')->setParameter('config', $config) ;
        $qb->andWhere('e.mode = :mode')->setParameter('mode', $mode) ;
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('e.created','DESC');
        $result = $qb->getQuery();
        return  $result;
    }

    public function findWithVatItemSearch($config, $mode , $data)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.item','item');
        $qb->where('e.config = :config')->setParameter('config', $config) ;
        $qb->andWhere('e.mode = :mode')->setParameter('mode', $mode) ;
        $qb->andWhere('item.vatProduct IS NOT NULL');
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('e.created','DESC');
        $result = $qb->getQuery();
        return  $result;
    }


    public function updatePurchaseTotalPrice(Purchase $entity)
    {
        $em = $this->_em;
        $total = $this->createQueryBuilder('si')
            ->join('si.assetsPurchase','e')
            ->select('sum(si.subTotal) as total','sum(si.rebate) as rebate','sum(si.valueAddedTax) as valueAddedTax','sum(si.totalTaxIncidence) as totalTaxIncidence')
            ->where('e.id = :entity')
            ->setParameter('entity', $entity ->getId())
            ->getQuery()->getOneOrNullResult();

        if($total['total'] > 0){
            $subTotal = $total['total'];
            $entity->setSubTotal($subTotal);
            $entity->setValueAddedTax($total['valueAddedTax']);
            $entity->setRebate($total['rebate']);
            $entity->setTotalTaxIncidence($total['totalTaxIncidence']);
            $entity->setNetTotal($subTotal + $total['totalTaxIncidence'] - $total['rebate']);
        }else{
            $entity->setSubTotal(0);
            $entity->setNetTotal(0);
            $entity->setValueAddedTax(0);
            $entity->setTotalTaxIncidence(0);
        }

        $em->persist($entity);
        $em->flush();
        return $entity;

    }

    private function getTaxTariffCalculation($subTotal,$tariff)
    {
        $value = 0;
        $value = (($subTotal * $tariff)/100);
        return $value;
    }

    private function salesPriceCalculation(AssetsConfig $config , $price)
    {
        $percent = (($price * $config->getProfitPercent())/100);
        $salesPrice = ($price + $percent);
        return $salesPrice;
    }

    public function updatePurchaseItemPrice(PurchaseItem $entity)
    {
        $em = $this->_em;
        $subTotal = $entity->getSubTotal();
        $product = $entity->getItem();
        $entity->setTotal($entity->getSubTotal() + $entity->getTotalTaxIncidence());
        $purchasePrice = ($entity->getTotal() / $entity->getQuantity());
        $entity->setPurchasePrice($purchasePrice);
        $salesPrice = $this->salesPriceCalculation($entity->getConfig(),$purchasePrice);
        $entity->setSalesPrice($salesPrice);
        $em->persist($entity);
        $em->flush($entity);

    }


    public function getPurchaseItems(Purchase $sales)
    {
        $entities = $sales->getPurchaseItems();
        $data = '';
        $i = 1;

        /* @var $entity PurchaseItem */

        foreach ($entities as $entity) {
            $total = ($entity->getSubTotal() + $entity->getTotalTaxIncidence() - $entity->getRebate());
            $data .= "<tr id='remove-{$entity->getId()}'>";
            $data .= "<td>{$i}</td>";
            $data .= "<td>{$entity->getBarcode()}</td>";
            $data .= "<td>{$entity->getItem()->getName()}</td>";
            $data .= "<td>{$entity->getName()}</td>";
            $data .= "<td>{$entity->getQuantity()}</td>";
            $data .= "<td>{$entity->getPrice()}</td>";
            $data .= "<td>{$entity->getSubTotal()}</td>";
            $data .= "<td><a id='{$entity->getId()}'  data-url='/assets/purchase/{$sales->getId()}/{$entity->getId()}/item-delete' href='javascript:' class='btn red mini item-delete' ><i class='icon-trash'></i></a></td>";
            $data .= '</tr>';
            $i++;

        }
        return $data;
    }

    public function stockPurchaseItemPrice($percentage,$price)
    {
        $discount = (($price * $percentage )/100);
        $purchasePrice = ($price - $discount);
        return $purchasePrice;

    }

    public function purchaseItemUpdate(PurchaseItem $item,$fieldName)
    {
        $qb = $this->createQueryBuilder('e');
        if($fieldName == 'sales'){
            $qb->select('e.salesQuantity AS quantity');
        }elseif($fieldName == 'sales-return'){
            $qb->select('e.quantity AS quantity');
        }elseif($fieldName == 'sales-return'){
            $qb->select('e.quantity AS quantity');
        }elseif($fieldName == 'sales-return'){
            $qb->select('e.quantity AS quantity');
        }else{
            $qb->select('SUM(e.quantity) AS quantity');
        }
        $qb->addSelect('e.remainingQuantity AS remainingQuantity');
        $qb->where('e.id = :item')->setParameter('item', $item->getId());
        $qnt = $qb->getQuery()->getOneOrNullResult();
        return $qnt;
    }

    public function purchaseStockItemUpdate(Item $stockItem)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.item', 'mp');
        $qb->select('SUM(e.quantity) AS quantity');
        $qb->where('e.item = :item')->setParameter('item', $stockItem->getId());
        $qb->andWhere('e.process = :process')->setParameter('process', 'Approved');
        $qb->andWhere('e.mode = :mode')->setParameter('mode', 'purchase');
        $qnt = $qb->getQuery()->getOneOrNullResult();
        return $qnt['quantity'];
    }

     public function openingStockItemUpdate(Item $stockItem)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.item', 'mp');
        $qb->select('SUM(e.quantity) AS quantity');
        $qb->where('e.item = :item')->setParameter('item', $stockItem->getId());
        $qb->andWhere('e.process = :process')->setParameter('process', 'Approved');
    //    $qb->andWhere('e.mode = :mode')->setParameter('mode', 'opening');
        $qnt = $qb->getQuery()->getOneOrNullResult();
        return $qnt['quantity'];
    }

    public function insertProductSerialNo(Purchase $purchase)
    {
        foreach ($purchase->getPurchaseItems() as $item):
            $this->generateSerialNo($item);
        endforeach;
    }

    public function generateSerialNo(PurchaseItem $entity){

        /* @var $product Item */

        $em = $this->_em;
        $product = $entity->getitem();

        $prefix = !empty($product->getItemPrefix()) ? $product->getItemPrefix().'-':'';
        $format = $product->getSerialFormat();
        $generation = $product->getSerialGeneration();

        if($generation == 'auto' and empty($entity->getExternalSerial())){
            $serialNos = array();
            for($qnt = 1; $entity->getQuantity() >= $qnt; $qnt++ ){
                $generate = str_pad($qnt,$format, '0', STR_PAD_LEFT);
                $serialNos[] = $prefix.$entity->getBarcode().'/'.$generate;
            }
            $entity->setInternalSerial($serialNos);
            $comma_separated = implode(",", $serialNos);
            $entity->setExternalSerial($comma_separated);
            $em->persist($entity);
            $em->flush($entity);

        }elseif($generation == 'manual' and empty($entity->getExternalSerial())){

            $serialNos = array();
            for($qnt = 1; $entity->getQuantity() >= $qnt; $qnt++ ){
                $generate = str_pad($qnt,$format, '0', STR_PAD_LEFT);
                $serialNos[] = $prefix.$entity->getBarcode().'/'.$generate;
            }
            $entity->setInternalSerial($serialNos);
            $comma_separated = implode(",", $serialNos);
            $entity->setExternalSerial($comma_separated);
            $em->persist($entity);
            $em->flush($entity);
        }
    }


    public function getManualSalesItem($inventory,$data)
    {

        $qb = $this->createQueryBuilder('pi');
        $qb->join('pi.item', 'item');
        $qb->join('pi.purchase', 'purchase');
        $qb->join('purchase.inventoryConfig', 'ic');
        $qb->select('pi');
        $qb->where($qb->expr()->in("pi.id", $data ));
        $qb->andWhere("ic.id = :inventory");
        $qb->setParameter('inventory', $inventory->getId());
        $qb->orderBy('item.name','ASC');
        return $qb->getQuery()->getResult();

    }

    public function returnPurchaseItemDetails($config,$barcode)
    {

        $qb = $this->createQueryBuilder('pi');
        $qb->join('pi.stockItems', 'stock');
        $qb->select('pi.id');
        $qb->addSelect('SUM(stock.quantity) as remainingQuantity');
        $qb->where("pi.barcode = :barcode" )->setParameter('barcode', $barcode);
        $qb->andWhere("pi.config = :config")->setParameter('config', $config);
        return $qb->getQuery()->getSingleResult();

    }

    public function searchAutoComplete($item,$config)
    {

        $qb = $this->createQueryBuilder('pi');
        $qb->join('pi.stockItems', 'stockitem');
        $qb->select('pi.barcode as id');
        $qb->addSelect('pi.barcode as text');
        $qb->addSelect('SUM(stockitem.quantity) as item_name');
        $qb->where($qb->expr()->like("pi.barcode", "'$item%'"  ));
        $qb->andWhere("pi.config = :config");
        $qb->setParameter('config', $config);
        $qb->orderBy('pi.updated', 'ASC');
        $qb->setMaxResults( '10' );
        return $qb->getQuery()->getResult();

    }

    public function findWithItemReceive($config,$data)
    {

        $vendor = isset($data['vendor'])? $data['vendor'] :'';
        $grn = isset($data['grn'])? $data['grn'] :'';
        $qb = $this->createQueryBuilder('item');
        $qb->select('item.id as id','item.remainingQuantity as remainingQuantity','item.quantity as quantity','item.receiveQuantity as receiveQuantity');
        $qb->addSelect('p.grn as grn','p.updated as updated');
        $qb->addSelect('i.name as itemName','c.name as category');
        $qb->join('item.item','i');
        $qb->join('i.category','c');
        $qb->join('item.assetsPurchase','p');
        $qb->where("p.config = :config")->setParameter('config', $config);
        // $qb->andWhere("item.remainingQuantity > 0");
        $qb->andWhere("p.vendor = :vendor")->setParameter('vendor', $vendor);
        if($grn){
            $qb->andWhere("p.grn = :grn")->setParameter('grn', $grn);
        }
        $qb->orderBy('p.updated','DESC');
        $qb->getQuery();
        return  $qb;

    }

}
