<?php

namespace Appstore\Bundle\MedicineBundle\Repository;
use Appstore\Bundle\MedicineBundle\Entity\MedicineConfig;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchase;
use Appstore\Bundle\MedicineBundle\Entity\MedicineVendor;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;


/**
 * MedicinePurchaseRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MedicinePurchaseRepository extends EntityRepository
{

    protected function handleSearchBetween($qb,$data)
    {

        $grn = isset($data['grn'])? $data['grn'] :'';
        $vendor = isset($data['vendor'])? $data['vendor'] :'';
        $medicine = isset($data['name'])? $data['name'] :'';
        $brand = isset($data['brandName'])? $data['brandName'] :'';
        $mode = isset($data['mode'])? $data['mode'] :'';
        $vendorId = isset($data['vendorId'])? $data['vendorId'] :'';
        $startDate = isset($data['startDate'])? $data['startDate'] :'';
        $endDate = isset($data['endDate'])? $data['endDate'] :'';

        if (!empty($grn)) {
            $qb->andWhere($qb->expr()->like("e.grn", "'%$grn%'"  ));
        }
        if(!empty($medicine)){
            $qb->andWhere($qb->expr()->like("ms.name", "'%$medicine%'"  ));
        }
        if(!empty($brand)){
            $qb->andWhere($qb->expr()->like("ms.brandName", "'%$brand%'"  ));
        }
        if(!empty($mode)){
            $qb->andWhere($qb->expr()->like("ms.mode", "'%$mode%'"  ));
        }
        if(!empty($vendor)){
            $qb->join('e.medicineVendor','v');
            $qb->andWhere("v.companyName = :vendor")->setParameter('vendor', $vendor);
        }
        if(!empty($vendorId)){
            $qb->join('e.medicineVendor','v');
            $qb->andWhere("v.id = :vendorId")->setParameter('vendorId', $vendorId);
        }
        if (!empty($startDate) ) {
            $datetime = new \DateTime($data['startDate']);
            $start = $datetime->format('Y-m-d 00:00:00');
            $qb->andWhere("e.created >= :startDate");
            $qb->setParameter('startDate', $start);
        }

        if (!empty($endDate)) {
            $datetime = new \DateTime($data['endDate']);
            $end = $datetime->format('Y-m-d 23:59:59');
            $qb->andWhere("e.created <= :endDate");
            $qb->setParameter('endDate', $end);
        }
    }

    public function findWithSearch($config,$data = array(),$instant = '')
    {

        $qb = $this->createQueryBuilder('e');
        $qb->where('e.medicineConfig = :config')->setParameter('config', $config);
        if (!empty($instant)){
            $qb->andWhere('e.instantPurchase = :instant')->setParameter('instant', $instant);
        }
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('e.created','DESC');
        $qb->getQuery();
        return  $qb;
    }

    public function updatePurchaseTotalPrice(MedicinePurchase $entity)
    {
        $em = $this->_em;
        $total = $em->createQueryBuilder()
            ->from('MedicineBundle:MedicinePurchaseItem','si')
            ->select('sum(si.purchaseSubTotal) as total')
            ->where('si.medicinePurchase = :entity')
            ->setParameter('entity', $entity ->getId())
            ->getQuery()->getSingleResult();

        $subTotal = $total['total'];
        if($subTotal > 0){

            $entity->setSubTotal(round($subTotal));
            $entity->setDiscount(round($this->getUpdateDiscount($entity,$subTotal)));
            $entity->setNetTotal(round($entity->getSubTotal() - $entity->getDiscount()));
            $entity->setDue(round($entity->getNetTotal() - $entity->getPayment()));

        }else{

            $entity->setSubTotal(0);
            $entity->setNetTotal(0);
            $entity->setDue(0);
            $entity->setDiscount(0);
        }

        $em->persist($entity);
        $em->flush();
        return $entity;
    }

    public function updateInvoiceMode(MedicinePurchase $entity)
    {
        $em = $this->_em;
        $entity->setDue(0);
        $entity->setDiscount(0);
        $entity->setDiscountCalculation(0);
        $entity->setNetTotal($entity->getSubTotal());
        $em->persist($entity);
        $em->flush();
    }

    public function checkInstantPurchaseToday(MedicineVendor $vendor)
    {

	    $compare = new \DateTime();
	    $today =  $compare->format('Y-m-d');
	    $sql = "SELECT id
                FROM medicine_purchase as purchase
                WHERE purchase.medicineVendor_id = :vendor AND purchase.process = :process  AND DATE (purchase.receiveDate) =:receive AND  purchase.instantPurchase = :instantPurchase";
	    $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
	    $stmt->bindValue('vendor', $vendor->getId());
	    $stmt->bindValue('process', 'In-progress');
	    $stmt->bindValue('receive', $today);
	    $stmt->bindValue('instantPurchase', 1);
	    $stmt->execute();
	    $result =  $stmt->fetch();
	    if(!empty($result['id'])){
	    	return $data = array('purchase' => $result['id'],'status'=>'valid');
	    }else{
		    return $data = array('status'=>'in-valid');
	    }

    }


    public function insertInstantPurchase(User $user ,$data)
    {

	    $config = $user->getGlobalOption()->getMedicineConfig();
	    $em = $this->_em;

	    $vendor = $em->getRepository('MedicineBundle:MedicineVendor')->checkInInsert($config,$data['vendor']);
	    $check = $this->checkInstantPurchaseToday($vendor);
	    if($check['status'] == 'valid'){
	    	return $entity = $em->getRepository('MedicineBundle:MedicinePurchase')->find($check['purchase']);
	    }else{
		    $entity = new MedicinePurchase();
	    	$entity->setMedicineConfig($config);
		    $entity->setMedicineVendor($vendor);
		    $entity->setInstantPurchase(1);
		    if(!empty($data['purchasesBy'])){
			    $purchaseBy = $em->getRepository('UserBundle:User')->findOneBy(array('username' => $data['purchasesBy']));
			    $entity->setPurchaseBy($purchaseBy);
		    }
		    $entity->setProcess('In-progress');
		    $entity->setMode('instant');
		    $receiveDate = new \DateTime('now');
		    $entity->setReceiveDate($receiveDate);
		    $transactionMethod = $em->getRepository('SettingToolBundle:TransactionMethod')->find(1);
		    $entity->setTransactionMethod($transactionMethod);
		    $em->persist($entity);
		    $em->flush();
		    return $entity;

	    }
    }

    public function getUpdateDiscount(MedicinePurchase $invoice,$subTotal)
    {
        if($invoice->getDiscountType() == 'flat'){
            $discount = $invoice->getDiscountCalculation();
        }else{
            $discount = ($subTotal * $invoice->getDiscountCalculation())/100;
        }
        return round($discount,2);
    }

    public function reportPurchaseOverview(User $user ,$data)
    {
        $global =  $user->getGlobalOption()->getId();

        $qb = $this->_em->createQueryBuilder();
        $qb->from('AccountingBundle:AccountPurchase','e');
        $qb->select('sum(e.purchaseAmount) as total ,sum(e.payment) as totalPayment');
        $qb->where('e.globalOption = :config');
        $qb->setParameter('config', $global);
        $qb->andWhere('e.process = :process');
        $qb->setParameter('process', 'approved');
        $this->handleSearchBetween($qb,$data);
        return $qb->getQuery()->getOneOrNullResult();
    }

	public function medicinePurchaseMonthly(User $user , $data =array())
	{

		$config =  $user->getGlobalOption()->getMedicineConfig()->getId();
		$compare = new \DateTime();
		$year =  $compare->format('Y');
		$year = isset($data['year'])? $data['year'] :$year;
		$sql = "SELECT MONTH (purchase.created) as month,SUM(purchase.netTotal) AS total
                FROM medicine_purchase as purchase
                WHERE purchase.medicineConfig_id = :config AND purchase.process = :process  AND YEAR(purchase.updated) =:year
                GROUP BY month ORDER BY month ASC";
		$stmt = $this->getEntityManager()->getConnection()->prepare($sql);
		$stmt->bindValue('config', $config);
		$stmt->bindValue('process', 'Approved');
		$stmt->bindValue('year', $year);
		$stmt->execute();
		$result =  $stmt->fetchAll();
		return $result;

	}


	public function reportPurchaseTransactionOverview(User $user , $data = array())
    {

        $global =  $user->getGlobalOption()->getId();

        $qb = $this->_em->createQueryBuilder();
        $qb->from('AccountingBundle:AccountPurchase','e');
        $qb->join('e.transactionMethod','t');
        $qb->select('t.name as transactionName , sum(e.purchaseAmount) as total ,sum(e.payment) as totalPayment');
        $qb->where('e.globalOption = :config');
        $qb->setParameter('config', $global);
        $qb->andWhere('e.process = :process');
        $qb->setParameter('process', 'approved');
        $this->handleSearchBetween($qb,$data);
        $qb->groupBy("e.transactionMethod");
        $res = $qb->getQuery();
        return $result = $res->getArrayResult();

    }

    public function reportPurchaseProcessOverview(User $user,$data)
    {
        $config =  $user->getGlobalOption()->getMedicineConfig()->getId();
        $qb = $this->createQueryBuilder('s');
        $qb->select('e.process as name , sum(e.subTotal) as subTotal , sum(e.netTotal) as total ,sum(e.payment) as totalPayment , count(e.id) as totalVoucher, sum(e.due) as totalDue, sum(e.discount) as totalDiscount');
        $qb->where('e.medicineConfig = :config');
        $qb->setParameter('config', $config);
        $this->handleSearchBetween($qb,$data);
        $qb->groupBy("e.process");
        $res = $qb->getQuery();
        return $result = $res->getArrayResult();
    }

    public function reportPurchaseModeOverview(User $user,$data)
    {
        $config =  $user->getGlobalOption()->getMedicineConfig()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->select('e.mode as name , sum(e.netTotal) as total , sum(e.payment) as totalPayment ,  sum(e.due) as totalDue, sum(e.discount) as totalDiscount');
        $qb->where('e.medicineConfig = :config');
        $qb->setParameter('config', $config);
        $qb->andWhere('e.process = :process');
        $qb->setParameter('process', 'Approved');
        $this->handleSearchBetween($qb,$data);
        $qb->groupBy("e.mode");
        $res = $qb->getQuery();
        return $result = $res->getArrayResult();
    }

    public function reportStockModeOverview(User $user,$data)
    {
        $config =  $user->getGlobalOption()->getMedicineConfig()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.medicinePurchaseItems','mpi');
        $qb->join('mpi.medicineStock','ms');
        $qb->join('mpi.medicineSalesItems','msi');
        $qb->select('ms.mode as name , sum(mpi.purchasePrice) as purchasePrice , sum(msi.salesPrice) as salesPrice');
        $qb->where('e.medicineConfig = :config');
        $qb->setParameter('config', $config);
        $qb->andWhere('e.process = :process');
        $qb->setParameter('process', 'Approved');
        $this->handleSearchBetween($qb,$data);
        $qb->groupBy("ms.mode");
        $res = $qb->getQuery();
        return $result = $res->getArrayResult();
    }

    public function purchaseVendorReport(User $user , $data = array())
    {

        $global =  $user->getGlobalOption()->getId();
        $qb = $this->_em->createQueryBuilder();
        $qb->from('AccountingBundle:AccountPurchase','e');
        $qb->join('e.medicineVendor','t');
        $qb->select('t.companyName as companyName ,t.name as vendorName ,t.mobile as vendorMobile , sum(e.purchaseAmount) as total ,sum(e.payment) as payment');
        $qb->where('e.globalOption = :config');
        $qb->setParameter('config', $global);
        $qb->andWhere('e.process = :process');
        $qb->setParameter('process', 'approved');
        $this->handleSearchBetween($qb,$data);
        $qb->groupBy("e.medicineVendor");
        $qb->orderBy("t.companyName",'ASC');
        $res = $qb->getQuery();
        return $result = $res->getArrayResult();

    }

    public function salesVendorCustomerReport(User $user , $data = array())
    {

        $global =  $user->getGlobalOption()->getId();
        $qb = $this->_em->createQueryBuilder();
        $qb->from('AccountingBundle:AccountPurchase','e');
        $qb->join('e.medicineVendor','t');
        $qb->join('t.customer','customer');
        $qb->select('customer.id as customerId ,t.companyName as companyName ,t.name as vendorName ,t.mobile as vendorMobile , sum(e.purchaseAmount) as total ,sum(e.payment) as payment');
        $qb->where('e.globalOption = :config');
        $qb->setParameter('config', $global);
        $qb->andWhere('e.process = :process');
        $qb->setParameter('process', 'approved');
        $qb->andWhere('t.customer is not NULL');
        $this->handleSearchBetween($qb,$data);
        $qb->groupBy("e.medicineVendor");
        $qb->orderBy("t.companyName",'ASC');
        $res = $qb->getQuery();
        return $result = $res->getArrayResult();

    }

    public function vendorCustomerSalesReport(User $user , $customers)
    {

        $array=array();
        foreach ($customers as $customer){
            $array[] = $customer['customerId'];
        }
        $array2 = implode(",",$array);
        $global =  $user->getGlobalOption();
        $customer = '';
        if($customers){
            $customer =  "AND subCustomer.id IN ($array2) ";
        }
        $sql = "SELECT customer.`id` as id, sales.balance as customerBalance
                FROM account_sales as sales
                JOIN Customer as customer ON sales.customer_id = customer.id
                WHERE sales.id IN (
                    SELECT MAX(sub.id)
                    FROM account_sales AS sub
                    JOIN Customer as subCustomer ON sub.customer_id = subCustomer.id
                   WHERE sub.globalOption_id = :globalOption AND sub.process = 'approved' {$customer}
                   GROUP BY sub.customer_id
                ) 
                ORDER BY sales.id DESC";
        $qb = $this->getEntityManager()->getConnection()->prepare($sql);
        $qb->bindValue('globalOption', $global->getId());
        $qb->execute();
        $results =  $qb->fetchAll();
        $salesVendors = array();
        foreach ($results as $row){
            $salesVendors[$row['id']] = $row;
        }
        return $salesVendors;

    }

    public function productPurchaseStockSalesReport(User $user , $data = array())
    {
            $config =  $user->getGlobalOption()->getMedicineConfig()->getId();
            $qb = $this->createQueryBuilder('e');
            $qb->join('e.medicinePurchaseItems','mpi');
            $qb->join('mpi.medicineStock','ms');
            $qb->leftJoin('ms.rackNo','rack');
            $qb->select('ms.name,rack.name AS medicineRack,ms.brandName as brandName, ms.mode as mode , ms.purchaseQuantity as purchaseQuantity , ms.purchaseReturnQuantity as purchaseReturnQuantity, ms.salesQuantity as salesQuantity, ms.salesReturnQuantity as salesReturnQuantity, ms.damageQuantity as damageQuantity, ms.remainingQuantity as remainingQuantity, (ms.remainingQuantity * ms.purchasePrice ) as remainingPurchasePrice, (ms.remainingQuantity * ms.salesPrice ) as remainingSalesPrice');
            $qb->where('e.medicineConfig = :config');
            $qb->setParameter('config', $config);
            $qb->andWhere('e.process = :process');
            $qb->setParameter('process', 'approved');
            $this->handleSearchBetween($qb,$data);
            $qb->groupBy("ms.name");
            $qb->orderBy("ms.name",'ASC');
            $res = $qb->getQuery()->getArrayResult();
            return $res;
    }

    public function productPurchaseStockSalesPriceReport(User $user , $data = array())
    {
        $config =  $user->getGlobalOption()->getMedicineConfig()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.medicinePurchaseItems','mpi');
        $qb->join('mpi.medicineStock','ms');
        $qb->leftJoin('ms.medicineSalesItems','msi');
        $qb->leftJoin('ms.rackNo','rack');
        $qb->select('ms.name,rack.name AS medicineRack,ms.brandName as brandName, ms.mode as mode , sum(mpi.quantity * mpi.purchasePrice) as purchasePrice , sum(mpi.purchaseReturnQuantity * mpi.purchasePrice) as purchaseReturnPrice, sum(msi.salesQuantity * msi.discountPrice) as salesPrice, sum(mpi.salesReturnQuantity * mpi.purchasePrice) as salesReturnPrice, sum(mpi.damageQuantity * mpi.purchasePrice) as damagePrice,  sum(mpi.remainingQuantity * mpi.purchasePrice ) as remainingPurchasePrice, sum(mpi.remainingQuantity * mpi.salesPrice ) as remainingSalesPrice');
        $qb->where('e.medicineConfig = :config');
        $qb->setParameter('config', $config);
        $qb->andWhere('e.process = :process');
        $qb->setParameter('process', 'approved');
        $this->handleSearchBetween($qb,$data);
        $qb->groupBy("ms.name");
        $qb->orderBy("ms.name",'ASC');
        $res = $qb->getQuery();
        return $res;

    }

    public function getPurchaseVendorPrice(User $user , $data = array())
    {
        $config =  $user->getGlobalOption()->getMedicineConfig()->getId();
        $vendors =  $user->getGlobalOption()->getMedicineConfig()->getMedicineVendors();
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.medicinePurchaseItems','mpi');
        $qb->join('e.medicineVendor','t');
        $qb->select('t.id as id , sum(e.netTotal) as purchasePrice , sum(mpi.purchasePrice * mpi.remainingQuantity) as remainingPurchasePrice');
        $qb->where('e.medicineConfig = :config');
        $qb->setParameter('config', $config);
        $qb->andWhere('e.process = :process');
        $qb->setParameter('process', 'approved');
        $qb->andWhere('e.medicineVendor IN (:vendors)');
        $qb->setParameter('vendors', $vendors);
        $this->handleSearchBetween($qb,$data);
        $qb->groupBy("e.medicineVendor");
        $res = $qb->getQuery();
        $results = $res->getArrayResult();
        $purchaseVendors = array();
        foreach ($results as $row){
            $purchaseVendors[$row['id']] = $row;
        }
        return $purchaseVendors;
    }

    public function getSalesVendorPrice(User $user,$data)
    {
        $vendors =  $user->getGlobalOption()->getMedicineConfig()->getMedicineVendors();
        $config =  $user->getGlobalOption()->getMedicineConfig()->getId();
        $qb = $this->_em->createQueryBuilder();
        $qb->from('MedicineBundle:MedicineSalesItem','msi');
        $qb->join('msi.medicineSales','e');
        $qb->join('msi.medicinePurchaseItem','mpi');
        $qb->join('mpi.medicinePurchase','mp');
        $qb->join('mp.medicineVendor','t');
        $qb->select('t.id as id , sum(mpi.purchasePrice) as purchasePrice, sum(msi.salesPrice) as salesPrice');
        $qb->where('e.medicineConfig = :config');
        $qb->setParameter('config', $config);
        $qb->andWhere('e.process = :process');
        $qb->setParameter('process', 'Done');
        $qb->andWhere('mp.medicineVendor IN (:vendors)');
        $qb->setParameter('vendors', $vendors);
        $this->handleSearchBetween($qb,$data);
        $qb->groupBy("mp.medicineVendor");
        $res = $qb->getQuery();
        $results = $res->getArrayResult();
        $salesVendors = array();
        foreach ($results as $row){
            $salesVendors[$row['id']] = $row;
        }
        return $salesVendors;

    }

    public function getPurchaseBrandReport(User $user ,  $brands,  $data = array())
    {

	    $ids = array();
	    foreach ($brands as $y){
		    $ids[]=$y['brandName'];
	    }
        $config =  $user->getGlobalOption()->getMedicineConfig()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.medicinePurchaseItems','mpi');
        $qb->join('mpi.medicineStock','ms');
        $qb->select('ms.brandName as brandName , sum(mpi.purchaseSubTotal) as purchasePrice');
        $qb->where('e.medicineConfig = :config');
        $qb->setParameter('config', $config);
        $qb->andWhere('e.process IN (:process)');
	    $qb->setParameter('process', array('Approved','Complete'));
	    $qb->andWhere('ms.brandName IN (:brands)');
	    $qb->setParameter('brands', $ids);
        $this->handleSearchBetween($qb,$data);
        $qb->groupBy("ms.brandName");
        $res = $qb->getQuery();
        $results = $res->getArrayResult();
        $purchaseBrands = array();
        foreach ($results as $row){
            $purchaseBrands[$row['brandName']] = $row;
        }
        return $purchaseBrands;
    }

    public function getSalesBrandReport(User $user ,$brands, $data = array())
    {

	    $ids = array();
	    foreach ($brands as $y){
		    $ids[]=$y['brandName'];
	    }

    	$config =  $user->getGlobalOption()->getMedicineConfig()->getId();
        $qb = $this->_em->createQueryBuilder();
        $qb->from('MedicineBundle:MedicineSales','e');
        $qb->join('e.medicineSalesItems','mpi');
        $qb->join('mpi.medicineStock','ms');
        $qb->select('ms.brandName as brandName , sum(mpi.discountPrice * mpi.quantity) as salesPrice, sum(mpi.purchasePrice*mpi.quantity) as purchasePrice');
        $qb->where('e.medicineConfig = :config');
        $qb->setParameter('config', $config);
        $qb->andWhere('e.process = :process');
        $qb->setParameter('process', 'Done');
	    $qb->andWhere('ms.brandName IN (:brands)');
	    $qb->setParameter('brands', $ids);
        $this->handleSearchBetween($qb,$data);
        $qb->groupBy("ms.brandName");
        $res = $qb->getQuery();
        $results = $res->getArrayResult();
        $salesBrands = array();
        foreach ($results as $row){
            $salesBrands[$row['brandName']] = $row;
        }
        return $salesBrands;
    }

	public function getPurchaseBrandDetailsReport(User $user, $data = array())
	{

		$config =  $user->getGlobalOption()->getMedicineConfig()->getId();
		$qb = $this->createQueryBuilder('e');
		$qb->join('e.medicinePurchaseItems','mpi');
		$qb->join('mpi.medicineStock','ms');
		$qb->leftJoin('ms.rackNo','r');
		$qb->select('ms.name as name ,ms.brandName as brandName , ms.mode as mode , r.name as rackNo , ms.remainingQuantity as remainingQuantity , sum(mpi.purchaseSubTotal) as purchasePrice');
		$qb->where('e.medicineConfig = :config');
		$qb->setParameter('config', $config);
		$qb->andWhere('e.process IN (:process)');
		$qb->setParameter('process', array('Approved','Complete'));
		$qb->groupBy("ms.name");
		$this->handleSearchBetween($qb,$data);
		$res = $qb->getQuery();
		$results = $res->getArrayResult();
		return $results;
	}

	public function getSalesBrandDetailsReport(User $user, $stocks, $data = array())
	{

		$ids = array();
		foreach ($stocks as $y){
			$ids[]=$y['name'];
		}
		$config =  $user->getGlobalOption()->getMedicineConfig()->getId();
		$qb = $this->_em->createQueryBuilder();
		$qb->from('MedicineBundle:MedicineSales','e');
		$qb->join('e.medicineSalesItems','mpi');
		$qb->join('mpi.medicineStock','ms');
		$qb->select('ms.name as name , sum(mpi.discountPrice * mpi.quantity) as salesPrice, sum(mpi.purchasePrice*mpi.quantity) as purchasePrice');
		$qb->where('e.medicineConfig = :config');
		$qb->setParameter('config', $config);
		$qb->andWhere('e.process = :process');
		$qb->setParameter('process', 'Done');
		$qb->andWhere('ms.name IN (:names)');
		$qb->setParameter('names', $ids);
		$qb->groupBy("ms.name");
		$res = $qb->getQuery();
		$results = $res->getArrayResult();
		$salesBrands = array();
		foreach ($results as $row){
			$salesBrands[$row['name']] = $row;
		}
		return $salesBrands;
	}

    public function purchaseReport( User $user , $data)
    {

        $config =  $user->getGlobalOption()->getMedicineConfig()->getId();

        $qb = $this->createQueryBuilder('e');
        $qb->leftJoin('e.purchaseBy', 'u');
        $qb->leftJoin('e.transactionMethod', 't');
        $qb->select('u.username as purchaseBy');
        $qb->addSelect('t.name as transactionMethod');
        $qb->addSelect('e.id as id');
        $qb->addSelect('e.grn as grn');
        $qb->addSelect('e.created as created');
        $qb->addSelect('e.receiveDate as receiveDate');
        $qb->addSelect('e.process as process');
        $qb->addSelect('e.mode as mode');
        $qb->addSelect('e.invoice as invoice');
        $qb->addSelect('e.due as due');
        $qb->addSelect('e.subTotal as subTotal');
        $qb->addSelect('e.netTotal as total');
        $qb->addSelect('e.payment as payment');
        $qb->addSelect('e.discount as discount');
        $qb->where('e.medicineConfig = :config');
        $qb->setParameter('config', $config);
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('e.created','DESC');
        $result = $qb->getQuery();
        return $result;

    }

}
