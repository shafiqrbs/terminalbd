<?php

namespace Appstore\Bundle\AccountingBundle\Repository;
use Appstore\Bundle\AccountingBundle\Entity\AccountBank;
use Appstore\Bundle\AccountingBundle\Entity\AccountJournal;
use Appstore\Bundle\AccountingBundle\Entity\AccountOnlineOrder;
use Appstore\Bundle\AccountingBundle\Entity\AccountSales;
use Appstore\Bundle\AccountingBundle\Entity\Expenditure;
use Appstore\Bundle\AccountingBundle\Entity\PaymentSalary;
use Appstore\Bundle\AccountingBundle\Entity\PettyCash;
use Appstore\Bundle\AccountingBundle\Entity\Transaction;
use Appstore\Bundle\InventoryBundle\Entity\Damage;
use Appstore\Bundle\InventoryBundle\Entity\Purchase;
use Appstore\Bundle\InventoryBundle\Entity\Sales;
use Doctrine\ORM\EntityRepository;
use Appstore\Bundle\AccountingBundle\Entity\AccountPurchase;
use Symfony\Component\Debug\Debug;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * TransactionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TransactionRepository extends EntityRepository
{

    public function transactionOverview($globalOption,$accountHead = 0)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('sum(e.debit) as debit,sum(e.credit) as credit');
        $qb->from('AccountingBundle:Transaction','e');
        $qb->where("e.globalOption = :globalOption");
        $qb->setParameter('globalOption', $globalOption->getId());
        if($accountHead > 0)
        {
            $qb->andWhere("e.accountHead = :accountHead");
            $qb->setParameter('accountHead', $accountHead);
        }
        $result = $qb->getQuery()->getSingleResult();
        return $result;

    }
    public function getGroupByAccountHead($globalOption){

        $qb = $this->_em->createQueryBuilder();
        $qb->select('sum(e.amount) as amount,accountHead.name as name , parent.name as parentName, accountHead.id, accountHead.toIncrease, accountHead.code');
        $qb->from('AccountingBundle:Transaction','e');
        $qb->innerJoin('e.accountHead','accountHead');
        $qb->leftJoin('accountHead.parent','parent');
        $qb->where("e.globalOption = :globalOption");
        $qb->setParameter('globalOption', $globalOption->getId());
        $qb->groupBy('e.accountHead');
        $qb->orderBy('e.accountHead','ASC');
        $result = $qb->getQuery()->getResult();

        return $result;
    }

    public function specificParentAccountHead($globalOption,$parent){

        $datetime = new \DateTime("now");
        $today_startdatetime = $datetime->format('Y-m-d 00:00:00');
        $today_enddatetime = $datetime->format('Y-m-d 23:59:59');

        $qb = $this->_em->createQueryBuilder();
        $qb->select('sum(e.amount) as amount, accountHead.name as name , accountHead.id, accountHead.toIncrease, accountHead.code');
        $qb->from('AccountingBundle:Transaction','e');
        $qb->innerJoin('e.accountHead','accountHead');
        $qb->where('e.globalOption = :globalOption')
            ->andWhere("accountHead.parent = :parent")
            ->andWhere('e.updated >= :today_startdatetime')
            ->andWhere('e.updated <= :today_enddatetime');
        $qb->setParameter('globalOption', $globalOption->getId())
            ->setParameter('parent', $parent)
            ->setParameter('today_startdatetime', $today_startdatetime)
            ->setParameter('today_enddatetime', $today_enddatetime);
        $qb->groupBy('e.accountHead');
        $qb->orderBy('e.accountHead','ASC');
        $result = $qb->getQuery()->getResult();

        return $result;


    }

    public function specificAccountHead($globalOption,$accountHead){

        $datetime = new \DateTime("now");
        $today_startdatetime = $datetime->format('Y-m-d 00:00:00');
        $today_enddatetime = $datetime->format('Y-m-d 23:59:59');

        $qb = $this->_em->createQueryBuilder();
        $qb->select('e.amount as amount,e.debit as debit, e.credit as credit , e.updated, e.process, e.toIncrease, e.content');
        $qb->from('AccountingBundle:Transaction','e');
        $qb->where('e.globalOption = :globalOption')
            ->andWhere("e.accountHead = :accountHead");
        $qb->setParameter('globalOption', $globalOption->getId())
            ->setParameter('accountHead', $accountHead);
        $qb->orderBy('e.updated','DESC');
        $result = $qb->getQuery()->getResult();

        return $result;

    }

    public function insertAccountJournalTransaction(AccountJournal $journal,$processHead)
    {

        $this->_em->getRepository('AccountingBundle:AccountCash')->insertAccountCash($journal,$processHead);
        $this->insertAccountJournalDebitTransaction($journal);
        $this->insertAccountJournalCreditTransaction($journal);

    }

    public function insertAccountJournalDebitTransaction($entity)
    {

        $transaction = new Transaction();
        $transaction->setGlobalOption($entity->getGlobalOption());
        $transaction->setProcessHead('Journal');
        $transaction->setProcess($entity->getAccountHeadDebit()->getParent()->getName());
        $transaction->setAccountRefNo($entity->getAccountRefNo());
        $transaction->setUpdated($entity->getUpdated());
        $transaction->setAccountHead($entity->getAccountHeadDebit());
        $transaction->setAmount($entity->getAmount());
        $transaction->setDebit($entity->getAmount());
        $this->_em->persist($transaction);
        $this->_em->flush();

        return $transaction;

    }

    public function insertAccountJournalCreditTransaction($entity)
    {

        $transaction = new Transaction();
        $transaction->setGlobalOption($entity->getGlobalOption());
        $transaction->setProcessHead('Journal');
        $transaction->setProcess($entity->getAccountHeadCredit()->getParent()->getName());
        $transaction->setAccountRefNo($entity->getAccountRefNo());
        $transaction->setUpdated($entity->getUpdated());
        $transaction->setAccountHead($entity->getAccountHeadCredit());
        $transaction->setAmount('-'.$entity->getAmount());
        $transaction->setCredit($entity->getAmount());
        $this->_em->persist($transaction);
        $this->_em->flush();

        return $transaction;

    }


    public function purchaseTransaction(Purchase $purchase,$accountPurchase,$source='')
    {
        $this->insertInventoryAsset($purchase,$accountPurchase);
        $this->insertPurchaseCash($purchase,$accountPurchase);
        $this->insertPurchaseAccountPayable($purchase,$accountPurchase);
    }

    private function insertInventoryAsset($purchase,$accountPurchase)
    {

        $amount = $purchase->getTotalAmount();
        $transaction = new Transaction();
        $transaction->setGlobalOption($purchase->getInventoryConfig()->getGlobalOption());
        $transaction->setProcessHead('Purchase');
        $transaction->setProcess('Inventory Assets');
        $transaction->setAccountRefNo($accountPurchase->getAccountRefNo());
        $transaction->setUpdated($accountPurchase->getUpdated());
        /* Inventory Assets - Purchase Goods Received account */
        $transaction->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(6));
        $transaction->setAmount($amount);
        $transaction->setDebit($amount);
        $this->_em->persist($transaction);
        $this->_em->flush();


    }

    private function insertPurchaseCash($purchase,$accountPurchase)
    {


        $amount = $purchase->getPaymentAmount();
        if($amount > 0) {

            $transaction = new Transaction();
            $transaction->setGlobalOption($purchase->getInventoryConfig()->getGlobalOption());
            $transaction->setProcessHead('Purchase');
            $transaction->setProcess('Cash');
            $transaction->setAccountRefNo($accountPurchase->getAccountRefNo());
            $transaction->setUpdated($accountPurchase->getUpdated());
            /* Cash - Purchase Payment */
            $transaction->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(32));
            $transaction->setAmount('-' . $amount);
            $transaction->setCredit($amount);
            $this->_em->persist($transaction);
            $this->_em->flush();

        }
    }

    private function insertPurchaseAccountPayable($purchase,$accountPurchase)
    {

        $amount = $purchase->getDueAmount();
        if($amount > 0){
            $transaction = new Transaction();
            $transaction->setGlobalOption($accountPurchase->getGlobalOption());
            $transaction->setProcessHead('Purchase');
            $transaction->setProcess('Current Liabilities');
            $transaction->setAccountRefNo($accountPurchase->getAccountRefNo());
            $transaction->setUpdated($accountPurchase->getUpdated());
            /* Current Liabilities-Purchase Account payable */
            $transaction->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(13));
            $transaction->setAmount('-'.$amount);
            $transaction->setCredit($amount);
            $this->_em->persist($transaction);
            $this->_em->flush();
        }

    }

    public function insertPurchaseVendorTransaction(\Appstore\Bundle\AccountingBundle\Entity\AccountPurchase $entity)
    {

        $this->insertPurchaseLiabilityDebitTransaction($entity);
        $this->insertPurchaseCashCreditTransaction($entity);
        $this->_em->getRepository('AccountingBundle:AccountCash')->insertPurchaseCash($entity);


    }

    public function insertPurchaseCashCreditTransaction($entity)
    {

        $transaction = new Transaction();
        $transaction->setGlobalOption($entity->getGlobalOption());
        $transaction->setAccountRefNo($entity->getAccountRefNo());
        $transaction->setProcessHead('Purchase');
        $transaction->setUpdated($entity->getUpdated());
        if($entity->getTransactionMethod()->getId() == 2 || $entity->getTransactionMethod()->getId() == 3 ) {
            /* Asset Accounts - Bank Cash Payment */
            $transaction->setProcess('Current Asset');
            $transaction->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(38));
        }else{
            /* Cash - Purchase Payment Account */
            $transaction->setProcess('Cash');
            $transaction->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(32));
        }
        $transaction->setAmount('-'.$entity->getPayment());
        $transaction->setCredit($entity->getPayment());
        $this->_em->persist($transaction);
        $this->_em->flush();
    }

    public function insertPurchaseLiabilityDebitTransaction($entity)
    {


        $transaction = new Transaction();
        $transaction->setGlobalOption($entity->getGlobalOption());
        $transaction->setAccountRefNo($entity->getAccountRefNo());
        $transaction->setProcessHead('Purchase');
        $transaction->setUpdated($entity->getUpdated());
        $transaction->setProcess('Current Liabilities');
        /* Current Liabilities - Account Payable Payment */
        $transaction->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(37));
        $transaction->setAmount($entity->getPayment());
        $transaction->setDebit($entity->getPayment());
        $this->_em->persist($transaction);
        $this->_em->flush();

    }


    public function purchaseReturnTransaction($entity,$accountPurchaseReturn)
    {

        $this->insertPurchaseReturn($entity,$accountPurchaseReturn);
        $this->insertPurchaseReturnAccountReceivable($entity,$accountPurchaseReturn);


    }

    private function insertPurchaseReturn($entity,$accountPurchaseReturn)
    {

        $transaction = new Transaction();
        $transaction->setGlobalOption($accountPurchaseReturn->getGlobalOption());
        $transaction->setAccountRefNo($accountPurchaseReturn->getAccountRefNo());
        $transaction->setProcessHead('PurchaseReturn');
        $transaction->setUpdated($entity->getUpdated());
        $transaction->setProcess('Goods');
        /* Inventory Assets-Purchase Return account */
        $transaction->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(7));
        $transaction->setAmount('-'.$entity->getTotal());
        $transaction->setCredit($entity->getTotal());
        $this->_em->persist($transaction);
        $this->_em->flush();

    }

    private function insertPurchaseReturnAccountReceivable($entity,$accountPurchaseReturn)
    {
        $transaction = new Transaction();
        $transaction->setGlobalOption($accountPurchaseReturn->getGlobalOption());
        $transaction->setAccountRefNo($accountPurchaseReturn->getAccountRefNo());
        $transaction->setProcessHead('PurchaseReturn');
        $transaction->setUpdated($entity->getUpdated());
        $transaction->setProcess('Cash');
        /* Assets Account - Account Cash */
        $transaction->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(32));
        $transaction->setAmount($entity->getTotal());
        $transaction->setDebit($entity->getTotal());
        $this->_em->persist($transaction);
        $this->_em->flush();

    }

    public function salesTransaction($entity,$accountSales)
    {
        $this->insertSalesItem($entity,$accountSales);
        $this->insertSalesCash($entity,$accountSales);
        $this->insertSalesAccountReceivable($entity,$accountSales);
        $this->insertSalesVatAccountPayable($entity,$accountSales);
    }



    private function insertSalesItem($entity,AccountSales $accountSales)
    {


        $amount =  $entity->getTotal();
        $transaction = new Transaction();
        $transaction->setGlobalOption($accountSales->getGlobalOption());
        $transaction->setAccountRefNo($accountSales->getAccountRefNo());
        $transaction->setProcessHead('Sales');
        $transaction->setProcess('Goods');
        /* Sales Revenue - Sales goods account */
        $transaction->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(33));
        $transaction->setAmount('-'.$amount);
        $transaction->setCredit($amount);
        $this->_em->persist($transaction);
        $this->_em->flush();

    }

    private function insertSalesCash($entity,$accountSales)
    {
        $amount = $entity->getPayment();
        if($amount > 0) {
            $transaction = new Transaction();
            $transaction->setGlobalOption($accountSales->getGlobalOption());
            $transaction->setAccountRefNo($accountSales->getAccountRefNo());
            $transaction->setProcessHead('Sales');

            $transaction->setUpdated($entity->getUpdated());
            if($entity->getTransactionMethod()->getId() == 2 || $entity->getTransactionMethod()->getId() == 3 ) {
                /* Asset Accounts - Bank Cash Receive */
                $transaction->setProcess('Current Asset');
                $transaction->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(39));
            }else{
                /* Cash - Sales Receive Cash Account */
                $transaction->setProcess('Cash');
                $transaction->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(36));
            }
            $transaction->setAmount($amount);
            $transaction->setDebit($amount);
            $this->_em->persist($transaction);
            $this->_em->flush();
        }
    }

    private function insertSalesAccountReceivable($entity,$accountSales)
    {

       $amount = $entity->getDue();
        if($amount > 0){

            $transaction = new Transaction();
            $transaction->setGlobalOption($accountSales->getGlobalOption());
            $transaction->setAccountRefNo($accountSales->getAccountRefNo());
            $transaction->setProcessHead('Sales');
            $transaction->setProcess('AccountReceivable');
            /* Assets Account - Account Receivable */
            $transaction->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(4));
            $transaction->setAmount($amount);
            $transaction->setDebit($amount);
            $this->_em->persist($transaction);
            $this->_em->flush();

        }

    }

    private function insertSalesVatAccountPayable($entity,$accountSales)
    {

         $amount = $entity->getVat();
         if($amount > 0){

             $transaction = new Transaction();
             $transaction->setGlobalOption($accountSales->getGlobalOption());
             $transaction->setAccountRefNo($accountSales->getAccountRefNo());
             $transaction->setProcessHead('Sales');
             $transaction->setProcess('AccountPayable');
             /* Current Liabilities - Sales Tax */
             $transaction->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(16));
             $transaction->setAmount('-'.$amount);
             $transaction->setCredit($amount);
             $this->_em->persist($transaction);
             $this->_em->flush();

        }

    }

    public function salesReturnTransaction($entity,$accountSalesReturn)
    {

        $this->insertSalesReturnDebit($entity,$accountSalesReturn);
        $this->insertSalesReturnCredit($entity,$accountSalesReturn);
        $this->_em->getRepository('AccountingBundle:AccountCash')->insertSalesCashReturn($accountSalesReturn);

    }


    private function insertSalesReturnDebit($entity,$accountSalesReturn)
    {

        $transaction = new Transaction();
        $transaction->setGlobalOption($accountSalesReturn->getGlobalOption());
        $transaction->setAccountRefNo($accountSalesReturn->getAccountRefNo());
        $transaction->setProcessHead('SalesReturn');
        $transaction->setProcess('Goods');
        /* Sales Revenue - Sales Return Account */
        $transaction->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(34));
        $transaction->setAmount($entity->getTotal());
        $transaction->setDebit($entity->getTotal());
        $this->_em->persist($transaction);
        $this->_em->flush();

    }

    private function insertSalesReturnCredit($entity,$accountSalesReturn)
    {
        $transaction = new Transaction();
        $transaction->setGlobalOption($accountSalesReturn->getGlobalOption());
        $transaction->setAccountRefNo($accountSalesReturn->getAccountRefNo());
        $transaction->setProcessHead('SalesReturn');
        $transaction->setProcess('Cash');
        /* Cash - Sales Return Payment Account */
        $transaction->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(35));
        $transaction->setAmount('-'.$entity->getTotal());
        $transaction->setCredit($entity->getTotal());
        $this->_em->persist($transaction);
        $this->_em->flush();

    }

    public function onlineOrderTransaction($entity,$onlineOrder)
    {
        $this->insertOnlineOrderItem($entity,$onlineOrder);
        $this->insertOnlineOrderCash($entity,$onlineOrder);
        $this->insertOnlineOrderAccountReceivable($entity,$onlineOrder);
        $this->insertOnlineOrderAccountPayable($entity,$onlineOrder);
        $this->insertOnlineOrderVatAccountPayable($entity,$onlineOrder);
    }


    private function insertOnlineOrderItem($entity , AccountOnlineOrder $onlineOrder)
    {

        $amount =  $entity->getGrandTotalAmount();
        $transaction = new Transaction();
        $transaction->setGlobalOption($onlineOrder->getGlobalOption());
        $transaction->setAccountRefNo($onlineOrder->getAccountRefNo());
        $transaction->setProcessHead('Online');
        $transaction->setProcess('Goods');
        /* Sales Revenue - Sales goods account */
        $transaction->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(33));
        $transaction->setAmount('-'.$amount);
        $transaction->setCredit($amount);
        $this->_em->persist($transaction);
        $this->_em->flush();

    }

    private function insertOnlineOrderCash($entity,$onlineOrder)
    {
        $amount = $entity->getPaidAmount();
        if($amount > 0) {
            $transaction = new Transaction();
            $transaction->setGlobalOption($onlineOrder->getGlobalOption());
            $transaction->setAccountRefNo($onlineOrder->getAccountRefNo());
            $transaction->setProcessHead('Online');

            $transaction->setUpdated($entity->getUpdated());
            if($entity->getTransactionMethod()->getId() == 2 || $entity->getTransactionMethod()->getId() == 3 ) {
                /* Asset Accounts - Bank Cash Receive */
                $transaction->setProcess('Current Asset');
                $transaction->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(39));
            }else{
                /* Cash - Sales Receive Cash Account */
                $transaction->setProcess('Cash');
                $transaction->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(36));
            }
            $transaction->setAmount($amount);
            $transaction->setDebit($amount);
            $this->_em->persist($transaction);
            $this->_em->flush();
        }
    }

    private function insertOnlineOrderAccountReceivable($entity,$accountSales)
    {

        $amount = $entity->getDueAmount();
        if($amount > 0){

            $transaction = new Transaction();
            $transaction->setGlobalOption($accountSales->getGlobalOption());
            $transaction->setAccountRefNo($accountSales->getAccountRefNo());
            $transaction->setProcessHead('Sales');
            $transaction->setProcess('AccountReceivable');
            /* Assets Account - Account Receivable */
            $transaction->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(4));
            $transaction->setAmount($amount);
            $transaction->setDebit($amount);
            $this->_em->persist($transaction);
            $this->_em->flush();

        }

    }

    private function insertOnlineOrderAccountPayable($entity,$onlineOrder)
    {

        $amount = $entity->getReturnAmount();
        if($amount > 0){

            $transaction = new Transaction();
            $transaction->setGlobalOption($onlineOrder->getGlobalOption());
            $transaction->setAccountRefNo($onlineOrder->getAccountRefNo());
            $transaction->setProcessHead('Online');
            $transaction->setProcess('AccountPayable');
            /* Assets Account - Account Receivable */
            $transaction->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(12));
            $transaction->setAmount('-'.$amount);
            $transaction->setCredit($amount);
            $this->_em->persist($transaction);
            $this->_em->flush();

        }

    }

    private function insertOnlineOrderVatAccountPayable($entity,$onlineOrder)
    {

        $amount = $entity->getVat();
        if($amount > 0){

            $transaction = new Transaction();
            $transaction->setGlobalOption($onlineOrder->getGlobalOption());
            $transaction->setAccountRefNo($onlineOrder->getAccountRefNo());
            $transaction->setProcessHead('Online');
            $transaction->setProcess('AccountPayable');
            /* Current Liabilities - Sales Tax */
            $transaction->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(16));
            $transaction->setAmount('-'.$amount);
            $transaction->setCredit($amount);
            $this->_em->persist($transaction);
            $this->_em->flush();

        }

    }


    public function insertVendorReturnTransaction($entity)
    {

        $this->insertCashDebitTransaction($entity);
        $this->insertLiabilityCreditTransaction($entity);

    }

    public function insertCashDebitTransaction($entity)
    {

        $transaction = new Transaction();
        $transaction->setInventoryConfig($entity->getInventoryConfig());
        $transaction->setGlobalOption($entity->getInventoryConfig()->getGlobalOption());
        $transaction->setProcess('Cash');

        if(!empty($entity->getBank())) {

            /* Asset Accounts - Bank Cash Payment */
            $transaction->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(39));

        }else{

            /* Cash - Cash Debit */
            $transaction->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(30));
        }

        $transaction->setAmount($entity->getTotalAmount());
        $transaction->setDebit($entity->getTotalAmount());
        $this->_em->persist($transaction);
        $this->_em->flush();
    }

    public function insertLiabilityCreditTransaction($entity)
    {

        $transaction = new Transaction();
        $transaction->setInventoryConfig($entity->getInventoryConfig());
        $transaction->setGlobalOption($entity->getInventoryConfig()->getGlobalOption());
        $transaction->setProcess('Current Liabilities');
        /* Current Liabilities - Accounts Payable */
        $transaction->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(13));
        $transaction->setAmount('-'.$entity->getTotalAmount());
        $transaction->setCredit($entity->getTotalAmount());
        $this->_em->persist($transaction);
        $this->_em->flush();

    }

    public function insertAccountSalesTransaction(AccountSales $entity){

        $this->insertAccountDebitTransaction($entity);
        $this->insertAccountCreditTransaction($entity);
        $this->_em->getRepository('AccountingBundle:AccountCash')->insertSalesCash($entity);
    }

    public function  insertAccountDebitTransaction($entity)
    {

        $transaction = new Transaction();
        $transaction->setGlobalOption($entity->getGlobalOption());
        $transaction->setAccountRefNo($entity->getAccountRefNo());
        $transaction->setProcessHead('Sales');
        $transaction->setUpdated($entity->getUpdated());
        if($entity->getTransactionMethod()->getId() == 2 || $entity->getTransactionMethod()->getId() == 3 ) {
            /* Asset Accounts - Bank Cash Payment */
            $transaction->setProcess('Current Asset');
            $transaction->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(39));
        }else{
            /* Cash - Purchase Payment Account */
            $transaction->setProcess('Cash');
            $transaction->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(36));
        }

        $transaction->setAmount($entity->getAmount());
        $transaction->setDebit($entity->getAmount());
        $this->_em->persist($transaction);
        $this->_em->flush();
    }

    public function  insertAccountCreditTransaction($entity)
    {
        $transaction = new Transaction();
        $transaction->setGlobalOption($entity->getGlobalOption());
        $transaction->setProcess('Sales Revenue');
        $transaction->setAccountRefNo($entity->getAccountRefNo());
        $transaction->setUpdated($entity->getUpdated());
        /* Sales Revenue - Sales Due Payment credit */
        $transaction->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(42));
        $transaction->setAmount('-'.$entity->getAmount());
        $transaction->setCredit($entity->getAmount());
        $this->_em->persist($transaction);
        $this->_em->flush();
    }

    public function insertPettyCashTransaction($entity){

        $this->insertPettyCashDebitTransaction($entity);
        $this->insertPettyCashCreditTransaction($entity);


    }

    public function  insertPettyCashDebitTransaction($entity)
    {
        $transaction = new Transaction();
        $transaction->setGlobalOption($entity->getGlobalOption());
        $transaction->setAccountRefNo($entity->getAccountRefNo());
        $transaction->setProcessHead('Petty Cash');
        $transaction->setProcess('Account Receivable');
        /* Cash - Petty Cash */
        $transaction->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(4));
        $transaction->setAmount($entity->getAmount());
        $transaction->setDebit($entity->getAmount());
        $this->_em->persist($transaction);
        $this->_em->flush();
    }

    public function  insertPettyCashCreditTransaction($entity)
    {
        $transaction = new Transaction();
        $transaction->setGlobalOption($entity->getGlobalOption());
        $transaction->setAccountRefNo($entity->getAccountRefNo());
        $transaction->setProcessHead('Petty Cash');
        $transaction->setProcess('Cash Credit');
        /* Cash - Cash credit */
        $transaction->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(31));
        $transaction->setAmount('-'.$entity->getAmount());
        $transaction->setCredit($entity->getAmount());
        $this->_em->persist($transaction);
        $this->_em->flush();
    }

    public function returnPettyCashTransaction($entity)
    {
        $this->returnPettyCashDebitTransaction($entity);
        $this->returnPettyCashCreditTransaction($entity);
        $this->_em->getRepository('AccountingBundle:AccountCash')->insertPettyCashReturn($entity);

    }

    public function returnPettyCashDebitTransaction($entity)
    {

        $transaction = new Transaction();
        $transaction->setGlobalOption($entity->getGlobalOption());
        $transaction->setAccountRefNo($entity->getAccountRefNo());
        $transaction->setProcessHead('Petty Cash Return');
        $transaction->setProcess('Cash');
        /* Cash - Cash various */
        $transaction->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(30));
        $transaction->setAmount($entity->getAmount());
        $transaction->setDebit($entity->getAmount());
        $this->_em->persist($transaction);
        $this->_em->flush();
    }

    public function returnPettyCashCreditTransaction($entity)
    {

        $transaction = new Transaction();
        $transaction->setGlobalOption($entity->getGlobalOption());
        $transaction->setAccountRefNo($entity->getAccountRefNo());
        $transaction->setProcessHead('Petty Cash Return');
        $transaction->setProcess('Account Receivable');
        /* Cash - Petty Cash */
        $transaction->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(41));
        $transaction->setAmount('-'.$entity->getAmount());
        $transaction->setCredit($entity->getAmount());
        $this->_em->persist($transaction);
        $this->_em->flush();
    }


    public function insertExpenditureTransaction($entity)
    {
        $this->insertExpenditureDebitTransaction($entity);
        $this->insertExpenditureCreditTransaction($entity);
        $this->_em->getRepository('AccountingBundle:AccountCash')->insertExpenditureCash($entity);

    }

    public function insertExpenditureDebitTransaction($entity)
    {
        $transaction = new Transaction();
        $transaction->setGlobalOption($entity->getGlobalOption());
        $transaction->setAccountRefNo($entity->getAccountRefNo());
        $transaction->setProcessHead('Expenditure');
        $transaction->setProcess('Cash');
        /* Cash - Cash credit */
        $transaction->setAccountHead($entity->getAccountHead());
        $transaction->setAmount($entity->getAmount());
        $transaction->setDebit($entity->getAmount());
        $this->_em->persist($transaction);
        $this->_em->flush();

    }

    public function insertExpenditureCreditTransaction($entity)
    {
        $transaction = new Transaction();
        $transaction->setGlobalOption($entity->getGlobalOption());
        $transaction->setAccountRefNo($entity->getAccountRefNo());
        $transaction->setProcessHead('Expenditure');
        $transaction->setProcess('Cash');
        /* Cash - Cash various */
        $transaction->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(26));
        $transaction->setAmount('-'.$entity->getAmount());
        $transaction->setCredit($entity->getAmount());
        $this->_em->persist($transaction);
        $this->_em->flush();

    }


    public function insertSalaryTransaction(PaymentSalary $paymentSalary)
    {
        if($paymentSalary->getTransactionMethod()->getId() == 1 ){
            $this->insertSalaryDebitCashTransaction($paymentSalary);
            $this->insertSalaryCreditCashTransaction($paymentSalary);

        }else{

            $this->insertSalaryDebitBankTransaction($paymentSalary);
            $this->insertSalaryCreditBankTransaction($paymentSalary);
        }


    }

    public function insertSalaryDebitCashTransaction($paymentSalary)
    {
        $transaction = new Transaction();
        $transaction->setGlobalOption($paymentSalary->getGlobalOption());
        $transaction->setAccountRefNo($paymentSalary->getAccountRefNo());
        $transaction->setProcessHead('PaymentSalary');
        $transaction->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(25));
        $transaction->setProcess('General & Administrative expenses');
        /* Cash - Cash credit */
        $transaction->setAmount($paymentSalary->getTotalAmount());
        $transaction->setDebit($paymentSalary->getTotalAmount());
        $this->_em->persist($transaction);
        $this->_em->flush();

    }

    public function insertSalaryCreditCashTransaction($paymentSalary)
    {
        $transaction = new Transaction();
        $transaction->setGlobalOption($paymentSalary->getGlobalOption());
        $transaction->setAccountRefNo($paymentSalary->getAccountRefNo());
        $transaction->setProcessHead('PaymentSalary');
        $transaction->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(31));
        $transaction->setProcess('Cash');
        /* Cash - Cash various */
        $transaction->setAmount('-'.$paymentSalary->getTotalAmount());
        $transaction->setCredit($paymentSalary->getTotalAmount());
        $this->_em->persist($transaction);
        $this->_em->flush();

    }

    public function insertSalaryDebitBankTransaction($paymentSalary)
    {


        $transaction = new Transaction();
        $globalOption = $paymentSalary->getUser()->getGlobalOption();
        $accountHead = $this->_em->getRepository('AccountingBundle:AccountHead')->find(43);
        $transaction->setGlobalOption($globalOption);
        $transaction->setAccountHead($accountHead);
        $transaction->setProcess('Current Assets');
        /* Cash - Cash various */
        $transaction->setAmount($paymentSalary->getTotalAmount());
        $transaction->setDebit($paymentSalary->getTotalAmount());
        $this->_em->persist($transaction);
        $this->_em->flush();

    }


    public function insertSalaryCreditBankTransaction($paymentSalary)
    {
        $transaction = new Transaction();
        $globalOption = $paymentSalary->getUser()->getGlobalOption();
        $accountHead = $this->_em->getRepository('AccountingBundle:AccountHead')->find(38);
        $transaction->setGlobalOption($globalOption);
        $transaction->setAccountHead($accountHead);
        $transaction->setProcess('Current Assets');
        /* Cash - Cash various */
        $transaction->setAmount('-'.$paymentSalary->getTotalAmount());
        $transaction->setCredit($paymentSalary->getTotalAmount());
        $this->_em->persist($transaction);
        $this->_em->flush();

    }

    public function insertDamageTransaction(Damage $damage)
    {
        $this->insertDamageDebitTransaction($damage);
        $this->insertDamageCreditTransaction($damage);

    }

    public function insertDamageDebitTransaction($damage)
    {
        $transaction = new Transaction();
        $globalOption = $damage->getInventoryConfig()->getGlobalOption();
        $accountHead = $this->_em->getRepository('AccountingBundle:AccountHead')->find(45);
        $transaction->setGlobalOption($globalOption);
        $transaction->setAccountHead($accountHead);
        $transaction->setProcess('Inventory Assets');
        /* Cash - Cash various */
        $transaction->setAmount($damage->getTotal());
        $transaction->setDebit($damage->getTotal());
        $this->_em->persist($transaction);
        $this->_em->flush();
    }

    public function insertDamageCreditTransaction($damage)
    {
        $transaction = new Transaction();
        $globalOption = $damage->getInventoryConfig()->getGlobalOption();
        $accountHead = $this->_em->getRepository('AccountingBundle:AccountHead')->find(44);
        $transaction->setGlobalOption($globalOption);
        $transaction->setAccountHead($accountHead);
        $transaction->setProcess('Long Term Liabilities');
        /* Cash - Long Term Liabilities	 */
        $transaction->setAmount('-'.$damage->getTotal());
        $transaction->setCredit($damage->getTotal());
        $this->_em->persist($transaction);
        $this->_em->flush();
    }



    private  function getNetBalance($inventory)
    {

        $qb = $this->_em->createQueryBuilder();
        $qb->select('e');
        $qb->addSelect('e.balance AS balance');
        $qb->from('AccountingBundle:Transaction','e');
        $qb->where("e.inventoryConfig = :inventory");
        $qb->setParameter('inventory', $inventory->getId());
        $qb->orderBy('e.id','desc');
        $qb->setMaxResults(1);
        $netTotal = $qb->getQuery()->getSingleResult();
        if(empty($netTotal) > 0 ){
            return 0;
        }else{
            return $netTotal;
        }

    }

}
