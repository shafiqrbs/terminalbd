<?php

namespace Appstore\Bundle\BusinessBundle\Repository;
use Appstore\Bundle\AccountingBundle\Entity\AccountVendor;
use Appstore\Bundle\BusinessBundle\Entity\BusinessConfig;
use Appstore\Bundle\BusinessBundle\Entity\BusinessPurchase;
use Appstore\Bundle\BusinessBundle\Entity\BusinessPurchaseItem;
use Appstore\Bundle\BusinessBundle\Entity\BusinessParticular;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;


/**
 * BusinessPurchaseItemRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BusinessPurchaseItemRepository extends EntityRepository
{

	protected function handleSearchBetween($qb,$data)
	{

		$grn = isset($data['grn'])? $data['grn'] :'';
		$vendor = isset($data['vendor'])? $data['vendor'] :'';
		$business = isset($data['name'])? $data['name'] :'';
		$brand = isset($data['brandName'])? $data['brandName'] :'';
		$mode = isset($data['mode'])? $data['mode'] :'';
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
		if(!empty($mode)){
			$qb->andWhere($qb->expr()->like("ms.mode", "'%$mode%'"  ));
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


	public function findWithSearch(User $user, $data)
	{
		$config = $user->getGlobalOption()->getBusinessConfig()->getId();
		$qb = $this->createQueryBuilder('pi');
		$qb->join('pi.businessPurchase','e');
		$qb->where('e.businessConfig = :config')->setParameter('config', $config) ;
		$this->handleSearchBetween($qb,$data);
		$qb->orderBy('e.receiveDate','DESC');
		$qb->getQuery();
		return  $qb;
	}

	public function getVendorItem(BusinessConfig $config, AccountVendor $vendor)
	{
		$configId = $config->getId();
		$vendorId = $vendor->getId();
		$qb = $this->createQueryBuilder('pi');
		$qb->select('p.name as itemName','p.id as itemId','p.purchasePrice as purchasePrice');
		$qb->join('pi.businessPurchase','e');
		$qb->join('pi.businessParticular','p');
		$qb->where('p.businessConfig = :config')->setParameter('config', $configId) ;
		$qb->andWhere('e.vendor = :vendorId')->setParameter('vendorId', $vendorId) ;
		$qb->groupBy('p.name');
		$qb->orderBy('p.name','ASC');
		$result = $qb->getQuery()->getArrayResult();
		return  $result;
	}

	public function getPurchaseStockItem($pagination,$data = array())
    {

        $ids = [];
        foreach ($pagination as $entity){
            $ids[] =$entity['id'];
        }

        $qb = $this->createQueryBuilder('pi');
        $qb->join('pi.businessParticular','ms');
        $qb->join('pi.businessPurchase','e');
        $qb->select('ms.id as id , COALESCE(SUM(pi.quantity),0) as quantity');
        $qb->where('e.commissionInvoice = 1');
        $qb->andWhere('ms.id IN (:ids)')->setParameter('ids', $ids) ;
        $qb->groupBy('ms.id');
        //  $qb->where($qb->expr()->in("pi.id", $ids ));
        $this->handleSearchBetween($qb,$data);
        $result =  $qb->getQuery()->getArrayResult();
        $arrs = [];

        if(!empty($result)){
            foreach ($result as $row){
                $arrs[$row['id']] = $row;
            }
        }
        return $arrs;

    }

	public function getPurchaseAveragePrice(BusinessParticular $particular)
    {

       /* $qb = $this->_em->createQueryBuilder();
        $qb->from('BusinessBundle:BusinessPurchaseItem','e');
        $qb->select('AVG(e.purchasePrice) AS avgPurchasePrice');
        $qb->where('e.businessParticular = :particular')->setParameter('particular', $particular) ;
        $res = $qb->getQuery()->getOneOrNullResult();
        if(!empty($res)){
            $particular->setPurchaseAverage($res['avgPurchasePrice']);
            $this->_em->persist($particular);
            $this->_em->flush($particular);
        }*/
    }

    public function insertPurchaseItems($invoice, $data)
    {

    	$particular = $this->_em->getRepository('BusinessBundle:BusinessParticular')->find($data['particularId']);
        $em = $this->_em;
	    $purchasePrice = (isset($data['price']) and !empty($data['price']))? $data['price']:0;
        $entity = new BusinessPurchaseItem();
        $entity->setBusinessPurchase($invoice);
        $entity->setBusinessParticular($particular);
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

    public function insertPurchaseDistributionItems($invoice, $data)
    {

        $particular = $this->_em->getRepository('BusinessBundle:BusinessParticular')->find($data['particularId']);
        $em = $this->_em;
        $purchasePrice = (isset($data['price']) and !empty($data['price']))? $data['price']:0;
        $entity = new BusinessPurchaseItem();
        $entity->setBusinessPurchase($invoice);
        $entity->setBusinessParticular($particular);
        if(!empty($particular->getPrice())){
            $entity->setSalesPrice($particular->getPrice());
        }
        $entity->setPurchasePrice($purchasePrice);
        $entity->setActualPurchasePrice($purchasePrice);
        $entity->setQuantity($data['quantity']);
        $entity->setBonusQuantity($data['bonusQuantity']);
        $entity->setPurchaseSubTotal($data['quantity'] * $entity->getPurchasePrice());
        $em->persist($entity);
        $em->flush();
        $this->getPurchaseAveragePrice($particular);

    }

    public function insertSawmillPurchaseItems($invoice, $data)
    {
	    $particular = $this->_em->getRepository('BusinessBundle:BusinessParticular')->find($data['particularId']);
	    $em = $this->_em;
	    $entity = new BusinessPurchaseItem();
	    $quantity = 0;
	    $purchasePrice = 0;
	    if($data['particularType'] == 'round'){
		    $quantity = round((($data['height'] * $data['width'] * $data['length'])/2304),2);
		    $purchasePrice = round(($data['price']/$quantity),2);
	    }elseif ($data['particularType'] == 'square'){
		    $quantity = round((($data['height'] * $data['width'] * $data['length'])/144),2);
		    $purchasePrice = round(($data['price']/$quantity),2);
	    }
	    $entity->setBusinessPurchase($invoice);
	    $entity->setBusinessParticular($particular);
	    if(!empty($particular->getPrice())){
		    $entity->setSalesPrice($particular->getPrice());
	    }
	    $entity->setPurchasePrice($purchasePrice);
	    $entity->setActualPurchasePrice($purchasePrice);
	    $entity->setQuantity($quantity);
	    $entity->setPurchaseSubTotal($data['price']);
	    $em->persist($entity);
	    $em->flush();
    }

    public function insertSignPurchaseItems($invoice, $data)
    {
	    $particular = $this->_em->getRepository('BusinessBundle:BusinessParticular')->find($data['particularId']);
	    $em = $this->_em;
	    $entity = new BusinessPurchaseItem();
	    $purchasePrice = $data['purchasePrice'];
	    $quantity = 0;
	    if(!empty($data['height']) and !empty($data['width'])){
		    $entity->setHeight($data['height']);
		    $entity->setWidth($data['width']);
		    $quantity = round((($entity->getHeight() * $entity->getWidth()) * $data['quantity']),2);
		    $entity->setQuantity($quantity);
		    $entity->setSubQuantity($data['quantity']);
	    }else{
		    $entity->setQuantity($data['quantity']);
	    }
		$entity->setBusinessPurchase($invoice);
	    $entity->setBusinessParticular($particular);
	    if(!empty($particular->getPrice())){
		    $entity->setSalesPrice($particular->getPrice());
	    }
	    $entity->setPurchasePrice($purchasePrice);
	    $entity->setActualPurchasePrice($purchasePrice);
	    $entity->setPurchaseSubTotal($entity->getQuantity() * $purchasePrice);
	    $em->persist($entity);
	    $em->flush();
    }

    public function getPurchaseItems(BusinessPurchase $sales)
    {
        $entities = $sales->getPurchaseItems();
        $data = '';
        $i = 1;

        /* @var $entity BusinessPurchaseItem */

        foreach ($entities as $entity) {

            $unit = !empty($entity->getBusinessParticular()->getUnit()) ? $entity->getBusinessParticular()->getUnit()->getName() : '';
            $data .= "<tr id='remove-{$entity->getId()}'>";
            $data .= "<td>{$i}</td>";
            $data .= "<td>{$entity->getBusinessParticular()->getParticularCode()}</td>";
            $data .= "<td>{$entity->getBusinessParticular()->getName()}</td>";
            $data .= "<td>{$entity->getSalesPrice()}</td>";
            $data .= "<td>{$entity->getPurchasePrice()}</td>";
            if ($sales->getBusinessConfig()->getBusinessModel() == 'sign'){
                $data .= "<td>{$entity->getHeight()}x{$entity->getWidth()}</td>";
                $data .= "<td>{$entity->getSubQuantity()}</td>";
            }
            $data .= "<td>{$entity->getQuantity()}</td>";
            if ($sales->getBusinessConfig()->getBusinessModel() == 'distribution') {
                $totalQnt = $entity->getQuantity() + $entity->getBonusQuantity();
                $data .= "<td>{$entity->getBonusQuantity()}</td>";
                $data .= "<td>{$totalQnt}</td>";
            }
            $data .= "<td>{$unit}</td>";
            $data .= "<td>{$entity->getPurchaseSubTotal()}</td>";
            $data .= "<td><a id='{$entity->getId()}'  data-url='/business/purchase/{$sales->getId()}/{$entity->getId()}/particular-delete' href='javascript:' class='btn red mini delete' ><i class='icon-trash'></i></a></td>";
            $data .= '</tr>';
            $i++;
        }
        return $data;
    }

    public function purchaseStockItemUpdate(BusinessParticular $stockItem)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.businessPurchase', 'mp');
        $qb->select('SUM(e.quantity) AS quantity');
        $qb->where('e.businessParticular = :particular')->setParameter('particular', $stockItem->getId());
        $qb->andWhere('mp.process = :process')->setParameter('process', 'Approved');
        $qnt = $qb->getQuery()->getOneOrNullResult();
        return $qnt['quantity'];
    }

    public function bonusStockItemUpdate(BusinessParticular $stockItem)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.businessPurchase', 'mp');
        $qb->select('SUM(e.bonusQuantity) AS quantity');
        $qb->where('e.businessParticular = :particular')->setParameter('particular', $stockItem->getId());
        $qb->andWhere('mp.process = :process')->setParameter('process', 'Approved');
        $qnt = $qb->getQuery()->getOneOrNullResult();
        return $qnt['quantity'];
    }

    public function updatePurchaseItemPrice(BusinessPurchase $purchase)
    {
        /* @var BusinessPurchaseItem $item */

        foreach ($purchase->getBusinessPurchaseItems() as $item){

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
