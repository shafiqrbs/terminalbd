<?php

namespace Appstore\Bundle\AccountingBundle\Repository;
use Appstore\Bundle\AccountingBundle\Entity\AccountSales;
use Appstore\Bundle\InventoryBundle\Entity\Sales;
use Appstore\Bundle\InventoryBundle\Entity\SalesReturn;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

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
            $qb->andWhere("s.branches = :branch");
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
            $qb->andWhere("s.branches = :branch");
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
        if(empty($data))
        {
             $datetime = new \DateTime("now");
             $startDate = $datetime->format('Y-m-d 00:00:00');
             $endDate = $datetime->format('Y-m-d 23:59:59');

            /*
             $qb->andWhere("e.updated >= :startDate");
             $qb->setParameter('startDate', $startDate);
             $qb->andWhere("e.updated <= :endDate");
             $qb->setParameter('endDate', $endDate);*/

        }else{

            $startDate = isset($data['startDate'])  ? $data['startDate'] : '';
            $endDate =   isset($data['endDate'])  ? $data['endDate'] : '';
            $mobile =    isset($data['mobile'])? $data['mobile'] :'';
            $account =    isset($data['accountHead'])? $data['accountHead'] :'';

            if (!empty($data['startDate']) ) {

                $qb->andWhere("e.updated >= :startDate");
                $qb->setParameter('startDate', $startDate.' 00:00:00');
            }
            if (!empty($data['endDate'])) {

                $qb->andWhere("e.updated <= :endDate");
                $qb->setParameter('endDate', $endDate.' 23:59:59');
            }
            if (!empty($mobile)) {
                $qb->join('e.customer','c');
                $qb->andWhere("c.mobile = :mobile");
                $qb->setParameter('mobile', $mobile);
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
            array('globalOption' => $globalOption,'customer' => $entity->getCustomer(),'process'=>'approved'),
            array('id' => 'DESC')
        );

        if (empty($entity)) {
            return 0;
        }
        return $entity->getBalance();
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

        $data = array('mobile'=> $entity->getCustomer()->getMobile());

        $result = $this->salesOverview($entity->getInventoryConfig()->getGlobalOption(),$data);
        $balance = ($result['totalAmount'] -  $result['receiveAmount']);
        $lastBalance = ($balance + $entity->getDue());
        $accountSales->setBalance($lastBalance);

        $accountSales->setApprovedBy($entity->getCreatedBy());
        if(!empty($entity->getCreatedBy()->getProfile()->getBranches())){
            $entity->setBranches($entity->getCreatedBy()->getProfile()->getBranches());
        }
        $accountSales->setProcessHead('Sales');
        $accountSales->setProcess('approved');
        $em->persist($accountSales);
        $em->flush();
        $this->_em->getRepository('AccountingBundle:AccountCash')->insertSalesCash($accountSales);
        return $accountSales;

    }

    public function reportIncome($globalOption,$data)
    {
        if(empty($data)){
            $datetime = new \DateTime("now");
            $data['startDate'] = $datetime->format('Y-m-d 00:00:00');
            $data['endDate'] = $datetime->format('Y-m-d 23:59:59');
        }else{
            $data['startDate'] = date('Y-m-d',strtotime($data['startDate']));
            $data['endDate'] = date('Y-m-d',strtotime($data['endDate']));
        }

        $salesPrice = $this->_em->getRepository('InventoryBundle:SalesItem')->reportSalesPrice($globalOption,$data);
        $purchasePrice = $this->_em->getRepository('InventoryBundle:SalesItem')->reportPurchasePrice($globalOption,$data);
        $salesVat = $this->_em->getRepository('InventoryBundle:SalesItem')->reportProductVat($globalOption,$data);
        $expenditures = $this->_em->getRepository('AccountingBundle:Transaction')->reportTransactionIncome($globalOption, $accountHeads = array(37), $data);
        $revenues = $this->_em->getRepository('AccountingBundle:Transaction')->reportTransactionIncome($globalOption, $accountHeads = array(20), $data);
        $data =  array('salesAmount' => $salesPrice ,'purchasePrice' => $purchasePrice,'revenues' => $revenues ,'expenditures' => $expenditures,'salesVat' => $salesVat);
        return $data;

    }

    public function reportMonthlyIncome($globalOption,$data)
    {
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
        $result = $qb->getQuery()->getSingleResult();
        $purchasePrice = $this->_em->getRepository('InventoryBundle:SalesItem')->reportPurchasePrice($globalOption,$data);
        $expenditures = $this->_em->getRepository('AccountingBundle:Expenditure')->reportExpenditure($globalOption,$data);
        $administrative = $this->_em->getRepository('AccountingBundle:Transaction')->reportAdministrativeRevenue($globalOption,$data);
        $revenuesDebit = $this->_em->getRepository('AccountingBundle:AccountJournal')->reportOperatingRevenueCredit($globalOption,$data);
        $revenuesCredit = $this->_em->getRepository('AccountingBundle:AccountJournal')->reportOperatingRevenueCredit($globalOption,$data);

        $data =  array('salesAmount' => $result['salesAmount'],'purchasePrice' => $purchasePrice,'administrative' => $administrative ,'revenuesDebit' => $revenuesDebit ,'revenuesCredit' => $revenuesCredit ,'expenditures' => $expenditures);
        return $data;

    }



}
