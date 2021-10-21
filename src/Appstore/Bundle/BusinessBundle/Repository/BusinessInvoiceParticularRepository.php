<?php

namespace Appstore\Bundle\BusinessBundle\Repository;
use Appstore\Bundle\AccountingBundle\Entity\AccountVendor;
use Appstore\Bundle\BusinessBundle\Entity\BusinessConfig;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoice;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoiceParticular;
use Appstore\Bundle\BusinessBundle\Entity\BusinessParticular;
use Appstore\Bundle\BusinessBundle\Entity\BusinessProductionElement;
use Appstore\Bundle\BusinessBundle\Entity\BusinessPurchaseItem;
use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Appstore\Bundle\HospitalBundle\Entity\InvoiceParticular;
use Appstore\Bundle\RestaurantBundle\Entity\ProductionElement;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;


/**
 * BusinessInvoiceParticularRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BusinessInvoiceParticularRepository extends EntityRepository
{

    public function handleDateRangeFind($qb,$data)
    {
        if(empty($data)){
            $datetime = new \DateTime("now");
            $data['startDate'] = $datetime->format('Y-m-d 00:00:00');
            $data['endDate'] = $datetime->format('Y-m-d 23:59:59');
        }else{
            $data['startDate'] = date('Y-m-d',strtotime($data['startDate']));
            $data['endDate'] = date('Y-m-d',strtotime($data['endDate']));
        }

        if (!empty($data['startDate']) ) {
            $qb->andWhere("e.created >= :startDate");
            $qb->setParameter('startDate', $data['startDate'].' 00:00:00');
        }
        if (!empty($data['endDate'])) {
            $qb->andWhere("e.created <= :endDate");
            $qb->setParameter('endDate', $data['endDate'].' 23:59:59');
        }
    }


    public function insertInvoiceParticular(BusinessInvoice $invoice, $data)
    {
        $quantity = (isset($data['quantity']) and !empty($data['quantity'])) ? $data['quantity'] : 1;
        $em = $this->_em;
        $entity = new BusinessInvoiceParticular();
        $entity->setBusinessInvoice($invoice);
        $entity->setParticular($data['particular']);
        $entity->setPrice($data['price']);
        $entity->setDescription($data['description']);
        $entity->setQuantity($quantity);
        $entity->setSubQuantity(1);
        $entity->setTotalQuantity($quantity);
        if(!empty($data['quantity'])){
            $entity->setSubTotal($data['price'] * $quantity);
        }else{
            $entity->setSubTotal($data['price']);
        }
        if($data['unit']) {
            $unit = $this->_em->getRepository('SettingToolBundle:ProductUnit')->find($data['unit']);
            $entity->setUnit($unit);
        }
        $em->persist($entity);
        $em->flush();

    }

    public function insertStudentInvoiceParticular(BusinessInvoice $invoice,BusinessParticular $particular)
    {

        $date = new \DateTime("now");
        $em = $this->_em;
        $invoiceParticular = $this->findOneBy(array('businessInvoice'=>$invoice,'businessParticular' => $particular));
        if($invoiceParticular){
            $entity = $invoiceParticular;
        }else{
            $entity = new BusinessInvoiceParticular();
        }
        $entity->setBusinessInvoice($invoice);
        $entity->setBusinessParticular($particular);
        $entity->setParticular($particular->getName());
        $entity->setPrice($particular->getSalesPrice());
        $entity->setQuantity(1);
        $entity->setSubQuantity(1);
        $entity->setTotalQuantity(1);
        $entity->setStartDate($date);
        $entity->setEndDate($date);
        $entity->setSubTotal($particular->getSalesPrice());
        $em->persist($entity);
        $em->flush();

    }


    public function insertStudentMonthlyParticular(BusinessInvoice $invoice, $lastInvoice , $data)
    {
        $em = $this->_em;
        foreach ($data['itemId'] as $key => $value):

            $price = floatval ($data['salesPrice'][$key] ? $data['salesPrice'][$key] : 0 );
            if($price > 0){

                $particular = $this->_em->getRepository('BusinessBundle:BusinessParticular')->findOneBy(array('businessConfig' => $invoice->getBusinessConfig(), 'name' => $value ));
                $invoiceParticular = $this->findOneBy(array('businessInvoice' => $invoice, 'businessParticular' => $particular));
                if ($invoiceParticular) {
                    $entity = $invoiceParticular;
                } else {
                    $entity = new BusinessInvoiceParticular();
                }
                $quantity = (int) ($data['quantity'][$key] ? $data['quantity'][$key] : 0 );
                $entity->setBusinessInvoice($invoice);
                $entity->setBusinessParticular($particular);
                $entity->setParticular($particular->getName());
                $entity->setPrice($data['salesPrice'][$key]);
                $entity->setQuantity($quantity);
                $entity->setSubQuantity(1);
                $entity->setTotalQuantity($quantity);
                $entity->setSubTotal($data['salesPrice'][$key] * $quantity);
                if($value == "Monthly Fee" and $quantity > 0 ){
                    $date = $invoice->getEndDate()->format("d-m-Y");
                    $startDate = strtotime(date("Y-m-d", strtotime($date)) . " +1 month");
                    $start = date('Y-m-d',$startDate);
                    $entity->setStartDate(new \DateTime($start));
                    $endDate = strtotime(date("Y-m-d", strtotime($date)) . " +{$quantity} months");
                    $end = date('Y-m-d',$endDate);
                    $entity->setEndDate(new \DateTime($end));
                    $invoice->setEndDate(new \DateTime($end));
                }
                $em->persist($entity);
                $em->flush();
            }
        endforeach;
    }

    public function insertCustomerInvoiceParticular(BusinessInvoice $invoice, $data)
    {
       foreach ($data['itemId'] as $key => $value):

           $em = $this->_em;

           $particular = $this->findOneBy(array('businessInvoice'=>$invoice,'businessParticular'=>$value));
           if($particular){
               $entity = $particular;
           }else{
               $entity = new BusinessInvoiceParticular();
           }

            $particular = $this->_em->getRepository('BusinessBundle:BusinessParticular')->find($value);

            $entity->setBusinessInvoice($invoice);
            $entity->setBusinessParticular($particular);
            $entity->setParticular($particular->getName());
            $entity->setPrice($data['salesPrice'][$key]);
            $entity->setQuantity(1);
            $entity->setSubQuantity(1);
            $entity->setTotalQuantity(1);
            $entity->setSubTotal($data['salesPrice'][$key]);
            $em->persist($entity);
            $em->flush();
        endforeach;


    }

    public function insertCommissionInvoiceParticular(BusinessInvoice $invoice, $data)
    {
        $quantity = (isset($data['quantity']) and !empty($data['quantity'])) ? $data['quantity'] : 1;
        $em = $this->_em;
        $entity = new BusinessInvoiceParticular();
        $entity->setBusinessInvoice($invoice);
        $entity->setParticular($data['particular']);
        $entity->setPrice($data['price']);
        $entity->setPurchasePrice($data['price']);
        $entity->setQuantity($quantity);
        $entity->setSubQuantity(0);
        $entity->setTotalQuantity($quantity);
        $entity->setSubTotal($data['price'] * $quantity);
        $stock = $em->getRepository('BusinessBundle:BusinessParticular')->find($data['particular']);
        $entity->setParticular($stock->getName());
        $entity->setBusinessParticular($stock);
        if($data['vendor']) {
            $vendor = $this->_em->getRepository('AccountingBundle:AccountVendor')->find($data['vendor']);
            $entity->setVendor($vendor);
        }
        if($data['stockGrn']) {
            $stockGrn = $this->_em->getRepository('BusinessBundle:BusinessVendorStockItem')->find($data['stockGrn']);
            $entity->setVendorStockItem($stockGrn);
        }
        $em->persist($entity);
        $em->flush();

    }

    public function insertStockItem(BusinessInvoice $invoice, $data)
    {

        $em = $this->_em;
        $entity = new BusinessInvoiceParticular();
        $quantity = !empty($data['quantity']) ? $data['quantity'] :1;
        $entity->setQuantity($quantity);
	    $entity->setTotalQuantity($quantity);
	    $accessoriesId = $data['accessories'];
	    $price = $data['price'];
        $description = $data['description'];
	    $stock = $em->getRepository('BusinessBundle:BusinessParticular')->find($accessoriesId);
	    $entity->setParticular($stock->getName());
	    $entity->setBusinessParticular($stock);
	    $entity->setDescription($description);
	    $entity->setPrice($price);
	    $entity->setPurchasePrice($stock->getPurchasePrice());
        $entity->setSubTotal($quantity * $price);
        $entity->setBusinessInvoice($invoice);
        $em->persist($entity);
        $em->flush();

    }

    public function insertBannerSignItem(BusinessInvoice $invoice, $data)
    {

        $em = $this->_em;
        $entity = new BusinessInvoiceParticular();
        $quantity = !empty($data['quantity']) ? $data['quantity'] :1;
        $salesPrice = !empty($data['salesPrice']) ? $data['salesPrice'] :0;
        $width = !empty($data['width']) ? $data['width'] :'';
        $height = !empty($data['height']) ? $data['height'] :'';
        $description = !empty($data['description']) ? $data['description'] :'';
        $entity->setQuantity($quantity);
        $particular = $data['particular'];
        $stock = $em->getRepository('BusinessBundle:BusinessParticular')->find($particular);
        $entity->setParticular($stock->getName());
        $entity->setBusinessParticular($stock);
        if(!empty($stock->getUnit())) {
            $entity->setUnit($stock->getUnit()->getName());
        }
        $entity->setPrice($salesPrice);
        $entity->setDescription($description);
        $entity->setPurchasePrice($stock->getPurchasePrice());
        if(!empty($width) and !empty($height)){
            $entity->setWidth($width);
            $entity->setHeight($height);
            $entity->setSubQuantity($width * $height);
            $entity->setTotalQuantity($entity->getSubQuantity() * $quantity);
            $entity->setSubTotal(($quantity * $entity->getSubQuantity()) * $salesPrice);
        }else{
            $entity->setTotalQuantity($quantity);
            $entity->setSubTotal($quantity * $salesPrice);
        }
        $entity->setBusinessInvoice($invoice);
        $em->persist($entity);
        $em->flush();
        return $entity;

    }

	public function salesStockItemProduction(BusinessInvoiceParticular $invoice_particular,BusinessProductionElement $element)
	{
		$qb = $this->createQueryBuilder('e');
		if(!empty($element->getParticular()->getUnit()) and ($element->getParticular()->getUnit()->getName() != 'Sft')){
			$qb->select('e.quantity AS quantity');
		}else{
			$qb->select('SUM(e.totalQuantity) AS quantity');
		}
		$qb->where('e.id = :particular')->setParameter('particular', $invoice_particular->getId());
		$qnt = $qb->getQuery()->getOneOrNullResult();
		$qnt = ($qnt['quantity'] == 'NULL') ? 0 : $qnt['quantity'];
		$stockQuantity = floatval($element->getParticular()->getSalesQuantity());
		return ($stockQuantity+$qnt);

	}

    public function salesDamageProductAmount(BusinessInvoice $invoice)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('COALESCE(SUM(e.damageQnt * e.purchasePrice),0) AS totalDamage','COALESCE(SUM(e.spoilQnt * e.purchasePrice),0) AS totalSpoil');
        $qb->where('e.businessInvoice = :invoice')->setParameter('invoice', $invoice->getId());
        $res = $qb->getQuery()->getOneOrNullResult();
        $totalAmount = ($res['totalDamage'] + $res['totalSpoil']);
        return $totalAmount;
    }


	public function salesStockItemUpdate(BusinessParticular $stockItem)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('SUM(e.totalQuantity) AS quantity');
        $qb->where('e.businessParticular = :stock')->setParameter('stock', $stockItem->getId());
        $qnt = $qb->getQuery()->getOneOrNullResult();
        return $qnt['quantity'];
    }

    public function bonusStockItemUpdate(BusinessParticular $stockItem)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('SUM(e.bonusQnt) AS quantity');
        $qb->where('e.businessParticular = :stock')->setParameter('stock', $stockItem->getId());
        $qnt = $qb->getQuery()->getOneOrNullResult();
        return $qnt['quantity'];
    }

    public function getInvoicePurchasePrice($entity)
    {
        $sql = "SELECT COALESCE(SUM(salesItem.totalQuantity * salesItem.purchasePrice),0) as total FROM business_invoice_particular as salesItem
                WHERE salesItem.businessInvoice_id = :invoice";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('invoice', $entity);
        $stmt->execute();
        $result =  $stmt->fetch();
        return $result['total'];
    }

    public function getSalesItems(BusinessInvoice $sales)
    {
        $entities = $sales->getBusinessInvoiceParticulars();
        $data = '';
        $i = 1;

        /* @var $entity BusinessInvoiceParticular */

        foreach ($entities as $entity) {

            $subQuantity ='';
            if ($entity->getSubQuantity()) {
                $subQuantity = $entity->getHeight().' x '.$entity->getWidth().' = '.$entity->getSubQuantity();
            }
	        $subQnt = ( $entity->getSubQuantity() == '' ) ? 1 : $entity->getSubQuantity();

            $data .= "<tr id='remove-{$entity->getId()}'>";
            $data .= "<td>{$i}.</td>";
            $data .= "<td>{$entity->getParticular()}<br/>{$entity->getDescription()}</td>";
            if($sales->getBusinessConfig()->getBusinessModel() == 'sign') {
            $data .= "<td>{$subQuantity}</td>";
            }
            $data .= "<td>";
	        $data .= "<input type='hidden' name='subQuantity-{$entity->getId()}' id='subQuantity-{$entity->getId()}' value='{$subQnt}'>";
            $data .= "<input type='hidden' name='salesItem[]' value='{$entity->getId()}'>";
            $data .= "<input type='text' class='numeric td-inline-input salesPrice' data-id='{$entity->getId()}' autocomplete='off' id='salesPrice-{$entity->getId()}' name='salesPrice' value='{$entity->getPrice()}'>";
            $data .= "</td>";
            $data .= "<td>";
            $data .= "<input type='text' class='numeric td-inline-input-qnt quantity' data-id='{$entity->getId()}' autocomplete='off' min=1  id='quantity-{$entity->getId()}' name='quantity[]' value='{$entity->getQuantity()}' placeholder='Qnt'>";
            $data .= "</td>";
            $data .= "<td id='subTotal-{$entity->getId()}'>{$entity->getSubTotal()}</td>";
            $data .= "<td>";
            $data .= "<a id='{$entity->getId()}' data-id='{$entity->getId()}'  href='javascript:' class='btn blue mini itemUpdate' ><i class='icon-save'></i></a>";
            $data .= "<a id='{$entity->getId()}' data-id='{$entity->getId()}' data-url='/business/invoice/{$sales->getId()}/{$entity->getId()}/particular-delete' href='javascript:' class='btn red mini particularDelete' ><i class='icon-trash'></i></a>";
            $data .= "</td>";
            $data .= '</tr>';
            $data .= "<tr id='remove-{$entity->getId()}'>";
            if($entity->getDescription()){
                $data .= "<td colspan='7'>{$entity->getDescription()}</td>";
            }
            $data .= '</tr>';

            $i++;
        }
        return $data;
    }


	/**
	 * @param $qb
	 * @param $data
	 */

	protected function handleSearchStockBetween($qb,$data)
	{
		$createdStart = isset($data['startDate'])? $data['startDate'] :'';
		$createdEnd = isset($data['endDate'])? $data['endDate'] :'';
		$name = isset($data['name'])? $data['name'] :'';
		$customer = isset($data['customer'])? $data['customer'] :'';
		$category = isset($data['category'])? $data['category'] :'';
		$type = isset($data['type'])? $data['type'] :'';
		$sku = isset($data['sku'])? $data['sku'] :'';

		if (!empty($customer)) {
			$qb->join('e.customer','c');
			$qb->andWhere($qb->expr()->like("c.mobile", "'%$customer%'"  ));
		}

		if (!empty($name)) {
			$qb->andWhere($qb->expr()->like("mds.name", "'%$name%'"  ));
		}
		if (!empty($sku)) {
			$qb->andWhere($qb->expr()->like("mds.sku", "'%$sku%'"  ));
		}
		if(!empty($category)){
			$qb->andWhere("mds.category = :category");
			$qb->setParameter('category', $category);
		}
		if(!empty($type)){
			$qb->andWhere("mds.businessParticularType = :type");
			$qb->setParameter('type', $type);
		}

		if (!empty($category)) {
			$qb->join('e.businessCategory','c');
			$qb->andWhere("c.id = :cid");
			$qb->setParameter('cid', $category);
		}

		if (!empty($createdStart)) {
			$compareTo = new \DateTime($createdStart);
			$created =  $compareTo->format('Y-m-d 00:00:00');
			$qb->andWhere("e.created >= :createdStart");
			$qb->setParameter('createdStart', $created);
		}

		if (!empty($createdEnd)) {
			$compareTo = new \DateTime($createdEnd);
			$createdEnd =  $compareTo->format('Y-m-d 23:59:59');
			$qb->andWhere("e.created <= :createdEnd");
			$qb->setParameter('createdEnd', $createdEnd);
		}

	}


	public  function reportSalesStockItem(User $user, $data=''){

		$userBranch = $user->getProfile()->getBranches();
		$config =  $user->getGlobalOption()->getBusinessConfig()->getId();

		$qb = $this->createQueryBuilder('si');
		$qb->join('si.businessInvoice','e');
		$qb->join('si.businessParticular','mds');
		$qb->select('SUM(si.quantity) AS quantity');
		$qb->addSelect('SUM(si.totalQuantity * si.purchasePrice) AS purchasePrice');
		$qb->addSelect('SUM(si.subTotal) AS salesPrice');
		$qb->addSelect('mds.name AS name');
		$qb->addSelect('mds.particularCode AS sku');
		$qb->where('e.businessConfig = :config');
		$qb->setParameter('config', $config);
		$qb->andWhere('e.process IN (:process)');
        $qb->setParameter('process', array('Done','Delivered','In-progress','Condition','Chalan'));
        $this->handleSearchStockBetween($qb,$data);
		$qb->groupBy('si.businessParticular');
		$qb->orderBy('mds.name','ASC');
		return $qb->getQuery()->getArrayResult();
	}

    public  function reportDamageStockItem($stocks){

        $ids = array();
        foreach ($stocks as $stock){
            $ids[] =  $stock->getId();
        }
	    $qb = $this->createQueryBuilder('si');
        $qb->join('si.businessParticular','mds');
        $qb->select('SUM(si.damageQnt) AS quantity');
        $qb->addSelect('mds.id AS mdsId');
        $qb->where('mds.id IN (:mids)');
        $qb->setParameter('mids', $ids);
        $qb->groupBy('mds.id');
        $qb->orderBy('mds.name','ASC');
        $result = $qb->getQuery()->getArrayResult();
        $arras = array();
        foreach ($result as $row){
            $arras[$row['mdsId']] = $row['quantity'];
        }
        return $arras;
    }

    public  function reportCommissionSalesStockItem(User $user, $data =''){

        $vendor = isset($data['vendor'])? $data['vendor'] :'';

        $config =  $user->getGlobalOption()->getBusinessConfig()->getId();

        $qb = $this->createQueryBuilder('si');
        $qb->join('si.businessInvoice','e');
        $qb->join('e.customer','customer');
        $qb->join('si.businessParticular','mds');
        $qb->leftJoin('si.vendorStockItem','vsi');
        $qb->leftJoin('vsi.businessVendorStock','bvs');
        $qb->leftJoin('bvs.vendor','vendor');
        $qb->select('si.quantity AS quantity');
        $qb->addSelect('(si.totalQuantity * si.purchasePrice) AS purchasePrice');
        $qb->addSelect('si.price AS price');
        $qb->addSelect('si.subTotal AS salesPrice');
        $qb->addSelect('mds.name AS name');
        $qb->addSelect('mds.particularCode AS sku');
        $qb->addSelect('vsi.quantity AS purchaseQuantity');
        $qb->addSelect('bvs.grn AS grn');
        $qb->addSelect('customer.name AS customerName');
        $qb->addSelect('e.invoice AS invoice');
        $qb->addSelect('e.created AS created');
        $qb->addSelect('vendor.companyName AS companyName');
        $qb->where('e.businessConfig = :config');
        $qb->setParameter('config', $config);
        $qb->andWhere('e.process IN (:process)');
        $qb->setParameter('process', array('Done','Delivered','In-progress','Condition','Chalan'));
        $this->handleSearchStockBetween($qb,$data);
        if(!empty($vendor)){
            $qb->andWhere("vendor.id = :type");
            $qb->setParameter('type', $vendor);
        }
     //   $qb->groupBy('si.businessParticular');
        $qb->orderBy('mds.name','ASC');
        return $qb->getQuery()->getArrayResult();
    }

    public  function reportCustomerSalesItem(User $user, $data=''){

		$userBranch = $user->getProfile()->getBranches();
		$config =  $user->getGlobalOption()->getBusinessConfig()->getId();

		$qb = $this->createQueryBuilder('si');
		$qb->join('si.businessInvoice','e');
		$qb->join('si.businessParticular','mds');
		$qb->leftJoin('mds.unit','u');
		$qb->select('si.totalQuantity AS quantity');
		$qb->addSelect('si.totalQuantity * si.purchasePrice AS purchasePrice');
		$qb->addSelect('si.subTotal AS salesPrice');
		$qb->addSelect('e.invoice AS invoice');
		$qb->addSelect('e.created AS created');
		$qb->addSelect('mds.name AS name');
		$qb->addSelect('u.name AS unit');
		$qb->addSelect('mds.particularCode AS sku');
		$qb->where('e.businessConfig = :config');
		$qb->setParameter('config', $config);
		$qb->andWhere('e.process IN (:process)');
	    $qb->setParameter('process', array('Done','Delivered','In-progress','Condition','Chalan'));
        $this->handleSearchStockBetween($qb,$data);
		$qb->orderBy('mds.name','ASC');
		return $qb->getQuery()->getArrayResult();
	}

    public function searchAutoComplete(BusinessConfig $config,$q)
    {
        $query = $this->createQueryBuilder('e');
        $query->join('e.businessInvoice', 'i');
        $query->select('e.particular as id');
        $query->where($query->expr()->like("e.particular", "'$q%'"  ));
        $query->andWhere("i.businessConfig = :config");
        $query->setParameter('config', $config->getId());
        $query->groupBy('e.particular');
        $query->orderBy('e.particular', 'ASC');
        $query->setMaxResults( '10' );
        return $query->getQuery()->getArrayResult();
    }

    public function updateInvoiceItems($data)
    {

        $em = $this->_em;
        $invoiceParticular = $this->find($data['itemId']);
        if(!empty($invoiceParticular)) {

            /* @var $entity BusinessInvoiceParticular */

	        $entity = $invoiceParticular;
	        $entity->setQuantity( $data['quantity'] );
	        $entity->setPrice( $data['salesPrice'] );
	        if ($entity->getSubQuantity() > 0 and $entity->getBusinessInvoice()->getBusinessConfig()->getBusinessModel() == "sign") {
		        $entity->setTotalQuantity($data['quantity'] * $entity->getSubQuantity() );
		        $entity->setSubTotal( $entity->getPrice() * $entity->getTotalQuantity() );
	        }else{
		        $entity->setTotalQuantity( $data['quantity']);
		        $entity->setSubTotal( $entity->getPrice() * $entity->getTotalQuantity() );
	        }
        }
        $em->persist($entity);
        $em->flush();
        return $invoiceParticular->getBusinessInvoice();

    }

    public function insertInvoiceDistributionItems(BusinessInvoice $invoice , $data)
    {

        $em = $this->_em;

        $item = $data['particular'];

        $particular = $em->getRepository('BusinessBundle:BusinessParticular')->find($item);

        /* @var $entity BusinessInvoiceParticular */
        $config = $invoice->getBusinessConfig();
        $totalQuantity = $data['quantity'];
        $entity = new BusinessInvoiceParticular();
        $entity->setBusinessInvoice($invoice);
        $entity->setBusinessParticular($particular);
        if($particular->getUnit()){
            $entity->setUnit($particular->getUnit()->getName());
        }
        $entity->setParticular($particular->getName());
        $tlo = 0;
        $tloTotal = 0;
        if($config->isTloCommission() == 1){
            $tloMode = $data['tloMode'];
            if($tloMode == 'flat' and $data['tloPrice'] > 0){
                $tlo = $data['tloPrice'];
                $tloTotal = $tlo;
            }elseif($tloMode == 'percent' and $data['tloPrice'] > 0){
                $tlox = $data['tloPrice'];
                $tlo = (($data['salesPrice']) * $tlox)/100;
                $tloTotal = ($tlo * $data['quantity']);
            }elseif ($data['tloPrice'] > 0){
                $tlo = ($data['tloPrice']);
                $tloTotal = ($tlo * $data['quantity']);
            }
            $entity->setTloMode($tloMode);
            $entity->setTloPrice($tlo);
            $entity->setTloTotal($tloTotal);
        }
        if($config->isSrCommission() == 1){
            $tloMode = $data['tloMode'];
            if($tloMode == 'flat' and $data['srCommission'] > 0){
                $tlo = $data['tloPrice'];
                $tloTotal = $tlo;
            }elseif($tloMode == 'percent' and $data['srCommission'] > 0){
                $tlox = $data['srCommission'];
                $tlo = (($data['salesPrice']) * $tlox)/100;
                $tloTotal = ($tlo * $data['quantity']);
            }elseif ($data['srCommission'] > 0){
                $tlo = ($data['srCommission']);
                $tloTotal = ($tlo * $data['quantity']);
            }
            $entity->setTloMode($tloMode);
            $entity->setSrCommission($tlo);
            $entity->setSrCommissionTotal($tloTotal);
        }
        $entity->setPrice($data['salesPrice']);
        $entity->setQuantity( $data['quantity'] );
        $entity->setBonusQnt( $data['bonusQuantity'] );
        $entity->setPurchasePrice($entity->getBusinessParticular()->getPurchasePrice());
        $entity->setTotalQuantity((float)$totalQuantity);
        $subTotal = round(($entity->getPrice() * $entity->getTotalQuantity()),2);
        $entity->setSubTotal($subTotal);
        $em->persist($entity);
        $em->flush();
        return $entity->getBusinessInvoice();

    }

    public function getDistributionItems(BusinessInvoice $sales)
    {
        $entities = $sales->getBusinessInvoiceParticulars();
        $data = '';
        $i = 1;

        /* @var $entity BusinessInvoiceParticular */

        foreach ($entities as $entity) {

            $data .= "<tr id='remove-{$entity->getId()}'>";
            $data .= "<td>{$i}.</td>";
            $data .= "<td>{$entity->getParticular()}</td>";
            $data .= "<td>{$entity->getBusinessParticular()->getRemainingQuantity()}</td>";
            $data .= "<td>{$entity->getUnit()}</td>";
            $data .= "<td><input type='text' class='remove-value numeric td-inline-input-qnt salesPrice' data-id='{$entity->getId()}' id='salesPrice-{$entity->getId()}' name='salesPrice[]' value='{$entity->getPrice()}'></td>";
            $data .= "<td><input type='number' class='remove-value numeric td-inline-input-qnt salesQuantity' data-id='{$entity->getId()}' autocomplete='off' min=1  id='salesQuantity-{$entity->getId()}' name='salesQuantity[]' value='{$entity->getQuantity()}' placeholder='{$entity->getQuantity()}'></td>";
            $data .= "<td><input type='number' class='remove-value numeric td-inline-input-qnt returnQuantity' data-id='{$entity->getId()}' autocomplete='off' min=1  id='returnQuantity-{$entity->getId()}' name='returnQuantity[]' value='{$entity->getReturnQnt()}' placeholder='{$entity->getReturnQnt()}'></td>";
            $data .= "<td><input type='number' class='remove-value numeric td-inline-input-qnt damageQuantity' data-id='{$entity->getId()}' autocomplete='off' min=1  id='damageQuantity-{$entity->getId()}' name='damageQuantity[]' value='{$entity->getDamageQnt()}' placeholder='{$entity->getDamageQnt()}'></td>";
            $data .= "<td><input type='number' class='remove-value numeric td-inline-input-qnt spoilQuantity' data-id='{$entity->getId()}' autocomplete='off' min=1  id='spoilQuantity-{$entity->getId()}' name='spoilQuantity[]' value='{$entity->getSpoilQnt()}' placeholder='{$entity->getSpoilQnt()}'></td>";
            $data .= "<td id='totalQuantity-{$entity->getId()}'>{$entity->getTotalQuantity()}</td>";
            $data .= "<td id='subTotal-{$entity->getId()}'>{$entity->getSubTotal()}</td>";
            $data .= "<td><input type='number' class='remove-value numeric td-inline-input-qnt tloPrice' data-id='{$entity->getId()}' autocomplete='off' min=1  id='tloPrice-{$entity->getId()}' name='tloPrice[]' value='{$entity->getTloPrice()}' placeholder='{$entity->getTloPrice()}'></td>";
            $data .= "<td class='tloPrice-{$entity->getId()}'>{$entity->getTloTotal()}</td>";

            $data .= "<td><input type='number' class='remove-value numeric td-inline-input-qnt bonusQuantity' data-id='{$entity->getId()}' autocomplete='off' min=1  id='bonusQuantity-{$entity->getId()}' name='bonusQuantity[]' value='{$entity->getBonusQnt()}' placeholder='{$entity->getBonusQnt()}'></td>";
            $data .= "<td>";
            $data .= "<a id='{$entity->getId()}' data-id='{$entity->getId()}' data-url='/business/invoice/{$sales->getId()}/{$entity->getId()}/distribution-delete' href='javascript:' class='btn red mini distributionDelete' ><i class='icon-trash'></i></a>";
            $data .= "</td>";
            $data .= '</tr>';
            $i++;
        }
        return $data;
    }



    public function updateInvoiceDistributionItems($data)
    {

        $em = $this->_em;
        /* @var $entity BusinessInvoiceParticular */
        $entity = $this->find($data['itemId']);
        $config = $entity->getBusinessInvoice()->getBusinessConfig();
        if(!empty($entity)) {

            /* @var $entity BusinessInvoiceParticular */
            $tlo = 0;
            $tloTotal = 0;
            if($config->isTloCommission() == 1){
                $tloMode = $data['tloMode'];
                if($tloMode == 'flat' and $data['tloPrice'] > 0){
                    $tlo = $data['tloPrice'];
                    $tloTotal = $tlo;
                }elseif($tloMode == 'percent' and $data['tloPrice'] > 0){
                    $tlox = $data['tloPrice'];
                    $tlo = (($data['salesPrice']) * $tlox)/100;
                    $tloTotal = ($tlo * $data['totalQuantity']);
                }elseif ($data['tloPrice'] > 0){
                    $tlo = ($data['tloPrice']);
                    $tloTotal = ($tlo * $data['totalQuantity']);
                }
                $entity->setTloMode($tloMode);
                $entity->setTloPrice($tlo);
                $entity->setTloTotal($tloTotal);
            }
            if($config->isSrCommission() == 1){
                $tloMode = $data['tloMode'];
                if($tloMode == 'flat' and $data['srCommission'] > 0){
                    $tlo = $data['tloPrice'];
                    $tloTotal = $tlo;
                }elseif($tloMode == 'percent' and $data['srCommission'] > 0){
                    $tlox = $data['srCommission'];
                    $tlo = (($data['salesPrice']) * $tlox)/100;
                    $tloTotal = ($tlo * $data['totalQuantity']);
                }elseif ($data['srCommission'] > 0){
                    $tlo = ($data['srCommission']);
                    $tloTotal = ($tlo * $data['totalQuantity']);
                }
                $entity->setTloMode($tloMode);
                $entity->setSrCommission($tlo);
                $entity->setSrCommissionTotal($tloTotal);
            }
            $entity->setQuantity( $data['salesQuantity'] );
            $entity->setReturnQnt( $data['returnQuantity'] );
            $entity->setDamageQnt( $data['damageQuantity'] );
            $entity->setSpoilQnt( $data['spoilQuantity'] );
            $entity->setBonusQnt( $data['bonusQuantity'] );
            $entity->setPrice( $data['salesPrice'] );
            $entity->setPurchasePrice($entity->getBusinessParticular()->getPurchasePrice());
            $entity->setTotalQuantity((int)$data['totalQuantity']);
            $subTotal = round(($entity->getPrice() * $entity->getTotalQuantity()),2);
            $entity->setSubTotal($subTotal);
        }
        $em->persist($entity);
        $em->flush();
        return $entity->getBusinessInvoice();

    }

    public function getTotalSalesQnt(BusinessInvoiceParticular $particular)
    {
        $id = $particular->getVendorStockItem()->getId();
        $query = $this->createQueryBuilder('e');
        $query->join('e.vendorStockItem','s');
        $query->select(' COALESCE(SUM(e.quantity),0) AS  quantity');
        $query->where("s.id ={$id}");
        $total =  $query->getQuery()->getOneOrNullResult()['quantity'];
        return $total;
    }

    protected function handleSearchBetween($qb,$data)
    {
        $vendorId = isset($data['vendorId'])? $data['vendorId'] :'';
        $name = isset($data['name'])? $data['name'] :'';
        if(!empty($vendorId)){
            $qb->join('pi.vendor','v');
            $qb->andWhere("v.id = :vendorId")->setParameter('vendorId', $vendorId);
        }
        if(!empty($name)){
            $qb->andWhere($qb->expr()->like("p.name", "'%$name%'"  ));
        }

    }


    public function getSalesStockItem($pagination,$data = array())
    {

        $ids = [];
        foreach ($pagination as $entity){
            $ids[] = $entity['id'];
        }
        $qb = $this->createQueryBuilder('pi');
        $qb->join('pi.businessParticular','ms');
        $qb->join('pi.businessInvoice','e');
        $qb->select('ms.id as id , COALESCE(SUM(pi.quantity),0) as quantity');
        $qb->where('e.process IN (:processes)');
        $qb->setParameter('process', array('Done','Delivered','In-progress','Condition','Chalan'));
        $qb->andWhere('ms.id IN (:ids)')->setParameter('ids', $ids) ;
        $qb->groupBy('ms.id');
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

    public  function reportVendorCommissionSalesItem(User $user, $data=''){


        $config =  $user->getGlobalOption()->getBusinessConfig()->getId();
        $qb = $this->createQueryBuilder('si');
        $qb->join('si.businessInvoice','e');
        $qb->join('si.businessParticular','mds');
        $qb->leftJoin('mds.unit','u');
        $qb->join('e.customer','c');
        $qb->leftJoin('si.vendor','v');
        $qb->leftJoin('si.vendorStockItem','vsi');
        $qb->leftJoin('vsi.businessVendorStock','vs');
        $qb->select('si.totalQuantity AS quantity');
        $qb->addSelect('si.totalQuantity * si.purchasePrice AS purchasePrice');
        $qb->addSelect('c.name AS customerName');
        $qb->addSelect('v.companyName AS companyName');
        $qb->addSelect('vs.grn AS grn');
        $qb->addSelect('si.subTotal AS salesPrice');
        $qb->addSelect('si.price AS price');
        $qb->addSelect('e.invoice AS invoice');
        $qb->addSelect('e.created AS created');
        $qb->addSelect('mds.name AS name');
        $qb->addSelect('u.name AS unit');
        $qb->addSelect('mds.particularCode AS sku');
        $qb->where('e.businessConfig = :config');
        $qb->setParameter('config', $config);
        $qb->andWhere('e.process IN (:process)');
        $qb->setParameter('process', array('Done','Delivered','In-progress','Condition','Chalan'));
        $this->handleSearchCommissionBetween($qb,$data);
        $qb->orderBy('created','ASC');
        return $qb->getQuery()->getArrayResult();
    }

    protected function handleSearchCommissionBetween($qb,$data)
    {
        $createdStart = isset($data['startDate'])? $data['startDate'] :'';
        $createdEnd = isset($data['endDate'])? $data['endDate'] :'';
        $name = isset($data['name'])? $data['name'] :'';
        $customer = isset($data['customer'])? $data['customer'] :'';
        $vendor = isset($data['vendor'])? $data['vendor'] :'';
        $grn = isset($data['grn'])? $data['grn'] :'';

        if (!empty($customer)) {
            $qb->andWhere($qb->expr()->like("c.name", "'%$customer%'"  ));
        }
        if (!empty($vendor)) {
            $qb->andWhere($qb->expr()->like("v.id", "'%$vendor%'"  ));
        }
        if (!empty($name)) {
            $qb->andWhere($qb->expr()->like("mds.name", "'%$name%'"  ));
        }
        if (!empty($grn)) {
            $qb->andWhere($qb->expr()->like("vs.grn", "'%$grn%'"  ));
        }
        if (!empty($createdStart)) {
            $compareTo = new \DateTime($createdStart);
            $created =  $compareTo->format('Y-m-d 00:00:00');
            $qb->andWhere("e.created >= :createdStart");
            $qb->setParameter('createdStart', $created);
        }

        if (!empty($createdEnd)) {
            $compareTo = new \DateTime($createdEnd);
            $createdEnd =  $compareTo->format('Y-m-d 23:59:59');
            $qb->andWhere("e.created <= :createdEnd");
            $qb->setParameter('createdEnd', $createdEnd);
        }

    }

    public function insertDistributionItem(BusinessInvoice $entity,$particulars)
    {
        /* @var $particular BusinessParticular */
        $em = $this->_em;
        if(empty($entity->getBusinessInvoiceParticulars())){
            foreach ($particulars as $particular):
                $item = new BusinessInvoiceParticular();
                $item->setBusinessInvoice($entity);
                $item->setBusinessParticular($particular);
                $item->setParticular($particular->getName());
                $em->persist($item);
                $em->flush();
            endforeach;
        }

    }

    public function getCustomerItem(BusinessConfig $config, Customer $vendor)
    {
        $configId = $config->getId();
        $vendorId = $vendor->getId();
        $qb = $this->createQueryBuilder('pi');
        $qb->select('p.name as itemName','p.id as itemId','p.salesPrice as salesPrice','SUM(pi.totalQuantity) as quantity','SUM(pi.bonusQnt) as bonusQuantity');
        $qb->join('pi.businessInvoice','e');
        $qb->join('pi.businessParticular','p');
        $qb->where('p.businessConfig = :config')->setParameter('config', $configId) ;
        $qb->andWhere('e.customer = :vendorId')->setParameter('vendorId', $vendorId) ;
        $qb->groupBy('p.name');
        $qb->orderBy('p.name','ASC');
        $result = $qb->getQuery()->getArrayResult();
        return  $result;
    }

    public function getProductCount($vendor,$item)
    {

        $qb = $this->createQueryBuilder('pi');
        $qb->select('SUM(pi.totalQuantity) as quantity');
        $qb->join('pi.businessInvoice','e');
        $qb->join('pi.businessParticular','p');
        $qb->where('e.customer = :vendorId')->setParameter('vendorId', $vendor) ;
        $qb->andWhere('p.id = :pId')->setParameter('pId', $item) ;
        $result = $qb->getQuery()->getOneOrNullResult();
        return $result['quantity'];
    }



    public function getCurrentStock($stock)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.businessInvoice', 'mp');
        $qb->select('SUM(e.totalQuantity) AS quantity','SUM(pi.bonusQnt) as bonusQuantity');
        $qb->where('e.businessParticular = :particular')->setParameter('particular', $stock);
        $qb->andWhere('e.process IN (:process)');
        $qb->setParameter('process', array('Done','Delivered','In-progress','Condition','Chalan'));
        $qnt = $qb->getQuery()->getOneOrNullResult();
        return $qnt;
    }


}
