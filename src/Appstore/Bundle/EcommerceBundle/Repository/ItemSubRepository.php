<?php
namespace Appstore\Bundle\EcommerceBundle\Repository;
use Appstore\Bundle\EcommerceBundle\Entity\Discount;
use Appstore\Bundle\EcommerceBundle\Entity\EcommerceConfig;
use Appstore\Bundle\EcommerceBundle\Entity\Item;
use Appstore\Bundle\EcommerceBundle\Entity\ItemSub;
use Doctrine\ORM\EntityRepository;


/**
 * GoodsItemRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ItemSubRepository extends EntityRepository
{

    public function initialInsertSubProduct(Item $reEntity)
    {

        $em = $this->_em;
        $goods = new ItemSub();
        $goods->setSalesPrice($reEntity->getSalesPrice());
        $goods->setPurchasePrice($reEntity->getPurchasePrice());
        $goods->setQuantity($reEntity->getMasterQuantity());
        $goods->setItem($reEntity);
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

    public function insertCopySubProduct(Item $entity , Item $copyEntity)
    {
        $em = $this->_em;
        $i=0;

        if(!empty($copyEntity->getItemSubs())){

            /* @var ItemSub $goodsItem */
            
            foreach ($copyEntity->getItemSubs() as $goodsItem) {
                $goods = new ItemSub();
                $goods->setSalesPrice($goodsItem->getSalesPrice());
                $goods->setPurchasePrice($goodsItem->getPurchasePrice());
                $goods->setQuantity($goodsItem->getQuantity());
                $goods->setItem($entity);
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


    public function initialUpdateSubProduct(Item $reEntity)
    {

        $em = $this->_em;
        $goods = $this->_em->getRepository('EcommerceBundle:ItemSub')->findOneBy(array('item' => $reEntity, 'masterItem' => 1));
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
            $goods->setitem($reEntity);
            $em->persist($goods);
            $em->flush();
        }
    }

     public function insertSubProduct(Item $reEntity,$data)
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
               
                $goods = new ItemSub();
                $goods->setSalesPrice($salesPrice);
                $goods->setPurchasePrice($purchasePrice);
                $goods->setQuantity($quantity);
                if($colors != 'null' and !empty($colors)){
                    $colorIds = explode(',',$colors);
                    foreach ($colorIds as $color ){
                        $colorObj[] = $this->_em->getRepository('SettingToolBundle:ProductColor')->find($color);
                    }
                    $goods->setColors($colorObj);
                }
                if(isset($sizeId) and !empty($sizeId)){
                    $size = $this->_em->getRepository('SettingToolBundle:ProductSize')->find($sizeId);
                    $goods->setSize($size);
                }
                if(isset($unitId) and !empty($unitId)){
                    $unit = $this->_em->getRepository('SettingToolBundle:ProductUnit')->find($unitId);
                    $goods->setProductUnit($unit);
                }
                $goods->setItem($reEntity);
                $em->persist($goods);
                $em->flush();

            }


        }

    }

    public function updateSubProduct(ItemSub $goods,$data)
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
                    $colorObj[] = $this->_em->getRepository('SettingToolBundle:ProductColor')->findOneBy(array('ecommerceConfig' => $goods->getitem()->getecommerceConfig(),'id'=> $color));
                }
                $goods->setColors($colorObj);
            }
            if(isset($sizeId) and !empty($sizeId)){
                $size = $this->_em->getRepository('SettingToolBundle:ProductSize')->findOneBy(array('ecommerceConfig' => $goods->getitem()->getecommerceConfig(),'id'=> $sizeId));
                $goods->setSize($size);
            }
            $em->flush();

        }
    }

    public function updateItemGoods(ItemSub $goods,$updateSubProduct)
    {
        $em = $this->_em;
        $goods->setSalesPrice($updateSubProduct['salesPrice']);
        $goods->setPurchasePrice($updateSubProduct['purchasePrice']);
        $goods->setQuantity($updateSubProduct['quantity']);
        if(isset($updateSubProduct['size']) and !empty($updateSubProduct['size']) ){
            $size = $this->_em->getRepository('SettingToolBundle:ProductSize')->findOneBy(array('id'=> $updateSubProduct['size']));
            $goods->setSize($size);
            $goods->setName(null);
        }else{
            $goods->setName($updateSubProduct['name']);
        }
        if(isset($updateSubProduct['colors']) and !empty($updateSubProduct['colors'])) {
            foreach ($updateSubProduct['colors'] as $color){
                $colors[] = $this->_em->getRepository('SettingToolBundle:ProductColor')->findOneBy(array('id' => $color));
            }
            $goods->setColors($colors);

        }
        $em->flush();
    }

    public function subItemDiscountPrice(Item $entity ,Discount $discount)
    {
        $em = $this->_em;
        /** @var ItemSub $item */
        foreach( $entity->getItemSubs() as $item){
            $discountPrice = $this->getCulculationDiscountPrice($entity,$discount);
            $item->setDiscountPrice($discountPrice);
            $em->persist($item);
            $em->flush();
        }

    }

    public function getCulculationDiscountPrice(Item $purchase , Discount $discount)
    {
        $discountPrice = "";
        if($discount->getType() == 'percentage' and $purchase->getSalesPrice() > $discount->getDiscountAmount() ){
            $price = ( ($purchase->getSalesPrice() * (int)$discount->getDiscountAmount())/100 );
            $discountPrice = $purchase->getSalesPrice() - $price;
        }elseif($purchase->getSalesPrice() > $discount->getDiscountAmount()){
            $discountPrice = ( $purchase->getSalesPrice() - (int)$discount->getDiscountAmount());
        }
        return $discountPrice;
    }



    public function findGroupBrands(EcommerceConfig $config , $array = array())
    {

        $brands =  isset($array['brand']) ? $array['brand'] : array();

        $qb = $this->_em->createQueryBuilder();
        $qb->from('EcommerceBundle:Item','e');
        $qb->join('e.brand','brand');
        $qb->select('brand.id as id');
        $qb->addSelect('brand.name as name');
        $qb->where('e.ecommerceConfig='.$config->getId());
        $qb->groupBy('brand.id');
        $qb->orderBy('brand.name', 'ASC');
        $res = $qb->getQuery()->getArrayResult();

        $value ='';
        $value .='<ul class="ul-check-list-brand">';
        foreach ($res as $key => $val) {
            $checkd = in_array($val['id'], $brands) ? 'checked':'';
            $value .= '<li><input type="checkbox" class="checkbox" '.$checkd.' name="brand[]" value="'.$val['id'].'" ><span class="label" >'.$val['name']. '</span></li>';
        }
        $value .='</ul>';
        return $value;

    }

    public function findGroupColors(ecommerceConfig $config , $array = array())
    {

        $colors = isset($array['color']) ? $array['color'] :$array;

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.item','item');
        $qb->join('e.colors','colors');
        $qb->select('colors.id as id');
        $qb->addSelect('colors.name as name');
        $qb->where('item.ecommerceConfig='.$config->getId());
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

    public function findGroupSizes(ecommerceConfig $config , $array = array())
    {
        $sizes = isset($array['size'])  ? $array['size'] :$array;
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.item','item');
        $qb->join('e.size','size');
        $qb->select('size.id as id');
        $qb->addSelect('size.name as name');
        $qb->where('item.ecommerceConfig='.$config->getId());
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
        $qb->join('e.item','item');
        $qb->join('item.discount','discount');
        $qb->select('discount.id as id');
        $qb->addSelect('discount.name as name');
        $qb->where('item.ecommerceConfig='.$config->getId());
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
        $qb->join('e.item','item');
        $qb->join('item.promotion','promotion');
        $qb->select('promotion.id as id');
        $qb->addSelect('promotion.name as name');
        $qb->where('item.ecommerceConfig='.$config->getId());
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
        $qb->join('e.item','item');
        $qb->join('item.itemTags','tag');
        $qb->select('tag.id as id');
        $qb->addSelect('tag.name as name');
        $qb->where('item.ecommerceConfig='.$config->getId());
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
