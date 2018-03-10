<?php

namespace Appstore\Bundle\AccountingBundle\Repository;
use Appstore\Bundle\AccountingBundle\Entity\AccountSales;
use Appstore\Bundle\HospitalBundle\Entity\InvoiceTransaction;
use Appstore\Bundle\InventoryBundle\Entity\Sales;
use Appstore\Bundle\InventoryBundle\Entity\SalesReturn;
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

    public function findWithSearch(User $user,$data = '')
    {
        $globalOption = $user->getGlobalOption();
        $branch = $user->getProfile()->getBranches();

        $qb = $this->createQueryBuilder('e');
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
            $invoice =    isset($data['invoice'])? $data['invoice'] :'';
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

            if (!empty($transaction)) {
                $qb->andWhere("e.transactionMethod = :transaction");
                $qb->setParameter('transaction', $transaction);
            }
            if (!empty($mobile)) {
                $qb->join('e.customer','c');
                $qb->andWhere("c.mobile = :mobile");
                $qb->setParameter('mobile', $mobile);
            }
            if (!empty($invoice)) {
                $qb->join('e.sales','s');
                $qb->andWhere("s.invoice = :invoice");
                $qb->setParameter('invoice', $invoice);
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

    public function insertAccountSales(Sales $entity)
    {

        $em = $this->_em;
        $accountSales = new AccountSales();

        $accountSales->setAccountBank($entity->getAccountBank());
        $accountSales->setAccountMobileBank($entity->getAccountMobileBank());
        $accountSales->setGlobalOption($entity->getInventoryConfig()->getGlobalOption());
        $accountSales->setSales($entity);
        $accountSales->setCustomer($entity->getCustomer());
        $accountSales->setTransactionMethod($entity->getTransactionMethod());
        $accountSales->setTotalAmount($entity->getTotal());
        $accountSales->setAmount($entity->getPayment());
        $accountSales->setApprovedBy($entity->getApprovedBy());
        if(!empty($entity->getApprovedBy()->getProfile()->getBranches())){
            $accountSales->setBranches($entity->getApprovedBy()->getProfile()->getBranches());
        }
        $accountSales->setProcessHead('Sales');
        $accountSales->setProcess('approved');
        $em->persist($accountSales);
        $em->flush();
        $accountSalesClose = $this->updateCustomerBalance($accountSales);
        $this->_em->getRepository('AccountingBundle:AccountCash')->insertSalesCash($accountSales);
        return $accountSalesClose;

    }

    public function reportSalesIncome(User $user,$data)
    {
        if(empty($data)){
            $datetime = new \DateTime("now");
            $data['startDate'] = $datetime->format('Y-m-d 00:00:00');
            $data['endDate'] = $datetime->format('Y-m-d 23:59:59');
        }else{
            $data['startDate'] = date('Y-m-d',strtotime($data['startDate']));
            $data['endDate'] = date('Y-m-d',strtotime($data['endDate']));
        }

        $salesPrice = $this->_em->getRepository('InventoryBundle:SalesItem')->reportSalesPrice($user,$data);
        $purchasePrice = $this->_em->getRepository('InventoryBundle:SalesItem')->reportPurchasePrice($user,$data);
        $salesVat = $this->_em->getRepository('InventoryBundle:SalesItem')->reportProductVat($user, $data);
        $expenditures = $this->_em->getRepository('AccountingBundle:Transaction')->reportTransactionIncome($user->getGlobalOption(), $accountHeads = array(37), $data);
        $revenues = $this->_em->getRepository('AccountingBundle:Transaction')->reportTransactionIncome($user->getGlobalOption(), $accountHeads = array(20), $data);
        $data =  array('salesAmount' => $salesPrice ,'purchasePrice' => $purchasePrice,'revenues' => $revenues ,'expenditures' => $expenditures,'salesVat' => $salesVat);
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

        $salesPrice             = $this->_em->getRepository('InventoryBundle:SalesItem')->reportSalesPrice($user,$data);
        $purchasePrice          = $this->_em->getRepository('InventoryBundle:SalesItem')->reportPurchasePrice($user,$data);
        $salesVat               = $this->_em->getRepository('InventoryBundle:SalesItem')->reportProductVat($user, $data);
        $expenditures           = $this->_em->getRepository('AccountingBundle:Transaction')->reportTransactionIncome($user->getGlobalOption(), $accountHeads = array(37), $data);
        $revenues               = $this->_em->getRepository('AccountingBundle:Transaction')->reportTransactionIncome($user->getGlobalOption(), $accountHeads = array(20), $data);
        $administrative         = $this->_em->getRepository('AccountingBundle:Transaction')->reportTransactionIncome($user->getGlobalOption(), $accountHeads = array(23), $data);
        $data =  array('salesAmount' => $salesPrice ,'purchasePrice' => $purchasePrice,'revenues' => $revenues ,'expenditures' => $expenditures,'administrative' => $administrative, 'salesVat' => $salesVat);
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
        $expenditures           = $this->_em->getRepository('AccountingBundle:Transaction')->reportTransactionIncome($user->getGlobalOption(), $accountHeads = array(37), $data);
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
        $accountSales->setProcessHead('Sales');
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
        $accountSales->setProcessHead('Sales');
        $accountSales->setProcess('approved');
        $em->persist($accountSales);
        $em->flush();
        $this->_em->getRepository('AccountingBundle:AccountCash')->insertSalesCash($accountSales);
        return $accountSales;

    }

    public function reportHmsTransactionIncome()
    {

    }

    public function accountSalesReverse(Sales $sales)
    {
/*        $em = $this->_em;

        $data = array('sales' => $sales->getId());
        $result = $this->salesOverview($sales->getApprovedBy(), $data);
        $balance = ($result['totalAmount'] -  $result['receiveAmount']);
        $lastBalance = ($balance + $entity->getDue());
        $accountSales->setBalance($lastBalance);

        $accountSales->setApprovedBy($entity->getApprovedBy());
        if(!empty($entity->getApprovedBy()->getProfile()->getBranches())){
            $accountSales->setBranches($entity->getApprovedBy()->getProfile()->getBranches());
        }
        $accountSales->setProcessHead('Sales');
        $accountSales->setProcess('approved');
        $em->persist($accountSales);
        $em->flush();*/
    }




}
