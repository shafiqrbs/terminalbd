<?php

namespace Appstore\Bundle\AccountingBundle\Repository;
use Appstore\Bundle\AccountingBundle\Entity\AccountBalanceTransfer;
use Appstore\Bundle\AccountingBundle\Entity\AccountBank;
use Appstore\Bundle\AccountingBundle\Entity\AccountCash;
use Appstore\Bundle\AccountingBundle\Entity\AccountJournal;
use Appstore\Bundle\AccountingBundle\Entity\AccountOnlineOrder;
use Appstore\Bundle\AccountingBundle\Entity\AccountPurchase;
use Appstore\Bundle\AccountingBundle\Entity\AccountPurchaseCommission;
use Appstore\Bundle\AccountingBundle\Entity\AccountPurchaseReturn;
use Appstore\Bundle\AccountingBundle\Entity\AccountSales;
use Appstore\Bundle\AccountingBundle\Entity\AccountSalesReturn;
use Appstore\Bundle\AccountingBundle\Entity\Expenditure;
use Appstore\Bundle\AccountingBundle\Entity\PaymentSalary;
use Appstore\Bundle\AccountingBundle\Entity\PettyCash;
use Appstore\Bundle\AccountingBundle\Entity\Transaction;
use Appstore\Bundle\DmsBundle\Entity\DmsInvoice;
use Appstore\Bundle\DmsBundle\Entity\DmsTreatmentPlan;
use Appstore\Bundle\InventoryBundle\Entity\Purchase;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseReturn;
use Core\UserBundle\Entity\User;
use Core\UserBundle\UserBundle;
use Doctrine\ORM\EntityRepository;

