<?php

namespace Appstore\Bundle\MedicineBundle\Repository;
use Appstore\Bundle\AccountingBundle\Entity\AccountSales;
use Appstore\Bundle\MedicineBundle\Entity\MedicineConfig;
use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSales;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;


/**
 * MedicineSalesRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MedicineSalesRepository extends EntityRepository
{

    public function getLastInvoice(MedicineConfig $config)
    {
        $entity = $this->findOneBy(
            array('medicineConfig' => $config),
            array('id' => 'DESC')
        );
        return $entity;
    }

    /**
     * @param $qb
     * @param $data
     */

    protected function handleSearchBetween($qb,$data)
    {

        $invoice = isset($data['invoice'])? $data['invoice'] :'';
        $transactionMethod = isset($data['transactionMethod'])? $data['transactionMethod'] :'';
        $salesBy = isset($data['salesBy'])? $data['salesBy'] :'';
        $paymentStatus = isset($data['paymentStatus'])? $data['paymentStatus'] :'';
        $process = isset($data['process'])? $data['process'] :'';
        $customer = isset($data['customer'])? $data['customer'] :'';
        $customerName = isset($data['name'])? $data['name'] :'';
        $customerMobile = isset($data['mobile'])? $data['mobile'] :'';
        $createdStart = isset($data['startDate'])? $data['startDate'] :'';
	    $createdEnd = isset($data['endDate'])? $data['endDate'] :'';
        if (!empty($invoice)) {
            $qb->andWhere($qb->expr()->like("s.invoice", "'%$invoice%'"  ));
        }
        if (!empty($customerName)) {
            $qb->join('s.customer','c');
            $qb->andWhere($qb->expr()->like("c.name", "'$customerName%'"  ));
        }

        if (!empty($customerMobile)) {
            $qb->join('s.customer','c');
            $qb->andWhere($qb->expr()->like("c.mobile", "'%$customerMobile%'"  ));
        }

		if (!empty($customer)) {
            $qb->join('s.customer','c');
            $qb->andWhere($qb->expr()->like("c.mobile", "'%$customer%'"  ));
        }

        if (!empty($createdStart)) {
            $compareTo = new \DateTime($createdStart);
            $created =  $compareTo->format('Y-m-d 00:00:00');
            $qb->andWhere("s.created >= :createdStart");
            $qb->setParameter('createdStart', $created);
        }

        if (!empty($createdEnd)) {
            $compareTo = new \DateTime($createdEnd);
	        $createdEnd =  $compareTo->format('Y-m-d 23:59:59');
            $qb->andWhere("s.created <= :createdEnd");
            $qb->setParameter('createdEnd', $createdEnd);
        }
        if(!empty($salesBy)){
            $qb->join("s.salesBy",'un');
            $qb->andWhere("un.username = :username");
            $qb->setParameter('username', $salesBy);
        }
	    if(!empty($paymentStatus)){
            $qb->andWhere("s.paymentStatus = :status");
            $qb->setParameter('status', $paymentStatus);
        }
        if(!empty($transactionMethod)){
            $qb->andWhere("s.transactionMethod = :method");
            $qb->setParameter('method', $transactionMethod);
        }


    }

	protected function handleSearchStockBetween($qb,$data)
	{

		$grn = isset($data['grn'])? $data['grn'] :'';
		$vendor = isset($data['vendor'])? $data['vendor'] :'';
		$createdStart = isset($data['startDate'])? $data['startDate'] :'';
		$createdEnd = isset($data['endDate'])? $data['endDate'] :'';
		$name = isset($data['name'])? $data['name'] :'';
		$rackNo = isset($data['rackNo'])? $data['rackNo'] :'';
		$mode = isset($data['mode'])? $data['mode'] :'';
		$sku = isset($data['sku'])? $data['sku'] :'';
		$brandName = isset($data['brandName'])? $data['brandName'] :'';

		if (!empty($name)) {
			$qb->andWhere($qb->expr()->like("mds.name", "'%$name%'"  ));
		}
		if (!empty($sku)) {
			$qb->andWhere($qb->expr()->like("mds.sku", "'%$sku%'"  ));
		}
		if (!empty($brandName)) {
			$qb->andWhere($qb->expr()->like("mds.brandName", "'%$brandName%'"  ));
		}
		if(!empty($rackNo)){
			$qb->andWhere("mds.rackNo = :rack")->setParameter('rack', $rackNo);
		}
		if (!empty($createdStart)) {
			$compareTo = new \DateTime($createdStart);
			$created =  $compareTo->format('Y-m-d 00:00:00');
			$qb->andWhere("s.created >= :createdStart");
			$qb->setParameter('createdStart', $created);
		}

		if (!empty($createdEnd)) {
			$compareTo = new \DateTime($createdEnd);
			$createdEnd =  $compareTo->format('Y-m-d 23:59:59');
			$qb->andWhere("s.created <= :createdEnd");
			$qb->setParameter('createdEnd', $createdEnd);
		}

	}

    public function handleDateRangeFind($qb,$data)
    {
        if(empty($data)){
            $datetime = new \DateTime("now");
            $data['startDate'] = $datetime->format('Y-m-d 00:00:00');
            $data['endDate'] = $datetime->format('Y-m-d 23:59:59');
        }else{
            $data['startDate'] = date('Y-m-d',strtotime($data['startDate']));
            $data['endDate'] = date('Y-m-d',strtotime($data['endDate']));
        }

        if (!empty($data['startDate']) ) {
            $qb->andWhere("it.created >= :startDate");
            $qb->setParameter('startDate', $data['startDate'].' 00:00:00');
        }

        if (!empty($data['endDate'])) {
            $qb->andWhere("it.created <= :endDate");
            $qb->setParameter('endDate', $data['endDate'].' 23:59:59');
        }
    }

    public function updateSalesPaymentReceive(AccountSales $accountSales)
    {
        /* @var MedicineSales $sales **/

        $sales = $accountSales->getMedicineSales();
        $received = $sales->getReceived() + $accountSales->getAmount();
        $sales->setReceived($received);
        $sales->setDue($sales->getDue() - $accountSales->getAmount());
        if($sales->getDue() == 0 ){
            $sales->setPaymentStatus('Paid');
        }
        $this->_em->flush();
    }


    public function invoiceLists(User $user, $data)
    {
        $config = $user->getGlobalOption()->getMedicineConfig()->getId();

        $qb = $this->createQueryBuilder('s');
        $qb->where('s.medicineConfig = :config')->setParameter('config', $config) ;
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('s.created','DESC');
        $qb->getQuery();
        return  $qb;
    }

    public function updateMedicineSalesTotalPrice(MedicineSales $invoice)
    {
        $em = $this->_em;
        $total = $em->createQueryBuilder()
            ->from('MedicineBundle:MedicineSalesItem','si')
            ->select('sum(si.subTotal) as subTotal')
            ->where('si.medicineSales = :invoice')
            ->setParameter('invoice', $invoice ->getId())
            ->getQuery()->getOneOrNullResult();

        $subTotal = !empty($total['subTotal']) ? $total['subTotal'] :0;
        if($subTotal > 0){
            $invoice->setSubTotal(floor($subTotal));
            $invoice->setDiscount($this->getUpdateDiscount($invoice,$subTotal));
            $invoice->setNetTotal(floor($subTotal - $invoice->getDiscount()));
            $invoice->setDue(floor($subTotal - $invoice->getDiscount()));
        }else{
            $invoice->setSubTotal(0);
            $invoice->setTotal(0);
            $invoice->setNetTotal(0);
            $invoice->setDue(0);
            $invoice->setDiscount(0);
            $invoice->setVat(0);
        }
        $em->persist($invoice);
        $em->flush();
        return $invoice;

    }

    public function getUpdateDiscount(MedicineSales $invoice,$subTotal)
    {
        if($invoice->getDiscountType() == 'flat'){
            $discount = $invoice->getDiscountCalculation();
        }else{
            $discount = ($subTotal * $invoice->getDiscountCalculation())/100;
        }
        return round($discount,2);
    }

    public function updatePaymentReceive(MedicineSales $invoice)
    {
        $em = $this->_em;
        $res = $em->createQueryBuilder()
            ->from('MedicineBundle:MedicineTreatmentPlan','si')
            ->select('sum(si.price) as subTotal ,sum(si.payment) as payment ,sum(si.discount) as discount')
            ->where('si.medicineInvoice = :invoice')
            ->setParameter('invoice', $invoice ->getId())
            ->andWhere('si.status = :status')
            ->setParameter('status', 1)
            ->getQuery()->getOneOrNullResult();
        $subTotal = !empty($res['subTotal']) ? $res['subTotal'] :0;
        $payment = !empty($res['payment']) ? $res['payment'] :0;
        $discount = !empty($res['discount']) ? $res['discount'] :0;
        $invoice->setSubTotal($subTotal);
        $invoice->setPayment($payment);
        $invoice->setDiscount($discount);
        $invoice->setTotal($invoice->getSubTotal() - $discount);
        $invoice->setDue($invoice->getTotal() - $invoice->getPayment());
        $em->flush();

    }

    public function getCulculationVat(MedicineSales $sales,$totalAmount)
    {
        $vat = ( ($totalAmount * (int)$sales->getMedicineConfig()->getVatPercentage())/100 );
        return round($vat);
    }

    public function reportSalesOverview(User $user ,$data)
    {

        $userBranch = $user->getProfile()->getBranches();
        $config =  $user->getGlobalOption()->getMedicineConfig()->getId();

        $qb = $this->createQueryBuilder('s');
        $qb->select('sum(s.subTotal) as subTotal , sum(s.netTotal) as total ,sum(s.received) as totalPayment , count(s.id) as totalVoucher, sum(s.due) as totalDue, sum(s.discount) as totalDiscount, sum(s.vat) as totalVat');
        $qb->where('s.medicineConfig = :config');
        $qb->setParameter('config', $config);
        $qb->andWhere('s.process = :process');
        $qb->setParameter('process', 'Done');
        $this->handleSearchBetween($qb,$data);
        if ($userBranch){
            $qb->andWhere("s.branch = :branch");
            $qb->setParameter('branch', $userBranch);
        }
        return $qb->getQuery()->getOneOrNullResult();
    }

    public  function reportSalesItemPurchaseSalesOverview(User $user, $data = array()){

        $userBranch = $user->getProfile()->getBranches();
        $config =  $user->getGlobalOption()->getMedicineConfig()->getId();

        $qb = $this->createQueryBuilder('s');
        $qb->join('s.medicineSalesItems','si');
        $qb->select('SUM(si.quantity) AS quantity');
        $qb->addSelect('COUNT(si.id) AS totalItem');
        $qb->addSelect('SUM(si.quantity * si.purchasePrice) AS totalPurchase');
        $qb->addSelect('SUM(si.quantity * si.salesPrice) AS totalSales');
        $qb->where('s.medicineConfig = :config');
        $qb->setParameter('config', $config);
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
        $config =  $user->getGlobalOption()->getMedicineConfig()->getId();

        $qb = $this->createQueryBuilder('s');
        $qb->join('s.transactionMethod','t');
        $qb->select('t.name as transactionName , sum(s.subTotal) as subTotal , sum(s.netTotal) as total ,sum(s.received) as totalPayment , count(s.id) as totalVoucher, sum(s.due) as totalDue, sum(s.discount) as totalDiscount, sum(s.vat) as totalVat');
        $qb->where('s.medicineConfig = :config');
        $qb->setParameter('config', $config);
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

    public function reportSalesProcessOverview(User $user,$data)
    {

        $userBranch = $user->getProfile()->getBranches();
        $config =  $user->getGlobalOption()->getMedicineConfig()->getId();

        $qb = $this->createQueryBuilder('s');
        $qb->select('s.process as name , sum(s.subTotal) as subTotal , sum(s.netTotal) as total ,sum(s.received) as totalPayment , count(s.id) as totalVoucher, sum(s.due) as totalDue, sum(s.discount) as totalDiscount, sum(s.vat) as totalVat');
        $qb->where('s.medicineConfig = :config');
        $qb->setParameter('config', $config);
        $this->handleSearchBetween($qb,$data);
        if ($userBranch){
            $qb->andWhere("s.branches = :branch")->setParameter('branch', $userBranch);
        }
        $qb->groupBy("s.process");
        $res = $qb->getQuery();
        return $result = $res->getArrayResult();
    }

	public function medicineSalesMonthly(User $user , $data =array())
	{

		$config =  $user->getGlobalOption()->getMedicineConfig()->getId();
		$compare = new \DateTime();
		$year =  $compare->format('Y');
		$year = isset($data['year'])? $data['year'] :$year;
		$sql = "SELECT MONTH (sales.created) as month,SUM(sales.netTotal) AS total
                FROM medicine_sales as sales
                WHERE sales.medicineConfig_id = :config AND sales.process = :process  AND YEAR(sales.created) =:year
                GROUP BY month ORDER BY month ASC";
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->bindValue('config', $config);
		$stmt->bindValue('process', 'Done');
		$stmt->bindValue('year', $year);
		$stmt->execute();
		$result =  $stmt->fetchAll();
		return $result;


	}

    public function salesReport( User $user , $data)
    {

        $userBranch = $user->getProfile()->getBranches();
        $config =  $user->getGlobalOption()->getMedicineConfig()->getId();

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
        $qb->addSelect('(s.netTotal) as total');
        $qb->addSelect('(s.received) as payment');
        $qb->addSelect('(s.discount) as discount');
        $qb->addSelect('(s.vat) as vat');
        $qb->where('s.medicineConfig = :config');
        $qb->setParameter('config', $config);
        $qb->andWhere('s.process = :process');
        $qb->setParameter('process', 'Done');
        if(!empty($userBranch)){
            $qb->andWhere("s.branches =".$userBranch);
        }
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('s.updated','DESC');
        $result = $qb->getQuery();
        return $result;

    }

    public function salesUserReport( User $user , $data)
    {

        $userBranch = $user->getProfile()->getBranches();
        $config =  $user->getGlobalOption()->getMedicineConfig()->getId();

        $qb = $this->createQueryBuilder('s');
        $qb->leftJoin('s.salesBy', 'u');
        $qb->select('u.username as salesBy');
        $qb->addSelect('u.id as userId');
        $qb->addSelect('SUM(s.due) as due');
        $qb->addSelect('SUM(s.subTotal) as subTotal');
        $qb->addSelect('SUM(s.netTotal) as total');
        $qb->addSelect('SUM(s.received) as payment');
        $qb->addSelect('SUM(s.discount) as discount');
        $qb->addSelect('SUM(s.vat) as vat');
        $qb->where('s.medicineConfig = :config');
        $qb->setParameter('config', $config);
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

    public function salesPurchasePriceReport(User $user,$data,$x)
    {

        $userBranch = $user->getProfile()->getBranches();
        $config =  $user->getGlobalOption()->getMedicineConfig()->getId();

        $ids = array();
        foreach ($x as $y){
            $ids[]=$y['id'];
        }

        $qb = $this->createQueryBuilder('s');
        $qb->join('s.medicineSalesItems','si');
        $qb->select('s.id as salesId');
        $qb->addSelect('SUM(si.quantity * si.purchasePrice ) AS totalPurchaseAmount');
        $qb->where('s.medicineConfig = :config');
        $qb->setParameter('config', $config);
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

	public  function reportSalesItem(User $user, $data=''){

		$userBranch = $user->getProfile()->getBranches();
		$config =  $user->getGlobalOption()->getMedicineConfig()->getId();
		$group = isset($data['group']) ? $data['group'] :'medicineStock';

		$qb = $this->createQueryBuilder('s');
		$qb->join('s.medicineSalesItems','si');
		$qb->join('si.medicineStock','mds');
		$qb->select('SUM(si.quantity) AS quantity');
		$qb->addSelect('SUM(si.quantity * si.discountPrice ) AS salesPrice');
		$qb->addSelect('SUM(si.quantity * si.purchasePrice ) AS purchasePrice');
		$qb->addSelect('mds.name AS name');
		$qb->where('s.medicineConfig = :config');
		$qb->setParameter('config', $config);
		$qb->andWhere('s.process = :process');
		$qb->setParameter('process', 'Done');
		$qb->groupBy('si.medicineStock');
		$qb->orderBy('mds.name','ASC');
		return $qb->getQuery()->getArrayResult();
	}

    public  function reportSalesStockItem(User $user, $data=''){

        $userBranch = $user->getProfile()->getBranches();
        $config =  $user->getGlobalOption()->getMedicineConfig()->getId();
        $group = isset($data['group']) ? $data['group'] :'medicineStock';

        $qb = $this->createQueryBuilder('s');
        $qb->join('s.medicineSalesItems','si');
        $qb->join('si.medicinePurchaseItem','item');
        $qb->join('si.medicineStock','mds');
        $qb->select('SUM(si.quantity) AS quantity');
        $qb->addSelect('SUM(si.quantity * si.discountPrice ) AS salesPrice');
        $qb->addSelect('SUM(si.quantity * si.purchasePrice ) AS purchasePrice');
        $qb->addSelect('mds.name AS name');
        $qb->where('s.medicineConfig = :config');
        $qb->setParameter('config', $config);
        $qb->andWhere('s.process = :process');
        $qb->setParameter('process', 'Done');
        if($group == 'medicinePurchaseItem') {
            $qb->addSelect('item.barcode AS barcode');
        }
        $this->handleSearchStockBetween($qb,$data);
        if ($userBranch){
            $qb->andWhere("s.branches = :branch");
            $qb->setParameter('branch', $userBranch);
        }
        $qb->groupBy('si.'.$group);
        $qb->orderBy('s.created','DESC');
        return $qb;
    }

    public function monthlySales(User $user , $data =array())
    {

        $userBranch = $user->getProfile()->getBranches();
        $config =  $user->getGlobalOption()->getMedicineConfig()->getId();

        $compare = new \DateTime();
        $year =  $compare->format('Y');
        $year = isset($data['year'])? $data['year'] :$year;
        $sql = "SELECT sales.salesBy_id as salesBy, MONTH (sales.created) as month,SUM(sales.netTotal) AS total
                FROM medicine_sales as sales
                WHERE sales.medicineConfig_id = :config AND sales.process = :process  AND YEAR(sales.created) =:year
                GROUP BY month , salesBy ORDER BY salesBy ASC";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('config', $config);
        $stmt->bindValue('process', 'Done');
        $stmt->bindValue('year', $year);
        $stmt->execute();
        $result =  $stmt->fetchAll();
        return $result;


    }

	public function currentMonthSales(User $user , $data =array())
	{

		$userBranch = $user->getProfile()->getBranches();
		$config =  $user->getGlobalOption()->getMedicineConfig()->getId();

		$compare = new \DateTime();
		$year =  $compare->format('Y');
		$month =  $compare->format('m');
		$year = isset($data['year'])? $data['year'] :$year;

		$sql = "SELECT sales.salesBy_id as salesBy, MONTH (sales.created) as month, SUM(sales.netTotal) AS total
                FROM medicine_sales as sales
                WHERE sales.medicineConfig_id = :config AND sales.process = :process  AND YEAR(sales.created) =:year AND MONTH(sales.created) =:month
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

    public function salesUserPurchasePriceReport(User $user,$data)
    {
        $userBranch = $user->getProfile()->getBranches();
        $config =  $user->getGlobalOption()->getMedicineConfig()->getId();

        $qb = $this->createQueryBuilder('s');
        $qb->leftJoin('s.salesBy', 'u');
        $qb->join('s.medicineSalesItems','si');
        $qb->select('u.username as salesBy');
        $qb->addSelect('SUM(si.quantity * si.purchasePrice ) AS totalPurchaseAmount');
        $qb->where('s.medicineConfig = :config');
        $qb->setParameter('config', $config);
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


}
