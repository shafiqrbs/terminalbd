<?php

namespace Appstore\Bundle\AccountingBundle\Repository;
use Appstore\Bundle\AccountingBundle\Entity\AccountJournal;
use Appstore\Bundle\AccountingBundle\Entity\AccountPurchase;
use Appstore\Bundle\AccountingBundle\Entity\AccountPurchaseReturn;
use Appstore\Bundle\InventoryBundle\Entity\Purchase;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseReturn;
use Doctrine\ORM\EntityRepository;

/**
 * AccountPurchaseReturnRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AccountPurchaseReturnRepository extends EntityRepository
{

    public function findWithSearch($globalOption,$data = '')
    {
        $qb = $this->createQueryBuilder('e');
        $qb->where("e.globalOption = :globalOption");
        $qb->setParameter('globalOption', $globalOption);
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('e.id','DESC');
        $result = $qb->getQuery();
        return $result;

    }
    public function accountPurchaseOverview($globalOption,$data)
    {

        $qb = $this->createQueryBuilder('e');
        $qb->select('SUM(e.totalAmount) AS purchaseAmount, SUM(e.amount) AS payment');
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

/*                $qb->andWhere("e.updated >= :startDate");
                $qb->setParameter('startDate', $startDate);
                $qb->andWhere("e.updated <= :endDate");
                $qb->setParameter('endDate', $endDate);*/

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

    public function lastInsertPurchase($entity)
    {
        $em = $this->_em;
        $entity = $em->getRepository('AccountingBundle:AccountPurchaseReturn')->findOneBy(
            array('globalOption'=>$entity->getInventoryConfig()->getGlobalOption(),'vendor'=> $entity->getVendor()),
            array('id' => 'DESC')
        );

        if (empty($entity)) {
            return 0;
        }
        return $entity->getBalance();
    }


    public function insertAccountPurchaseReturn(PurchaseReturn $entity)
    {

        $balance = $this->lastInsertPurchase($entity);
        $em = $this->_em;
        $accountPurchase = new AccountPurchaseReturn();
        $accountPurchase->setGlobalOption($entity->getInventoryConfig()->getGlobalOption());
        $accountPurchase->setPurchaseReturn($entity);
        $accountPurchase->setVendor($entity->getVendor());
        $accountPurchase->setProcess('approved');
        $accountPurchase->setTotalAmount($entity->getTotal());
        $accountPurchase->setAmount($entity->getTotal());
        $accountPurchase->setBalance($balance + $entity->getTotal() );
        $accountPurchase->setProcessHead('PurchaseReturn');
        $accountPurchase->setTransactionMethod($this->_em->getRepository('SettingToolBundle:TransactionMethod')->find(1));
        $em->persist($accountPurchase);
        $em->flush();
        return $accountPurchase;

    }


}
