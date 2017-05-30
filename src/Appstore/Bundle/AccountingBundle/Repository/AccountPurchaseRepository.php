<?php

namespace Appstore\Bundle\AccountingBundle\Repository;
use Appstore\Bundle\AccountingBundle\Entity\AccountJournal;
use Appstore\Bundle\AccountingBundle\Entity\AccountPurchase;
use Appstore\Bundle\InventoryBundle\Entity\Purchase;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseReturn;
use Doctrine\ORM\EntityRepository;

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
        $qb->setParameter('globalOption', $globalOption);
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('e.updated','DESC');
        $result = $qb->getQuery();
        return $result;

    }
    public function accountPurchaseOverview($globalOption,$data)
    {

        $qb = $this->createQueryBuilder('e');
        $qb->select('SUM(e.purchaseAmount) AS purchaseAmount, SUM(e.payment) AS payment');
        $qb->where("e.globalOption = :globalOption");
        $qb->setParameter('globalOption', $globalOption);
        $qb->andWhere("e.process = :process");
        $qb->setParameter('process', 'approved');
        $this->handleSearchBetween($qb,$data);
        $result = $qb->getQuery()->getSingleResult();
        $data =  array('purchaseAmount'=> $result['purchaseAmount'],'payment'=> $result['payment']);
        return $data;

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
                $vendor =    isset($data['vendor'])? $data['vendor'] :'';
                $transactionMethod =    isset($data['transactionMethod'])? $data['transactionMethod'] :'';

                if (!empty($data['startDate']) and !empty($data['endDate']) ) {

                    $qb->andWhere("e.updated >= :startDate");
                    $qb->setParameter('startDate', $startDate.' 00:00:00');
                }
                if (!empty($data['endDate']) and !empty($data['startDate'])) {

                    $qb->andWhere("e.updated <= :endDate");
                    $qb->setParameter('endDate', $endDate.' 00:00:00');
                }
                if (!empty($vendor)) {

                    $qb->join('e.vendor','v');
                    $qb->andWhere("v.companyName = :vendor");
                    $qb->setParameter('vendor', $vendor);
                }
                if (!empty($transactionMethod)) {
                    $qb->andWhere("e.transactionMethod = :transactionMethod");
                    $qb->setParameter('transactionMethod', $transactionMethod);
                }
        }

    }

    public function lastInsertPurchase($globalOption,$vendor)
    {
        $em = $this->_em;
        $entity = $em->getRepository('AccountingBundle:AccountPurchase')->findOneBy(
            array('globalOption' => $globalOption,'vendor' => $vendor,'process'=>'approved'),
            array('id' => 'DESC')
        );

        if (empty($entity)) {
            return 0;
        }
        return $entity->getBalance();
    }


    public function insertAccountPurchase(Purchase $entity)
    {

        $data = array('vendor' => $entity->getVendor()->getCompanyName());
        $result = $this->accountPurchaseOverview($entity->getInventoryConfig()->getGlobalOption(),$data);
        $balance = ( $result['purchaseAmount'] - $result['payment']);

        $em = $this->_em;


        $accountPurchase = new AccountPurchase();
        $accountPurchase->setGlobalOption($entity->getInventoryConfig()->getGlobalOption());
        $accountPurchase->setPurchase($entity);
        $accountPurchase->setVendor($entity->getVendor());
        $accountPurchase->setTransactionMethod($entity->getTransactionMethod());
        $accountPurchase->setPurchaseAmount($entity->getTotalAmount());
        $accountPurchase->setPayment($entity->getPaymentAmount());
        $accountPurchase->setBalance(($balance + $entity->getTotalAmount()) - $accountPurchase->getPayment() );
        $accountPurchase->setProcessHead('Purchase');
        $accountPurchase->setReceiveDate($entity->getReceiveDate());
        $accountPurchase->setProcess('approved');
        $accountPurchase->setApprovedBy($entity->getApprovedBy());
        $em->persist($accountPurchase);
        $em->flush();
        $this->_em->getRepository('AccountingBundle:AccountCash')->insertPurchaseCash($accountPurchase);
        return $accountPurchase;

    }

    public function removeApprovedAccountPurchase(Purchase $purchase)
    {

        $accountPurchase = $purchase->getAccountPurchase();

        $accountCash = $this->_em->getRepository('AccountingBundle:AccountCash')->findOneBy(array('processHead'=>'Purchase','globalOption' => $accountPurchase->getGlobalOption() ,'accountRefNo' => $accountPurchase->getAccountRefNo()));
        if($accountCash){
            $this->_em->remove($accountCash);
            $this->_em->flush();
        }

        $transactions = $this->_em->getRepository('AccountingBundle:Transaction')->findBy(array('processHead'=>'Purchase','globalOption' => $accountPurchase->getGlobalOption() ,'accountRefNo' => $accountPurchase->getAccountRefNo()));
        foreach ($transactions as $transaction){
            if($transaction){
                $this->_em->remove($transaction);
                $this->_em->flush();
            }
        }

    }

}
