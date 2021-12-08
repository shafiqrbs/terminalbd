<?php

namespace Appstore\Bundle\InventoryBundle\Repository;
use Appstore\Bundle\InventoryBundle\Entity\Item;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseItem;
use Appstore\Bundle\InventoryBundle\Entity\Sales;
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

    public function getInvoicePurchasePrice($entity)
    {
        $sql = "SELECT COALESCE(SUM(salesItem.quantity * salesItem.purchasePrice),0) as total FROM SalesItem as salesItem
                WHERE salesItem.sales_id = :invoice";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('invoice', $entity);
        $stmt->execute();
        $result =  $stmt->fetch();
        return $result['total'];
    }

    public function inventorySalesDaily(User $user , $data =array())
    {

        $config =  $user->getGlobalOption()->getInventoryConfig()->getId();
        $compare = new \DateTime();
        $month =  $compare->format('F');
        $year =  $compare->format('Y');
        $month = isset($data['month'])? $data['month'] :$month;
        $year = isset($data['year'])? $data['year'] :$year;
        $sql = "SELECT DATE_FORMAT(sales.created,'%d-%m-%Y') as date , DATE (sales.created) as dateId , SUM(SalesItem.quantity * SalesItem.purchasePrice) as purchasePrice 
                FROM SalesItem
                INNER JOIN Sales as sales ON SalesItem.sales_id = sales.id
                WHERE sales.inventoryConfig_id = :config AND sales.process = :process AND MONTHNAME(sales.created) =:month  AND YEAR(sales.created) =:year
                GROUP BY date ORDER BY dateId ASC";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('config', $config);
        $stmt->bindValue('process', 'Done');
        $stmt->bindValue('year', $year);
        $stmt->bindValue('month', $month);
        $stmt->execute();
        $results =  $stmt->fetchAll();
        $arrays = array();
        foreach ($results as $result){
            $arrays[$result['date']] = $result;
        }
        return $arrays;
    }

    public function inventorySalesMonthly(User $user , $data =array())
    {

        $config =  $user->getGlobalOption()->getInventoryConfig()->getId();
        $compare = new \DateTime();
        $year =  $compare->format('Y');
        $year = isset($data['year'])? $data['year'] :$year;
        $sql = "SELECT DATE_FORMAT(sales.created,'%M') as month , MONTH (sales.created) as monthId, SUM(si.quantity * si.purchasePrice) as purchasePrice 
                FROM SalesItem as si
                 JOIN  Sales as sales on si.sales_id = sales.id
                WHERE sales.inventoryConfig_id = :config AND sales.process = :process  AND YEAR(sales.created) =:year
                GROUP BY month ORDER BY monthId ASC";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('config', $config);
        $stmt->bindValue('process', 'Done');
        $stmt->bindValue('year', $year);
        $stmt->execute();
        $results =  $stmt->fetchAll();
        $arrays = array();
        foreach ($results as $result){
            $arrays[$result['month']] = $result;
        }
        return $arrays;


    }


    public function getItemPurchasePrice(Sales $sales)
    {
	    $qb = $this->createQueryBuilder('si');
	    $qb->addSelect('SUM(si.quantity * si.purchasePrice ) AS totalPurchaseAmount');
	    $qb->where("si.sales = :sales");
	    $qb->setParameter('sales', $sales->getId());
	    $result = $qb->getQuery()->getOneOrNullResult()['totalPurchaseAmount'];
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

    public function ongoingSalesManuelQuantity($inventory , $data = array())
    {

        $qb = $this->createQueryBuilder('salesItem');
        $qb->join('salesItem.sales','sales');
        $qb->join('salesItem.item','item');
        $qb->select('SUM(salesItem.quantity) as ongoingQuantity ');
        $qb->addSelect('item.id as id ');
        $qb->where("sales.inventoryConfig = :inventory");
        $qb->setParameter('inventory', $inventory);
        $qb->andWhere('sales.process IN(:process)');
        $qb->setParameter('process',array_values(array('In-progress','Courier')));
        if(!empty($data)){
        $qb->andWhere($qb->expr()->in("salesItem.item", $data ));
        }
        $qb->groupBy('item.id');
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

    public function checkSalesItemQuantity(Item  $item)
    {

        $qb = $this->createQueryBuilder('salesItem');
        $qb->join('salesItem.sales','sales');
        $qb->addSelect('SUM(salesItem.quantity) as quantity ');
        $qb->where("salesItem.item = :item");
        $qb->setParameter('item', $item->getId());
        $qb->andWhere('sales.process IN(:process)');
        $qb->setParameter('process',array_values(array('In-progress','Courier')));
        $quantity =  $qb->getQuery()->getOneOrNullResult();
        if(!empty($quantity['quantity'])){
            return $quantity['quantity'];
        }else{
            return 0;
        }

    }

    public function insertSalesManualItems(Sales $sales,Item $item,$data)
    {

    	$em = $this->_em;
        $existEntity = $this->findOneBy(array('sales'=> $sales,'item'=> $item));
        if(!empty($existEntity)){

            $curQuantity =  $existEntity->getQuantity() + $data['quantity'];
            $existEntity->setQuantity($curQuantity);
            $existEntity->setSubTotal($data['salesPrice'] * $curQuantity);
            $em->persist($existEntity);

        }else{

            $entity = new SalesItem();
            $entity->setSales($sales);
            $entity->setItem($item);
            $entity->setSalesPrice($data['salesPrice']);
            $entity->setEstimatePrice($item->getSalesPrice());
            $entity->setQuantity($data['quantity']);
            $entity->setPurchasePrice($data['purchasePrice']);
            $entity->setCustomPrice($data['salesPrice']);
            $entity->setSubTotal($data['salesPrice'] * $data['quantity']);
            $em->persist($entity);
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
        $isAttribute = $sales->getInventoryConfig()->isAttribute();
        $entities = $sales->getSalesItems();

        $data = '';
        $i = 1;
        /* @var $entity SalesItem */

        foreach( $entities as $entity){

            $option = '';
            if(!empty($entity->getPurchaseItem()->getSerialNo())){

                $salesSerials = explode(",",$entity->getSerialNo());
                $serials = explode(",",$entity->getPurchaseItem()->getSerialNo());
                $option .="<select class='serial-no' id='serialNo-{$entity->getId()}' name='serialNo' multiple='multiple'>";
                $option .="<option>--Serial no--</option>";
                foreach ($serials as $serial){
                    $selected = in_array($serial,$salesSerials) ? 'selected=selected':'';
                    $option.="<option {$selected} value='{$serial}'>{$serial}</option>";
                }
                $option .="</select>";
            }

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
                $data .='<td>'.$entity->getPurchaseItem()->getBarcode().'</td>';
                $data .='<td>'.$itemName.'</td>';
                if ($isAttribute == 1){
                    $data .= '<td class="numeric" >'.$option.'</td>';
                }
                $data .='<td>'.$entity->getQuantity().$unit.'</td>';
                $data .='<td>'.$entity->getSalesPrice().'</td>';
                $data .='<td>'.$entity->getSubTotal().'</td>';
                $data .='<td class="" >
                     <a id="'.$entity->getId().'" title="Are you sure went to delete ?" rel="/inventory/sales/'.$entity->getSales()->getId().'/'.$entity->getId().'/delete" href="javascript:" class="btn red mini delete" ><i class="icon-trash"></i></a>
                     </td>';
                $data .='</tr>';

            }else {

                $data .= '<tr id="remove-' . $entity->getId() . '">';
                $data .= '<td class="numeric" >' . $i . '</td>';
                $data .= "<td>{$entity->getPurchaseItem()->getBarcode()}</td>";
                $data .= "<td>{$itemName}</td>";
                if ($isAttribute == 1){
                    $data .= '<td class="numeric" >'.$option.'</td>';
                }
                $data .='<td class="numeric" >';
                $data .='<input type="text" name="quantity[]" rel="'.$entity->getId().'"  id="quantity-'.$entity->getId().'" class="td-inline-input quantity" value="'.$entity->getQuantity().'" min="1" max="'.$entity->getPurchaseItem()->getQuantity().'" placeholder="'.$entity->getPurchaseItem()->getQuantity().'">';
                $data .='</td>';
                $data .='<td class="" ><div class="input-prepend">';
                $data .='<span class="add-on-inline add-on-box">';
                $data .='<input type="hidden" name="estimatePrice" id="estimatePrice-'.$entity->getId().'" value="'.$entity->getEstimatePrice().'">';
                $data .='<input type="checkbox"  class="customPrice" value="1"  '. $checked .' rel="'.$entity->getId().'" id="customPrice-'.$entity->getId().'">';
                $data .='</span>';
                $data .='<input class="td-inline-input salesPrice"  '.$readonly.' rel="'.$entity->getId().'" id="salesPrice-'.$entity->getId().'" type="text" name="salesPrice" value="'.$entity->getSalesPrice().'" placeholder="'.$entity->getEstimatePrice().'">';
                $data .='</div></td>';
                $data .='<td class="numeric" ><span id="subTotalShow-'. $entity->getId().'" >'.$entity->getSubTotal().'</td>';
                $data .="<td class='numeric' >";
                if ($isAttribute == 1 and !empty($entity->getPurchaseItem()->getSerialNo())) {
                    $data .= "<a id='{$entity->getId()}'  data-url='/inventory/sales/{$entity->getId()}/update-serial-no' href='javascript:' class='btn blue mini serialSave' ><i class='icon-save'></i></a>";
                }
                $data .="<a id='{$entity->getId()}'  rel='/inventory/sales/{$entity->getSales()->getId()}/{$entity->getId()}/delete' href='javascript:' class='btn red mini delete' ><i class='icon-trash'></i></a>";
                $data .="</td>";
                $data .='</tr>';
            }


            $i++;
        }
        return $data;
    }


    public function manualSalesItemUpdate(SalesItem $salesItem , $data)
    {
	    $em = $this->_em;
	    $salesItemId = $data['itemId'];
	    $quantity = $data['quantity'];
	    $salesPrice =$data['salesPrice'];

	    $remainingQuantity = $salesItem->getItem()->getRemainingQuantity();
	    $checkQuantity = $this->checkSalesItemQuantity($salesItem->getItem());
	    $currentRemainingQnt = ($remainingQuantity + $salesItem->getQuantity()) - ($checkQuantity + $quantity);
	    if (!empty($salesItem) && $remainingQuantity > 0 && $currentRemainingQnt >= 0) {
		    $salesItem->setQuantity($quantity);
		    $salesItem->setSalesPrice($salesPrice);
		    $salesItem->setSubTotal($quantity * $salesPrice);
		    $em->persist($salesItem);
		    $em->flush();
		    return 'valid';
	    } else {
		    return 'in-valid';
	    }

    }

    public function getManualSalesItems(Sales $sales)
    {
        $entities = $sales->getSalesItems();
        $data = '';
        $i = 1;
        /* @var $entity SalesItem */
        foreach( $entities as $entity){

            if (!empty($entity->getItem()->getMasterItem()) and !empty($entity->getItem()->getMasterItem()->getProductUnit())){
                $unit = $entity->getItem()->getMasterItem()->getProductUnit()->getName();
            } else{
                $unit = '';
            }
            $itemName = $entity->getItem()->getName();
            $data .='<tr id="remove-'.$entity->getId().'">';
            $data .='<td class="numeric" >'.$i.'</td>';
            $data .='<td class="numeric" >'.$itemName.'</td>';
	        $data .="<td class='numeric' ><input type='text' name='salesPrice[]' data-id='{$entity->getId()}'  id='salesPrice-{$entity->getId()}' class='td-inline-input salesPrice' value='{$entity->getSalesPrice()}'</td>";
	        $data .="<td class='numeric' ><input type='text' name='quantity[]' data-id='{$entity->getId()}'  id='quantity-{$entity->getId()}' class='td-inline-input quantity' value='{$entity->getQuantity()}'</td>";
            $data .='<td class="numeric" >'.$unit.'</td>';
            $data .="<td class=''><span id='subTotalShow-{$entity->getId()}' ><strong>{$entity->getSubTotal()}</strong></span></td>";
            $data .='<td class="" ><a id="'.$entity->getId().'"  data-url="/inventory/sales-manual/'.$entity->getSales()->getId().'/'.$entity->getId().'/manual-item-delete" href="javascript:" class="btn red mini itemRemove" ><i class="icon-trash"></i></a></td>';
            $data .='</tr>';
            $i++;
        }
        return $data;
    }

    public function getOnlineSalesItems(Sales $sales)
    {
        $entities = $sales->getSalesItems();
        $data = '';
        $i = 1;
        /* @var $entity SalesItem */
        foreach( $entities as $entity){

            if (!empty($entity->getItem()->getMasterItem()) and !empty($entity->getItem()->getMasterItem()->getProductUnit())){
                $unit = $entity->getItem()->getMasterItem()->getProductUnit()->getName();
            } else{
                $unit = '';
            }
            $itemName = $entity->getItem()->getName();
            $data .='<tr id="remove-'.$entity->getId().'">';
            $data .='<td class="numeric" >'.$i.'</td>';
            $data .='<td class="numeric" >'.$itemName.'</td>';
            $data .='<td class="numeric" >'.$entity->getSalesPrice().'</td>';
            $data .='<td class="numeric" >'.$entity->getQuantity().'</td>';
            $data .='<td class="numeric" >'.$unit.'</td>';
            $data .='<td class="" >'.$entity->getSubTotal().'</td>';
            $data .='<td class="" ><a id="'.$entity->getId().'" title="Are you sure went to delete ?" data-url="/inventory/sales-manual/'.$entity->getSales()->getId().'/'.$entity->getId().'/manual-item-delete" href="javascript:" class="btn red mini remove" ><i class="icon-trash"></i></a></td>';
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
        if (!empty($startDate)) {
            $start = date('Y-m-d 00:00:00',strtotime($startDate));
            $qb->andWhere("sales.created >= :startDate");
            $qb->setParameter('startDate',$start);
        }

        if (!empty($endDate)) {
            $end = date('Y-m-d 23:59:59',strtotime($startDate));
            $qb->andWhere("sales.created <= :endDate");
            $qb->setParameter('endDate',$end);
        }

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
