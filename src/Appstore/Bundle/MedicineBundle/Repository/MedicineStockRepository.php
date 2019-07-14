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
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchase;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;


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
        $webName = isset($data['item']['webName'])? $data['item']['webName'] :'';
        if (!empty($name)) {
            $qb->andWhere($qb->expr()->like("e.name", "'%$name%'"  ));
        }
        if (!empty($webName)) {
            $aKeyword = explode(" ", $webName);
            $qb->andWhere($qb->expr()->like("e.name", "'%$webName%'"  ));
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
      $stock =  $this->findOneBy(array('medicineConfig'=>$config,'medicineBrand'=> $brand));
      return $stock;
    }

    public function checkDuplicateStockNonMedicine(MedicineConfig $config,$brand)
    {
        $stock =  $this->findOneBy(array('medicineConfig' => $config,'name' => $brand));
        return $stock;
    }

    public function getPurchaseDetails(MedicineConfig $config,MedicineStock $stock){

    	$qb = $this->_em->createQueryBuilder();
	    $qb->from('MedicineBundle:MedicinePurchaseItem','e');
	    $qb->join('e.medicinePurchase','mp');
	    $qb->select('e');
	    $qb->where('e.medicineStock = :item')->setParameter('item',$stock->getId());
	    $qb->andWhere('mp.medicineConfig = :config')->setParameter('config',$config->getId());
	    $qb->orderBy('mp.created','DESC');
	    $result = $qb->getQuery();
	    return $result;

    }

    public function getPurchaseReturnDetails(MedicineConfig $config,MedicineStock $stock){

    	$qb = $this->_em->createQueryBuilder();
	    $qb->from('MedicineBundle:MedicinePurchaseReturnItem','e');
	    $qb->join('e.medicinePurchaseReturn','mp');
	    $qb->select('e');
	    $qb->where('e.medicineStock = :item')->setParameter('item',$stock->getId());
	    $qb->andWhere('mp.medicineConfig = :config')->setParameter('config',$config->getId());
	    $qb->orderBy('mp.created','DESC');
	    $result = $qb->getQuery();
	    return $result;

    }

    public function getSalesDetails(MedicineConfig $config,MedicineStock $stock){

    	$qb = $this->_em->createQueryBuilder();
	    $qb->from('MedicineBundle:MedicineSalesItem','e');
	    $qb->join('e.medicineSales','mp');
	    $qb->select('e');
	    $qb->where('e.medicineStock = :item')->setParameter('item',$stock->getId());
	    $qb->andWhere('mp.medicineConfig = :config')->setParameter('config',$config->getId());
	    $qb->orderBy('mp.created','DESC');
	    $result = $qb->getQuery();
	    return $result;

    }

    public function getSalesReturnDetails(MedicineConfig $config,MedicineStock $stock){

    	$qb = $this->_em->createQueryBuilder();
	    $qb->from('MedicineBundle:MedicineSalesReturn','e');
	    $qb->select('e');
	    $qb->where('e.medicineStock = :item')->setParameter('item',$stock->getId());
	    $qb->andWhere('e.medicineConfig = :config')->setParameter('config',$config->getId());
	    $qb->orderBy('e.created','DESC');
	    $result = $qb->getQuery();
	    return $result;

    }

    public function getDamageDetails(MedicineConfig $config,MedicineStock $stock){

    	$qb = $this->_em->createQueryBuilder();
	    $qb->from('MedicineBundle:MedicineDamage','e');
	    $qb->select('e');
	    $qb->where('e.medicineStock = :item')->setParameter('item',$stock->getId());
	    $qb->andWhere('e.medicineConfig = :config')->setParameter('config',$config->getId());
	    $qb->orderBy('e.created','DESC');
	    $result = $qb->getQuery();
	    return $result;

    }

    public function findWithSearch($config,$data){

        $qb = $this->createQueryBuilder('e');
        $qb->where('e.medicineConfig = :config')->setParameter('config', $config) ;
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('e.sku','ASC');
        $qb->getQuery()->getArrayResult();
        return  $qb;
    }

    public function reportCurrentStockPrice(User $user)
    {

	    $config =  $user->getGlobalOption()->getMedicineConfig()->getId();
	    $qb = $this->createQueryBuilder('e');
	    $qb->select('SUM(e.purchasePrice * e.remainingQuantity) as purchasePrice, SUM(e.salesPrice * e.remainingQuantity) as salesPrice');
	    $qb->where('e.medicineConfig = :config')->setParameter('config', $config) ;
	    $result = $qb->getQuery()->getOneOrNullResult();
	    return $result;

    }

	public function findMedicineShortListCount($user)
	{
		$config =  $user->getGlobalOption()->getMedicineConfig()->getId();
		$qb = $this->createQueryBuilder('item');
		$qb->select('COUNT(item.id) as totalShortList');
		$qb->where("item.medicineConfig = :config");
		$qb->setParameter('config', $config);
		$qb->andWhere("item.minQuantity > 0");
		$qb->andWhere("item.minQuantity >= item.remainingQuantity");
		$count = $qb->getQuery()->getOneOrNullResult()['totalShortList'];
		return  $count;

	}

    public function findWithShortListSearch($config,$data)
    {

        $name = isset($data['name'])? $data['name'] :'';
        $brand = isset($data['brandName'])? $data['brandName'] :'';
        $sku = isset($data['sku'])? $data['sku'] :'';
        $minQnt = isset($data['minQnt'])? $data['minQnt'] :'';
        $qb = $this->createQueryBuilder('item');
        $qb->where("item.medicineConfig = :config");
        $qb->setParameter('config', $config);
        $qb->andWhere("item.minQuantity > 0");
        if($minQnt == 'minimum') {
            $qb->andWhere("item.minQuantity > item.remainingQuantity");
        }
        if (!empty($sku)) {
            $qb->andWhere($qb->expr()->like("item.sku", "'%$sku%'"  ));
        }
        if (!empty($brand)) {
            $qb->andWhere($qb->expr()->like("item.brandName", "'%$brand%'"  ));
        }
        if (!empty($name)) {
             $qb->andWhere($qb->expr()->like("item.name", "'%$name%'"  ));
        }
        $qb->orderBy('item.name','ASC');
        $qb->getQuery();
        return  $qb;

    }

    public function getBrandLists($user)
    {
        $config =  $user->getGlobalOption()->getMedicineConfig()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->select('e.brandName as brandName , e.mode as mode');
        $qb->where('e.medicineConfig = :config');
        $qb->setParameter('config', $config);
        $qb->groupBy("e.brandName");
        $res = $qb->getQuery();
        return $result = $res->getArrayResult();
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
                 $this->updatePurchasePrice($stockItem,$purchaseItem);
            }
        }
    }

    public function updateRemovePurchaseQuantity(MedicineStock $stock , $fieldName = '', $pack = 1, $minStock = 0 ){

    	$em = $this->_em;
        if($fieldName == 'sales'){
            $qnt = $em->getRepository('MedicineBundle:MedicineSalesItem')->salesStockItemUpdate($stock);
            $stock->setSalesQuantity($qnt);
        }elseif($fieldName == 'sales-return'){
	        $quantity = $this->_em->getRepository('MedicineBundle:MedicineSalesReturn')->salesReturnStockUpdate($stock);
            $stock->setSalesReturnQuantity($quantity);
        }elseif($fieldName == 'purchase-return'){
            $qnt = $em->getRepository('MedicineBundle:MedicinePurchaseReturnItem')->purchaseReturnStockUpdate($stock);
            $stock->setPurchaseReturnQuantity($qnt);
        }elseif($fieldName == 'damage'){
            $quantity = $em->getRepository('MedicineBundle:MedicineDamage')->damageStockItemUpdate($stock);
            $stock->setDamageQuantity($quantity);
        }else{
            $qnt = $em->getRepository('MedicineBundle:MedicinePurchaseItem')->purchaseStockItemUpdate($stock);
            $stock->setMinQuantity($minStock);
            $stock->setPack($pack);
            $stock->setPurchaseQuantity($qnt);
        }
        $em->persist($stock);
        $em->flush();
        $this->remainingQnt($stock);
    }

    public function remainingQnt(MedicineStock $stock)
    {
        $em = $this->_em;
	    $qnt = ($stock->getOpeningQuantity() + $stock->getPurchaseQuantity() + $stock->getSalesReturnQuantity()) - ($stock->getPurchaseReturnQuantity() + $stock->getSalesQuantity() + $stock->getDamageQuantity());
        $stock->setRemainingQuantity($qnt);
        $em->persist($stock);
        $em->flush();
    }

    public function updatePurchasePrice(MedicineStock $stock,MedicinePurchaseItem $item)
    {
        $em = $this->_em;
        $avg = $em->getRepository('MedicineBundle:MedicinePurchaseItem')->getPurchaseSalesAvg($stock);
	    $stock->setPurchasePrice($item->getPurchasePrice());
	    $stock->setSalesPrice($item->getSalesPrice());
        $stock->setAveragePurchasePrice($avg['purchase']);
        $stock->setAverageSalesPrice($avg['sales']);
        $em->persist($stock);
        $em->flush();
    }

    public function getSalesUpdateQnt(MedicineSales $invoice){

        $em = $this->_em;

        /** @var $item MedicineSalesItem */
        if($invoice->getMedicineSalesItems()){
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
    }

    public function updateRemoveSalesQuantity(MedicineStock $stock){

        $em = $this->_em;
        $qnt = $em->getRepository('MedicineBundle:MedicineSalesItem')->salesStockItemUpdate($stock);
        $stock->setPurchaseQuantity($qnt);
        $em->persist($stock);
        $em->flush();
        $this->remainingQnt($stock);
    }

    public function searchAutoComplete($q, MedicineConfig $config)
    {

        $query = $this->createQueryBuilder('e');
        $query->join('e.medicineConfig', 'ic');
        $query->leftJoin('e.rackNo', 'rack');
        $query->leftJoin('e.unit', 'unit');
        $query->leftJoin('e.medicineBrand', 'brand');
        $query->leftJoin('brand.medicineGeneric', 'generic');
        $query->select('e.id as id');
       // $query->addSelect('CONCAT(e.sku, \' - \', e.name,  \' [\', e.remainingQuantity, \'] \', unit.name,\' => \', rack.name,\' - PP Tk. \', e.purchasePrice) AS text');
        $query->addSelect("CASE WHEN (e.rackNo IS NULL) THEN CONCAT(e.name,' [',e.remainingQuantity, '] ', unit.name, ' => PP Tk.', e.purchasePrice)  ELSE CONCAT(e.name,' [',e.remainingQuantity, '] ', unit.name,'=>', rack.name , ' => PP Tk.', e.purchasePrice)  END as text");
        //$query->addSelect("CASE WHEN (e.strength IS NULL) THEN CONCAT(e.medicineForm,' ', e.name,' ',g.name, ' ', c.name)  ELSE CONCAT(e.medicineForm,' ',e.name, ' ',e.strength,' ', g.name,' ',c.name)  END as text");
        $query->where($query->expr()->like("e.name", "'%$q%'"  ));
        $query->orWhere($query->expr()->like("generic.name", "'%$q%'"  ));
        $query->andWhere("ic.id = :config");
        $query->setParameter('config', $config->getId());
        $query->groupBy('e.name');
        $query->orderBy('e.name', 'ASC');
        $query->setMaxResults( '30' );
        return $query->getQuery()->getResult();

    }

    public function searchAutoPurchaseStock($q, MedicineConfig $config)
    {

        $query = $this->createQueryBuilder('e');
        $query->join('e.medicineConfig', 'ic');
        $query->leftJoin('e.rackNo', 'rack');
        $query->select('e.id as id');
        $query->addSelect("CASE WHEN (e.rackNo IS NULL) THEN e.name  ELSE CONCAT(e.name,' => ', rack.name)  END as text");
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

    public function searchWebStock($q, MedicineConfig $config)
    {

        $query = $this->createQueryBuilder('e');
        $query->join('e.medicineConfig', 'ic');
        $query->select('e.id as id');
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


    public function getApiStock(GlobalOption $option)
    {
        $config = $option->getMedicineConfig();
        $qb = $this->createQueryBuilder('e');
        $qb->leftJoin('e.medicineBrand','brand');
        $qb->leftJoin('e.unit','u');
        $qb->select('e.id as stockId','e.name as name','e.remainingQuantity as remainingQuantity','e.salesPrice as salesPrice','e.purchasePrice as purchasePrice','e.printHide as printHidden');
        $qb->addSelect('brand.name as brandName','brand.strength as strength');
        $qb->addSelect('u.id as unitId','u.name as unitName');
        $qb->where('e.medicineConfig = :config')->setParameter('config', $config->getId()) ;
        $qb->orderBy('e.sku','ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach($result as $key => $row) {

            $data[$key]['global_id']            = (int) $option->getId();
            $data[$key]['item_id']              = (int) $row['stockId'];

            if($row['brandName']){
                $printName = $row['brandName'].' '.$row['strength'];
            }else{
                $printName = $row['name'];
            }

            $data[$key]['category_id']      = 0;
            $data[$key]['categoryName']     = '';
            if ($row['unitId']){
                $data[$key]['unit_id']          = $row['unitId'];
                $data[$key]['unit']             = $row['unitName'];
            }else{
                $data[$key]['unit_id']          = 0;
                $data[$key]['unit']             = '';
            }
            $data[$key]['name']                 = $row['name'];
            $data[$key]['printName']            = $printName;
            $data[$key]['quantity']             = $row['remainingQuantity'];
            $data[$key]['salesPrice']           = $row['salesPrice'];
            $data[$key]['purchasePrice']        = $row['purchasePrice'];
            $data[$key]['printHidden']          = $row['printHidden'];

        }
        return $data;
    }

    public function brandStock(User $user,$data)
    {
        $config =  $user->getGlobalOption()->getMedicineConfig()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->select('e.brandName as name' );
        $qb->addSelect('COALESCE(SUM(e.averagePurchasePrice * e.remainingQuantity),0) as avgPurchase');
        $qb->addSelect('COALESCE(SUM(e.purchasePrice * e.remainingQuantity),0) as purchase');
        $qb->addSelect('COALESCE(SUM(e.averageSalesPrice * e.remainingQuantity),0) as avgSales ' );
        $qb->addSelect('COALESCE(SUM(e.salesPrice * e.remainingQuantity),0) as sales ' );
        $qb->addSelect('(COALESCE(SUM(e.averageSalesPrice * e.remainingQuantity),0))-(COALESCE(SUM(e.averagePurchasePrice * e.remainingQuantity),0)) as avgProfit');
        $qb->addSelect('(COALESCE(SUM(e.salesPrice * e.remainingQuantity),0))-(COALESCE(SUM(e.purchasePrice * e.remainingQuantity),0)) as profit');
        $qb->where('e.medicineConfig = :config');
        $qb->setParameter('config', $config);
        $this->handleSearchBetween($qb,$data);
        $qb->groupBy("e.brandName");
        $res = $qb->getQuery();
        return $result = $res->getArrayResult();
    }

}
