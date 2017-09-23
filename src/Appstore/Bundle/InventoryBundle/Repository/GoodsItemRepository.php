<?php
namespace Appstore\Bundle\InventoryBundle\Repository;
use Appstore\Bundle\AccountingBundle\Entity\Transaction;
use Appstore\Bundle\EcommerceBundle\Entity\Discount;
use Appstore\Bundle\InventoryBundle\Entity\Damage;
use Appstore\Bundle\InventoryBundle\Entity\GoodsItem;
use Appstore\Bundle\InventoryBundle\Entity\ItemSize;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseReturn;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseVendorItem;
use Appstore\Bundle\InventoryBundle\Entity\Sales;
use Appstore\Bundle\InventoryBundle\Entity\SalesItem;
use Appstore\Bundle\InventoryBundle\Entity\SalesReturn;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\Null;

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
        if($reEntity->getSize()){
            $goods->setSize($reEntity->getSize());
        }
        if($reEntity->getItemColors()){
            $goods->setColors($reEntity->getItemColors());
        }
        $goods->setPurchaseVendorItem($reEntity);
        $goods->setMasterItem(1);
        $em->persist($goods);
        $em->flush();
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
            }
            if($reEntity->getItemColors()){
                $goods->setColors($reEntity->getItemColors());
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

            $colors = isset($data['colors']) ? $data['colors'] :'';
            $sizeId = isset($data['size']) ? $data['size'] :'';
            $quantity = isset($data['quantity']) ? $data['quantity'] :1;
            $purchasePrice = isset($data['purchasePrice']) ? $data['purchasePrice'] :1;
            $salesPrice = isset($data['salesPrice']) ? $data['salesPrice'] :1;

            if (isset($data['salesPrice']) and !empty($data['salesPrice']) ) {

                //$updateSubProduct = array('size' => ,'colors'=> $colors , 'quantity' => $quantity,'purchasePrice' => $purchasePrice,'salesPrice' => $salesPrice);
                $goods = new GoodsItem();
                $goods->setSalesPrice($salesPrice);
                $goods->setPurchasePrice($purchasePrice);
                $goods->setQuantity($quantity);
                if(isset($colors) and !empty($colors)){
                    $colorIds = explode(',',$colors);
                    foreach ($colorIds as $color ){
                        $colorObj[] = $this->_em->getRepository('InventoryBundle:ItemColor')->findOneBy(array('inventoryConfig' => $reEntity->getInventoryConfig(),'id'=> $color));
                    }
                    $goods->setColors($colorObj);
                }
                if(isset($sizeId) and !empty($sizeId)){
                    $size = $this->_em->getRepository('InventoryBundle:ItemSize')->findOneBy(array('inventoryConfig' => $reEntity->getInventoryConfig(),'id'=> $sizeId));
                    $goods->setSize($size);
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
            echo $discountPrice = $this->getCulculationDiscountPrice($item,$discount);
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




}
