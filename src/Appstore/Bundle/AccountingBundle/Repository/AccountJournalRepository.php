<?php

namespace Appstore\Bundle\AccountingBundle\Repository;
use Doctrine\ORM\EntityRepository;

/**
 * AccountJournalRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AccountJournalRepository extends EntityRepository
{
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
            $qb->andWhere("s.toUser = :toUser");
            $qb->setParameter('toUser', $toUser);
        }
        if (!empty($accountHead)) {
            $qb->andWhere("s.accountHead = :accountHead");
            $qb->setParameter('accountHead', $accountHead);
        }

        $amount = $qb->getQuery()->getSingleScalarResult();
        return  $amount ;

    }
}
