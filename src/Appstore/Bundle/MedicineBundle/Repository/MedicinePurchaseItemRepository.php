<?php

namespace Appstore\Bundle\MedicineBundle\Repository;
use Appstore\Bundle\MedicineBundle\Entity\MedicineConfig;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchase;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchaseItem;
use Appstore\Bundle\MedicineBundle\Entity\MedicineParticular;
use Appstore\Bundle\MedicineBundle\Entity\MedicineStock;
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
            $qb->andWhere("e.medicineVendor = :vendor")->setParameter('vendor', $vendor);
        }
        if (!empty($startDate) ) {
            $datetime = new \DateTime($data['startDate']);
            $start = $datetime->format('Y-m-d 00:00:00');
            $qb->andWhere("mpi.expirationEndDate >= :startDate");
            $qb->setParameter('startDate', $start);
        }

        if (!empty($endDate)) {
            $datetime = new \DateTime($data['endDate']);
            $end = $datetime->format('Y-m-d 23:59:59');
            $qb->andWhere("mpi.expirationEndDate <= :endDate");
            $qb->setParameter('endDate', $end);
        }
    }

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



    public function findWithSearch($config,$data = array(),$instant = ''){

        $qb = $this->createQueryBuilder('mpi');
        $qb->join('mpi.medicinePurchase','e');
        $qb->join('mpi.medicineStock','s');
        $qb->where('e.medicineConfig = :config')->setParameter('config', $config) ;
        $qb->andWhere('e.process = :process')->setParameter('process', 'Approved');
        $qb->andWhere('mpi.remainingQuantity > 0');
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('s.name','ASC');
        $qb->getQuery();
        return  $qb;
    }

    public function expiryMedicineSearch($config,$data = array(),$instant = ''){

        $qb = $this->createQueryBuilder('mpi');
        $qb->join('mpi.medicinePurchase','e');
        $qb->join('mpi.medicineStock','s');
        $qb->where('e.medicineConfig = :config')->setParameter('config', $config) ;
        $qb->andWhere('e.process = :process')->setParameter('process', 'Approved');
        $qb->andWhere('mpi.expirationStartDate IS NOT NULL');
        $qb->andWhere('mpi.remainingQuantity > 0');
        if($instant == 1 ) {
            $qb->andWhere('e.instantPurchase = :instant')->setParameter('instant', $instant);
        }
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('mpi.expirationStartDate','ASC');
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
        $qnt = ($item->getQuantity()  + $item->getSalesReturnQuantity()) - ($item->getPurchaseReturnQuantity()+$item->getSalesQuantity()+$item->getDamageQuantity());
        $item->setRemainingQuantity($qnt);
        $em->persist($item);
        $em->flush();
    }

    public function updatePurchaseItemPrice(MedicinePurchase $purchase)
    {
        /* @var MedicinePurchaseItem $item */

        foreach ($purchase->getMedicinePurchaseItems() as $item){

            $em = $this->_em;
            $percentage = $purchase->getDiscountCalculation();
            $purchasePrice = $this->stockInstantPurchaseItemPrice($percentage,$item->getActualPurchasePrice());
            $item->setPurchasePrice($purchasePrice);
            $em->persist($item);
            $em->flush();

        }
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
        if(!empty($data['expirationStartDate'])){
            $expirationStartDate = (new \DateTime($data['expirationStartDate']));
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
        $entity->setPurchaseSubTotal($data['salesPrice']);
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

            if(!empty($entity->getExpirationEndDate()) and !empty($entity->getExpirationStartDate())){
                $expirationStartDate = $entity->getExpirationStartDate()->format('M y');
                $expirationEndDate = $entity->getExpirationEndDate()->format('M y');
                $expiration = $expirationStartDate.' To '.$expirationEndDate;
            }else{
                $expiration='';
            }

            $data .= '<tr id="remove-'. $entity->getId().'">';
            $data .= '<td class="span1" >' . $i.'. '.$entity->getBarcode().'</td>';
            $data .= '<td class="span3" >' . $entity->getMedicineStock()->getName() . '</td>';
            $data .= '<th class="span1" >' .$expiration. '</th>';
            $data .= '<td class="span1" ><a class="editable editable-click" data-name="PurchasePrice" href="#" data-url="/medicine/purchase/purchase-item-inline-update?id='.$entity->getId().'" data-type="text" data-pk="'.$entity->getId().'" data-original-title="Change purchase price">'.$entity->getPurchasePrice().'</a></td>';
            $data .= '<td class="span1" ><a class="editable editable-click" data-name="SalesPrice" href="#" data-url="/medicine/purchase/purchase-item-inline-update?id='.$entity->getId().'" data-type="text" data-pk="'.$entity->getId().'" data-original-title="Change MRP price">'.$entity->getSalesPrice().'</a></td>';
            $data .= '<td class="span1" ><a class="editable editable-click" data-name="Quantity" href="#" data-url="/medicine/purchase/purchase-item-inline-update?id='.$entity->getId().'" data-type="text" data-pk="'.$entity->getId().'" data-original-title="Change quantity">'.$entity->getQuantity().'</a></td>';
            $data .= '<td class="span1" >' . $entity->getSalesQuantity(). '</td>';
            $data .= '<td class="span1" >' . $entity->getPurchaseSubTotal() . '</td>';
            $data .= '<td class="span1" >
                     <a id="'.$entity->getId(). '" data-url="/medicine/purchase/' . $sales->getId() . '/' . $entity->getId() . '/particular-delete" href="javascript:" class="btn red mini delete" ><i class="icon-trash"></i></a>
                     </td>';
            $data .= '</tr>';
            $i++;
        }
        return $data;
    }


}
