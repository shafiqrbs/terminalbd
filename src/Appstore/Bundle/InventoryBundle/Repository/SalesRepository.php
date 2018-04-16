<?php

namespace Appstore\Bundle\InventoryBundle\Repository;
use Appstore\Bundle\AccountingBundle\Entity\AccountSales;
use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Appstore\Bundle\InventoryBundle\Entity\Sales;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

/**
 * SalesRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SalesRepository extends EntityRepository
{

    /**
     * @param $qb
     * @param $data
     */

    protected function handleSearchBetween($qb,$data)
    {
        if(!empty($data))
        {

            $startDate = isset($data['startDate'])  ? $data['startDate'] : '';
            $endDate = isset($data['endDate'])  ? $data['endDate'] : '';
            $invoice =    isset($data['invoice'])? $data['invoice'] :'';
            $process =    isset($data['process'])? $data['process'] :'';
            $transactionMethod =    isset($data['transactionMethod'])? $data['transactionMethod'] :'';
            $courierInvoice =    isset($data['courierInvoice'])? $data['courierInvoice'] :'';
            $salesBy =    isset($data['toUser'])? $data['toUser'] :'';
            $customer =    isset($data['customer'])? $data['customer'] :'';
            $paymentStatus =    isset($data['paymentStatus'])? $data['paymentStatus'] :'';
            $mode =    isset($data['mode'])? $data['mode'] :'';

            if (!empty($startDate)) {
                $start = date('Y-m-d 00:00:00',strtotime($data['startDate']));
                $qb->andWhere("s.created >= :startDate");
                $qb->setParameter('startDate',$start);
            }

            if (!empty($endDate)) {
                $end = date('Y-m-d 23:59:59',strtotime($data['endDate']));
                $qb->andWhere("s.created <= :endDate");
                $qb->setParameter('endDate',$end);
            }

            if (!empty($invoice)) {

                $qb->andWhere("s.invoice LIKE :invoice");
                $qb->setParameter('invoice', $invoice.'%');
              }

            if (!empty($courierInvoice)) {

                $qb->andWhere("s.courierInvoice LIKE :courierInvoice");
                $qb->setParameter('invoice','%'. $courierInvoice.'%');
            }

            if (!empty($process)) {

                $qb->andWhere("s.process = :process");
                $qb->setParameter('process', $process);

            }

            if (!empty($customer)) {

                $qb->andWhere("c.mobile = :mobile");
                $qb->setParameter('mobile', $customer);
            }

            if (!empty($salesBy)) {

                $qb->andWhere("u.username = :user");
                $qb->setParameter('user', $salesBy);
            }
            if (!empty($transactionMethod)) {

                $qb->andWhere("s.transactionMethod = :transactionMethod");
                $qb->setParameter('transactionMethod', $transactionMethod);
            }

            if (!empty($paymentStatus)) {

                $qb->andWhere("s.paymentStatus = :paymentStatus");
                $qb->setParameter('paymentStatus', $paymentStatus);
            }

            if(!empty($mode)){
                $qb->andWhere("s.salesMode = :mode");
                $qb->setParameter('mode', $mode);
            }
        }

    }

    public function salesLists( User $user , $mode = '', $data)
    {

        /* @var InventoryConfig $config */
        $config = $user->getGlobalOption()->getInventoryConfig();
        $branch = $user->getProfile()->getBranches();
        $existArray = array(
            'ROLE_DOMAIN_INVENTORY_MANAGER',
            'ROLE_DOMAIN_INVENTORY_BRANCH_MANAGER',
            'ROLE_DOMAIN_INVENTORY_APPROVE',
            'ROLE_DOMAIN_MANAGER',
            'ROLE_DOMAIN'
        );

        $qb = $this->createQueryBuilder('s');
        $qb->leftJoin('s.customer', 'c');
        $qb->leftJoin('s.salesBy', 'u');
        $qb->where("s.inventoryConfig = :config");
        $qb->setParameter('config', $config);
        $qb->andWhere("s.salesMode = :mode");
        $qb->setParameter('mode', $mode);
        if ($branch and $mode == 'online'){

            $qb->andWhere("s.branches is NULL OR s.branches =".$branch->getId());

        }elseif($config->getIsBranch() == 1 and empty($branch) and $user->getCheckRoleGlobal(array('ROLE_DOMAIN_INVENTORY_SALES_ONLINE')) and ! $user->getCheckRoleGlobal($existArray) ){

            $qb->andWhere("s.createdBy =".$user->getId());
        }
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('s.created','DESC');
        $result = $qb->getQuery();
        return $result;

    }

    public function salesReport( InventoryConfig $inventory , $data)
    {

        $branch =    isset($data['branch'])? $data['branch'] :'';
        $qb = $this->createQueryBuilder('s');
        $qb->leftJoin('s.salesBy', 'u');
        $qb->leftJoin('s.transactionMethod', 't');
        $qb->select('u.username as salesBy');
        $qb->addSelect('t.name as transactionMethod');
        $qb->addSelect('s.id as id');
        $qb->addSelect('s.created as created');
        $qb->addSelect('s.process as process');
        $qb->addSelect('s.invoice as invoice');
        $qb->addSelect('(s.due) as due');
        $qb->addSelect('(s.subTotal) as subTotal');
        $qb->addSelect('(s.total) as total');
        $qb->addSelect('(s.payment) as payment');
        $qb->addSelect('(s.totalItem) totalItem');
        $qb->addSelect('(s.discount) as discount');
        $qb->addSelect('(s.vat) as vat');
        $qb->where("s.inventoryConfig = :config");
        $qb->setParameter('config', $inventory);
        $qb->andWhere('s.paymentStatus IN(:paymentStatus)');
        $qb->setParameter('paymentStatus',array_values(array('Paid','Due')));

        if(!empty($branch)){
            $qb->andWhere("s.branches =".$branch);
        }
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('s.updated','DESC');
        $result = $qb->getQuery();
        return $result;

    }

    public function salesUserReport( InventoryConfig $inventory , $data)
    {

        $branch =    isset($data['branch'])? $data['branch'] :'';

        $qb = $this->createQueryBuilder('s');
        $qb->leftJoin('s.salesBy', 'u');
        $qb->select('u.username as salesBy');
        $qb->addSelect('u.id as userId');
        $qb->addSelect('SUM(s.due) as due');
        $qb->addSelect('SUM(s.subTotal) as subTotal');
        $qb->addSelect('SUM(s.total) as total');
        $qb->addSelect('SUM(s.payment) as payment');
        $qb->addSelect('SUM(s.totalItem) totalItem');
        $qb->addSelect('SUM(s.discount) as discount');
        $qb->addSelect('SUM(s.vat) as vat');
        $qb->where("s.inventoryConfig = :config");
        $qb->setParameter('config', $inventory);
        $qb->andWhere('s.paymentStatus IN(:paymentStatus)');
        $qb->setParameter('paymentStatus',array_values(array('Paid','Due')));

        if(!empty($branch)){
            $qb->andWhere("s.branches =".$branch);
        }
        $this->handleSearchBetween($qb,$data);
        $qb->groupBy('salesBy');
        $qb->orderBy('total','DESC');
        $result = $qb->getQuery()->getArrayResult();
        return $result;

    }

    public function salesUserPurchasePriceReport($inventory,$data)
    {
        $qb = $this->createQueryBuilder('s');
        $qb->leftJoin('s.salesBy', 'u');
        $qb->join('s.salesItems','si');
        $qb->select('u.username as salesBy');
        $qb->addSelect('SUM(si.quantity * si.purchasePrice ) AS totalPurchaseAmount');
        $qb->where("s.inventoryConfig = :inventory");
        $qb->setParameter('inventory', $inventory->getId());
        $qb->andWhere('s.paymentStatus IN(:paymentStatus)');
        $qb->setParameter('paymentStatus',array_values(array('Paid','Due')));
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('totalPurchaseAmount','DESC');
        $qb->groupBy('salesBy');
        $result = $qb->getQuery()->getArrayResult();
        $array= array();
        foreach ($result as $row ){
            $array[$row['salesBy']]= $row['totalPurchaseAmount'];
        }
        return $array;
    }

    public function salesPurchasePriceReport($inventory,$data,$x)
    {
        $ids = array();
        foreach ($x as $y){
            $ids[]=$y['id'];
        }

        $qb = $this->createQueryBuilder('s');
        $qb->join('s.salesItems','si');
        $qb->select('s.id as salesId');
        $qb->addSelect('SUM(si.quantity * si.purchasePrice ) AS totalPurchaseAmount');
        $qb->where("s.inventoryConfig = :inventory");
        $qb->setParameter('inventory', $inventory->getId());
        $qb->andWhere('s.paymentStatus IN(:paymentStatus)');
        $qb->setParameter('paymentStatus',array_values(array('Paid','Due')));
        $qb->andWhere("s.id IN (:salesId)");
        $qb->setParameter('salesId', $ids);
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('totalPurchaseAmount','DESC');
        $qb->groupBy('salesId');
        $result = $qb->getQuery()->getArrayResult();
        $array= array();
        foreach ($result as $row ){
            $array[$row['salesId']]= $row['totalPurchaseAmount'];
        }
        return $array;
    }

    public function getSalesLastId($inventory)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('s.id');
        $qb->from('InventoryBundle:Sales','s');
        $qb->where("s.inventoryConfig = :inventory");
        $qb->setParameter('inventory', $inventory->getId());
        $qb->orderBy('s.id','DESC');
        $qb->setMaxResults(1);
        $lastId = $qb->getQuery()->getSingleScalarResult();
        if( $lastId > 0 ){
            return $lastId +1;
        }else{
            return 1;
        }
        return $lastId;
    }

    public function updateSalesTotalPrice(Sales $sales,$import ='')
    {
        $em = $this->_em;
        $total = $em->createQueryBuilder()
            ->from('InventoryBundle:SalesItem','si')
            ->select('sum(si.subTotal) as total , sum(si.quantity) as totalItem')
            ->where('si.sales = :sales')
            ->setParameter('sales', $sales ->getId())
            ->getQuery()->getSingleResult();
        if($import == 'import'){
            $sales->setPayment($total['total']);
        }

        if ($sales->getInventoryConfig()->getVatEnable() == 1 && $sales->getInventoryConfig()->getVatPercentage() > 0) {
            $totalAmount = ($total['total'] - $sales->getDiscount());
            $vat = $this->getCulculationVat($sales,$totalAmount);
            $sales->setVat($vat);
        }
        if($total['total'] > 0){
            $sales->setSubTotal($total['total']);
            $sales->setTotal($total['total'] + $sales->getVat());
            $sales->setDue($total['total']+ $sales->getVat());
            $sales->setTotalItem($total['totalItem']);
        }else{
            $sales->setSubTotal(0);
            $sales->setTotal(0);
            $sales->setDue(0);
            $sales->setTotalItem(0);
            $sales->setDiscount(0);
            $sales->setVat(0);
        }

        $em->persist($sales);
        $em->flush();

        return $sales;

    }

    public function updateSalesPaymentReceive(AccountSales $accountSales)
    {
        /* @var Sales $sales **/

        $sales = $accountSales->getSales();
        $sales->setPayment($sales->getPayment() + $accountSales->getAmount());
        $sales->setDue($sales->getDue() - $accountSales->getAmount());
        if($sales->getDue() == 0 ){
            $sales->setPaymentStatus('Paid');
        }
        $this->_em->persist($sales);
        $this->_em->flush();
    }

    public function todaySalesOverview(User $user , $mode='')
    {

        $inventory = $user->getGlobalOption()->getInventoryConfig();
        $branch = $user->getProfile()->getBranches();

        $qb = $this->_em->createQueryBuilder();
        $datetime = new \DateTime("now");
        $today_startdatetime = $datetime->format('Y-m-d 00:00:00');
        $today_enddatetime = $datetime->format('Y-m-d 23:59:59');
        $qb->from('InventoryBundle:Sales','s');
        $qb->select('sum(s.subTotal) as subTotal , sum(s.total) as total , count(s.id) as totalVoucher, sum(s.due) as totalDue, sum(s.discount) as totalDiscount, sum(s.vat) as totalVat');
        $qb->where('s.inventoryConfig = :inventory')
            ->andWhere('s.salesMode =:mode')
            ->andWhere('s.paymentStatus IN (:pStatus)')
            ->andWhere('s.updated >= :today_startdatetime')
            ->andWhere('s.updated <= :today_enddatetime');
        $qb->setParameter('inventory', $inventory)
            ->setParameter('mode', $mode)
            ->setParameter('pStatus', array('Paid','Due'))
            ->setParameter('today_startdatetime', $today_startdatetime)
            ->setParameter('today_enddatetime', $today_enddatetime);
        if ($branch){
            $qb->andWhere("s.branches = :branch");
            $qb->setParameter('branch', $branch);
        }
        $qb->orderBy("s.updated", 'DESC');
        return $qb->getQuery()->getResult();
    }

    public function SalesOverview(InventoryConfig $inventory,$data)
    {

        $startDate = isset($data['startDate'])  ? $data['startDate'] : '';
        $endDate = isset($data['endDate'])  ? $data['endDate'] : '';
        $branch = isset($data['branch'])  ? $data['branch'] : '';

        $qb = $this->_em->createQueryBuilder();

        $qb->from('InventoryBundle:Sales','s');
        $qb->select('sum(s.subTotal) as subTotal , sum(s.total) as total ,sum(s.payment) as totalPayment , count(s.id) as totalVoucher, sum(s.due) as totalDue, sum(s.discount) as totalDiscount, sum(s.vat) as totalVat');
        $qb->where('s.inventoryConfig = :inventory');
        $qb->setParameter('inventory', $inventory);
        $qb->andWhere('s.process = :process');
        $qb->setParameter('process', 'Done');
        if (!empty($startDate)) {
            $start = date('Y-m-d',strtotime($data['startDate']));
            $qb->andWhere("s.updated >= :startDate");
            $qb->setParameter('startDate',$start);
        }

        if (!empty($endDate)) {
            $end = date('Y-m-d',strtotime($data['endDate']));
            $qb->andWhere("s.updated <= :endDate");
            $qb->setParameter('endDate',$end);
        }

        if ($branch){
            $qb->andWhere("s.branches = :branch");
            $qb->setParameter('branch', $branch);
        }
        return $qb->getQuery()->getOneOrNullResult();
    }

    public function salesTransactionOverview(InventoryConfig $inventory,$data)
    {
        $startDate = isset($data['startDate'])  ? $data['startDate'] : '';
        $endDate = isset($data['endDate'])  ? $data['endDate'] : '';
        $branch = isset($data['branch'])  ? $data['branch'] : '';

        $qb = $this->createQueryBuilder('s');
        $qb->join('s.transactionMethod','t');
        $qb->select('t.name as transactionName , sum(s.subTotal) as subTotal , sum(s.total) as total ,sum(s.payment) as totalPayment , count(s.id) as totalVoucher, sum(s.due) as totalDue, sum(s.discount) as totalDiscount, sum(s.vat) as totalVat');
        $qb->where('s.inventoryConfig = :inventory');
        $qb->setParameter('inventory', $inventory);
        $qb->andWhere('s.process = :process');
        $qb->setParameter('process', 'Done');
        if (!empty($startDate)) {
            $start = date('Y-m-d',strtotime($data['startDate']));
            $qb->andWhere("s.updated >= :startDate");
            $qb->setParameter('startDate',$start);
        }

        if (!empty($endDate)) {
            $end = date('Y-m-d',strtotime($data['endDate']));
            $qb->andWhere("s.updated <= :endDate");
            $qb->setParameter('endDate',$end);
        }

        if ($branch){
            $qb->andWhere("s.branches = :branch");
            $qb->setParameter('branch', $branch);
        }
        $qb->groupBy("s.transactionMethod");
        $res = $qb->getQuery();
        return $result = $res->getArrayResult();
    }

    public function salesModeOverview(InventoryConfig $inventory,$data)
    {
        $startDate = isset($data['startDate'])  ? $data['startDate'] : '';
        $endDate = isset($data['endDate'])  ? $data['endDate'] : '';
        $branch = isset($data['branch'])  ? $data['branch'] : '';

        $qb = $this->createQueryBuilder('s');
        $qb->select('s.salesMode as name , sum(s.subTotal) as subTotal , sum(s.total) as total ,sum(s.payment) as totalPayment , count(s.id) as totalVoucher, sum(s.due) as totalDue, sum(s.discount) as totalDiscount, sum(s.vat) as totalVat');
        $qb->where('s.inventoryConfig = :inventory');
        $qb->setParameter('inventory', $inventory);
        $qb->andWhere('s.process = :process');
        $qb->setParameter('process', 'Done');
        if (!empty($startDate)) {
            $start = date('Y-m-d',strtotime($data['startDate']));
            $qb->andWhere("s.updated >= :startDate");
            $qb->setParameter('startDate',$start);
        }

        if (!empty($endDate)) {
            $end = date('Y-m-d',strtotime($data['endDate']));
            $qb->andWhere("s.updated <= :endDate");
            $qb->setParameter('endDate',$end);
        }

        if ($branch){
            $qb->andWhere("s.branches = :branch");
            $qb->setParameter('branch', $branch);
        }
        $qb->groupBy("s.salesMode");
        $res = $qb->getQuery();
        return $result = $res->getArrayResult();
    }

    public function salesProcessOverview(InventoryConfig $inventory,$data)
    {
        $startDate = isset($data['startDate'])  ? $data['startDate'] : '';
        $endDate = isset($data['endDate'])  ? $data['endDate'] : '';
        $branch = isset($data['branch'])  ? $data['branch'] : '';

        $qb = $this->createQueryBuilder('s');
        $qb->select('s.process as name , sum(s.subTotal) as subTotal , sum(s.total) as total ,sum(s.payment) as totalPayment , count(s.id) as totalVoucher, sum(s.due) as totalDue, sum(s.discount) as totalDiscount, sum(s.vat) as totalVat');
        $qb->where('s.inventoryConfig = :inventory');
        $qb->setParameter('inventory', $inventory);
        if (!empty($startDate)) {
            $start = date('Y-m-d',strtotime($data['startDate']));
            $qb->andWhere("s.updated >= :startDate");
            $qb->setParameter('startDate',$start);
        }

        if (!empty($endDate)) {
            $end = date('Y-m-d',strtotime($data['endDate']));
            $qb->andWhere("s.updated <= :endDate");
            $qb->setParameter('endDate',$end);
        }

        if ($branch){
            $qb->andWhere("s.branches = :branch");
            $qb->setParameter('branch', $branch);
        }
        $qb->groupBy("s.process");
        $res = $qb->getQuery();
        return $result = $res->getArrayResult();
    }

    public function todaySales(User $user , $mode = '')
    {

        $inventory = $user->getGlobalOption()->getInventoryConfig();
        $branch = $user->getProfile()->getBranches();

        $qb = $this->_em->createQueryBuilder();
        $datetime = new \DateTime("now");
        $today_startdatetime = $datetime->format('Y-m-d 00:00:00');
        $today_enddatetime = $datetime->format('Y-m-d 23:59:59');
        $qb->from('InventoryBundle:Sales','s');
        $qb->select('s')
            ->where('s.inventoryConfig = :inventory')
            ->andWhere('s.salesMode =:mode')
            ->andWhere('s.updated >= :today_startdatetime')
            ->andWhere('s.updated <= :today_enddatetime');

        $qb->setParameter('inventory', $inventory)
            ->setParameter('mode', $mode)
            ->setParameter('today_startdatetime', $today_startdatetime)
            ->setParameter('today_enddatetime', $today_enddatetime);
        if ($branch){
            $qb->andWhere("s.branches = :branch");
            $qb->setParameter('branch', $branch);
        }
        $qb->orderBy("s.invoice", 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function findBySalesReturn($saleId = 0)
    {

        return $query = $this->findOneBy(array('invoice'=>$saleId));
        exit;
        echo $query->getId();

        $qb = $this->_em->createQueryBuilder();
        $qb->from('InventoryBundle:Sales','sales');
        $qb->select('sales');
        $qb->innerJoin('sales.salesItems','salesItems');
        $qb->innerJoin('salesItems.purchaseItem','purchaseitem');
        $qb->where("sales.inventoryConfig = :inventory");
        $qb->setParameter('inventory', $inventory->getId());
        if(!empty($saleId)){
            $qb->andWhere("sales.salesCode = :code");
            $qb->setParameter('code',$saleId);
        }
        if(!empty($barcode)){
            $qb->andWhere("purchaseitem.barcode = :barcode");
            $qb->setParameter('barcode',$barcode);
        }
        return $result =   $qb->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);;

    }

    public function getCulculationVat(Sales $sales,$totalAmount)
    {
        $vat = ( ($totalAmount * (int)$sales->getInventoryConfig()->getVatPercentage())/100 );
        return round($vat);
    }

}
