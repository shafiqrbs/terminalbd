<?php

namespace Appstore\Bundle\AccountingBundle\Repository;
use Appstore\Bundle\AccountingBundle\Entity\Expenditure;
use Appstore\Bundle\AccountingBundle\Entity\PaymentSalary;
use Appstore\Bundle\HospitalBundle\Entity\DoctorInvoice;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * ExpenditureRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ExpenditureRepository extends EntityRepository
{

    /**
     * @param $qb
     * @param $data
     */

    protected function handleSearchBetween($qb,$data)
    {

        $startDate      = isset($data['startDate']) and $data['startDate'] != '' ? $data['startDate']:'';
        $endDate        = isset($data['endDate']) and $data['endDate'] != '' ? $data['endDate']:'';
        $toUser         = isset($data['toUser'])? $data['toUser'] :'';
        $accountHead    = isset($data['accountHead'])? $data['accountHead'] :'';
        $transactionMethod    = isset($data['transactionMethod'])? $data['transactionMethod'] :'';
        $category       = isset($data['category'])? $data['category'] :'';
        if(!empty($startDate) and !empty($endDate)){
           $start = new \DateTime($data['startDate']);
           $startDate = $start->format('Y-m-d 00:00:00');
           $end = new \DateTime($data['endDate']);
           $endDate = $end->format('Y-m-d 23:59:59');
           $qb->andWhere("e.updated >= :startDate");
           $qb->setParameter('startDate', $startDate);
           $qb->andWhere("e.updated <= :endDate");
           $qb->setParameter('endDate', $endDate);
        }

        if (!empty($toUser)) {
            $qb->join("e.toUser",'u');
	        $qb->andWhere("u.username = :username");
	        $qb->setParameter('username', $toUser);
        }

        if (!empty($accountHead)) {
            $qb->andWhere("e.accountHead = :accountHead");
            $qb->setParameter('accountHead', $accountHead);
        }
        if (!empty($transactionMethod)) {
            $qb->andWhere("e.transactionMethod = :transactionMethod");
            $qb->setParameter('transactionMethod', $transactionMethod);
        }
        if (!empty($category)) {
            $qb->andWhere("e.expenseCategory = :category");
            $qb->setParameter('category', $category);
        }
    }

    public function handleDateRangeFind($qb,$data)
    {
        if(empty($data)){
            $datetime = new \DateTime("now");
            $startDate = $datetime->format('Y-m-d 00:00:00');
            $endDate = $datetime->format('Y-m-d 23:59:59');
        }elseif(!empty($data['startDate']) and !empty($data['endDate'])){
            $start = new \DateTime($data['startDate']);
            $startDate = $start->format('Y-m-d 00:00:00');
            $end = new \DateTime($data['endDate']);
            $endDate = $end->format('Y-m-d 23:59:59');
        }
        if (!empty($startDate) ) {
            $qb->andWhere("e.updated >= :startDate");
            $qb->setParameter('startDate', $startDate);
        }
        if (!empty($endDate)) {
            $qb->andWhere("e.updated <= :endDate");
            $qb->setParameter('endDate', $endDate);
        }
    }


    public function expenditureOverview(User $user , $data)
    {
        $globalOption = $user->getGlobalOption();
        $branch = $user->getProfile()->getBranches();

        $qb = $this->_em->createQueryBuilder();
        $qb->from('AccountingBundle:Expenditure','e');
        $qb->select('sum(e.amount) as amount');
        $qb->where('e.process = :process');
        $qb->setParameter('process', 'approved');
        $qb->andWhere('e.globalOption = :globalOption');
        $qb->setParameter('globalOption', $globalOption);
        if (!empty($branch)){
            $qb->andWhere("e.branches = :branch");
            $qb->setParameter('branch', $branch);
        }
        $this->handleSearchBetween($qb,$data);
        $amount = $qb->getQuery()->getOneOrNullResult();
        return  $amount['amount'] ;

    }

    public function findWithSearch(User $user , $data)
    {
        $globalOption = $user->getGlobalOption();
        $branch = $user->getProfile()->getBranches();

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.createdBy','cu');
        $qb->join('e.toUser','tu');
        $qb->join('tu.profile','profile');
        $qb->leftJoin('e.expenseCategory','c');
        $qb->leftJoin('e.transactionMethod','t');
        $qb->leftJoin('e.accountMobileBank','amb');
        $qb->leftJoin('e.accountBank','ab');
        $qb->select('e.id as id','e.created as created','e.amount as amount','e.accountRefNo as accountRefNo','e.path as path','e.remark as remark','e.process as process');
        $qb->addSelect('cu.username as createdBy');
        $qb->addSelect('profile.name as toUser');
        $qb->addSelect('t.name as methodName');
        $qb->addSelect('c.name as categoryName');
        $qb->addSelect('amb.name as mobileBankName');
        $qb->addSelect('amb.mobile as mobileNo');
        $qb->addSelect('ab.name as bankName');
        $qb->addSelect('ab.accountNo as accountNo');
        $qb->where("e.globalOption = :globalOption");
        $qb->setParameter('globalOption', $globalOption);
        if (!empty($branch)){
            $qb->andWhere("e.branches = :branch");
            $qb->setParameter('branch', $branch);
        }
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('e.created','DESC');
        $result = $qb->getQuery();
        return $result;

    }

    public function lastInsertExpenditure(Expenditure $entity)
    {

        $em = $this->_em;
        $entity = $em->getRepository('AccountingBundle:Expenditure')->findOneBy(
            array('globalOption' => $entity->getGlobalOption(),'expenseCategory' => $entity->getExpenseCategory(),'process'=>'approved'),
            array('id' => 'DESC')
        );
        if (empty($entity)) {
            return 0;
        }
        return $entity->getBalance();

    }

    public function insertCommissionPayment(DoctorInvoice $doctorInvoice)
    {
        $em = $this->_em;
        $entity = new Expenditure();
        $entity->setGlobalOption($doctorInvoice->getHospitalConfig()->getGlobalOption());
        $entity->setDoctorInvoice($doctorInvoice);
        $entity->setAmount($doctorInvoice->getPayment());
        $entity->setCreatedBy($doctorInvoice->getCreatedBy());
        $entity->setApprovedBy($doctorInvoice->getApprovedBy());
        $entity->setAccountHead($this->_em->getRepository('AccountingBundle:AccountHead')->find(52));
        $entity->setTransactionMethod($doctorInvoice->getTransactionMethod());
        $entity->setAccountMobileBank($doctorInvoice->getAccountMobileBank());
        $entity->setAccountBank($doctorInvoice->getAccountBank());
        $commission = "From Commission. Invoice No.-{$doctorInvoice->getHmsDoctorInvoice()} Name: {$doctorInvoice->getAssignDoctor()->getName()} and  type of commission: {$doctorInvoice->getHmsCommission()->getName()}";
        $entity->setRemark($commission);
        $entity->setProcess('approved');
        $em->persist($entity);
        $em->flush();
        $em->getRepository('AccountingBundle:AccountCash')->insertExpenditureCash($entity);
        $em->getRepository('AccountingBundle:Transaction')->insertExpenditureTransaction($entity);

    }

    public function reportForExpenditure(GlobalOption $option,$data)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.expenseCategory','ec');
        $qb->select('SUM(e.amount) as amount');
        $qb->addSelect('ec.name as categoryName');
        $qb->where('e.globalOption =:option')->setParameter('option', $option);
        $qb->andWhere('e.process =:process')->setParameter('process', 'approved');
        $qb->groupBy('ec.name');
        $this->handleDateRangeFind($qb,$data);
        $result = $qb->getQuery()->getArrayResult();
        return $result;
    }

    public function monthlyExpenditure(User $user , $data =array())
    {
        $config = $user->getGlobalOption()->getId();
        $compare = new \DateTime();
        $month =  $compare->format('F');
        $year =  $compare->format('Y');
        $month = isset($data['month'])? $data['month'] :$month;
        $year = isset($data['year'])? $data['year'] :$year;
        $sql = "SELECT DATE_FORMAT(transaction.updated,'%d-%m-%Y') as date,SUM(transaction.amount) as payment
                FROM Expenditure as transaction
                WHERE transaction.globalOption_id = :option AND transaction.process = :process AND MONTHNAME(transaction.updated) =:month AND YEAR(transaction.updated) =:year
                GROUP BY date";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('option', $config);
        $stmt->bindValue('process', 'Approved');
        $stmt->bindValue('month', $month);
        $stmt->bindValue('year', $year);
        $stmt->execute();
        $results =  $stmt->fetchAll();
        $arrays = array();
        foreach ($results as $result){
            $arrays[$result['date']] = $result;
        }
        return $arrays;
    }

    public function yearlyExpenditure(User $user , $data =array())
    {
        $config = $user->getGlobalOption()->getId();
        $compare = new \DateTime();
        $month =  $compare->format('F');
        $year =  $compare->format('Y');
        $month = isset($data['month'])? $data['month'] :$month;
        $year = isset($data['year'])? $data['year'] :$year;
        $sql = "SELECT DATE_FORMAT(transaction.updated,'%M') as month,SUM(transaction.amount) as payment
                FROM Expenditure as transaction
                WHERE transaction.globalOption_id = :option AND transaction.process = :process AND  YEAR(transaction.updated) =:year
                GROUP BY month";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('option', $config);
        $stmt->bindValue('process', 'Approved');
        $stmt->bindValue('year', $year);
        $stmt->execute();
        $results =  $stmt->fetchAll();
        $arrays = array();
        foreach ($results as $result){
            $arrays[$result['month']] = $result;
        }
        return $arrays;
    }

    public function accountReverse(Expenditure $entity)
    {
        $em = $this->_em;
        $transaction = $em->createQuery("DELETE AccountingBundle:Transaction e WHERE e.globalOption = ".$entity->getGlobalOption()->getId() ." AND e.accountRefNo =".$entity->getAccountRefNo()." AND e.processHead = 'Expenditure'");
        $transaction->execute();
        $accountCash = $em->createQuery("DELETE AccountingBundle:AccountCash e WHERE e.globalOption = ".$entity->getGlobalOption()->getId() ." AND e.expenditure =".$entity->getId()." AND e.processHead = 'Expenditure'");
        $accountCash->execute();
    }


}
