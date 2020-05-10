<?php
namespace Appstore\Bundle\InventoryBundle\Repository;
use Appstore\Bundle\AccountingBundle\Entity\Transaction;
use Appstore\Bundle\EcommerceBundle\Entity\Discount;
use Appstore\Bundle\InventoryBundle\Entity\Damage;
use Appstore\Bundle\InventoryBundle\Entity\GoodsItem;
use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Appstore\Bundle\InventoryBundle\Entity\ItemSize;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseReturn;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseVendorItem;
use Appstore\Bundle\InventoryBundle\Entity\Sales;
use Appstore\Bundle\InventoryBundle\Entity\SalesItem;
use Appstore\Bundle\InventoryBundle\Entity\SalesReturn;
use Doctrine\ORM\EntityRepository;


/**
 * GoodsItemRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class GoodsItemRepository extends EntityRepository
{

    public function initialInsertSubProduct(PurchaseVendorItem $reEntity)
    {

        $em = $this->_em;
        $goods = new GoodsItem();
        $goods->setSalesPrice($reEntity->getSalesPrice());
        $goods->setPurchasePrice($reEntity->getPurchasePrice());
        $goods->setQuantity($reEntity->getMasterQuantity());
        $goods->setPurchaseVendorItem($reEntity);
        if($reEntity->getSize()){
            $goods->setSize($reEntity->getSize());
        }
        if($reEntity->getItemColors()){
            $goods->setColors($reEntity->getItemColors());
        }
        if(!empty($reEntity->getProductUnit())){
            $goods->setProductUnit($reEntity->getProductUnit());
        }
        $goods->setMasterItem(1);
        $em->persist($goods);
        $em->flush($goods);
    }

    public function insertCopySubProduct(PurchaseVendorItem $entity , PurchaseVendorItem $copyEntity)
    {
        $em = $this->_em;
        $i=0;

        if(!empty($copyEntity->getGoodsItems())){

            /* @var GoodsItem $goodsItem */
            foreach ($copyEntity->getGoodsItems() as $goodsItem) {


                $goods = new GoodsItem();
                $goods->setSalesPrice($goodsItem->getSalesPrice());
                $goods->setPurchasePrice($goodsItem->getPurchasePrice());
                $goods->setQuantity($goodsItem->getQuantity());
                $goods->setPurchaseVendorItem($entity);
                if($goodsItem->getSize()){
                    $goods->setSize($goodsItem->getSize());
                }
                if($goodsItem->getColors()){
                    $goods->setColors($goodsItem->getColors());
                }
                if(!empty($goodsItem->getProductUnit())){
                    $goods->setProductUnit($goodsItem->getProductUnit());
                }
                $goods->setMasterItem($goodsItem->getMasterItem());
                $em->persist($goods);
                $em->flush($goods);

            }

        }
    }


    public function initialUpdateSubProduct(PurchaseVendorItem $reEntity)
    {

        $em = $this->_em;
        $goods = $this->_em->getRepository('InventoryBundle:GoodsItem')->findOneBy(array('purchaseVendorItem' => $reEntity, 'masterItem' => 1));
        if(empty($goods)){
            $this->initialInsertSubProduct($reEntity);
        }else{
            $goods->setSalesPrice($reEntity->getSalesPrice());
            $goods->setPurchasePrice($reEntity->getPurchasePrice());
            $goods->setQuantity($reEntity->getQuantity());
            if($reEntity->getSize()){
                $goods->setSize($reEntity->getSize());
            }elseif(empty($reEntity->getSize()) and !empty($goods->getSize())){
                $goods->setSize(null);
            }
            if($reEntity->getItemColors()){
                $goods->setColors($reEntity->getItemColors());
            }
            if(!empty($reEntity->getProductUnit())){
                $goods->setProductUnit($reEntity->getProductUnit());
            }
            $goods->setPurchaseVendorItem($reEntity);
            $em->persist($goods);
            $em->flush();
        }
    }

     public function insertSubProduct(PurchaseVendorItem $reEntity,$data)
    {

        $em = $this->_em;
        $i=0;

        if($reEntity->getSubProduct() == 1 and isset($data['salesPrice']) ) {

            $colors = !empty($data['colors']) ? $data['colors'] :'';
            $sizeId = isset($data['size']) ? $data['size'] :'';
            $unitId = isset($data['unit']) ? $data['unit'] :'';
            $quantity = isset($data['quantity']) ? $data['quantity'] :1;
            $purchasePrice = isset($data['purchasePrice']) ? $data['purchasePrice'] :1;
            $salesPrice = isset($data['salesPrice']) ? $data['salesPrice'] :1;


            if (isset($data['salesPrice']) and !empty($data['salesPrice']) ) {

                //$updateSubProduct = array('size' => ,'colors'=> $colors , 'quantity' => $quantity,'purchasePrice' => $purchasePrice,'salesPrice' => $salesPrice);
                $goods = new GoodsItem();
                $goods->setSalesPrice($salesPrice);
                $goods->setPurchasePrice($purchasePrice);
                $goods->setQuantity($quantity);
                if($colors != 'null' and !empty($colors)){
                    $colorIds = explode(',',$colors);
                    foreach ($colorIds as $color ){
                        $colorObj[] = $this->_em->getRepository('InventoryBundle:ItemColor')->find($color);
                    }
                    $goods->setColors($colorObj);
                }
                if(isset($sizeId) and !empty($sizeId)){
                    $size = $this->_em->getRepository('InventoryBundle:ItemSize')->find($sizeId);
                    $goods->setSize($size);
                }
                if(isset($unitId) and !empty($unitId)){
                    $unit = $this->_em->getRepository('SettingToolBundle:ProductUnit')->find($unitId);
                    $goods->setProductUnit($unit);
                }
                $goods->setPurchaseVendorItem($reEntity);
                $em->persist($goods);
                $em->flush();

            }


        }

    }

    public function updateSubProduct(GoodsItem $goods,$data)
    {
        $em = $this->_em;
        $colors         = isset($data['colors']) ? $data['colors'] :'';
        $sizeId         = isset($data['size']) ? $data['size'] :'';
        $quantity       = isset($data['quantity']) ? $data['quantity'] :1;
        $purchasePrice  = isset($data['purchasePrice']) ? $data['purchasePrice'] :'';
        $salesPrice     = isset($data['salesPrice']) ? $data['salesPrice'] :'';

        if (isset($data['salesPrice']) and !empty($data['salesPrice']) ) {

            $goods->setSalesPrice($salesPrice);
            $goods->setPurchasePrice($purchasePrice);
            $goods->setQuantity($quantity);
            if(isset($colors) and !empty($colors)){

                $colorIds = explode(',',$colors);
                foreach ($colorIds as $color ){
                    $colorObj[] = $this->_em->getRepository('InventoryBundle:ItemColor')->findOneBy(array('inventoryConfig' => $goods->getPurchaseVendorItem()->getInventoryConfig(),'id'=> $color));
                }
                $goods->setColors($colorObj);
            }
            if(isset($sizeId) and !empty($sizeId)){
                $size = $this->_em->getRepository('InventoryBundle:ItemSize')->findOneBy(array('inventoryConfig' => $goods->getPurchaseVendorItem()->getInventoryConfig(),'id'=> $sizeId));
                $goods->setSize($size);
            }
            $em->flush();

        }
    }

    public function updateItemGoods(GoodsItem $goods,$updateSubProduct)
    {
        $em = $this->_em;
        $goods->setSalesPrice($updateSubProduct['salesPrice']);
        $goods->setPurchasePrice($updateSubProduct['purchasePrice']);
        $goods->setQuantity($updateSubProduct['quantity']);
        if(isset($updateSubProduct['size']) and !empty($updateSubProduct['size']) ){
            $size = $this->_em->getRepository('InventoryBundle:ItemSize')->findOneBy(array('inventoryConfig' => $goods->getPurchaseVendorItem()->getInventoryConfig(),'id'=> $updateSubProduct['size']));
            $goods->setSize($size);
            $goods->setName(null);
        }else{
            $goods->setName($updateSubProduct['name']);
        }
        if(isset($updateSubProduct['colors']) and !empty($updateSubProduct['colors'])) {
            foreach ($updateSubProduct['colors'] as $color){
                $colors[] = $this->_em->getRepository('InventoryBundle:ItemColor')->findOneBy(array('inventoryConfig' => $goods->getPurchaseVendorItem()->getInventoryConfig(), 'id' => $color));
            }
            $goods->setColors($colors);

        }
        $em->flush();
    }

    public function subItemDiscountPrice(PurchaseVendorItem $entity ,Discount $discount)
    {
        $em = $this->_em;
        /** @var GoodsItem $item */
        foreach( $entity->getGoodsItems() as $item){
            $discountPrice = $this->getCulculationDiscountPrice($item,$discount);
            $item->setDiscountPrice($discountPrice);
            $em->persist($item);
            $em->flush();
        }

    }

    public function updateEcommerceItem(Sales $entity ,$calculation ='minus')
    {
        foreach ($entity->getSalesItems() as $row){
            $purchaseVendorItem = $row->getPurchaseItem()->getPurchaseVendorItem();
            if($purchaseVendorItem->getIsWeb() == 1 ){
                $qnt = $row->getQuantity();
                if(!empty($row->getItem()->getSize())){
                    $size = $row->getItem()->getSize();
                    $this->webItemQuantityUpdate($calculation , $purchaseVendorItem , $size , $qnt);
                }else{
                    $this->webItemQuantityUpdate($calculation , $purchaseVendorItem , $size = 0 , $qnt);
                }
            }
        }
    }


    public function webItemQuantityUpdate($calculation, PurchaseVendorItem $purchaseVendorItem , $size, $qnt)
    {
        $em = $this->_em;

        /** @var GoodsItem $item */

        $subItemWithSize = $em->getRepository('InventoryBundle:GoodsItem')->findOneBy(array('purchaseVendorItem' => $purchaseVendorItem,'size' => $size));
        $subItem = $em->getRepository('InventoryBundle:GoodsItem')->findOneBy(array('purchaseVendorItem' => $purchaseVendorItem));
        if(!empty($subItemWithSize)){
            $subGood = $subItemWithSize;
        }elseif(!empty($subItem)){
            $subGood = $subItem;
        }

        if(!empty($subGood)){
            if($calculation == 'minus'){
                $subItem->setQuantity($subGood->getQuantity() - $qnt);
            }else{
                $subItem->setQuantity($subGood->getQuantity() + $qnt);
            }
            $subProductQuantity = $purchaseVendorItem->getSubProductQuantity();
            $purchaseVendorItem->setMasterQuantity($subProductQuantity);
            $em->flush();
        }


    }


    public function updateInventorySalesReturnItem(SalesReturn $entity ,$calculation ='plus')
    {
        foreach ($entity->getSalesReturnItems() as $row){
            $purchaseVendorItem = $row->getSalesItem()->getPurchaseItem()->getPurchaseVendorItem();
            if($purchaseVendorItem->getIsWeb() == 1 ){
                $qnt = $row->getQuantity();
                if(!empty($size = $row->getSalesItem()->getItem()->getSize())){
                    $size = $row->getSalesItem()->getItem()->getSize();
                    $this->webItemQuantityUpdate($calculation , $purchaseVendorItem , $size , $qnt);
                }else{
                    $this->webItemQuantityUpdate($calculation , $purchaseVendorItem , $size = 0 , $qnt);
                }
            }
        }
    }

    public function updateInventoryPurchaseReturnItem(PurchaseReturn $entity,$calculation ='minus')
    {

        foreach($entity->getPurchaseReturnItems() as $row ){

            $purchaseVendorItem = $row->getPurchaseItem()->getPurchaseVendorItem();
            if($purchaseVendorItem->getIsWeb() == 1 ){
                $qnt = $row->getQuantity();
                if(!empty($size = $row->getPurchaseItem()->getItem()->getSize())){
                    $size = $row->getPurchaseItem()->getItem()->getSize();
                    $this->webItemQuantityUpdate($calculation , $purchaseVendorItem , $size , $qnt);
                }else{
                    $this->webItemQuantityUpdate($calculation , $purchaseVendorItem , $size = 0 , $qnt);
                }
            }
        }

    }

    public function insertInventoryDamageItem(Damage $entity,$calculation ='minus')
    {
            $purchaseVendorItem = $entity->getPurchaseItem()->getPurchaseVendorItem();
            if($purchaseVendorItem->getIsWeb() == 1 ){
                $qnt = $entity->getQuantity();
                if(!empty($size = $entity->getPurchaseItem()->getItem()->getSize())){
                    $size = $entity->getPurchaseItem()->getItem()->getSize();
                    $this->webItemQuantityUpdate($calculation , $purchaseVendorItem , $size , $qnt);
                }else{
                    $this->webItemQuantityUpdate($calculation , $purchaseVendorItem , $size = 0 , $qnt);
                }
            }

    }

    public function insertInventorySubProduct(PurchaseVendorItem $entity)
    {

        if($entity->getSubProduct() == true){
            $this->updateProductToEcommerce($entity);
        }else{
            $this->insertProductToEcommerce($entity);
        }
    }

    public function insertProductToEcommerce(PurchaseVendorItem $entity){

        $em = $this->_em;
        $rows=array();
        $i = 0;

        foreach($entity->getPurchaseItems() as $item ) {

            if (!empty($item->getItem()->getSize())){

                if (!isset($rows[$item->getItem()->getSize()->getId()]['color'])) {
                    $rows[$item->getItem()->getSize()->getId()]['color'] = array();
                }
                if (!empty($item->getItem()->getColor())){
                    $rows[$item->getItem()->getSize()->getId()]['color'][$item->getItem()->getColor()->getId()] = $item->getItem()->getColor()->getId();
                }

                if (!isset($rows[$item->getItem()->getSize()->getId()]['quantity'])) {
                    $rows[$item->getItem()->getSize()->getId()]['quantity'] = 0;
                }

                $rows[$item->getItem()->getSize()->getId()]['quantity'] += $item->getStockItemQuantity();
                $rows[$item->getItem()->getSize()->getId()]['purchasePrice'] = $item->getPurchasePrice();
                $rows[$item->getItem()->getSize()->getId()]['salesPrice'] = $item->getSalesPrice();
            }
        }

        if(!empty($rows)){

            foreach($rows as $size => $row )
            {
                $colors = $row['color'];
                $goods = new GoodsItem();
                $goods->setSalesPrice($row['salesPrice']);
                $goods->setPurchasePrice($row['purchasePrice']);
                $goods->setQuantity($row['quantity']);

                $sizeObj = $this->_em->getRepository('InventoryBundle:ItemSize')->find($size);
                $goods->setSize($sizeObj);

                $colorObjs =array();
                foreach ($colors as $color){
                    $colorObjs[] = $this->_em->getRepository('InventoryBundle:ItemColor')->findOneBy(array('id'=> $color));
                }
                if(!empty($colorObjs) && is_array($colorObjs)){
                    $goods->setColors($colorObjs);
                }
                $goods->setPurchaseVendorItem($entity);
                if($i == 0){
                    $entity->setMasterQuantity($row['quantity']);
                    $entity->setSize($sizeObj);
                    if(!empty($colorObjs) && is_array($colorObjs)){
                        $entity->setItemColors($colorObjs);
                    }
                    $goods->setMasterItem(1);
                }
                $em->persist($goods);
                $em->flush($goods);
                $i ++ ;

            }

        }else{

            $qunt=0;
            $color = array();
            foreach($entity->getPurchaseItems() as $item ) {
                $qunt += $item->getStockItemQuantity();
            }
            $goods = new GoodsItem();
            $goods->setSalesPrice($entity->getSalesPrice());
            $goods->setPurchasePrice($entity->getPurchasePrice());
            $goods->setQuantity($qunt);
            $goods->setPurchaseVendorItem($entity);
            $goods->setMasterItem(1);
            if($i == 0) {
                $entity->setMasterQuantity($qunt);
            }
            $em->persist($goods);
            $em->flush($goods);
        }
    }

    public function updateProductToEcommerce(PurchaseVendorItem $entity){

        $em = $this->_em;
        $rows=array();
        $i = 0;
        foreach($entity->getPurchaseItems() as $item ) {

            if (!empty($item->getItem()->getSize())){

                if (!isset($rows[$item->getItem()->getSize()->getId()]['color'])) {
                    $rows[$item->getItem()->getSize()->getId()]['color'] = array();
                }
                if (!empty($item->getItem()->getColor())){
                    $rows[$item->getItem()->getSize()->getId()]['color'][$item->getItem()->getColor()->getId()] = $item->getItem()->getColor()->getId();
                }

                if (!isset($rows[$item->getItem()->getSize()->getId()]['quantity'])) {
                    $rows[$item->getItem()->getSize()->getId()]['quantity'] = 0;
                }

                $rows[$item->getItem()->getSize()->getId()]['quantity'] += $item->getStockItemQuantity();
                $rows[$item->getItem()->getSize()->getId()]['purchasePrice'] = $item->getPurchasePrice();
                $rows[$item->getItem()->getSize()->getId()]['salesPrice'] = $item->getSalesPrice();
            }
        }

        if(!empty($rows)){

            foreach($rows as $size => $row )
            {
                $colors = $row['color'];
                $goods = $this->_em->getRepository('InventoryBundle:GoodsItem')->findOneBy(array('purchaseVendorItem' => $entity->getId(),'size' => $size));
                $goods->setSalesPrice($row['salesPrice']);
                $goods->setPurchasePrice($row['purchasePrice']);
                $goods->setQuantity($row['quantity']);

                $sizeObj = $this->_em->getRepository('InventoryBundle:ItemSize')->find($size);
                $goods->setSize($sizeObj);

                $colorObjs =array();
                foreach ($colors as $color){
                    $colorObjs[] = $this->_em->getRepository('InventoryBundle:ItemColor')->findOneBy(array('id'=> $color));
                }
                if(!empty($colorObjs) && is_array($colorObjs)){
                    $goods->setColors($colorObjs);
                }
                $goods->setPurchaseVendorItem($entity);
                if($i == 0){
                    $entity->setMasterQuantity($row['quantity']);
                    $entity->setSize($sizeObj);
                    if(!empty($colorObjs) && is_array($colorObjs)){
                        $entity->setItemColors($colorObjs);
                    }
                    $goods->setMasterItem(1);
                }
                $em->persist($goods);
                $em->flush($goods);
                $i ++ ;

            }
        }else{

            $qunt=0;
            $color = array();
            foreach($entity->getPurchaseItems() as $item ) {
                $qunt += $item->getStockItemQuantity();
            }
            $goods = $this->_em->getRepository('InventoryBundle:GoodsItem')->findOneBy(array('purchaseVendorItem' => $entity->getId(),'masterItem'=>1));
            $goods->setSalesPrice($entity->getSalesPrice());
            $goods->setPurchasePrice($entity->getPurchasePrice());
            $goods->setQuantity($qunt);
            $goods->setPurchaseVendorItem($entity);
            $goods->setMasterItem(1);
            if($i == 0) {
                $entity->setMasterQuantity($qunt);
            }
            $em->persist($goods);
            $em->flush($goods);
        }
    }


    public function getCulculationDiscountPrice(GoodsItem $purchase , Discount $discount)
    {
        if($discount->getType() == 'percentage'){
            $price = (($purchase->getSalesPrice() * (int)$discount->getDiscountAmount())/100 );
            $discountPrice = $purchase->getSalesPrice() - $price;
        }else{
            $discountPrice = ( $purchase->getSalesPrice() - $discount->getDiscountAmount());
        }
        return $discountPrice;

    }

    public function ecommerceItemReverse(Sales $entity , $calculation ='')
    {
        /* @var SalesItem $item */

        foreach ($entity->getSalesItems() as $row){

            $purchaseVendorItem = $row->getPurchaseItem()->getPurchaseVendorItem();
            if($purchaseVendorItem->getIsWeb() == 1 ){
                $qnt = $row->getQuantity();
                if(!empty($row->getItem()->getSize())){
                    $size = $row->getItem()->getSize();
                    $this->webItemQuantityUpdate($calculation , $purchaseVendorItem , $size , $qnt);
                }else{
                    $this->webItemQuantityUpdate($calculation , $purchaseVendorItem , $size = 0 , $qnt);
                }
            }
        }
    }

    public function findGroupBrands(InventoryConfig $config , $array = array())
    {

        $brands =  isset($array['brand']) ? $array['brand'] : array();

        $qb = $this->_em->createQueryBuilder();
        $qb->from('InventoryBundle:PurchaseVendorItem','e');
        $qb->join('e.brand','brand');
        $qb->select('brand.id as id');
        $qb->addSelect('brand.name as name');
        $qb->where('e.inventoryConfig='.$config->getId());
        $qb->groupBy('brand.id');
        $qb->orderBy('brand.name', 'ASC');
        $res = $qb->getQuery()->getArrayResult();

        $value ='';
        $value .='<ul class="ul-check-list-brand">';
        foreach ($res as $key => $val) {
            $checkd = in_array($val['id'], $brands) ? 'checked':'';
            $value .= '<li><input type="checkbox" class="checkbox" '.$checkd.' name="brand[]" value="'.$val['slug'].'" ><span class="label" >'.$val['name']. '</span></li>';
        }
        $value .='</ul>';
        return $value;

    }

    public function findGroupColors(InventoryConfig $config , $array = array())
    {

        $colors = isset($array['color']) ? $array['color'] :$array;

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.purchaseVendorItem','purchasevendoritem');
        $qb->join('e.colors','colors');
        $qb->select('colors.id as id');
        $qb->addSelect('colors.name as name');
        $qb->where('purchasevendoritem.inventoryConfig='.$config->getId());
        $qb->groupBy('colors.id');
        $qb->orderBy('colors.name', 'ASC');
        $res = $qb->getQuery()->getArrayResult();

        $value ='';
        $value .='<ul class="ul-check-list">';
        foreach ($res as $key => $val) {
            $checkd = in_array($val['id'], $colors ) ? 'checked':'';
            $value .= '<li><input type="checkbox" class="checkbox" '.$checkd.' name="color[]" value="'.$val['id'].'" ><span class="label">'.$val['name']. '</span></li>';
        }
        $value .='</ul>';
        return $value;
    }

    public function findGroupSizes(InventoryConfig $config , $array = array())
    {
        $sizes = isset($array['size'])  ? $array['size'] :$array;
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.purchaseVendorItem','purchasevendoritem');
        $qb->join('e.size','size');
        $qb->select('size.id as id');
        $qb->addSelect('size.name as name');
        $qb->where('purchasevendoritem.inventoryConfig='.$config->getId());
        $qb->groupBy('size.id');
        $qb->orderBy('size.name', 'ASC');
        $res = $qb->getQuery()->getArrayResult();

        $value ='';
        $value .='<ul class="ul-check-list">';
            foreach ($res as $key => $val) {
                $checkd = in_array($val['id'], $sizes )? 'checked':'';
                $value .= '<li><input type="checkbox" class="checkbox" '.$checkd.' name="size[]" value="'.$val['id'].'" ><span class="label">'.$val['name']. '</span></li>';
            }
        $value .='</ul>';
        return $value;
    }

    public function findGroupDiscount($config,  $array = array())
    {
        $discounts = isset($array['discount']) ? $array['discount'] :array();
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.purchaseVendorItem','purchasevendoritem');
        $qb->join('purchasevendoritem.discount','discount');
        $qb->select('discount.id as id');
        $qb->addSelect('discount.name as name');
        $qb->where('purchasevendoritem.inventoryConfig='.$config->getId());
        $qb->groupBy('discount.id');
        $qb->orderBy('discount.name', 'ASC');
        $res = $qb->getQuery()->getArrayResult();

        $value ='';
        $value .='<ul class="ul-check-list">';
        foreach ($res as $key => $val) {
            $checkd = in_array($val['id'], $discounts )? 'checked':'';
            $value .= '<li><input type="checkbox" class="checkbox" '.$checkd.' name="discount[]" value="'.$val['id'].'" ><span class="label">'.$val['name']. '</span></li>';
        }
        $value .='</ul>';
        return $value;

    }

    public function findPromotionTree($config , $array = array())
    {
        $promotions = isset($array['promotion']) ? $array['promotion'] : array();
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.purchaseVendorItem','purchasevendoritem');
        $qb->join('purchasevendoritem.promotion','promotion');
        $qb->select('promotion.id as id');
        $qb->addSelect('promotion.name as name');
        $qb->where('purchasevendoritem.inventoryConfig='.$config->getId());
        $qb->groupBy('promotion.id');
        $qb->orderBy('promotion.name', 'ASC');
        $res = $qb->getQuery()->getArrayResult();

        $value ='';
        $value .='<ul class="ul-check-list">';
        foreach ($res as $key => $val) {
            $checkd = in_array($val['id'], $promotions )? 'checked':'';
            $value .= '<li><input type="checkbox" class="checkbox" '.$checkd.' name="promotion[]" value="'.$val['id'].'" ><span class="label">'.$val['name']. '</span></li>';
        }
        $value .='</ul>';
        return $value;
    }

    public function findTagTree($config , $array = array())
    {
        $tags = isset($array['tag']) ? $array['tag'] : array();
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.purchaseVendorItem','purchasevendoritem');
        $qb->join('purchasevendoritem.itemTags','tag');
        $qb->select('tag.id as id');
        $qb->addSelect('tag.name as name');
        $qb->where('purchasevendoritem.inventoryConfig='.$config->getId());
        $qb->groupBy('tag.id');
        $qb->orderBy('tag.name', 'ASC');
        $res = $qb->getQuery()->getArrayResult();

        $value ='';
        $value .='<ul class="ul-check-list">';
        foreach ($res as $key => $val) {
            $checkd = in_array($val['id'], $tags )? 'checked':'';
            $value .= '<li><input type="checkbox" class="checkbox" '.$checkd.' name="tag[]" value="'.$val['id'].'" ><span class="label">'.$val['name']. '</span></li>';
        }
        $value .='</ul>';
        return $value;
    }




}
