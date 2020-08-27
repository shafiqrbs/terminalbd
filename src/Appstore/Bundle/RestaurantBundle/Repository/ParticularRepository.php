<?php

namespace Appstore\Bundle\RestaurantBundle\Repository;

use Appstore\Bundle\RestaurantBundle\Entity\Invoice;
use Appstore\Bundle\RestaurantBundle\Entity\InvoiceParticular;
use Appstore\Bundle\RestaurantBundle\Entity\Particular;
use Appstore\Bundle\RestaurantBundle\Entity\ProductionElement;
use Appstore\Bundle\RestaurantBundle\Entity\ProductionExpense;
use Appstore\Bundle\RestaurantBundle\Entity\Purchase;
use Appstore\Bundle\RestaurantBundle\Entity\PurchaseItem;
use Appstore\Bundle\RestaurantBundle\Entity\RestaurantConfig;
use Appstore\Bundle\RestaurantBundle\Entity\RestaurantDamage;
use Doctrine\ORM\EntityRepository;
use Gregwar\Image\Image;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;


/**
 * ParticularRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ParticularRepository extends EntityRepository
{

    /**
     * @param $qb
     * @param $data
     */

    protected function handleSearchBetween($qb,$data)
    {

        $name = isset($data['name']) ? $data['name'] :'';
        $service = isset($data['service']) ? $data['service'] :'';
        $category = isset($data['category']) ? $data['category'] :'';

        if (!empty($name)) {
            $qb->andWhere($qb->expr()->like("e.name", "'%$name%'"));
        }
        if(!empty($category)){
            $qb->andWhere("c.id = :category")->setParameter('category', $category);
        }
        if(!empty($service)){
            $qb->andWhere($qb->expr()->like("s.slug", "'%$service%'"));
        }

    }

    public function getServiceLists(Invoice $invoice,$data)
    {
        $config = $invoice->getRestaurantConfig();
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.service','s');
        $qb->join('e.category','c');
        $qb->addSelect('e.id as id');
        $qb->addSelect('e.name as name');
        $qb->addSelect('e.particularCode');
        $qb->addSelect('e.price');
        $qb->addSelect('e.quantity');
        $qb->addSelect('c.name as categoryName');
        $qb->addSelect('s.name as serviceName');
        $qb->addSelect('s.code as serviceCode');
        $qb->where('e.restaurantConfig = :config');
        $qb->setParameter('config',$config);
        $qb->orderBy('c.name , e.name','ASC');
        $result = $qb->getQuery()->getResult();
      //  $particulars = $this->getServiceWithParticular($config,$services);
        $data = '';
        $service = '';
        foreach ($result as $particular) {

            if ($service != $particular['categoryName']) {
                $data .='<tr>';
                $data .= '<td class="category">'.$particular['categoryName'].'</td>';
                $data .= '<td class="category">&nbsp;</td>';
                $data .= '<td class="category">&nbsp;</td>';
                $data .='</tr>';
            }
            $data .='<tr>';
            $data .='<td>'.$particular['particularCode'] .'-'. $particular['name'].'</td>';
            $data .='<td>'.$particular['price'].'</td>';
            $data .='<td>';
            $data .='<div class="input-group input-append">';
            $data .='<span class="input-group-btn">';
            $data .='<button type="button" class="btn yellow btn-number" data-type="minus" data-field="quantity" data-id="'.$particular['id'].'"  data-text="'.$particular['id'].'" data-title="'.$particular['price'].'"><i class="icon-minus"></i></button>';
            $data .='</span>';
            $data .='<input type="text" readonly="readonly" name="quantity" id="quantity-'.$particular['id'].'" class="form-control m-wrap  span4 input-number" value="1" min="1" max="100" >';
            $data .='<span class="input-group-btn">';
            $data .='<button type="button" class="btn green btn-number" data-type="plus" data-field="quantity" data-id="'.$particular['id'].'"   data-title="'.$particular['price'].'"><i class="icon-plus"></i></button>';
            $data .='<input type="hidden" id="price-'.$particular['id'].'" value="'.$particular['price'].'" name="">';
            $data .='<button type="button" class="btn red addCart" id=""  data-id="'.$particular['id'].'"  data-url="/restaurant/invoice/'.$invoice->getId().'/particular-add" ><i class="icon-shopping-cart"></i></button>';
            $data .='<div>';
            $data .='</td>';
            $data .='</tr>';
            $service = $particular['categoryName'];
        }

        return $data ;

    }

    public function getApiStock(GlobalOption $option)
    {
        $config = $option->getRestaurantConfig();
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.category','c');
        $qb->leftJoin('e.unit','u');
        $qb->select('e.id as stockId','e.name as name','e.quantity as quantity','e.price as salesPrice','e.purchasePrice as purchasePrice','e.path as path');
        $qb->addSelect('u.id as unitId','u.name as unitName');
        $qb->addSelect('c.id as categoryId','c.name as categoryName');
        $qb->where('e.restaurantConfig = :config');
        $qb->setParameter('config',$config);
        $qb->orderBy('e.sorting','ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();

        foreach($result as $key => $row) {
            $data[$key]['global_id']            = (int) $option->getId();
            $data[$key]['item_id']              = (int) $row['stockId'];

            $data[$key]['category_id']          = $row['categoryId'];
            $data[$key]['categoryName']         = $row['categoryName'];
            if ($row['unitId']){
                $data[$key]['unit_id']          = $row['unitId'];
                $data[$key]['unit']             = $row['unitName'];
            }else{
                $data[$key]['unit_id']          = 0;
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

    public function findWithSearch($config,$service, $data = array()){

        $name = isset($data['name'])? $data['name'] :'';
        $category = isset($data['category'])? $data['category'] :'';
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.service','s');
        $qb->where('e.restaurantConfig = :config')->setParameter('config', $config) ;
        $qb->andWhere('s.slug IN (:slugs)')->setParameter('slugs',$service) ;
        $qb->orderBy('e.sorting','ASC');
        $result = $qb->getQuery()->getResult();
        return  $result;
    }

    public function productSortingList($config,$service){

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.service','s');
        $qb->leftJoin('e.category','c');
        $qb->where('e.restaurantConfig = :config')->setParameter('config', $config) ;
        $qb->andWhere('e.status = :status')->setParameter('status', 1) ;
        $qb->andWhere('c.status = :cstatus')->setParameter('cstatus', 1) ;
        $qb->andWhere('s.slug IN (:slugs)')->setParameter('slugs',$service) ;
        $qb->orderBy('e.sorting','ASC');
        $result = $qb->getQuery()->getResult();
        return  $result;
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
            ->addSelect('e.quantity')
            ->addSelect('s.name as serviceName')
            ->addSelect('s.code as serviceCode')
            ->where('e.restaurantConfig = :config')->setParameter('config', $hospital)
            ->andWhere('s.slug IN(:service)')
            ->setParameter('service',array_values($services))
            ->orderBy('e.service','ASC')
            ->orderBy('e.name','ASC')
            ->getQuery()->getArrayResult();
        return  $qb;
    }

    public function getServices($config,$services){


        $particulars = $this->getServiceWithParticular($config,$services);
        $data = '';
        $service = '';
        foreach ($particulars as $particular) {
            if ($service != $particular['serviceName']) {
                if ($service != '') {
                    $data .= '</optgroup>';
                }
                $data .= '<optgroup label="' . $particular['serviceCode'] . '-' . ucfirst($particular['serviceName']) . '">';
            }
            $data .= '<option value="/restaurant/invoice/' . $particular['id'] . '/particular-search">' . $particular['particularCode'] . ' - ' . htmlspecialchars(ucfirst($particular['name'])).' - '.$particular['category'] . ' - Tk. ' . $particular['price'] .'</option>';
            $service = $particular['serviceName'];
        }
        if ($service != '') {
            $data .= '</optgroup>';
        }
        return $data ;

    }


    public function getServiceWithParticular($config,$services){

        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.service','s')
            ->leftJoin('e.category','c')
            ->select('e.id')
            ->addSelect('e.name')
            ->addSelect('e.particularCode')
            ->addSelect('e.price')
            ->addSelect('e.quantity')
            ->addSelect('c.name as category')
            ->addSelect('s.name as serviceName')
            ->addSelect('s.code as serviceCode')
            ->where('e.restaurantConfig = :config')->setParameter('config', $config)
            ->andWhere('s.slug IN(:service)')
            ->setParameter('service',array_values($services))
            ->orderBy('e.service','ASC')
            ->getQuery()->getArrayResult();
            return  $qb;
    }

    public function getMedicineParticular($hospital){

        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.service','s')
            ->leftJoin('e.unit','u')
            ->select('e.id')
            ->addSelect('e.name')
            ->addSelect('e.particularCode')
            ->addSelect('e.price')
            ->addSelect('e.minimumPrice')
            ->addSelect('e.quantity')
            ->addSelect('e.status')
            ->addSelect('e.salesQuantity')
            ->addSelect('e.minQuantity')
            ->addSelect('e.remainingQuantity')
            ->addSelect('e.openingQuantity')
            ->addSelect('u.name as unit')
            ->addSelect('s.name as serviceName')
            ->addSelect('s.code as serviceCode')
            ->addSelect('e.purchasePrice')
            ->addSelect('e.purchaseQuantity')
            ->where('e.restaurantConfig = :config')->setParameter('config', $hospital)
            ->andWhere('s.slug IN(:slugs)')
            ->setParameter('slugs',array_values(array('stockable','consuamble')))
            ->orderBy('e.name','ASC')
            ->getQuery()->getArrayResult();
            return  $qb;
    }

    public function getProductionParticular($config){

        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.service','s')
            ->leftJoin('e.unit','u')
            ->select('e.id')
            ->addSelect('e.name')
            ->addSelect('e.particularCode')
            ->addSelect('e.price')
            ->addSelect('u.name as unit')
            ->addSelect('e.purchasePrice')
            ->where('e.restaurantConfig = :config')->setParameter('config', $config)
            ->andWhere('s.slug IN(:slugs)')
            ->setParameter('slugs',array_values(array('stockable','consuamble')))
            ->orderBy('e.name','ASC')
            ->getQuery()->getArrayResult();
        return  $qb;
    }

    public function getAccessoriesParticular($config,$data = ""){


        $qb = $this->createQueryBuilder('e');
        $qb->leftJoin('e.restaurantConfig','r');
        $qb->leftJoin('e.service','s');
            $qb->leftJoin('e.category','c');
            $qb->leftJoin('e.unit','u');
            $qb->select('e.id');
            $qb->addSelect('e.name');
            $qb->addSelect('e.productionType as productionType');
            $qb->addSelect('r.id as restaurantConfig');
            $qb->addSelect('r.isProduction as isProduction');
            $qb->addSelect('e.particularCode');
            $qb->addSelect('e.price');
            $qb->addSelect('e.minimumPrice');
            $qb->addSelect('e.quantity');
            $qb->addSelect('e.status');
            $qb->addSelect('e.salesQuantity');
            $qb->addSelect('e.minQuantity');
            $qb->addSelect('e.openingQuantity');
            $qb->addSelect('e.remainingQuantity');
            $qb->addSelect('u.name as unit');
            $qb->addSelect('s.name as serviceName');
            $qb->addSelect('s.slug as serviceSlug');
            $qb->addSelect('s.code as serviceCode');
            $qb->addSelect('c.name as categoryName');
            $qb->addSelect('e.purchasePrice');
            $qb->addSelect('e.purchaseQuantity');
            $qb->where('e.restaurantConfig = :config')->setParameter('config', $config);
            $qb->andWhere('s.slug IN(:slugs)');
            $qb->setParameter('slugs',array('stockable','consuamble',"product"));
            $this->handleSearchBetween($qb,$data);
            $qb->orderBy('e.name','ASC');
            $result = $qb->getQuery();
            return  $result;
    }

    public function updateRemoveStockQuantity(Particular $stock , $fieldName=''){

        $em = $this->_em;
        if($fieldName == 'sales'){
            $qnt = $em->getRepository('RestaurantBundle:InvoiceParticular')->salesStockItemUpdate($stock);
            $stock->setSalesQuantity($qnt);
        }elseif($fieldName == 'purchase-return'){
           // $qnt = $em->getRepository('BusinessBundle:BusinessPurchaseReturnItem')->purchaseReturnStockUpdate($stock);
          // $stock->setPurchaseReturnQuantity($qnt);
        }elseif($fieldName == 'damage'){
             $quantity = $em->getRepository('RestaurantBundle:RestaurantDamage')->damageStockItemUpdate($stock);
            $stock->setDamageQuantity($quantity);
        }elseif($fieldName == 'production'){
            $quantity = $em->getRepository('RestaurantBundle:ProductionExpense')->productionExpenseStockItemUpdate($stock);
            $stock->setProductionQuantity($quantity);
        }elseif($fieldName == 'production-in'){
            $quantity = $em->getRepository('RestaurantBundle:ProductionBatch')->productionBatchItemUpdate($stock);
            $stock->setPurchaseQuantity($quantity);
        }else{
            $qnt = $em->getRepository('RestaurantBundle:PurchaseItem')->purchaseStockItemUpdate($stock);
            $stock->setPurchaseQuantity($qnt);
        }
        $em->persist($stock);
        $em->flush();
        $this->remainingQnt($stock);
    }

    public function remainingQnt(Particular $stock)
    {
        $em = $this->_em;
        $qnt = ($stock->getOpeningQuantity() + $stock->getPurchaseQuantity()) - ($stock->getSalesQuantity() + $stock->getDamageQuantity() + $stock->getPurchaseReturnQuantity() + $stock->getProductionQuantity());
        $stock->setRemainingQuantity($qnt);
        $em->persist($stock);
        $em->flush();
    }

    public function getPurchaseUpdateQnt(Purchase $purchase){

        $em = $this->_em;

        /** @var PurchaseItem $purchaseItem */

        foreach($purchase->getPurchaseItems() as $purchaseItem ){

            /** @var Particular  $particular */

            $particular = $purchaseItem->getParticular();
            $this->updateRemoveStockQuantity($particular);
            $particular->setPurchasePrice($purchaseItem->getPurchasePrice());
            $em->persist($particular);
            $em->flush();
            if($purchase->getRestaurantConfig()->isProduction() == 1 ) {
                $this->updateProductionElementPrice($particular, $purchaseItem->getPurchasePrice());
            }
        }
        if($purchase->getRestaurantConfig()->isProduction() == 1 ) {
            $this->updateProductionPrice($purchase->getRestaurantConfig()->getId());
        }
    }

    public function getDamageQnt(RestaurantDamage $damage){

        $em = $this->_em;

        /** @var Particular  $particular */

        $particular = $damage->getParticular();
        $this->updateRemoveStockQuantity($particular,'damage');
        $em->persist($particular);
        $em->flush();

    }


    public function insertAccessories(Invoice $invoice){

        $em = $this->_em;

        $invoiceParticulars = $em->getRepository('RestaurantBundle:InvoiceParticular')->findBy(array('invoice'=> $invoice ->getId()));

        /** @var InvoiceParticular $item */

        if($invoiceParticulars){

            foreach($invoiceParticulars as $item ){

                /** @var Particular  $particular */
                $particular = $item->getParticular();
                if( $particular->getService()->getSlug() == 'stockable' ){
                    $this->updateRemoveStockQuantity($particular,'sales');
                }elseif ($particular->getService()->getSlug() == 'product' and $particular->getProductionType() == "pre-production"){
                    $this->updateRemoveStockQuantity($particular,'sales');
                }elseif ($particular->getService()->getSlug() == 'product' and $particular->getProductionType() == "post-production"){
                    $em->getRepository('RestaurantBundle:ProductionExpense')->salesProductionElementExpense($item);
                }
            }
        }
    }

    public function getSalesUpdateQnt(Invoice $invoice){

        $em = $this->_em;

        /** @var InvoiceParticular $item */

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


    public function groupServiceBy(){

        $pass2 = array();
        $qb = $this->createQueryBuilder('e');
        $qb->where('e.restaurantConfig = :config')->setParameter('config', 1) ;
        $qb->andWhere('e.service IN(:service)')
            ->setParameter('service',array_values(array(1,2,3,4)));
        $qb->orderBy('e.name','ASC');
        $data = $qb->getQuery()->getResult();

        foreach ($data as $parent => $children){

            foreach($children as $child => $none){
                $pass2[$parent][$child] = true;
                $pass2[$child][$parent] = true;
            }
        }

    }


    public function getParticularOptionGroup(RestaurantConfig $config)
    {

        $qb = $this->createQueryBuilder('p');
        $qb->select('p, c');
        $qb->leftJoin('p.service', 's');
        $qb->leftJoin('p.category', 'c');
        $qb->orderBy('c.name', 'ASC')->addOrderBy('p.name', 'ASC');
        $qb->andWhere('s.slug IN(:slugs)')->setParameter('slugs',array_values(array('product','stockable')));
        $products = $qb->getQuery()->execute();
        $choices = [];
        foreach ($products as $product) {
            $choices[$product->getCategory()->getName()][$product->getId()] =  $product->getName();
        }
        return $choices;

    }

    public function setParticularSorting($data)
    {
        $i = 1;
        $em = $this->_em;
        foreach ($data as $key => $value){
            $particular = $this->find($value);
            $particular->setSorting($i);
            $em->persist($particular);
            $em->flush();
            $i++;
        }
    }
    public function setProductSorting($data)
    {
        $i = 1;
        $em = $this->_em;
        foreach ($data as $key => $value){
            $sort = sprintf("%s", str_pad($i,3, '0', STR_PAD_LEFT));
            $particular = $this->findOneBy(array('status'=> 1,'id' => $value));
            $particular->setSorting($sort);
            $em->persist($particular);
            $em->flush();
            $i++;
        }
    }

    public function getApiRestaurantToken(GlobalOption $entity)
    {
        $config = $entity->getRestaurantConfig()->getId();
        $result = $this->createQueryBuilder('e')
            ->join("e.service",'s')
            ->select('e.id as id','e.name as name')
            ->where("e.status = 1")
            ->andWhere('s.slug IN (:slugs)')
            ->setParameter('slugs',array('token'))
            ->andWhere("e.restaurantConfig ={$config}")
            ->orderBy("e.name","ASC")
            ->getQuery()->getArrayResult();

        /* @var $row Particular */

        $data = array();

        foreach($result as $key => $row){

            $data[$key]['tokenId'] = (int) $row['id'];
            $data[$key]['tokenName'] = $row['name'];
        }
        return $data;

    }

    public function updateProductionElementPrice(Particular $entity ,$price)
    {

        $id = $entity->getId();
        $elem = "UPDATE `restaurant_production_element` as sub
SET sub.price = $price , sub.subTotal = (sub.quantity * $price)
WHERE sub.material_id =:material";
        $qb1 = $this->getEntityManager()->getConnection()->prepare($elem);
        $qb1->bindValue('material', $id);
        $qb1->execute();
    }

    public function updateProductionPrice($config)
    {

        $sql = "Update restaurant_particular as stock
inner join (
  select item_id, COALESCE(SUM(ele.subTotal),0) as productionElementAmount
  from restaurant_production_element as ele
  where ele.item_id is not NULL
  group by ele.item_id
) as pa on stock.id = pa.item_id
set stock.productionElementAmount = pa.productionElementAmount,
stock.purchasePrice = COALESCE((COALESCE(stock.productionElementAmount,0) + COALESCE(stock.valueAddedAmount,0)),0)
WHERE stock.restaurantConfig_id =:config";
        $qb = $this->getEntityManager()->getConnection()->prepare($sql);
        $qb->bindValue('config', $config);
        $qb->execute();

    }


}
