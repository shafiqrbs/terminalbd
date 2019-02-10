<?php

namespace Appstore\Bundle\EducationBundle\Repository;
use Appstore\Bundle\EducationBundle\Entity\EducationConfig;
use Appstore\Bundle\EducationBundle\Entity\EducationStock;
use Doctrine\ORM\EntityRepository;


/**
 * EducationStockRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EducationStockRepository extends EntityRepository
{

    public function getServiceLists(EducationConfig $config)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.service','service');
        $qb->where('e.educationConfig = :config');
        $qb->setParameter('config',$config);
        $qb->andWhere('service.dentalService is null');
        $qb->orderBy('service.name , e.name','ASC');
        $result = $qb->getQuery()->getResult();
        return $result;
    }


    public function searchAutoComplete(EducationConfig $config,$q)
    {
        $query = $this->createQueryBuilder('e');
	    $query->select('e.name as id');
	    $query->addSelect('e.name as text');
        $query->where($query->expr()->like("e.name", "'$q%'"  ));
        $query->andWhere("e.educationConfig = :config");
        $query->setParameter('config', $config->getId());
        $query->groupBy('e.name');
        $query->orderBy('e.name', 'ASC');
        $query->setMaxResults( '10' );
        return $query->getQuery()->getResult();
    }

    public function findWithSearch($config, $data){

        $name = isset($data['name'])? $data['name'] :'';
        $type = isset($data['type'])? $data['type'] :'';
        $qb = $this->createQueryBuilder('e');
        $qb->where('e.educationConfig = :config')->setParameter('config', $config) ;
        if (!empty($name)) {
            $qb->andWhere($qb->expr()->like("e.name", "'%$name%'"  ));
        }
        if(!empty($type)){
            $qb->andWhere("e.productType = :type");
            $qb->setParameter('type', $type);
        }
        $qb->orderBy('e.name','ASC');
        $qb->getQuery();
        return  $qb;
    }

    public function getFindWithParticular($config,$type){

        $qb = $this->createQueryBuilder('e')
            ->join('e.EducationStockType','p')
            ->where('e.educationConfig = :config')->setParameter('config', $config)
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
            ->where('e.educationConfig = :config')->setParameter('config', $config)
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
            $qb->where('e.educationConfig = :config')->setParameter('config', $config);
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
            ->where('e.educationConfig = :config')->setParameter('config', $config)
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

            /** @var EducationStock  $particular */

            $particular = $purchaseItem->getEducationStock();
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
                $stockItem = $purchaseItem->getEducationStock();
                $this->updateRemoveStockQuantity($stockItem);
            }
        }
    }

	public function updateRemovePurchaseQuantity(BusinessInvoiceParticular $invoice_particular,$fieldName=''){

    	if($invoice_particular->getEducationStock()->getEducationStockType()->getSlug() == 'production'){
		    $this->updateRemoveProductionQuantity($invoice_particular,$fieldName);
	    }else{
		    $this->updateRemoveStockQuantity($invoice_particular->getEducationStock(),$fieldName);
	    }

    }

    public function updateRemoveStockQuantity(EducationStock $stock,$fieldName=''){

        $em = $this->_em;
        if($fieldName == 'sales'){
            $qnt = $em->getRepository('EducationBundle:BusinessInvoiceParticular')->salesStockItemUpdate($stock);
            $stock->setSalesQuantity($qnt);
        }elseif($fieldName == 'sales-return'){
            $quantity = $this->_em->getRepository('EducationBundle:BusinessInvoiceReturn')->salesReturnStockUpdate($stock);
            $stock->setSalesReturnQuantity($quantity);
        }elseif($fieldName == 'purchase-return'){
            $qnt = $em->getRepository('EducationBundle:BusinessPurchaseReturnItem')->purchaseReturnStockUpdate($stock);
            $stock->setPurchaseReturnQuantity($qnt);
        }elseif($fieldName == 'damage'){
            $quantity = $em->getRepository('EducationBundle:BusinessDamage')->damageStockItemUpdate($stock);
            $stock->setDamageQuantity($quantity);
        }else{
            $qnt = $em->getRepository('EducationBundle:BusinessPurchaseItem')->purchaseStockItemUpdate($stock);
            $stock->setPurchaseQuantity($qnt);
        }
        $em->persist($stock);
        $em->flush();
        $this->remainingQnt($stock);
    }

	public function updateRemoveProductionQuantity(BusinessInvoiceParticular $invoice_particular,$fieldName=''){

		$em = $this->_em;

		/* @var $entity BusinessProductionElement */

		foreach ($invoice_particular->getEducationStock()->getProductionElements() as $entity){

			$production = $entity->getParticular();

			if($fieldName == 'sales'){
				$qnt = $em->getRepository('EducationBundle:BusinessInvoiceParticular')->salesStockItemProduction($invoice_particular,$entity);
				$production->setSalesQuantity($qnt);
			}elseif($fieldName == 'sales-return'){
				$quantity = $em->getRepository('EducationBundle:BusinessInvoiceReturn')->salesStockItemProduction($invoice_particular,$entity);
				$production->setSalesReturnQuantity($quantity);
			}elseif($fieldName == 'damage'){
				$quantity = $em->getRepository('EducationBundle:BusinessDamage')->salesStockItemProduction($invoice_particular,$entity);
				$production->setDamageQuantity($quantity);
			}
			$em->persist($production);
			$em->flush();
			$this->remainingQnt($production);
		}

	}

    public function remainingQnt(EducationStock $stock)
    {
        $em = $this->_em;
        $qnt = ($stock->getOpeningQuantity() + $stock->getPurchaseQuantity() + $stock->getSalesReturnQuantity() + $stock->getTransferQuantity()) - ($stock->getPurchaseReturnQuantity() + $stock->getSalesQuantity()+$stock->getDamageQuantity());
        $stock->setRemainingQuantity($qnt);
        $em->persist($stock);
        $em->flush();
    }

    public function insertInvoiceProductItem(BusinessInvoice $invoice){
	    $em = $this->_em;
        if(!empty($invoice->getBusinessInvoiceParticulars())) {

            /* @var  $item BusinessInvoiceParticular */

            foreach ($invoice->getBusinessInvoiceParticulars() as $item) {
				if(!empty($item->getEducationStock())) {
					if ( $item->getEducationStock()->getEducationStockType()->getSlug() == 'post-production') {
						$this->productionExpense( $item );
					}
					$this->getSalesUpdateQnt( $item );
				}
            }
        }

    }

    public function productionExpense(BusinessInvoiceParticular  $item)
    {
       if(!empty($item->getEducationStock()->getProductionElements())){

           $productionElements = $item->getEducationStock()->getProductionElements();

           /* @var $element BusinessProductionElement */

           if($productionElements) {

               foreach ($productionElements as $element) {

                   $entity = new BusinessProductionExpense();
                   $entity->setBusinessInvoiceParticular($item);
                   $entity->setProductionItem($item->getEducationStock());
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

	public function salesProductionQnt(BusinessProductionElement  $element){

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

    public function getSumTotalProductionItemQuantity(EducationStock $particular){



    }

	public function getSumTotalInvoiceItemQuantity($particular){
	}

	public function getSalesUpdateQnt(BusinessInvoiceParticular  $item){

        $em = $this->_em;
        $particular = $item->getEducationStock();
		$productionQnt = $this->getSumTotalProductionItemQuantity($particular);
		$invoiceQnt = $this->getSumTotalInvoiceItemQuantity($particular);
		$qnt = $productionQnt + $invoiceQnt;
		$particular->setSalesQuantity($qnt);
        $em->persist($particular);
        $em->flush();
	    $this->remainingQnt($particular);
    }


}
