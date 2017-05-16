<?php

namespace Appstore\Bundle\InventoryBundle\Repository;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseItem;
use Appstore\Bundle\InventoryBundle\Entity\SalesItem;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * SalesItemRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SalesItemRepository extends EntityRepository
{


    public function salesItems($inventory,$data)
    {
        $item = isset($data['item'])? $data['item'] :'';

/*        $qb = $this->createQueryBuilder('salesItem');
        $qb->join('salesItem.item','item');
        $qb->join('salesItem.sales','sales');
        $qb->select('item.sku as sku');
        $qb->addSelect('item.name as name');
        $qb->addSelect('item.purchaseQuantity as purchaseQuantity');
        $qb->addSelect('item.salesQuantity as salesQuantity');
        $qb->addSelect('item.purchaseQuantityReturn as purchaseQuantityReturn');
        $qb->addSelect('item.salesQuantityReturn as salesQuantityReturn');
        $qb->addSelect('item.onlineOrderQuantity as onlineOrderQuantity');
        $qb->addSelect('item.onlineOrderQuantityReturn as onlineOrderQuantityReturn');
        $qb->addSelect('item.damageQuantity as damageQuantity');
        $qb->addSelect('SUM(salesItem.quantity) as salesOngoingQuantity ');
        $qb->where("sales.inventoryConfig = :inventoryConfig");
        $qb->setParameter('inventoryConfig',$inventory);
        $qb->andWhere('sales.process IN(:process)');
        $qb->setParameter('process',array_values(array('In-progress','Courier')));
        if (!empty($item)) {
            $qb->join('item.masterItem', 'm');
            $qb->andWhere("m.name = :name");
            $qb->setParameter('name', $item);
        }
        $qb->groupBy("salesItem.item");
        $result =  $qb->getQuery();
        return $result;*/

        $sql = "SELECT SUM(SalesItem.quantity) as salesOngoingQuantity,
                item.sku as sku,item.name as product,item.purchaseQuantity as purchaseQuantity,item.purchaseQuantityReturn as purchaseQuantityReturn,
                item.salesQuantityReturn as salesQuantityReturn,item.onlineOrderQuantityReturn as onlineOrderQuantityReturn,
                item.salesQuantity as salesQuantity,item.damageQuantity as damageQuantity,item.onlineOrderQuantity as onlineOrderQuantity
                FROM SalesItem
                INNER JOIN Sales ON SalesItem.sales_id = Sales.id
                INNER JOIN Item as item ON SalesItem.item_id = item.id
                WHERE Sales.inventoryConfig_id = :inventoryConfig AND Sales.process IN ('In-progress', 'Courier')
                GROUP BY item_id";
        $stmt = $this->getEntityManager()
            ->getConnection()
            ->prepare($sql);
        $stmt->bindValue('inventoryConfig', $inventory->getId());
        $stmt->execute();
        $result =  $stmt->fetchAll();
        return $result;

    }

    public function ongoingSalesQuantity($inventory , $data = array())
    {

        $qb = $this->createQueryBuilder('salesItem');
        $qb->join('salesItem.sales','sales');
        $qb->join('salesItem.purchaseItem','pi');
        $qb->select('SUM(salesItem.quantity) as ongoingQuantity ');
        $qb->addSelect('pi.id as id ');
        $qb->where("sales.inventoryConfig = :inventory");
        $qb->setParameter('inventory', $inventory);
        $qb->andWhere('sales.process IN(:process)');
        $qb->setParameter('process',array_values(array('In-progress','Courier')));
        if(!empty($data)){
        $qb->andWhere($qb->expr()->in("pi.id", $data ));
        }
        $qb->groupBy('salesItem.purchaseItem');
        $result =  $qb->getQuery()->getArrayResult();
        $data = array();
        foreach($result as $row) {
            $data[$row['id']] = $row['ongoingQuantity'];
        }
        return $data;
    }



    public function checkSalesQuantity(PurchaseItem $purchaseItem)
    {

        $qb = $this->createQueryBuilder('salesItem');
        $qb->join('salesItem.sales','sales');
        $qb->addSelect('SUM(salesItem.quantity) as quantity ');
        $qb->where("salesItem.purchaseItem = :purchaseItem");
        $qb->setParameter('purchaseItem', $purchaseItem->getId());
        $qb->andWhere('sales.process IN(:process)');
        $qb->setParameter('process',array_values(array('In-progress','Courier')));
        $quantity =  $qb->getQuery()->getOneOrNullResult();
        if(!empty($quantity['quantity'])){
            return $quantity['quantity'];
        }else{
            return 0;
        }

    }

    public function insertSalesManualItems($sales,$data)
    {
        $em = $this->_em;
        $i = 0;
        var_dump($data);
        foreach ($data['item'] as $row){

            $purchaseItem = $em->getRepository('InventoryBundle:PurchaseItem')->find($row);

            $entity = new SalesItem();
            $entity->setSales($sales);
            $entity->setPurchaseItem($purchaseItem);
            $entity->setItem($purchaseItem->getItem());
            $entity->setPurchasePrice($purchaseItem->getPurchasePrice());
            $entity->setEstimatePrice($purchaseItem->getSalesPrice());
            $entity->setSalesPrice($data['salesPrice'][$i]);
            $entity->setQuantity($data['quantity'][$i]);
            $entity->setSubTotal($data['salesPrice'][$i] * $data['quantity'][$i]);
            $em->persist($entity);

            $i++;
        }
        $em->flush();
    }


    public function insertSalesItems($sales,$purchaseItem,$customPrice = 0 )
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
            $entity->setCustomPrice($customPrice);
            $entity->setSubTotal($purchaseItem->getSalesPrice());
            $em->persist($entity);
        }
        $em->flush();
    }

    public function getSalesItems($sales , $device = '' )
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
            if (!empty($entity->getItem()->getMasterItem()) and !empty($entity->getItem()->getMasterItem()->getProductUnit())){
                $unit = '-'.$entity->getItem()->getMasterItem()->getProductUnit()->getName();
            } else{
                $unit = '';
            }

            $itemName = $entity->getItem()->getName();
            if($device == 'mobile'){

                $data .=' <tr id="remove-'.$entity->getId().'">';
                $data .='<td>'.$i.'</td>';
                $data .='<td>'.$itemName.'</td>';
                $data .='<td>'.$entity->getQuantity().$unit.'</td>';
                $data .='<td>'.$entity->getSalesPrice().'</td>';
                $data .='<td>'.$entity->getSubTotal().'</td>';
                $data .='<td class="" >
                     <a id="'.$entity->getId().'" title="Are you sure went to delete ?" rel="/inventory/sales/'.$entity->getSales()->getId().'/'.$entity->getId().'/delete" href="javascript:" class="btn red mini delete" ><i class="icon-trash"></i></a>
                     </td>';
                $data .='</tr>';

            }else{

                $data .=' <tr id="remove-'.$entity->getId().'">';
                $data .='<td class="numeric" >'.$i.'</td>';
                $data .='<td class="numeric" >'.$entity->getPurchaseItem()->getBarcode();
                $data .='</br><span>'.$itemName.'</span>';
                $data .='</td>';
                $data .='<td class=" span3" ><div class="input-append">';
                $data .='<input type="text" name="quantity[]" rel="'.$entity->getId().'"  id="quantity-'.$entity->getId().'" class="m-wrap span6 quantity" value="'.$entity->getQuantity().'" min="1" max="'.$entity->getPurchaseItem()->getQuantity().'" placeholder="'.$entity->getPurchaseItem()->getQuantity().'">';
                $data .='<span class="add-on">'.$unit.'</span>';
                $data .='</div></td>';
                $data .='<td class=" span3" ><div class="input-prepend">';
                $data .='<span class="add-on">';
                $data .='<input type="hidden" name="estimatePrice" id="estimatePrice-'.$entity->getId().'" value="'.$entity->getEstimatePrice().'">';
                $data .='<input type="checkbox"  class="customPrice" value="1"  '. $checked .' rel="'.$entity->getId().'" id="customPrice-'.$entity->getId().'">';
                $data .='</span>';
                $data .='<input class="m-wrap span8 salesPrice"  '.$readonly.' rel="'.$entity->getId().'" id="salesPrice-'.$entity->getId().'" type="text" name="salesPrice" value="'.$entity->getSalesPrice().'" placeholder="'.$entity->getEstimatePrice().'">';
                $data .='</div></td>';
                $data .='<td class="" ><span id="subTotalShow-'. $entity->getId().'" >'.$entity->getSubTotal().'</td>';
                $data .='<td class="" >
                     <a id="'.$entity->getId().'" title="Are you sure went to delete ?" rel="/inventory/sales/'.$entity->getSales()->getId().'/'.$entity->getId().'/delete" href="javascript:" class="btn red mini delete" ><i class="icon-trash"></i></a>
                     </td>';
                $data .='</tr>';
            }


            $i++;
        }
        return $data;
    }

    public function getManualSalesItems($sales)
    {
        $entities = $sales->getSalesItems();
        $data = '';
        $i = 1;
        foreach( $entities as $entity){

            if (!empty($entity->getItem()->getMasterItem()) and !empty($entity->getItem()->getMasterItem()->getProductUnit())){
                $unit = $entity->getItem()->getMasterItem()->getProductUnit()->getName();
            } else{
                $unit = '';
            }

            $itemName = $entity->getItem()->getName();
            $data .=' <tr id="remove-'.$entity->getId().'">';
            $data .='<td class="numeric" >'.$i.'</td>';
            $data .='<td class="numeric" >'.$entity->getPurchaseItem()->getBarcode();
            $data .='</br><span>'.$itemName.'</span>';
            $data .='</td>';
            $data .='<td class=" span3" ><div class="input-append">';
            $data .='<input type="text" name="quantity[]" rel="'.$entity->getId().'"  id="quantity-'.$entity->getId().'" class="m-wrap span6 quantity" value="'.$entity->getQuantity().'" min="1" max="'.$entity->getPurchaseItem()->getQuantity().'" placeholder="'.$entity->getPurchaseItem()->getQuantity().'">';
            $data .='<span class="add-on">'.$unit.'</span>';
            $data .='</div></td>';
            $data .='<td class=" span3" >';
            $data .='<input class="m-wrap span8 salesPrice" id="salesPrice-'.$entity->getId().'" type="text" name="salesPrice" value="'.$entity->getSalesPrice().'" placeholder="'.$entity->getEstimatePrice().'">';
            $data .='</td>';
            $data .='<td class="" ><span id="subTotalShow-'. $entity->getId().'" >'.$entity->getSubTotal().'</td>';
            $data .='<td class="" >
                     <a id="'.$entity->getId().'" title="Are you sure went to delete ?" rel="/inventory/sales/'.$entity->getSales()->getId().'/'.$entity->getId().'/delete" href="javascript:" class="btn red mini delete" ><i class="icon-trash"></i></a>
                     </td>';
            $data .='</tr>';
            $i++;
        }
        return $data;
    }


    /**
     * @param $qb
     * @param $data
     */

    protected function handleSearchBetween($qb,$data)
    {

            $startDate = isset($data['startDate'])  ? $data['startDate'] : '';
            $endDate =   isset($data['endDate'])  ? $data['endDate'] : '';

            if (!empty($data['startDate']) ) {

                $qb->andWhere("sales.updated >= :startDate");
                $qb->setParameter('startDate', $startDate.' 00:00:00');
            }
            if (!empty($data['endDate'])) {

                $qb->andWhere("sales.updated <= :endDate");
                $qb->setParameter('endDate', $endDate.' 23:59:59');
            }
    }

    public function reportSalesPrice(User $user ,$data)
    {

        $globalOption = $user->getGlobalOption();
        $branch = $user->getProfile()->getBranches();

        $qb = $this->_em->createQueryBuilder();
        $qb->from('InventoryBundle:Sales','sales');
        $qb->select('SUM(sales.total) AS salesAmount');
        $qb->where("sales.globalOption = :globalOption");
        $qb->where("sales.inventoryConfig = :inventoryConfig");
        $qb->setParameter('inventoryConfig', $globalOption->getInventoryConfig());
        if (!empty($branch)){
            $qb->andWhere("sales.branches = :branch");
            $qb->setParameter('branch', $branch);
        }
        $qb->andWhere('sales.paymentStatus IN(:paymentStatus)');
        $qb->setParameter('paymentStatus',array_values(array('Paid','Due')));
        $this->handleSearchBetween($qb,$data);
        $result = $qb->getQuery()->getSingleResult();
        return $data = $result['salesAmount'] ;
    }


    public function reportPurchasePrice(User $user,$data)
    {
        $globalOption = $user->getGlobalOption();
        $branch = $user->getProfile()->getBranches();
        $qb = $this->createQueryBuilder('si');
        $qb->join('si.sales','sales');
        $qb->select('SUM(si.quantity * si.purchasePrice ) AS totalPurchaseAmount');
        $qb->where("sales.inventoryConfig = :inventoryConfig");
        $qb->setParameter('inventoryConfig', $globalOption->getInventoryConfig());
        if (!empty($branch)){
            $qb->andWhere("sales.branches = :branch");
            $qb->setParameter('branch', $branch);
        }
        $qb->andWhere('sales.paymentStatus IN(:paymentStatus)');
        $qb->setParameter('paymentStatus',array_values(array('Paid','Due')));
        $this->handleSearchBetween($qb,$data);
        $result = $qb->getQuery()->getSingleResult();
        return $data = $result['totalPurchaseAmount'] ;

    }

    public function reportProductVat(User $user ,$data)
    {
        $globalOption = $user->getGlobalOption();
        $branch = $user->getProfile()->getBranches();

        $qb = $this->_em->createQueryBuilder();
        $qb->from('InventoryBundle:Sales','sales');
        $qb->select('SUM(sales.vat) AS salesVat');
        $qb->where("sales.inventoryConfig = :inventoryConfig");
        $qb->setParameter('inventoryConfig', $globalOption->getInventoryConfig());
        $qb->andWhere('sales.paymentStatus IN(:paymentStatus)');
        $qb->setParameter('paymentStatus',array_values(array('Paid','Due')));
        $this->handleSearchBetween($qb,$data);
        $result = $qb->getQuery()->getOneOrNullResult();
        return $data = $result['salesVat'] ;
    }
}
