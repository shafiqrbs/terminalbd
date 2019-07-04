<?php

namespace Appstore\Bundle\MedicineBundle\Repository;
use Appstore\Bundle\AccountingBundle\Entity\AccountSales;
use Appstore\Bundle\InventoryBundle\Entity\SalesItem;
use Appstore\Bundle\MedicineBundle\Entity\MedicineAndroidProcess;
use Appstore\Bundle\MedicineBundle\Entity\MedicineConfig;
use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSales;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSalesItem;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;


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

    public function androidDeviceSalesOverview(GlobalOption $option ,$data)
    {


        $config =  $option->getMedicineConfig()->getId();
        $qb = $this->createQueryBuilder('s');
        $qb->select('sum(s.netTotal) as total ,sum(s.received) as salesReceive , count(s.id) as voucher');
        $qb->where('s.medicineConfig = :config')->setParameter('config', $config);
        $qb->andWhere('s.process = :process')->setParameter('process', 'Done');
        $qb->andWhere('s.androidDevice = :device')->setParameter('device', $data['device']);
        $this->handleSearchBetween($qb,$data);
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

    public function insertApiSales(GlobalOption $option,MedicineAndroidProcess $process)
    {
        $em = $this->_em;

            $items = json_decode($process->getJsonItem(),true);
            foreach ($items as $item):
                $sales = new MedicineSales();
                $sales->setMedicineConfig($option->getMedicineConfig());
                $sales->setAndroidDevice($process->getAndroidDevice());
                $sales->setAndroidProcess($process);
                $sales->setInvoice($item['invoiceId']);
                $sales->setDeviceSalesId($item['invoiceId']);
                $sales->setSubTotal($item['subTotal']);
                if(isset($item['discount']) and $item['discount'] > 0){
                    $sales->setDiscount($item['discount']);
                    $sales->setDiscountType($item['discountType']);
                    $sales->setDiscountCalculation($item['discountCalculation']);
                }
                $sales->setNetTotal($item['total']);
                if($item['total'] < $item['receive']){
                    $sales->setReceived($item['total']);
                }else{
                    $sales->setReceived($item['receive']);
                }
                $sales->setDue($item['due']);
                $sales->setVat($item['vat']);
                if($item['transactionMethod']){
                    $method = $em->getRepository('SettingToolBundle:TransactionMethod')->findOneBy(array('slug'=>$item['transactionMethod']));
                    $sales->setTransactionMethod($method);
                }elseif (empty($item['transactionMethod']) and $sales->getReceived() > 0){
                    $method = $em->getRepository('SettingToolBundle:TransactionMethod')->findOneBy(array('slug'=>'cash'));
                    $sales->setTransactionMethod($method);
                }
                if(isset($item['bankAccount']) and $item['bankAccount'] > 0 ){
                    $bank = $em->getRepository('AccountingBundle:AccountBank')->find($item['bankAccount']);
                    $sales->setAccountBank($bank);
                    $card = $em->getRepository('SettingToolBundle:PaymentCard')->find($item['paymentCard']);
                    $sales->setPaymentCard($card);
                    $sales->setCardNo($item['paymentCardNo']);
                    $sales->setTransactionId($item['transactionId']);
                }
                if(isset($item['mobileBankAccount']) and $item['mobileBankAccount'] > 0){
                    $mobile = $em->getRepository('AccountingBundle:AccountMobileBank')->find($item['mobileBankAccount']);
                    $sales->setAccountMobileBank($mobile);
                    $sales->setPaymentMobile($item['paymentMobile']);
                    $sales->setTransactionId($item['transactionId']);
                }
                if(isset($item['customerName']) and $item['customerName'] and isset($item['customerMobile']) and $item['customerMobile']){
                    $customer = $em->getRepository('DomainUserBundle:Customer')->newExistingCustomerForSales($option,$item['customerMobile'],$item);
                    $sales->setCustomer($customer);
                }elseif(($item['customerId']) and $item['customerId'] > 0 ){
                    $customer = $em->getRepository('DomainUserBundle:Customer')->find($item['customerId']);
                    $sales->setCustomer($customer);
                }elseif(empty($item['customerId']) and empty($item['customerName']) ) {
                    $customer = $em->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $option, 'name' => 'Default'));
                    $sales->setCustomer($customer);
                }
                if(($item['createdBy']) and $item['createdBy'] > 0){
                    $createdBy = $em->getRepository('UserBundle:User')->find($item['createdBy']);
                    $sales->setCreatedBy($createdBy);
                }
                if(($item['salesBy']) and $item['salesBy'] > 0){
                     $salesBy = $em->getRepository('UserBundle:User')->find($item['salesBy']);
                     $sales->setSalesBy($salesBy);
                }

                $created = new \DateTime($item['created']);
                $sales->setCreated($created);
                $sales->setUpdated($created);
                $sales->setProcess("Device");
                $sales->setPaymentStatus("Paid");
                $em->persist($sales);
                $em->flush();

           endforeach;
           $this->insertApiSalesItem( $option, $process);
         /*
          $countRecords = $this->countNumberSalesSubItem($process->getId());
          $countRecords = $this->countNumberSalesItem($process->getId());
          if($process->getItemCount() == $countRecords){
                $this->insertApiSalesItem( $option, $process);
           }elseif( $countRecords > 0 and $process->getItemCount() != $countRecords){
               $batch = $process->getId();
               $remove = $em->createQuery("DELETE MedicineBundle:MedicineSales e WHERE e.androidProcess = {$batch}");
               $remove->execute();
           }else{
               return "Failed";
           }*/

    }

    public function countNumberSalesItem($batch)
    {
        $em = $this->_em;
        $total = $em->createQueryBuilder()
            ->from('MedicineBundle:MedicineSales','si')
            ->select('count(si.id) as totalCount')
            ->where("si.androidProcess={$batch}")
            ->getQuery()->getOneOrNullResult();
        return $total['totalCount'];

    }

    public function insertApiSalesItem(GlobalOption $option,MedicineAndroidProcess $process){

        $em = $this->_em;
        $conf = $option->getMedicineConfig();

        $items = json_decode($process->getJsonSubItem(),true);

        foreach ($items as $item):

            $deviceSalesId = $item['salesId'];
            $sales = $em->getRepository('MedicineBundle:MedicineSales')->findOneBy(array('medicineConfig' => $conf,'deviceSalesId' => $deviceSalesId));
            if($sales){
                $salesItem = new MedicineSalesItem();
                $salesItem->setAndroidProcess($process);
                $salesItem->setMedicineSales($sales);
                $stockId = $em->getRepository('MedicineBundle:MedicineStock')->find($item['stockId']);
                if($stockId){
                    $salesItem->setMedicineStock($stockId);
                    $salesItem->setPurchasePrice($stockId->getAveragePurchasePrice());
                }
                $salesItem->setQuantity($item['quantity']);
                if(isset($item['unitPrice']) and $item['unitPrice']) {
                    $salesItem->setSalesPrice(floatval($item['unitPrice']));
                }
                $salesItem->setSubTotal($item['subTotal']);
                $em->persist($salesItem);
                $em->flush();
            }
        endforeach;

        /*$countRecords = $this->countNumberSalesSubItem($process->getId());
        if($process->getItemCount() == $countRecords){
            $this->insertApiSalesItem( $option, $process);
        }elseif( $countRecords > 0 and $process->getItemCount() != $countRecords){
            $batch = $process->getId();
            $remove = $em->createQuery("DELETE MedicineBundle:MedicineSalesItem e WHERE e.androidProcess = {$batch}");
            $remove->execute();
        }else{
            return "Failed";
        }*/

    }

    public function countNumberSalesSubItem($batch)
    {
        $em = $this->_em;
        $total = $em->createQueryBuilder()
            ->from('MedicineBundle:MedicineSalesItem','si')
            ->select('count(si.id) as totalCount')
            ->where("si.androidProcess={$batch}")
            ->getQuery()->getOneOrNullResult();
        return $total['totalCount'];

    }

    public function androidDeviceSales($config)
    {

        $qb = $this->createQueryBuilder('e');
        $qb->leftJoin('e.createdBy', 'u');
        $qb->join('e.androidDevice','a');
        $qb->select('u.username as salesBy');
        $qb->addSelect('a.id as deviceId','a.device as device');
        $qb->addSelect('COUNT(e.id) as totalInvoice','SUM(e.subTotal) as subTotal','SUM(e.discount) as discount','SUM(e.netTotal) as total','SUM(e.received) as received','SUM(e.due) as due');
        $qb->where('e.medicineConfig = :config')->setParameter('config', $config);
        $qb->andWhere('e.deviceApproved = :deviceApproved')->setParameter('deviceApproved', 0);
        $compareTo = new \DateTime("now");
        $created =  $compareTo->format('Y-m-d 00:00:00');
        $qb->andWhere("e.created >= :createdStart")->setParameter('createdStart', $created);
        $createdEnd =  $compareTo->format('Y-m-d 23:59:59');
        $qb->andWhere("e.created <= :createdEnd")->setParameter('createdEnd', $createdEnd);
        $qb->groupBy('e.androidDevice');
        $qb->groupBy('e.createdBy');
        $result = $qb->getQuery()->getArrayResult();
        return $result;

    }

    public function androidDeviceSalesProcess($device)
    {
        $em = $this->_em;
        $entities = $this->findBy(array('androidDevice' => $device,'deviceApproved' => 0));

        /* @var $entity MedicineSales */

        foreach ($entities as $entity){

            $entity->setProcess('Approved');
            $entity->setApprovedBy($entity->getCreatedBy());
            $entity->setUpdated($entity->getCreated());
            $entity->setDeviceApproved(true);
            $em->flush();
            $em->getRepository('MedicineBundle:MedicineStock')->getSalesUpdateQnt($entity);
            $accountSales = $em->getRepository('AccountingBundle:AccountSales')->insertMedicineAccountInvoice($entity);
            $em->getRepository('AccountingBundle:Transaction')->salesGlobalTransaction($accountSales);
        }
    }


}
