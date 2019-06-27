<?php

namespace Appstore\Bundle\AccountingBundle\Repository;
use Appstore\Bundle\AccountingBundle\Entity\AccountJournal;
use Appstore\Bundle\BusinessBundle\Entity\BusinessPurchase;
use Appstore\Bundle\HospitalBundle\Entity\HmsInvoiceReturn;
use Appstore\Bundle\HotelBundle\Entity\HotelPurchase;
use Appstore\Bundle\InventoryBundle\Entity\Purchase;
use Appstore\Bundle\InventoryBundle\Entity\SalesReturn;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchase;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSalesReturn;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

/**
 * AccountJournalRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AccountJournalRepository extends EntityRepository
{


    public function findWithSearch(User $user,$data = '')
    {
        $globalOption = $user->getGlobalOption();
        $branch = $user->getProfile()->getBranches();

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.accountHeadDebit','accountHeadDebit');
        $qb->join('e.accountHeadCredit','accountHeadCredit');
        $qb->join('e.toUser','toUser');
        $qb->join('toUser.profile','profile');
        $qb->leftJoin('e.transactionMethod','t');
        $qb->leftJoin('e.accountMobileBank','amb');
        $qb->leftJoin('e.accountBank','ab');
        $qb->select('e.id as id','e.created as updated','e.accountRefNo as accountRefNo','e.transactionType as transactionType','e.amount as amount','e.remark as remark','e.process as process');
        $qb->addSelect('profile.name as userName');
        $qb->addSelect('accountHeadDebit.name as accountHeadDebitName');
        $qb->addSelect('accountHeadCredit.name as accountHeadCreditName');
        $qb->addSelect('t.name as methodName');
        $qb->addSelect('amb.name as mobileBankName');
        $qb->addSelect('amb.mobile as mobileNo');
        $qb->addSelect('ab.name as bankName');
        $qb->addSelect('ab.accountNo as accountNo');
        $qb->where("e.globalOption = :globalOption");
        $qb->setParameter('globalOption', $globalOption);
        if (!empty($branch)){
            $qb->andWhere("e.branches = :branch");
            $qb->setParameter('branch', $branch);
        }
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('e.updated','DESC');
        $result = $qb->getQuery();
        return $result;
    }

	public function finalTransaction($globalOption)
	{
		//$globalOption = $user->getGlobalOption();

		$qb = $this->createQueryBuilder('e');
		$qb->join('e.accountHeadDebit','d');
		$qb->join('e.accountHeadCredit','c');
		$qb->select('d.name as debitName ,c.name as creditName,e.transactionType as type , SUM(e.amount) AS amount');
		$qb->where("e.globalOption = :globalOption");
		$qb->setParameter('globalOption', $globalOption);
		$qb->andWhere("e.process = 'approved'");
		$qb->groupBy("e.accountHeadDebit,e.accountHeadCredit");
		//$qb->groupBy("e.transactionType,e.accountHeadDebit,e.accountHeadCredit");
		$result = $qb->getQuery()->getArrayResult();
		return $result;

	}

    public function accountCashOverview(User $user,$type,$data)
    {
        $globalOption = $user->getGlobalOption();
        $branch = $user->getProfile()->getBranches();

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.transactionMethod','t');
        $qb->select('SUM(e.amount) AS amount');
        $qb->where("e.globalOption = :globalOption");
        $qb->setParameter('globalOption', $globalOption);
        $qb->andWhere("e.process = 'approved'");
        $qb->andWhere("e.transactionType = :transactionType");
        $qb->setParameter('transactionType', $type);
        if (!empty($branch)){
            $qb->andWhere("e.branches = :branch");
            $qb->setParameter('branch', $branch);
        }
        $this->handleSearchBetween($qb,$data);
        $result = $qb->getQuery()->getOneOrNullResult();
        $amount =  $result['amount'];
        return $amount;

    }

    /**
     * @param $qb
     * @param $data
     */

    protected function handleSearchBetween($qb,$data)
    {
        if(!empty($data))
        {

        	$accountRefNo = isset($data['accountRefNo'])  ? $data['accountRefNo'] : '';
            $toUser = isset($data['toUser']) ? $data['toUser'] :'';
            $accountHead = isset($data['accountHead']) ? $data['accountHead'] :'';
            $startDate = isset($data['startDate']) ? $data['startDate'] : '';
            $endDate =   isset($data['endDate']) ? $data['endDate'] : '';

            if (!empty($accountRefNo)) {
                $qb->andWhere("e.accountRefNo = :accountRefNo");
                $qb->setParameter('accountRefNo', $accountRefNo);
            }

            if (!empty($toUser)) {
	            $qb->join('e.toUser','u');
	            $qb->andWhere("u.username = :toUser");
	            $qb->setParameter('toUser', $toUser);
            }

            if (!empty($accountHead)) {
                $qb->andWhere("e.accountHeadDebit = :accountHeadDebit");
                $qb->setParameter('accountHeadDebit', $accountHead);
             }

			if (!empty($startDate) ) {
				$datetime = new \DateTime($data['startDate']);
				$startDate = $datetime->format('Y-m-d 00:00:00');
		        $qb->andWhere("e.created >= :startDate");
                $qb->setParameter('startDate', $startDate);
            }

            if (!empty($endDate)) {
	            $datetime = new \DateTime($data['endDate']);
	            $date = $datetime->format('Y-m-d 23:59:59');
	            $qb->andWhere("e.created <= :endDate");
	            $qb->setParameter('endDate', $date);
            }
        }
    }

    public function accountJournalOverview($globalOption,$data)
    {
        $qb = $this->_em->createQueryBuilder();
        $datetime = new \DateTime("now");
        $today_startdatetime = $datetime->format('Y-m-d 00:00:00');
        $today_enddatetime = $datetime->format('Y-m-d 23:59:59');

        $startDate = isset($data['startDate']) and $data['startDate'] != '' ? $data['startDate'].' 00:00:00' : $today_startdatetime;
        $endDate =   isset($data['endDate']) and $data['endDate'] != '' ? $data['endDate'].' 23:59:59' : $today_enddatetime;
        $toUser =    isset($data['toUser'])? $data['toUser'] :'';
        $accountHead = isset($data['accountHead'])? $data['accountHead'] :'';

        $qb->from('AccountingBundle:AccountJournal','s');
        $qb->select('sum(s.amount) as amount');
        $qb->where('s.globalOption = :globalOption');
        $qb->setParameter('globalOption', $globalOption);

        if (!empty($startDate) and $startDate !="") {
            $qb->andWhere("s.updated >= :startDate");
            $qb->setParameter('startDate', $startDate);
        }
        if (!empty($endDate)) {
            $qb->andWhere("s.updated <= :endDate");
            $qb->setParameter('endDate', $endDate);
        }
        if (!empty($toUser)) {
	        $qb->join('s.toUser','u');
            $qb->andWhere("u.username = :toUser");
            $qb->setParameter('toUser', $toUser);
        }
        if (!empty($accountHead)) {
            $qb->andWhere("s.accountHead = :accountHead");
            $qb->setParameter('accountHead', $accountHead);
        }

        $amount = $qb->getQuery()->getSingleScalarResult();
        return  $amount ;

    }

    public function reportOperatingRevenue($globalOption,$data){

        $parent = array(23,37);
        $qb = $this->createQueryBuilder('ex');
        $qb->join('ex.accountHeadCredit','accountHead');
        $qb->select('sum(ex.amount) as amount, accountHead.name as name');
        $qb->where("ex.parent IN (:parent)");
        $qb->setParameter('parent', $parent);
        $qb->andWhere('ex.globalOption = :globalOption');
        $qb->setParameter('globalOption', $globalOption);
        $this->handleSearchBetween($qb,$data);
        $qb->groupBy('accountHead.id');
        return  $qb->getQuery()->getArrayResult();
    }


    public function  insertAccountPurchaseJournal(Purchase $purchase)
    {
        $journalSource = "inventory-{$purchase->getId()}";
        $entity = new AccountJournal();
        $accountHeadCredit = $this->_em->getRepository('AccountingBundle:AccountHead')->find(49);
        $accountCashHead = $this->_em->getRepository('AccountingBundle:AccountHead')->find(30);
        $accountBankHead = $this->_em->getRepository('AccountingBundle:AccountHead')->find(38);
        $accountMobileHead = $this->_em->getRepository('AccountingBundle:AccountHead')->find(45);

        $entity->setGlobalOption($purchase->getInventoryConfig()->getGlobalOption());
        $entity->setTransactionType('Debit');
        $entity->setAmount($purchase->getPaymentAmount());
        $entity->setTransactionMethod($purchase->getTransactionMethod());
        $entity->setAccountBank($purchase->getAccountBank());
        $entity->setAccountMobileBank($purchase->getAccountMobileBank());
        $entity->setApprovedBy($purchase->getApprovedBy());
        $entity->setCreatedBy($purchase->getApprovedBy());
        $entity->setAccountHeadCredit($accountHeadCredit);
        if ($purchase->getTransactionMethod()->getId() == 2){
            $entity->setAccountHeadDebit($accountBankHead);
        }elseif ($purchase->getTransactionMethod()->getId() == 3){
            $entity->setAccountHeadDebit($accountMobileHead);
        }else{
            $entity->setAccountHeadDebit($accountCashHead);
        }
        $entity->setToUser($purchase->getApprovedBy());
        $entity->setJournalSource($journalSource);
        $entity->setRemark("Inventory purchase as investment,Ref GRN no.{$purchase->getGrn()}");
        $entity->setProcess('approved');
        $this->_em->persist($entity);
        $this->_em->flush();
        return $entity;
    }

    public function removeApprovedPurchaseJournal(Purchase $purchase)
    {
        $option =  $purchase->getInventoryConfig()->getGlobalOption()->getId();
        $journalSource = "inventory-{$purchase->getId()}";
        $journal = $this->_em->getRepository('AccountingBundle:AccountJournal')->findOneBy(array('approvedBy' => $purchase->getApprovedBy(),'globalOption'=> $option ,'amount'=> $purchase->getPaymentAmount(),'journalSource' => $journalSource ));
        if(!empty($journal)) {
            $accountCash = $this->_em->getRepository('AccountingBundle:AccountCash')->findOneBy(array('processHead' => 'Journal', 'globalOption' => $option, 'accountRefNo' => $journal->getAccountRefNo()));
            if ($accountCash) {
                $this->_em->remove($accountCash);
                $this->_em->flush();
            }

            $transactions = $this->_em->getRepository('AccountingBundle:Transaction')->findBy(array('processHead' => 'Journal', 'globalOption' => $journal->getGlobalOption(), 'accountRefNo' => $journal->getAccountRefNo()));
            foreach ($transactions as $transaction) {
                if ($transaction) {
                    $this->_em->remove($transaction);
                    $this->_em->flush();
                }
            }
            $this->_em->remove($journal);
            $this->_em->flush();
        }
    }

    public function   insertAccountMedicinePurchaseJournal(MedicinePurchase $purchase)
    {

        $journalSource = "medicine-{$purchase->getId()}";
        $entity = new AccountJournal();
        $accountHeadCredit = $this->_em->getRepository('AccountingBundle:AccountHead')->find(49);
        $accountCashHead = $this->_em->getRepository('AccountingBundle:AccountHead')->find(30);
        $accountBankHead = $this->_em->getRepository('AccountingBundle:AccountHead')->find(38);
        $accountMobileHead = $this->_em->getRepository('AccountingBundle:AccountHead')->find(45);

        $entity->setGlobalOption($purchase->getMedicineConfig()->getGlobalOption());
        $entity->setTransactionType('Debit');
        $entity->setAmount($purchase->getPayment());
        $entity->setTransactionMethod($purchase->getTransactionMethod());
        $entity->setAccountBank($purchase->getAccountBank());
        $entity->setAccountMobileBank($purchase->getAccountMobileBank());
        $entity->setApprovedBy($purchase->getApprovedBy());
        $entity->setCreatedBy($purchase->getApprovedBy());
        $entity->setAccountHeadCredit($accountHeadCredit);
        if ($purchase->getTransactionMethod()->getId() == 2){
            $entity->setAccountHeadDebit($accountBankHead);
        }elseif ($purchase->getTransactionMethod()->getId() == 3){
            $entity->setAccountHeadDebit($accountMobileHead);
        }else{
            $entity->setAccountHeadDebit($accountCashHead);
        }
        $entity->setToUser($purchase->getApprovedBy());
        $entity->setJournalSource($journalSource);
        $entity->setRemark("Medicine purchase as investment,Ref GRN no.{$purchase->getGrn()}");
        $entity->setProcess('approved');
        $entity->setCreated($purchase->getCreated());
        $entity->setUpdated($purchase->getUpdated());
        $this->_em->persist($entity);
        $this->_em->flush();
        return $entity;
    }

    public function removeApprovedMedicinePurchaseJournal(MedicinePurchase $purchase)
    {
        $option =  $purchase->getMedicineConfig()->getGlobalOption()->getId();
        $journalSource = "medicine-{$purchase->getId()}";
        $journal = $this->_em->getRepository('AccountingBundle:AccountJournal')->findOneBy(array('approvedBy' => $purchase->getApprovedBy(),'globalOption'=> $option,'journalSource' => $journalSource ));
        $em = $this->_em;
        if(!empty($journal)) {

                /* @var  $journal AccountJournal */

                $globalOption = $journal->getGlobalOption()->getId();
                $accountRefNo = $journal->getAccountRefNo();

                $transaction = $em->createQuery("DELETE AccountingBundle:Transaction e WHERE e.globalOption = ".$globalOption ." AND e.accountRefNo =".$accountRefNo." AND e.processHead = 'Journal'");
                $transaction->execute();
                $accountCash = $em->createQuery("DELETE AccountingBundle:AccountCash e WHERE e.globalOption = ".$globalOption ." AND e.accountRefNo =".$accountRefNo." AND e.processHead = 'Journal'");
                $accountCash->execute();
                $journalRemove = $em->createQuery('DELETE AccountingBundle:AccountJournal e WHERE e.id = '.$journal->getId());
                if(!empty($journalRemove)){
                    $journalRemove->execute();
                }
        }

    }

	public function accountReverse(AccountJournal $entity)
	{
		$em = $this->_em;
		$transaction = $em->createQuery("DELETE AccountingBundle:Transaction e WHERE e.globalOption = ".$entity->getGlobalOption()->getId() ." AND e.accountRefNo =".$entity->getAccountRefNo()." AND e.processHead = 'Journal'");
		$transaction->execute();
		$accountCash = $em->createQuery("DELETE AccountingBundle:AccountCash e WHERE e.globalOption = ".$entity->getGlobalOption()->getId() ." AND e.accountJournal =".$entity->getId()." AND e.processHead = 'Journal'");
		$accountCash->execute();
	}

	public function insertInventoryAccountSalesReturn(SalesReturn $salesReturn)
	{
		$global = $salesReturn->getInventoryConfig()->getGlobalOption();
		$accountSales = new AccountJournal();
		$accountSales->setGlobalOption($global);

		$journalSource = "Sales-Return-{$salesReturn->getSales()->getInvoice()}";
		$entity = new AccountJournal();
		$accountCashHead = $this->_em->getRepository('AccountingBundle:AccountHead')->find(34);
		$accountHeadCredit = $this->_em->getRepository('AccountingBundle:AccountHead')->find(30);
		$transaction = $this->_em->getRepository('SettingToolBundle:TransactionMethod')->find(1);

		$entity->setTransactionType('Credit');
		$entity->setAmount($salesReturn->getTotal());
		$entity->setTransactionMethod($transaction);
		$entity->setApprovedBy($salesReturn->getCreatedBy());
		$entity->setCreatedBy($salesReturn->getCreatedBy());
		$entity->setGlobalOption($salesReturn->getCreatedBy()->getGlobalOption());
		$entity->setAccountHeadCredit($accountHeadCredit);
		$entity->setAccountHeadDebit($accountCashHead);
		$entity->setToUser($salesReturn->getCreatedBy());
		$entity->setRemark("Inventory sales return as assets,Ref Invoice no-{$salesReturn->getSales()->getInvoice()}");
		$entity->setJournalSource($journalSource);
		$entity->setProcess('approved');
		$this->_em->persist($entity);
		$this->_em->flush();
		$this->_em->getRepository('AccountingBundle:AccountCash')->insertAccountCash($entity);
		$this->_em->getRepository('AccountingBundle:Transaction')->insertAccountJournalTransaction($entity);
		return $entity->getAccountRefNo();

	}

	public function insertMedicineAccountSalesReturn(MedicineSalesReturn $salesReturn)
	{
		$global = $salesReturn->getMedicineConfig()->getGlobalOption();
		$sales = $salesReturn->getMedicineSalesItem()->getMedicineSales();
		$accountSales = new AccountJournal();
		$accountSales->setGlobalOption($global);

		$journalSource = "Sales-Return-{$sales->getId()}";
		$entity = new AccountJournal();
		$accountCashHead = $this->_em->getRepository('AccountingBundle:AccountHead')->find(6);
		$accountHeadCredit = $this->_em->getRepository('AccountingBundle:AccountHead')->find(30);
		$transaction = $this->_em->getRepository('SettingToolBundle:TransactionMethod')->find(1);
     	$entity->setTransactionType('Credit');
		$entity->setAmount($salesReturn->getSubTotal());
		$entity->setTransactionMethod($transaction);
		$entity->setApprovedBy($salesReturn->getCreatedBy());
		$entity->setCreatedBy($salesReturn->getCreatedBy());
		$entity->setGlobalOption($salesReturn->getCreatedBy()->getGlobalOption());
		$entity->setAccountHeadCredit($accountHeadCredit);
		$entity->setAccountHeadDebit($accountCashHead);
		$entity->setJournalSource($journalSource);
		$entity->setProcess('approved');
		$this->_em->persist($entity);
		$this->_em->flush();
		$this->_em->getRepository('AccountingBundle:AccountCash')->insertAccountCash($entity);
		$this->_em->getRepository('AccountingBundle:Transaction')->insertAccountJournalTransaction($entity);
		return $entity->getAccountRefNo();

	}

	public function hmsReturnInvoice(HmsInvoiceReturn $salesReturn)
	{
		$global = $salesReturn->getHospitalConfig()->getGlobalOption();
		$sales = $salesReturn->getHmsInvoice();

		$accountSales = new AccountJournal();
		$accountSales->setGlobalOption($global);

        $journalSource = "Sales-Return-{$sales->getId()}";
		$remark = "Diagnostic-Invoice ID: {$sales->getInvoice()}, " .$salesReturn->getRemark();

		$entity = new AccountJournal();
		$accountCashHead = $this->_em->getRepository('AccountingBundle:AccountHead')->find(34);
		$accountHeadCredit = $this->_em->getRepository('AccountingBundle:AccountHead')->find(30);
		$transaction = $this->_em->getRepository('SettingToolBundle:TransactionMethod')->find(1);

     	$entity->setTransactionType('Credit');
		$entity->setAmount($salesReturn->getAmount());
		$entity->setTransactionMethod($transaction);
		$entity->setApprovedBy($salesReturn->getCreatedBy());
		$entity->setCreatedBy($salesReturn->getCreatedBy());
		$entity->setGlobalOption($salesReturn->getCreatedBy()->getGlobalOption());
		$entity->setAccountHeadCredit($accountHeadCredit);
		$entity->setAccountHeadDebit($accountCashHead);
		$entity->setToUser($salesReturn->getCreatedBy());
		$entity->setJournalSource($journalSource);
		$entity->setRemark($remark);
		$entity->setProcess('approved');
		$this->_em->persist($entity);
		$this->_em->flush();
		$this->_em->getRepository('AccountingBundle:AccountCash')->insertAccountCash($entity);
		$this->_em->getRepository('AccountingBundle:Transaction')->insertAccountJournalTransaction($entity);
		return $entity->getAccountRefNo();
	}

	public function insertAccountBusinessPurchaseJournal(BusinessPurchase $purchase)
	{

		$journalSource = "business-{$purchase->getId()}";
		$entity = new AccountJournal();
		$accountHeadCredit = $this->_em->getRepository('AccountingBundle:AccountHead')->find(49);
		$accountCashHead = $this->_em->getRepository('AccountingBundle:AccountHead')->find(30);
		$accountBankHead = $this->_em->getRepository('AccountingBundle:AccountHead')->find(38);
		$accountMobileHead = $this->_em->getRepository('AccountingBundle:AccountHead')->find(45);

		$entity->setGlobalOption($purchase->getBusinessConfig()->getGlobalOption());
		$entity->setTransactionType('Debit');
		$entity->setAmount($purchase->getPayment());
		$entity->setTransactionMethod($purchase->getTransactionMethod());
		$entity->setAccountBank($purchase->getAccountBank());
		$entity->setAccountMobileBank($purchase->getAccountMobileBank());
		$entity->setApprovedBy($purchase->getApprovedBy());
		$entity->setCreatedBy($purchase->getApprovedBy());
		$entity->setAccountHeadCredit($accountHeadCredit);
		if ($purchase->getTransactionMethod()->getId() == 2){
			$entity->setAccountHeadDebit($accountBankHead);
		}elseif ($purchase->getTransactionMethod()->getId() == 3){
			$entity->setAccountHeadDebit($accountMobileHead);
		}else{
			$entity->setAccountHeadDebit($accountCashHead);
		}
		$entity->setToUser($purchase->getApprovedBy());
		$entity->setJournalSource($journalSource);
		$entity->setRemark("Business purchase as investment,Ref GRN no.{$purchase->getGrn()}");
		$entity->setProcess('approved');
		$this->_em->persist($entity);
		$this->_em->flush();
		return $entity;
	}

	public function removeApprovedBusinessPurchaseJournal(BusinessPurchase $purchase)
	{
		$option =  $purchase->getBusinessConfig()->getGlobalOption()->getId();
		$journalSource = "business-{$purchase->getId()}";
		$journal = $this->_em->getRepository('AccountingBundle:AccountJournal')->findOneBy(array('approvedBy' => $purchase->getApprovedBy(),'globalOption'=> $option,'journalSource' => $journalSource ));
		$em = $this->_em;
		if(!empty($journal)) {

			/* @var  $journal AccountJournal */

			$globalOption = $journal->getGlobalOption()->getId();
			$accountRefNo = $journal->getAccountRefNo();

			$transaction = $em->createQuery("DELETE AccountingBundle:Transaction e WHERE e.globalOption = ".$globalOption ." AND e.accountRefNo =".$accountRefNo." AND e.processHead = 'Journal'");
			$transaction->execute();
			$accountCash = $em->createQuery("DELETE AccountingBundle:AccountCash e WHERE e.globalOption = ".$globalOption ." AND e.accountRefNo =".$accountRefNo." AND e.processHead = 'Journal'");
			$accountCash->execute();
			$journalRemove = $em->createQuery('DELETE AccountingBundle:AccountJournal e WHERE e.id = '.$journal->getId());
			if(!empty($journalRemove)){
				$journalRemove->execute();
			}
		}

	}

	public function insertAccountHotelPurchaseJournal(HotelPurchase $purchase)
	{

		$journalSource = "hotel-{$purchase->getId()}";
		$entity = new AccountJournal();
		$accountHeadCredit = $this->_em->getRepository('AccountingBundle:AccountHead')->find(49);
		$accountCashHead = $this->_em->getRepository('AccountingBundle:AccountHead')->find(30);
		$accountBankHead = $this->_em->getRepository('AccountingBundle:AccountHead')->find(38);
		$accountMobileHead = $this->_em->getRepository('AccountingBundle:AccountHead')->find(45);

		$entity->setGlobalOption($purchase->getHotelConfig()->getGlobalOption());
		$entity->setTransactionType('Debit');
		$entity->setAmount($purchase->getPayment());
		$entity->setTransactionMethod($purchase->getTransactionMethod());
		$entity->setAccountBank($purchase->getAccountBank());
		$entity->setAccountMobileBank($purchase->getAccountMobileBank());
		$entity->setApprovedBy($purchase->getApprovedBy());
		$entity->setCreatedBy($purchase->getApprovedBy());
		$entity->setAccountHeadCredit($accountHeadCredit);
		if ($purchase->getTransactionMethod()->getId() == 2){
			$entity->setAccountHeadDebit($accountBankHead);
		}elseif ($purchase->getTransactionMethod()->getId() == 3){
			$entity->setAccountHeadDebit($accountMobileHead);
		}else{
			$entity->setAccountHeadDebit($accountCashHead);
		}
		$entity->setToUser($purchase->getApprovedBy());
		$entity->setJournalSource($journalSource);
		$entity->setRemark("Hotel purchase as investment,Ref GRN no.{$purchase->getGrn()}");
		$entity->setProcess('approved');
		$this->_em->persist($entity);
		$this->_em->flush();
		return $entity;
	}

	public function removeApprovedHotelPurchaseJournal(HotelPurchase $purchase)
	{
		$option =  $purchase->getHotelConfig()->getGlobalOption()->getId();
		$journalSource = "hotel-{$purchase->getId()}";
		$journal = $this->_em->getRepository('AccountingBundle:AccountJournal')->findOneBy(array('approvedBy' => $purchase->getApprovedBy(),'globalOption'=> $option,'journalSource' => $journalSource ));
		$em = $this->_em;
		if(!empty($journal)) {

			/* @var  $journal AccountJournal */

			$globalOption = $journal->getGlobalOption()->getId();
			$accountRefNo = $journal->getAccountRefNo();

			$transaction = $em->createQuery("DELETE AccountingBundle:Transaction e WHERE e.globalOption = ".$globalOption ." AND e.accountRefNo =".$accountRefNo." AND e.processHead = 'Journal'");
			$transaction->execute();
			$accountCash = $em->createQuery("DELETE AccountingBundle:AccountCash e WHERE e.globalOption = ".$globalOption ." AND e.accountRefNo =".$accountRefNo." AND e.processHead = 'Journal'");
			$accountCash->execute();
			$journalRemove = $em->createQuery('DELETE AccountingBundle:AccountJournal e WHERE e.id = '.$journal->getId());
			if(!empty($journalRemove)){
				$journalRemove->execute();
			}
		}

	}


}
