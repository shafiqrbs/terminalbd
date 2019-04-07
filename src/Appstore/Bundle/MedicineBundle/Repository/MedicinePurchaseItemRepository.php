<?php

namespace Appstore\Bundle\MedicineBundle\Repository;
use Appstore\Bundle\MedicineBundle\Entity\MedicineConfig;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchase;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchaseItem;
use Appstore\Bundle\MedicineBundle\Entity\MedicineParticular;
use Appstore\Bundle\MedicineBundle\Entity\MedicineStock;
use Appstore\Bundle\MedicineBundle\Entity\MedicineVendor;
use Appstore\Bundle\RestaurantBundle\Form\StockType;
use Doctrine\ORM\EntityRepository;


/**
 * MedicinePurchaseItemRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MedicinePurchaseItemRepository extends EntityRepository
{

    protected function handleSearchBetween($qb,$data)
    {

        $grn = isset($data['grn'])? $data['grn'] :'';
        $vendor = isset($data['vendor'])? $data['vendor'] :'';
        $startDate = isset($data['startDate'])? $data['startDate'] :'';
        $endDate = isset($data['endDate'])? $data['endDate'] :'';
	    $name = isset($data['name'])? $data['name'] :'';
        $rackNo = isset($data['rackNo'])? $data['rackNo'] :'';
        $mode = isset($data['mode'])? $data['mode'] :'';
        $sku = isset($data['sku'])? $data['sku'] :'';
        $brandName = isset($data['brandName'])? $data['brandName'] :'';

        if (!empty($name)) {
            $qb->andWhere($qb->expr()->like("s.name", "'%$name%'"  ));
        }
        if (!empty($sku)) {
            $qb->andWhere($qb->expr()->like("s.sku", "'%$sku%'"  ));
        }
        if (!empty($brandName)) {
            $qb->andWhere($qb->expr()->like("s.brandName", "'%$brandName%'"  ));
        }
        if(!empty($rackNo)){
            $qb->andWhere("s.rackNo = :rack")->setParameter('rack', $rackNo);
        }
        if(!empty($mode)){
            $qb->andWhere("e.mode = :mode")->setParameter('mode', $mode);
        }
        if (!empty($grn)) {
            $qb->andWhere($qb->expr()->like("e.grn", "'%$grn%'"  ));
        }
        if(!empty($vendor)){
            $qb->join("e.medicineVendor",'vendor');
            $qb->andWhere($qb->expr()->like("vendor.companyName", "'%$vendor%'"  ));
        }
	    if (!empty($data['startDate']) ) {
		    $datetime = new \DateTime($startDate);
	    	$qb->andWhere("e.created >= :startDate");
		    $qb->setParameter('startDate', $datetime->format('Y-m-d 00:00:00'));
	    }
	    if (!empty($data['endDate'])) {
            $datetime = new \DateTime($endDate);
            $qb->andWhere("e.created <= :endDate");
		    $qb->setParameter('endDate', $datetime->format('Y-m-d 23:59:59'));
	    }
    }

    public function handleDateRangeFind($qb,$data)
    {
        if(empty($data['startDate']) and empty($data['endDate'])){
            $datetime = new \DateTime("now");
            $qb->andWhere("e.created <= :startDate")->setParameter('startDate', $datetime->format('Y-m-d 00:00:00'));
            $qb->andWhere("e.created >= :endDate")->setParameter('endDate', $datetime->format('Y-m-d 23:59:59'));
        }else{
            $datetime = new \DateTime($data['startDate']);
            $qb->andWhere("e.created <= :startDate")->setParameter('startDate', $datetime->format('Y-m-d 00:00:00'));
            $datetime = new \DateTime($data['endDate']);
            $qb->andWhere("e.created >= :endDate")->setParameter('endDate', $datetime->format('Y-m-d 23:59:59'));
        }

    }

    public function findWithInstantItemSearch($config,$data = array(),$instant = ''){

        $qb = $this->createQueryBuilder('mpi');
        $qb->join('mpi.medicinePurchase','e');
        $qb->join('mpi.medicineStock','s');
        $qb->where('e.medicineConfig = :config')->setParameter('config', $config) ;
        if($instant == 1 ) {
            $qb->andWhere('e.instantPurchase = :instant')->setParameter('instant', $instant);
        }
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('e.updated','DESC');
        $qb->getQuery();
        return  $qb;
    }



    public function findWithSearch(MedicineConfig $config,$data = array(),$instant = ''){

	    $startExpiryDate = isset($data['startExpiryDate'])? $data['startExpiryDate'] :'';
	    $endExpiryDate = isset($data['endExpiryDate'])? $data['endExpiryDate'] :'';
	    $qb = $this->createQueryBuilder('mpi');
        $qb->join('mpi.medicinePurchase','e');
        $qb->join('mpi.medicineStock','s');
        $qb->where('e.medicineConfig = :config')->setParameter('config', $config->getId()) ;
        $qb->andWhere('e.process = :process')->setParameter('process', 'Approved');
       // $qb->andWhere('mpi.remainingQuantity > 0');
        $this->handleSearchBetween($qb,$data);

        if (!empty($startExpiryDate) ) {
		    $datetime = new \DateTime($startExpiryDate);
		    $start = $datetime->format('Y-m-d 00:00:00');
		    $qb->andWhere("mpi.expirationEndDate >= :startDate");
		    $qb->setParameter('startDate', $start);
	    }

	    if (!empty($endExpiryDate)) {
		    $datetime = new \DateTime($endExpiryDate);
		    $end = $datetime->format('Y-m-d 23:59:59');
		    $qb->andWhere("mpi.expirationEndDate <= :endDate");
		    $qb->setParameter('endDate', $end);
	    }
        $qb->orderBy('s.name','ASC');
        $qb->getQuery();
        return  $qb;
    }

    public function stockLedger(MedicineConfig $config,$data = array()){

        $vendor = isset($data['vendor'])? $data['vendor']:'';
        $qb = $this->createQueryBuilder('mpi');
        $qb->join('mpi.medicinePurchase','e');
        $qb->join('mpi.medicineStock','s');
        $qb->where('e.medicineConfig = :config')->setParameter('config', $config->getId()) ;
        $qb->andWhere('e.medicineVendor = :vendor')->setParameter('vendor', $vendor);
        $this->handleDateRangeFind($qb,$data);
        $qb->orderBy('s.name','ASC');
        $qb->getQuery();
        return  $qb;
    }

	public function expiryMedicineCount($user){

		$config =  $user->getGlobalOption()->getMedicineConfig()->getId();
    	$qb = $this->createQueryBuilder('mpi');
		$qb->join('mpi.medicinePurchase','e');
		$qb->select('COUNT(mpi.id) as countId');
		$qb->where('e.medicineConfig = :config')->setParameter('config', $config) ;
		$qb->andWhere('e.process = :process')->setParameter('process', 'Approved');
		$qb->andWhere('mpi.expirationEndDate IS NOT NULL');
		$qb->andWhere('mpi.remainingQuantity > 0');
		$datetime = new \DateTime();
		$start = $datetime->format('Y-m-d 00:00:00');
		$qb->andWhere("mpi.expirationEndDate >= :startDate");
		$qb->setParameter('startDate', $start);
		$count = $qb->getQuery()->getOneOrNullResult()['countId'];
		return  $count;
	}


	public function expiryMedicineSearch($config,$data = array(),$instant = ''){

		$startExpiryDate = isset($data['startExpiryDate']) ? $data['startExpiryDate'] :'';
		$endExpiryDate = isset($data['endExpiryDate']) ? $data['endExpiryDate'] :'';

		$qb = $this->createQueryBuilder('mpi');
        $qb->join('mpi.medicinePurchase','e');
        $qb->join('mpi.medicineStock','s');
        $qb->where('e.medicineConfig = :config')->setParameter('config', $config) ;
        $qb->andWhere('e.process = :process')->setParameter('process', 'Approved');
        $qb->andWhere('mpi.expirationEndDate IS NOT NULL');
        $qb->andWhere('mpi.remainingQuantity > 0');
        if($instant == 1 ) {
            $qb->andWhere('e.instantPurchase = :instant')->setParameter('instant', $instant);
        }
		if(empty($data)){
			$datetime = new \DateTime("now");
			$startExpiryDate = $datetime->format('Y-m-01 00:00:00');
			$endExpiryDate = $datetime->format('Y-m-t 23:59:59');
		}else{
			$datetime = new \DateTime($startExpiryDate);
			$startExpiryDate = $datetime->format('Y-m-d 00:00:00');
			$datetime = new \DateTime($endExpiryDate);
			$endExpiryDate = $datetime->format('Y-m-d 23:59:59');
		}
		if (!empty($startExpiryDate) ) {
			$qb->andWhere("mpi.expirationEndDate >= :startDate");
			$qb->setParameter('startDate', $startExpiryDate);
		}

		if (!empty($endExpiryDate)) {
			$qb->andWhere("mpi.expirationEndDate <= :endDate");
			$qb->setParameter('endDate', $endExpiryDate);
		}
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('mpi.expirationEndDate','ASC');
        $qb->getQuery();
        return  $qb;
    }

    public function medicinePurchaseItemUpdate(MedicinePurchaseItem $item,$fieldName)
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

    public function purchaseStockItemUpdate(MedicineStock $stockItem)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.medicinePurchase', 'mp');
        $qb->select('SUM(e.quantity) AS quantity');
        $qb->where('e.medicineStock = :medicineStock')->setParameter('medicineStock', $stockItem->getId());
        $qb->andWhere('mp.process = :process')->setParameter('process', 'Approved');
        $qnt = $qb->getQuery()->getOneOrNullResult();
        return $qnt['quantity'];
    }

    public function getPurchaseSalesAvg(MedicineStock $stockItem)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.medicinePurchase', 'mp');
        $qb->select('AVG(e.purchasePrice) AS purchase');
        $qb->addSelect('AVG(e.salesPrice) AS sales');
        $qb->where('e.medicineStock = :medicineStock')->setParameter('medicineStock', $stockItem->getId());
        //$qb->andWhere('mp.process = :process')->setParameter('process', 'Approved');
        $avg = $qb->getQuery()->getOneOrNullResult();
        return $avg;
    }

    public function salesMedicinePurchaseItemUpdate(MedicinePurchaseItem $item,$fieldName='')
    {

        $qb = $this->createQueryBuilder('e');
        if($fieldName == 'sales'){
            $quantity = $this->_em->getRepository('MedicineBundle:MedicineSalesItem')->salesPurchaseStockItemUpdate($item);
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

        $qb->where('e.medicineStock = :medicineStock')->setParameter('medicineStock', $item->getMedicineStock()->getId());
        $qnt = $qb->getQuery()->getOneOrNullResult();
        return $qnt['quantity'];
    }

    public function updateRemovePurchaseItemQuantity(MedicinePurchaseItem $item,$fieldName='')
    {

        $em = $this->_em;
        if($fieldName == 'sales'){
	        $quantity = $this->_em->getRepository('MedicineBundle:MedicineSalesItem')->salesPurchaseStockItemUpdate($item);
            $item->setSalesQuantity($quantity);
        }elseif($fieldName == 'sales-return'){
	        $quantity = $this->_em->getRepository('MedicineBundle:MedicineSalesReturn')->salesReturnStockItemUpdate($item);
            $item->setSalesReturnQuantity($quantity);
        }elseif($fieldName == 'purchase-return'){
            $quantity = $this->_em->getRepository('MedicineBundle:MedicinePurchaseReturnItem')->purchaseReturnStockItemUpdate($item);
            $item->setPurchaseReturnQuantity($quantity);
        }elseif($fieldName == 'damage'){
            $quantity = $this->_em->getRepository('MedicineBundle:MedicineDamage')->damagePurchaseStockItemUpdate($item);
            $item->setDamageQuantity($quantity);
        }
        $em->persist($item);
        $em->flush();
        $this->remainingQnt($item);

    }

    public function remainingQnt(MedicinePurchaseItem $item)
    {
        $em = $this->_em;
	    $qnt = ($item->getQuantity()  + $item->getSalesReturnQuantity()) - ($item->getPurchaseReturnQuantity() + $item->getSalesQuantity() + $item->getDamageQuantity());
        $item->setRemainingQuantity($qnt);
        $em->persist($item);
        $em->flush();
    }

    public function updatePurchaseItemPrice(MedicinePurchase $purchase)
    {
        /* @var  $item MedicinePurchaseItem */

        foreach ($purchase->getMedicinePurchaseItems() as $item){

            $em = $this->_em;
            $percentage = $purchase->getDiscountCalculation();
            $purchasePrice = $this->stockInstantPurchaseItemPrice($percentage,$item->getActualPurchasePrice());
            $item->setPurchasePrice($purchasePrice);
            $item->setPurchaseSubTotal($item->getActualPurchasePrice() * $item->getQuantity());
            $em->persist($item);
            $em->flush();

        }
    }

    public function updatePurchaseItem($data)
    {

        $em = $this->_em;
        $entity = $this->_em->getRepository('MedicineBundle:MedicinePurchaseItem')->find($data['purchaseItemId']);
        $salesQnt = $entity->getSalesQuantity() + $entity->getDamageQuantity() + $entity->getPurchaseReturnQuantity();
        if(!empty($entity) and $salesQnt  <= (float)$data['quantity']) {
            $entity->setQuantity($data['quantity']);
            $entity->setPurchasePrice($data['purchasePrice']);
            $entity->setActualPurchasePrice($data['purchasePrice']);
            $entity->setSalesPrice($data['salesPrice']);
            $entity->setPurchaseSubTotal($data['purchasePrice'] * $data['quantity']);
            $entity->setRemainingQuantity($data['quantity']);
        }
        $em->persist($entity);
        $em->flush();
        return $entity->getMedicinePurchase();

    }


    public function insertStockPurchaseItems(MedicinePurchase $purchase,MedicineStock $item, $data)
    {
        $em = $this->_em;
        $entity = new MedicinePurchaseItem();
        $entity->setMedicinePurchase($purchase);
        $entity->setMedicineStock($item);
        if(!empty($data['expirationStartDate'])){
            $expirationStartDate = (new \DateTime($data['expirationStartDate']));
            $entity->setExpirationStartDate($expirationStartDate);
        }
        if(!empty($data['expirationEndDate'])){
            $expirationEndDate = (new \DateTime($data['expirationEndDate']));
            $entity->setExpirationEndDate($expirationEndDate);
        }
        $unitPrice = round(($item->getPurchasePrice()/$item->getPurchaseQuantity()),2);
        $salesPrice = round(($item->getSalesPrice()/$item->getPurchaseQuantity()),2);
        $entity->setPurchaseSubTotal($item->getPurchasePrice());
        $entity->setSalesPrice($salesPrice);
        $entity->setPurchasePrice($unitPrice);
        $entity->setActualPurchasePrice($unitPrice);
        $entity->setQuantity($item->getPurchaseQuantity());
        $entity->setRemainingQuantity($item->getPurchaseQuantity());
        $em->persist($entity);
        $item->setPurchasePrice($unitPrice);
        $item->setSalesPrice($unitPrice);
        $em->flush();
    }

    public function checkInsertStockItem(MedicineConfig $config,$data){

        if(empty($data['medicineId'])) {
            $checkStockMedicine = $this->_em->getRepository('MedicineBundle:MedicineStock')->checkDuplicateStockNonMedicine($config, $data['medicineBrand']);
        }else{
            $medicine =  $this->_em->getRepository('MedicineBundle:MedicineBrand')->find($data['medicineId']);
            $checkStockMedicine =  $this->_em->getRepository('MedicineBundle:MedicineStock')->checkDuplicateStockMedicine($config, $medicine);
        }

        if (empty($checkStockMedicine)){
            $em = $this->_em;
            $entity = new MedicineStock();
            $entity->setMedicineConfig($config);
            if(empty($data['medicineId'])){
                if($data['mode']){
                    $brandName = $this->_em->getRepository('MedicineBundle:MedicineParticular')->find($data['mode']);
                    $entity->setMode($brandName->getParticularType()->getSlug());
                    $entity->setBrandName($brandName->getName());
                }
                $entity->setName($data['medicineBrand']);
            }else{
                $entity->setMedicineBrand($medicine);
                $name = $medicine->getMedicineForm().' '.$medicine->getName().' '.$medicine->getStrength();
                $entity->setName($name);
                $entity->setBrandName($medicine->getMedicineCompany()->getName());
                $entity->setMode('medicine');
            }
            if(!empty($data['rackNo'])){
                $entity->setRackNo($this->_em->getRepository('MedicineBundle:MedicineParticular')->find($data['rackNo']));
            }
            $entity->setSalesPrice($data['salesPrice']);
            $entity->setPurchasePrice($this->stockInstantPurchaseItemPrice($config->getInstantVendorPercentage(),$data['salesPrice']));
            $em->persist($entity);
            $em->flush();
            return $entity;
        }else{
            return $checkStockMedicine;
        }
    }

    public function stockInstantPurchaseItemPrice($percentage,$price)
    {

        $discount = (($price * $percentage )/100);
        $purchasePrice = ($price - $discount);
        return round($purchasePrice,2);

    }

    public function stockPurchaseItemPrice(MedicineConfig $config,$price)
    {
        $discount = (($price * $config->getVendorPercentage())/100);
        $purchasePrice = ($price - $discount);
        return round($purchasePrice,2);
    }

    public function insertPurchaseItems(MedicineConfig $config,MedicinePurchase $purchase, $data)
    {

        $item = $this->checkInsertStockItem($config,$data);

        $em = $this->_em;
        $entity = new MedicinePurchaseItem();
        $entity->setMedicinePurchase($purchase);
        $entity->setMedicineStock($item);
        if(!empty($data['expirationEndDate'])){
            $expirationStartDate = (new \DateTime($data['expirationEndDate']));
            $entity->setExpirationStartDate($expirationStartDate);
        }
        if(!empty($data['expirationEndDate'])){
            $expirationEndDate = (new \DateTime($data['expirationEndDate']));
            $entity->setExpirationEndDate($expirationEndDate);
        }
        $unitPrice = round(($data['salesPrice']/$data['purchaseQuantity']),2);
        $entity->setSalesPrice($unitPrice);
        $entity->setPurchasePrice($this->stockInstantPurchaseItemPrice($config->getInstantVendorPercentage(),$unitPrice));
        $entity->setActualPurchasePrice($unitPrice);
        $entity->setQuantity($data['purchaseQuantity']);
        $entity->setRemainingQuantity($data['purchaseQuantity']);
        $entity->setPurchaseSubTotal($data['purchaseQuantity'] * $entity->getPurchasePrice());
        $em->persist($entity);
        $em->flush();
        return $entity;
    }

    public function getInstantPurchaseItem(MedicineConfig $config,$data = array()){

        $qb = $this->createQueryBuilder('pi');
        $qb->join('pi.medicinePurchase','e');
        $qb->where('e.medicineConfig = :config')->setParameter('config',$config->getId());
        $qb->andWhere('e.instantPurchase = 1');
        $this->handleDateRangeFind($qb,$data);
        $result = $qb->getQuery()->getResult();
        return $result;
    }

    public function getPurchaseItems(MedicinePurchase $sales)
    {
        $entities = $sales->getMedicinePurchaseItems();
        $data = '';
        $i = 1;
        /* @var $entity MedicinePurchaseItem */

        foreach ($entities as $entity) {

            if(!empty($entity->getExpirationEndDate())){
	            $expiration = $entity->getExpirationEndDate()->format('M y');
            }else{
                $expiration='';
            }
            $rack ="";
            if(!empty($entity->getMedicineStock()->getRackNo())){
               $rack = $entity->getMedicineStock()->getRackNo()->getName();
            }

            $data .= '<tr id="remove-'. $entity->getId().'">';
            $data .= '<td class="span1" >' . $i.'. '.$entity->getBarcode().'</td>';
            $data .= '<td class="span3" >' . $entity->getMedicineStock()->getName() .'</td>';
            $data .= '<th class="span1" >' .$rack. '</th>';
            $data .= '<th class="span1" >' .$expiration. '</th>';
            $data .= "<td class='span1' >";
            $data .= "<input type='text' class='numeric td-inline-input purchasePrice' data-id='{$entity->getid()}' autocomplete='off' id='purchasePrice-{$entity->getId()}' name='purchasePrice' value='{$entity->getActualPurchasePrice()}'>";
            $data .= "</td>";
            $data .= "<td class='span1' >";
            $data .= "<input type='text' class='numeric td-inline-input salesPrice' data-id='{$entity->getid()}' autocomplete='off' id='salesPrice-{$entity->getId()}' name='salesPrice' value='{$entity->getSalesPrice()}'>";
            $data .= "</td>";
            $data .= "<td class='span1' >";
            $data .= "<input type='hidden' id='purchaseQuantity-{$entity->getId()}'  value='{$entity->getQuantity()}' >";
            $data .= "<input type='hidden' id='salesQuantity-{$entity->getId()}'  value='{$entity->getSalesQuantity()}' >";
            $data .= "<input type='text' class='numeric td-inline-input quantity' data-id='{$entity->getid()}' autocomplete='off' id='quantity-{$entity->getId()}' name='quantity' value='{$entity->getQuantity()}'>";
            $data .= "</td>";
            $data .= "<td class='span1' id='subTotal-{$entity->getid()}'>{$entity->getPurchaseSubTotal()}</td>";
            $data .= '<td class="span1" >' . $entity->getSalesQuantity(). '</td>';
            $data .= '<td class="span1" >
                     <a id="'.$entity->getId(). '" data-url="/medicine/purchase/' . $sales->getId() . '/' . $entity->getId() . '/particular-delete" href="javascript:" class="btn red mini delete" ><i class="icon-trash"></i></a>
                     </td>';
            $data .= '</tr>';
            $i++;
        }
        return $data;
    }

    public function mergePurchaseItem(MedicinePurchase $purchase , MedicineVendor $vendor){


        $em = $this->_em;
        $qb = $this->createQueryBuilder('pi');
        $qb->join('pi.medicinePurchase','e');
        $qb->where('e.medicineConfig = :config')->setParameter('config',$purchase->getMedicineConfig()->getId());
        $qb->andWhere('e.medicineVendor = :vendor')->setParameter('vendor',$vendor->getId());
        $result = $qb->getQuery()->getResult();

        /* @var $row MedicinePurchaseItem */

        foreach ($result as $row){

            $entity = new MedicinePurchaseItem();
            $entity->setMedicinePurchase($purchase);
            $entity->setActualPurchasePrice($row->getActualPurchasePrice());
            $entity->setPurchasePrice($row->getActualPurchasePrice());
            $entity->setSalesPrice($row->getSalesPrice());
            $entity->setMedicineStock($row->getMedicineStock());
            $entity->setExpirationEndDate($row->getExpirationStartDate());
            $entity->setExpirationStartDate($row->getExpirationEndDate());
            $em->persist($entity);
            $em->flush();

        }






    }


}
