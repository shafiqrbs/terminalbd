<?php

namespace Appstore\Bundle\BusinessBundle\Repository;
use Appstore\Bundle\BusinessBundle\Entity\BusinessConfig;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoiceAccessories;
use Appstore\Bundle\BusinessBundle\Entity\BusinessProduction;
use Appstore\Bundle\BusinessBundle\Entity\BusinessProductionElement;
use Appstore\Bundle\BusinessBundle\Entity\BusinessProductionExpense;
use Appstore\Bundle\BusinessBundle\Entity\BusinessPurchase;
use Appstore\Bundle\BusinessBundle\Entity\BusinessPurchaseItem;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoice;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoiceParticular;

use Appstore\Bundle\BusinessBundle\Entity\BusinessParticular;
use Appstore\Bundle\RestaurantBundle\Entity\ProductionElement;
use Doctrine\ORM\EntityRepository;
use Gregwar\Image\Image;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;


/**
 * BusinessParticularRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BusinessParticularRepository extends EntityRepository
{

    public function getServiceLists(BusinessConfig $config)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.service','service');
        $qb->where('e.businessConfig = :config');
        $qb->setParameter('config',$config);
        $qb->andWhere('service.dentalService is null');
        $qb->orderBy('service.name , e.name','ASC');
        $result = $qb->getQuery()->getResult();
        return $result;
    }

    public function updateSalesPrice(BusinessParticular $particular)
    {
	    $price = $this->_em->getRepository('BusinessBundle:BusinessProductionElement')->getProductPurchaseSalesPrice($particular);
	    $salesPrice =  empty($price['salesPrice']) ? 0 : $price['salesPrice'];
	    $purchasePrice = empty($price['purchasePrice']) ? 0 : $price['purchasePrice'];
	    $particular->setSalesPrice($salesPrice);
    	$particular->setPurchasePrice($purchasePrice);
    	$particular->setProductionSalesPrice($salesPrice);
	    $this->_em->flush();
	    return $particular;
    }

    public function searchAutoComplete(BusinessConfig $config,$q)
    {
        $query = $this->createQueryBuilder('e');
	    $query->select('e.name as id');
	    $query->addSelect('e.name as text');
        $query->where($query->expr()->like("e.name", "'$q%'"  ));
        $query->andWhere("e.businessConfig = :config");
        $query->setParameter('config', $config->getId());
        $query->groupBy('e.name');
        $query->orderBy('e.name', 'ASC');
        $query->setMaxResults( '10' );
        return $query->getQuery()->getResult();
    }

    public function findWithSearch($config, $data){

        $name = isset($data['name'])? $data['name'] :'';
        $category = isset($data['category'])? $data['category'] :'';
        $type = isset($data['type'])? $data['type'] :'';
        $qb = $this->createQueryBuilder('e');
        $qb->where('e.businessConfig = :config')->setParameter('config', $config) ;
        if (!empty($name)) {
            $qb->andWhere($qb->expr()->like("e.name", "'%$name%'"  ));
        }
        if(!empty($category)){
        	$qb->andWhere("e.category = :category");
            $qb->setParameter('category', $category);
        }
        if(!empty($type)){
            $qb->andWhere("e.businessParticularType = :type");
            $qb->setParameter('type', $type);
        }
        $qb->orderBy('e.name','ASC');
        $qb->getQuery();
        return  $qb;
    }

    public function getApiStock(GlobalOption $option)
    {
        $config = $option->getBusinessConfig();
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.category','c');
        $qb->leftJoin('e.unit','u');
        $qb->select('e.id as stockId','e.name as name','e.quantity as quantity','e.salesPrice as salesPrice','e.purchasePrice as purchasePrice','e.path as path');
        $qb->addSelect('u.id as unitId','u.name as unitName');
        $qb->addSelect('c.id as categoryId','c.name as categoryName');
        $qb->where('e.businessConfig = :config');
        $qb->setParameter('config',$config);
        $qb->orderBy('c.name , e.name','ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach($result as $key => $row) {

            $data[$key]['global_id']            = (int) $option->getId();
            $data[$key]['item_id']              = (int) $row['stockId'];

            $data[$key]['category_id']          = $row['categoryId'];
            $data[$key]['categoryName']         = $row['categoryName'];
            if ($row['unitId']){
                $data[$key]['unit_id']          = $row['unitId'];
                $data[$key]['unit']             = $row['unitName'];
            }else{
                $data[$key]['unit_id']          = 0;
                $data[$key]['unit']             = '';
            }
            $data[$key]['name']                 = $row['name'];
            $data[$key]['printName']            = $row['name'];
            $data[$key]['quantity']             = $row['quantity'];
            $data[$key]['salesPrice']           = $row['salesPrice'];
            $data[$key]['purchasePrice']        = $row['purchasePrice'];
            $data[$key]['printHidden']          = "";
            if($row['path']){
                $path = $this->resizeFilter("uploads/domain/{$option->getId()}/product/{$row['path']}");
                $data[$key]['imagePath']            =  $path;
            }else{
                $data[$key]['imagePath']            = "";
            }

        }
        return $data;
    }
    public function resizeFilter($pathToImage, $width = 256, $height = 256)
    {
        $path = '/' . Image::open(__DIR__.'/../../../../../web/' . $pathToImage)->cropResize($width, $height, 'transparent', 'top', 'left')->guess();
        return $_SERVER['HTTP_HOST'].$path;
    }

    public function getFindWithParticular($config,$type){

        $qb = $this->createQueryBuilder('e')
            ->join('e.businessParticularType','p')
            ->where('e.businessConfig = :config')->setParameter('config', $config)
            ->andWhere('e.status = :status')->setParameter('status', 1)
            ->andWhere('p.slug IN(:type)')->setParameter('type',array_values($type))
            ->orderBy('e.sorting','ASC')
            ->orderBy('e.name','ASC')
            ->getQuery()->getResult();
            return  $qb;
    }



    public function getServiceWithParticular($config,$services){

        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.service','s')
            ->select('e.id')
            ->addSelect('e.name')
            ->addSelect('e.particularCode')
            ->addSelect('e.price')
            ->addSelect('e.minimumPrice')
            ->addSelect('e.quantity')
            ->addSelect('s.name as serviceName')
            ->addSelect('s.code as serviceCode')
            ->where('e.businessConfig = :config')->setParameter('config', $config)
            ->andWhere('s.slug IN(:slugs)')
            ->setParameter('slugs',array_values($services))
            ->orderBy('e.service','ASC')
            ->getQuery()->getArrayResult();
            return  $qb;
    }

    public function getMedicineParticular($config,$services,$data = array()){

        $qb = $this->createQueryBuilder('e');
            $qb->leftJoin('e.service','s');
            $qb->leftJoin('e.unit','u');
            $qb->select('e.id');
            $qb->addSelect('e.name');
            $qb->addSelect('e.particularCode');
            $qb->addSelect('e.price');
            $qb->addSelect('e.minimumPrice');
            $qb->addSelect('e.quantity');
            $qb->addSelect('e.status');
            $qb->addSelect('e.salesQuantity');
            $qb->addSelect('e.minQuantity');
            $qb->addSelect('e.openingQuantity');
            $qb->addSelect('u.name as unit');
            $qb->addSelect('s.serviceFormat as serviceFormat');
            $qb->addSelect('s.name as serviceName');
            $qb->addSelect('s.code as serviceCode');
            $qb->addSelect('e.purchasePrice');
            $qb->addSelect('e.purchaseQuantity');
            $qb->where('e.businessConfig = :config')->setParameter('config', $config);
            $qb->andWhere('s.serviceFormat IN(:process)');
            $qb->setParameter('process',$services);
            if(!empty($data['particular'])) {
                $qb->andWhere('e.id =:particularId');
                $qb->setParameter('particularId', $data['particular']);
            }
            $qb->orderBy('e.name','ASC');
            $result = $qb->getQuery();
            return  $result;
    }

    public function getAccessoriesParticular($config,$services){

        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.service','s')
            ->leftJoin('e.unit','u')
            ->select('e.id')
            ->addSelect('e.name')
            ->addSelect('e.particularCode')
            ->addSelect('e.status')
            ->addSelect('e.salesQuantity')
            ->addSelect('e.openingQuantity')
            ->addSelect('u.name as unit')
            ->addSelect('e.purchaseQuantity')
            ->where('e.businessConfig = :config')->setParameter('config', $config)
            ->andWhere('s.serviceFormat IN(:process)')
            ->setParameter('process',$services)
            ->orderBy('e.name','ASC')
            ->getQuery()->getArrayResult();
            return  $qb;
    }

    public function findBusinessExistingCustomer($hospital, $mobile,$data)
    {
    }

    public function getPurchaseUpdateQntx(BusinessPurchase $purchase){

        $em = $this->_em;

        /** @var BusinessPurchaseItem $purchaseItem */

        foreach($purchase->getPurchaseItems() as $purchaseItem ){

            /** @var BusinessParticular  $particular */

            $particular = $purchaseItem->getBusinessParticular();
            $qnt = ($particular->getPurchaseQuantity() + $purchaseItem->getQuantity());
            $particular->setPurchaseQuantity($qnt);
            $em->persist($particular);
            $em->flush();

        }
    }

    public function getPurchaseUpdateQnt(BusinessPurchase $purchase){

        /** @var  $purchaseItem BusinessPurchaseItem */

        if(!empty($purchase->getBusinessPurchaseItems())) {
            foreach ($purchase->getBusinessPurchaseItems() as $purchaseItem) {
                $stockItem = $purchaseItem->getBusinessParticular();
                $this->updateRemoveStockQuantity($stockItem);
            }
        }
    }

	public function updateRemovePurchaseQuantity(BusinessInvoiceParticular $invoice_particular,$fieldName=''){

    	if($invoice_particular->getBusinessParticular()->getBusinessParticularType()->getSlug() == 'production'){
		    $this->updateRemoveProductionQuantity($invoice_particular,$fieldName);
	    }else{
		    $this->updateRemoveStockQuantity($invoice_particular->getBusinessParticular(),$fieldName);
	    }

    }

    public function updateRemoveStockQuantity(BusinessParticular $stock,$fieldName=''){

        $em = $this->_em;
        if($fieldName == 'sales'){
            $qnt = $em->getRepository('BusinessBundle:BusinessInvoiceParticular')->salesStockItemUpdate($stock);
            $stock->setSalesQuantity($qnt);
            $bonusQnt = $em->getRepository('BusinessBundle:BusinessInvoiceParticular')->bonusStockItemUpdate($stock);
            $stock->setBonusSalesQuantity($bonusQnt);
        }elseif($fieldName == 'sales-return'){
            $quantity = $this->_em->getRepository('BusinessBundle:BusinessInvoiceReturn')->salesReturnStockUpdate($stock);
            $stock->setSalesReturnQuantity($quantity);
        }elseif($fieldName == 'purchase-return'){
            $qnt = $em->getRepository('BusinessBundle:BusinessPurchaseReturnItem')->purchaseReturnStockUpdate($stock);
            $stock->setPurchaseReturnQuantity($qnt);
        }elseif($fieldName == 'damage'){
            $quantity = $em->getRepository('BusinessBundle:BusinessDamage')->damageStockItemUpdate($stock);
            $stock->setDamageQuantity($quantity);
        }else{
            $qnt = $em->getRepository('BusinessBundle:BusinessPurchaseItem')->purchaseStockItemUpdate($stock);
            $stock->setPurchaseQuantity($qnt);
            $bonusQnt = $em->getRepository('BusinessBundle:BusinessPurchaseItem')->bonusStockItemUpdate($stock);
            $stock->setBonusPurchaseQuantity($bonusQnt);
        }
        $em->persist($stock);
        $em->flush();
        $this->remainingQnt($stock);
    }

	public function updateRemoveProductionQuantity(BusinessInvoiceParticular $invoice_particular,$fieldName=''){

		$em = $this->_em;

		/* @var $entity BusinessProductionElement */

		foreach ($invoice_particular->getBusinessParticular()->getProductionElements() as $entity){

			$production = $entity->getParticular();

			if($fieldName == 'sales'){
				$qnt = $em->getRepository('BusinessBundle:BusinessInvoiceParticular')->salesStockItemProduction($invoice_particular,$entity);
				$production->setSalesQuantity($qnt);
			}elseif($fieldName == 'sales-return'){
				$quantity = $em->getRepository('BusinessBundle:BusinessInvoiceReturn')->salesStockItemProduction($invoice_particular,$entity);
				$production->setSalesReturnQuantity($quantity);
			}elseif($fieldName == 'damage'){
				$quantity = $em->getRepository('BusinessBundle:BusinessDamage')->salesStockItemProduction($invoice_particular,$entity);
				$production->setDamageQuantity($quantity);
			}
			$em->persist($production);
			$em->flush();
			$this->remainingQnt($production);
		}

	}

    public function remainingQnt(BusinessParticular $stock)
    {
        $em = $this->_em;
        $qnt = ($stock->getOpeningQuantity() + $stock->getPurchaseQuantity() + $stock->getSalesReturnQuantity() + $stock->getTransferQuantity()) - ($stock->getPurchaseReturnQuantity() + $stock->getSalesQuantity()+ $stock->getDamageQuantity());
        $stock->setRemainingQuantity($qnt);
        $stock->setBonusQuantity($stock->getBonusPurchaseQuantity() - $stock->getBonusSalesQuantity());
        $em->persist($stock);
        $em->flush();
    }

    public function insertInvoiceProductItem(BusinessInvoice $invoice){

	    $em = $this->_em;
        if(!empty($invoice->getBusinessInvoiceParticulars())) {

            /* @var  $item BusinessInvoiceParticular */

            foreach ($invoice->getBusinessInvoiceParticulars() as $item) {
				if(!empty($item->getBusinessParticular())) {

					if ( $item->getBusinessParticular()->getBusinessParticularType()->getSlug() == 'post-production') {
						$this->productionExpense( $item );
					}
					$this->getSalesUpdateQnt( $item );
					if($item->getBonusQnt() > 0){
                        $this->getSalesUpdateBonusQnt( $item );
                    }
				}
            }
        }

    }

    public function productionExpense(BusinessInvoiceParticular  $item)
    {
       if(!empty($item->getBusinessParticular()->getProductionElements())){

           $productionElements = $item->getBusinessParticular()->getProductionElements();

           /* @var $element BusinessProductionElement */

           if($productionElements) {

               foreach ($productionElements as $element) {

                   $entity = new BusinessProductionExpense();
                   $entity->setBusinessInvoiceParticular($item);
                   $entity->setProductionItem($item->getBusinessParticular());
                   $entity->setProductionElement($element->getParticular());
                   $entity->setPurchasePrice($element->getPurchasePrice());
                   $entity->setSalesPrice($element->getSalesPrice());
	               if(!empty($element->getParticular()->getUnit()) and ($element->getParticular()->getUnit()->getName() == 'Sft')) {
		               $entity->setQuantity($item->getTotalQuantity());
	               }else{
		               $entity->setQuantity($element->getQuantity() * $item->getQuantity());
	               }
                   $this->_em->persist($entity);
                   $this->_em->flush();
                   $this->salesProductionQnt($element);
               }
           }
       }
    }

	public function salesProductionQnt( BusinessProductionElement  $element){

        $em = $this->_em;
        $particular = $element->getParticular();
		$productionQnt = $this->getSumTotalProductionItemQuantity($particular);
		$invoiceQnt = $this->getSumTotalInvoiceItemQuantity($particular);
        $qnt = $productionQnt + $invoiceQnt;
        $particular->setSalesQuantity($qnt);
        $em->persist($particular);
        $em->flush();
		$this->remainingQnt($particular);
    }

    public function getSumTotalProductionItemQuantity(BusinessParticular $particular){

	    $qb = $this->_em->createQueryBuilder();
	    $qb->from('BusinessBundle:BusinessProductionExpense','e');
	    $qb->select('COALESCE(SUM(e.quantity),0) AS quantity');
	    $qb->where('e.productionElement = :particular')->setParameter('particular', $particular->getId());
	    $qnt = $qb->getQuery()->getOneOrNullResult();
	    $productionQnt = ($qnt['quantity'] == 0 ) ? 0 : $qnt['quantity'];
	    return $productionQnt;

    }

	public function getSumTotalInvoiceItemQuantity($particular){

		$qb = $this->_em->createQueryBuilder();
		$qb->from('BusinessBundle:BusinessInvoiceParticular','e');
        $qb->join('e.businessInvoice','i');
		$qb->select('COALESCE(SUM(e.totalQuantity),0) AS quantity');
		$qb->where('e.businessParticular = :particular')->setParameter('particular', $particular->getId());
		$qb->andWhere('i.process IN (:process)')->setParameter('process', array('Done','Delivered'));
		$qnt = $qb->getQuery()->getOneOrNullResult();
		$invoiceQnt = ($qnt['quantity'] > 0 ) ? $qnt['quantity'] : 0;
		return $invoiceQnt;

	}

    public function getSumTotalInvoiceItemBonusQuantity($particular){

		$qb = $this->_em->createQueryBuilder();
		$qb->from('BusinessBundle:BusinessInvoiceParticular','e');
        $qb->join('e.businessInvoice','i');
		$qb->select(' COALESCE(SUM(e.bonusQnt),0) AS quantity');
		$qb->where('e.businessParticular = :particular')->setParameter('particular', $particular->getId());
		$qb->andWhere('i.process IN (:process)')->setParameter('process', array('Done','Delivered'));
		$qnt = $qb->getQuery()->getOneOrNullResult();
		$invoiceQnt = ($qnt['quantity'] == 'NULL') ? 0 : $qnt['quantity'];
		return $invoiceQnt;

	}


	public function getSalesUpdateQnt(BusinessInvoiceParticular  $item){

        $em = $this->_em;
        $particular = $item->getBusinessParticular();
		$productionQnt = $this->getSumTotalProductionItemQuantity($particular);
		$invoiceQnt = $this->getSumTotalInvoiceItemQuantity($particular);
		$qnt = $productionQnt + $invoiceQnt;
		$particular->setSalesQuantity($qnt);
        $em->persist($particular);
        $em->flush();
	    $this->remainingQnt($particular);
    }

    public function getSalesUpdateBonusQnt(BusinessInvoiceParticular  $item){

        $em = $this->_em;
        $particular = $item->getBusinessParticular();
        $invoiceQnt = $this->getSumTotalInvoiceItemBonusQuantity($particular);
        $particular->setSalesQuantity($invoiceQnt);
        $em->persist($particular);
        $em->flush();
        $this->remainingQnt($particular);
    }


}
