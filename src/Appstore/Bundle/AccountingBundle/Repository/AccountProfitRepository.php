<?php

namespace Appstore\Bundle\AccountingBundle\Repository;
use Appstore\Bundle\AccountingBundle\Entity\AccountBalanceTransfer;
use Appstore\Bundle\AccountingBundle\Entity\AccountBank;
use Appstore\Bundle\AccountingBundle\Entity\AccountCash;
use Appstore\Bundle\AccountingBundle\Entity\AccountJournal;
use Appstore\Bundle\AccountingBundle\Entity\AccountOnlineOrder;
use Appstore\Bundle\AccountingBundle\Entity\AccountProfit;
use Appstore\Bundle\AccountingBundle\Entity\AccountPurchase;
use Appstore\Bundle\AccountingBundle\Entity\AccountPurchaseCommission;
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
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * AccountCashRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AccountProfitRepository extends EntityRepository
{
    public function findWithSearch($globalOption,$data = '')
    {
        $qb = $this->createQueryBuilder('e');
        $qb->where("e.globalOption = :globalOption");
        $qb->setParameter('globalOption', $globalOption);
        $qb->orderBy('e.updated','DESC');
        $result = $qb->getQuery()->getResult();
        return $result;

    }

    public function insertAccountProfit(GlobalOption $option,$month,$year,$data)
    {
        $em = $this->_em;
        $date = new \DateTime($data);
        $entity = new AccountProfit();
        $entity->setGlobalOption($option);
        $entity->setMonth($month);
        $entity->setYear($year);
        $entity->setGenerateMonth($date);
        $entity->setCreated($date);
        $entity->setUpdated($date);
        $em->persist($entity);
        $em->flush();
        return $entity;
    }


    public function reportMonthlyProfitLoss(AccountProfit $profit,$data)
    {
        $em = $this->_em;
        $this->removeExistingTransaction($profit);
        $journalAccountPurchase = $this->monthlyPurchaseJournal($profit, $data);
        $journalAccountSales = $this->monthlySalesJournal($profit, $data);
        $monthlySalesAccountReceivable = $this->monthlySalesAccountReceivable($profit, $data);
        $journalAccountSalesAdjustment = $this->monthlySalesAdjustmentJournal($profit, $data);
        $journalExpenditure = $this->monthlyExpenditureJournal($profit, $data);
        $journalContra = $this->monthlyContraJournal($profit, $data);
        $salesPurchasePrice = $this->reportSalesItemPurchaseSalesOverview($profit, $data);

        if($journalAccountPurchase) {
            foreach ($journalAccountPurchase as $row):
                if (in_array($row['processType'], array('Credit','Outstanding', 'Opening'))) {
                    $em->getRepository('AccountingBundle:Transaction')->insertPurchaseMonthlyOpeningTransaction($profit, $row);
                } elseif ($row['amount'] > 0 and $row['processType'] == 'Debit') {
                    $em->getRepository('AccountingBundle:Transaction')->insertPurchaseMonthlyDiscountTransaction($profit, $row);
                } elseif ($row['amount'] > 0 and $row['processType'] == 'Due') {
                    $em->getRepository('AccountingBundle:Transaction')->insertPurchaseMonthlyDueTransaction($profit, $row);
                } elseif ($row['total'] > 0 and $row['processType'] == 'Purchase') {
                    $em->getRepository('AccountingBundle:Transaction')->insertPurchaseMonthlyTransaction($profit, $row);
                } elseif ($row['amount'] > 0 and $row['processType'] == 'Advance') {
                    $em->getRepository('AccountingBundle:Transaction')->insertPurchaseAdvanceMonthlyTransaction($profit, $row);
                }
            endforeach;
        }


        if($journalAccountSales){

            foreach ($journalAccountSales as $row):

                if(in_array($row['processHead'],array('Debit','Outstanding','Opening'))){
                    $em->getRepository('AccountingBundle:Transaction')->insertSalesMonthlyOpeningTransaction($profit,$row);
                }elseif($row['amount'] > 0 and $row['processHead'] == 'Credit' ){
                    $em->getRepository('AccountingBundle:Transaction')->insertSalesMonthlyDiscountTransaction($profit,$row);
                }elseif($row['amount'] > 0 and $row['processHead'] == 'Due' ){
                    $em->getRepository('AccountingBundle:Transaction')->insertSalesMonthlyDueTransaction($profit,$row);
                }elseif($row['amount'] > 0 and $row['processHead'] == 'Sales-Return'){
                    $em->getRepository('AccountingBundle:Transaction')->insertSalesMonthlyMedicineReturnTransaction($profit,$row);
                }elseif(in_array($row['processHead'],array('medicine','business','inventory','restaurant','hotel'))){
                    $em->getRepository('AccountingBundle:Transaction')->insertSalesMonthlyTransaction($profit,$row);
                }elseif(in_array($row['processHead'],array('hospital','diagnostic','admission','visit'))){
                    $em->getRepository('AccountingBundle:Transaction')->insertSalesHospitalMonthlyTransaction($profit,$row);
                }elseif(in_array($row['processHead'],array('Advance'))){
                    $em->getRepository('AccountingBundle:Transaction')->insertSalesAdvanceMonthlyTransaction($profit,$row);
                }

            endforeach;
        }
        if($journalAccountSalesAdjustment) {
            $em->getRepository('AccountingBundle:Transaction')->insertSalesAdjustmentMonthlyTransaction($profit, $journalAccountSalesAdjustment);
        }
        if($salesPurchasePrice['total'] > 0) {
            $em->getRepository('AccountingBundle:Transaction')->insertSalesMonthlyPurchaseTransaction($profit, round($salesPurchasePrice['total']));
        }
        if($journalExpenditure){
            foreach ($journalExpenditure as $row):
                $em->getRepository('AccountingBundle:Transaction')->insertExpenseMonthlyTransaction($profit,$row);
            endforeach;
        }
        if($journalContra){
            foreach ($journalContra as $row):
                $em->getRepository('AccountingBundle:Transaction')->insertContraMonthlyTransaction($profit,$row);
            endforeach;
        }
        $salesReturn =$this->getTransactionSalesReturn($profit, 'Sales-Return');
       // $em->getRepository('AccountingBundle:Transaction')->insertMonthlySalesAccountReceivable($profit,$monthlySalesAccountReceivable);
        $salesReconcialtion = $this->monthlyProfitReconcialtionProcess($profit, 'sales');
        $salesAdjustmentReconcialtion = $this->monthlyProfitReconcialtionProcess($profit, 'sales-adjustment');
        $salesPurchaserReconcialtion = $this->monthlyProfitReconcialtionProcess($profit, 'sales-purchase');
        $end = $profit->getGenerateMonth();
        $date['startDate'] = $end->format('Y-m-01 00:00:00');
        $date['endDate'] = $end->format('Y-m-t 23:59:59');
        $expenditures = $this->_em->getRepository('AccountingBundle:Transaction')->reportTransactionIncomeLoss($profit->getGlobalOption(), $accountHeads = array(37,23),$date);
        $operatingRevenue = $this->_em->getRepository('AccountingBundle:Transaction')->reportTransactionIncomeLoss($profit->getGlobalOption(), $accountHeads = array(20),$date);
        $data =  array('sales' => $monthlySalesAccountReceivable['total'] , 'salesReturn' => $salesReturn['debit'] ,'salesAdjustment' => $salesAdjustmentReconcialtion['debit'] ,'purchaseAdjustment' => $salesAdjustmentReconcialtion['credit'] ,'purchase' => $salesPurchaserReconcialtion['credit'], 'operatingRevenue' => $operatingRevenue['amount'], 'expenditure' => $expenditures['amount']);
        return $data;

    }


    private function monthlyPurchaseJournal(AccountProfit $profit,$data)
    {
        $config = $profit->getGlobalOption()->getId();
        $compare = new \DateTime($data);
        $month =  $compare->format('F');
        $year =  $compare->format('Y');
        $sql = "SELECT processType,transactionMethod_id as method,accountBank_id as bank ,accountMobileBank_id as mobile ,COALESCE(SUM(purchase.purchaseAmount),0) as total, COALESCE(SUM(purchase.payment),0) as amount
                FROM account_purchase as purchase
                WHERE purchase.globalOption_id = :config AND purchase.process = :process AND  MONTHNAME(purchase.created) =:month AND YEAR(purchase.created) =:year GROUP BY transactionMethod_id,processType,accountBank_id,accountMobileBank_id";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('config', $config);
        $stmt->bindValue('process', 'approved');
        $stmt->bindValue('month', $month);
        $stmt->bindValue('year', $year);
        $stmt->execute();
        $result =  $stmt->fetchAll();
        return $result;
    }

    private function monthlySalesJournal(AccountProfit $profit,$data)
    {
        $config = $profit->getGlobalOption()->getId();
        $compare = new \DateTime($data);
        $month =  $compare->format('F');
        $year =  $compare->format('Y');
        $sql = "SELECT processHead,transactionMethod_id as method,accountBank_id as bank ,accountMobileBank_id as mobile ,(COALESCE(SUM(sales.totalAmount),0) + COALESCE(SUM(sales.tloPrice),0)) as total, COALESCE(SUM(sales.amount),0) as amount
                FROM account_sales as sales
                WHERE sales.globalOption_id = :config AND sales.process = :process AND  MONTHNAME(sales.created) =:month AND YEAR(sales.created) =:year GROUP BY transactionMethod_id,processHead,accountBank_id,accountMobileBank_id";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('config', $config);
        $stmt->bindValue('process', 'approved');
        $stmt->bindValue('month', $month);
        $stmt->bindValue('year', $year);
        $stmt->execute();
        $result =  $stmt->fetchAll();
        return $result;
    }

    private function monthlySalesAccountReceivable(AccountProfit $profit,$data)
    {
        $config = $profit->getGlobalOption()->getId();
        $compare = new \DateTime($data);
        $month =  $compare->format('F');
        $year =  $compare->format('Y');
        $sql = "SELECT (COALESCE(SUM(sales.totalAmount),0) + COALESCE(SUM(sales.tloPrice),0)) as total, COALESCE(SUM(sales.amount),0) as amount
                FROM account_sales as sales
                WHERE sales.globalOption_id = :config AND sales.process = :process AND  MONTHNAME(sales.created) =:month AND YEAR(sales.created) =:year";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('config', $config);
        $stmt->bindValue('process', 'approved');
        $stmt->bindValue('month', $month);
        $stmt->bindValue('year', $year);
        $stmt->execute();
        $result =  $stmt->fetch();
        return $result;

       /* $option =  $user->getGlobalOption()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->select('sum(e.totalAmount) as total , sum(e.purchasePrice) as purchasePrice, sum(e.vat) as vat');
        $qb->where('e.globalOption = :option')->setParameter('option', $option);
        $qb->andWhere('e.processType = :type')->setParameter('type', 'Sales');
        $qb->andWhere('e.process = :process')->setParameter('process', 'approved');
        $this->handleSearchBetween($qb,$data);
        return $qb->getQuery()->getOneOrNullResult();*/
    }



    private function monthlySalesAdjustmentJournal(AccountProfit $profit,$data)
    {
        $config = $profit->getGlobalOption()->getId();
        $compare = new \DateTime($data);
        $month =  $compare->format('F');
        $year =  $compare->format('Y');
        $sql = "SELECT COALESCE(SUM(sales.sales),0) as sales, COALESCE(SUM(sales.purchase),0) as purchase
                FROM account_sales_adjustment as sales
                WHERE sales.globalOption_id = :config AND sales.process = :process AND  MONTHNAME(sales.created) =:month AND YEAR(sales.created) =:year";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('config', $config);
        $stmt->bindValue('process', 'approved');
        $stmt->bindValue('month', $month);
        $stmt->bindValue('year', $year);
        $stmt->execute();
        $result =  $stmt->fetch();
        return $result;
    }

    public  function reportSalesItemPurchaseSalesOverview(AccountProfit $profit, $data){

        $config =  $profit->getGlobalOption()->getId();
        $compare = new \DateTime($data);
        $month =  $compare->format('F');
        $year =  $compare->format('Y');
        $sql = "SELECT COALESCE(SUM(sales.purchasePrice),0) as total
                FROM account_sales as sales
                WHERE sales.globalOption_id = :config AND sales.process = :process AND  MONTHNAME(sales.created) =:month AND YEAR(sales.created) =:year";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('config', $config);
        $stmt->bindValue('process', 'approved');
        $stmt->bindValue('month', $month);
        $stmt->bindValue('year', $year);
        $stmt->execute();
        $result =  $stmt->fetch();
        return $result;

    }

    public  function reportBusinessSalesItemPurchaseSalesOverview(AccountProfit $profit, $data){

        $config =  $profit->getGlobalOption()->getBusinessConfig()->getId();

        $compare = new \DateTime($data);
        $month =  $compare->format('F');
        $year =  $compare->format('Y');
        $sql = "SELECT COALESCE(SUM(salesItem.totalQuantity * salesItem.purchasePrice),0) as total
                FROM business_invoice_particular as salesItem
                JOIN business_invoice as sales ON salesItem.businessInvoice_id = sales.id
                WHERE sales.businessConfig_id = :config AND sales.process = :process AND  MONTHNAME(sales.created) =:month AND YEAR(sales.created) =:year";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('config', $config);
        $stmt->bindValue('process', 'Done');
        $stmt->bindValue('month', $month);
        $stmt->bindValue('year', $year);
        $stmt->execute();
        $result =  $stmt->fetch();
        return $result;
    }

    private function monthlyExpenditureJournal(AccountProfit $profit,$data)
    {
        $config = $profit->getGlobalOption()->getId();
        $compare = new \DateTime($data);
        $month =  $compare->format('F');
        $year =  $compare->format('Y');
        $sql = "SELECT ec.accountHead_id as head,expense.transactionMethod_id as method,expense.accountBank_id as bank,expense.accountMobileBank_id as mobile,COALESCE(SUM(expense.amount),0) as amount
                FROM Expenditure as expense
                JOIN expenseCategory as ec ON expense.expenseCategory_id = ec.id  
                WHERE expense.globalOption_id = :config AND expense.process = :process AND  MONTHNAME(expense.created) =:month AND YEAR(expense.created) =:year GROUP BY transactionMethod_id,ec.accountHead_id,accountBank_id,accountMobileBank_id";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('config', $config);
        $stmt->bindValue('process', 'approved');
        $stmt->bindValue('month', $month);
        $stmt->bindValue('year', $year);
        $stmt->execute();
        $result =  $stmt->fetchAll();
        return $result;
    }

    private function monthlyContraJournal(AccountProfit $profit,$data)
    {
        $config = $profit->getGlobalOption()->getId();
        $compare = new \DateTime($data);
        $month =  $compare->format('F');
        $year =  $compare->format('Y');
        $sql = "SELECT contra.fromTransactionMethod_id as fromMethod ,contra.fromAccountBank_id as fromBank,contra.fromAccountMobileBank_id as fromMobileBank,contra.toTransactionMethod_id as toMethod,contra.toAccountBank_id as toBank,contra.toAccountMobileBank_id as toMobileBank,COALESCE(SUM(contra.amount),0) as amount
                FROM account_balance_transfer as contra
                WHERE contra.globalOption_id = :config AND contra.process = :process AND  MONTHNAME(contra.created) =:month AND YEAR(contra.created) =:year GROUP BY contra.fromTransactionMethod_id,contra.fromAccountBank_id,contra.fromAccountMobileBank_id,contra.toTransactionMethod_id,contra.toAccountBank_id,contra.toAccountMobileBank_id";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('config', $config);
        $stmt->bindValue('process', 'approved');
        $stmt->bindValue('month', $month);
        $stmt->bindValue('year', $year);
        $stmt->execute();
        $result =  $stmt->fetchAll();
        return $result;
    }

    private function monthlyProfitReconcialtion(AccountProfit $profit,$data)
    {
        $config = $profit->getGlobalOption()->getId();
        $sql = "SELECT trans.process as process,COALESCE(SUM(trans.debit),0) as debit, COALESCE(SUM(trans.credit),0) as credit
                FROM Transaction as trans
                WHERE trans.globalOption_id = :config AND trans.accountProfit_id = :profit GROUP BY trans.process";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('config', $config);
        $stmt->bindValue('profit', $profit->getId());
        $stmt->execute();
        $result =  $stmt->fetchAll();
        return $result;
    }

    private function monthlyProfitReconcialtionProcess(AccountProfit $profit,$process)
    {
        $config = $profit->getGlobalOption()->getId();
        $sql = "SELECT trans.process as process,COALESCE(SUM(trans.debit),0) as debit, COALESCE(SUM(trans.credit),0) as credit
                FROM Transaction as trans
                WHERE trans.globalOption_id = :config AND trans.accountProfit_id = :profit AND trans.process = :process";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('config', $config);
        $stmt->bindValue('profit', $profit->getId());
        $stmt->bindValue('process', $process);
        $stmt->execute();
        $result =  $stmt->fetch();
        return $result;
    }

    private function getTransactionSalesReturn(AccountProfit $profit,$process)
    {
        $config = $profit->getGlobalOption()->getId();
        $sql = "SELECT trans.process as process,COALESCE(SUM(trans.debit),0) as debit, COALESCE(SUM(trans.credit),0) as credit
                FROM Transaction as trans
                WHERE trans.globalOption_id = :config AND trans.accountProfit_id = :profit AND trans.process = :process";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('config', $config);
        $stmt->bindValue('profit', $profit->getId());
        $stmt->bindValue('process', $process);
        $stmt->execute();
        $result =  $stmt->fetch();
        return $result;
    }

    public function removeExistingTransaction(AccountProfit $profit){
        $em = $this->_em;
        $transaction = $em->createQuery("DELETE AccountingBundle:Transaction e WHERE e.accountProfit = {$profit->getId()}");
        $transaction->execute();
    }

}
