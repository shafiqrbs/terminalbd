<?php

namespace Appstore\Bundle\HotelBundle\Repository;
use Appstore\Bundle\HotelBundle\Entity\HotelInvoiceParticular;
use Appstore\Bundle\HotelBundle\Entity\HotelInvoiceTransaction;
use Appstore\Bundle\HotelBundle\Entity\HotelInvoiceTransactionSummary;
use Appstore\Bundle\HotelBundle\HotelBundle;
use Appstore\Bundle\HotelBundle\Entity\HotelConfig;
use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Appstore\Bundle\HotelBundle\Entity\HotelInvoice;
use Appstore\Bundle\HotelBundle\Entity\HotelParticular;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;


/**
 * HotelInvoiceRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class HotelInvoiceRepository extends EntityRepository
{

    public function getLastInvoice(HotelConfig $config)
    {
        $entity = $this->findOneBy(
            array('hotelConfig' => $config),
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
        $createdStart = isset($data['startDate'])? $data['startDate'] :'';
        $createdEnd = isset($data['endDate'])? $data['endDate'] :'';

        if (!empty($invoice)) {
            $qb->andWhere($qb->expr()->like("e.invoice", "'%$invoice%'"  ));
        }
        if (!empty($customerName)) {
            $qb->join('e.customer','c');
            $qb->andWhere($qb->expr()->like("c.name", "'$customerName%'"  ));
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
        $config =  $user->getGlobalOption()->getHotelConfig()->getId();

        $qb = $this->createQueryBuilder('e');
        $qb->select('sum(e.subTotal) as subTotal , sum(e.total) as total ,sum(e.received) as totalPayment , count(e.id) as totalVoucher, sum(e.due) as totalDue, sum(e.discount) as totalDiscount, sum(e.vat) as totalVat');
        $qb->where('e.hotelConfig = :config');
        $qb->setParameter('config', $config);
        $qb->andWhere('e.process IN (:process)');
        $qb->setParameter('process', array('Done','Booking','Check-in','Check-out'));
        $this->handleSearchBetween($qb,$data);
        if ($userBranch){
            $qb->andWhere("e.branch = :branch");
            $qb->setParameter('branch', $userBranch);
        }
        return $qb->getQuery()->getOneOrNullResult();
    }

    public  function reportSalesItemPurchaseSalesOverview(User $user, $data = array()){

        $userBranch = $user->getProfile()->getBranches();
        $config =  $user->getGlobalOption()->getHotelConfig()->getId();

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.hotelInvoiceParticulars','si');
        $qb->select('SUM(si.quantity) AS quantity');
        $qb->addSelect('COUNT(si.id) AS totalItem');
        $qb->addSelect('SUM(si.totalQuantity * si.purchasePrice) AS totalPurchase');
        $qb->addSelect('SUM(si.subTotal) AS salesPrice');
        $qb->where('e.hotelConfig = :config');
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
		$config =  $user->getGlobalOption()->getHotelConfig()->getId();

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
		$qb->where('e.hotelConfig = :config');
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
		$config =  $user->getGlobalOption()->getHotelConfig()->getId();

		$ids = array();
		foreach ($x as $y){
			$ids[]=$y['id'];
		}

		$qb = $this->createQueryBuilder('e');
		$qb->join('e.hotelInvoiceParticulars','si');
		$qb->select('e.id as salesId');
		$qb->addSelect('SUM(si.totalQuantity * si.purchasePrice ) AS totalPurchaseAmount');
		$qb->where('e.hotelConfig = :config');
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

        $config = $user->getGlobalOption()->getHotelConfig()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->leftJoin('e.invoiceTransactions','it');
        $qb->select('sum(e.subTotal) as subTotal ,sum(e.discount) as discount ,sum(it.total) as netTotal , sum(it.payment) as netPayment , sum(e.due) as netDue , sum(e.commission) as netCommission');
        $qb->where('e.hotelConfig = :config')->setParameter('config', $config);
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
        $config = $user->getGlobalOption()->getHotelConfig()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->leftJoin('e.invoiceTransactions','it');
        $qb->select('sum(e.subTotal) as subTotal ,sum(e.discount) as discount ,sum(e.total) as netTotal , sum(e.payment) as netPayment , sum(e.due) as netDue , sum(e.commission) as netCommission');
        $qb->where('e.hotelConfig = :config')->setParameter('config', $config);
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
        $config = $user->getGlobalOption()->getHotelConfig()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->leftJoin('e.invoiceTransactions','it');
        $qb->leftJoin('e.invoiceParticulars','ip');
        $qb->leftJoin('ip.particular','p');
        $qb->leftJoin('p.service','s');
        $qb->select('sum(ip.subTotal) as subTotal');
        $qb->addSelect('s.name as serviceName');
        $qb->where('e.hotelConfig = :config')->setParameter('config', $config);
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
        $config = $user->getGlobalOption()->getHotelConfig()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->leftJoin('e.invoiceTransactions','it');
        $qb->leftJoin('ip.transactionMethod','p');
        $qb->select('sum(ip.payment) as paymentTotal');
        $qb->addSelect('p.name as transName');
        $qb->where('e.hotelConfig = :config')->setParameter('config', $config);
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


        $config = $user->getGlobalOption()->getHotelConfig()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->leftJoin('e.doctorInvoices','ip');
        $qb->leftJoin('ip.assignDoctor','d');
        $qb->select('sum(ip.payment) as paymentTotal');
        $qb->addSelect('d.name as referredName');
        $qb->where('e.hotelConfig = :config')->setParameter('config', $config);
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


    public function invoiceLists(User $user, $invoiceFor , $data)
    {
        $config = $user->getGlobalOption()->getHotelConfig()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->where('e.hotelConfig = :config')->setParameter('config', $config) ;
        $qb->andWhere('e.invoiceFor = :invoiceFor')->setParameter('invoiceFor', $invoiceFor) ;
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('e.created','DESC');
        $qb->getQuery();
        return  $qb;
    }

	public function monthlySales(User $user , $data =array())
	{

		$config =  $user->getGlobalOption()->getHotelConfig()->getId();
		$compare = new \DateTime();
		$year =  $compare->format('Y');
		$year = isset($data['year'])? $data['year'] :$year;
		$sql = "SELECT MONTH (sales.created) as month,SUM(sales.total) AS total
                FROM hotel_invoice as sales
                WHERE sales.hotelConfig_id = :config AND sales.process = :process  AND YEAR(sales.created) =:year
                GROUP BY month ORDER BY month ASC";
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->bindValue('config', $config);
		$stmt->bindValue('process', 'Done');
		$stmt->bindValue('year', $year);
		$stmt->execute();
		$result =  $stmt->fetchAll();
		return $result;


	}


    public function updateInvoiceTotalPrice(HotelInvoice $invoice)
    {
        $em = $this->_em;
        $total = $em->createQueryBuilder()
            ->from('HotelBundle:HotelInvoiceParticular','si')
            ->select('sum(si.subTotal) as subTotal')
            ->where('si.hotelInvoice = :invoice')
            ->setParameter('invoice', $invoice ->getId())
            ->getQuery()->getOneOrNullResult();

        $subTotal = !empty($total['subTotal']) ? $total['subTotal'] :0;
        if($subTotal > 0){

            if ($invoice->getHotelConfig()->getServiceCharge() > 0 && $invoice->getInvoiceFor() == "hotel") {
                $totalAmount = ($subTotal- $invoice->getDiscount());
                $service = $this->getCalculationService($invoice,$totalAmount);
                $invoice->setServiceCharge($service);
            }
            if ($invoice->getHotelConfig()->getVatEnable() == 1 && $invoice->getInvoiceFor() == "hotel" && $invoice->getHotelConfig()->getVatForHotel() > 0) {
                $totalAmount = ($subTotal- $invoice->getDiscount());
                $vat = $this->getCalculationVat($invoice,$totalAmount);
                $invoice->setVat($vat);
            }
			if ($invoice->getHotelConfig()->getVatEnable() == 1 && $invoice->getInvoiceFor() == "restaurant" && $invoice->getHotelConfig()->getVatForRestaurant() > 0) {
                $totalAmount = ($subTotal- $invoice->getDiscount());
                $vat = $this->getCalculationRestaurant($invoice,$totalAmount);
                $invoice->setVat($vat);
            }
            $invoice->setSubTotal($subTotal);
            $invoice->setDiscount($this->getUpdateDiscount($invoice,$subTotal));
            $invoice->setTotal($invoice->getSubTotal() + $invoice->getVat() + $invoice->getServiceCharge() - $invoice->getDiscount());
            $invoice->setDue($invoice->getTotal() - $invoice->getReceived());

        }else{

            $invoice->setSubTotal(0);
            $invoice->setTotal(0);
            $invoice->setDue(0);
            $invoice->setDiscount(0);
            $invoice->setVat(0);
            $invoice->setServiceCharge(0);
        }

        $em->persist($invoice);
        $em->flush();
        return $invoice;

    }

	public function updatePaymentReceive(HotelInvoiceTransaction $transaction)
	{
		$em = $this->_em;
		$invoice = $transaction->getHotelInvoice();
		$res = $em->createQueryBuilder()
		          ->from('HotelBundle:HotelInvoiceTransaction','si')
		          ->select('sum(si.received) as received , sum(si.discount) as discount')
		          ->where('si.hotelInvoice = :invoice')
		          ->setParameter('invoice', $invoice->getId())
		          ->andWhere('si.process = :process')
		          ->setParameter('process', 'Done')
		          ->getQuery()->getOneOrNullResult();
		$received = !empty($res['received']) ? $res['received'] :0;
		$discount = !empty($res['discount']) ? $res['discount'] :0;
		$invoice->setReceived($received);
		$invoice->setDue($invoice->getTotal() - ($invoice->getReceived() + $discount) );
		if($invoice->getReceived() >= $invoice->getTotal()){
			$invoice->setPaymentStatus('Paid');
		}else{
			$invoice->setPaymentStatus('Due');
		}
		$em->flush();
		$this->updateTransactionPaymentReceive($invoice,$transaction);
		return $invoice;
	}

	public function updateTransactionPaymentReceive(HotelInvoiceTransaction $transaction)
	{
		$em = $this->_em;
		$res = $em->createQueryBuilder()
		          ->from('HotelBundle:HotelInvoiceTransaction','si')
		          ->join('si.hotelInvoice','e')
		          ->select('sum(si.total) as total, sum(si.received) as received, sum(si.discount) as discount')
		          ->where('si.referenceInvoice = :invoice')
		          ->setParameter('invoice', $transaction->getReferenceInvoice())
		          ->andWhere('e.hotelConfig = :config')
		          ->setParameter('config', $transaction->getHotelInvoice()->getHotelConfig()->getId())
		          ->andWhere('si.process = :process')
		          ->setParameter('process', 'done')
		          ->getQuery()->getOneOrNullResult();
		$due = ($res['total'] - ($res['received'] + $res['discount']));
		$transaction->setDue($due);
		$em->persist($transaction);
		$em->flush();
	}

	public function insertTransaction(HotelInvoice $invoice)
	{
		$entity = new HotelInvoiceTransaction();
		$code = $this->getLastCode($invoice);
		$entity->setHotelInvoice($invoice);
		$entity->setReferenceInvoice($invoice->getId());
		$entity->setCode($code + 1);
		$transactionCode = sprintf("%s", str_pad($entity->getCode(),2, '0', STR_PAD_LEFT));
		$entity->setTransactionCode($transactionCode);
		$entity->setProcess('done');
		$entity->setTransactionMethod($invoice->getTransactionMethod());
		$entity->setAccountBank($invoice->getAccountBank());
		$entity->setPaymentCard($invoice->getPaymentCard());
		$entity->setCardNo($invoice->getCardNo());
		$entity->setBank($invoice->getBank());
		$entity->setAccountMobileBank($invoice->getAccountMobileBank());
		$entity->setPaymentMobile($invoice->getPaymentMobile());
		$entity->setTransactionId($invoice->getTransactionId());
		$entity->setComment($invoice->getComment());
		$entity->setSubTotal($invoice->getSubTotal());
		$entity->setDiscount($invoice->getDiscount());
		$entity->setVat($invoice->getVat());
		$entity->setServiceCharge($invoice->getServiceCharge());
		$entity->setTotal($invoice->getTotal());
		$entity->setReceived($invoice->getReceived());
		$entity->setDue($invoice->getDue());
		$entity->setMain(true);
		$this->_em->persist($entity);
		$this->_em->flush($entity);
		$this->updateTransactionPaymentReceive($entity);
		$this->_em->getRepository('HotelBundle:HotelInvoiceTransactionSummary')->updateTransactionSummary($invoice);
		$arrs= array('check-in','booked');
		if(in_array($entity->getHotelInvoice()->getProcess(),$arrs)  and $entity->getHotelInvoice()->getInvoiceFor() == 'hotel' and $entity->getProcess() == 'done' and $entity->getReceived() > 0 ){
			$accountInvoice = $this->_em->getRepository('AccountingBundle:AccountSales')->insertHotelAccountInvoice($entity);
			$this->_em->getRepository('AccountingBundle:Transaction')->hotelSalesTransaction($entity, $accountInvoice);
		}elseif($entity->getHotelInvoice()->getInvoiceFor() == 'restaurant'){
			$accountInvoice = $this->_em->getRepository('AccountingBundle:AccountSales')->insertHotelAccountInvoice($entity);
			$this->_em->getRepository('AccountingBundle:Transaction')->hotelSalesTransaction($entity, $accountInvoice);
		}

	}

	public function updateInvoiceTransaction(HotelInvoice $invoice)
	{
		$entity = $this->_em->getRepository('HotelBundle:HotelInvoiceTransaction')->findOneBy(array('hotelInvoice' => $invoice,'main' => 1));
		if(!empty($entity)){
			$entity->setSubTotal($invoice->getSubTotal());
			$entity->setDiscount($invoice->getDiscount());
			$entity->setVat($invoice->getVat());
			$entity->setServiceCharge($invoice->getServiceCharge());
			$entity->setTotal($invoice->getTotal());
			$entity->setReceived($invoice->getReceived());
			$entity->setDue($invoice->getDue());
			$this->_em->persist($entity);
			$this->_em->flush($entity);
			$this->updateTransactionPaymentReceive($entity);
			$this->_em->getRepository('HotelBundle:HotelInvoiceTransactionSummary')->updateTransactionSummary($invoice);
		}
	}

	public function insertRestaurantTransaction(HotelInvoice $invoice , HotelInvoiceParticular $hip)
	{

		$exist = $this->_em->getRepository('HotelBundle:HotelInvoiceTransaction')->findOneBy(
		    array('hotelInvoice' => $invoice)
		);
		if(empty($exist)){
			$entity = new HotelInvoiceTransaction();
			$code = $this->getLastCode($invoice);
			$entity->setHotelInvoice($invoice);
			$entity->setReferenceInvoice($hip->getHotelInvoice()->getId());
			$entity->setCode($code + 1);
			$transactionCode = sprintf("%s", str_pad($entity->getCode(),2, '0', STR_PAD_LEFT));
			$entity->setTransactionCode($transactionCode);
			$entity->setProcess('done');
			$entity->setTransactionMethod($invoice->getTransactionMethod());
			$entity->setAccountBank($invoice->getAccountBank());
			$entity->setPaymentCard($invoice->getPaymentCard());
			$entity->setCardNo($invoice->getCardNo());
			$entity->setBank($invoice->getBank());
			$entity->setAccountMobileBank($invoice->getAccountMobileBank());
			$entity->setPaymentMobile($invoice->getPaymentMobile());
			$entity->setTransactionId($invoice->getTransactionId());
			$entity->setComment($invoice->getComment());
			$entity->setSubTotal($invoice->getSubTotal());
			$entity->setDiscount($invoice->getDiscount());
			$entity->setVat($invoice->getVat());
			$entity->setServiceCharge($invoice->getServiceCharge());
			$entity->setTotal($invoice->getTotal());
			$entity->setReceived($invoice->getReceived());
			$entity->setDue($invoice->getDue());
			$this->_em->persist($entity);
			$this->_em->flush($entity);
			$this->updateTransactionPaymentReceive($entity);
			$this->_em->getRepository('HotelBundle:HotelInvoiceTransactionSummary')->updateTransactionSummary($hip->getHotelInvoice());
			$accountInvoice = $this->_em->getRepository('AccountingBundle:AccountSales')->insertHotelAccountInvoice($entity);
			$this->_em->getRepository('AccountingBundle:Transaction')->hotelSalesTransaction($entity, $accountInvoice);

		}

	}

	public function insertPaymentTransaction(HotelInvoice $invoice,$data)
	{

		$entity = New HotelInvoiceTransaction();
		$code = $this->getLastCode($invoice);
		$entity->setHotelInvoice($invoice);
		$entity->setReferenceInvoice($invoice->getId());
		$entity->setCode($code + 1);
		$transactionCode = sprintf("%s", str_pad($entity->getCode(),2, '0', STR_PAD_LEFT));
		$entity->setTransactionCode($transactionCode);
		$entity->setDiscount($data['invoice']['discount']);
		$entity->setReceived($data['invoice']['received']);
		$entity->setProcess('in-progress');
		$entity->setTransactionMethod($invoice->getTransactionMethod());
		$entity->setAccountBank($invoice->getAccountBank());
		$entity->setPaymentCard($invoice->getPaymentCard());
		$entity->setCardNo($invoice->getCardNo());
		$entity->setBank($invoice->getBank());
		$entity->setAccountMobileBank($invoice->getAccountMobileBank());
		$entity->setPaymentMobile($invoice->getPaymentMobile());
		$entity->setTransactionId($invoice->getTransactionId());
		$entity->setComment($invoice->getComment());
		$this->_em->persist($entity);
		$this->_em->flush($entity);

	}

	public function getLastCode(HotelInvoice $invoice)
	{
		$qb = $this->_em->getRepository('HotelBundle:HotelInvoiceTransaction')->createQueryBuilder('s');
		$qb
			->select('MAX(s.code)')
			->where('s.hotelInvoice = :invoice')
			->setParameter('invoice', $invoice->getId());
		$lastCode = $qb->getQuery()->getSingleScalarResult();
		if (empty($lastCode)) {
			return 0;
		}
		return $lastCode;
	}


	public function getUpdateDiscount(HotelInvoice $invoice,$subTotal)
    {
        if($invoice->getDiscountType() == 'flat'){
            $discount = $invoice->getDiscountCalculation();
        }else{
            $discount = ($subTotal * $invoice->getDiscountCalculation())/100;
        }
        return $discount;
    }

    public function getCalculationVat(HotelInvoice $sales,$totalAmount)
    {
        $vat = ( ($totalAmount * (int)$sales->getHotelConfig()->getVatForHotel())/100 );
        return round($vat);
    }
	
    public function getCalculationRestaurant(HotelInvoice $sales,$totalAmount)
    {
        $vat = ( ($totalAmount * (int)$sales->getHotelConfig()->getVatForRestaurant())/100 );
        return round($vat);
    }
    
    public function getCalculationService(HotelInvoice $sales,$totalAmount)
    {
        $vat = ( ($totalAmount * (int)$sales->getHotelConfig()->getServiceCharge())/100 );
        return round($vat);
    }

}
