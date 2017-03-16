<?php

namespace Appstore\Bundle\InventoryBundle\Repository;
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
        }

    }

    public function salesLists( User $user , $mode = '', $data)
    {

        $config = $user->getGlobalOption()->getInventoryConfig();
        $branch = $user->getProfile()->getBranches();

        $qb = $this->createQueryBuilder('s');
        $qb->leftJoin('s.customer', 'c');
        $qb->leftJoin('s.salesBy', 'u');
        $qb->where("s.inventoryConfig = :config");
        $qb->setParameter('config', $config);
        $qb->andWhere("s.salesMode = :mode");
        $qb->setParameter('mode', $mode);
        if ($branch){
            $qb->andWhere("s.branches = :branch");
            $qb->setParameter('branch', $branch);
        }
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('s.updated','DESC');
        $result = $qb->getQuery();
        return $result;

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

    public function todaySalesOverview(User $user , $mode='')
    {

        $inventory = $user->getGlobalOption()->getInventoryConfig();
        $branch = $user->getProfile()->getBranches();

        $qb = $this->_em->createQueryBuilder();
        $datetime = new \DateTime("now");
        $today_startdatetime = $datetime->format('Y-m-d 00:00:00');
        $today_enddatetime = $datetime->format('Y-m-d 23:59:59');
        $qb->from('InventoryBundle:Sales','s');
        $qb->select('sum(s.subTotal) as subTotal , sum(s.total) as total , count(s.id) as totalVoucher, sum(s.due) as totalDue, sum(s.discount) as totalDiscount');
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
