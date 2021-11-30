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
use Gregwar\Image\Image;
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
        $keyword = isset($data['keyword'])? $data['keyword'] :'';
        if (!empty($name)) {
            $qb->andWhere($qb->expr()->like("e.name", "'$name%'"  ));
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
        if(!empty($category)){
            $qb->andWhere($qb->expr()->like("c.slug", "'%$category%'"  ));
        }
        if (!empty($keyword)) {
            $qb->leftJoin('e.medicineBrand','mb');
            $qb->leftJoin('mb.medicineGeneric','mg');
            $qb->andWhere('e.name LIKE :searchTerm OR e.brandName LIKE :searchTerm OR mg.name LIKE :searchTerm');
            $qb->setParameter('searchTerm', '%'.$keyword.'%');
        }
    }

    public function checkDuplicateStockMedicine(MedicineConfig $config, MedicineBrand $brand)
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

    public function findEcommerceWithSearch($config,$data){

        $sort = isset($data['sort'])? $data['sort'] :'e.sku';
        $direction = isset($data['direction'])? $data['direction'] :'ASC';
        $qb = $this->createQueryBuilder('e');
        $qb->leftJoin('e.rackNo','p');
        $qb->where('e.medicineConfig = :config')->setParameter('config', $config) ;
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy("{$sort}",$direction);
        $result = $qb->getQuery()->getResult();
        return  $result;
    }

    public function findWithSearch($config,$data){

        $sort = isset($data['sort'])? $data['sort'] :'e.name';
        $direction = isset($data['direction'])? $data['direction'] :'ASC';
        $process = isset($data['process'])? $data['process'] :'';
        $startQuantity = isset($data['quantityStart'])? $data['quantityStart'] :0;
        $endQuantity = isset($data['quantityEnd'])? $data['quantityEnd'] :0;

        $qb = $this->createQueryBuilder('e');
        $qb->where('e.medicineConfig = :config')->setParameter('config', $config) ;
        if($process == 'Current Stock' and $startQuantity == 0 and $endQuantity == 0){
            $qb->andWhere('e.remainingQuantity  > 0');
        }elseif($process == 'Current Stock'  and $startQuantity >= 0 and $endQuantity > 0){
            $qb->andWhere("e.remainingQuantity  >= {$startQuantity}");
            $qb->andWhere("e.remainingQuantity  <= {$endQuantity}");
        }
        if($process == 'Empty Stock'){
            $qb->andWhere('e.remainingQuantity  = 0');
            $qb->andWhere('e.salesQuantity = 0');
        }
        if($process == 'Active'){
            $qb->andWhere('e.status = 1');
        }
        if($process == 'In-active'){
            $qb->andWhere('e.status != 1');
        }
        if($process == 'Sales' and $startQuantity == 0 and $endQuantity == 0){
            $qb->andWhere('e.salesQuantity  <= 0');
        }elseif ($process == 'Sales'  and $startQuantity >= 0 and $endQuantity > 0){
            $qb->andWhere("e.salesQuantity  >= {$startQuantity}");
            $qb->andWhere("e.salesQuantity  <= {$endQuantity}");
        }
         if($process == 'Sales Minus' and $startQuantity == 0 and $endQuantity == 0){
            $qb->andWhere('e.remainingQuantity  <= 0');
        }elseif ($process == 'Sales Minus'  and $startQuantity >= 0 and $endQuantity > 0){
             $qb->andWhere("e.remainingQuantity  >= -{$startQuantity}");
             $qb->andWhere("e.remainingQuantity  <= -{$endQuantity}");
        }
        if($process == "Opening Quantity" and $startQuantity == 0 and $endQuantity == 0){
            $qb->andWhere('e.openingQuantity > 0');
        }elseif($process == "Opening Quantity"  and $startQuantity >= 0 and $endQuantity > 0){
            $qb->andWhere("e.openingQuantity  >= {$startQuantity}");
            $qb->andWhere("e.openingQuantity  <= {$endQuantity}");
        }
        if($process == "Min Quantity" and $startQuantity == 0 and $endQuantity == 0){
            $qb->andWhere('e.minQuantity > 0');
        }elseif($process == "Min Quantity"  and $startQuantity >= 0 and $endQuantity > 0){
            $qb->andWhere("e.minQuantity  >= {$startQuantity}");
            $qb->andWhere("e.minQuantity  <= {$endQuantity}");
        }
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy("{$sort}",$direction);
        $result = $qb->getQuery();
        return  $result;
    }

    public function findWithGlobalSearch($data){

        $sort = isset($data['sort'])? $data['sort'] :'e.sku';
        $direction = isset($data['direction'])? $data['direction'] :'ASC';
        $qb = $this->createQueryBuilder('e');
        $qb->where('e.remainingQuantity > 0');
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy("{$sort}",$direction);
        $result = $qb->getQuery()->getResult();
        return  $result;
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

    public function updateRemovePurchaseQuantity(MedicineStock $stock , $fieldName = '', $minQuantity = 0, $openStock = 0 ){

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
            $bonusQnt = $em->getRepository('MedicineBundle:MedicinePurchaseItem')->purchaseStockBonusItemUpdate($stock);
            if($openStock > 0){
                $stock->setOpeningQuantity($openStock);
            }
            $stock->setMinQuantity($minQuantity);
            $stock->setBonusQuantity($bonusQnt);
            $stock->setPurchaseQuantity($qnt);
        }
        $em->persist($stock);
        $em->flush();
        $this->remainingQnt($stock);
    }

    public function remainingQnt(MedicineStock $stock)
    {
        $em = $this->_em;
	    $qnt = ($stock->getOpeningQuantity() + $stock->getPurchaseQuantity() + $stock->getSalesReturnQuantity() + $stock->getBonusQuantity()) - ($stock->getPurchaseReturnQuantity() + $stock->getSalesQuantity() + $stock->getDamageQuantity());
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
        $query->addSelect("CASE WHEN (e.rackNo IS NULL) THEN CONCAT(e.name,' [',e.remainingQuantity, '] ','- Tk.', e.salesPrice)  ELSE CONCAT(e.name,' [',e.remainingQuantity, '] ', rack.name , '- Tk.', e.salesPrice)  END as text");
        $query->where("ic.id = :config")->setParameter('config', $config->getId());
        if($config->isSearchSlug() == 1){
            $query->andWhere($query->expr()->like("e.slug", "'$q%'"  ));
        }else{
            $query->andWhere($query->expr()->like("e.name", "'%$q%'"  ));
        }
        $query->andWhere('e.status = 1');
        $query->groupBy('e.name');
        $query->orderBy('e.slug', 'ASC');
        $query->setMaxResults( '50' );
        return $query->getQuery()->getResult();

    }

    public function ecommerceSearchAutoComplete($q, MedicineConfig $config)
    {

        $query = $this->createQueryBuilder('e');
        $query->join('e.medicineConfig', 'ic');
        $query->leftJoin('e.rackNo', 'rack');
        $query->leftJoin('e.unit', 'unit');
        $query->leftJoin('e.medicineBrand', 'brand');
        $query->leftJoin('brand.medicineGeneric', 'generic');
        $query->select('e.id as id');
        $query->addSelect("e.name as text");
        $query->where("ic.id = :config")->setParameter('config', $config->getId());
        if($config->isSearchSlug() == 1){
            $query->andWhere($query->expr()->like("e.slug", "'$q%'"  ));
        }else{
            $query->andWhere($query->expr()->like("e.name", "'%$q%'"  ));
        }
        $query->groupBy('e.name');
        $query->orderBy('e.name', 'ASC');
        $query->setMaxResults( '50' );
        return $query->getQuery()->getResult();

    }

    public function searchAutoPurchaseStock($q, MedicineConfig $config)
    {
        $query = $this->createQueryBuilder('e');
        $query->join('e.medicineConfig', 'ic');
        $query->select('e.id as id');
        $query->addSelect("CONCAT(e.name,' => MRP - ', e.salesPrice) as text");
        $query->where("ic.id = :config")->setParameter('config', $config->getId());
        if($config->isSearchSlug() == 1){
            $query->andWhere($query->expr()->like("e.slug", "'$q%'"  ));
        }else{
            $query->andWhere($query->expr()->like("e.name", "'%$q%'"  ));
        }
        $query->andWhere('e.status =1');
        $query->groupBy('e.name');
        $query->orderBy('e.name', 'ASC');
        $query->setMaxResults( '50' );
        return $query->getQuery()->getResult();

    }


    public function searchNameAutoComplete($q, MedicineConfig $config)
    {

        $query = $this->createQueryBuilder('e');
        $query->join('e.medicineConfig', 'ic');
        $query->select('e.name as id');
        $query->addSelect('e.name as text');
        $query->where("ic.id = :config")->setParameter('config', $config->getId());
        if($config->isSearchSlug() == 1){
            $query->andWhere($query->expr()->like("e.slug", "'$q%'"  ));
        }else{
            $query->andWhere($query->expr()->like("e.name", "'%$q%'"  ));
        }
        $query->andWhere("e.status = 1");
        $query->orderBy('e.name', 'ASC');
        $query->setMaxResults( '50' );
        return $query->getQuery()->getResult();

    }

    public function searchWebStock($q, MedicineConfig $config)
    {

        $query = $this->createQueryBuilder('e');
        $query->join('e.medicineConfig', 'ic');
        $query->select('e.id as id');
        $query->addSelect('e.name as text');
        $query->where("ic.id = :config")->setParameter('config', $config->getId());
        if($config->isSearchSlug() == 1){
            $query->andWhere($query->expr()->like("e.slug", "'$q%'"  ));
        }else{
            $query->andWhere($query->expr()->like("e.name", "'%$q%'"  ));
        }
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
        $qb->select('e.id as stockId','e.name as name','e.remainingQuantity as remainingQuantity','e.salesPrice as salesPrice','e.purchasePrice as purchasePrice','e.printHide as printHidden','e.path as path');
        $qb->addSelect('brand.name as brandName','brand.strength as strength');
        $qb->addSelect('u.id as unitId','u.name as unitName');
        $qb->where('e.medicineConfig = :config')->setParameter('config', $config->getId()) ;
        $qb->andWhere('e.status = 1');
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
            if($row['path']){
                $path = $this->resizeFilter("uploads/domain/{$option->getId()}/product/{$row['path']}");
                $data[$key]['imagePath']            =  $path;
            }else{
                $data[$key]['imagePath']            = "";
            }

        }
        return $data;
    }

    public function resizeFilter($pathToImage, $width = 256, $height = 256)
    {
        $path = '/' . Image::open(__DIR__.'/../../../../../web/' . $pathToImage)->cropResize($width, $height, 'transparent', 'top', 'left')->guess();
        return $_SERVER['HTTP_HOST'].$path;
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
   //     $this->handleSearchBetween($qb,$data);
        $qb->groupBy("e.brandName");
        $res = $qb->getQuery();
        return $result = $res->getArrayResult();
    }

    public function  processStockMigration($from, $to)
    {

        $em = $this->_em;
        $stock = $em->createQuery("DELETE MedicineBundle:MedicineStock e WHERE e.medicineConfig={$to}");
        if($stock){
            $stock->execute();
        }
        $elem = "INSERT INTO medicine_stock(`unit_id`,`name`,`slug`,`minQuantity`,`remainingQuantity`,`salesPrice`, `purchasePrice`, `medicineBrand_id`,`brandName`,`pack`,`isAndroid`,`printHide`,mode,status,`medicineConfig_id`)
  SELECT `unit_id`, trim(name),trim(slug),`minQuantity`,0, `salesPrice`,(salesPrice - ((salesPrice * 12.5)/100)), `medicineBrand_id`, `brandName`, `pack`, `isAndroid`, `printHide`,mode,1,$to
  FROM medicine_stock
  WHERE medicineConfig_id =:config";
        $qb1 = $this->getEntityManager()->getConnection()->prepare($elem);
        $qb1->bindValue('config', $from);
        $qb1->execute();

        $stockUpdate = "UPDATE medicine_stock SET mode = 'medicine' WHERE  medicineConfig_id =:config AND mode IS NULL";
        $qb1 = $this->getEntityManager()->getConnection()->prepare($stockUpdate);
        $qb1->bindValue('config', $to);
        $qb1->execute();
    }

    public function insertGlobalToLocalStock(GlobalOption $option,MedicineBrand $brand)
    {
        $config = $option->getMedicineConfig();
        $em = $this->_em;
        $find = $this->findOneBy(array('medicineConfig'=>$config,'medicineBrand' => $brand));
        if(empty($find)){
            $entity = new MedicineStock();
            $entity->setMedicineConfig($config);
            $name = $brand->getMedicineForm().' '.$brand->getName().' '.$brand->getStrength();
            $entity->setName($name);
            $entity->setBrandName($brand->getMedicineCompany()->getName());
            $entity->setMedicineBrand($brand);
            $unit = $em->getRepository('SettingToolBundle:ProductUnit')->findOneBy(array('name'=>'Pcs'));
            $entity->setUnit($unit);
            $entity->setSalesPrice($brand->getPrice());
            if($brand->getPrice()){
                $purchasePrice = ((float)$brand->getPrice()-(((float)$brand->getPrice()* 10)/100));
                $entity->setPurchasePrice($purchasePrice);
            }
            $entity->setMode('medicine');
            $em->persist($entity);
            $em->flush();
            return $entity;
        }
    }

    public function insertAndroidStock(GlobalOption $option,$data)
    {
        $config = $option->getMedicineConfig();
        $em = $this->_em;
        $find = $this->findOneBy(array('medicineConfig'=>$config,'name' => $data['name']));
        if(empty($find)){
            $entity = new MedicineStock();
            $entity->setMedicineConfig($config);
            $entity->setName($data['name']);
            $entity->setBrandName($data['brandName']);
            $medicine = $em->getRepository('MedicineBundle:MedicineBrand')->findOneBy(array('name'=>$entity->getName()));
            if($medicine){
                $entity->setMedicineBrand($medicine);
            }
            if($data['unit']){
                $unit = $em->getRepository('SettingToolBundle:ProductUnit')->findOneBy(array('name' => $data['unit']));
                $entity->setUnit($unit);
            }else{
                $unit = $em->getRepository('SettingToolBundle:ProductUnit')->findOneBy(array('name'=>'Pcs'));
                $entity->setUnit($unit);
            }
            if($data['category']){
                $category = $em->getRepository('MedicineBundle:MedicineParticular')->findOneBy(array('medicineConfig'=>$config,'name' => $data['category']));
                $entity->setCategory($category);
            }
            $entity->setOpeningQuantity($data['openingQuantity']);
            $entity->setRemainingQuantity($data['openingQuantity']);
            $entity->setSalesPrice($data['price']);
            if($data['purchasePrice']){
                $entity->setPurchasePrice($data['purchasePrice']);
            }else{
                $purchasePrice = ((float)$data['price']-(((float)$data['price'] * 10)/100));
            }
            $entity->setPurchasePrice($purchasePrice);
            $entity->setDescription($data['description']);
            $entity->setMinQuantity($data['minQuantity']);
            $entity->setMode('medicine');
            $em->persist($entity);
            $em->flush();
            return $entity;
        }
        return $find;
    }

    public function updateAndroidStock($data)
    {

        $em = $this->_em;
        $entity = $this->find($data['id']);
        /* @var $entity MedicineStock */
        if($entity){
            $entity->setName($data['name']);
            $entity->setBrandName($data['brandName']);
            if($data['category']){
                $category = $em->getRepository('MedicineBundle:MedicineParticular')->findOneBy(array('name' => $data['category']));
                $entity->setCategory($category);
            }
            if($data['unit']){
                $unit = $em->getRepository('SettingToolBundle:ProductUnit')->findOneBy(array('name' => $data['unit']));
                $entity->setUnit($unit);
            }
            $entity->setSalesPrice($data['price']);
            $purchasePrice = ((float)$data['price']-(((float)$data['price'] * 10)/100));
            $entity->setPurchasePrice($purchasePrice);
            $entity->setMinQuantity($data['minQuantity']);
            $entity->setMode('medicine');
            $em->persist($entity);
            $em->flush();
        }

    }

    public function sumOpeningQuantity(MedicineConfig $config)
    {

        $qb = $this->createQueryBuilder('e');
        $qb->select('COALESCE(SUM(e.purchasePrice * e.openingQuantity),0) as opening');
        $qb->where('e.medicineConfig = :config')->setParameter('config', $config->getId()) ;
        $qb->andWhere('e.id != 1');
        $res = $qb->getQuery();
        $result = $res->getSingleScalarResult();
        return $result;

    }

    public function updateOpeningQuantity($config)
    {
        $stockUpdate = "UPDATE medicine_stock SET openingApprove = 1 WHERE  medicineConfig_id = {$config->getId()} && openingQuantity IS NOT NULL";
        $qb1 = $this->getEntityManager()->getConnection()->prepare($stockUpdate);
        $qb1->execute();
    }

    public function getApiStockDelete(GlobalOption $option,$id)
    {
        $em = $this->_em;
        $config = $option->getMedicineConfig()->getId();
        $stock = $em->createQuery("DELETE MedicineBundle:MedicineStock e WHERE e.medicineConfig = {$config} and id = {$id}");
        $stock->execute();
        return 'success';
    }

}
