<?php

namespace Appstore\Bundle\BusinessBundle\Repository;
use Appstore\Bundle\BusinessBundle\Entity\BusinessConfig;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoice;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoiceParticular;
use Appstore\Bundle\BusinessBundle\Entity\BusinessParticular;
use Appstore\Bundle\BusinessBundle\Entity\BusinessProductionElement;
use Appstore\Bundle\BusinessBundle\Entity\BusinessPurchaseItem;
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
	    $quantity = (isset($data['quantity']) and !empty($data['quantity'])) ? $data['quantity'] : 0;
        $em = $this->_em;
        $entity = new BusinessInvoiceParticular();
        $entity->setBusinessInvoice($invoice);
        $entity->setParticular($data['particular']);
        $entity->setPrice($data['price']);
        $entity->setQuantity($quantity);
        $entity->setSubQuantity($quantity);
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
	    $stock = $em->getRepository('BusinessBundle:BusinessParticular')->find($accessoriesId);
	    $entity->setParticular($stock->getName());
	    $entity->setBusinessParticular($stock);
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
        $entity->setQuantity($quantity);
        $particular = $data['particular'];
        $stock = $em->getRepository('BusinessBundle:BusinessParticular')->find($particular);
        $entity->setParticular($stock->getName());
        $entity->setBusinessParticular($stock);
        if(!empty($stock->getUnit())) {
            $entity->setUnit($stock->getUnit()->getName());
        }
        $entity->setPrice($salesPrice);
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


	public function salesStockItemUpdate(BusinessParticular $stockItem)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('SUM(e.quantity) AS quantity');
        $qb->where('e.businessParticular = :stock')->setParameter('stock', $stockItem->getId());
        $qnt = $qb->getQuery()->getOneOrNullResult();
        return $qnt['quantity'];
    }

    public function getSalesItems(BusinessInvoice $sales)
    {
        $entities = $sales->getBusinessInvoiceParticulars();
        $data = '';
        $i = 1;

        /* @var $entity BusinessInvoiceParticular */

        foreach ($entities as $entity) {

            $subQuantity ='';
            if (!empty($entity->getSubQuantity())) {
                $subQuantity = $entity->getHeight().' x '.$entity->getWidth().' = '.$entity->getSubQuantity();
            }
	        $subQnt = ( $subQuantity == '' ) ? 1 : $subQuantity;

            $data .= "<tr id='remove-{$entity->getId()}'>";
            $data .= "<td>{$i}.</td>";
            $data .= "<td>{$entity->getParticular()}</td>";
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
			$qb->andWhere($qb->expr()->like("c.name", "'%$customer%'"  ));
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
			$qb->andWhere("s.created >= :createdStart");
			$qb->setParameter('createdStart', $created);
		}

		if (!empty($createdEnd)) {
			$compareTo = new \DateTime($createdEnd);
			$createdEnd =  $compareTo->format('Y-m-d 23:59:59');
			$qb->andWhere("s.created <= :createdEnd");
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
		$qb->setParameter('process', array('Done','Delivered','Chalan'));
		$this->handleSearchStockBetween($qb,$data);
		$qb->groupBy('si.businessParticular');
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
		$qb->setParameter('process', array('Done','Delivered','Chalan'));
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
	        if ($entity->getSubQuantity() > 0) {
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
        $qb->where('e.process IN (:processes)')->setParameter('processes', array('Done','Delivered')) ;
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
        $qb->addSelect('e.invoice AS invoice');
        $qb->addSelect('e.created AS created');
        $qb->addSelect('mds.name AS name');
        $qb->addSelect('u.name AS unit');
        $qb->addSelect('mds.particularCode AS sku');
        $qb->where('e.businessConfig = :config');
        $qb->setParameter('config', $config);
        $qb->andWhere('e.process IN (:process)');
        $qb->setParameter('process', array('Done','Delivered','Chalan'));
        $this->handleSearchCommissionBetween($qb,$data);
        $qb->orderBy('mds.name','ASC');
        return $qb->getQuery()->getArrayResult();
    }

    protected function handleSearchCommissionBetween($qb,$data)
    {
        $createdStart = isset($data['startDate'])? $data['startDate'] :'';
        $createdEnd = isset($data['endDate'])? $data['endDate'] :'';
        $name = isset($data['name'])? $data['name'] :'';
        $customer = isset($data['customer'])? $data['customer'] :'';
        $vendor = isset($data['vendor'])? $data['vendor'] :'';

        if (!empty($customer)) {
            $qb->andWhere($qb->expr()->like("c.name", "'%$customer%'"  ));
        }
        if (!empty($vendor)) {
            $qb->andWhere($qb->expr()->like("v.id", "'%$vendor%'"  ));
        }
        if (!empty($name)) {
            $qb->andWhere($qb->expr()->like("mds.name", "'%$name%'"  ));
        }
        if (!empty($createdStart)) {
            $compareTo = new \DateTime($createdStart);
            $created =  $compareTo->format('Y-m-d 00:00:00');
            $qb->andWhere("s.created >= :createdStart");
            $qb->setParameter('createdStart', $created);
        }

        if (!empty($createdEnd)) {
            $compareTo = new \DateTime($createdEnd);
            $createdEnd =  $compareTo->format('Y-m-d 23:59:59');
            $qb->andWhere("s.created <= :createdEnd");
            $qb->setParameter('createdEnd', $createdEnd);
        }

    }

}
