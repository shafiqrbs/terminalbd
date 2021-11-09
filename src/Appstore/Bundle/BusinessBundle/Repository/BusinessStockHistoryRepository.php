<?php

namespace Appstore\Bundle\BusinessBundle\Repository;
use Appstore\Bundle\BusinessBundle\Entity\BusinessConfig;
use Appstore\Bundle\BusinessBundle\Entity\BusinessDamage;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoiceAccessories;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoiceReturn;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoiceReturnItem;
use Appstore\Bundle\BusinessBundle\Entity\BusinessProduction;
use Appstore\Bundle\BusinessBundle\Entity\BusinessProductionElement;
use Appstore\Bundle\BusinessBundle\Entity\BusinessProductionExpense;
use Appstore\Bundle\BusinessBundle\Entity\BusinessPurchase;
use Appstore\Bundle\BusinessBundle\Entity\BusinessPurchaseItem;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoice;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoiceParticular;

use Appstore\Bundle\BusinessBundle\Entity\BusinessParticular;
use Appstore\Bundle\BusinessBundle\Entity\BusinessPurchaseReturn;
use Appstore\Bundle\BusinessBundle\Entity\BusinessPurchaseReturnItem;
use Appstore\Bundle\BusinessBundle\Entity\BusinessStockHistory;
use Appstore\Bundle\RestaurantBundle\Entity\ProductionElement;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Gregwar\Image\Image;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;


