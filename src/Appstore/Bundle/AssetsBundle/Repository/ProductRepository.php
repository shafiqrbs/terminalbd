<?php

namespace Appstore\Bundle\AssetsBundle\Repository;
use Appstore\Bundle\AssetsBundle\Entity\Product;
use Core\UserBundle\Entity\User;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\DependencyInjection\Container;

use Doctrine\ORM\EntityRepository;

/**
 * ItemTypeGroupingRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProductRepository extends EntityRepository
{

    public function findFrontendProductWithSearch($inventory, $data , $limit = 0)
    {
        if (!empty($data['sortBy'])) {

            $sortBy = explode('=?=', $data['sortBy']);
             $sort = $sortBy[0];
             $order = $sortBy[1];
        }

        $qb = $this->createQueryBuilder('product');
        $qb->leftJoin("product.item",'masterItem');
        $qb->leftJoin('product.brand','brand');
        $qb->where("product.purchaseQuantity > 0");
        $qb->andWhere("product.status = 1");
        $qb->andWhere("product.isWeb = 1");
        $qb->andWhere("product.inventoryConfig = :inventory");
        $qb->setParameter('inventory', $inventory);

        if (!empty($data['brand'])) {
            $qb->andWhere("masterItem.brand = :brand");
            $qb->setParameter('brand', $data['brand']);
        }

        if (!empty($data['tag'])) {
            $qb->leftJoin('masterItem.tag','tag');
            $qb->andWhere("tag.id = :tagId");
            $qb->setParameter('tagId', $data['tag']);
        }

        if (!empty($data['discount'])) {
            $qb->andWhere("masterItem.discount >= :discount");
            $qb->setParameter('discount', $data['discount']);
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
        if (!empty($data['product'])) {
             $search = strtolower($data['product']);
            $qb->andWhere($qb->expr()->like(strtolower("product.name"), "'$search%'"  ));
        }

        if (empty($data['sortBy'])){
            $qb->orderBy('product.updated', 'DESC');
            $qb->orderBy('product.name','ASC');
        }else{
            $qb->orderBy($sort ,$order);
        }
        if($limit > 0 ) {
            $qb->setMaxResults($limit);
        }

        $res = $qb->getQuery();
        return  $res;

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

    public function checkDuplicateSKU(GlobalOption $option,$data)
    {


        $masterItem = $data['item']['name'];
        $vendor     = isset($data['item']['vendor']) ? $data['item']['vendor'] :'NULL';
        $itemBrand  = isset($data['item']['brand']) ? $data['item']['brand']:'NULL';

        $qb = $this->createQueryBuilder('e');
        $qb->select('COUNT(e.id) countid');
        $qb->where("e.globalOption = :globalOption");
        $qb->setParameter('globalOption', $option);
        $count = $qb->getQuery()->getOneOrNullResult();
        $result = $count['countid'];
        return $result;

    }


    public function findWithSearch($data)
    {

        $item = isset($data['item'])? $data['item'] :'';
        $branch = isset($data['branch'])? $data['branch'] :'';
        $category = isset($data['category'])? $data['category'] :'';
        $parent = isset($data['parent'])? $data['parent'] :'';
	    $depreciation = isset($data['depreciation'])? $data['depreciation'] :'';


        $qb = $this->createQueryBuilder('item');
        $qb->where("item.status IS NOT NULL");
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
        $qb->orderBy('item.updated','DESC');
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

    public function searchAutoComplete($item, InventoryConfig $inventory)
    {

        $search = strtolower($item);
        $query = $this->createQueryBuilder('i');
        $query->join('i.inventoryConfig', 'ic');
        $query->leftJoin('i.stockItems', 'stockItem');
        $query->select('i.id as id');
        $query->addSelect('i.name as name');
        $query->addSelect('i.skuSlug as text');
        $query->addSelect('i.sku as sku');
        $query->addSelect('SUM(stockItem.quantity) as remainingQuantity');
        $query->where($query->expr()->like("i.skuSlug", "'%$search%'"  ));
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
        $query->addSelect('i.name as name');
        $query->addSelect('i.skuSlug as text');
        $query->addSelect('i.sku as sku');
        $query->where($query->expr()->like("i.skuSlug", "'%$search%'"  ));
        $query->andWhere("ic.id = :inventory");
        $query->setParameter('inventory', $inventory->getId());
        $query->groupBy('i.id');
        $query->orderBy('i.name', 'ASC');
        $query->setMaxResults( '30' );
        return $query->getQuery()->getResult();

    }

    public function updateRemainingQuantity(Item $item)
    {
	    $em = $this->_em;
    	$reminQnt = (($item->getPurchaseQuantity() + $item->getSalesQuantity()) - ($item->getSalesQuantity() + $item->getPurchaseQuantityReturn() + $item->getDamageQuantity()));
    	$item->setRemainingQnt($reminQnt);
	    $em->flush();
    }

    public function getItemUpdatePriceQnt($purchase){

        $em = $this->_em;
        foreach($purchase->getPurchaseItems() as $purchaseItem ){

            $entity = $purchaseItem->getItem();

            /** @var Item $entity */

            $qnt = ($entity->getPurchaseQuantity() + $purchaseItem->getQuantity());
            $entity->setPurchaseQuantity($qnt);
            $entity->setUpdated($purchase->getCreated());
            $em->persist($entity);
            $em->flush();
        }
    }

    public function insertReceiveItem(Sales $sales)
    {
	    $em = $this->_em;

	    /** @var  $item SalesItem */

	    $status = $this->_em->getRepository('AssetsBundle:Particular')->findOneBy(array('slug'=>'ready-to-deploy'));
	    $depreciation = $this->_em->getRepository('AssetsBundle:DepreciationModel')->find(1);

	    foreach($sales->getSalesItems() as $item ){

	    	if($item->getItem()->getProductType() == 'assets'){
	    		if(!empty($item->getSerialNo())){
	    			foreach ($item->getSerialNo() as $serialNo):

					    $product = new Product();
					    $product->setSalesItem($item);
					    $product->setSerialNo($serialNo);
					    $product->setName($item->getPurchaseItem()->getName());
					    $product->setBranch($item->getSales()->getBranches());
					    $product->setPurchaseItem($item->getPurchaseItem());
					    $product->setVendor($item->getPurchaseItem()->getPurchase()->getVendor());
					    $product->setPurchaseRequisition('PR-'.$item->getSales()->getPurchaseRequisition()->getGrn());
					    $product->setItem($item->getItem());
					    $product->setCategory($item->getItem()->getMasterItem()->getCategory());
					    $product->setParentCategory($product->getCategory()->getParent());
					    $product->setPurchasePrice($item->getPurchasePrice());
					    $product->setBookValue($item->getPurchasePrice());
					    $product->setAssuranceType($item->getAssuranceType());
					    $product->setExpiredDate($item->getExpiredDate());
					    $product->setDepreciationStatus($status);
					    $product->setDepreciation($depreciation);
					    $em->persist($product);
					    $em->flush($product);
					    $this->_em->getRepository('AssetsBundle:ProductLedger')->insertProductLedger($product);

					endforeach;
			    }

		    }
	    }
    }


    public function getCulculationDiscountPrice(Item $item , Discount $discount)
    {
        if($discount->getType() == 'percentage'){
            $dpPrice = ( ($item->getSalesDistributorPrice() * (int)$discount->getDiscountAmount())/100 );
            $dpDiscountPrice = $item->getSalesDistributorPrice() - $dpPrice;
            $item->setDpDiscountPrice(round($dpDiscountPrice));
            $regPrice = ( ($item->getSalesPrice() * (int)$discount->getDiscountAmount())/100 );
            $salesDiscountPrice = $item->getSalesPrice() - $regPrice;
            $item->setDiscountPrice(round($salesDiscountPrice));
        }else{
            $dpDiscountPrice = ( $item->getSalesDistributorPrice() - (int)$discount->getDiscountAmount());
            $item->setDpDiscountPrice(round($dpDiscountPrice));
            $salesDiscountPrice = ( $item->getSalesPrice() - (int)$discount->getDiscountAmount());
            $item->setDiscountPrice(round($salesDiscountPrice));
        }
        $item->setDiscount($discount);
        $this->_em->persist($item);
        $this->_em->flush();



    }


}
