<?php

namespace Appstore\Bundle\InventoryBundle\Repository;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseItem;
use Appstore\Bundle\InventoryBundle\Entity\SalesItem;
use Doctrine\ORM\EntityRepository;

/**
 * SalesItemRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SalesItemRepository extends EntityRepository
{

    public function checkPurchaseQuantity(PurchaseItem $purchaseItem)
    {

        $qb = $this->createQueryBuilder('salesItem');
        $qb->addSelect('SUM(salesItem.quantity) as quantity ');
        $qb->where("salesItem.purchaseItem = :purchaseItem");
        $qb->setParameter('purchaseItem', $purchaseItem->getId());
        $quantity =  $qb->getQuery()->getSingleResult();
        if ($purchaseItem->getQuantity() == $quantity['quantity']){
            return false;
        }else{
            return true;
        }

    }

    public function insertSalesItems($sales,$purchaseItem)
    {
        $em = $this->_em;
        $existEntity = $this->findOneBy(array('sales'=> $sales,'purchaseItem'=> $purchaseItem));

        if(!empty($existEntity)){
            $qnt = ($existEntity->getQuantity()+1);
            $existEntity->setQuantity($qnt);
            if($existEntity ->isCustomPrice() == 1){
                $existEntity->setSubTotal($existEntity->getSalesPrice() * $qnt);
            }else{
                $existEntity->setSubTotal($purchaseItem->getSalesPrice() * $qnt);
            }
            $em->persist($existEntity);
        }else{
            $entity = new SalesItem();
            $entity->setSales($sales);
            $entity->setPurchaseItem($purchaseItem);
            $entity->setItem($purchaseItem->getItem());
            $entity->setPurchasePrice($purchaseItem->getPurchasePrice());
            $entity->setSalesPrice($purchaseItem->getSalesPrice());
            $entity->setEstimatePrice($purchaseItem->getSalesPrice());
            $entity->setQuantity(1);
            $entity->setSubTotal($purchaseItem->getSalesPrice());
            $em->persist($entity);
        }
        $em->flush();
    }

    public function getSalesItems($sales)
    {
        $entities = $sales->getSalesItems();
        $data = '';
        $i = 1;
        foreach( $entities as $entity){
            if ($entity->isCustomPrice() == 1 and $entity->getSalesPrice() != $entity->getEstimatePrice()){
                $checked = 'checked="checked"';
            } else{
                $checked = '';
            }
             if ($entity->isCustomPrice() != 1 and $entity->getSalesPrice() == $entity->getEstimatePrice()){
                 $readonly = 'readonly="readonly"';
            } else{
                 $readonly = '';
            }

            $color ='';
            $size ='';

            $masterItem = $entity->getItem()->getMasterItem()->getName();
            if(!empty($entity->getItem()->getColor())){
                $color = '-'.$entity->getItem()->getColor()->getName();
            }
            if(!empty($entity->getItem()->getSize())){
                $size = '-'.$entity->getItem()->getSize()->getName();
            }
            $itemName = $masterItem.$color.$size;

            $data .=' <tr id="remove-'.$entity->getId().'">';
            $data .='<td class="numeric" >'.$i.'</td>';
            $data .='<td class="numeric" >'.$entity->getPurchaseItem()->getBarcode();
            $data .='</br><span>'.$itemName.'</span>';
            $data .='</td>';
            $data .='<td class="numeric" ><input type="text" name="quantity[]" rel="'.$entity->getId().'"  id="quantity-'.$entity->getId().'" class="m-wrap span10 quantity" value="'.$entity->getQuantity().'" placeholder="'.$entity->getItem()->getQuantity().'"></td>';
            $data .='<td class="" ><div class="input-prepend">';
            $data .='<span class="add-on">';
            $data .='<input type="hidden" name="estimatePrice" id="estimatePrice-'.$entity->getId().'" value="'.$entity->getEstimatePrice().'">';
            $data .='<input type="checkbox"  class="customPrice" value="1"  '. $checked .' rel="'.$entity->getId().'" id="customPrice-'.$entity->getId().'">';
            $data .='</span>';
            $data .='<input class="m-wrap span8 numeric salesPrice"  '.$readonly.' rel="'.$entity->getId().'" id="salesPrice-'.$entity->getId().'" type="text" name="salesPrice" value="'.$entity->getSalesPrice().'" placeholder="'.$entity->getEstimatePrice().'">';
            $data .'</div></td>';
            $data .='<td class="numeric" ><span id="subTotalShow-'. $entity->getId().'" >'.$entity->getSubTotal().'</td>';
            $data .='<td class="numeric" >
                     <a id="'.$entity->getId().'" title="Are you sure went to delete ?" rel="/inventory/sales/'.$entity->getSales()->getId().'/'.$entity->getId().'/delete" href="javascript:" class="btn red mini delete" ><i class="icon-trash"></i></a>
                     </td>';
            $data .='</tr>';
            $i++;
        }
        return $data;
    }


}
