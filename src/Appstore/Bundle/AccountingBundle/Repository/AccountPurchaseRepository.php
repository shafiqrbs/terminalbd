<?php

namespace Appstore\Bundle\AccountingBundle\Repository;
use Appstore\Bundle\AccountingBundle\Entity\AccountJournal;
use Appstore\Bundle\AccountingBundle\Entity\AccountPurchase;
use Appstore\Bundle\BusinessBundle\Entity\BusinessPurchase;
use Appstore\Bundle\DmsBundle\Entity\DmsPurchase;
use Appstore\Bundle\HospitalBundle\Entity\HmsPurchase;
use Appstore\Bundle\HotelBundle\Entity\HotelPurchase;
use Appstore\Bundle\InventoryBundle\Entity\Purchase;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseReturn;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchaseReturn;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchase;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Setting\Bundle\ToolBundle\Event\Glo;


/**
 * AccountPurchaseRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AccountPurchaseRepository extends EntityRepository
{

    public function findWithSearch($globalOption,$data = '')
    {
        $qb = $this->createQueryBuilder('e');
        $qb->where("e.globalOption = :globalOption");
        $qb->setParameter('globalOption', $globalOption->getId());
        $this->handleSearchBetween($qb,$globalOption,$data);
        $qb->orderBy('e.created','DESC');
        $result = $qb->getQuery();
        return $result;

    }

	public function searchAutoComplete($q, GlobalOption $global)
	{
		$qb = $this->createQueryBuilder('e');
		$qb->select('e.companyName as id');
		$qb->addSelect('e.companyName as text');
		$qb->where("e.globalOption = :global")->setParameter('global', $global->getId());
		$qb->andWhere($qb->expr()->like("e.companyName", "'$q%'" ));
		$qb->groupBy('e.companyName');
		$qb->orderBy('e.companyName', 'ASC');
		$qb->setMaxResults( '30' );
		return $qb->getQuery()->getResult();

	}

    public function updateVendorBalance(AccountPurchase $accountPurchase){

        $qb = $this->createQueryBuilder('e');
        $qb->select('SUM(e.purchaseAmount) AS purchaseAmount, SUM(e.payment) AS payment');
        $qb->where("e.globalOption = :globalOption")->setParameter('globalOption', $accountPurchase->getGlobalOption()->getId());
        $qb->andWhere("e.process = 'approved'");
	    $qb->andWhere("e.companyName = :company")->setParameter('company', $accountPurchase->getCompanyName());
        $result = $qb->getQuery()->getSingleResult();
        $balance = ($result['purchaseAmount'] -  $result['payment']);
        $accountPurchase->setBalance($balance);
        $this->_em->flush();
        return $accountPurchase;

    }


    public function accountPurchaseOverview(User $user ,$data)
    {
        $globalOption = $user->getGlobalOption();
        $qb = $this->createQueryBuilder('e');
        $qb->select('SUM(e.purchaseAmount) AS purchaseAmount, SUM(e.payment) AS payment');
        $qb->where("e.globalOption = :globalOption");
        $qb->setParameter('globalOption', $globalOption);
        $qb->andWhere("e.process = :process");
        $qb->setParameter('process', 'approved');
        $this->handleSearchBetween($qb,$globalOption,$data);
        $result = $qb->getQuery()->getOneOrNullResult();
        $data =  array('purchaseAmount'=> $result['purchaseAmount'],'payment'=> $result['payment']);
        return $data;

    }

    public function vendorLedgerOutstanding(GlobalOption $globalOption)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('vendor.id as vendorId ,vendor.companyName as companyName ,vendor.name as vendorName , vendor.mobile as vendorMobile,(SUM(e.purchaseAmount) - SUM(e.payment)) as customerBalance ');
        if ($globalOption->getMainApp()->getSlug() == 'inventory'){
            $qb->join('e.vendor','vendor');
        }else if ($globalOption->getMainApp()->getSlug() == 'miss') {
            $qb->join('e.medicineVendor','vendor');
        }else{
            $qb->join('e.accountVendor','vendor');
        }
        $qb->where("e.globalOption = :globalOption")->setParameter('globalOption', $globalOption->getId());
        $qb->andWhere("e.process = 'approved'");
        $qb->groupBy('vendor.id');
        $qb->having('customerBalance > :balance')->setParameter('balance', 0);
        $qb->orHaving('customerBalance < :balance')->setParameter('balance', 0);
        $qb->orderBy('vendor.companyName','ASC');
        $result = $qb->getQuery()->getArrayResult();
        return $result;
    }



    public function vendorInventoryOutstanding($globalOption,$head, $data = array())
    {

        $mode = isset($data['outstanding'])  ? $data['outstanding'] : 'Payable';
        $amount =   isset($data['amount'])  ? $data['amount'] : 1;
        $vendor =   isset($data['vendor'])  ? $data['vendor'] : '';

        $outstanding = '';
        $company = '';
        if($vendor){
            $company =  "AND subVendor.companyName LIKE '%{$vendor}%'";
        }
        if($mode == 'Receivable'  and $amount !="" ){
            $outstanding ="AND purchase.balance <= -{$amount}";
        }elseif($mode == 'Payable' and $amount !=""){
            $outstanding ="AND purchase.balance >= {$amount}";
        }else{
            $outstanding ="AND purchase.balance >= 1";
        }
        $sql = "SELECT vendor.`companyName` as companyName , vendor.mobile as vendorMobile,vendor.name as vendorName,purchase.balance as customerBalance
                FROM account_purchase as purchase
                LEFT JOIN supplier as vendor ON purchase.vendor_id = vendor.id
                WHERE purchase.id IN (
                    SELECT MAX(sub.id)
                    FROM account_purchase AS sub
                    LEFT JOIN supplier as subVendor ON sub.vendor_id = subVendor.id
                   WHERE sub.globalOption_id = :globalOption AND sub.processHead = :head AND sub.process = 'approved' {$company}
                    GROUP BY sub.vendor_id
                  
                ) {$outstanding}
                ORDER BY purchase.id DESC";
        $qb = $this->getEntityManager()->getConnection()->prepare($sql);
        $qb->bindValue('globalOption', $globalOption->getId());
        $qb->bindValue('head', $head);
        $qb->execute();
        $result =  $qb->fetchAll();
        return $result;

    }

    public function vendorMedicineOutstanding($globalOption)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('vendor.id as vendorId ,vendor.companyName as companyName ,vendor.name as vendorName , vendor.mobile as vendorMobile,(SUM(e.purchaseAmount) - SUM(e.payment)) as customerBalance ');
        $qb->join('e.medicineVendor','vendor');
        $qb->where("e.globalOption = :globalOption")->setParameter('globalOption', $globalOption->getId());
        $qb->andWhere("e.process = 'approved'");
        $qb->groupBy('vendor.id');
        $qb->having('customerBalance > :balance')->setParameter('balance', 0);
        $qb->orderBy('vendor.id','ASC');
        $result = $qb->getQuery()->getArrayResult();
        return $result;
    }

    public function vendorMedicineOutstandingOld($globalOption,$data = array())
    {

        $mode = isset($data['outstanding'])  ? $data['outstanding'] : 'Payable';
        $amount =   isset($data['amount']) ? $data['amount'] : 1;
        $vendor =   isset($data['vendor']) ? $data['vendor'] : '';

        $outstanding = '';
        $company = '';
        if($vendor){
            $company =  "AND subVendor.companyName LIKE '%{$vendor}%'";
        }
        if($mode == 'Receivable'  and $amount !="" ){
            $outstanding ="AND purchase.balance <= -{$amount}";
        }elseif($mode == 'Payable' and $amount !=""){
            $outstanding ="AND purchase.balance >= {$amount}";
        }else{
            $outstanding ="AND purchase.balance >= 1";
        }
        $sql = "SELECT vendor.`companyName` as companyName , vendor.mobile as vendorMobile,vendor.name as vendorName, (SUM(sub.purchaseAmount) - SUM(sub.payment)) as customerBalance
FROM account_purchase AS sub
LEFT JOIN medicine_vendor as vendor ON sub.medicineVendor_id = vendor.id
WHERE sub.globalOption_id  = :globalOption AND sub.process = 'approved'
GROUP BY sub.medicineVendor_id
HAVING customerBalance > 0 ORDER BY vendor.`companyName` ASC";
        $qb = $this->getEntityManager()->getConnection()->prepare($sql);
        $qb->bindValue('globalOption', $globalOption->getId());
        $qb->execute();
        $result =  $qb->fetchAll();
        return $result;

    }

    public function vendorBusinessOutstanding($globalOption,$data = array())
    {

        $mode = isset($data['outstanding'])  ? $data['outstanding'] : 'Payable';
        $amount =   isset($data['amount'])  ? $data['amount'] : 1;
        $vendor =   isset($data['accountVendor'])  ? $data['accountVendor'] : '';
        $outstanding = '';
        $company = '';
        if($vendor){
            $company =  "AND subVendor.companyName LIKE '%{$vendor}%'";
        }
        if($mode == 'Receivable'  and $amount !="" ){
            $outstanding ="AND purchase.balance <= -{$amount}";
        }elseif($mode == 'Payable' and $amount !=""){
            $outstanding ="AND purchase.balance >= {$amount}";
        }else{
            $outstanding ="AND purchase.balance >= 1";
        }
        $sql = "SELECT vendor.`companyName` as companyName , vendor.mobile as vendorMobile,vendor.name as vendorName, purchase.balance as customerBalance
                FROM account_purchase as purchase
                LEFT JOIN account_vendor as vendor ON purchase.accountVendor_id = vendor.id
                WHERE purchase.id IN (
                    SELECT MAX(sub.id)
                    FROM account_purchase AS sub
                    LEFT JOIN account_vendor as subVendor ON sub.accountVendor_id = subVendor.id
                   WHERE sub.globalOption_id = :globalOption AND sub.process = 'approved' {$company}
                    GROUP BY sub.companyName
                  
                ) {$outstanding}
                ORDER BY purchase.id DESC";
        $qb = $this->getEntityManager()->getConnection()->prepare($sql);
        $qb->bindValue('globalOption', $globalOption->getId());
        $qb->execute();
        $result =  $qb->fetchAll();
        return $result;

    }

	public function vendorSingleOutstanding($globalOption,$head = '',$vendor)
	{

		$qb = $this->createQueryBuilder('e');
		$qb->select('SUM(e.purchaseAmount) - SUM(e.payment) As balance');
		$qb->where("e.globalOption = :globalOption");
		$qb->setParameter('globalOption', $globalOption);
		$qb->andWhere("e.process = :process");
		$qb->setParameter('process', 'approved');
        if($globalOption->getMainApp()->getSlug() == 'miss'){
            $qb->join('e.medicineVendor','v');
        }elseif($globalOption->getMainApp()->getSlug() == 'inventory'){
            $qb->join('e.vendor','v');
        }else{
            $qb->join('e.accountVendor','v');
        }
		$qb->andWhere("v.id = :vendor")->setParameter('vendor', $vendor);
		$result = $qb->getQuery()->getOneOrNullResult()['balance'];
		return $result;

	}

    public function vendorLedger(GlobalOption $globalOption,$data)
    {
        $startDate = isset($data['startDate'])  ? $data['startDate'] : '';
        $endDate =   isset($data['endDate'])  ? $data['endDate'] : '';
        $vendor =    isset($data['vendor'])? $data['vendor'] :'';
        $qb = $this->createQueryBuilder('e');
        $qb->select('e');
        $qb->where("e.globalOption = :globalOption");
        $qb->setParameter('globalOption', $globalOption);
        $qb->andWhere("e.process = :process");
        $qb->setParameter('process', 'approved');
        if($globalOption->getMainApp()->getSlug() == 'miss'){
            $qb->join('e.medicineVendor','v');
        }elseif($globalOption->getMainApp()->getSlug() == 'inventory'){
            $qb->join('e.vendor','v');
        }else{
            $qb->join('e.accountVendor','v');
        }
        $qb->andWhere("v.companyName = :vendor");
        $qb->setParameter('vendor', $vendor);
        if (!empty($startDate) and !empty($endDate) ) {
            $compareTo = new \DateTime($startDate);
            $startDate =  $compareTo->format('Y-m-d 00:00:00');
            $qb->andWhere("e.created >= :startDate");
            $qb->setParameter('startDate', $startDate);
        }
        if (!empty($startDate) and !empty($endDate) ) {
            $compareTo = new \DateTime($endDate);
            $endDate =  $compareTo->format('Y-m-d 23:59:59');
            $qb->andWhere("e.created <= :endDate");
            $qb->setParameter('endDate', $endDate);
        }
        $qb->orderBy('e.created', 'DESC');
        $result = $qb->getQuery();
        return $result;

    }



    public function vendorOutstanding($globalOption,$head,$data)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('SUM(e.purchaseAmount) AS purchaseAmount, SUM(e.payment) AS payment');
	    $qb->addSelect('e.companyName as vendorName');
        $qb->where("e.globalOption = :globalOption");
        $qb->setParameter('globalOption', $globalOption);
        $qb->andWhere("e.process = :process");
        $qb->setParameter('process', 'approved');
	    $qb->groupBy('e.companyName');
        $qb->andWhere("e.processHead = :head");
        $qb->setParameter('head', $head);
        $this->handleSearchOutstanding($qb,$data);
        $result = $qb->getQuery()->getArrayResult();
        return $result;

    }

    /**
     * @param $qb
     * @param $data
     */

    protected function handleSearchBetween($qb,GlobalOption $globalOption,$data)
    {
        if(empty($data))
        {
                $datetime = new \DateTime("now");
                $startDate = $datetime->format('Y-m-d 00:00:00');
                $endDate = $datetime->format('Y-m-d 23:59:59');
        }else{

                $grn = isset($data['grn'])  ? $data['grn'] : '';
                $startDate = isset($data['startDate'])  ? $data['startDate'] : '';
                $endDate =   isset($data['endDate'])  ? $data['endDate'] : '';
                $vendor =    isset($data['vendor'])? $data['vendor'] :'';
                $processHead =    isset($data['processHead'])? $data['processHead'] :'';
	            $transactionMethod =    isset($data['transactionMethod'])? $data['transactionMethod'] :'';

                $globalOption->getMainApp()->getSlug();
                if($globalOption->getMainApp()->getSlug() == 'miss'){
                    $qb->leftJoin('e.medicineVendor','v');
                }elseif($globalOption->getMainApp()->getSlug() == 'inventory'){
                    $qb->leftJoin('e.vendor','v');
                }else{
                    $qb->leftJoin('e.accountVendor','v');
                }
                if(!empty($vendor)){
                    $qb->andWhere("v.companyName = :vendor");
                    $qb->setParameter('vendor', $vendor);
                }
                if (!empty($startDate) and !empty($endDate) ) {
	                $compareTo = new \DateTime($startDate);
	                $startDate =  $compareTo->format('Y-m-d 00:00:00');
	                $qb->andWhere("e.created >= :startDate");
                    $qb->setParameter('startDate', $startDate);
                }
                if (!empty($startDate) and !empty($endDate) ) {
	                $compareTo = new \DateTime($endDate);
	                $endDate =  $compareTo->format('Y-m-d 23:59:59');
	                $qb->andWhere("e.created <= :endDate");
                    $qb->setParameter('endDate', $endDate);
                }
                if (!empty($grn)) {
                    $qb->andWhere("e.grn = :grn");
                    $qb->setParameter('grn', $grn);
                }
                if (!empty($processHead)) {
                   $qb->andWhere("e.processHead = :process");
                   $qb->setParameter('process', $processHead);
                }

	            if (!empty($transactionMethod)) {
		        $qb->andWhere("e.transactionMethod = :transactionMethod");
		        $qb->setParameter('transactionMethod', $transactionMethod);
	            }


        }

    }

    protected function handleSearchOutstanding($qb,$data)
    {

            $startDate = isset($data['startDate'])  ? $data['startDate'] : '';
            $endDate =   isset($data['endDate'])  ? $data['endDate'] : '';
            $inventoryVendor =    isset($data['vendor'])? $data['vendor'] :'';
            $hmsVendor =    isset($data['hmsVendor'])? $data['hmsVendor'] :'';
            $medicineVendor =    isset($data['medicineVendor'])? $data['medicineVendor'] :'';
            $transactionMethod =    isset($data['transactionMethod'])? $data['transactionMethod'] :'';

		    if (!empty($startDate) and !empty($endDate) ) {
			    $compareTo = new \DateTime($startDate);
			    $startDate =  $compareTo->format('Y-m-d 00:00:00');
			    $qb->andWhere("e.created >= :startDate");
			    $qb->setParameter('startDate', $startDate);
		    }
		    if (!empty($startDate) and !empty($endDate) ) {
			    $compareTo = new \DateTime($endDate);
			    $endDate =  $compareTo->format('Y-m-d 23:59:59');
			    $qb->andWhere("e.created <= :endDate");
			    $qb->setParameter('endDate', $endDate);

		    }
		    if (!empty($vendor)) {
			    $qb->andWhere("e.companyName = :vendor");
			    $qb->setParameter('vendor', $vendor);
		    }
            if (!empty($transactionMethod)) {
                $qb->andWhere("e.transactionMethod = :transactionMethod");
                $qb->setParameter('transactionMethod', $transactionMethod);
            }
    }

    public function lastInsertPurchase($globalOption,$vendor)
    {
        $em = $this->_em;
        $entity = $em->getRepository('AccountingBundle:AccountPurchase')->findOneBy(
            array('globalOption' => $globalOption,'trader' => $vendor,'process'=>'approved'),
            array('id' => 'DESC')
        );

        if (empty($entity)) {
            return 0;
        }
        return $entity->getBalance();
    }

    public function lastInsertHmsPurchase($globalOption,$vendor)
    {
        $em = $this->_em;
        $entity = $em->getRepository('AccountingBundle:AccountPurchase')->findOneBy(
            array('globalOption' => $globalOption,'hmsVendor' => $vendor,'process'=>'approved'),
            array('id' => 'DESC')
        );

        if (empty($entity)) {
            return 0;
        }
        return $entity->getBalance();
    }

	public function accountReverse(AccountPurchase $entity)
	{
		$em = $this->_em;
		$transaction = $em->createQuery("DELETE AccountingBundle:Transaction e WHERE e.globalOption = ".$entity->getGlobalOption()->getId() ." AND e.accountRefNo =".$entity->getAccountRefNo()." AND e.processHead = 'Purchase'");
		$transaction->execute();
        $accountCash = $em->createQuery("DELETE AccountingBundle:AccountCash e WHERE e.globalOption = {$entity->getGlobalOption()->getId()} AND e.accountRefNo ={$entity->getAccountRefNo()} AND e.accountPurchase ={$entity->getId()} AND e.processHead = 'Purchase'");
        $accountCash->execute();
	}

	public function insertAccountPurchase(Purchase $entity)
    {

        $em = $this->_em;
        $accountPurchase = new AccountPurchase();
        $accountPurchase->setGlobalOption($entity->getInventoryConfig()->getGlobalOption());
        $accountPurchase->setPurchase($entity);
	    $accountPurchase->setAccountBank( $entity->getAccountBank() );
	    $accountPurchase->setAccountMobileBank( $entity->getAccountMobileBank() );
	    $accountPurchase->setVendor($entity->getVendor());
        $accountPurchase->setTransactionMethod($entity->getTransactionMethod());
        $accountPurchase->setPurchaseAmount($entity->getTotalAmount());
        $accountPurchase->setPayment($entity->getPaymentAmount());
        $accountPurchase->setProcessHead('inventory');
        $accountPurchase->setProcessType('Purchase');
        $accountPurchase->setCompanyName($entity->getVendor()->getCompanyName());
        $accountPurchase->setGrn($entity->getGrn());
        $accountPurchase->setReceiveDate($entity->getReceiveDate());
        $accountPurchase->setProcess('approved');
        $accountPurchase->setApprovedBy($entity->getApprovedBy());
        $em->persist($accountPurchase);
        $em->flush();
        $this->updateVendorBalance($accountPurchase);
        $this->_em->getRepository('AccountingBundle:AccountCash')->insertPurchaseCash($accountPurchase);
        return $accountPurchase;

    }

    public function insertDmsAccountPurchase(DmsPurchase $entity)
    {


       /* $em = $this->_em;
        $accountPurchase = new AccountPurchase();
        $accountPurchase->setGlobalOption($entity->getDmsConfig()->getGlobalOption());
        $accountPurchase->setDmsPurchase($entity);
	    $accountPurchase->setAccountBank( $entity->getAccountBank());
	    $accountPurchase->setAccountMobileBank( $entity->getAccountMobileBank() );
	    $accountPurchase->setDmsVendor($entity->getDmsVendor());
        $accountPurchase->setTransactionMethod($entity->getTransactionMethod());
        $accountPurchase->setPurchaseAmount($entity->getNetTotal());
        $accountPurchase->setPayment($entity->getPayment());
        $accountPurchase->setReceiveDate($entity->getReceiveDate());
        $accountPurchase->setBalance(($balance + $entity->getNetTotal()) - $accountPurchase->getPayment() );
	    $accountPurchase->setCompanyName($entity->getDmsVendor()->getCompanyName());
	    $accountPurchase->setGrn($entity->getGrn());
	    $accountPurchase->setProcessHead('dms');
        $accountPurchase->setProcessType('Purchase');
        $accountPurchase->setProcess('approved');
        $accountPurchase->setApprovedBy($entity->getApprovedBy());
        $em->persist($accountPurchase);
        $em->flush();
	    $this->updateVendorBalance($accountPurchase);
        if($accountPurchase->getPayment() > 0 ){
            $this->_em->getRepository('AccountingBundle:AccountCash')->insertPurchaseCash($accountPurchase);
        }
        return $accountPurchase;*/

    }

    public function insertMedicineAccountPurchase(MedicinePurchase $entity)
    {

        $global = $entity->getMedicineConfig()->getGlobalOption();
        $em = $this->_em;
        $accountPurchase = new AccountPurchase();
        $accountPurchase->setGlobalOption($global);
        $accountPurchase->setMedicinePurchase($entity);
        $accountPurchase->setMedicineVendor($entity->getMedicineVendor());
	    $accountPurchase->setAccountBank( $entity->getAccountBank() );
	    $accountPurchase->setAccountMobileBank( $entity->getAccountMobileBank() );
	    if (!empty( $entity->getTransactionMethod())) {
		    $accountPurchase->setTransactionMethod( $entity->getTransactionMethod() );
	    }
        $accountPurchase->setPurchaseAmount($entity->getNetTotal());
        $accountPurchase->setPayment($entity->getPayment());
        $accountPurchase->setCompanyName($entity->getMedicineVendor()->getCompanyName());
	    $accountPurchase->setGrn($entity->getGrn());
	    $accountPurchase->setProcessHead('medicine');
        $accountPurchase->setProcessType('Purchase');
        $accountPurchase->setCreated($entity->getCreated());
        $accountPurchase->setUpdated($entity->getUpdated());
        $accountPurchase->setProcess('approved');
        $accountPurchase->setApprovedBy($entity->getApprovedBy());
        $em->persist($accountPurchase);
        $em->flush();
        $this->updateVendorBalance($accountPurchase);
        if($accountPurchase->getPayment() > 0 ){
            $this->_em->getRepository('AccountingBundle:AccountCash')->insertPurchaseCash($accountPurchase);
        }
        return $accountPurchase;

    }

    public function insertMedicineAccountPurchaseReturn(MedicinePurchaseReturn $entity)
    {
        $global = $entity->getMedicineConfig()->getGlobalOption();
        $em = $this->_em;
        $accountPurchase = new AccountPurchase();
        $accountPurchase->setGlobalOption($global);
        $accountPurchase->setMedicineVendor($entity->getMedicineVendor());
        $accountPurchase->setPayment($entity->getTotal());
	    $accountPurchase->setCompanyName($entity->getMedicineVendor()->getCompanyName());
	    $accountPurchase->setGrn($entity->getInvoice());
	    $accountPurchase->setProcessHead('medicine');
        $accountPurchase->setProcessType('Purchase-return');
        $accountPurchase->setProcess('approved');
        $accountPurchase->setApprovedBy($entity->getApprovedBy());
        $em->persist($accountPurchase);
        $em->flush();
        $this->updateVendorBalance($accountPurchase);
        return $accountPurchase;

    }

    public function insertHmsAccountPurchase(HmsPurchase $entity)
    {

        $em = $this->_em;
        $accountPurchase = new AccountPurchase();
        $accountPurchase->setGlobalOption($entity->getHospitalConfig()->getGlobalOption());
        $accountPurchase->setHmsPurchase($entity);
	    $accountPurchase->setAccountBank( $entity->getAccountBank() );
	    $accountPurchase->setAccountMobileBank( $entity->getAccountMobileBank() );
	    $accountPurchase->setHmsVendor($entity->getVendor());
        $accountPurchase->setTransactionMethod($entity->getTransactionMethod());
        $accountPurchase->setPurchaseAmount($entity->getNetTotal());
        $accountPurchase->setPayment($entity->getPayment());
	    $accountPurchase->setCompanyName($entity->getVendor()->getCompanyName());
	    $accountPurchase->setGrn($entity->getGrn());
	    $accountPurchase->setProcess('hms');
        $accountPurchase->setProcessType('Purchase');
        $accountPurchase->setReceiveDate($entity->getReceiveDate());
        $accountPurchase->setProcess('approved');
        $accountPurchase->setApprovedBy($entity->getApprovedBy());
        $em->persist($accountPurchase);
        $em->flush();
        if($accountPurchase->getPayment() > 0 ){
            $this->_em->getRepository('AccountingBundle:AccountCash')->insertPurchaseCash($accountPurchase);
        }
        return $accountPurchase;

    }

    public function insertRestaurantAccountPurchase(\Appstore\Bundle\RestaurantBundle\Entity\Purchase $entity)
    {

        $em = $this->_em;
        $accountPurchase = new AccountPurchase();
        $accountPurchase->setGlobalOption($entity->getRestaurantConfig()->getGlobalOption());
        $accountPurchase->setRestaurantPurchase($entity);
        $accountPurchase->setAccountVendor($entity->getVendor());
	    $accountPurchase->setAccountBank( $entity->getAccountBank() );
	    $accountPurchase->setAccountMobileBank( $entity->getAccountMobileBank() );
	    $accountPurchase->setTransactionMethod($entity->getTransactionMethod());
        $accountPurchase->setPurchaseAmount($entity->getNetTotal());
        $accountPurchase->setPayment($entity->getPayment());
	    $accountPurchase->setCompanyName($entity->getVendor()->getCompanyName());
	    $accountPurchase->setGrn($entity->getGrn());
	    $accountPurchase->setProcessHead('restaurant');
        $accountPurchase->setProcessType('Purchase');
        $accountPurchase->setReceiveDate($entity->getReceiveDate());
        $accountPurchase->setProcess('approved');
        $accountPurchase->setUpdated($entity->getUpdated());
        $accountPurchase->setApprovedBy($entity->getApprovedBy());
        $em->persist($accountPurchase);
        $em->flush();
        $this->updateVendorBalance($accountPurchase);
        if($accountPurchase->getPayment() > 0 ){
            $this->_em->getRepository('AccountingBundle:AccountCash')->insertPurchaseCash($accountPurchase);
        }
        return $accountPurchase;

    }

	public function insertBusinessAccountPurchase(BusinessPurchase $entity) {

		$global          = $entity->getBusinessConfig()->getGlobalOption();
		$em              = $this->_em;
		$accountPurchase = new AccountPurchase();
		$accountPurchase->setGlobalOption( $global );
		$accountPurchase->setBusinessPurchase( $entity );
		$accountPurchase->setAccountVendor( $entity->getVendor() );
		$accountPurchase->setAccountBank( $entity->getAccountBank() );
		$accountPurchase->setAccountMobileBank( $entity->getAccountMobileBank() );
		if (!empty( $entity->getTransactionMethod())) {
			$accountPurchase->setTransactionMethod( $entity->getTransactionMethod() );
		}
		$accountPurchase->setPurchaseAmount($entity->getNetTotal());
		$accountPurchase->setPayment($entity->getPayment());
		$accountPurchase->setCompanyName($entity->getVendor()->getCompanyName());
		$accountPurchase->setGrn($entity->getGrn());
		$accountPurchase->setProcessHead('business');
		$accountPurchase->setProcessType('Purchase');
		$accountPurchase->setUpdated($entity->getUpdated());
		$accountPurchase->setProcess('approved');
		$accountPurchase->setApprovedBy($entity->getApprovedBy());
		$em->persist($accountPurchase);
		$em->flush();
		$this->updateVendorBalance($accountPurchase);
		if($accountPurchase->getPayment() > 0 ){
			$this->_em->getRepository('AccountingBundle:AccountCash')->insertPurchaseCash($accountPurchase);
		}
		return $accountPurchase;

	}

	public function insertHotelAccountPurchase(HotelPurchase $entity) {

		$global          = $entity->getHotelConfig()->getGlobalOption();
		$em              = $this->_em;
		$accountPurchase = new AccountPurchase();
		$accountPurchase->setGlobalOption( $global );
		$accountPurchase->setHotelPurchase($entity);
		$accountPurchase->setAccountVendor( $entity->getVendor() );
		$accountPurchase->setAccountBank( $entity->getAccountBank() );
		$accountPurchase->setAccountMobileBank( $entity->getAccountMobileBank() );
		if (!empty( $entity->getTransactionMethod())) {
			$accountPurchase->setTransactionMethod( $entity->getTransactionMethod() );
		}
		$accountPurchase->setPurchaseAmount($entity->getNetTotal());
		$accountPurchase->setPayment($entity->getPayment());
		$accountPurchase->setCompanyName($entity->getVendor()->getCompanyName());
		$accountPurchase->setGrn($entity->getGrn());
		$accountPurchase->setProcessHead('hotel');
		$accountPurchase->setProcessType('Purchase');
		$accountPurchase->setReceiveDate($entity->getReceiveDate());
		$accountPurchase->setProcess('approved');
		$accountPurchase->setApprovedBy($entity->getApprovedBy());
		$em->persist($accountPurchase);
		$em->flush();
		$this->updateVendorBalance($accountPurchase);
		if($accountPurchase->getPayment() > 0 ){
			$this->_em->getRepository('AccountingBundle:AccountCash')->insertPurchaseCash($accountPurchase);
		}
		return $accountPurchase;

	}


    public function removeApprovedAccountPurchase(Purchase $purchase)
    {

        $accountPurchase = $purchase->getAccountPurchase();
        if(!empty($accountPurchase)) {
            $accountCash = $this->_em->getRepository('AccountingBundle:AccountCash')->findOneBy(array('processHead' => 'Purchase', 'globalOption' => $accountPurchase->getGlobalOption(), 'accountRefNo' => $accountPurchase->getAccountRefNo()));
            if ($accountCash) {
                $this->_em->remove($accountCash);
                $this->_em->flush();
            }
            $transactions = $this->_em->getRepository('AccountingBundle:Transaction')->findBy(array('processHead' => 'Purchase', 'globalOption' => $accountPurchase->getGlobalOption(), 'accountRefNo' => $accountPurchase->getAccountRefNo()));
            foreach ($transactions as $transaction) {
                if ($transaction) {
                    $this->_em->remove($transaction);
                    $this->_em->flush();
                }
            }

        }

    }

    public function accountPurchaseReverse(Purchase $entity)
    {
        $em = $this->_em;
        if(!empty($entity->getAccountPurchase())){
            /* @var AccountPurchase $purchase */
            foreach ($entity->getAccountPurchase() as $purchase ){
                $globalOption = $purchase->getGlobalOption()->getId();
                $accountRefNo = $purchase->getAccountRefNo();
                $transaction = $em->createQuery("DELETE AccountingBundle:Transaction e WHERE e.globalOption = ".$globalOption ." AND e.accountRefNo =".$accountRefNo." AND e.processHead = 'Purchase'");
                $transaction->execute();
                $accountCash = $em->createQuery("DELETE AccountingBundle:AccountCash e WHERE e.globalOption = ".$globalOption ." AND e.accountRefNo =".$accountRefNo." AND e.processHead = 'Purchase'");
                $accountCash->execute();
            }
        }
        $accountCash = $em->createQuery('DELETE AccountingBundle:AccountPurchase e WHERE e.purchase = '.$entity->getId());
        if(!empty($accountCash)){
            $accountCash->execute();
        }
    }

    public function accountMedicinePurchaseReverse(MedicinePurchase $entity)
    {
        $em = $this->_em;
        if(!empty($entity->getAccountPurchases())){
            /* @var AccountPurchase $purchase */
            foreach ($entity->getAccountPurchases() as $purchase ){
                $globalOption = $purchase->getGlobalOption()->getId();
                $accountRefNo = $purchase->getAccountRefNo();
                $transaction = $em->createQuery("DELETE AccountingBundle:Transaction e WHERE e.globalOption = ".$globalOption ." AND e.accountRefNo =".$accountRefNo." AND e.processHead = 'Purchase'");
                $transaction->execute();
                $accountCash = $em->createQuery("DELETE AccountingBundle:AccountCash e WHERE e.globalOption = ".$globalOption ." AND e.accountRefNo =".$accountRefNo." AND e.processHead = 'Purchase'");
                $accountCash->execute();
            }
        }
        $accountCash = $em->createQuery('DELETE AccountingBundle:AccountPurchase e WHERE e.medicinePurchase = '.$entity->getId());
        if(!empty($accountCash)){
            $accountCash->execute();
        }
    }



	public function accountBusinessPurchaseReverse(BusinessPurchase $entity)
	{
		$em = $this->_em;
		if(!empty($entity->getAccountPurchase())){
			/* @var AccountPurchase $purchase */
			foreach ($entity->getAccountPurchase() as $purchase ){
				$globalOption = $purchase->getGlobalOption()->getId();
				$accountRefNo = $purchase->getAccountRefNo();
				$transaction = $em->createQuery("DELETE AccountingBundle:Transaction e WHERE e.globalOption = ".$globalOption ." AND e.accountRefNo =".$accountRefNo." AND e.processHead = 'Purchase'");
				$transaction->execute();
				$accountCash = $em->createQuery("DELETE AccountingBundle:AccountCash e WHERE e.globalOption = ".$globalOption ." AND e.accountRefNo =".$accountRefNo." AND e.processHead = 'Purchase'");
				$accountCash->execute();
			}
		}
		$accountCash = $em->createQuery('DELETE AccountingBundle:AccountPurchase e WHERE e.businessPurchase = '.$entity->getId());
		if(!empty($accountCash)){
			$accountCash->execute();
		}
	}


}
