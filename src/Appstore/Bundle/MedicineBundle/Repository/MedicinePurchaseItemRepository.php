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
            $qb->andWhere("mpi.expirationStartDate >= :startDate");
            $qb->setParameter('startDate', $start);
        }

        if (!empty($endDate)) {
            $datetime = new \DateTime($data['endDate']);
            $end = $datetime->format('Y-m-d 23:59:59');
            $qb->andWhere("mpi.expirationEndDate >= :endDate");
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

    public function findWithSearch($config,$data = array(),$instant = ''){

        $qb = $this->createQueryBuilder('mpi');
        $qb->join('mpi.medicinePurchase','e');
        $qb->join('mpi.medicineStock','s');
        $qb->where('e.medicineConfig = :config')->setParameter('config', $config) ;
        if($instant == 1 ) {
            $qb->andWhere('e.instantPurchase = :instant')->setParameter('instant', $instant);
        }
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('mpi.expirationDate','ASC');
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

    public function updateRemovePurchaseItemQuantity(MedicinePurchaseItem $item,$fieldName=''){
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



    public function getPurchaseAveragePrice(MedicineParticular $particular)
    {

        $qb = $this->_em->createQueryBuilder();
        $qb->from('MedicineBundle:MedicinePurchaseItem','e');
        $qb->select('AVG(e.purchasePrice) AS avgPurchasePrice');
        $qb->where('e.dmsParticular = :particular')->setParameter('particular', $particular) ;
        $res = $qb->getQuery()->getOneOrNullResult();
        if(!empty($res)){
            $particular->setPurchaseAverage($res['avgPurchasePrice']);
            $this->_em->persist($particular);
            $this->_em->flush($particular);
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
        $entity->setSalesPrice($item->getSalesPrice());
        $entity->setPurchasePrice($item->getPurchasePrice());
        $entity->setQuantity($item->getPurchaseQuantity());
        $entity->setRemainingQuantity($item->getPurchaseQuantity());
        $entity->setPurchaseSubTotal($item->getPurchaseQuantity()* $item->getPurchasePrice());
        $em->persist($entity);
        $em->flush();
    }

    public function checkInsertStockItem($config,$data){

        $medicine = $this->_em->getRepository('MedicineBundle:MedicineBrand')->find($data['medicineBrand']);
        $checkStockMedicine = $this->_em->getRepository('MedicineBundle:MedicineStock')->checkDuplicateStockMedicine($config,$medicine);
        if (empty($checkStockMedicine)){
            $em = $this->_em;
            $entity = new MedicineStock();
            $entity->setMedicineConfig($config);
            $entity->setMedicineBrand($medicine);
            $name = $medicine->getMedicineForm().' '.$medicine->getName().' '.$medicine->getStrength();
            $entity->setName($name);
            if(!empty($data['rackNo'])){
                $entity->setRackNo($this->_em->getRepository('MedicineBundle:MedicineParticular')->find($data['rackNo']));
            }
            $entity->setSalesPrice($data['salesPrice']);
            $entity->setPurchasePrice($data['purchasePrice']);
            $entity->setBrandName($medicine->getMedicineCompany()->getName());
            $entity->setMode('medicine');
            $em->persist($entity);
            $em->flush();
            return $entity;
        }else{
            return $checkStockMedicine;
        }
    }

    public function insertPurchaseItems($config,MedicinePurchase $purchase, $data)
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
        $entity->setSalesPrice($item->getSalesPrice());
        $entity->setPurchasePrice($data['purchasePrice']);
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
            $data .= '<td class="span1" >'.$entity->getSalesPrice().'</td>';
            $data .= '<td class="span1" ><a class="editable editable-click" data-name="Quantity" href="#" data-url="/medicine/purchase/purchase-item-inline-update?id='.$entity->getId().'" data-type="text" data-pk="'.$entity->getId().'" data-original-title="Change quantity">'.$entity->getQuantity().'</a></td>';
            $data .= '<td class="span1" >' . $entity->getSalesQuantity(). '</td>';
            $data .= '<td class="span1" >' . $entity->getPurchaseSubTotal() . '</td>';
            $data .= '<td class="span1" >
                     <a id="'.$entity->getId(). '" title="Are you sure went to delete ?" data-url="/medicine/purchase/' . $sales->getId() . '/' . $entity->getId() . '/particular-delete" href="javascript:" class="btn red mini delete" ><i class="icon-trash"></i></a>
                     </td>';
            $data .= '</tr>';
            $i++;
        }
        return $data;
    }

    public function invoiceMedicineParticularLists($hospital,$data = array()){

        $invoice = isset($data['invoice'])? $data['invoice'] :'';
        $particular = isset($data['particular'])? $data['particular'] :'';
        $category = isset($data['category'])? $data['category'] :'';

        $qb = $this->createQueryBuilder('e');
        $qb->select('e');
        $qb->join('e.invoice','invoice');
        $qb->join('e.particular','particular');
        $qb->join('particular.category','category');
        $qb->where('particular.service = :service')->setParameter('service', 1) ;
        /*            $qb->andWhere('invoice.hospitalConfig = :hospital')->setParameter('hospital', $hospital) ;
                    $qb->andWhere('particular.process IN(:process)');
                    $qb->setParameter('process',array_values(array('In-progress','Damage','Impossible')));
                    if (!empty($invoice)) {
                        $qb->andWhere($qb->expr()->like("invoice.invoice", "'%$invoice%'"  ));
                    }
                    if (!empty($particular)) {
                        $qb->andWhere('particular.name = :partName')->setParameter('partName', $particular) ;
                    }
                    if (!empty($category)) {
                        $qb->andWhere('category.name = :catName')->setParameter('catName', $category) ;
                    }*/
        $qb->orderBy('e.updated','DESC');
        $qb->getQuery();
        return  $qb;

    }
}
