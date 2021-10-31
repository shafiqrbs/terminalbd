<?php
namespace Appstore\Bundle\AccountingBundle\Repository;
use Appstore\Bundle\AccountingBundle\Entity\AccountLoan;
use Appstore\Bundle\AccountingBundle\Entity\AccountVendor;
use Appstore\Bundle\MedicineBundle\Entity\MedicineVendor;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * VendorRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AccountLoanRepository extends EntityRepository
{

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
            $employee =    isset($data['employee'])? $data['employee'] :'';
            $process =    isset($data['process'])? $data['process'] :'';
            if (!empty($employee)) {
                $qb->andWhere("e.employee = :employee")->setParameter('employee', $employee);
            }
            if (!empty($process)) {
                $qb->andWhere("e.process = :process")->setParameter('process', $process);
            }
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
        }

    }

    public function findWithSearch(GlobalOption $globalOption, $data)
    {

        $qb = $this->createQueryBuilder('e');
        $qb->where('e.globalOption = :config')->setParameter('config', $globalOption->getId()) ;
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('e.updated','DESC');
        return  $qb;
    }

    public function getLastBalance(GlobalOption $global)
    {
        $em = $this->_em;
        $qb = $this->createQueryBuilder('e');
        $qb->select('(COALESCE(SUM(e.amount),0)) AS balance');
        $qb->where("e.globalOption = :globalOption");
        $qb->setParameter('globalOption', $global->getId());
        $qb->andWhere("e.process = 'approved'");
        $result = $qb->getQuery()->getSingleResult();
        $balance = $result['balance'];
        return $balance;

    }


    public function dailyLoan($user,$data)
    {
        $globalOption = $user->getGlobalOption()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->select('p.name as name','SUM(e.debit) as debit','SUM(e.credit) as credit');
        $qb->join('e.employee','u');
        $qb->join('u.profile','p');
        $qb->where("e.globalOption = :globalOption")->setParameter('globalOption', $globalOption);
        $qb->andWhere("e.process = 'approved'");
        $this->handleSearchBetween($qb,$data);
        $qb->groupBy('p.name');
        $qb->orderBy('p.name','ASC');
        $result = $qb->getQuery()->getArrayResult();
        return $result;
    }

    public function outstandingLoan($option)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('p.name as name','p.mobile as mobile','SUM(e.debit) as debit','SUM(e.credit) as credit','(SUM(e.credit) + SUM(e.debit)) as balance');
        $qb->join('e.employee','u');
        $qb->join('u.profile','p');
        $qb->where("e.globalOption = :globalOption")->setParameter('globalOption', $option);
        $qb->andWhere("e.process = 'approved'");
        $qb->groupBy('p.name');
        $qb->orderBy('p.name','ASC');
        $result = $qb->getQuery()->getArrayResult();
        return $result;
    }



}
