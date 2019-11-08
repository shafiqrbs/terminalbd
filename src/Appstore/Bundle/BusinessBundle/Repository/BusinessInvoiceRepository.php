<?php

namespace Appstore\Bundle\BusinessBundle\Repository;
use Appstore\Bundle\BusinessBundle\BusinessBundle;
use Appstore\Bundle\BusinessBundle\Entity\BusinessConfig;
use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoice;
use Appstore\Bundle\BusinessBundle\Entity\BusinessParticular;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;


/**
 * BusinessInvoiceRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BusinessInvoiceRepository extends EntityRepository
{

    public function getLastInvoice(BusinessConfig $config,$customer)
    {
        $entity = $this->findOneBy(
            array('businessConfig' => $config,'customer' => $customer),
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
        $process = isset($data['process'])? $data['process'] :'';
        $customerName = isset($data['name'])? $data['name'] :'';
        $customerMobile = isset($data['mobile'])? $data['mobile'] :'';
        $createdStart = isset($data['createdStart'])? $data['createdStart'] :'';
        $createdEnd = isset($data['createdEnd'])? $data['createdEnd'] :'';
        $startDate = isset($data['startDate'])? $data['startDate'] :'';
        $endDate = isset($data['endDate'])? $data['endDate'] :'';
        $createdBy = isset($data['createdBy'])? $data['createdBy'] :'';

        if (!empty($invoice)) {
            $qb->andWhere($qb->expr()->like("e.invoice", "'%$invoice%'"  ));
        }
        if (!empty($customerName)) {
            $qb->join('e.customer','c');
            $qb->andWhere($qb->expr()->like("c.name", "'$customerName%'"  ));
        }
        if (!empty($createdBy)) {
            $qb->andWhere("e.createdBy = :user")->setParameter('user', $createdBy);
        }

        if (!empty($customerMobile)) {
            $qb->join('e.customer','m');
            $qb->andWhere($qb->expr()->like("m.mobile", "'%$customerMobile%'"  ));
        }
        if (!empty($createdStart)) {
            $compareTo = new \DateTime($createdStart);
            $created =  $compareTo->format('Y-m-d 00:00:00');
            $qb->andWhere("e.created >= :created");
            $qb->setParameter('created', $created);
        }
        if (!empty($createdEnd)) {
            $compareTo = new \DateTime($createdEnd);
            $createdEnd =  $compareTo->format('Y-m-d 23:59:59');
            $qb->andWhere("e.created <= :createdEnd");
            $qb->setParameter('createdEnd', $createdEnd);
        }
        if (!empty($startDate)) {
            $compareTo = new \DateTime($startDate);
            $created =  $compareTo->format('Y-m-d 00:00:00');
            $qb->andWhere("e.created >= :created");
            $qb->setParameter('created', $created);
        }
        if (!empty($endDate)) {
            $compareTo = new \DateTime($endDate);
            $createdEnd =  $compareTo->format('Y-m-d 23:59:59');
            $qb->andWhere("e.created <= :createdEnd");
            $qb->setParameter('createdEnd', $createdEnd);
        }
        if(!empty($process)){
            $qb->andWhere("e.process = :process");
            $qb->setParameter('process', $process);
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

    public function reportSalesOverview(User $user ,$data)
    {


        $userBranch = $user->getProfile()->getBranches();
        $config =  $user->getGlobalOption()->getBusinessConfig()->getId();

        $qb = $this->createQueryBuilder('e');
        $qb->select('sum(e.subTotal) as subTotal , sum(e.total) as total ,sum(e.received) as totalPayment , count(e.id) as totalVoucher, sum(e.due) as totalDue, sum(e.discount) as totalDiscount, sum(e.vat) as totalVat');
        $qb->where('e.businessConfig = :config');
        $qb->setParameter('config', $config);
        $qb->andWhere('e.process IN (:process)');
        $qb->setParameter('process', array('Done','Delivered','Chalan'));
        $this->handleSearchBetween($qb,$data);
        if ($userBranch){
            $qb->andWhere("e.branch = :branch");
            $qb->setParameter('branch', $userBranch);
        }
        return $qb->getQuery()->getOneOrNullResult();
    }

    public  function reportSalesItemPurchaseSalesOverview(User $user, $data = array()){

        $userBranch = $user->getProfile()->getBranches();
        $config =  $user->getGlobalOption()->getBusinessConfig()->getId();

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.businessInvoiceParticulars','si');
        $qb->select('SUM(si.quantity) AS quantity');
        $qb->addSelect('COUNT(si.id) AS totalItem');
        $qb->addSelect('SUM(si.totalQuantity * si.purchasePrice) AS totalPurchase');
        $qb->addSelect('SUM(si.subTotal) AS salesPrice');
        $qb->where('e.businessConfig = :config');
        $qb->setParameter('config', $config);
	    $qb->andWhere('e.process IN (:process)');
	    $qb->setParameter('process', array('Done','Delivered','Chalan'));
        $this->handleSearchBetween($qb,$data);
        if ($userBranch){
            $qb->andWhere("e.branches = :branch");
            $qb->setParameter('branch', $userBranch);
        }
        $result = $qb->getQuery()->getOneOrNullResult();
        return $result;
    }

	public function salesReport( User $user , $data)
	{

		$userBranch = $user->getProfile()->getBranches();
		$config =  $user->getGlobalOption()->getBusinessConfig()->getId();

		$qb = $this->createQueryBuilder('e');
		$qb->leftJoin('e.salesBy', 'u');
		$qb->leftJoin('e.transactionMethod', 't');
		$qb->select('u.username as salesBy');
		$qb->addSelect('t.name as transactionMethod');
		$qb->addSelect('e.id as id');
		$qb->addSelect('e.created as created');
		$qb->addSelect('e.process as process');
		$qb->addSelect('e.invoice as invoice');
		$qb->addSelect('(e.due) as due');
		$qb->addSelect('(e.subTotal) as subTotal');
		$qb->addSelect('(e.total) as total');
		$qb->addSelect('(e.received) as payment');
		$qb->addSelect('(e.discount) as discount');
		$qb->addSelect('(e.vat) as vat');
		$qb->where('e.businessConfig = :config');
		$qb->setParameter('config', $config);
		$qb->andWhere('e.process IN (:process)');
		$qb->setParameter('process', array('Done','Delivered','Chalan'));
		if(!empty($userBranch)){
			$qb->andWhere("e.branches =".$userBranch);
		}
		$this->handleSearchBetween($qb,$data);
		$qb->orderBy('e.updated','DESC');
		$result = $qb->getQuery();
		return $result;

	}


	public function salesPurchasePriceReport(User $user,$data,$x)
	{

		$userBranch = $user->getProfile()->getBranches();
		$config =  $user->getGlobalOption()->getBusinessConfig()->getId();

		$ids = array();
		foreach ($x as $y){
			$ids[]=$y['id'];
		}

		$qb = $this->createQueryBuilder('e');
		$qb->join('e.businessInvoiceParticulars','si');
		$qb->select('e.id as salesId');
		$qb->addSelect('SUM(si.totalQuantity * si.purchasePrice ) AS totalPurchaseAmount');
		$qb->where('e.businessConfig = :config');
		$qb->setParameter('config', $config);
		$qb->andWhere('e.process IN (:process)');
		$qb->setParameter('process', array('Done','Delivered','Chalan'));
		$qb->andWhere("e.id IN (:salesId)")->setParameter('salesId', $ids);
		if(!empty($userBranch)){
			$qb->andWhere("e.branches =".$userBranch);
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

		$qb = $this->createQueryBuilder('e');
		$qb->join('e.medicineSalesItems','si');
		$qb->join('si.medicineStock','mds');
		$qb->select('SUM(si.quantity) AS quantity');
		$qb->addSelect('SUM(si.quantity * si.discountPrice ) AS salesPrice');
		$qb->addSelect('SUM(si.quantity * si.purchasePrice ) AS purchasePrice');
		$qb->addSelect('mde.name AS name');
		$qb->where('e.medicineConfig = :config');
		$qb->setParameter('config', $config);
		$qb->andWhere('e.process = :process');
		$qb->setParameter('process', 'Done');
		$qb->groupBy('si.medicineStock');
		$qb->orderBy('mde.name','ASC');
		return $qb->getQuery()->getArrayResult();
	}

	public  function reportSalesStockItem(User $user, $data=''){

		$userBranch = $user->getProfile()->getBranches();
		$config =  $user->getGlobalOption()->getMedicineConfig()->getId();
		$group = isset($data['group']) ? $data['group'] :'medicineStock';

		$qb = $this->createQueryBuilder('s');
		$qb->join('e.medicineSalesItems','si');
		$qb->join('si.medicinePurchaseItem','item');
		$qb->join('si.medicineStock','mds');
		$qb->select('SUM(si.quantity) AS quantity');
		$qb->addSelect('SUM(si.quantity * si.discountPrice ) AS salesPrice');
		$qb->addSelect('SUM(si.quantity * si.purchasePrice ) AS purchasePrice');
		$qb->addSelect('mde.name AS name');
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

	public function findWithOverview(User $user , $data , $mode='')
    {

        $config = $user->getGlobalOption()->getBusinessConfig()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->leftJoin('e.invoiceTransactions','it');
        $qb->select('sum(e.subTotal) as subTotal ,sum(e.discount) as discount ,sum(it.total) as netTotal , sum(it.payment) as netPayment , sum(e.due) as netDue , sum(e.commission) as netCommission');
        $qb->where('e.businessConfig = :config')->setParameter('config', $config);
        if (!empty($mode)){
            $qb->andWhere('e.invoiceMode = :mode')->setParameter('mode', $mode);
        }
       // $this->handleSearchBetween($qb,$data);
        $this->handleDateRangeFind($qb,$data);
        $qb->andWhere("e.process IN (:process)");
        $qb->setParameter('process', array('Done','Paid','In-progress','Diagnostic','Admitted','Release','Released','Death','Dead'));
        $result = $qb->getQuery()->getOneOrNullResult();

        $subTotal = !empty($result['subTotal']) ? $result['subTotal'] :0;
        $netTotal = !empty($result['netTotal']) ? $result['netTotal'] :0;
        $netPayment = !empty($result['netPayment']) ? $result['netPayment'] :0;
        $netDue = !empty($result['netDue']) ? $result['netDue'] :0;
        $discount = !empty($result['discount']) ? $result['discount'] :0;
        $vat = !empty($result['vat']) ? $result['vat'] :0;
        $netCommission = !empty($result['netCommission']) ? $result['netCommission'] :0;
        $data = array('subTotal'=> $subTotal ,'discount'=> $discount ,'vat'=> $vat ,'netTotal'=> $netTotal , 'netPayment'=> $netPayment , 'netDue'=> $netDue , 'netCommission'=> $netCommission);

        return $data;
    }

    public function findWithSalesOverview(User $user , $data , $mode='')
    {
        $config = $user->getGlobalOption()->getBusinessConfig()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->leftJoin('e.invoiceTransactions','it');
        $qb->select('sum(e.subTotal) as subTotal ,sum(e.discount) as discount ,sum(e.total) as netTotal , sum(e.payment) as netPayment , sum(e.due) as netDue , sum(e.commission) as netCommission');
        $qb->where('e.businessConfig = :config')->setParameter('config', $config);
        if (!empty($mode)){
            $qb->andWhere('e.invoiceMode = :mode')->setParameter('mode', $mode);
        }
        $this->handleDateRangeFind($qb,$data);
        $qb->andWhere("e.process IN (:process)");
        $qb->setParameter('process', array('Done','Paid','In-progress','Diagnostic','Admitted'));
        $result = $qb->getQuery()->getOneOrNullResult();
        $subTotal = !empty($result['subTotal']) ? $result['subTotal'] :0;
        $netTotal = !empty($result['netTotal']) ? $result['netTotal'] :0;
        $netPayment = !empty($result['netPayment']) ? $result['netPayment'] :0;
        $netDue = !empty($result['netDue']) ? $result['netDue'] :0;
        $discount = !empty($result['discount']) ? $result['discount'] :0;
        $vat = !empty($result['vat']) ? $result['vat'] :0;
        $netCommission = !empty($result['netCommission']) ? $result['netCommission'] :0;
        $data = array('subTotal'=> $subTotal ,'discount'=> $discount ,'vat'=> $vat ,'netTotal'=> $netTotal , 'netPayment'=> $netPayment , 'netDue'=> $netDue , 'netCommission'=> $netCommission);

        return $data;
    }

    public function findWithServiceOverview(User $user, $data)
    {
        $config = $user->getGlobalOption()->getBusinessConfig()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->leftJoin('e.invoiceTransactions','it');
        $qb->leftJoin('e.invoiceParticulars','ip');
        $qb->leftJoin('ip.particular','p');
        $qb->leftJoin('p.service','s');
        $qb->select('sum(ip.subTotal) as subTotal');
        $qb->addSelect('s.name as serviceName');
        $qb->where('e.businessConfig = :config')->setParameter('config', $config);
        if (!empty($mode)){
            $qb->andWhere('e.invoiceMode = :mode')->setParameter('mode', $mode);
        }
        $qb->andWhere("e.process IN (:process)");
        $qb->setParameter('process', array('Done','Paid','In-progress','Diagnostic','Admitted','Release','Death','Released','Dead'));
        $this->handleDateRangeFind($qb,$data);
        $qb->groupBy('s.id');
        $result = $qb->getQuery()->getArrayResult();
        return $result;
    }

    public function findWithTransactionOverview(User $user, $data)
    {
        $config = $user->getGlobalOption()->getBusinessConfig()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->leftJoin('e.invoiceTransactions','it');
        $qb->leftJoin('ip.transactionMethod','p');
        $qb->select('sum(ip.payment) as paymentTotal');
        $qb->addSelect('p.name as transName');
        $qb->where('e.businessConfig = :config')->setParameter('config', $config);
        if (!empty($mode)){
            $qb->andWhere('e.invoiceMode = :mode')->setParameter('mode', $mode);
        }
        $qb->andWhere("e.process IN (:process)");
        $qb->setParameter('process', array('Done','Paid','In-progress','Diagnostic','Admitted'));
        $this->handleDateRangeFind($qb,$data);
        $qb->groupBy('p.id');
        $result = $qb->getQuery()->getArrayResult();
        return $result;
    }

    public function findWithCommissionOverview(User $user, $data)
    {
        if(empty($data)){
            $datetime = new \DateTime("now");
            $data['startDate'] = $datetime->format('Y-m-d 00:00:00');
            $data['endDate'] = $datetime->format('Y-m-d 23:59:59');
        }else{
            $data['startDate'] = date('Y-m-d',strtotime($data['startDate']));
            $data['endDate'] = date('Y-m-d',strtotime($data['endDate']));
        }


        $config = $user->getGlobalOption()->getBusinessConfig()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->leftJoin('e.doctorInvoices','ip');
        $qb->leftJoin('ip.assignDoctor','d');
        $qb->select('sum(ip.payment) as paymentTotal');
        $qb->addSelect('d.name as referredName');
        $qb->where('e.businessConfig = :config')->setParameter('config', $config);
        $qb->andWhere('ip.process = :mode')->setParameter('mode', 'Paid');
        if (!empty($data['startDate']) ) {
            $qb->andWhere("ip.updated >= :startDate");
            $qb->setParameter('startDate', $data['startDate'].' 00:00:00');
        }

        if (!empty($data['endDate'])) {
            $qb->andWhere("ip.updated <= :endDate");
            $qb->setParameter('endDate', $data['endDate'].' 23:59:59');
        }

        $qb->groupBy('ip.assignDoctor');
        $result = $qb->getQuery()->getArrayResult();
        return $result;
    }


    public function invoiceLists($config, $data)
    {


        $qb = $this->createQueryBuilder('e');
        $qb->where('e.businessConfig = :config')->setParameter('config', $config) ;
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('e.created','DESC');
        $qb->getQuery();
        return  $qb;
    }

	public function monthlySales(User $user , $data =array())
	{

		$config =  $user->getGlobalOption()->getBusinessConfig()->getId();
		$compare = new \DateTime();
		$year =  $compare->format('Y');
		$year = isset($data['year'])? $data['year'] :$year;
		$sql = "SELECT MONTH (sales.created) as month,SUM(sales.total) AS total
                FROM business_invoice as sales
                WHERE sales.businessConfig_id = :config AND sales.process = :process  AND YEAR(sales.created) =:year
                GROUP BY month ORDER BY month ASC";
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->bindValue('config', $config);
		$stmt->bindValue('process', 'Done');
		$stmt->bindValue('year', $year);
		$stmt->execute();
		$result =  $stmt->fetchAll();
		return $result;
	}


    public function updateInvoiceTotalPrice(BusinessInvoice $invoice)
    {
        $em = $this->_em;
        $total = $em->createQueryBuilder()
            ->from('BusinessBundle:BusinessInvoiceParticular','si')
            ->select('sum(si.subTotal) as subTotal')
            ->where('si.businessInvoice = :invoice')
            ->setParameter('invoice', $invoice ->getId())
            ->getQuery()->getOneOrNullResult();

        $subTotal = !empty($total['subTotal']) ? $total['subTotal'] :0;
        if($subTotal > 0){

            if ($invoice->getBusinessConfig()->getVatEnable() == 1 && $invoice->getBusinessConfig()->getVatPercentage() > 0) {
                $totalAmount = ($subTotal- $invoice->getDiscount());
                $vat = $this->getCulculationVat($invoice,$totalAmount);
                $invoice->setVat($vat);
            }
            $invoice->setSubTotal($subTotal);
            $invoice->setDiscount($this->getUpdateDiscount($invoice,$subTotal));
            $total = round($invoice->getSubTotal() + $invoice->getVat() - $invoice->getDiscount());
            $invoice->setTotal($total);
            $invoice->setDue($invoice->getTotal() - $invoice->getReceived());

        }else{

            $invoice->setSubTotal(0);
            $invoice->setTotal(0);
            $invoice->setDue(0);
            $invoice->setDiscount(0);
            $invoice->setVat(0);
        }

        $em->persist($invoice);
        $em->flush();
        return $invoice;

    }

    public function updateInvoiceDistributionTotalPrice(BusinessInvoice $invoice)
    {
        $em = $this->_em;
        $total = $em->createQueryBuilder()
            ->from('BusinessBundle:BusinessInvoiceParticular','si')
            ->select("sum(si.subTotal) as subTotal","sum(si.quantity) as salesQnt","sum(si.returnQnt) as returnQnt","sum(si.damageQnt) as damageQnt","sum(si.spoilQnt) as spoilQnt","sum(si.totalQuantity) as totalQnt","sum(si.bonusQnt) as bonusQnt")
            ->where('si.businessInvoice = :invoice')
            ->setParameter('invoice', $invoice ->getId())
            ->getQuery()->getOneOrNullResult();

        $subTotal = !empty($total['subTotal']) ? $total['subTotal'] :0;
        if($subTotal > 0){

            if ($invoice->getBusinessConfig()->getVatEnable() == 1 && $invoice->getBusinessConfig()->getVatPercentage() > 0) {
                $totalAmount = ($subTotal- $invoice->getDiscount());
                $vat = $this->getCulculationVat($invoice,$totalAmount);
                $invoice->setVat($vat);
            }
            $invoice->setSubTotal(round($subTotal,2));
            $invoice->setDiscount($this->getUpdateDiscount($invoice,$subTotal));
            $invoice->setTotal($invoice->getSubTotal() + $invoice->getVat() - $invoice->getDiscount());
            $invoice->setDue($invoice->getTotal() - $invoice->getReceived());

        }else{

            $invoice->setSubTotal(0);
            $invoice->setTotal(0);
            $invoice->setDue(0);
            $invoice->setDiscount(0);
            $invoice->setVat(0);
        }

        $em->persist($invoice);
        $em->flush();
        return $total;

    }

    public function getUpdateDiscount(BusinessInvoice $invoice,$subTotal)
    {
        if($invoice->getDiscountType() == 'flat'){
            $discount = $invoice->getDiscountCalculation();
        }else{
            $discount = ($subTotal * $invoice->getDiscountCalculation())/100;
        }
        return $discount;
    }

    public function getCulculationVat(BusinessInvoice $sales,$totalAmount)
    {
        $vat = ( ($totalAmount * (int)$sales->getBusinessConfig()->getVatPercentage())/100 );
        return round($vat);
    }

    public function salesUserReport( User $user , $data)
    {
        $config =  $user->getGlobalOption()->getBusinessConfig()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->leftJoin('e.salesBy', 'u');
        $qb->select('u.username as salesBy');
        $qb->addSelect('u.id as userId');
        $qb->addSelect('SUM(e.due) as due');
        $qb->addSelect('SUM(e.subTotal) as subTotal');
        $qb->addSelect('SUM(e.total) as total');
        $qb->addSelect('SUM(e.received) as payment');
        $qb->addSelect('SUM(e.discount) as discount');
        $qb->addSelect('SUM(e.vat) as vat');
        $qb->where('e.businessConfig = :config');
        $qb->setParameter('config', $config);
        $qb->andWhere('e.process IN (:process)');
        $qb->setParameter('process', array('Done','Delivered'));
        $this->handleSearchBetween($qb,$data);
        $qb->groupBy('salesBy');
        $qb->orderBy('total','DESC');
        $result = $qb->getQuery()->getArrayResult();
        return $result;

    }

    public function currentMonthSales(User $user , $data =array())
    {

        $config =  $user->getGlobalOption()->getMedicineConfig()->getId();
        $compare = new \DateTime();
        $year =  $compare->format('Y');
        $month =  $compare->format('m');
        $year = isset($data['year'])? $data['year'] :$year;

        $sql = "SELECT sales.salesBy_id as salesBy, MONTH (sales.created) as month, SUM(sales.total) AS total
                FROM business_invoice as sales
                WHERE sales.businessConfig_id = :config AND sales.process = :process  AND YEAR(sales.created) =:year AND MONTH(sales.created) =:month
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
        $config =  $user->getGlobalOption()->getMedicineConfig()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->leftJoin('e.salesBy', 'u');
        $qb->join('e.businessInvoiceParticulars','si');
        $qb->select('u.username as salesBy');
        $qb->addSelect('SUM(si.totalQuantity * si.purchasePrice ) AS totalPurchaseAmount');
        $qb->where('e.businessConfig = :config');
        $qb->setParameter('config', $config);
        $qb->andWhere('e.process IN (:process)');
        $qb->setParameter('process', array('Done','Delivered'));
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

    public function getCountDailyInvoice($config){

        $qb = $this->createQueryBuilder('e');
        $qb->select('COUNT(e.id) as totalInvoice');
        $qb->where('e.businessConfig = :config')->setParameter('config', $config);
        $compareTo = new \DateTime("now");
        $created =  $compareTo->format('Y-m-d 00:00:00');
        $qb->andWhere("e.created >= :createdStart")->setParameter('createdStart', $created);
        $createdEnd =  $compareTo->format('Y-m-d 23:59:59');
        $qb->andWhere("e.created <= :createdEnd")->setParameter('createdEnd', $createdEnd);
        $result = $qb->getQuery()->getOneOrNullResult();
        return ($result['totalInvoice'])?$result['totalInvoice']:1;
    }

    public function insertApiInvoiceToken(GlobalOption $option , $data){

        $em = $this->_em;
        $conf = $option->getBusinessConfig();
        $sales = new BusinessInvoice();
        $sales->setBusinessConfig($option->getBusinessConfig());
        if($data['customerName'] and $data['customerMobile']){
            $customer = $em->getRepository('DomainUserBundle:Customer')->newExistingCustomerForSales($option,$data['customerMobile'],$data);
            $sales->setCustomer($customer);
        }elseif($data['customerId']){
            $customer = $em->getRepository('DomainUserBundle:Customer')->find($data['customerId']);
            $sales->setCustomer($customer);
        }elseif(empty($data['customerId']) and empty($data['customerName']) ) {
            $customer = $em->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $option, 'name' => 'Default'));
            $sales->setCustomer($customer);
        }
        if($data['createdBy']){
            $createdBy = $em->getRepository('UserBundle:User')->find($data['createdBy']);
            $sales->setCreatedBy($createdBy);
        }
        $sales->setComment($data['counterNo']);
        $tokenNo = $this->getCountDailyInvoice($conf->getId())+1;
        $em->persist($sales);
        $em->flush();
        $data = array(
            'invoice' => $sales->getInvoice(),
            'customerId' => $sales->getCustomer()->getId(),
            'customerName' => $sales->getCustomer()->getName(),
            'customerMobile' => $sales->getCustomer()->getMobile(),
            'counterNo' => $sales->getComment(),
            'tokenNo' => $tokenNo,
            'created' => $sales->getCreated()->format('d-m-Y')
        );
        return $data;

    }

    public function getLastInvoiceParticular(Customer $customer){


        $qb = $this->createQueryBuilder('e');
        $qb
            ->select('MAX(e.id)')
            ->where('e.customer = :customer')
            ->setParameter('customer', $customer->getId());
        $lastId = $qb->getQuery()->getSingleScalarResult();
        if (empty($lastId)) {
            return $this->insertAssociation($customer);
        }
        $lastCode =  $this->find($lastId);
        return $lastCode;

    }

    public function insertAssociation(Customer $customer)
    {
        $em = $this->_em;
        $invoice = new BusinessInvoice();
        $invoice->setBusinessConfig($customer->getGlobalOption()->getBusinessConfig());
        $invoice->setCustomer($customer);
        $invoice->setProcess("Done");
        $invoice->setEndDate($customer->getCreated());
        $em->persist($invoice);
        $em->flush();
        return $invoice;

    }

}
