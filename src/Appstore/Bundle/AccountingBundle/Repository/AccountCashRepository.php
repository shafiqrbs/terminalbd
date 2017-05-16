<?php

namespace Appstore\Bundle\AccountingBundle\Repository;
use Appstore\Bundle\AccountingBundle\Entity\AccountCash;
use Appstore\Bundle\AccountingBundle\Entity\AccountJournal;
use Appstore\Bundle\AccountingBundle\Entity\AccountOnlineOrder;
use Appstore\Bundle\AccountingBundle\Entity\AccountPurchase;
use Appstore\Bundle\AccountingBundle\Entity\AccountPurchaseReturn;
use Appstore\Bundle\AccountingBundle\Entity\AccountSales;
use Appstore\Bundle\AccountingBundle\Entity\AccountSalesReturn;
use Appstore\Bundle\AccountingBundle\Entity\Expenditure;
use Appstore\Bundle\AccountingBundle\Entity\PaymentSalary;
use Appstore\Bundle\AccountingBundle\Entity\PettyCash;
use Appstore\Bundle\AccountingBundle\Entity\Transaction;
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

    public function transactionCashOverview(User $user,$data = '')
    {


        $globalOption = $user->getGlobalOption();
        $branch = $user->getProfile()->getBranches();

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.transactionMethod','transactionMethod');
        $qb->select('transactionMethod.name , SUM(e.debit) AS debit, SUM(e.credit) AS credit');
        $qb->where("e.globalOption = :globalOption");
        $qb->setParameter('globalOption', $globalOption);
        if (!empty($branch)){
            $qb->andWhere("e.branches = :branch");
            $qb->setParameter('branch', $branch);
        }
        $this->handleSearchBetween($qb,$data);
        $qb->groupBy("e.transactionMethod");
        $result = $qb->getQuery()->getArrayResult();
        return $result;

    }

    public function transactionBankCashOverview(User $user, $data = '')
    {

        $globalOption = $user->getGlobalOption();
        $branch = $user->getProfile()->getBranches();

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.accountBank','accountBank');
        $qb->select('accountBank.name , SUM(e.debit) AS debit, SUM(e.credit) AS credit');
        $qb->where("e.globalOption = :globalOption");
        $qb->setParameter('globalOption', $globalOption);
        if (!empty($branch)){
            $qb->andWhere("e.branches = :branch");
            $qb->setParameter('branch', $branch);
        }
        $this->handleSearchBetween($qb,$data);
        $qb->groupBy("e.accountBank");
        $result = $qb->getQuery()->getArrayResult();
        return $result;

    }

    public function transactionBkashCashOverview(User $user ,$data = '')
    {

        $globalOption = $user->getGlobalOption();
        $branch = $user->getProfile()->getBranches();

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.accountMobileBank','accountMobileBank');
        $qb->select('accountMobileBank.name , SUM(e.debit) AS debit, SUM(e.credit) AS credit');
        $qb->where("e.globalOption = :globalOption");
        $qb->setParameter('globalOption', $globalOption);
        if (!empty($branch)){
            $qb->andWhere("e.branches = :branch");
            $qb->setParameter('branch', $branch);
        }
        $this->handleSearchBetween($qb,$data);
        $qb->groupBy("e.accountMobileBank");
        $result = $qb->getQuery()->getArrayResult();
        return $result;

    }

    public function transactionAccountHeadCashOverview(User $user,$data = '')
    {

        $globalOption = $user->getGlobalOption();
        $branch = $user->getProfile()->getBranches();

        $qb = $this->createQueryBuilder('e');
        $qb->select('e.processHead as name , SUM(e.debit) AS debit, SUM(e.credit) AS credit');
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
    public function accountCashOverview(User $user,$transactionMethods,$data)
    {
        $globalOption = $user->getGlobalOption();
        $branch = $user->getProfile()->getBranches();

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.transactionMethod','t');
        $qb->select('SUM(e.debit) AS debit, SUM(e.credit) AS credit');
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
        $data =  array('debit'=> $result['debit'],'credit'=> $result['credit']);
        return $data;

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
            $tillDate = isset($data['tillDate'])  ? $data['tillDate'] : '';
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

            if (!empty($tillDate) ) {

                $compareTo = new \DateTime($data['tillDate']);
                $tillDate =  $compareTo->format('Y-m-d 23:59:59');
                $qb->andWhere("e.updated >= :updated");
                $qb->setParameter('updated', $tillDate);
            }

            if (!empty($accountBank)) {

                $qb->andWhere("e.accountBank = :accountBank");
                $qb->setParameter('accountBank', $accountBank);
            }

            if (!empty($accountMobileBank)) {

                $qb->andWhere("e.accountMobileBank = :accountMobileBank");
                $qb->setParameter('accountMobileBank', $accountMobileBank);
            }

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
        $cash->setTransactionMethod($entity->getTransactionMethod());
        $cash->setProcessHead('Sales');
        $cash->setAccountRefNo($entity->getAccountRefNo());
        $cash->setUpdated($entity->getUpdated());

        $cash->setBalance($balance + $entity->getAmount() );
        $cash->setDebit($entity->getAmount());
        $em->persist($cash);
        $em->flush();
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

}
