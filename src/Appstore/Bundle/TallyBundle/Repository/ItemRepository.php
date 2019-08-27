<?php

namespace Appstore\Bundle\TallyBundle\Repository;
use Appstore\Bundle\AccountingBundle\Entity\AccountPurchase;
use Appstore\Bundle\TallyBundle\Entity\Product;
use Appstore\Bundle\TallyBundle\Entity\Item;
use Appstore\Bundle\TallyBundle\Entity\Purchase;
use Appstore\Bundle\TallyBundle\Entity\PurchaseItem;
use Appstore\Bundle\TallyBundle\Entity\Sales;
use Appstore\Bundle\TallyBundle\Entity\StockItem;
use Appstore\Bundle\TallyBundle\Entity\VoucherItem;
use Core\UserBundle\Entity\User;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Setting\Bundle\ToolBundle\Event\Glo;
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

    /**
     * @param $qb
     * @param $data
     */

    protected function handleWithSearch($qb,$data)
    {
        if(!empty($data))
        {
            $item = isset($data['item'])? $data['item'] :'';
            $color = isset($data['color'])? $data['color'] :'';
            $size = isset($data['size'])? $data['size'] :'';
            $vendor = isset($data['vendor'])? $data['vendor'] :'';
            $brand = isset($data['brand'])? $data['brand'] :'';
            $category = isset($data['category'])? $data['category'] :'';
            $unit = isset($data['unit'])? $data['unit'] :'';
            $barcode = isset($data['barcode'])? $data['barcode'] :'';

            if (!empty($barcode)) {

                $qb->join('e.purchaseItem', 'p');
                $qb->andWhere("p.barcode = :barcode");
                $qb->setParameter('barcode', $barcode);
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
                $qb->join('m.category','cat');
                $qb->andWhere("cat.name = :category");
                $qb->setParameter('category', $category);
            }

            if (!empty($unit)) {
                $qb->join('m.productUnit','u');
                $qb->andWhere("b.name = :unit");
                $qb->setParameter('unit', $unit);
            }

        }

    }

    public function modeWiseStockItem($inventory,$mode ='purchase',$data)
    {

        $qb = $this->createQueryBuilder('item');
        $qb->where("item.config = :inventory")->setParameter('inventory', $inventory);
        $qb->andWhere("item.productType = :type")->setParameter('type', "Assets");
        $this->handleWithSearch($qb,$data);
        $qb->orderBy('item.name','ASC');
        $qb->getQuery();
        return  $qb;

    }

    public function filterFrontendProductWithSearch($data , $limit = 0)
    {
        if (!empty($data['sortBy'])) {

            $sortBy = explode('=?=', $data['sortBy']);
            $sort = $sortBy[0];
            $order = $sortBy[1];
        }

        $qb = $this->createQueryBuilder('product');
        $qb->leftJoin("product.masterItem",'masterItem');
        $qb->leftJoin('product.goodsItems','goodsitems');
        $qb->where("product.isWeb = 1");
        $qb->andWhere("product.status = 1");
        $qb->andWhere("product.inventoryConfig = :inventory");
        $qb->setParameter('inventory', $inventory);

        if (!empty($data['brand'])) {
            $qb->andWhere("product.brand IN(:brand)");
            $qb->setParameter('brand',$data['brand']);
        }

        if (!empty($data['size'])) {
            $qb->andWhere("goodsitems.size IN(:size)");
            $qb->setParameter('size',$data['size']);
        }

        if (!empty($data['color'])) {
            $qb->leftJoin('goodsitems.colors','colors');
            $qb->andWhere("colors.id IN(:color)");
            $qb->setParameter('color',$data['color']);
        }

        if (!empty($data['promotion'])) {
            $qb->andWhere("product.promotion IN(:promotion)");
            $qb->setParameter('promotion',$data['promotion']);
        }

        if (!empty($data['tag'])) {
            $qb->andWhere("product.tag IN(:tag)");
            $qb->setParameter('tag',$data['tag']);
        }

        if (!empty($data['discount'])) {
            $qb->andWhere("product.discount IN(:discount)");
            $qb->setParameter('discount',$data['discount']);
        }

        if (!empty($data['priceStart'])) {
            $qb->andWhere(' product.salesPrice >= :priceStart');
            $qb->setParameter('priceStart',$data['priceStart']);
        }

        if (!empty($data['priceEnd'])) {
            $qb->andWhere(' product.salesPrice <= :priceEnd');
            $qb->setParameter('priceEnd',$data['priceEnd']);
        }

        if (empty($data['sortBy'])){
            $qb->orderBy('product.updated', 'DESC');
        }else{
            $qb->orderBy($sort ,$order);
        }
        if($limit > 0 ) {
            $qb->setMaxResults($limit);
        }
        $res = $qb->getQuery();
        return  $res;

    }

    public function getFeatureCategoryProduct($inventory,$data,$limit){


        $qb = $this->createQueryBuilder('product');
        $qb->leftJoin("product.masterItem",'masterItem');
        $qb->leftJoin('product.goodsItems','goodsitems');
        $qb->where("product.isWeb = 1");
        $qb->andWhere("product.status = 1");
        $qb->andWhere("product.inventoryConfig = :inventory");
        $qb->setParameter('inventory', $inventory);

        if (!empty($data['brand'])) {
            $qb->andWhere("product.brand IN(:brand)");
            $qb->setParameter('brand',$data['brand']);
        }
        if (!empty($data['promotion'])) {
            $qb->andWhere("product.promotion IN(:promotion)");
            $qb->setParameter('promotion',$data['promotion']);
        }

        if (!empty($data['tag'])) {
            $qb->andWhere("product.tag IN(:tag)");
            $qb->setParameter('tag',$data['tag']);
        }

        if (!empty($data['discount'])) {
            $qb->andWhere("product.discount IN(:discount)");
            $qb->setParameter('discount',$data['discount']);
        }

        if (!empty($data['category'])) {

            $qb
                ->join('masterItem.category', 'category')
                ->andWhere(
                    $qb->expr()->orX(
                        $qb->expr()->like('category.path', "'". intval($data['category']) . "/%'"),
                        $qb->expr()->like('category.path', "'%/" . intval($data['category']) . "/%'")
                    )
                );
        }
        $qb->orderBy('product.updated', 'DESC');
        if($limit > 0 ) {
            $qb->setMaxResults($limit);
        }
        $res = $qb->getQuery();
        return  $res;
    }


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

        $qb->groupBy('item.id');
        $result = $qb->getQuery()->getResult();
        foreach ($result as $row ){
            $entity = $this->find($row['id']);
            $entity->setPurchaseQuantity($row['quantity']);
            $this->_em->persist($entity);
            $this->_em->flush($entity);
        }

    }

    public function checkDuplicateSKU($config,$data)
    {


        $type = $data['item']['productType'];
        $masterItem = $data['item']['name'];
        $vendor     = isset($data['item']['vendor']) ? $data['item']['vendor'] :'NULL';
        $brand  = isset($data['item']['brand']) ? $data['item']['brand']:'NULL';
        $category  = isset($data['item']['brand']) ? $data['item']['category']:'NULL';

        $qb = $this->createQueryBuilder('e');
        $qb->select('COUNT(e.id) countid');
        $qb->where("e.config = :config");
        $qb->setParameter('config', $config);
        $qb->andWhere("e.productType = :type")->setParameter('type', $type);
        $qb->andWhere("e.name = :name")->setParameter('name', $masterItem);
        if($category){
            $qb->andWhere("e.category = :category")->setParameter('category', $category);
        }
        if($vendor){
            $qb->andWhere("e.vendor = :vendor")->setParameter('vendor', $vendor);
        }
        if($brand){
            $qb->andWhere("e.brand = :brand")->setParameter('brand', $brand);
        }
        $count = $qb->getQuery()->getOneOrNullResult();
        $result = $count['countid'];
        return $result;

    }


    public function findWithSearch($config,$data)
    {

        $item = isset($data['item'])? $data['item'] :'';
        $branch = isset($data['branch'])? $data['branch'] :'';
        $category = isset($data['category'])? $data['category'] :'';
        $parent = isset($data['parent'])? $data['parent'] :'';
	    $depreciation = isset($data['depreciation'])? $data['depreciation'] :'';


        $qb = $this->createQueryBuilder('item');
        $qb->where("item.status IS NOT NULL");
        $qb->andWhere("item.config = :config")->setParameter('config', $config);
        if (!empty($item)) {
            $qb->andWhere("item.name = :name");
            $qb->setParameter('name', $item);
        }

        if (!empty($category)) {
            $qb->join('item.category', 'c');
            $qb->andWhere("c.name = :category");
            $qb->setParameter('category', $category);
        }

        if (!empty($parent)) {
            $qb->join('item.parentCategory', 'pc');
            $qb->andWhere("pc.name = :parent");
            $qb->setParameter('parent', $parent);
        }

        if (!empty($depreciation)) {
	        $qb->join('item.depreciation', 'd');
            $qb->andWhere("d.id = :depreciation");
            $qb->setParameter('depreciation', $depreciation);
        }

        if (!empty($vendor)) {
            $qb->join('item.vendor', 'v');
            $qb->andWhere("v.companyName = :vendor");
            $qb->setParameter('vendor', $vendor);
        }

        if (!empty($branch)) {

            $qb->join('item.branch', 'b');
            $qb->andWhere("b.name = :branch");
            $qb->setParameter('branch', $branch);

        }
        $qb->orderBy('item.name','ASC');
        $qb->getQuery();
        return  $qb;

    }

	public function depreciationGenerate($data)
	{

		$item = isset($data['item'])? $data['item'] :'';
		$category = isset($data['category'])? $data['category'] :'';

		$qb = $this->createQueryBuilder('item');
		$qb->where("item.status IS NOT NULL");
		if (!empty($item)) {
			$qb->join('item.item', 'm');
			$qb->andWhere("m.id = :name");
			$qb->setParameter('name', $item);
		}
		if (!empty($category)) {
			$qb->join('item.category', 'c');
			$qb->andWhere("c.id = :category");
			$qb->setParameter('category', $category);
		}
		$qb->orderBy('item.updated','DESC');
		$qb->getQuery();
		return  $qb;

	}

    public function getInventoryExcel($inventory,$data){

        $item = isset($data['item'])? $data['item'] :'';
        $gpSku = isset($data['gpSku'])? $data['gpSku'] :'';
        $category = isset($data['category'])? $data['category'] :'';
        $brand = isset($data['brand'])? $data['brand'] :'';

        $qb = $this->createQueryBuilder('item');
        $qb->join('item.masterItem', 'm');
        $qb->where("item.inventoryConfig = :inventory");
        $qb->setParameter('inventory', $inventory);

        if (!empty($item)) {

            $qb->andWhere("m.name = :name");
            $qb->setParameter('name', $item);
        }
        if (!empty($gpSku)) {
            $qb->andWhere($qb->expr()->like("item.gpSku", "'%$gpSku%'"  ));
        }
        if (!empty($category)) {
            $qb->join('m.category', 'c');
            $qb->andWhere("c.name = :category");
            $qb->setParameter('category', $category);
        }
        if (!empty($brand)) {
            $qb->join('m.brand', 'b');
            $qb->andWhere("b.name = :brand");
            $qb->setParameter('brand', $brand);
        }
        $qb->orderBy('item.gpSku','ASC');
        $result = $qb->getQuery()->getResult();
        return  $result;
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

    public function searchAutoComplete($item, $config)
    {

        $search = strtolower($item);
        $query = $this->createQueryBuilder('i');
        $query->select('i.id as id');
        $query->addSelect('i.name as name');
        $query->addSelect('i.slug as text');
        $query->addSelect('i.sku as sku');
        $query->addSelect('i.remainingQuantity as remainingQuantity');
        $query->where($query->expr()->like("i.slug", "'$search%'"  ));
        $query->andWhere("i.remainingQuantity > 0 ");
        $query->andWhere("i.config = :config");
        $query->setParameter('config', $config);
        $query->orderBy('i.name', 'ASC');
        $query->setMaxResults( '30' );
        return $query->getQuery()->getResult();

    }

     public function searchAutoCompleteAllItem($item,  $inventory)
    {

        $search = strtolower($item);
        $query = $this->createQueryBuilder('i');
        $query->join('i.config', 'ic');
        $query->select('i.id as id');
        $query->addSelect('i.name as name');
        $query->addSelect('i.skuSlug as text');
        $query->addSelect('i.sku as sku');
        $query->where($query->expr()->like("i.skuSlug", "'%$search%'"  ));
        $query->andWhere("ic.id = :inventory");
        $query->setParameter('inventory', $inventory);
        $query->groupBy('i.id');
        $query->orderBy('i.name', 'ASC');
        $query->setMaxResults( '30' );
        return $query->getQuery()->getResult();

    }

    public function updateRemovePurchaseQuantity(Item $stock , $fieldName = '', $minStock = 0 ){

        $em = $this->_em;

        if($fieldName == 'sales'){
            $quantity = $em->getRepository('TallyBundle:StockItem')->getItemUpdateQuantity($stock->getId(),'sales');
            $stock->setSalesQuantity($quantity);
        }elseif($fieldName == 'sales-return'){
            $quantity = $this->_em->getRepository('TallyBundle:StockItem')->getItemUpdateQuantity($stock,'sales-return');
            $stock->setSalesReturnQuantity($quantity);
        }elseif($fieldName == 'purchase'){
            $quantity = $em->getRepository('TallyBundle:StockItem')->getItemUpdateQuantity($stock,'purchase');
            $stock->setPurchaseQuantity($quantity);
         }elseif($fieldName == 'purchase-return'){
            $quantity = $em->getRepository('TallyBundle:StockItem')->getItemUpdateQuantity($stock,'purchase-return');
            $stock->setPurchaseReturnQuantity($quantity);
        }elseif($fieldName == 'damage'){
            $quantity = $em->getRepository('TallyBundle:StockItem')->getItemUpdateQuantity($stock,'damage');
            $stock->setDamageQuantity($quantity);
        }elseif($fieldName == 'opening'){
            $quantity = $em->getRepository('TallyBundle:StockItem')->getItemUpdateQuantity($stock->getId(),'opening');
            $stock->setOpeningQuantity($quantity);
        }elseif($fieldName == 'assets'){
            $quantity = $em->getRepository('TallyBundle:StockItem')->getItemUpdateQuantity($stock,'assets');
            $stock->setAssetsQuantity($quantity);
        }elseif($fieldName == 'assets-return'){
            $quantity = $em->getRepository('TallyBundle:StockItem')->getItemUpdateQuantity($stock,'assets-return');
            $stock->setAssetsReturnQuantity($quantity);
        }
        $em->persist($stock);
        $em->flush();
        $this->remainingQnt($stock);
    }

    public function remainingQnt(Item $stock)
    {
        $em = $this->_em;
        $qnt = ($stock->getOpeningQuantity() + $stock->getPurchaseQuantity() + $stock->getSalesReturnQuantity()) - ($stock->getPurchaseReturnQuantity() + $stock->getSalesQuantity() + $stock->getDamageQuantity());
        $stock->setRemainingQuantity($qnt);
        $em->persist($stock);
        $em->flush();
    }



    public function getPurchaseUpdateQnt(Purchase $entity){

        $em = $this->_em;

        /** @var $item PurchaseItem  */

        if($entity->getPurchaseItems()){

            foreach($entity->getPurchaseItems() as $item ){
                /** @var  $stock Item */
                $stock = $item->getItem();
                $this->updateRemovePurchaseQuantity($stock,'purchase');
            }
        }
    }

    public function getSalesUpdateQnt(Sales $entity){

        $em = $this->_em;

        /** @var $item StockItem  */

        if($entity->getStockItems()){

            foreach($entity->getStockItems() as $item ){

                /** @var  $stock Item */
                $stock = $item->getItem();
                $this->updateRemovePurchaseQuantity($stock,'assets');
            }
        }
    }



}