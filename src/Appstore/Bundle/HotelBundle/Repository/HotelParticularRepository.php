<?php

namespace Appstore\Bundle\HotelBundle\Repository;
use Appstore\Bundle\HotelBundle\Entity\HotelConfig;
use Appstore\Bundle\HotelBundle\Entity\HotelInvoiceAccessories;
use Appstore\Bundle\HotelBundle\Entity\HotelProductionElement;
use Appstore\Bundle\HotelBundle\Entity\HotelProductionExpense;
use Appstore\Bundle\HotelBundle\Entity\HotelPurchase;
use Appstore\Bundle\HotelBundle\Entity\HotelPurchaseItem;
use Appstore\Bundle\HotelBundle\Entity\HotelInvoice;
use Appstore\Bundle\HotelBundle\Entity\HotelInvoiceParticular;

use Appstore\Bundle\HotelBundle\Entity\HotelParticular;
use Appstore\Bundle\RestaurantBundle\Entity\ProductionElement;
use Doctrine\ORM\EntityRepository;


/**
 * HotelParticularRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class HotelParticularRepository extends EntityRepository
{

    public function getServiceLists(HotelConfig $config)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.service','service');
        $qb->where('e.hotelConfig = :config');
        $qb->setParameter('config',$config);
        $qb->andWhere('service.dentalService is null');
        $qb->orderBy('service.name , e.name','ASC');
        $result = $qb->getQuery()->getResult();
        return $result;
    }

    public function updateSalesPrice(HotelParticular $particular)
    {
	    $price = $this->_em->getRepository('HotelBundle:HotelProductionElement')->getProductPurchaseSalesPrice($particular);
	    $salesPrice =  empty($price['salesPrice']) ? 0 : $price['salesPrice'];
	    $purchasePrice = empty($price['purchasePrice']) ? 0 : $price['purchasePrice'];
	    $particular->setSalesPrice($salesPrice);
    	$particular->setPurchasePrice($purchasePrice);
    	$particular->setProductionSalesPrice($salesPrice);
	    $this->_em->flush();
	    return $particular;
    }

    public function searchAutoComplete(HotelConfig $config,$q)
    {
        $query = $this->createQueryBuilder('e');
	    $query->select('e.name as id');
	    $query->addSelect('e.name as text');
        $query->where($query->expr()->like("e.name", "'$q%'"  ));
        $query->andWhere("e.hotelConfig = :config");
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
        $qb->where('e.hotelConfig = :config')->setParameter('config', $config) ;
        if (!empty($name)) {
            $qb->andWhere($qb->expr()->like("e.name", "'%$name%'"  ));
        }
        if(!empty($category)){
        	$qb->andWhere("e.category = :category");
            $qb->setParameter('category', $category);
        }
        if(!empty($type)){
            $qb->andWhere("e.hotelParticularType = :type");
            $qb->setParameter('type', $type);
        }
        $qb->orderBy('e.name','ASC');
        $qb->getQuery();
        return  $qb;
    }

    public function getFindWithParticular($config,$type){

        $qb = $this->createQueryBuilder('e')
            ->join('e.hotelParticularType','p')
            ->where('e.hotelConfig = :config')->setParameter('config', $config)
            ->andWhere('e.status = :status')->setParameter('status', 1)
            ->andWhere('p.slug IN(:type)')->setParameter('type',array_values($type))
            ->orderBy('e.sorting','ASC')
            ->orderBy('e.name','ASC')
            ->getQuery()->getResult();
            return  $qb;
    }

	public function getAvailableRoom($config,$type,$booked = array()){

		$qb = $this->createQueryBuilder('e');
       $qb->join('e.hotelParticularType','p');
       $qb->select('e.name as name, e.id as id , e.salesPrice as salesPrice, e.particularCode as particularCode');
       $qb->where('e.hotelConfig = :config')->setParameter('config', $config);
       $qb->andWhere('e.status = :status')->setParameter('status', 1);
       $qb->andWhere('p.slug IN(:type)')->setParameter('type',array_values($type));
       $qb->andWhere('e.id NOT IN(:ids)')->setParameter('ids',$booked);
	   // $qb->where($qb->expr()->notIn('rl.request_id', $booked));
       $qb->orderBy('e.sorting','ASC');
       $qb->orderBy('e.name','ASC');
       $result = $qb->getQuery()->getArrayResult();
		return  $result;
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
            ->where('e.hotelConfig = :config')->setParameter('config', $config)
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
            $qb->where('e.hotelConfig = :config')->setParameter('config', $config);
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
            ->where('e.hotelConfig = :config')->setParameter('config', $config)
            ->andWhere('s.serviceFormat IN(:process)')
            ->setParameter('process',$services)
            ->orderBy('e.name','ASC')
            ->getQuery()->getArrayResult();
            return  $qb;
    }

    public function findHotelExistingCustomer($hospital, $mobile,$data)
    {
    }

    public function getPurchaseUpdateQntx(HotelPurchase $purchase){

        $em = $this->_em;

        /** @var HotelPurchaseItem $purchaseItem */

        foreach($purchase->getPurchaseItems() as $purchaseItem ){

            /** @var HotelParticular  $particular */

            $particular = $purchaseItem->getHotelParticular();
            $qnt = ($particular->getPurchaseQuantity() + $purchaseItem->getQuantity());
            $particular->setPurchaseQuantity($qnt);
            $em->persist($particular);
            $em->flush();

        }
    }

    public function getPurchaseUpdateQnt(HotelPurchase $purchase){

        /** @var  $purchaseItem HotelPurchaseItem */

        if(!empty($purchase->getHotelPurchaseItems())) {
            foreach ($purchase->getHotelPurchaseItems() as $purchaseItem) {
                $stockItem = $purchaseItem->getHotelParticular();
                $this->updateRemoveStockQuantity($stockItem);
            }
        }
    }

	public function updateRemovePurchaseQuantity(HotelInvoiceParticular $invoice_particular,$fieldName=''){

    	if($invoice_particular->getHotelParticular()->getHotelParticularType()->getSlug() == 'production'){
		    $this->updateRemoveProductionQuantity($invoice_particular,$fieldName);
	    }else{
		    $this->updateRemoveStockQuantity($invoice_particular->getHotelParticular(),$fieldName);
	    }

    }

    public function updateRemoveStockQuantity(HotelParticular $stock,$fieldName=''){

        $em = $this->_em;
        if($fieldName == 'sales'){
            $qnt = $em->getRepository('HotelBundle:HotelInvoiceParticular')->salesStockItemUpdate($stock);
            $stock->setSalesQuantity($qnt);
        }elseif($fieldName == 'sales-return'){
            $quantity = $this->_em->getRepository('HotelBundle:HotelInvoiceReturn')->salesReturnStockUpdate($stock);
            $stock->setSalesReturnQuantity($quantity);
        }elseif($fieldName == 'purchase-return'){
            $qnt = $em->getRepository('HotelBundle:HotelPurchaseReturnItem')->purchaseReturnStockUpdate($stock);
            $stock->setPurchaseReturnQuantity($qnt);
        }elseif($fieldName == 'damage'){
            $quantity = $em->getRepository('HotelBundle:HotelDamage')->damageStockItemUpdate($stock);
            $stock->setDamageQuantity($quantity);
        }else{
            $qnt = $em->getRepository('HotelBundle:HotelPurchaseItem')->purchaseStockItemUpdate($stock);
            $stock->setPurchaseQuantity($qnt);
        }
        $em->persist($stock);
        $em->flush();
        $this->remainingQnt($stock);
    }

	public function updateRemoveProductionQuantity(HotelInvoiceParticular $invoice_particular,$fieldName=''){

		$em = $this->_em;

		/* @var $entity HotelProductionElement */

		foreach ($invoice_particular->getHotelParticular()->getProductionElements() as $entity){

			$production = $entity->getParticular();

			if($fieldName == 'sales'){
				$qnt = $em->getRepository('HotelBundle:HotelInvoiceParticular')->salesStockItemProduction($invoice_particular,$entity);
				$production->setSalesQuantity($qnt);
			}elseif($fieldName == 'sales-return'){
				$quantity = $em->getRepository('HotelBundle:HotelInvoiceReturn')->salesStockItemProduction($invoice_particular,$entity);
				$production->setSalesReturnQuantity($quantity);
			}elseif($fieldName == 'damage'){
				$quantity = $em->getRepository('HotelBundle:HotelDamage')->salesStockItemProduction($invoice_particular,$entity);
				$production->setDamageQuantity($quantity);
			}
			$em->persist($production);
			$em->flush();
			$this->remainingQnt($production);
		}

	}

    public function remainingQnt(HotelParticular $stock)
    {
        $em = $this->_em;
        $qnt = ($stock->getOpeningQuantity() + $stock->getPurchaseQuantity() + $stock->getSalesReturnQuantity()) - ($stock->getPurchaseReturnQuantity()+$stock->getSalesQuantity()+$stock->getDamageQuantity());
        $stock->setRemainingQuantity($qnt);
        $em->persist($stock);
        $em->flush();
    }

    public function insertInvoiceProductItem(HotelInvoice $invoice){
	    $em = $this->_em;
        if(!empty($invoice->getHotelInvoiceParticulars())) {

            /* @var  $item HotelInvoiceParticular */

            foreach ($invoice->getHotelInvoiceParticulars() as $item) {
				if(!empty($item->getHotelParticular())) {
					if ( $item->getHotelParticular()->getHotelParticularType()->getSlug() == 'production' and $invoice->getHotelConfig()->getProductionType() == 'post-production' ) {
						$this->productionExpense( $item );
						$particular = $item->getHotelParticular();
						$qnt        = $particular->getSalesQuantity() + $item->getTotalQuantity();
						$particular->setPurchaseQuantity( $qnt );
						$particular->setSalesQuantity( $qnt );
						$em->persist( $particular );
						$em->flush();
					} else {
						$this->getSalesUpdateQnt( $item );
					}
				}
            }
        }

    }

    public function productionExpense(HotelInvoiceParticular  $item)
    {
       if(!empty($item->getHotelParticular()->getProductionElements())){

           $productionElements = $item->getHotelParticular()->getProductionElements();

           /* @var $element HotelProductionElement */

           if($productionElements) {

               foreach ($productionElements as $element) {

                   $entity = new HotelProductionExpense();
                   $entity->setHotelInvoiceParticular($item);
                   $entity->setProductionItem($item->getHotelParticular());
                   $entity->setProductionElement($element->getParticular());
                   $entity->setPurchasePrice($element->getPurchasePrice());
                   $entity->setSalesPrice($element->getSalesPrice());
	               if(!empty($element->getParticular()->getUnit()) and ($element->getParticular()->getUnit()->getName() != 'Sft')) {
		               $entity->setQuantity( $element->getQuantity() * $item->getQuantity());
	               }else{
		               $entity->setQuantity( $element->getQuantity() * $item->getTotalQuantity());
	               }
                   $this->_em->persist($entity);
                   $this->_em->flush();
                   $this->salesProductionQnt($element,$entity);
               }
           }
       }
    }

	public function salesProductionQnt(HotelProductionElement  $element, HotelProductionExpense $entity){

        $em = $this->_em;
        $particular = $element->getParticular();
        $qnt = $particular->getSalesQuantity() + $entity->getQuantity();
        $particular->setSalesQuantity($qnt);
        $em->persist($particular);
        $em->flush();

    }

    public function getSalesUpdateQnt(HotelInvoiceParticular  $item){

        $em = $this->_em;
        $particular = $item->getHotelParticular();
        $qnt = $particular->getSalesQuantity() + $item->getTotalQuantity();
        $particular->setSalesQuantity($qnt);
        $em->persist($particular);
        $em->flush();

    }


}
