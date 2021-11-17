<?php

namespace Appstore\Bundle\InventoryBundle\Repository;
use Appstore\Bundle\EcommerceBundle\Entity\Order;
use Appstore\Bundle\EcommerceBundle\Entity\OrderItem;
use Appstore\Bundle\InventoryBundle\Entity\Damage;
use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Appstore\Bundle\InventoryBundle\Entity\Item;
use Appstore\Bundle\InventoryBundle\Entity\Purchase;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseItem;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseReturn;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseReturnItem;
use Appstore\Bundle\InventoryBundle\Entity\Sales;
use Appstore\Bundle\InventoryBundle\Entity\SalesItem;
use Appstore\Bundle\InventoryBundle\Entity\SalesReturn;
use Core\UserBundle\Entity\User;
use Gregwar\Image\Image;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\DependencyInjection\Container;

use Doctrine\ORM\EntityRepository;

/**
 * ItemTypeGroupingRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ItemRepository extends EntityRepository
{

    public  function getSumPurchaseItem($inventory , $excelImporter = ''){

        $qb = $this->createQueryBuilder('item');
        $qb->join('item.purchaseItems', 'pItem');
        $qb->join('pItem.purchase', 'purchase');
        $qb->select('item.id as id');
        $qb->addSelect('SUM(pItem.quantity) as quantity ');
        $qb->where("purchase.inventoryConfig = :inventory");
        $qb->setParameter('inventory', $inventory);
        $qb->where("purchase.process = :process");
        $qb->setParameter('process', 'imported');

        /*if(!empty($excelImporter)){

            $purchaseIds = array();
            $purchases = $this->_em->getRepository('InventoryBundle:Purchase')->findBy(array('purchaseImport' => $excelImporter, 'process' => 'imported' ));
            foreach ($purchases as $purchase){
                $purchaseIds = $purchase->getId();
            }
            $qb->andWhere("purchase.id IN (:ids)");
            $qb->setParameter('ids',array_values($purchaseIds));
        }*/

        $qb->groupBy('item.id');
        $result = $qb->getQuery()->getResult();
        foreach ($result as $row ){
            $entity = $this->find($row['id']);
            $entity->setPurchaseQuantity($row['quantity']);
            $this->_em->persist($entity);
            $this->_em->flush($entity);
        }

    }

    public function getStockPriceOverview($inventory)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('SUM(e.quantity) AS quantity');
        $qb->addSelect('SUM(e.remainingQnt * e.purchaseAvgPrice) AS purchasePrice');
        $qb->addSelect('SUM(e.remainingQnt * e.salesAvgPrice) AS salesPrice');
        $qb->where("e.inventoryConfig = :inventory");
        $qb->setParameter('inventory', $inventory);
        $result = $qb->getQuery()->getArrayResult();
        return $result;

    }

    public function checkDuplicateSKU(InventoryConfig $inventory,$data)
    {

        $product = $this->_em->getRepository("InventoryBundle:Product")->insertMasterItem($inventory,$data);
        $masterItem = $data['item']['name'];
        $vendor = isset($data['item']['vendor']) ? $data['item']['vendor'] :'NULL';
        $itemColor = isset ($data['item']['color']) ? $data['item']['color']:'NULL';
        $itemSize = isset($data['item']['size']) ? $data['item']['size'] : 'NULL';
        $itemBrand = isset($data['item']['brand']) ? $data['item']['brand']:'NULL';
        $itemCategory = isset($data['item']['category']) ? $data['item']['category']:'NULL';
        $itemModel = isset($data['item']['model']) ? $data['item']['model']:'NULL';

        $qb = $this->createQueryBuilder('item');
        $qb->join('item.masterItem', 'm');
        $qb->select('COUNT(item.id) AS totalNumber');
        $qb->where("item.inventoryConfig = :inventory");
        $qb->setParameter('inventory', $inventory);
        $qb->andWhere('item.name= :masterId');
        $qb->setParameter('masterId', $masterItem);
        if($inventory->getIsSize() == 1) {
            $qb->andWhere('item.size = :itemSize');
            $qb->setParameter('itemSize', $itemSize);
        }
        if($inventory->getIsColor() == 1) {
            $qb->andWhere('item.color = :itemColor');
            $qb->setParameter('itemColor', $itemColor);
        }
        if($inventory->getIsVendor() == 1) {
            $qb->andWhere('item.vendor = :vendor');
            $qb->setParameter('vendor', $vendor);
        }
        if($inventory->getIsBrand() == 1) {
            $qb->andWhere('item.brand = :itemBrand');
            $qb->setParameter('itemBrand', $itemBrand);
        }
        if($inventory->isModel() == 1) {
            $qb->andWhere('item.model = :itemModel');
            $qb->setParameter('itemModel', $itemModel);
        }
        $count = $qb->getQuery()->getOneOrNullResult();
        $result = array('masterItem'=> $product , 'count' => $count['totalNumber']);
        return $result;

    }

    public function  getApiStock(GlobalOption $option , $data = '')
    {

        $config = $option->getInventoryConfig()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.masterItem','m');
        $qb->join('m.category','c');
        $qb->leftJoin('m.productUnit','u');
        $qb->select('e.id as stockId','e.barcode as barcode','e.name as name','e.remainingQnt as quantity','e.salesPrice as salesPrice','e.purchaseAvgPrice as purchasePrice','e.path as path');
        $qb->addSelect('u.id as unitId','u.name as unitName');
        $qb->addSelect('c.id as categoryId','c.name as categoryName');
        $qb->where('e.inventoryConfig = :config');
        $qb->setParameter('config',$config);
        if(isset($data['category']) and !empty($data['category'])){
            $catid = $data['category'];
            $qb->andWhere('c.id = :catid');
            $qb->setParameter('catid',$catid);
        }
        $qb->orderBy('e.name','ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach($result as $key => $row) {

            $data[$key]['global_id']            = (int) $option->getId();
            $data[$key]['item_id']              = (int) $row['stockId'];

            $data[$key]['category_id']          = $row['categoryId'];
            $data[$key]['categoryName']         = $row['categoryName'];
            if ($row['unitId']){
                $data[$key]['unit_id']          = $row['barcode'];
                $data[$key]['unit']             = $row['unitName'];
            }else{
                $data[$key]['unit_id']          = $row['barcode'];
                $data[$key]['unit']             = '';
            }
            $data[$key]['name']                 = $row['name'];
            $data[$key]['printName']            = $row['name'];
            $data[$key]['quantity']             = $row['quantity'];
            $data[$key]['salesPrice']           = $row['salesPrice'];
            $data[$key]['purchasePrice']        = $row['purchasePrice'];
            $data[$key]['printHidden']          = "";
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

    public function getApiCategory(GlobalOption $option)
    {

        $config = $option->getInventoryConfig()->getId();
        $qb = $this->createQueryBuilder('item');
        $qb->join('item.masterItem', 'mi');
        $qb->join('mi.category', 'category');
        $qb->join('item.inventoryConfig', 'ic');
        $qb->select('category.id as id');
        $qb->addSelect('category.name as name','category.slug as slug');
        $qb->where("item.inventoryConfig = :inventory");
        $qb->setParameter('inventory', $config);
        $qb->groupBy('category.id');
        $qb->orderBy('category.name','ASC');
        $result = $qb->getQuery()->getArrayResult();

        $data = array();
        foreach($result as $key => $row) {

            $data[$key]['global_id']        = (int) $option->getId();
            $data[$key]['category_id']      = (int) $row['id'];
            $data[$key]['name']             = $row['name'];
            $data[$key]['slug']             = $row['slug'];

        }

        return $data;
    }

    public function checkInstantDuplicateSKU(InventoryConfig $inventory,$data)
    {


        $masterItem = $data['masterItem'];
        $vendor = isset($data['vendor']) ? $data['vendor'] :'NULL';
        $itemColor = isset ($data['color']) ? $data['color']:'NULL';
        $itemSize = isset($data['size']) ? $data['size'] : 'NULL';
        $itemBrand = isset($data['brand'])? $data['brand']:'NULL';

        $qb = $this->createQueryBuilder('item');
        $qb->join('item.masterItem', 'm');
        $qb->select('COUNT(item.id) AS totalNumber');
        $qb->where("item.inventoryConfig = :inventory");
        $qb->setParameter('inventory', $inventory);

        $existMasterItem = $this->_em->getRepository('InventoryBundle:Product')->findOneBy(array('name'=> $data['masterItem']))->getId();
        $qb->andWhere('item.masterItem = :masterId');
        $qb->setParameter('masterId', $existMasterItem);

        if($inventory->getIsSize() == 1) {
            $itemSize = $this->_em->getRepository('InventoryBundle:ItemSize')->findOneBy(array('name'=> $itemSize ));
            $qb->andWhere('item.size = :itemSize');
            $qb->setParameter('itemSize', $itemSize);
        }
        if($inventory->getIsColor() == 1) {
            $itemColor = $this->_em->getRepository('InventoryBundle:ItemColor')->findOneBy(array('name'=> $itemColor));
            $qb->andWhere('item.color = :itemColor');
            $qb->setParameter('itemColor', $itemColor);
        }
        if($inventory->getIsVendor() == 1) {
            $vendor = $this->_em->getRepository('InventoryBundle:Vendor')->findOneBy(array('inventoryConfig'=> $inventory,'companyName'=> $vendor ));
            $qb->andWhere('item.vendor = :vendor');
            $qb->setParameter('vendor', $vendor);
        }
        if($inventory->getIsBrand() == 1) {
            $itemBrand = $this->_em->getRepository('InventoryBundle:ItemBrand')->findOneBy(array('inventoryConfig'=> $inventory,'name'=> $itemBrand ));
            $qb->andWhere('item.brand = :itemBrand');
            $qb->setParameter('itemBrand', $itemBrand);
        }
        $count = $qb->getQuery()->getOneOrNullResult();
        $result = $count['totalNumber'];
        return $result;


    }

    public function findWithSearch($inventory,$data)
    {

        $sort = isset($data['sort'])? $data['sort'] :'item.name';
        $direction = isset($data['direction'])? $data['direction'] :'ASC';
        $item = isset($data['item'])? $data['item'] :'';
        $color = isset($data['color'])? $data['color'] :'';
        $size = isset($data['size'])? $data['size'] :'';
        $vendor = isset($data['vendor'])? $data['vendor'] :'';
        $brand = isset($data['brand'])? $data['brand'] :'';
        $sku = isset($data['sku'])? $data['sku'] :'';
        $barcode = isset($data['barcode'])? $data['barcode'] :'';
        $category = isset($data['category'])? $data['category'] :'';
        $unit = isset($data['unit'])? $data['unit'] :'';

        $qb = $this->createQueryBuilder('item');
        $qb->join('item.masterItem', 'm');
        $qb->where("item.inventoryConfig = :inventory");
        $qb->setParameter('inventory', $inventory);

        if (!empty($sku)) {
            $qb->andWhere($qb->expr()->like("item.sku", "'%$sku%'"  ));
        }
        if (!empty($barcode)) {
            $qb->andWhere($qb->expr()->like("item.barcode", "'%$barcode%'"  ));
        }
        if (!empty($item)) {
            $qb->andWhere("m.name = :name");
            $qb->setParameter('name', $item);
        }
        if (!empty($color)) {

            $qb->join('item.color', 'c');
            $qb->andWhere("c.name = :color");
            $qb->setParameter('color', $color);
        }
        if (!empty($size)) {

            $qb->join('item.size', 's');
            $qb->andWhere("s.name = :size");
            $qb->setParameter('size', $size);
        }
        if (!empty($vendor)) {

            $qb->join('item.vendor', 'v');
            $qb->andWhere("v.companyName = :vendor");
            $qb->setParameter('vendor', $vendor);
        }

        if (!empty($brand)) {

            $qb->join('item.brand', 'b');
            $qb->andWhere("b.name = :brand");
            $qb->setParameter('brand', $brand);
        }
         if (!empty($category)) {

            $qb->leftJoin('m.category', 'c');
            $qb->andWhere("c.name = :category");
            $qb->setParameter('category', $category);
        }

        if (!empty($unit)) {
            $qb->leftJoin('m.productUnit', 'u');
            $qb->andWhere("u.name = :unit");
            $qb->setParameter('unit', $unit);
        }
        $qb->orderBy("{$sort}",$direction);
        $qb->getQuery();
        return  $qb;

    }

    public function findWithShortListSearch($inventory,$data)
    {

        $item = isset($data['item'])? $data['item'] :'';
        $color = isset($data['color'])? $data['color'] :'';
        $size = isset($data['size'])? $data['size'] :'';
        $vendor = isset($data['vendor'])? $data['vendor'] :'';
        $brand = isset($data['brand'])? $data['brand'] :'';
        $sku = isset($data['sku'])? $data['sku'] :'';
        $category = isset($data['category'])? $data['category'] :'';
        $minQnt = isset($data['minQnt'])? $data['minQnt'] :'';

        $qb = $this->createQueryBuilder('item');
        $qb->join('item.masterItem', 'm');
        $qb->where("item.inventoryConfig = :inventory");
        $qb->setParameter('inventory', $inventory);
        $qb->andWhere("item.minQnt > 0");
        if($minQnt == 'minimum') {
            $qb->andWhere("item.minQnt >= item.remainingQnt");
        }

        if (!empty($sku)) {
            $qb->andWhere($qb->expr()->like("item.sku", "'%$sku%'"  ));
        }
        if (!empty($item)) {

            $qb->andWhere("m.name = :name");
            $qb->setParameter('name', $item);
        }
        if (!empty($color)) {

            $qb->join('item.color', 'c');
            $qb->andWhere("c.name = :color");
            $qb->setParameter('color', $color);
        }
        if (!empty($size)) {

            $qb->join('item.size', 's');
            $qb->andWhere("s.name = :size");
            $qb->setParameter('size', $size);
        }
        if (!empty($vendor)) {

            $qb->join('item.vendor', 'v');
            $qb->andWhere("v.companyName = :vendor");
            $qb->setParameter('vendor', $vendor);
        }

        if (!empty($brand)) {

            $qb->join('item.brand', 'b');
            $qb->andWhere("b.name = :brand");
            $qb->setParameter('brand', $brand);
        }
        if (!empty($category)) {

            $qb->leftJoin('m.category', 'c');
            $qb->andWhere("c.name = :category");
            $qb->setParameter('category', $category);
        }

        if (!empty($unit)) {
            $qb->leftJoin('m.productUnit', 'u');
            $qb->andWhere("u.name = :unit");
            $qb->setParameter('unit', $unit);
        }

        $qb->orderBy('m.name','ASC');
        $qb->getQuery();
        return  $qb;

    }

    public function getLastId($inventory)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('count(item.id)');
        $qb->from('InventoryBundle:Item','item');
        $qb->where("item.inventoryConfig = :inventory");
        $qb->setParameter('inventory', $inventory);
        $count = $qb->getQuery()->getSingleScalarResult();
        if($count > 0 ){
         return $count+1;
        }else{
         return 1;
        }

    }

    public function searchAutoComplete($item, InventoryConfig $inventory)
    {

        $search = strtolower($item);
        $query = $this->createQueryBuilder('i');
        $query->join('i.inventoryConfig', 'ic');
        $query->leftJoin('i.stockItems', 'stockItem');
        $query->select('i.id as id');
        $query->addSelect('CONCAT(i.sku, \' - \', i.name,  \' [\',  SUM(stockItem.quantity), \'] \') AS name');
	    $query->addSelect('CONCAT(i.sku, \' - \', i.name,  \' [\',  SUM(stockItem.quantity), \'] \') AS text');
	    $query->addSelect('i.barcode as sku');
        $query->addSelect('SUM(stockItem.quantity) as remainingQuantity');
        $query->where($query->expr()->like("i.name", "'%$search%'"  ));
        $query->orWhere($query->expr()->like("i.skuSlug", "'%$search%'"  ));
        $query->orWhere($query->expr()->like("i.barcode", "'%$search%'"  ));
        $query->andWhere("i.purchaseQuantity > 0 ");
        $query->andWhere("ic.id = :inventory");
        $query->setParameter('inventory', $inventory->getId());
        $query->groupBy('i.id');
        $query->orderBy('i.name', 'ASC');
        $query->setMaxResults( '30' );
        return $query->getQuery()->getResult();

    }

     public function searchAutoCompleteAllItem($item, InventoryConfig $inventory)
    {

        $search = strtolower($item);
        $query = $this->createQueryBuilder('i');
        $query->join('i.inventoryConfig', 'ic');
        $query->select('i.id as id');
	    $query->addSelect('CONCAT(i.sku, \' - \', i.name) AS name');
	   // $query->addSelect('CONCAT(i.sku, \' - \', i.name) AS text');
	    $query->addSelect('i.sku as sku');
        $query->where($query->expr()->like("i.name", "'%$search%'"  ));
	    $query->orWhere($query->expr()->like("i.skuSlug", "'%$search%'"  ));
	   // $query->orWhere($query->expr()->like("i.sku", "'%$search%'"  ));
	   // $query->orWhere($query->expr()->like("i.barcode", "'%$search%'"  ));
        $query->andWhere("ic.id = :inventory");
        $query->setParameter('inventory', $inventory->getId());
        $query->groupBy('i.id');
        $query->orderBy('i.sku', 'ASC');
        $query->setMaxResults( '30' );
        return $query->getQuery()->getResult();

    }

    public function searchBarcodeItem($item, InventoryConfig $inventory)
    {

        $search = strtolower($item);
        $query = $this->createQueryBuilder('i');
        $query->join('i.inventoryConfig', 'ic');
        $query->select('i.id as id');
        $query->addSelect('i.barcode AS name');
        $query->addSelect('i.barcode AS text');
        $query->where($query->expr()->like("i.barcode", "'%$search%'"  ));
        $query->andWhere("ic.id = :inventory");
        $query->andWhere("i.status = 1");
        $query->setParameter('inventory', $inventory->getId());
        $query->groupBy('i.id');
        $query->orderBy('i.sku', 'ASC');
        $query->setMaxResults( '30' );
        return $query->getQuery()->getResult();

    }

    public function updateRemainingQuantity(Item $item)
    {
        $em = $this->_em;
        $remainingQnt = ($item->getPurchaseQuantity() + $item->getSalesQuantityReturn()) - ($item->getSalesQuantity() + $item->getPurchaseQuantityReturn()+$item->getDamageQuantity());
        $item->setRemainingQnt($remainingQnt);
        $em->flush();

    }

    public function getItemUpdatePriceQnt(Purchase $purchase){

        $em = $this->_em;
        /* @var $purchaseItem PurchaseItem */
        foreach($purchase->getPurchaseItems() as $purchaseItem ){

            $entity = $purchaseItem->getItem();
            /** @var Item $entity */
            $qnt = $this->_em->getRepository('InventoryBundle:StockItem')->getItemQuantity($entity->getId(),'purchase');
            $entity->setPurchaseQuantity($qnt);
            if($purchaseItem->getSalesPrice() > 0 ){
                $entity->setSalesPrice($purchaseItem->getSalesPrice());
            }
            $entity->setPurchasePrice($purchaseItem->getPurchasePrice());
            $avgPurchasePrice = $em->getRepository("InventoryBundle:PurchaseItem")->getItemAveragePrice($purchaseItem->getItem());
            $entity->setPurchaseAvgPrice($avgPurchasePrice['purchaseAvg']);
            $entity->setSalesAvgPrice($avgPurchasePrice['salesAvg']);
            $entity->setUpdated($purchase->getCreated());
            $em->persist($entity);
            $em->flush();
            $this->updateRemainingQuantity($entity);
        }
    }

    public function purchaseItemReverseUpdateQnt(Purchase $purchase){

        $em = $this->_em;
        /** @var $purchaseItem PurchaseItem  */
        foreach($purchase->getPurchaseItems() as $purchaseItem ){

            $entity = $purchaseItem->getItem();
            /** @var Item $entity */
            $qnt = $this->_em->getRepository('InventoryBundle:StockItem')->getItemQuantity($entity->getId(),'purchase');
            $entity->setPurchaseQuantity(abs($qnt));
            $entity->setUpdated($purchase->getCreated());
            $em->persist($entity);
            $em->flush();
            $this->updateRemainingQuantity($entity);

        }
    }

    public function getItemPurchaseReturn(PurchaseReturn $purchaseReturn){

        $em = $this->_em;
        /* @var $purchaseReturnItem PurchaseReturnItem */
        foreach($purchaseReturn->getPurchaseReturnItems() as $purchaseReturnItem ){

            $entity = $purchaseReturnItem->getPurchaseItem()->getItem();
            $qnt = ($entity->getPurchaseQuantityReturn() + $purchaseReturnItem->getQuantity());
            $entity->setPurchaseQuantityReturn($qnt);
            $entity->setUpdated($purchaseReturn->getCreated());
            $em->persist($entity);
            $em->flush();
            $this->updateRemainingQuantity($entity);
        }


    }

    public function getItemPurchaseReplace($purchaseReplaceItem){

        $em = $this->_em;
            $entity = $purchaseReplaceItem->getPurchaseItem()->getItem();
            $qnt = ($entity->getPurchaseQuantityReplace() + $purchaseReplaceItem->getReplaceQuantity());
            $entity->setPurchaseQuantityReturn($qnt);
            $em->persist($entity);
            $em->flush();
        $this->updateRemainingQuantity($entity);

    }

    public function getItemSalesUpdate($id){

        $em = $this->_em;
        $salesItems = $em->getRepository("InventoryBundle:SalesItem")->findBy(array('sales' => $id));
        if($salesItems){
            /** @var $salesItem SalesItem */
            foreach($salesItems as $salesItem ){
                /** @var  $entity Item */
                $entity = $salesItem->getItem();
                $qnt = $this->_em->getRepository('InventoryBundle:StockItem')->getItemQuantity($entity->getId(),'sales');
                $entity->setSalesQuantity(abs($qnt));
                $em->persist($entity);
                $em->flush($entity);
                $this->updateRemainingQuantity($entity);
            }
        }
    }

    public function getSalesItemReverse(Sales $sales){

        $em = $this->_em;

        /* @var  $salesItem SalesItem */

        foreach ($sales->getSalesItems() as $salesItem ){

            /* @var Item $entity */
            $entity = $salesItem->getItem();
            $qnt = $this->_em->getRepository('InventoryBundle:StockItem')->getItemQuantity($entity->getId(),'sales');
            $entity->setSalesQuantity(abs($qnt));
            $em->persist($entity);
            $em->flush($entity);
            $this->updateRemainingQuantity($entity);
        }

    }

    public function getItemSalesReturnUpdate(SalesReturn $sales){

        $em = $this->_em;

        /** @var $salesItem SalesItem */

        foreach($sales->getSalesReturnItems() as $salesItem ){

            /** @var Item $entity */

            $entity = $salesItem->getSalesItem()->getItem();
            $qnt = ($entity->getSalesQuantityReturn() + $salesItem->getQuantity());
            $entity->setSalesQuantityReturn($qnt);
            $em->persist($entity);
            $em->flush($entity);
            $this->updateRemainingQuantity($entity);
        }


    }

    public function onlineOrderUpdate(Order $order){

        $em = $this->_em;
        foreach($order->getOrderItems() as $orderItem ) {

            $entity = $orderItem->getPurchaseItem()->getItem();
            $qnt = ($entity->getOnlineOrderQuantity() + $orderItem->getQuantity());
            $entity->setOnlineOrderQuantity($qnt);
            $em->persist($entity);
            $em->flush($entity);
        }

    }

    public function itemDamageUpdate(Damage $damage){

        $em = $this->_em;

            $entity = $damage->getItem();
            $qnt = ($entity->getDamageQuantity() + $damage->getQuantity());
            $entity->setDamageQuantity($qnt);
            $em->persist($entity);
            $em->flush($entity);
        $this->updateRemainingQuantity($entity);

    }

    public function groupByItemCategory(InventoryConfig $config){

        $em = $this->_em;

        $qb = $this->createQueryBuilder('item');
        $qb->join('item.masterItem', 'mi');
        $qb->join('mi.category', 'category');
        $qb->join('item.inventoryConfig', 'ic');
        $qb->select('category.id as id');
        $qb->addSelect('category.name as name','category.slug as slug');
        $qb->where("item.inventoryConfig = :inventory");
        $qb->setParameter('inventory', $config->getId());
        $qb->groupBy('category.id');
        $qb->orderBy('category.name','ASC');
        return $qb->getQuery()->getArrayResult();

    }

    public function itemPurchaseDetails($securityContext,$inventory,$id,$customer = '', $device = '')
    {

        $data ='';
        $result = $this->findOneBy(array('inventoryConfig' => $inventory,'id' => $id));
        foreach($result->getPurchaseItems() as $purchaseItem  ) {

            $grn = $purchaseItem->getPurchase()->getGrn();
            $received = $purchaseItem->getPurchase()->getReceiveDate()->format('d M,Y');

            if ($securityContext->isGranted('ROLE_DOMAIN') || $securityContext->isGranted('ROLE_DOMAIN_INVENTORY_PURCHASE')|| $securityContext->isGranted('ROLE_DOMAIN_INVENTORY') ) {
                 $purchasePrice = $purchaseItem->getPurchasePrice();
            }else{
                $purchasePrice = '';
            }

            $ongoingSalesQnt = $this->_em->getRepository('InventoryBundle:SalesItem')->checkSalesQuantity($purchaseItem);
            $data .= '<tr>';
            $data .= '<td class="numeric" >' . $purchaseItem->getBarcode() .'</td>';
            if($device != 'mobile') {
                $data .= '<td class="numeric" >' . $result->getSku() . '</td>';
                $data .= '<td class="numeric" >' . $received.' / '.$grn . '</td>';
                $data .= '<td class="numeric" >' . $purchaseItem->getItemStock() . '</td>';
                $data .= '<td class="numeric" >' . $ongoingSalesQnt . '</td>';
            }
            $data .= '<td class="numeric" >' . ($purchaseItem->getItemStock() - $ongoingSalesQnt) . '</td>';
            if($device != 'mobile') {
                $data .= '<td class="numeric" >' . $purchasePrice . '</td>';
            }
            $data .= '<td class="numeric" >' . $purchaseItem->getSalesPrice().'</td>';
            $data .= '<td class="numeric" ><a class="btn mini blue addSales" href="javascript:" id="'.$purchaseItem->getBarcode().'"><i class="icon-shopping-cart"></i>  Add</a></td>';
            $data .= '</tr>';
        }
        return $data;

    }

    public function itemDeliveryPurchaseDetails(User $user,$item)
    {

        $data ='';

        $result = $this->_em->getRepository('InventoryBundle:Delivery')->stockItemDetails($user,$item);
        $stockSalesItem = $this->_em->getRepository('InventoryBundle:Delivery')->stockSalesItemDetails($user,$item);
        $stockOngoingItem = $this->_em->getRepository('InventoryBundle:Delivery')->stockOngoingItemDetails($user,$item);
        $stockReturnItem = $this->_em->getRepository('InventoryBundle:Delivery')->stockReturnItemDetails($user,$item);

        foreach( $result as $row  ) {


            $received = $row['purchaseDate']->format('d-m-Y');
            $memo = $row['memo'];

            $salesQnt = !empty($stockSalesItem[$row['purchaseItemId']]) ? $stockSalesItem[$row['purchaseItemId']] : 0;
            $ongoingQnt = !empty($stockOngoingItem[$row['purchaseItemId']]) ? $stockOngoingItem[$row['purchaseItemId']] : 0;
            $returnQnt = !empty($stockReturnItem[$row['purchaseItemId']]) ? $stockReturnItem[$row['purchaseItemId']] : 0;

            $remaingQnt = $row['receiveQuantity'] - $salesQnt - $returnQnt ;

            $data .= '<tr>';
            $data .= '<td class="numeric" >'.$row['barcode'].'</td>';
            $data .= '<td class="numeric" >'.$row['name'].' / '.$row['sku'].'</td>';
            $data .= '<td class="numeric" >'.$received.'/'.$memo.'</td>';
            $data .= '<td class="numeric" >'.$remaingQnt.'</td>';
            $data .= '<td class="numeric" >'.$ongoingQnt.'</td>';
            $data .= '<td class="numeric" >'.$remaingQnt - $ongoingQnt.'</td>';
            $data .= '<td class="numeric" >'.$row['salesPrice'].'</td>';
            $data .= '<a class="btn mini blue addSales" href="javascript:" id="'.$row['barcode'].'"><i class="icon-plus"></i> Add</a></td>';
            $data .= '</tr>';
        }

        return $data;
    }

    public function skuUpdate(InventoryConfig $config ,Item $entity)
    {

        $masterItem         = $entity->getMasterItem()->getSTRPadCode();
        $masterSlug         = $entity->getMasterItem()->getSlug();
        $masterName         = $entity->getMasterItem()->getName();

        $color ='';
        $colorName ='';

        if(!empty($config->getIsColor()) and $config->getIsColor() == 1 ){
            $color              = '-C'.$entity->getColor()->getSTRPadCode();
            $colorSlug          = $entity->getColor()->getSlug();
            $colorName          = '-'.$entity->getColor()->getName();
        }elseif(!empty($entity->getColor())){
            $colorSlug          =$entity->getColor()->getSlug();
        }else{
            $colorSlug ='';
        }

        $size ='';
        $sizeName = '';

        if(!empty($config->getIsSize()) and $config->getIsSize() == 1){
            $size               = '-S'.$entity->getSize()->getSTRPadCode();
            $sizeSlug           = $entity->getSize()->getSlug();
            $sizeName           = '-'.$entity->getSize()->getName();
        }elseif(!empty($entity->getSize())){
            $sizeSlug           = $entity->getSize()->getSlug();
        }else{
            $sizeSlug = '';
        }

        $brand ='';
        $brandName = '';

        if(!empty($config->getIsBrand()) and $config->getIsBrand() == 1){
            $brand               = '-B'.$entity->getBrand()->getSTRPadCode();
            $brandSlug           = $entity->getBrand()->getSlug();
            $brandName           = '-'.$entity->getBrand()->getName();
        }elseif(!empty($entity->getBrand())){
            $brandSlug           = $entity->getBrand()->getSlug();
        }else{
            $brandSlug = '';
        }


        $vendor ='';
        $vendorName ='';

        if(!empty($config->getIsVendor()) and $config->getIsVendor() == 1 ){

            $vendor             = '-V'.$entity->getVendor()->getSTRPadCode();
            $vendorSlug         =  $entity->getVendor()->getSlug();
            $vendorName         = '-'.$entity->getVendor()->getVendorCode();

        }elseif(!empty($entity->getVendor())){
            $vendorSlug           = $entity->getVendor()->getSlug();
        }else{
            $vendorSlug = '';
        }

        $sku            = $masterItem.$color.$size.$brand.$vendor;
        $name           = $masterName.$colorName.$sizeName.$brandName.$vendorName;
        $skuSlug        = $masterSlug.$colorSlug.$sizeSlug.$brandSlug.$vendorSlug;


        $domainSlug     = $config->getGlobalOption()->getSlug();
        $skuWeb         = $skuSlug.'_'.$domainSlug;

        $em = $this->_em;
        $entity->setName($name);
        $entity->setSku($sku);
        $entity->setSlug($skuSlug);
        $entity->setSkuSlug($skuSlug);
        $entity->setSkuWebSlug($skuWeb);
        $em->persist($entity);
        $em->flush();

    }


	public function inventoryShortListCount(User $user)
	{
		$config =  $user->getGlobalOption()->getInventoryConfig()->getId();
		$qb = $this->createQueryBuilder('item');
		$qb->select('COUNT(item.id) as totalShortList');
		$qb->where("item.inventoryConfig = :config");
		$qb->setParameter('config', $config);
		$qb->andWhere("item.minQnt > 0");
		$qb->andWhere("item.minQnt >= item.remainingQnt");
		$count = $qb->getQuery()->getOneOrNullResult()['totalShortList'];
		return  $count;

	}


	public function getBarcodeForPrint($inventory,$data)
	{

		$qb = $this->createQueryBuilder('item');
		$qb->join('item.inventoryConfig', 'ic');
		$qb->select('item.barcode');
		$qb->addSelect('item.id');
		$qb->addSelect('item.sku');
		$qb->addSelect('item.name');
		$qb->addSelect('item.salesPrice');
		$qb->addSelect('item.remainingQnt');
		$qb->where($qb->expr()->in("item.id", $data ));
		$qb->andWhere("item.inventoryConfig = :inventory");
		$qb->setParameter('inventory', $inventory->getId());
		$qb->orderBy('item.name','ASC');
		return $qb->getQuery()->getArrayResult();

	}

    public function  processStockMigration($from, $to)
    {

        $em = $this->_em;
        $stock = $em->createQuery("DELETE InventoryBundle:Item e WHERE e.inventoryConfig={$to}");
        if($stock){
            $stock->execute();
        }

        $elem = "INSERT INTO medicine_stock(`unit_id`,`name`,`minQuantity`,`purchasePrice`,`salesPrice`, `medicineBrand_id`,`brandName`,`pack`,`averagePurchasePrice`,`averageSalesPrice`,`isAndroid`,`printHide`,mode,status,`medicineConfig_id`)
  SELECT `unit_id`, `name`,`minQuantity`, `purchasePrice`, `salesPrice`, `medicineBrand_id`, `brandName`, `pack`, `averagePurchasePrice`, `averageSalesPrice`, `isAndroid`, `printHide`,mode,1,$to
  FROM medicine_stock
  WHERE medicineConfig_id =:config";
        $qb1 = $this->getEntityManager()->getConnection()->prepare($elem);
        $qb1->bindValue('config', $from);
        $qb1->execute();

        $stockUpdate = "UPDATE medicine_stock SET mode = 'medicice' WHERE  medicineConfig_id =:config AND mode IS NULL";
        $qb1 = $this->getEntityManager()->getConnection()->prepare($stockUpdate);
        $qb1->bindValue('config', $to);
        $qb1->execute();
    }


}