/**
 * BusinessParticularRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BusinessStockHistoryRepository extends EntityRepository
{

    public function getItemOpeningQuantity(BusinessParticular $item){
        $em = $this->_em;
        $qb = $this->createQueryBuilder('e');
        $qb->select('(COALESCE(SUM(e.quantity),0) AS openingQuantity');
        $qb->where("e.item = :item")->setParameter('item', $item->getId());
        $result = $qb->getQuery()->getSingleResult();
        $openingQuantity = $result['openingQuantity'];
        return $openingQuantity;

    }

    public function updateItemClosingQuantity(BusinessStockHistory $stock){
        $em = $this->_em;
        $closingQnt = ($stock->getOpeningQuantity() + $stock->getQuantity());
        $stock->setClosingQuantity($closingQnt);
        $em->persist($stock);
        $em->flush();

    }

    public function processStockQuantity($item , $fieldName = ''){

        $em = $this->_em;
        $openingQnt = 0;
        if($fieldName == "opening"){
            $openingQnt = $this->getItemOpeningQuantity($item);
        }else{
            $openingQnt = $this->getItemOpeningQuantity($item->getBusinessParticular());
        }

        /* @var  $entity BusinessParticular */

        $entity = new BusinessStockHistory();

        if($fieldName == 'purchase'){

            /* @var $item BusinessPurchaseItem */

            $exist = $this->findOneBy(array('purchaseItem' => $item));
            if($exist){ $entity = $exist; }
            $entity->setQuantity($item->getQuantity());
            $entity->setPurchaseQuantity($item->getQuantity());
            $entity->setItem($item->getBusinessParticular());
            $entity->setPurchaseItem($item);
            $entity->setProcess('purchase');


        }elseif($fieldName == 'purchase-return') {

            /* @var $item BusinessPurchaseReturnItem */

            $entity->setQuantity("-{$item->getQuantity()}");
            $entity->setPurchaseReturnQuantity($item->getQuantity());
            $entity->setItem($item->getBusinessParticular());
            $entity->setPurchaseReturnItem($item);
            $entity->setProcess('purchase-return');

        }elseif($fieldName == 'sales'){

            /* @var $item BusinessInvoiceParticular */

            $entity->setQuantity("-{$item->getTotalQuantity()}");
            $entity->setSalesQuantity($item->getTotalQuantity());
            $entity->setItem($item->getBusinessParticular());
            $entity->setSalesItem($item);
            $entity->setProcess('sales');

        }elseif($fieldName == 'sales-return'){

            /* @var $item BusinessInvoiceReturnItem */

            $entity->setQuantity($item->getQuantity());
            $entity->setSalesQuantity($item->getQuantity());
            $entity->setItem($item->getBusinessParticular());
            $entity->setSalesItem($item);
            $entity->setProcess('sales-return');

        }elseif($fieldName == 'damage') {

            /* @var $item BusinessDamage */

            $entity->setQuantity("-{$item->getQuantity()}");
            $entity->setDamageQuantity($item->getQuantity());
            $entity->setItem($item->getBusinessParticular());
            $entity->setDamageItem($item);
            $entity->setProcess('sales-return');

        }elseif($fieldName == 'opening') {

            $entity->setQuantity("{$item->getQuantity()}");
            $entity->setOpening($item->getQuantity());
            $entity->setItem($item->getBusinessParticular());
            $entity->setProcess('opening');

        }
        if($openingQnt){
            $entity->setOpeningQuantity(floatval($openingQnt));
        }else{
            $entity->setOpeningQuantity(0);
        }
        $closingQuantity = $entity->getQuantity() + $entity->getOpeningQuantity();
        $entity->setClosingQuantity(floatval($closingQuantity));
        $entity->setBusinessConfig($item->getBusinessParticular()->getBusinessConfig());
        $em->persist($entity);
        $em->flush();

    }

    public function processInsertPurchaseItem(BusinessPurchase $entity){

        $em = $this->_em;

        /** @var $item BusinessPurchaseItem  */

        if($entity->getPurchaseItems()){

            foreach($entity->getPurchaseItems() as $item ){
                $em->createQuery("DELETE BusinessBundle:BusinessStockHistory e WHERE e.purchaseItem = '{$item->getId()}'")->execute();
                $this->processStockQuantity($item,"purchase");
            }
        }
    }

    public function processReversePurchaseItem(BusinessPurchase $entity){

        $em = $this->_em;

        /** @var $item BusinessPurchaseItem  */

        if($entity->getPurchaseItems()){

            foreach($entity->getPurchaseItems() as $item ){
                $em->createQuery("DELETE BusinessBundle:BusinessStockHistory e WHERE e.purchaseItem = '{$item->getId()}'")->execute();
            }
        }
    }


    public function processInsertPurchaseReturnItem(BusinessPurchaseReturn $entity){

        $em = $this->_em;

        /** @var $item BusinessPurchaseReturnItem  */

        if($entity->getBusinessPurchaseReturnItems()){

            foreach($entity->getBusinessPurchaseReturnItems() as $item ){
                $em->createQuery("DELETE BusinessBundle:BusinessStockHistory e WHERE e.purchaseReturnItem = '{$item->getId()}'")->execute();
                if($item->getQuantity() > 0){
                    $this->processStockQuantity($item,"purchase-return");
                }
            }
        }
    }

    public function processInsertSalesItem(BusinessInvoice $entity){

        $em = $this->_em;

        /** @var $item BusinessInvoiceParticular  */

        if($entity->getBusinessInvoiceParticulars()){

            foreach($entity->getBusinessInvoiceParticulars() as $item ){
                $em->createQuery("DELETE BusinessBundle:BusinessStockHistory e WHERE e.salesItem = '{$item->getId()}'")->execute();
                if($item->getTotalQuantity() > 0){
                    $this->processStockQuantity($item,"sales");
                }
            }
        }
    }

    public function processReverseSalesItem(BusinessInvoice $entity){

        $em = $this->_em;

        /** @var $item BusinessInvoiceParticular  */

        if($entity->getBusinessInvoiceParticulars()){
            foreach($entity->getBusinessInvoiceParticulars() as $item ){
                $em->createQuery("DELETE BusinessBundle:BusinessStockHistory e WHERE e.salesItem = '{$item->getId()}'")->execute();
            }
        }
    }

    public function processInsertSalesReturnItem(BusinessInvoiceReturn $entity){

        $em = $this->_em;

        /** @var $item BusinessInvoiceReturnItem  */

        if($entity->getInvoiceReturnItems()){
            foreach($entity->getInvoiceReturnItems() as $item ){
                $em->createQuery("DELETE BusinessBundle:BusinessStockHistory e WHERE e.salesReturnItem = '{$item->getId()}'")->execute();
                if($item->getQuantity() > 0){
                    $this->processStockQuantity($item,"sales-return");
                }
            }
        }
    }

    public function processInsertDamageItem(BusinessInvoice $entity){

        $em = $this->_em;

        /** @var $item BusinessDamage  */

        if($entity->getBusinessInvoiceParticulars()){

            foreach($entity->getBusinessInvoiceParticulars() as $item ){
                $em->createQuery("DELETE BusinessBundle:BusinessStockHistory e WHERE e.damage = '{$item->getId()}'")->execute();
                if($item->getQuantity() > 0){
                    $this->processStockQuantity($item,"damage");
                }
            }
        }
    }

    public function openingDailyQuantity(BusinessConfig $config,$data)
    {

        $item = isset($data['name'])? $data['name'] :'';
        if(isset($data['startDate'])){
            $date = new \DateTime($data['startDate']);
        }else{
            $date = new \DateTime();
        }
        $date->add(\DateInterval::createFromDateString('yesterday'));
        $tillDate = $date->format('Y-m-d 23:59:59');
        $qb = $this->createQueryBuilder('e');
        $qb->select('SUM(e.quantity) as openingQnt');
        $qb->join("e.item",'i');
        $qb->where("i.id = :name")->setParameter('name',$item);
        $qb->andWhere("e.created <= :created")->setParameter('created', $tillDate);
        $lastCode = $qb->getQuery()->getOneOrNullResult()['openingQnt'];
        return $lastCode;

    }

    public function monthlyStockLedger(BusinessConfig $config , $data)
    {
        $config = $config->getId();
        $compare = new \DateTime();
        $month =  $compare->format('F');
        $year =  $compare->format('Y');
        $month = isset($data['month'])? $data['month'] :$month;
        $year = isset($data['year'])? $data['year'] :$year;
        $item = isset($data['name'])? $data['name'] :'';
        $sql = "SELECT DATE_FORMAT(e.created,'%d-%m-%Y') as date ,COALESCE(SUM(e.purchaseQuantity),0) as purchaseQuantity,COALESCE(SUM(e.purchaseReturnQuantity),0) as purchaseReturnQuantity,COALESCE(SUM(e.salesQuantity),0) as salesQuantity,COALESCE(SUM(e.salesReturnQuantity),0) as salesReturnQuantity,COALESCE(SUM(e.damageQuantity),0) as damageQuantity
                FROM business_stock_history as e
                JOIN business_particular ON e.item_id = business_particular.id
                WHERE e.businessConfig_id = :config AND business_particular.id = :item  AND MONTHNAME(e.created) =:month AND YEAR(e.created) =:year
                GROUP BY date";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('config', $config);
        $stmt->bindValue('item', "{$item}");
        $stmt->bindValue('month', $month);
        $stmt->bindValue('year', $year);
        $stmt->execute();
        $results =  $stmt->fetchAll();
        $arrays = [];
        foreach ($results as $result){
            $arrays[$result['date']] = $result;
        }
        return $arrays;
    }

    public function getStockHistoryLedger($config,$data)
    {
        $config = $config->getId();
        $compare = new \DateTime();
        $date = $compare->format('Y-m-d');
        $startDa = isset($data['startDate']) ? $data['startDate'] : $date;
        $start = new \DateTime($startDa);
        $startDate = $start->format('Y-m-d');
        $endDa = isset($data['endDate']) ? $data['endDate'] : $date;
        $end = new \DateTime($endDa);
        $endDate = $end->format('Y-m-d');
        $item = isset($data['particular']) ? $data['particular'] : '';
        $process = isset($data['process']) ? $data['process'] : '';
        if(empty($process)){
            $sql = "SELECT e.*,i.invoice as salesInvoice,c.name as customer,p.grn as grn,fu.username as createdBy
                FROM business_stock_history as e
                JOIN business_particular ON e.item_id = business_particular.id 
                LEFT JOIN fos_user as fu  ON e.createdBy_id = fu.id
                LEFT JOIN business_purchase_item as pi  ON e.purchaseItem_id = pi.id
                LEFT JOIN business_purchase as p  ON pi.businessPurchase_id = p.id
                LEFT JOIN business_purchase_return_item as pri  ON e.purchaseReturnItem_id = pri.id
                LEFT JOIN business_purchase_return as bpr  ON pri.businessPurchaseReturn_id = bpr.id
                LEFT JOIN business_invoice_particular as ip  ON e.salesItem_id = ip.id
                LEFT JOIN business_invoice as i  ON ip.businessInvoice_id = i.id
                LEFT JOIN Customer as c  ON i.customer_id = c.id
                LEFT JOIN business_invoice_return_item as sri  ON e.salesReturnItem_id  = sri.id
                LEFT JOIN business_invoice_return as bir  ON sri.invoiceReturn_id = bir.id
                LEFT JOIN business_damage as d  ON e.damageItem_id  = d.id
                WHERE e.businessConfig_id = :config AND business_particular.id = :item  AND e.created BETWEEN '{$startDate}' AND '{$endDate}' 
                ORDER BY e.created ASC";
            $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
            $stmt->bindValue('config', $config);
            $stmt->bindValue('item', "{$item}");
            $stmt->execute();
            $results = $stmt->fetchAll();
            return $results;
        }else{
            $sql = "SELECT e.*,i.invoice as salesInvoice,c.name as customer,p.grn as grn,fu.username as createdBy
                FROM business_stock_history as e
                JOIN business_particular ON e.item_id = business_particular.id 
                LEFT JOIN fos_user as fu  ON e.createdBy_id = fu.id
                LEFT JOIN business_purchase_item as pi  ON e.purchaseItem_id = pi.id
                LEFT JOIN business_purchase as p  ON pi.businessPurchase_id = p.id
                LEFT JOIN business_purchase_return_item as pri  ON e.purchaseReturnItem_id = pri.id
                LEFT JOIN business_purchase_return as bpr  ON pri.businessPurchaseReturn_id = bpr.id
                LEFT JOIN business_invoice_particular as ip  ON e.salesItem_id = ip.id
                LEFT JOIN business_invoice as i  ON ip.businessInvoice_id = i.id
                LEFT JOIN Customer as c  ON i.customer_id = c.id
                LEFT JOIN business_invoice_return_item as sri  ON e.salesReturnItem_id  = sri.id
                LEFT JOIN business_invoice_return as bir  ON sri.invoiceReturn_id = bir.id
                LEFT JOIN business_damage as d  ON e.damageItem_id  = d.id
                WHERE e.businessConfig_id = :config AND business_particular.id = :item  AND e.created BETWEEN '{$startDate}' AND '{$endDate}' 
                AND e.process='{$process}' ORDER BY e.created ASC ";
            $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
            $stmt->bindValue('config', $config);
            $stmt->bindValue('item', "{$item}");
            $stmt->execute();
            $results = $stmt->fetchAll();
            return $results;
        }


    }


}