/**
 * AccountCashRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AccountCashRepository extends EntityRepository
{

    public function openingBalance(User $user,$transactionMethods = [], $data =[]){

        $globalOption = $user->getGlobalOption();
        $branch = $user->getProfile()->getBranches();
        if(isset($data['startDate'])){
            $date = new \DateTime($data['startDate']);
        }else{
            $date = new \DateTime();
        }
        $date->add(\DateInterval::createFromDateString('yesterday'));
        $tillDate = $date->format('Y-m-d 23:59:59');
        $accountBank =    isset($data['accountBank'])? $data['accountBank'] :'';
        $accountMobileBank =    isset($data['accountMobileBank'])? $data['accountMobileBank'] :'';

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.transactionMethod','t');
        $qb->select('COALESCE(SUM(e.debit)) AS debit, COALESCE(SUM(e.credit)) AS credit');
        $qb->where("e.globalOption = :globalOption");
        $qb->setParameter('globalOption', $globalOption);
        if (!empty($branch)){
            $qb->andWhere("e.branches = :branch");
            $qb->setParameter('branch', $branch);
        }
        if(!empty($transactionMethods)){
            $qb->andWhere("t.id IN(:transactionMethod)");
            $qb->setParameter('transactionMethod',array_values($transactionMethods));
        }
        if (!empty($accountBank)) {
            $qb->andWhere("e.accountBank = :accountBank");
            $qb->setParameter('accountBank', $accountBank);
        }

        if (!empty($accountMobileBank)) {
            $qb->andWhere("e.accountMobileBank = :accountMobileBank");
            $qb->setParameter('accountMobileBank', $accountMobileBank);
        }

        $qb->andWhere("e.updated <= :updated");
        $qb->setParameter('updated', $tillDate);
        $result = $qb->getQuery()->getOneOrNullResult();
        $openingBalance = ( $result['debit'] - $result['credit']);
        return $openingBalance;
    }

    public function openingBalanceGroup(User $user,$transactionMethods,$data){

        $globalOption = $user->getGlobalOption();
        $branch = $user->getProfile()->getBranches();
        if(isset($data['startDate'])){
            $date = new \DateTime($data['startDate']);
        }else{
            $date = new \DateTime();
        }
        $date->add(\DateInterval::createFromDateString('yesterday'));
        $tillDate = $date->format('Y-m-d 23:59:59');
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.transactionMethod','t');
        $qb->select('(COALESCE(SUM(e.debit),0) - COALESCE(SUM(e.credit),0)) AS balance');
        $qb->where("e.globalOption = :globalOption");
        $qb->setParameter('globalOption', $globalOption);
        if (!empty($branch)){
            $qb->andWhere("e.branches = :branch");
            $qb->setParameter('branch', $branch);
        }
        $qb->andWhere("e.updated <= :updated");
        $qb->setParameter('updated', $tillDate);
        $openingBalance = $qb->getQuery()->getOneOrNullResult()['balance'];
        return $openingBalance;
    }

    public function cashOverview(User $user,$transactionMethods,$data)
    {
        $globalOption = $user->getGlobalOption();
        $branch = $user->getProfile()->getBranches();

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.transactionMethod','t');
        $qb->select('COALESCE(SUM(e.debit),0) AS debit, COALESCE(SUM(e.credit),0) AS credit');
        $qb->where("e.globalOption = :globalOption");
        $qb->setParameter('globalOption', $globalOption);
        if (!empty($branch)){
            $qb->andWhere("e.branches = :branch");
            $qb->setParameter('branch', $branch);
        }
        $qb->andWhere("t.id IN(:transactionMethod)");
        $qb->setParameter('transactionMethod',array_values($transactionMethods));
        $this->handleSearchBetween($qb,$data);
        $result = $qb->getQuery()->getOneOrNullResult();
        $openingBalance = $this->openingBalance($user,$transactionMethods,$data);
        $data =  array('openingBalance'=> $openingBalance , 'debit'=> $result['debit'],'credit'=> $result['credit']);
        return $data;

    }

    public function transactionWiseOverview(User $user,$data = array())
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->from('SettingToolBundle:TransactionMethod','e');
        $qb->select('e.name AS transactionName');
        $qb->addSelect('e.id AS transactionId');
        $result = $qb->getQuery()->getArrayResult();
        $openingBalances = array();
        $transactionBalances = array();
        foreach($result as $row) {
            $openingBalances[$row['transactionId']]         = $this->openingBalance($user,array($row['transactionId']),$data);
            $transactionBalances[$row['transactionId']]     = $this->transactionCashOverview($user,$row['transactionId'],$data);
        }
        $data =  array('result' => $result,'openingBalance' => $openingBalances , 'transactionBalances'=> $transactionBalances);
        return $data;


    }

    public function transactionCashOverview(User $user,$method,$data)
    {
        $globalOption = $user->getGlobalOption();
        $branch = $user->getProfile()->getBranches();
        $qb = $this->createQueryBuilder('e');
        $qb->select('COALESCE(SUM(e.debit),0) AS debit, COALESCE(SUM(e.credit),0) AS credit');
        $qb->where("e.globalOption = :globalOption");
        $qb->setParameter('globalOption', $globalOption);
        $qb->andWhere("e.transactionMethod = :transactionMethod");
        $qb->setParameter('transactionMethod',$method);

        if (!empty($branch)){
            $qb->andWhere("e.branches = :branch");
            $qb->setParameter('branch', $branch);
        }
        if(!empty($method)){
            $qb->andWhere("e.transactionMethod = :transactionMethod");
            $qb->setParameter('transactionMethod',$method);
        }
        $this->handleSearchBetween($qb,$data);
        $result = $qb->getQuery()->getOneOrNullResult();
        return $result;

    }

    public function transactionBankCashOverview(User $user, $data = '')
    {
        $globalOption = $user->getGlobalOption();
        $qb = $this->_em->createQueryBuilder();
        $qb->from('AccountingBundle:AccountBank','accountBank');
        $qb->select('accountBank.id AS accountId , accountBank.name AS bankName');
        $qb->where("accountBank.globalOption = :globalOption");
        $qb->setParameter('globalOption', $globalOption->getId());
        $result = $qb->getQuery()->getArrayResult();
        $transactionBankCash = [];
        $openingBalances = [];
        foreach($result as $row) {
            $transactionBankCash[$row['accountId']]     = $this->transactionBankCash($row['accountId'],$data);
            $openingBalances[$row['accountId']]         = $this->openingBalance($user,[2],['accountBank'=> $row['accountId']]);
        }
        $arrs = ['result' => $result,'openingBalance' => $openingBalances , 'transactionBankCash' => $transactionBankCash];
        return $arrs;
    }

    public function transactionBankCash($bank,$data)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->leftJoin('e.accountBank','a');
      //  $qb->select('SUM(e.debit) AS debit, SUM(e.credit) AS credit');
        $qb->select('COALESCE(SUM(e.debit),0) AS debit, COALESCE(SUM(e.credit),0) AS credit');
        $qb->where("a.id = :bank");
        $qb->setParameter('bank', $bank);
        $this->handleSearchBetween($qb,$data);
        $result = $qb->getQuery()->getOneOrNullResult();
        return $result;

    }

    public function transactionMobileBankCashOverview(User $user ,$data = '')
    {

        $globalOption = $user->getGlobalOption();
        $qb = $this->_em->createQueryBuilder();
        $qb->from('AccountingBundle:AccountMobileBank','accountMobileBank');
        $qb->select('accountMobileBank.id AS accountId , accountMobileBank.name AS mobileBankName');
        $qb->where("accountMobileBank.globalOption = :globalOption");
        $qb->setParameter('globalOption', $globalOption);
        $result = $qb->getQuery()->getArrayResult();
        $transactionMobileCash = [];
        $openingBalances = [];
        foreach($result as $row) {
            $transactionMobileCash[$row['accountId']]     = $this->transactionMobileCash($row['accountId'],$data);
            $openingBalances[$row['accountId']] = $this->openingBalance($user,array(3),array('accountMobileBank'=> $row['accountId']));
        }
        $arrs = ['result' => $result,'openingBalance' => $openingBalances , 'transactionMobileCash' => $transactionMobileCash];
        return $arrs;
    }

    public function transactionMobileCash($mobile,$data)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->leftJoin('e.accountMobileBank','a');
        $qb->select('COALESCE(SUM(e.debit),0) AS debit, COALESCE(SUM(e.credit),0) AS credit');
        $qb->where("a.id = :mobile");
        $qb->setParameter('mobile', $mobile);
        $this->handleSearchBetween($qb,$data);
        $result = $qb->getQuery()->getOneOrNullResult();
        return $result;
    }

    public function transactionAccountHeadCashOverview(User $user,$data = '')
    {

        $globalOption = $user->getGlobalOption();
        $branch = $user->getProfile()->getBranches();

        $qb = $this->createQueryBuilder('e');
        $qb->select('e.processHead as name , COALESCE(SUM(e.debit),0) AS debit, COALESCE(SUM(e.credit),0) AS credit');
        $qb->where("e.globalOption = :globalOption");
        $qb->setParameter('globalOption', $globalOption);
        if (!empty($branch)){
            $qb->andWhere("e.branches = :branch");
            $qb->setParameter('branch', $branch);
        }
        $this->handleSearchBetween($qb,$data);
        $qb->groupBy("e.processHead");
        $result = $qb->getQuery()->getArrayResult();
        return $result;

    }

	public function cashReceivePayment(User $user,$data = '')
	{

		$globalOption = $user->getGlobalOption();
		$branch = $user->getProfile()->getBranches();

		$qb = $this->createQueryBuilder('e');
		$qb->join('e.transactionMethod','t');
		$qb->where("e.globalOption = :globalOption");
		$qb->setParameter('globalOption', $globalOption);
		if (!empty($branch)){
			$qb->andWhere("e.branches = :branch");
			$qb->setParameter('branch', $branch);
		}
		//$qb->andWhere("t.id IN(:transactionMethod)");
		//$qb->setParameter('transactionMethod',array_values($transactionMethods));
		$this->handleSearchBetween($qb,$data);
		$qb->orderBy('e.updated','DESC');
		$result = $qb->getQuery();
		return $result;

	}

    public function findWithSearch(User $user,$transactionMethods,$data = '')
    {

        $globalOption = $user->getGlobalOption();
        $branch = $user->getProfile()->getBranches();

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.transactionMethod','t');
        $qb->where("e.globalOption = :globalOption");
        $qb->setParameter('globalOption', $globalOption);
        if (!empty($branch)){
            $qb->andWhere("e.branches = :branch");
            $qb->setParameter('branch', $branch);
        }
        $qb->andWhere("t.id IN(:transactionMethod)");
        $qb->setParameter('transactionMethod',array_values($transactionMethods));
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('e.updated','DESC');
        $result = $qb->getQuery();
        return $result;

    }


    /**
     * @param $qb
     * @param $data
     */

    protected function handleSearchBetween($qb,$data)
    {
        $accountRefNo = isset($data['accountRefNo'])  ? $data['accountRefNo'] : '';
        $startDate = isset($data['startDate'])  ? $data['startDate'] : '';
        $endDate = isset($data['endDate'])  ? $data['endDate'] : '';
        $process =    isset($data['processHead'])? $data['processHead'] :'';
        $accountBank =    isset($data['accountBank'])? $data['accountBank'] :'';
        $accountMobileBank =    isset($data['accountMobileBank'])? $data['accountMobileBank'] :'';

        if (!empty($process)) {
            $qb->andWhere("e.processHead = :process");
            $qb->setParameter('process', $process);
        }
        if (!empty($accountRefNo)) {
            $qb->andWhere("e.accountRefNo = :accountRefNo");
            $qb->setParameter('accountRefNo', $accountRefNo);
        }

        $compareStart = new \DateTime();
        if (!empty($startDate) ) {
        $compareStart = new \DateTime($startDate);
        }
        $start =  $compareStart->format('Y-m-d 00:00:01');
        $qb->andWhere("e.updated >= :startDate");
        $qb->setParameter('startDate', $start);

        $compareEnd = new \DateTime();
        if (!empty($endDate) ) {
        $compareEnd = new \DateTime($endDate);
        }
        $end =  $compareEnd->format('Y-m-d 23:59:59');
        $qb->andWhere("e.updated <= :endDate");
        $qb->setParameter('endDate', $end);

        if (!empty($accountBank)) {
            $qb->andWhere("e.accountBank = :accountBank");
            $qb->setParameter('accountBank', $accountBank);
        }

        if (!empty($accountMobileBank)) {
            $qb->andWhere("e.accountMobileBank = :accountMobileBank");
            $qb->setParameter('accountMobileBank', $accountMobileBank);
        }

    }


    public function lastInsertCash($entity,$processHead)
    {
        $em = $this->_em;

        if($entity->getTransactionMethod()->getId() == 2){
            $array = array('globalOption' => $entity->getGlobalOption(),'transactionMethod' => $entity->getTransactionMethod(),'accountBank' => $entity->getAccountBank(), 'processHead' => $processHead );
        }elseif($entity->getTransactionMethod()->getId() == 3 ){
            $array = array('globalOption' => $entity->getGlobalOption(),'transactionMethod' => $entity->getTransactionMethod(),'accountMobileBank' => $entity->getAccountMobileBank(), 'processHead' => $processHead );
        }else{
            $array = array('globalOption' => $entity->getGlobalOption(),'transactionMethod' => $entity->getTransactionMethod(), 'processHead' => $processHead );
        }

        $entity = $em->getRepository('AccountingBundle:AccountCash')->findOneBy($array,array('id' => 'DESC'));

        if (empty($entity)) {
            return 0;
        }
        return $entity->getBalance();
    }

    public function insertAccountCash(AccountJournal $entity , $processHead ='Journal')
    {

        $balance = $this->lastInsertCash($entity,$processHead);
        $em = $this->_em;
        $cash = new AccountCash();

        $cash->setAccountBank($entity->getAccountBank());
        $cash->setAccountMobileBank($entity->getAccountMobileBank());
        $cash->setGlobalOption($entity->getGlobalOption());
        $cash->setAccountJournal($entity);
        $cash->setToUser($entity->getToUser());
        $cash->setTransactionMethod($entity->getTransactionMethod());
        $cash->setProcessHead($processHead);
        $cash->setAccountRefNo($entity->getAccountRefNo());
        if(!empty($entity->getBranches())){
            $cash->setBranches($entity->getBranches());
        }
        $cash->setUpdated($entity->getUpdated());
        if($entity->getTransactionType()  == 'Debit' ){
            $cash->setAccountHead($entity->getAccountHeadDebit());
            $cash->setDebit($entity->getAmount());
            $cash->setBalance($balance + $entity->getAmount());
        }else{
            $cash->setAccountHead($entity->getAccountHeadCredit());
            $cash->setBalance($balance - $entity->getAmount() );
            $cash->setCredit($entity->getAmount());
        }
        $em->persist($cash);
        $em->flush();

    }

	public function balanceTransferAccountCash(AccountBalanceTransfer $entity , $processHead ='BalanceTransfer')
	{
		$this->fromBalanceTransfer($entity,$processHead);
		$this->toBalanceTransfer($entity,$processHead);
	}

	private function fromBalanceTransfer(AccountBalanceTransfer $entity,$processHead){

		$em = $this->_em;
		$cash = new AccountCash();
		$cash->setGlobalOption($entity->getGlobalOption());
		$cash->setTransactionMethod($entity->getFromTransactionMethod());
		$cash->setAccountBank($entity->getFromAccountBank());
		$cash->setAccountMobileBank($entity->getFromAccountMobileBank());
		$cash->setProcessHead($processHead);
		$cash->setAccountRefNo($entity->getAccountRefNo());
		if(!empty($entity->getBranches())){
			$cash->setBranches($entity->getBranches());
		}
		if(!empty($entity->getFromTransactionMethod()) and $entity->getFromTransactionMethod()->getId() == 2 ){
			$cash->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(38));
		}elseif(!empty($entity->getFromTransactionMethod()) and $entity->getFromTransactionMethod()->getId() == 3 ){
			$cash->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(45));
		}else{
			$cash->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(31));
		}
		$cash->setCredit($entity->getAmount());
		$em->persist($cash);
		$em->flush();

	}

	private function toBalanceTransfer(AccountBalanceTransfer $entity,$processHead){

		$em = $this->_em;
		$cash = new AccountCash();
		$cash->setGlobalOption($entity->getGlobalOption());
		$cash->setTransactionMethod($entity->getToTransactionMethod());
		$cash->setAccountBank($entity->getToAccountBank());
		$cash->setAccountMobileBank($entity->getToAccountMobileBank());
		$cash->setProcessHead($processHead);
		$cash->setAccountRefNo($entity->getAccountRefNo());
		if(!empty($entity->getBranches())){
			$cash->setBranches($entity->getBranches());
		}
		if(!empty($entity->getToTransactionMethod()) and $entity->getToTransactionMethod()->getId() == 2 ){
			$cash->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(3));
		}elseif(!empty($entity->getToTransactionMethod()) and $entity->getToTransactionMethod()->getId() == 3 ){
			$cash->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(10));
		}else{
			$cash->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(30));
		}
		$cash->setDebit($entity->getAmount());
		$em->persist($cash);
		$em->flush();

	}

    public function insertPurchaseCommission(AccountPurchaseCommission $entity){

		$em = $this->_em;
		$cash = new AccountCash();
		$cash->setGlobalOption($entity->getGlobalOption());
		$cash->setTransactionMethod($entity->getTransactionMethod());
		$cash->setAccountBank($entity->getAccountBank());
		$cash->setAccountMobileBank($entity->getAccountMobileBank());
        $cash->setProcessHead('Purchase-Commission');
		$cash->setAccountRefNo($entity->getAccountRefNo());
		if(!empty($entity->getTransactionMethod()) and $entity->getTransactionMethod()->getId() == 2 ){
			$cash->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(3));
		}elseif(!empty($entity->getTransactionMethod()) and $entity->getTransactionMethod()->getId() == 3 ){
			$cash->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(10));
		}else{
			$cash->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(30));
		}
		$cash->setDebit($entity->getAmount());
		$em->persist($cash);
		$em->flush();

	}


	public function insertPurchaseCash(AccountPurchase $entity)
    {

        $balance = $this->lastInsertCash($entity,'Purchase');
        $em = $this->_em;
        $cash = new AccountCash();

        $cash->setGlobalOption($entity->getGlobalOption());
        $cash->setAccountPurchase($entity);
        $cash->setTransactionMethod($entity->getTransactionMethod());
        $cash->setAccountMobileBank($entity->getAccountMobileBank());
        $cash->setAccountBank($entity->getAccountBank());
        $cash->setProcessHead('Purchase');
        $cash->setAccountRefNo($entity->getAccountRefNo());
        $cash->setUpdated($entity->getUpdated());
        /* Cash - Cash various */
        if($entity->getTransactionMethod()->getId() == 1 ){
            $cash->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(31));
        }elseif($entity->getTransactionMethod()->getId() == 2 ){
            $cash->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(38));
        }if($entity->getTransactionMethod()->getId() == 3 ){
            $cash->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(45));
        }

        $cash->setBalance($balance - $entity->getPayment() );
        $cash->setCredit($entity->getPayment());
        $em->persist($cash);
        $em->flush();

    }

    public function insertSalesCash(AccountSales $entity)
    {

        $balance = $this->lastInsertCash($entity,'Sales');
        $em = $this->_em;
        $cash = new AccountCash();

        /* Cash - Cash various */
        if($entity->getTransactionMethod()->getId() == 2 ){
            /* Current Asset Bank Cash Debit */
            $cash->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(3));
            $cash->setAccountBank($entity->getAccountBank());
        }elseif($entity->getTransactionMethod()->getId() == 3 ){
            /* Current Asset Mobile Account Debit */
            $cash->setAccountMobileBank($entity->getAccountMobileBank());
            $account = $this->_em->getRepository('AccountingBundle:AccountHead')->find(10);
            $cash->setAccountHead($account);
        }else{
            /* Cash - Cash Debit */
            $cash->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(30));
        }
        $cash->setGlobalOption($entity->getGlobalOption());
        $cash->setAccountSales($entity);
        if(!empty($entity->getBranches())){
            $cash->setBranches($entity->getBranches());
        }
        if(!empty($entity->getTransactionMethod())){
	        $cash->setTransactionMethod($entity->getTransactionMethod());
        }else{
	        $method = $this->_em->getRepository('SettingToolBundle:TransactionMethod')->find(1);
	        $cash->setTransactionMethod($method);
        }
        $cash->setProcessHead('Sales');
        $cash->setAccountRefNo($entity->getAccountRefNo());
        $cash->setUpdated($entity->getUpdated());
        $cash->setBalance($balance + $entity->getAmount() );
        $cash->setDebit($entity->getAmount());
        $em->persist($cash);
        $em->flush();
    }

    public function resetSalesCash(AccountSales $entity)
    {

	    $exist = $this->findOneBy(array('processHead'=>'Sales','accountSales'=> $entity));
	    if($exist) {
		    $balance = $this->lastInsertCash( $entity, 'Sales' );
		    $em      = $this->_em;
		    $cash    = $exist;

		    $cash->setGlobalOption( $entity->getGlobalOption() );
		    $cash->setAccountSales( $entity );
		    if ( ! empty( $entity->getBranches() ) ) {
			    $cash->setBranches( $entity->getBranches() );
		    }
		    $cash->setUpdated($cash->getCreated());
		    $cash->setProcessHead( 'Sales' );
		    $cash->setAccountRefNo( $entity->getAccountRefNo() );
		    $cash->setUpdated( $entity->getUpdated() );
		    $cash->setBalance( $balance + $entity->getAmount() );
		    $cash->setDebit( $entity->getAmount() );
		    $em->persist( $cash );
		    $em->flush();

	    }else{

		    $this->insertSalesCash($entity);
	    }
    }



    public function insertSalesCashReturn(AccountSalesReturn $entity)
    {

        $balance = $this->lastInsertCash($entity,'SalesReturn');
        $em = $this->_em;
        $cash = new AccountCash();

        $cash->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(35));
        $cash->setGlobalOption($entity->getGlobalOption());
        if(!empty($entity->getBranches())){
            $cash->setBranches($entity->getBranches());
        }
        $cash->setAccountSalesReturn($entity);
        $cash->setTransactionMethod($entity->getTransactionMethod());
        $cash->setProcessHead('SalesReturn');
        $cash->setAccountRefNo($entity->getAccountRefNo());
        $cash->setUpdated($entity->getUpdated());
        $cash->setBalance($balance - $entity->getAmount() );
        $cash->setCredit($entity->getAmount());
        $em->persist($cash);
        $em->flush();

    }

    public function insertOnlineOrderCash(AccountOnlineOrder $entity)
    {

        $balance = $this->lastInsertCash($entity,'Online');
        $em = $this->_em;
        $cash = new AccountCash();

        if($entity->getTransactionMethod()->getId() == 2){
            $cash->setAccountBank($entity->getAccountBank());
            $cash->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(3));
        }elseif($entity->getTransactionMethod()->getId() == 3 ){
            $cash->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(43));
            $cash->setAccountMobileBank($entity->getAccountMobileBank());
        }else{
            $cash->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(36));
        }

        $cash->setGlobalOption($entity->getGlobalOption());
        $cash->setAccountOnlineOrder($entity);
        $cash->setTransactionMethod($entity->getTransactionMethod());
        $cash->setProcessHead('Online');
        $cash->setAccountRefNo($entity->getAccountRefNo());
        $cash->setUpdated($entity->getUpdated());

        $cash->setBalance($balance + $entity->getAmount() );
        $cash->setDebit($entity->getAmount());
        $em->persist($cash);
        $em->flush();

    }

    public function insertAccountPurchaseReturnCash(AccountPurchaseReturn $entity)
    {

        $balance = $this->lastInsertCash($entity,'PurchaseReturn');
        $em = $this->_em;
        $cash = new AccountCash();

        $cash->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(36));
        $cash->setGlobalOption($entity->getGlobalOption());
        $cash->setAccountPurchaseReturn($entity);
        $cash->setTransactionMethod($entity->getTransactionMethod());
        $cash->setProcessHead('PurchaseReturn');
        $cash->setAccountRefNo($entity->getAccountRefNo());
        $cash->setUpdated($entity->getUpdated());
        $cash->setBalance($balance + $entity->getAmount() );
        $cash->setDebit($entity->getAmount());
        $em->persist($cash);
        $em->flush();

    }

    public function insertExpenditureCash(Expenditure $entity)
    {

        $balance = $this->lastInsertCash($entity,'Expenditure');
        $em = $this->_em;
        $cash = new AccountCash();
        if($entity->getTransactionMethod()->getId() == 2){
            $cash->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(31));
        }elseif($entity->getTransactionMethod()->getId() == 3){
            $cash->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(31));
        }else{
            $cash->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(31));
        }
        $cash->setGlobalOption($entity->getGlobalOption());
        if(!empty($entity->getBranches())){
            $cash->setBranches($entity->getBranches());
        }
        $cash->setExpenditure($entity);
	    $cash->setToUser($entity->getToUser());
	    $cash->setTransactionMethod($entity->getTransactionMethod());
        $cash->setAccountBank($entity->getAccountBank());
        $cash->setAccountMobileBank($entity->getAccountMobileBank());
        $cash->setProcessHead('Expenditure');
        $cash->setAccountRefNo($entity->getAccountRefNo());
        $cash->setUpdated($entity->getUpdated());
        $cash->setBalance($balance - $entity->getAmount() );
        $cash->setCredit($entity->getAmount());
        $em->persist($cash);
        $em->flush();

    }

    public function insertPettyCash(PettyCash $entity)
    {

        $balance = $this->lastInsertCash($entity,'PettyCash');
        $em = $this->_em;
        $cash = new AccountCash();

        $cash->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(31));
        $cash->setGlobalOption($entity->getGlobalOption());
        $cash->setPettyCash($entity);
        $cash->setTransactionMethod($entity->getTransactionMethod());
        $cash->setAccountMobileBank($entity->getAccountMobileBank());
        $cash->setAccountBank($entity->getAccountBank());
        $cash->setProcessHead('PettyCash');
        $cash->setAccountRefNo($entity->getAccountRefNo());
        $cash->setUpdated($entity->getUpdated());
        $cash->setBalance($balance - $entity->getAmount() );
        $cash->setCredit($entity->getAmount());
        $em->persist($cash);
        $em->flush();

    }

    public function insertPettyCashReturn(PettyCash $entity)
    {

        $balance = $this->lastInsertCash($entity,'PettyCash');
        $em = $this->_em;
        $cash = new AccountCash();

        $cash->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(30));
        $cash->setGlobalOption($entity->getGlobalOption());
        $cash->setPettyCash($entity);
        $cash->setTransactionMethod($entity->getTransactionMethod());
        $cash->setAccountMobileBank($entity->getAccountMobileBank());
        $cash->setAccountBank($entity->getAccountBank());
        $cash->setProcessHead('PettyCash');
        $cash->setAccountRefNo($entity->getAccountRefNo());
        $cash->setUpdated($entity->getUpdated());
        $cash->setBalance($balance + $entity->getAmount() );
        $cash->setDebit($entity->getAmount());
        $em->persist($cash);
        $em->flush();

    }

    public function insertSalaryCash(PaymentSalary $entity)
    {

        $balance = $this->lastInsertCash($entity,'PaymentSalary');
        $em = $this->_em;
        $cash = new AccountCash();

        $cash->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(31));
        $cash->setGlobalOption($entity->getGlobalOption());
        $cash->setPaymentSalary($entity);
        $cash->setTransactionMethod($entity->getTransactionMethod());
        $cash->setAccountMobileBank($entity->getAccountMobileBank());
        $cash->setAccountBank($entity->getAccountBank());
        $cash->setProcessHead('PaymentSalary');
        $cash->setAccountRefNo($entity->getAccountRefNo());
        $cash->setUpdated($entity->getUpdated());
        $cash->setBalance($balance - $entity->getPaidAmount() );
        $cash->setCredit($entity->getPaidAmount());
        $em->persist($cash);
        $em->flush();

    }

    public function dmsInsertSalesCash(DmsTreatmentPlan $entity)
    {

        $invoice = $entity->getDmsInvoice();
        $balance = $this->closingInsertCash($entity,$invoice->getDmsConfig()->getGlobalOption(),'Sales');
        $em = $this->_em;
        $cash = new AccountCash();

        /* Cash - Cash various */
        if($entity->getTransactionMethod()->getId() == 2 ){
            /* Current Asset Bank Cash Debit */
            $cash->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(3));
            $cash->setAccountBank($entity->getAccountBank());
        }elseif($entity->getTransactionMethod()->getId() == 3 ){
            /* Current Asset Mobile Account Debit */
            $cash->setAccountMobileBank($entity->getAccountMobileBank());
            $account = $this->_em->getRepository('AccountingBundle:AccountHead')->find(10);
            $cash->setAccountHead($account);
        }else{
            /* Cash - Cash Debit */
            $cash->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(30));
        }
        $cash->setGlobalOption($invoice->getDmsConfig()->getGlobalOption());
        $cash->setTransactionMethod($entity->getTransactionMethod());
        $cash->setProcessHead('Sales');
        $cash->setAccountRefNo($invoice->getInvoice());
        $cash->setUpdated($entity->getUpdated());
        $cash->setBalance($balance + $entity->getPayment() );
        $cash->setDebit($entity->getPayment());
        $em->persist($cash);
        $em->flush();

    }

    public function closingInsertCash(DmsTreatmentPlan $entity,$globalOption,$processHead)
    {
        $em = $this->_em;
        if($entity->getTransactionMethod()->getId() == 2){
            $array = array('globalOption' => $globalOption,'transactionMethod' => $entity->getTransactionMethod() ,'accountBank' => $entity->getAccountBank(), 'processHead' => $processHead );
        }elseif($entity->getTransactionMethod()->getId() == 3 ){
            $array = array('globalOption' => $globalOption,'transactionMethod' => $entity->getTransactionMethod(),'accountMobileBank' => $entity->getAccountMobileBank(), 'processHead' => $processHead );
        }else{
            $array = array('globalOption' => $globalOption,'transactionMethod' => $entity->getTransactionMethod(), 'processHead' => $processHead );
        }
        $entity = $em->getRepository('AccountingBundle:AccountCash')->findOneBy($array,array('id' => 'DESC'));
        if (empty($entity)) {
            return 0;
        }
        return $entity->getBalance();
    }


    public function dailyProcessHead(User $user , $head = '', $data = [])
    {
        $option = $user->getGlobalOption()->getId();
        $compare = new \DateTime();
        $month =  $compare->format('F');
        $year =  $compare->format('Y');
        $month = isset($data['month'])? $data['month'] :$month;
        $year = isset($data['year'])? $data['year'] :$year;
        $sql = "SELECT DATE_FORMAT(invoice.updated,'%d-%m-%Y') as date ,COALESCE(SUM(invoice.debit),0) as debit,COALESCE(SUM(invoice.credit),0) as credit
                FROM AccountCash as invoice
                WHERE invoice.globalOption_id = :option AND MONTHNAME(invoice.updated) =:month AND YEAR(invoice.updated) =:year AND invoice.processHead =:process 
                GROUP BY date";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('option', $option);
        $stmt->bindValue('month', $month);
        $stmt->bindValue('year', $year);
        $stmt->bindValue('process', $head);
        $stmt->execute();
        $results =  $stmt->fetchAll();
        $arrays = [];
        foreach ($results as $result){
            $arrays[$result['date']] = $result;
        }
        return $arrays;
    }

    public function monthlyProcessHead(User $user , $head = '', $data = [])
    {
        $option = $user->getGlobalOption()->getId();
        $compare = new \DateTime();
        $year =  $compare->format('Y');
        $year = isset($data['year'])? $data['year'] :$year;
        $sql = "SELECT MONTH(invoice.updated) as month ,COALESCE(SUM(invoice.debit),0) as debit,COALESCE(SUM(invoice.credit),0) as credit
                FROM AccountCash as invoice
                WHERE invoice.globalOption_id = :option AND YEAR(invoice.updated) =:year AND invoice.processHead =:process 
                GROUP BY month";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('option', $option);
        $stmt->bindValue('year', $year);
        $stmt->bindValue('process', $head);
        $stmt->execute();
        $results =  $stmt->fetchAll();
        $arrays = [];
        foreach ($results as $result){
            $arrays[$result['month']] = $result;
        }
        return $arrays;
    }


 /*   public function dailyProcessHead1(User $user,$head = '' , $data = []){

        $emConfig = $this->getEntityManager()->getConfiguration();
        $emConfig->addCustomDatetimeFunction('YEAR', 'DoctrineExtensions\Query\Mysql\Year');
        $emConfig->addCustomDatetimeFunction('MONTH', 'DoctrineExtensions\Query\Mysql\Month');
        $emConfig->addCustomDatetimeFunction('DAY', 'DoctrineExtensions\Query\Mysql\Day');
        $option = $user->getGlobalOption()->getId();
        $compare = new \DateTime($data['monthYear']);
        $start =  $compare->format('Y-m-01 00:00:01');
        $end =  $compare->format('Y-m-t 23:59:59');
        $month =  $compare->format('m');
        $year =  $compare->format('Y');

        $qb = $this->createQueryBuilder('e');
        $qb->select('DAY(e.updated) AS day, COALESCE(SUM(e.debit),0) as debit, COALESCE(SUM(e.credit),0) as credit');
        $qb->where("e.globalOption = :option")->setParameter('option', $option);
        $qb->andWhere("e.updated >= :startDate")->setParameter('startDate', $start);
        $qb->andWhere("e.updated <= :endDate")->setParameter('endDate', $end);
        $qb->andWhere("e.processHead =:head")->setParameter('head', $head);
      //  $qb->andWhere('YEAR(e.updated) = :year')->setParameter('year', $year);
       // $qb->andWhere('MONTH(e.updated) = :month')->setParameter('month', $month);
        $qb->groupBy('day');
        $results = $qb->getQuery()->getArrayResult();
        $arrays = [];
        foreach ($results as $result){
            $date = "{$result['day']}-{$month}-{$year}";
            $arrays[$date] = $result;
        }
        return $arrays;
    }
    public function monthlyProcessHead1(User $user,$head = '' , $data = []){

        $emConfig = $this->getEntityManager()->getConfiguration();
        $emConfig->addCustomDatetimeFunction('YEAR', 'DoctrineExtensions\Query\Mysql\Year');
        $emConfig->addCustomDatetimeFunction('MONTH', 'DoctrineExtensions\Query\Mysql\Month');
        $emConfig->addCustomDatetimeFunction('DAY', 'DoctrineExtensions\Query\Mysql\Day');
        $option = $user->getGlobalOption()->getId();
        $year =  $data['year'];

        $qb = $this->createQueryBuilder('e');
        $qb->select('MONTH(e.updated) AS month, COALESCE(SUM(e.debit),0) as debit, COALESCE(SUM(e.credit),0) as credit');
        $qb->where("e.globalOption = :option")->setParameter('option', $option);
        $qb->andWhere("e.processHead =:head")->setParameter('head', $head);
        $qb->andWhere('YEAR(e.updated) = :year')->setParameter('year', $year);
        $qb->groupBy('month');
        $results = $qb->getQuery()->getArrayResult();
        $arrays = [];
        foreach ($results as $result){
            $date = "{$result['month']}";
            $arrays[$date] = $result;
        }
        return $arrays;
    }*/



}
