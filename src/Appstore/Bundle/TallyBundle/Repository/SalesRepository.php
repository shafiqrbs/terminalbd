<?php

namespace Appstore\Bundle\TallyBundle\Repository;
use Appstore\Bundle\AccountingBundle\Entity\AccountSales;
use Appstore\Bundle\TallyBundle\Entity\InventoryConfig;
use Appstore\Bundle\TallyBundle\Entity\Sales;
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
            $branch =    isset($data['branch'])? $data['branch'] :'';
            $item =    isset($data['item'])? $data['item'] :'';
            $barcode =    isset($data['barcode'])? $data['barcode'] :'';
            $serialNo =    isset($data['serialNo'])? $data['serialNo'] :'';
            $vendor =    isset($data['vendor'])? $data['vendor'] :'';

            if (!empty($startDate)) {
                $datetime = new \DateTime($startDate);
                $start = $datetime->format('Y-m-d 00:00:00');
                $qb->andWhere("s.created >= :startDate")->setParameter('startDate',$start);
            }

            if (!empty($endDate)) {
                $datetime = new \DateTime($endDate);
                $end = $datetime->format('Y-m-d 23:59:59');
                $qb->andWhere("s.created <= :endDate")->setParameter('endDate',$end);
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
            if(!empty($branch)){
                $qb->andWhere("s.branches = :branch");
                $qb->setParameter('branch', $branch);
            }

            if (!empty($item)) {
                $qb->join('si.item','item');
                $qb->andWhere("item.name = :name");
                $qb->setParameter('name', $item);
            }

            if (!empty($vendor)) {
                $qb->andWhere("vendor.companyName = :vendorName");
                $qb->setParameter('vendorName', $vendor);
            }

            if (!empty($barcode)) {
                $qb->leftJoin('si.purchaseItem','purchaseItem');
                $qb->andWhere("purchaseItem.barcode = :barcode");
                $qb->setParameter('barcode', $barcode);
            }

            if (!empty($serialNo)) {
                $qb->andWhere("si.serialNo LIKE :serialNo");
                $qb->setParameter('serialNo','%'. $serialNo.'%');
            }

        }

    }

    public function salesLists( User $user , $mode = '', $data)
    {

        /* @var InventoryConfig $config */
        $config = $user->getGlobalOption()->getTallyConfig();
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
        $qb->where("s.config = :config");
        $qb->setParameter('config', $config);
        $qb->andWhere("s.salesMode = :mode");
        $qb->setParameter('mode', $mode);
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('s.created','DESC');
        $result = $qb->getQuery();
        return $result;

    }

    public function findArrayIds($ids){

	    $query = $this->createQueryBuilder('e');
	    $query->select('e');
	    $query->andWhere("e.id IN(:ids)");
	    $query->setParameter('ids', $ids);
	    return $query->getQuery()->getResult();
    }


	public function searchAutoComplete($q, InventoryConfig $inventory)
	{

		$query = $this->createQueryBuilder('e');
		$query->join('e.config', 'ic');
		$query->select('e.invoice as id');
		$query->addSelect('e.invoice as text');
		$query->where($query->expr()->like("e.invoice", "'$q%'"  ));
		$query->andWhere("ic.id = :inventory");
		$query->setParameter('inventory', $inventory->getId());
		$query->groupBy('e.id');
		$query->orderBy('e.companyName', 'ASC');
		$query->setMaxResults( '30' );
		return $query->getQuery()->getResult();

	}

    public function salesReport( User $user , $data)
    {
        $userBranch = $user->getProfile()->getBranches();
        $inventory =  $user->getGlobalOption()->getTallyConfig()->getId();
        $qb = $this->createQueryBuilder('s');
        $qb->leftJoin('s.salesBy', 'u');
        $qb->leftJoin('s.transactionMethod', 't');
        $qb->innerJoin('s.salesItems', 'si');
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
        $qb->addSelect('SUM(si.purchasePrice * si.quantity) as purchasePrice');
        $qb->where("s.config = :config");
        $qb->setParameter('config', $inventory);
        $qb->andWhere('s.process = :process');
        $qb->setParameter('process', 'Done');
        if(!empty($userBranch)){
            $qb->andWhere("s.branches =".$userBranch);
        }
        $this->handleSearchBetween($qb,$data);
        $qb->groupBy('s.id');
        $qb->orderBy('s.updated','DESC');
        $result = $qb->getQuery();
        return $result;

    }

    public function salesUserReport( User $user , $data)
    {

        $userBranch = $user->getProfile()->getBranches();
        $inventory =  $user->getGlobalOption()->getTallyConfig()->getId();

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
        $qb->where("s.config = :config");
        $qb->setParameter('config', $inventory);
        $qb->andWhere('s.process = :process');
        $qb->setParameter('process', 'Done');
        if(!empty($userBranch)){
            $qb->andWhere("s.branches =".$userBranch);
        }
        $this->handleSearchBetween($qb,$data);
        $qb->groupBy('salesBy');
        $qb->orderBy('total','DESC');
        $result = $qb->getQuery()->getArrayResult();
        return $result;

    }


    public function salesUserPurchasePriceReport(User $user,$data)
    {
        $userBranch = $user->getProfile()->getBranches();
        $inventory =  $user->getGlobalOption()->getTallyConfig()->getId();

        $qb = $this->createQueryBuilder('s');
        $qb->leftJoin('s.salesBy', 'u');
        $qb->join('s.salesItems','si');
        $qb->select('u.username as salesBy');
        $qb->addSelect('SUM(si.quantity * si.purchasePrice ) AS totalPurchaseAmount');
        $qb->where("s.config = :inventory");
        $qb->setParameter('inventory', $inventory);
        $qb->andWhere('s.process = :process');
        $qb->setParameter('process', 'Done');
        if(!empty($userBranch)){
            $qb->andWhere("s.branches =".$userBranch);
        }
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

	public function inventorySalesMonthly(User $user , $data =array())
	{

		$config =  $user->getGlobalOption()->getTallyConfig()->getId();
		$compare = new \DateTime();
		$year =  $compare->format('Y');
		$year = isset($data['year'])? $data['year'] :$year;
		$sql = "SELECT DATE_FORMAT(sales.created,'%M') as month , MONTH (sales.created) as monthId ,SUM(sales.total) AS total,SUM(sales.subTotal) AS subTotal,
                SUM(sales.discount) AS discount,SUM(sales.vat) AS vat,SUM(sales.payment) AS receive,SUM(sales.due) AS due
                FROM Sales as sales
                WHERE sales.config_id = :config AND sales.process = :process  AND YEAR(sales.created) =:year
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

    public function inventorySalesDaily(User $user , $data =array())
    {

        $config =  $user->getGlobalOption()->getTallyConfig()->getId();
        $compare = new \DateTime();
        $month =  $compare->format('F');
        $year =  $compare->format('Y');
        $month = isset($data['month'])? $data['month'] :$month;
        $year = isset($data['year'])? $data['year'] :$year;
        $sql = "SELECT DATE_FORMAT(sales.created,'%d-%m-%Y') as date , DATE (sales.created) as dateId ,SUM(sales.total) AS total,SUM(sales.subTotal) AS subTotal,
                SUM(sales.discount) AS discount,SUM(sales.vat) AS vat,SUM(sales.payment) AS receive,SUM(sales.due) AS due
                FROM Sales as sales
                WHERE sales.config_id = :config AND sales.process = :process AND MONTHNAME(sales.created) =:month  AND YEAR(sales.created) =:year
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

    public function monthlySales(User $user , $data =array())
    {

        $userBranch = $user->getProfile()->getBranches();
        $inventory =  $user->getGlobalOption()->getTallyConfig()->getId();

        $compare = new \DateTime();
        $year =  $compare->format('Y');
        $year = isset($data['year'])? $data['year'] :$year;
        $sql = "SELECT sales.salesBy_id as salesBy, MONTH (sales.created) as month,SUM(sales.total) AS total
                FROM Sales as sales
                WHERE sales.config_id = :inventoryConfig AND sales.process = :process  AND YEAR(sales.created) =:year
                GROUP BY month , salesBy ORDER BY salesBy ASC";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('inventoryConfig', $inventory);
        $stmt->bindValue('process', 'Done');
        $stmt->bindValue('year', $year);
        $stmt->execute();
        $result =  $stmt->fetchAll();
        return $result;
    }

	public function currentMonthSales(User $user , $data =array())
	{

		$userBranch = $user->getProfile()->getBranches();
		$config =  $user->getGlobalOption()->getTallyConfig()->getId();

		$compare = new \DateTime();
		$year =  $compare->format('Y');
		$month =  $compare->format('m');
		$year = isset($data['year'])? $data['year'] :$year;

		$sql = "SELECT sales.salesBy_id as salesBy, MONTH (sales.created) as month, SUM(sales.total) AS total
                FROM Sales as sales
                WHERE sales.config_id = :config AND sales.process = :process  AND YEAR(sales.created) =:year AND MONTH(sales.created) =:month
                GROUP BY month , salesBy ORDER BY salesBy ASC";
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->bindValue('config', $config);
		$stmt->bindValue('process', 'Done');
		$stmt->bindValue('year', $year);
		$stmt->bindValue('month', $month);
		$stmt->execute();
		$result =  $stmt->fetchAll();
		return $result;


	}

	public function salesPurchasePriceReport(User $user,$data,$x)
    {

        $userBranch = $user->getProfile()->getBranches();
        $inventory =  $user->getGlobalOption()->getTallyConfig()->getId();

        $ids = array();
        foreach ($x as $y){
            $ids[]=$y['id'];
        }

        $qb = $this->createQueryBuilder('s');
        $qb->join('s.salesItems','si');
        $qb->select('s.id as salesId');
        $qb->addSelect('SUM(si.quantity * si.purchasePrice ) AS totalPurchaseAmount');
        $qb->where("s.config = :inventory");
        $qb->setParameter('inventory', $inventory);
        $qb->andWhere('s.process = :process');
        $qb->setParameter('process', 'Done');
        $qb->andWhere("s.id IN (:salesId)")->setParameter('salesId', $ids);
        if(!empty($userBranch)){
            $qb->andWhere("s.branches =".$userBranch);
        }
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
        $qb->from('TallyBundle:Sales','s');
        $qb->where("s.config = :inventory");
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
            ->from('TallyBundle:SalesItem','si')
            ->select('sum(si.subTotal) as total , sum(si.quantity) as totalItem')
            ->where('si.sales = :sales')
            ->setParameter('sales', $sales ->getId())
            ->getQuery()->getSingleResult();
        if($import == 'import'){
            $sales->setPayment($total['total']);
        }


        if($total['total'] > 0){
	        $subTotal = $total['total'];
            $sales->setSubTotal($total['total']);
            $sales->setTotal($total['total'] + $sales->getVat());
            $sales->setDue($total['total']+ $sales->getVat());
            $sales->setTotalItem($total['totalItem']);
	        $sales->setDiscount($this->getUpdateDiscount($sales,$subTotal));
	        $sales->setTotal(floor($subTotal - $sales->getDiscount()));
	        $sales->setDue(floor($subTotal - $sales->getDiscount()));
        }else{
            $sales->setSubTotal(0);
            $sales->setTotal(0);
            $sales->setDue(0);
            $sales->setTotalItem(0);
            $sales->setDiscount(0);
            $sales->setVat(0);
        }
	    if ($sales->getTallyConfig()->getVatEnable() == 1 && $sales->getTallyConfig()->getVatPercentage() > 0) {
		    $totalAmount = $sales->getTotal();
		    $vat = $this->getCulculationVat($sales,$totalAmount);
		    $sales->setVat($vat);
	    }

        $em->persist($sales);
        $em->flush();

        return $sales;

    }

	public function getUpdateDiscount(Sales $invoice,$subTotal)
	{
		if($invoice->getDiscountType() == 'Flat'){
			$discount = $invoice->getDiscountCalculation();
		}else{
			$discount = ($subTotal * $invoice->getDiscountCalculation())/100;
		}
		return round($discount,2);
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

        $inventory = $user->getGlobalOption()->getTallyConfig();
        $branch = $user->getProfile()->getBranches();

        $qb = $this->_em->createQueryBuilder();
        $datetime = new \DateTime("now");
        $today_startdatetime = $datetime->format('Y-m-d 00:00:00');
        $today_enddatetime = $datetime->format('Y-m-d 23:59:59');
        $qb->from('TallyBundle:Sales','s');
        $qb->select('sum(s.subTotal) as subTotal , sum(s.total) as total , count(s.id) as totalVoucher, sum(s.due) as totalDue, sum(s.discount) as totalDiscount, sum(s.vat) as totalVat');
        $qb->where('s.config = :inventory')
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

    public function reportSalesOverview(User $user ,$data)
    {

        $userBranch = $user->getProfile()->getBranches();
        $inventory =  $user->getGlobalOption()->getTallyConfig()->getId();

        $qb = $this->createQueryBuilder('s');
      //  $qb->join('s.salesItems','si');
        $qb->select('sum(s.subTotal) as subTotal , sum(s.total) as total ,sum(s.payment) as totalPayment , count(s.id) as totalVoucher, count(s.totalItem) as totalItem, sum(s.due) as totalDue, sum(s.discount) as totalDiscount, sum(s.vat) as totalVat');
       // $qb->addSelect('SUM(si.quantity * si.purchasePrice) as purchasePrice');
       // $qb->addSelect('SUM(si.quantity) as quantity');
        $qb->where('s.config = :inventory');
        $qb->setParameter('inventory', $inventory);
        $qb->andWhere('s.process = :process');
        $qb->setParameter('process', 'Done');
        $this->handleSearchBetween($qb,$data);
        if ($userBranch){
            $qb->andWhere("s.branches = :branch");
            $qb->setParameter('branch', $userBranch);
        }
        return $qb->getQuery()->getOneOrNullResult();
    }

    public  function reportSalesItemPurchaseSalesOverview(User $user, $data = array()){

        $userBranch = $user->getProfile()->getBranches();
        $inventory =  $user->getGlobalOption()->getTallyConfig()->getId();

        $qb = $this->createQueryBuilder('s');
        $qb->join('s.salesItems','si');
        $qb->select('SUM(si.quantity) AS quantity');
        $qb->addSelect('COUNT(si.id) AS totalItem');
        $qb->addSelect('SUM(si.quantity * si.purchasePrice) AS purchasePrice');
        $qb->addSelect('SUM(si.quantity * si.salesPrice) AS salesPrice');
        $qb->where('s.config = :inventory');
        $qb->setParameter('inventory', $inventory);
        $qb->andWhere('s.process = :process');
        $qb->setParameter('process', 'Done');
        $this->handleSearchBetween($qb,$data);
        if ($userBranch){
            $qb->andWhere("s.branches = :branch");
            $qb->setParameter('branch', $userBranch);
        }
        $result = $qb->getQuery()->getOneOrNullResult();
        return $result;
    }

    public function reportSalesTransactionOverview(User $user , $data = array())
    {

        $userBranch = $user->getProfile()->getBranches();
        $inventory =  $user->getGlobalOption()->getTallyConfig()->getId();

        $qb = $this->createQueryBuilder('s');
        $qb->join('s.transactionMethod','t');
        $qb->select('t.name as transactionName , sum(s.subTotal) as subTotal , sum(s.total) as total ,sum(s.payment) as totalPayment , count(s.id) as totalVoucher, sum(s.due) as totalDue, sum(s.discount) as totalDiscount, sum(s.vat) as totalVat');
        $qb->where('s.config = :inventory');
        $qb->setParameter('inventory', $inventory);
        $qb->andWhere('s.process = :process');
        $qb->setParameter('process', 'Done');
        $this->handleSearchBetween($qb,$data);
        if ($userBranch){
            $qb->andWhere("s.branches = :branch");
            $qb->setParameter('branch', $userBranch);
        }
        $qb->groupBy("s.transactionMethod");
        $res = $qb->getQuery();
        return $result = $res->getArrayResult();
    }

    public function reportSalesModeOverview(User $user,$data)
    {
        $userBranch = $user->getProfile()->getBranches();
        $inventory =  $user->getGlobalOption()->getTallyConfig()->getId();

        $qb = $this->createQueryBuilder('s');
        $qb->select('s.salesMode as name , sum(s.subTotal) as subTotal , sum(s.total) as total ,sum(s.payment) as totalPayment , count(s.id) as totalVoucher, sum(s.due) as totalDue, sum(s.discount) as totalDiscount, sum(s.vat) as totalVat');
        $qb->where('s.config = :inventory');
        $qb->setParameter('inventory', $inventory);
        $qb->andWhere('s.process = :process');
        $qb->setParameter('process', 'Done');
        $this->handleSearchBetween($qb,$data);
        if ($userBranch){
            $qb->andWhere("s.branches = :branch");
            $qb->setParameter('branch', $userBranch);
        }
        $qb->groupBy("s.salesMode");
        $res = $qb->getQuery();
        return $result = $res->getArrayResult();
    }

    public function reportSalesProcessOverview(User $user,$data)
    {

        $userBranch = $user->getProfile()->getBranches();
        $inventory =  $user->getGlobalOption()->getTallyConfig()->getId();

        $qb = $this->createQueryBuilder('s');
        $qb->select('s.process as name , sum(s.subTotal) as subTotal , sum(s.total) as total ,sum(s.payment) as totalPayment , count(s.id) as totalVoucher, sum(s.due) as totalDue, sum(s.discount) as totalDiscount, sum(s.vat) as totalVat');
        $qb->where('s.config = :inventory');
        $qb->setParameter('inventory', $inventory);
        $this->handleSearchBetween($qb,$data);
        if ($userBranch){
            $qb->andWhere("s.branches = :branch")->setParameter('branch', $userBranch);
        }
        $qb->groupBy("s.process");
        $res = $qb->getQuery();
        return $result = $res->getArrayResult();
    }

    public  function reportSalesItem(User $user , $data = ''){

        $userBranch = $user->getProfile()->getBranches();
        $inventory =  $user->getGlobalOption()->getTallyConfig()->getId();
        $qb = $this->createQueryBuilder('s');
        $qb->join('s.salesItems','si');
        $qb->join('si.item','item');
        $qb->leftJoin('si.purchaseItem','pi');
        $qb->select('s.created AS created');
        $qb->addSelect('s.invoice AS invoice');
        $qb->addSelect('si.quantity AS quantity');
        $qb->addSelect('si.purchasePrice');
        $qb->addSelect('si.salesPrice');
        $qb->addSelect('item.name AS name');
        $qb->addSelect('pi.barcode AS barcode');
        $qb->where("s.config = :inventory");
        $qb->setParameter('inventory', $inventory);
        $qb->andWhere('s.process = :process');
        $qb->setParameter('process', 'Done');
        $this->handleSearchBetween($qb,$data);
        if ($userBranch){
            $qb->andWhere("s.branches = :branch");
            $qb->setParameter('branch', $userBranch);
        }
        $qb->orderBy('s.created','DESC');
        $result = $qb->getQuery();
        return $result;
    }

    public  function reportSalesItemDetails(User $user, $data=''){
        $userBranch = $user->getProfile()->getBranches();
        $inventory =  $user->getGlobalOption()->getTallyConfig()->getId();
        $qb = $this->_em->createQueryBuilder();
        $qb->from('TallyBundle:SalesItem','si');
        $qb->join('si.sales','s');
        $qb->leftJoin('s.customer','customer');
        $qb->join('si.item','item');
        $qb->join('si.purchaseItem','pi');
        $qb->leftJoin('pi.purchase','purchase');
        $qb->leftJoin('purchase.vendor','vendor');
        $qb->select('s.created AS salesCreated');
        $qb->addSelect('customer.name AS customerName');
        $qb->addSelect('s.invoice AS salesInvoice');
        $qb->addSelect('pi.barcode AS barcode');
        $qb->addSelect('pi.expiredDate AS purchaseExpiredDate');
        $qb->addSelect('purchase.grn AS purchaseGrn');
        $qb->addSelect('vendor.vendorCode AS vendorCode');
        $qb->addSelect('si.assuranceType AS assuranceType');
        $qb->addSelect('si.assuranceToCustomer AS assuranceToCustomer');
        $qb->addSelect('si.serialNo AS serialNo');
        $qb->addSelect('si.quantity AS quantity');
        $qb->addSelect('si.salesPrice AS salesPrice');
        $qb->addSelect('item.sku AS name');
        $qb->where("s.config = :inventory");
        $qb->setParameter('inventory', $inventory);
        $qb->andWhere('s.process = :process');
        $qb->setParameter('process', 'Done');
        $this->handleSearchBetween($qb,$data);
        if ($userBranch){
            $qb->andWhere("s.branches = :branch");
            $qb->setParameter('branch', $userBranch);
        }
        $qb->orderBy('s.created','DESC');
        $result = $qb->getQuery();
        return $result;
    }

    public function todaySales(User $user , $mode = '')
    {

        $inventory = $user->getGlobalOption()->getTallyConfig();
        $branch = $user->getProfile()->getBranches();

        $qb = $this->_em->createQueryBuilder();
        $datetime = new \DateTime("now");
        $today_startdatetime = $datetime->format('Y-m-d 00:00:00');
        $today_enddatetime = $datetime->format('Y-m-d 23:59:59');
        $qb->from('TallyBundle:Sales','s');
        $qb->select('s')
            ->where('s.config = :inventory')
            ->andWhere('s.salesMode =:mode')
            ->andWhere('s.created >= :today_startdatetime')
            ->andWhere('s.created <= :today_enddatetime');

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
        $qb->from('TallyBundle:Sales','sales');
        $qb->select('sales');
        $qb->innerJoin('sales.salesItems','salesItems');
        $qb->innerJoin('salesItems.purchaseItem','purchaseitem');
        $qb->where("sales.config = :inventory");
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
        $vat = ( ($totalAmount * (int)$sales->getTallyConfig()->getVatPercentage())/100 );
        return round($vat);
    }

}
