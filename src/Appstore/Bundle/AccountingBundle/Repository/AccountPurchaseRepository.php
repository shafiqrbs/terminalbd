<?php

namespace Appstore\Bundle\AccountingBundle\Repository;
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
        $qb->select('SUM(e.totalAmount) AS totalAmount, SUM(e.amount) AS amount, SUM(e.dueAmount) AS dueAmount');
        $qb->where("e.globalOption = :globalOption");
        $qb->setParameter('globalOption', $globalOption);
        $qb->andWhere("e.process = :process");
        $qb->setParameter('process', 'approved');
        $this->handleSearchBetween($qb,$data);
        $result = $qb->getQuery()->getSingleResult();
        $data =  array('totalAmount'=> $result['totalAmount'],'amount'=> $result['amount'],'dueAmount'=> $result['dueAmount']);
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

                $qb->andWhere("e.updated >= :startDate");
                $qb->setParameter('startDate', $startDate);
                $qb->andWhere("e.updated <= :endDate");
                $qb->setParameter('endDate', $endDate);

        }else{

                $startDate = isset($data['startDate'])  ? $data['startDate'] : '';
                $endDate =   isset($data['endDate'])  ? $data['endDate'] : '';
                $vendor =    isset($data['vendor'])? $data['vendor'] :'';

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
        }

    }

    public function insertAccountPurchase(Purchase $entity,$inventory)
    {

        $em = $this->_em;
       // $receiveDate = $entity->getReceiveDate();
        //$receiveDate->format('Y-m-d 00:00:00');

        $accountPurchase = new AccountPurchase();
        $accountPurchase->setInventoryConfig($inventory);
        $accountPurchase->setGlobalOption($inventory->getGlobalOption());
        $accountPurchase->setPurchase($entity);
        $accountPurchase->setVendor($entity->getVendor());
        /* Cash - Cash Credit */
        $accountPurchase->setAccountHead($em->getRepository('AccountingBundle:AccountHead')->find(31));
        $accountPurchase->setToIncrease('Debit');
        $accountPurchase->setProcess('approved');
        $accountPurchase->setApprovedBy($entity->getCreatedBy());
        $accountPurchase->setTotalAmount($entity->getTotalAmount());
        $accountPurchase->setAmount($entity->getPaymentAmount());
        $accountPurchase->setDueAmount($entity->getDueAmount());
        $accountPurchase->setProcessType('Purchase');
        $accountPurchase->setReceiveDate($entity->getReceiveDate());
        $em->persist($accountPurchase);

        $em->flush();

    }

    public function insertAccountPurchaseReturn(PurchaseReturn $entity)
    {
        $em = $this->_em;

        $accountPurchase = new AccountPurchase();
        $accountPurchase->setInventoryConfig($entity->getInventoryConfig());
        $accountPurchase->setGlobalOption($entity->getInventoryConfig()->getGlobalOption());
        $accountPurchase->setPurchaseReturn($entity);
        $accountPurchase->setVendor($entity->getVendor());
        /* Current assets - Account Receivable */
        $accountPurchase->setAccountHead($em->getRepository('AccountingBundle:AccountHead')->find(4));
        $accountPurchase->setToIncrease('Debit');
        $accountPurchase->setProcess('approved');
        $accountPurchase->setApprovedBy($entity->getCreatedBy());
        $accountPurchase->setAmount($entity->getTotal());
        $accountPurchase->setProcessType('Purchase Return');
        $accountPurchase->setReceiveDate($entity->getUpdated());
        $em->persist($accountPurchase);
        $em->flush();

    }

    public function insertAccountPurchaseReplace(PurchaseReturn $entity,$replaceAmount)
    {
        $em = $this->_em;
        $accountPurchase = new AccountPurchase();
        $accountPurchase->setInventoryConfig($entity->getInventoryConfig());
        $accountPurchase->setGlobalOption($entity->getInventoryConfig()->getGlobalOption());
        $accountPurchase->setPurchaseReturn($entity);
        $accountPurchase->setVendor($entity->getVendor());
        /* Current Liabilities	- Accounts Payable */
        $accountPurchase->setAccountHead($em->getRepository('AccountingBundle:AccountHead')->find(13));
        $accountPurchase->setToIncrease('Credit');
        $accountPurchase->setProcess('approved');
        $accountPurchase->setApprovedBy($entity->getCreatedBy());
        $accountPurchase->setTotalAmount($replaceAmount);
        $accountPurchase->setDueAmount($replaceAmount);
        $accountPurchase->setProcessType('Purchase Replace');
        $accountPurchase->setReceiveDate($entity->getUpdated());
        $em->persist($accountPurchase);
        $em->flush();

    }




}
