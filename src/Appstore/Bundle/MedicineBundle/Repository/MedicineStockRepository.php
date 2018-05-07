<?php

namespace Appstore\Bundle\MedicineBundle\Repository;
use Appstore\Bundle\InventoryBundle\Entity\Sales;
use Appstore\Bundle\InventoryBundle\Entity\SalesItem;
use Appstore\Bundle\MedicineBundle\Entity\MedicineBrand;
use Appstore\Bundle\MedicineBundle\Entity\MedicineConfig;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchaseItem;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSales;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSalesItem;
use Appstore\Bundle\MedicineBundle\Entity\MedicineStock;
use Doctrine\ORM\EntityRepository;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchase;


/**
 * PathologyRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MedicineStockRepository extends EntityRepository
{


    protected function handleSearchBetween($qb,$data)
    {

        $name = isset($data['name'])? $data['name'] :'';
        $rackNo = isset($data['rackNo'])? $data['rackNo'] :'';
        $mode = isset($data['mode'])? $data['mode'] :'';
        $sku = isset($data['sku'])? $data['sku'] :'';
        $brandName = isset($data['brandName'])? $data['brandName'] :'';

        if (!empty($name)) {
            $qb->andWhere($qb->expr()->like("e.name", "'%$name%'"  ));
        }
        if (!empty($sku)) {
            $qb->andWhere($qb->expr()->like("e.sku", "'%$sku%'"  ));
        }
         if (!empty($brandName)) {
            $qb->andWhere($qb->expr()->like("e.brandName", "'%$brandName%'"  ));
        }
        if(!empty($rackNo)){
            $qb->andWhere("e.rackNo = :rack")->setParameter('rack', $rackNo);
        }
        if(!empty($mode)){
            $qb->andWhere("e.mode = :mode")->setParameter('mode', $mode);
        }
    }

    public function checkDuplicateStockMedicine(MedicineConfig $config,MedicineBrand $brand)
    {
      $stock =  $this->findOneBy(array('medicineConfig'=>$config,'medicineBrand'=>$brand));
      return $stock;
    }

    public function findWithSearch($config,$data){

        $qb = $this->createQueryBuilder('e');
        $qb->where('e.medicineConfig = :config')->setParameter('config', $config) ;
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('e.sku','ASC');
        $qb->getQuery();
        return  $qb;
    }

    public function findWithShortListSearch($config,$data)
    {

        $item = isset($data['item'])? $data['item'] :'';
        $brand = isset($data['brand'])? $data['brand'] :'';
        $sku = isset($data['sku'])? $data['sku'] :'';
        $minQnt = isset($data['minQnt'])? $data['minQnt'] :'';

        $qb = $this->createQueryBuilder('item');
        $qb->where("item.medicineConfig = :config");
        $qb->setParameter('config', $config);
        $qb->andWhere("item.minQuantity > 0");
        if($minQnt == 'minimum') {
            $qb->andWhere("item.minQuantity >= item.remainingQuantity");
        }
        if (!empty($sku)) {
            $qb->andWhere($qb->expr()->like("item.sku", "'%$sku%'"  ));
        }
        if (!empty($item)) {

            $qb->andWhere("item.name = :name");
            $qb->setParameter('name', $item);
        }
        if (!empty($brand)) {
            $qb->join('item.medicineBrand', 'b');
            $qb->andWhere("b.name = :brand");
            $qb->setParameter('brand', $brand);
        }
        $qb->orderBy('item.name','ASC');
        $qb->getQuery();
        return  $qb;

    }

    public function getFindWithParticular($hospital,$services){

        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.service','s')
            ->select('e.id')
            ->addSelect('e.name')
            ->addSelect('e.name')
            ->addSelect('e.particularCode')
            ->addSelect('e.mobile')
            ->addSelect('e.price')
            ->addSelect('e.minimumPrice')
            ->addSelect('e.quantity')
            ->addSelect('s.name as serviceName')
            ->addSelect('s.code as serviceCode')
            ->where('e. = :config')->setParameter('config', $hospital)
            ->andWhere('s.id IN(:service)')
            ->setParameter('service',array_values($services))
            ->orderBy('e.service','ASC')
            ->orderBy('e.name','ASC')
            ->getQuery()->getArrayResult();
        return  $qb;
    }

    public function getPurchaseUpdateQnt(MedicinePurchase $purchase){

        /** @var  $purchaseItem MedicinePurchaseItem */

        if(!empty($purchase->getMedicinePurchaseItems())) {
            foreach ($purchase->getMedicinePurchaseItems() as $purchaseItem) {
                 $stockItem = $purchaseItem->getMedicineStock();
                 $this->updateRemovePurchaseQuantity($stockItem);
            }
        }
    }

    public function updateRemovePurchaseQuantity(MedicineStock $stock,$fieldName=''){
        $em = $this->_em;
        if($fieldName == 'sales'){
            $qnt = $em->getRepository('MedicineBundle:MedicineSalesItem')->salesStockItemUpdate($stock);
            $stock->setSalesQuantity($qnt);
        }elseif($fieldName == 'sales-return'){
            $quantity = $this->_em->getRepository('MedicineBundle:MedicineSalesReturn')->salesReturnStockUpdate($stock);
            $item->setSalesReturnQuantity($quantity);
        }elseif($fieldName == 'purchase-return'){
            $qnt = $em->getRepository('MedicineBundle:MedicinePurchaseReturnItem')->purchaseReturnStockUpdate($stock);
            $stock->setPurchaseReturnQuantity($qnt);
        }elseif($fieldName == 'damage'){
            $quantity = $em->getRepository('MedicineBundle:MedicineDamage')->damageStockItemUpdate($stock);
            $stock->setDamageQuantity($quantity);
        }else{
            $qnt = $em->getRepository('MedicineBundle:MedicinePurchaseItem')->purchaseStockItemUpdate($stock);
            $stock->setPurchaseQuantity($qnt);
        }
        $em->persist($stock);
        $em->flush();
        $this->remainingQnt($stock);
    }

    public function remainingQnt(MedicineStock $stock)
    {
        $em = $this->_em;
        $qnt = ($stock->getOpeningQuantity() + $stock->getPurchaseQuantity() + $stock->getSalesReturnQuantity()) - ($stock->getPurchaseReturnQuantity()+$stock->getSalesQuantity()+$stock->getDamageQuantity());
        $stock->setRemainingQuantity($qnt);
        $em->persist($stock);
        $em->flush();
    }

    public function getSalesUpdateQnt(MedicineSales $invoice){

        $em = $this->_em;

        /** @var $item MedicineSalesItem */

        foreach($invoice->getMedicineSalesItems() as $item ){

            /** @var  $stock MedicineStock */
            $stock = $item->getMedicineStock();
            $qnt = $this->_em->getRepository('MedicineBundle:MedicineSalesItem')->salesStockItemUpdate($stock);
            $stock->setSalesQuantity($qnt);
            $em->persist($stock);
            $em->flush();
            $this->remainingQnt($stock);
        }
    }

    public function updateRemoveSalesQuantity(MedicineStock $stock){

        $em = $this->_em;
        $qnt = $em->getRepository('MedicineBundle:MedicineSalesItem')->salesStockItemUpdate($stock);
        $stock->setPurchaseQuantity($qnt);
        $em->persist($stock);
        $em->flush();
        $this->remainingQnt($stock);
    }

    public function findMedicineExistingCustomer($hospital, $mobile,$data)
    {
        $em = $this->_em;

        $name = $data['referredDoctor']['name'];
        $department = $data['referredDoctor']['department'];
        $location = $data['referredDoctor']['location'];
        $address = $data['referredDoctor']['address'];
        $entity = $em->getRepository('MedicineBundle:Particular')->findOneBy(array('hospitalConfig' => $hospital ,'service' => 6 ,'mobile' => $mobile));
        if($entity){

            return $entity;

        }else{

            $entity = new Particular();
            if(!empty($location)){
                $location = $em->getRepository('SettingLocationBundle:Location')->find($location);
                $entity->setLocation($location);
            }
            if(!empty($department)){
                $department = $em->getRepository('MedicineBundle:MedicineCategory')->find($department);
                $entity->setDepartment($department);
            }
            $entity->setService($em->getRepository('MedicineBundle:Service')->find(6));
            $entity->setMobile($mobile);
            $entity->setName($name);
            $entity->setAddress($address);
            $entity->setMedicineConfig($hospital);
            $em->persist($entity);
            $em->flush($entity);
            return $entity;
        }

    }


    public function insertAccessories(Invoice $invoice){

        $em = $this->_em;

        $em = $this->_em;
        /** @var InvoiceParticular $item */
        if(!empty($invoice->getInvoiceParticulars())){
            foreach($invoice->getInvoiceParticulars() as $item ){
                /** @var Particular  $particular */
                $particular = $item->getParticular();
                if( $particular->getService()->getId() == 4 ){
                    $qnt = ($particular->getSalesQuantity() + $item->getQuantity());
                    $particular->setSalesQuantity($qnt);
                    $em->persist($particular);
                    $em->flush();
                }
            }
        }
    }



    public function admittedPatientAccessories(InvoiceTransaction $transaction){

        $em = $this->_em;

        /** @var InvoiceParticular $item */
        if(!empty($transaction->getAdmissionPatientParticulars())){

            foreach($transaction->getAdmissionPatientParticulars() as $item ){

                /** @var Particular  $particular */

                $particular = $item->getParticular();
                if( $particular->getService()->getId() == 4 ){
                    $qnt = ($particular->getSalesQuantity() + $item->getQuantity());
                    $particular->setSalesQuantity($qnt);
                    $em->persist($particular);
                    $em->flush();
                }
            }
        }

    }


    public function searchAutoComplete($q, MedicineConfig $config)
    {

        $query = $this->createQueryBuilder('e');
        $query->join('e.medicineConfig', 'ic');
        $query->join('e.rackNo', 'rack');
        $query->join('e.unit', 'unit');
        $query->select('e.id as id');
        $query->addSelect('CONCAT(e.sku, \' - \', e.name,  \' [\', e.remainingQuantity, \'] \', unit.name,\' => \', rack.name) AS text');
        $query->where($query->expr()->like("e.name", "'%$q%'"  ));
        $query->andWhere("ic.id = :config");
        $query->setParameter('config', $config->getId());
        $query->groupBy('e.name');
        $query->orderBy('e.name', 'ASC');
        $query->setMaxResults( '30' );
        return $query->getQuery()->getResult();

    }


    public function searchNameAutoComplete($q, MedicineConfig $config)
    {

        $query = $this->createQueryBuilder('e');
        $query->join('e.medicineConfig', 'ic');
        $query->select('e.name as id');
        $query->addSelect('e.name as text');
        $query->where($query->expr()->like("e.name", "'%$q%'"  ));
        $query->andWhere("ic.id = :config");
        $query->setParameter('config', $config->getId());
        $query->groupBy('e.name');
        $query->orderBy('e.name', 'ASC');
        $query->setMaxResults( '30' );
        return $query->getQuery()->getResult();

    }


    public function searchAutoCompleteBrandName($q, MedicineConfig $config)
    {

        $query = $this->createQueryBuilder('e');
        $query->join('e.medicineConfig', 'ic');
        $query->select('e.brandName as id');
        $query->addSelect('e.brandName as text');
        $query->where($query->expr()->like("e.brandName", "'%$q%'"  ));
        $query->andWhere("ic.id = :config");
        $query->setParameter('config', $config->getId());
        $query->groupBy('e.brandName');
        $query->orderBy('e.name', 'ASC');
        $query->setMaxResults( '30' );
        return $query->getQuery()->getResult();

    }

}
