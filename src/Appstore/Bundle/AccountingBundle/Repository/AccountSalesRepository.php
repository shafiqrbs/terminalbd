<?php

namespace Appstore\Bundle\AccountingBundle\Repository;
use Appstore\Bundle\AccountingBundle\Entity\AccountSales;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoice;
use Appstore\Bundle\HospitalBundle\Entity\InvoiceTransaction;
use Appstore\Bundle\HotelBundle\Entity\HotelInvoice;
use Appstore\Bundle\HotelBundle\Entity\HotelInvoiceTransaction;
use Appstore\Bundle\InventoryBundle\Entity\Sales;
use Appstore\Bundle\InventoryBundle\Entity\SalesReturn;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSales;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSalesReturn;
use Appstore\Bundle\RestaurantBundle\Entity\Invoice;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * AccountSalesRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AccountSalesRepository extends EntityRepository
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
			$endDate =   isset($data['endDate'])  ? $data['endDate'] : '';
			$mobile =    isset($data['mobile'])? $data['mobile'] :'';
			$customer =    isset($data['customer'])? $data['customer'] :'';
			$invoice =    isset($data['invoice'])? $data['invoice'] :'';
			$accountRefNo =    isset($data['accountRefNo'])? $data['accountRefNo'] :'';
			$medicineInvoice =    isset($data['medicineInvoice'])? $data['medicineInvoice'] :'';
			$businessInvoice =    isset($data['businessInvoice'])? $data['businessInvoice'] :'';
			$transaction =    isset($data['transactionMethod'])? $data['transactionMethod'] :'';
			$account =    isset($data['accountHead'])? $data['accountHead'] :'';
			$sales =    isset($data['sales'])? $data['sales'] :'';

			if (!empty($startDate) ) {
				$start = date('Y-m-d 00:00:00',strtotime($data['startDate']));
				$qb->andWhere("e.updated >= :startDate");
				$qb->setParameter('startDate', $start);
			}
			if (!empty($endDate)) {
				$end = date('Y-m-d 23:59:59',strtotime($data['endDate']));
				$qb->andWhere("e.updated <= :endDate");
				$qb->setParameter('endDate',$end);
			}

			if (!empty($accountRefNo)) {
				$qb->andWhere("e.accountRefNo = :accountRefNo");
				$qb->setParameter('accountRefNo', $accountRefNo);
			}

			if (!empty($transaction)) {
				$qb->andWhere("e.transactionMethod = :transaction");
				$qb->setParameter('transaction', $transaction);
			}

			if (!empty($mobile)) {
				$qb->join('e.customer','c');
				$qb->andWhere("c.mobile = :mobile");
				$qb->setParameter('mobile', $mobile);
			}
			if (!empty($customer)) {
				$qb->join('e.customer','cn');
				$qb->andWhere("cn.name = :name");
				$qb->setParameter('name', $customer);
			}
			if (!empty($invoice)) {
				$qb->join('e.sales','s');
				$qb->andWhere($qb->expr()->like("s.invoice", "'%$invoice%'"  ));
			}
			if (!empty($medicineInvoice)) {
				$qb->join('e.medicineSales','ms');
				$qb->andWhere("ms.invoice = :invoice");
				$qb->setParameter('invoice', $medicineInvoice);
			}

			if (!empty($businessInvoice)) {
				$qb->join('e.businessInvoice','ms');
				$qb->andWhere("ms.invoice = :invoice");
				$qb->setParameter('invoice', $businessInvoice);
			}

			if (!empty($sales)) {
				$qb->andWhere("e.sales = :sales");
				$qb->setParameter('sales', $sales);
			}
			if (!empty($account)) {
				$qb->join('e.accountHead','a');
				$qb->andWhere("a.id = :account");
				$qb->setParameter('account', $account);
			}
		}

	}

	public function receiveModeOverview(User $user,$data)
	{
		$globalOption = $user->getGlobalOption();
		$branch = $user->getProfile()->getBranches();
		$qb = $this->createQueryBuilder('e');
		$qb->leftJoin('e.transactionMethod','t');
		$qb->select('e.processHead, SUM(e.totalAmount) AS total, SUM(e.amount) AS receive, count(e.id) AS totalCount');
		$qb->where("e.globalOption = :globalOption");
		$qb->setParameter('globalOption', $globalOption);
		if (!empty($branch)){
			$qb->andWhere("e.branches = :branch");
			$qb->setParameter('branch', $branch);
		}
		$qb->andWhere("e.process = 'approved'");
		$this->handleSearchBetween($qb,$data);
		$qb->groupBy('e.processHead');
		$result = $qb->getQuery()->getArrayResult();
		return $result;

	}


	public function salesOverview(User $user,$data)
    {
        $globalOption = $user->getGlobalOption();
        $branch = $user->getProfile()->getBranches();
        $qb = $this->createQueryBuilder('e');
        $qb->select('SUM(e.totalAmount) AS totalAmount, SUM(e.amount) AS receiveAmount, SUM(e.amount) AS dueAmount, SUM(e.amount) AS returnAmount ');
        $qb->where("e.globalOption = :globalOption");
        $qb->setParameter('globalOption', $globalOption);
        if (!empty($branch)){
            $qb->andWhere("e.branches = :branch");
            $qb->setParameter('branch', $branch);
        }
        $qb->andWhere("e.process = 'approved'");
        $this->handleSearchBetween($qb,$data);
        $result = $qb->getQuery()->getSingleResult();
        $data =  array('totalAmount'=> $result['totalAmount'],'receiveAmount'=>$result['receiveAmount'],'dueAmount'=>$result['dueAmount'],'returnAmount'=>$result['returnAmount']);
        return $data;

    }



    public function findWithSearch(User $user,$data = '',$process = '')
    {
        $globalOption = $user->getGlobalOption();
        $branch = $user->getProfile()->getBranches();

	    $sort = isset($data['sort'])? $data['sort'] :'e.updated';
	    $direction = isset($data['direction'])? $data['direction'] :'DESC';

        $qb = $this->createQueryBuilder('e');
        $qb->where("e.globalOption = :globalOption");
        $qb->setParameter('globalOption', $globalOption);
        if (!empty($branch)){
            $qb->andWhere("e.branches = :branch");
            $qb->setParameter('branch', $branch);
        }
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('e.updated','DESC');
	    $qb->orderBy("{$sort}",$direction);
        $result = $qb->getQuery();
        return $result;

    }

	public function customerLedger(User $user,$data = '',$process = '')
	{
		$startDate = isset($data['startDate'])  ? $data['startDate'] : '';
		$endDate =   isset($data['endDate'])  ? $data['endDate'] : '';
		$mobile =    isset($data['mobile'])? $data['mobile'] :'';


		$globalOption = $user->getGlobalOption();
		$qb = $this->createQueryBuilder('e');
		$qb->where("e.globalOption = :globalOption");
		$qb->setParameter('globalOption', $globalOption);
		$qb->join('e.customer','c');
		$qb->andWhere("c.mobile = :mobile");
		$qb->setParameter('mobile', $mobile);
		if (!empty($startDate) ) {
			$start = date('Y-m-d 00:00:00',strtotime($data['startDate']));
			$qb->andWhere("e.updated >= :startDate");
			$qb->setParameter('startDate', $start);
		}
		if (!empty($endDate)) {
			$end = date('Y-m-d 23:59:59',strtotime($data['endDate']));
			$qb->andWhere("e.updated <= :endDate");
			$qb->setParameter('endDate',$end);
		}
		$qb->orderBy('e.updated','ASC');
		$result = $qb->getQuery();
		return $result;

	}


    public function lastInsertSales($globalOption,$entity)
    {
        $em = $this->_em;
        $entity = $em->getRepository('AccountingBundle:AccountSales')->findOneBy(
            array('globalOption' => $globalOption,'customer' => $entity->getCustomer(),'process' => 'approved'),
            array('id' => 'DESC')
        );
        if (empty($entity)) {
            return 0;
        }
        return $entity->getBalance();
    }

    public function updateCustomerBalance(AccountSales $accountSales){

        $customer = $accountSales->getCustomer()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->select('SUM(e.totalAmount) AS totalAmount, SUM(e.amount) AS receiveAmount, SUM(e.amount) AS dueAmount, SUM(e.amount) AS returnAmount ');
        $qb->where("e.globalOption = :globalOption");
        $qb->setParameter('globalOption', $accountSales->getGlobalOption()->getId());
        $qb->andWhere("e.process = 'approved'");
        $qb->andWhere("e.customer = :customer");
        $qb->setParameter('customer', $customer);
        $result = $qb->getQuery()->getSingleResult();
        $balance = ($result['totalAmount'] -  $result['receiveAmount']);
        $accountSales->setBalance($balance);
        $this->_em->flush();
        return $accountSales;

    }

    public function customerOutstanding($globalOption, $data = array())
    {
        $mode = isset($data['outstanding'])  ? $data['outstanding'] : '';
        $amount =   isset($data['amount'])  ? $data['amount'] : '';
        $mobile =   isset($data['mobile'])  ? $data['mobile'] : '';

        $outstanding = '';
        $customer = '';
        if($mobile){
            $customer =  "AND subCustomer.mobile LIKE '%{$mobile}%'";
        }
        if($mode == 'Receivable' and $amount !="" ){
           $outstanding =  "AND sales.balance >= {$amount}";
        }
        if($mode == 'Payable' and $amount !="") {
           $outstanding =  "AND sales.balance <= -{$amount}";
        }
        $sql = "SELECT customer.`id` as id,customer.`customerId` as customerId,customer.`name` as customerName , customer.mobile as customerMobile, customer.address as customerAddress, sales.balance as customerBalance
                FROM account_sales as sales
                JOIN Customer as customer ON sales.customer_id = customer.id
                WHERE sales.id IN (
                    SELECT MAX(sub.id)
                    FROM account_sales AS sub
                    JOIN Customer as subCustomer ON sub.customer_id = subCustomer.id
                   WHERE sub.globalOption_id = :globalOption AND sub.process = 'approved' {$customer}
                   GROUP BY sub.customer_id
                ) {$outstanding}
                ORDER BY sales.id DESC";
        $qb = $this->getEntityManager()->getConnection()->prepare($sql);
        $qb->bindValue('globalOption', $globalOption->getId());
        $qb->execute();
        $result =  $qb->fetchAll();
        return $result;

    }

    public function insertAccountSales(Sales $entity) {

	    $em = $this->_em;
	    $accountSales = new AccountSales();
	    $accountSales->setAccountBank( $entity->getAccountBank() );
	    $accountSales->setAccountMobileBank( $entity->getAccountMobileBank() );
	    $accountSales->setGlobalOption( $entity->getInventoryConfig()->getGlobalOption() );
	    $accountSales->setSales( $entity );
	    $accountSales->setCustomer( $entity->getCustomer() );
	    $accountSales->setTotalAmount( $entity->getTotal() );
	    $accountSales->setAmount( $entity->getPayment() );
	    if ( $entity->getPayment() > 0 ) {
		    $accountSales->setTransactionMethod( $entity->getTransactionMethod() );
	    }
        $accountSales->setApprovedBy($entity->getApprovedBy());
        if(!empty($entity->getApprovedBy()->getProfile()->getBranches())){
            $accountSales->setBranches($entity->getApprovedBy()->getProfile()->getBranches());
        }
        $accountSales->setProcessHead('Inventory');
        $accountSales->setProcessType('Sales');
        $accountSales->setProcess('approved');
        $em->persist($accountSales);
        $em->flush();
        $accountSalesClose = $this->updateCustomerBalance($accountSales);
        if($accountSales->getAmount() > 0 ){
	        $this->_em->getRepository('AccountingBundle:AccountCash')->insertSalesCash($accountSales);
        }
        return $accountSalesClose;

    }

	public function resetAccountSales(Sales $entity) {

		$em = $this->_em;
		$exist = $this->findOneBy(array('processHead'=>'inventory','sales'=> $entity,'process'=>'approved'));
		if($exist){
			$accountSales = $exist;
			$accountSales->setTotalAmount( $entity->getTotal() );
			$accountSales->setAmount( $entity->getPayment() );
			$accountSales->setUpdated($accountSales->getCreated());
			if($entity->getPayment() > 0 ) {
				$accountSales->setTransactionMethod( $entity->getTransactionMethod() );
			}
			$em->flush();
			$accountSalesClose = $this->updateCustomerBalance($accountSales);
			if($accountSales->getAmount() > 0 ){
				$this->_em->getRepository('AccountingBundle:AccountCash')->resetSalesCash($accountSales);
			}
			return $accountSalesClose;
		}else{
    		return $this->insertAccountSales($entity);
		}

	}

	public function insertInventoryAccountSalesReturn(SalesReturn $entity)
	{
		$global = $entity->getInventoryConfig()->getGlobalOption();
		$sales = $entity->getSales();
		$em = $this->_em;
		$accountSales = new AccountSales();
		$accountSales->setGlobalOption($global);
		$accountSales->setCustomer($sales->getCustomer());
		$accountSales->setAmount($entity->getTotal());
		$accountSales->setProcessHead('Sales-Return');
		$accountSales->setProcessType('Sales');
		$accountSales->setProcess('approved');
		$accountSales->setApprovedBy($entity->getCreatedBy());
		$accountSales->setSales($sales);
	//	$accountSales->setTransactionMethod($em->getRepository('SettingToolBundle:TransactionMethod')->find(1));
		$em->persist($accountSales);
		$em->flush();
		$this->updateCustomerBalance($accountSales);
		return $accountSales;

	}

    public function reportSalesIncome(User $user,$data)
    {
        if(empty($data)){
            $datetime = new \DateTime("now");
            $data['startDate'] = $datetime->format('Y-m-d 00:00:00');
            $data['endDate'] = $datetime->format('Y-m-d 23:59:59');
        }
        $salesOverview = $this->_em->getRepository('InventoryBundle:Sales')->reportSalesOverview($user,$data);
        $purchasePrice = $this->_em->getRepository('InventoryBundle:Sales')->reportSalesItemPurchaseSalesOverview($user,$data);
        $expenditures = $this->_em->getRepository('AccountingBundle:Transaction')->reportTransactionIncome($user->getGlobalOption(), $accountHeads = array(37), $data);
        $revenues = $this->_em->getRepository('AccountingBundle:Transaction')->reportTransactionIncome($user->getGlobalOption(), $accountHeads = array(20), $data);
        $data =  array('salesAmount' => $salesOverview['total'] ,'purchasePrice' => $purchasePrice['purchasePrice'],'revenues' => $revenues ,'expenditures' => $expenditures,'salesVat' => $salesOverview['totalVat']);
        return $data;

    }

    public function reportMonthlyIncome(User $user,$data)
    {
        if(empty($data)){

            $datetime = new \DateTime("now");
            $data['startDate'] = $datetime->format('Y-m-01 00:00:00');
            $data['endDate'] = $datetime->format('Y-m-t 23:59:59');

        }else{

            $data['startDate'] = date('Y-m-d 00:00:00',strtotime($data['year'].'-'.$data['startMonth']));
            $data['endDate'] = date('Y-m-t 23:59:59',strtotime($data['year'].'-'.$data['endMonth']));
        }

        $salesPrice             = $this->_em->getRepository('InventoryBundle:Sales')->reportSalesOverview($user,$data);
        $purchasePrice          = $this->_em->getRepository('InventoryBundle:Sales')->reportSalesItemPurchaseSalesOverview($user,$data);
        $salesVat               = $this->_em->getRepository('InventoryBundle:SalesItem')->reportProductVat($user, $data);
        $expenditures           = $this->_em->getRepository('AccountingBundle:Transaction')->reportTransactionIncome($user->getGlobalOption(), $accountHeads = array(37,23), $data);
        $revenues               = $this->_em->getRepository('AccountingBundle:Transaction')->reportTransactionIncome($user->getGlobalOption(), $accountHeads = array(20), $data);
        $administrative         = $this->_em->getRepository('AccountingBundle:Transaction')->reportTransactionIncome($user->getGlobalOption(), $accountHeads = array(23), $data);
        $data =  array('salesAmount' => $salesPrice['total'] ,'purchasePrice' => $purchasePrice['purchasePrice'],'revenues' => $revenues ,'expenditures' => $expenditures,'administrative' => $administrative, 'salesVat' => $salesVat);
	    return $data;

    }

    public function reportHmsMonthlyIncome(User $user,$data)
    {
        $globalOption = $user->getGlobalOption();
        if(empty($data)){

            $datetime = new \DateTime("now");
            $data['startDate'] = $datetime->format('Y-m-01 00:00:00');
            $data['endDate'] = $datetime->format('Y-m-t 23:59:59');

        }else{

            $data['startDate'] = date('Y-m-d 00:00:00',strtotime($data['year'].'-'.$data['startMonth']));
            $data['endDate'] = date('Y-m-t 23:59:59',strtotime($data['year'].'-'.$data['endMonth']));
        }


        $qb = $this->createQueryBuilder('e');
        $qb->select('SUM(e.totalAmount) AS salesAmount');
        $qb->where("e.globalOption = :globalOption");
        $qb->setParameter('globalOption', $globalOption);
        $this->handleSearchBetween($qb,$data);
        $result  = $qb->getQuery()->getOneOrNullResult();
        $salesAmount = $result['salesAmount'];

        $purchase = $this->_em->getRepository('HospitalBundle:InvoiceParticular')->reportSalesAccessories($globalOption, $data);
        $salesVat               = $this->_em->getRepository('AccountingBundle:Transaction')->reportTransactionVat($globalOption, $accountHeads = array(20), $data);
        $expenditures           = $this->_em->getRepository('AccountingBundle:Transaction')->reportTransactionIncome($user->getGlobalOption(), $accountHeads = array(37,23), $data);
        $revenues               = $this->_em->getRepository('AccountingBundle:Transaction')->reportTransactionIncome($user->getGlobalOption(), $accountHeads = array(20), $data);
        $administrative         = $this->_em->getRepository('AccountingBundle:Transaction')->reportTransactionIncome($user->getGlobalOption(), $accountHeads = array(23), $data);
        $data =  array('salesAmount' => $salesAmount ,'purchase' => $purchase,'revenues' => $revenues ,'expenditures' => $expenditures,'administrative' => $administrative, 'salesVat' => $salesVat);
        return $data;

    }

    public function reportHmsIncome($globalOption,$data)
    {
        if(empty($data)){
            $datetime = new \DateTime("now");
            $data['startDate'] = $datetime->format('Y-m-d 00:00:00');
            $data['endDate'] = $datetime->format('Y-m-d 23:59:59');
        }else{
            $data['startDate'] = date('Y-m-d',strtotime($data['startDate']));
            $data['endDate'] = date('Y-m-d',strtotime($data['endDate']));
        }

        $qb = $this->createQueryBuilder('e');
        $qb->select('SUM(e.totalAmount) AS salesAmount');
        $qb->where("e.globalOption = :globalOption");
        $qb->setParameter('globalOption', $globalOption);
        $this->handleSearchBetween($qb,$data);
        $result  = $qb->getQuery()->getOneOrNullResult();
        $salesAmount = $result['salesAmount'];

        $purchase = $this->_em->getRepository('HospitalBundle:InvoiceParticular')->reportSalesAccessories($globalOption, $data);
        $revenues = $this->_em->getRepository('AccountingBundle:Transaction')->reportTransactionIncome($globalOption, $accountHeads = array(20), $data);
        $expenditures = $this->_em->getRepository('AccountingBundle:Transaction')->reportTransactionIncome($globalOption, $accountHeads = array(37), $data);
        $salesVat = $this->_em->getRepository('AccountingBundle:Transaction')->reportTransactionVat($globalOption, $accountHeads = array(20), $data);
        $data =  array('salesAmount' => $salesAmount ,'purchase' => $purchase, 'revenues' => $revenues ,'expenditures' => $expenditures,'salesVat' => $salesVat);
        return $data;

    }

    public function reportMedicineMonthlyIncome(User $user,$data)
    {
        $globalOption = $user->getGlobalOption();
        if(empty($data)){

            $datetime = new \DateTime("now");
            $data['startDate'] = $datetime->format('Y-m-01 00:00:00');
            $data['endDate'] = $datetime->format('Y-m-t 23:59:59');

        }else{

            $data['startDate'] = date('Y-m-d 00:00:00',strtotime($data['year'].'-'.$data['startMonth']));
            $data['endDate'] = date('Y-m-t 23:59:59',strtotime($data['year'].'-'.$data['endMonth']));
        }

        $sales = $this->_em->getRepository('MedicineBundle:MedicineSales')->reportSalesOverview($user, $data);
        $purchase = $this->_em->getRepository('MedicineBundle:MedicineSales')->reportSalesItemPurchaseSalesOverview($user, $data);
        $expenditures = $this->_em->getRepository('AccountingBundle:Transaction')->reportTransactionIncome($globalOption, $accountHeads = array(37,23), $data);
        $data =  array('sales' => $sales['total'] ,'purchase' => $purchase['totalPurchase'], 'expenditures' => $expenditures);
        return $data;

    }

    public function reportMedicineIncome(User $user,$data)
    {
        $globalOption = $user->getGlobalOption()->getId();
        if(empty($data)){
            $datetime = new \DateTime("now");
            $data['startDate'] = $datetime->format('Y-m-d 00:00:00');
            $data['endDate'] = $datetime->format('Y-m-d 23:59:59');
        }else{
            $data['startDate'] = date('Y-m-d',strtotime($data['startDate']));
            $data['endDate'] = date('Y-m-d',strtotime($data['endDate']));
        }

        $sales = $this->_em->getRepository('MedicineBundle:MedicineSales')->reportSalesOverview($user, $data);
        $purchase = $this->_em->getRepository('MedicineBundle:MedicineSales')->reportSalesItemPurchaseSalesOverview($user, $data);
        $expenditures = $this->_em->getRepository('AccountingBundle:Transaction')->reportTransactionIncome($globalOption, $accountHeads = array(37), $data);
        $data =  array('sales' => $sales['total'] ,'purchase' => $purchase['totalPurchase'], 'expenditures' => $expenditures);
        return $data;

    }

    public function insertAccountInvoice(InvoiceTransaction $entity)
    {
        $em = $this->_em;
        $accountSales = new AccountSales();

        $accountSales->setAccountBank($entity->getAccountBank());
        $accountSales->setAccountMobileBank($entity->getAccountMobileBank());
        $accountSales->setGlobalOption($entity->getHmsInvoice()->getHospitalConfig()->getGlobalOption());
        $accountSales->setHmsInvoices($entity->getHmsInvoice());
        $accountSales->setCustomer($entity->getHmsInvoice()->getCustomer());
        $accountSales->setTransactionMethod($entity->getTransactionMethod());
        $accountSales->setTotalAmount($entity->getPayment());
        $accountSales->setAmount($entity->getPayment());
        $accountSales->setApprovedBy($entity->getCreatedBy());
        if(!empty($entity->getCreatedBy()->getProfile()->getBranches())){
            $accountSales->setBranches($entity->getCreatedBy()->getProfile()->getBranches());
        }
        $accountSales->setProcessHead('hms');
        $accountSales->setProcessType('Sales');
        $accountSales->setProcess('approved');
        $em->persist($accountSales);
        $em->flush();
        $this->_em->getRepository('AccountingBundle:AccountCash')->insertSalesCash($accountSales);
        return $accountSales;

    }

    public function insertRestaurantAccountInvoice(Invoice $entity)
    {
        $em = $this->_em;
        $accountSales = new AccountSales();

        $accountSales->setAccountBank($entity->getAccountBank());
        $accountSales->setAccountMobileBank($entity->getAccountMobileBank());
        $accountSales->setGlobalOption($entity->getRestaurantConfig()->getGlobalOption());
        $accountSales->setRestaurantInvoice($entity);
        $accountSales->setCustomer($entity->getCustomer());
        $accountSales->setTransactionMethod($entity->getTransactionMethod());
        $accountSales->setTotalAmount($entity->getPayment());
        $accountSales->setAmount($entity->getPayment());
        $accountSales->setApprovedBy($entity->getCreatedBy());
        $accountSales->setProcessHead('restaurant');
        $accountSales->setProcessType('Sales');
        $accountSales->setProcess('approved');
        $em->persist($accountSales);
        $em->flush();
        $this->_em->getRepository('AccountingBundle:AccountCash')->insertSalesCash($accountSales);
        return $accountSales;

    }

    public function insertMedicineAccountInvoice(MedicineSales $entity)
    {
        $em = $this->_em;
        $accountSales = new AccountSales();
        $accountSales->setAccountBank($entity->getAccountBank());
        $accountSales->setAccountMobileBank($entity->getAccountMobileBank());
        $accountSales->setGlobalOption($entity->getMedicineConfig()->getGlobalOption());
        $accountSales->setCustomer($entity->getCustomer());
        $accountSales->setTransactionMethod($entity->getTransactionMethod());
        $accountSales->setTotalAmount($entity->getNetTotal());
        $accountSales->setAmount($entity->getReceived());
        $accountSales->setApprovedBy($entity->getCreatedBy());
        $accountSales->setMedicineSales($entity);
        $accountSales->setProcessHead('Sales');
        $accountSales->setProcessType('Sales');
        $accountSales->setProcess('approved');
        $em->persist($accountSales);
        $em->flush();
        $this->updateCustomerBalance($accountSales);
        $this->_em->getRepository('AccountingBundle:AccountCash')->insertSalesCash($accountSales);
        return $accountSales;

    }

    public function insertMedicineAccountSalesReturn(MedicineSalesReturn $entity)
    {
        $global = $entity->getMedicineConfig()->getGlobalOption();
        $sales = $entity->getMedicineSalesItem()->getMedicineSales();
        $em = $this->_em;
        $accountSales = new AccountSales();
        $accountSales->setGlobalOption($global);
        $accountSales->setCustomer($sales->getCustomer());
        $accountSales->setAmount($entity->getSubTotal());
        $accountSales->setProcessHead('Sales-Return');
        $accountSales->setProcessType('Sales-Return');
        $accountSales->setProcess('approved');
        $accountSales->setApprovedBy($entity->getCreatedBy());
	    $accountSales->setMedicineSales($entity->getMedicineSalesItem()->getMedicineSales());
        $accountSales->setTransactionMethod($em->getRepository('SettingToolBundle:TransactionMethod')->find(1));
        $em->persist($accountSales);
        $em->flush();
        $this->updateCustomerBalance($accountSales);
      //$this->_em->getRepository('AccountingBundle:AccountCash')->insertSalesCash($accountSales);
        return $accountSales;

    }

    public function reportHmsTransactionIncome()
    {

    }

    public function accountSalesReverse(Sales $entity)
    {
        $em = $this->_em;
        if(!empty($entity->getAccountSales())){
            /* @var AccountSales $sales*/
            foreach ($entity->getAccountSales() as $sales ){
                $globalOption = $sales->getGlobalOption()->getId();
                $accountRefNo = $sales->getAccountRefNo();
                $transaction = $em->createQuery("DELETE AccountingBundle:Transaction e WHERE e.globalOption = ".$globalOption ." AND e.accountRefNo =".$accountRefNo." AND e.processHead = 'Sales'");
                $transaction->execute();
                $accountCash = $em->createQuery("DELETE AccountingBundle:AccountCash e WHERE e.globalOption = ".$globalOption ." AND e.accountRefNo =".$accountRefNo." AND e.processHead = 'Sales'");
                $accountCash->execute();
            }
        }
        $accountCash = $em->createQuery('DELETE AccountingBundle:AccountSales e WHERE e.sales = '.$entity->getId());
        if(!empty($accountCash)){
            $accountCash->execute();
        }
    }


    public function insertBusinessAccountInvoice(BusinessInvoice $entity)
    {
        $em = $this->_em;
        $accountSales = new AccountSales();
        $accountSales->setAccountBank($entity->getAccountBank());
        $accountSales->setAccountMobileBank($entity->getAccountMobileBank());
        $accountSales->setGlobalOption($entity->getBusinessConfig()->getGlobalOption());
        $accountSales->setCustomer($entity->getCustomer());
        $accountSales->setTransactionMethod($entity->getTransactionMethod());
        $accountSales->setTotalAmount($entity->getTotal());
        $accountSales->setAmount($entity->getReceived());
        $accountSales->setApprovedBy($entity->getCreatedBy());
        $accountSales->setBusinessInvoice($entity);
        $accountSales->setProcessHead('business');
        $accountSales->setProcessType('Sales');
        $accountSales->setProcess('approved');
        $em->persist($accountSales);
        $em->flush();
	    $sales = $this->updateCustomerBalance($accountSales);
        $this->_em->getRepository('AccountingBundle:AccountCash')->insertSalesCash($accountSales);
        return $accountSales;
    }

    public function insertBusinessAccountPurchaseReturn(MedicineSalesReturn $entity)
    {
        $global = $entity->getMedicineConfig()->getGlobalOption();
        $sales = $entity->getMedicineSalesItem()->getMedicineSales();
        $em = $this->_em;
        $accountSales = new AccountSales();
        $accountSales->setGlobalOption($global);
        $accountSales->setCustomer($sales->getCustomer());
        $accountSales->setAmount($entity->getSubTotal());
        $accountSales->setProcessHead('business');
        $accountSales->setProcessType('Sales-Return');
        $accountSales->setProcess('approved');
        $accountSales->setApprovedBy($entity->getCreatedBy());
        $accountSales->setTransactionMethod($em->getRepository('SettingToolBundle:TransactionMethod')->find(1));
        $em->persist($accountSales);
        $em->flush();
        $this->updateCustomerBalance($accountSales);
        $this->_em->getRepository('AccountingBundle:AccountCash')->insertSalesCash($accountSales);
        return $accountSales;

    }

    public function accountMedicineSalesReverse(MedicineSales $entity)
    {
        $em = $this->_em;
        if(!empty($entity->getAccountSales())){
            /* @var AccountSales $sales*/
            foreach ($entity->getAccountSales() as $sales ){
                $globalOption = $sales->getGlobalOption()->getId();
                $accountRefNo = $sales->getAccountRefNo();
                $transaction = $em->createQuery("DELETE AccountingBundle:Transaction e WHERE e.globalOption = ".$globalOption ." AND e.accountRefNo =".$accountRefNo." AND e.processHead = 'Sales'");
                $transaction->execute();
                $accountCash = $em->createQuery("DELETE AccountingBundle:AccountCash e WHERE e.globalOption = ".$globalOption ." AND e.accountRefNo =".$accountRefNo." AND e.processHead = 'Sales'");
                $accountCash->execute();
            }
        }
        $accountCash = $em->createQuery('DELETE AccountingBundle:AccountSales e WHERE e.medicineSales = '.$entity->getId());
        if(!empty($accountCash)){
            $accountCash->execute();
        }
    }

    public function reportBusinessMonthlyIncome(User $user,$data)
    {
        $globalOption = $user->getGlobalOption();
        if(empty($data)){

            $datetime = new \DateTime("now");
            $data['startDate'] = $datetime->format('Y-m-01 00:00:00');
            $data['endDate'] = $datetime->format('Y-m-t 23:59:59');

        }else{

            $data['startDate'] = date('Y-m-d 00:00:00',strtotime($data['year'].'-'.$data['startMonth']));
            $data['endDate'] = date('Y-m-t 23:59:59',strtotime($data['year'].'-'.$data['endMonth']));
        }

	    $sales = $this->_em->getRepository( 'ElectionIssue.php' )->reportSalesOverview($user, $data);
	    $purchase = $this->_em->getRepository( 'ElectionIssue.php' )->reportSalesItemPurchaseSalesOverview($user, $data);
	    $expenditures = $this->_em->getRepository('AccountingBundle:Transaction')->reportTransactionIncome($globalOption, $accountHeads = array(37,23), $data);
        $data =  array('sales' => $sales['total'] ,'purchase' => $purchase['totalPurchase'], 'expenditures' => $expenditures);
        return $data;

    }

    public function reportBusinessIncome(User $user,$data)
    {
        $globalOption = $user->getGlobalOption()->getId();
        if(empty($data)){
            $datetime = new \DateTime("now");
            $data['startDate'] = $datetime->format('Y-m-d 00:00:00');
            $data['endDate'] = $datetime->format('Y-m-d 23:59:59');
        }else{
            $data['startDate'] = date('Y-m-d',strtotime($data['startDate']));
            $data['endDate'] = date('Y-m-d',strtotime($data['endDate']));
        }

        $sales = $this->_em->getRepository( 'ElectionIssue.php' )->reportSalesOverview($user, $data);
        $purchase = $this->_em->getRepository( 'ElectionIssue.php' )->reportSalesItemPurchaseSalesOverview($user, $data);
        $expenditures = $this->_em->getRepository('AccountingBundle:Transaction')->reportTransactionIncome($globalOption, $accountHeads = array(37), $data);
        $data =  array('sales' => $sales['total'] ,'purchase' => $purchase['totalPurchase'], 'expenditures' => $expenditures);
        return $data;

    }


	public function accountBusinessSalesReverse(BusinessInvoice $entity)
	{
		$em = $this->_em;
		if(!empty($entity->getAccountSales())){

			/* @var AccountSales $sales */

			foreach ($entity->getAccountSales() as $sales ){

				$globalOption = $sales->getGlobalOption()->getId();
				$accountRefNo = $sales->getAccountRefNo();
				$transaction = $em->createQuery("DELETE AccountingBundle:Transaction e WHERE e.globalOption = ".$globalOption ." AND e.accountRefNo =".$accountRefNo." AND e.processHead = 'Sales'");
				$transaction->execute();
				$accountCash = $em->createQuery("DELETE AccountingBundle:AccountCash e WHERE e.globalOption = ".$globalOption ." AND e.accountRefNo =".$accountRefNo." AND e.processHead = 'Sales'");
				$accountCash->execute();

			}
		}
		$accountCash = $em->createQuery('DELETE AccountingBundle:AccountSales e WHERE e.businessInvoice = '.$entity->getId());
		if(!empty($accountCash)){
			$accountCash->execute();
		}
	}


	public function accountReverse(AccountSales $entity)
	{
		$em = $this->_em;
		$transaction = $em->createQuery("DELETE AccountingBundle:Transaction e WHERE e.globalOption = ".$entity->getGlobalOption()->getId() ." AND e.accountRefNo =".$entity->getAccountRefNo()." AND e.processHead = 'Sales'");
		$transaction->execute();
		$accountCash = $em->createQuery("DELETE AccountingBundle:AccountCash e WHERE e.globalOption = ".$entity->getGlobalOption()->getId() ." AND e.accountRefNo =".$entity->getAccountRefNo()." AND e.processHead = 'Sales'");
		$accountCash->execute();
	}


	/* Start Account sales for Hotel Module */

	public function insertHotelAccountInvoice(HotelInvoiceTransaction $entity)
	{
		$em = $this->_em;
		$accountSales = new AccountSales();

		$accountSales->setAccountBank($entity->getAccountBank());
		$accountSales->setAccountMobileBank($entity->getAccountMobileBank());
		$accountSales->setGlobalOption($entity->getHotelInvoice()->getHotelConfig()->getGlobalOption());
		$accountSales->setHotelInvoice($entity->getHotelInvoice());
		$accountSales->setCustomer($entity->getHotelInvoice()->getCustomer());
		$accountSales->setTransactionMethod($entity->getTransactionMethod());
		$accountSales->setTotalAmount($entity->getPayment());
		$accountSales->setAmount($entity->getPayment());
		$accountSales->setApprovedBy($entity->getCreatedBy());
		$accountSales->setProcessHead('hotel');
		$accountSales->setProcessType('Sales');
		$accountSales->setProcess('approved');
		$em->persist($accountSales);
		$em->flush();
		$this->_em->getRepository('AccountingBundle:AccountCash')->insertSalesCash($accountSales);
		return $accountSales;

	}

	public function checkOutHotelAccountInvoice(HotelInvoice $entity)
	{
		$em = $this->_em;
		$accountSales = new AccountSales();
		$accountSales->setGlobalOption($entity->getHotelConfig()->getGlobalOption());
		$accountSales->setCustomer($entity->getCustomer());
		$accountSales->setHotelInvoice($entity);
		$accountSales->setTransactionMethod(NUll);
		$accountSales->setTotalAmount($entity->getDue());
		$accountSales->setAmount(0);
		$accountSales->setApprovedBy($entity->getCreatedBy());
		$accountSales->setProcessHead('hotel');
		$accountSales->setProcessType('Sales');
		$accountSales->setProcess('approved');
		$em->persist($accountSales);
		$em->flush();
		$this->updateCustomerBalance($accountSales);
		//$this->_em->getRepository('AccountingBundle:AccountCash')->insertSalesCash($accountSales);
		return $accountSales;

	}

	/* End Account sales for Hotel Module */




}
