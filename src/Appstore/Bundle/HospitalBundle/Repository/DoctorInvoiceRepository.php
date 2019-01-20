<?php

namespace Appstore\Bundle\HospitalBundle\Repository;
use Appstore\Bundle\HospitalBundle\Entity\HospitalConfig;
use Appstore\Bundle\HospitalBundle\Entity\Invoice;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;


/**
 * PathologyRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DoctorInvoiceRepository extends EntityRepository
{


    /**
     * @param $qb
     * @param $data
     */

    protected function handleSearchBetween($qb,$data)
    {
        $invoice = isset($data['hmsInvoice'])? $data['hmsInvoice'] :'';
        $commission = isset($data['commission'])? $data['commission'] :'';
        $assignDoctor = isset($data['assignDoctor'])? $data['assignDoctor'] :'';
        $process = isset($data['process'])? $data['process'] :'';
        $startDate = isset($data['startDate'])? $data['startDate'] :'';
        $endDate = isset($data['endDate'])? $data['endDate'] :'';
        $transactionMethod = isset($data['transactionMethod'])? $data['transactionMethod'] :'';

        if (!empty($invoice)) {
            $qb->andWhere($qb->expr()->like("hmsInvoice.invoice", "'%$invoice%'"  ));
        }
        if(!empty($commission)){
            $qb->andWhere("e.hmsCommission = :commission");
            $qb->setParameter('commission', $commission);
        }
        if(!empty($assignDoctor)){
            $qb->andWhere("e.assignDoctor = :assignDoctor");
            $qb->setParameter('assignDoctor', $assignDoctor);
        }
        if(!empty($process)){
            $qb->andWhere("e.process = :process");
            $qb->setParameter('process', $process);
        }
        if(!empty($transactionMethod)){
            $qb->andWhere("e.transactionMethod = :transactionMethod");
            $qb->setParameter('transactionMethod', $transactionMethod);
        }
        if (!empty($startDate) ) {
            $datetime = new \DateTime($startDate);
            $startDate = $datetime->format('Y-m-d 00:00:00');
            $qb->andWhere("e.updated >= :startDate")->setParameter('startDate',$startDate);
        }
        if (!empty($endDate) ) {
            $datetime = new \DateTime($endDate);
            $endDate = $datetime->format('Y-m-d 23:59:59');
            $qb->andWhere("e.updated <= :endDate")->setParameter('endDate',$endDate);
        }
    }

    public function findWithList(User $user,$data)
    {
        $hospital = $user->getGlobalOption()->getHospitalConfig()->getId();

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.hmsInvoice','hmsInvoice');
        $qb->where('e.hospitalConfig = :hospital')->setParameter('hospital', $hospital) ;
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('e.updated','DESC');
        $qb->getQuery();
        return  $qb;
    }

    public function  commissionSummary(User $user,$data)
    {
        $hospital = $user->getGlobalOption()->getHospitalConfig()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.hmsInvoice','hmsInvoice');
        $qb->select('sum(e.payment) as subTotal');
        $qb->where('e.hospitalConfig = :hospital')->setParameter('hospital', $hospital) ;
        $qb->andWhere('e.process = :process')->setParameter('process', 'Paid') ;
        $this->handleSearchBetween($qb,$data);
        $receivable = $qb->getQuery()->getOneOrNullResult();
        $receivableTotal = !empty($receivable['subTotal']) ? $receivable['subTotal'] :0;
        return $receivableTotal;

    }

    public function  findWithOverview(User $user,$data, $mode = '' )
    {
        $hospital = $user->getGlobalOption()->getHospitalConfig()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.hmsInvoice','hmsInvoice');
        $qb->select('sum(e.payment) as subTotal');
        $qb->where('e.hospitalConfig = :hospital')->setParameter('hospital', $hospital) ;
        $qb->andWhere('e.process = :process')->setParameter('process', 'In-progress') ;
        $this->handleSearchBetween($qb,$data);
        $receivable = $qb->getQuery()->getOneOrNullResult();
        $receivableTotal = !empty($receivable['subTotal']) ? $receivable['subTotal'] :0;


        $qb = $this->createQueryBuilder('e');
        $qb->join('e.hmsInvoice','hmsInvoice');
        $qb->select('sum(e.payment) as subTotal');
        $qb->where('e.hospitalConfig = :hospital')->setParameter('hospital', $hospital) ;
        $qb->andWhere('e.process = :process')->setParameter('process', 'Paid') ;
        $this->handleSearchBetween($qb,$data);
        $payment = $qb->getQuery()->getOneOrNullResult();
        $paymentTotal = !empty($payment['subTotal']) ? $payment['subTotal'] :0;
        $due = $receivableTotal- $paymentTotal;
        $data = array( 'commission'=> $receivableTotal , 'payment'=> $paymentTotal , 'due'=> $due);
        return $data;
    }

    public function updateCommissionInvoice(Invoice $invoice)
    {
        $em = $this->_em;
        $total = $this->createQueryBuilder('e')
            ->select('sum(e.payment) as subTotal')
            ->where('e.hmsInvoice = :invoice')
            ->setParameter('invoice', $invoice ->getId())
            ->getQuery()->getOneOrNullResult();
        $subTotal = !empty($total['subTotal']) ? $total['subTotal'] :0;
        return $subTotal;
    }

    public function getInvoiceBaseCommission(HospitalConfig $config , $entities)
    {
        $ids = array();
        foreach ($entities->getQuery()->getArrayResult() as $row){
            $ids[] = $row['id'];
        }
        $em = $this->_em;
        $qb = $this->createQueryBuilder('e');
        $qb->select('i.id as invoiceId , particular.name,particular.id as commissionId');
        $qb->addSelect('SUM(e.payment) as payment');
        $qb->innerJoin('e.hmsInvoice','i');
        $qb->innerJoin('e.hmsCommission','particular');
        $qb->where('i.id IN (:invoices)');
        $qb->setParameter('invoices',$ids);
        $qb->groupBy('particular.id,i.id');
        $result = $qb->getQuery()->getArrayResult();
        $resDatas = array();
        foreach ($result as $row){
            $uniqueId = $row['invoiceId'].'-'.$row['commissionId'];
            $resDatas[$uniqueId]= $row;
        }
        return $resDatas;

    }

    public function getInvoiceBaseCommissionSummary(HospitalConfig $config , $entities)
    {
        $ids = array();
        foreach ($entities->getQuery()->getArrayResult() as $row){
            $ids[] = $row['id'];
        }
        $em = $this->_em;
        $qb = $this->createQueryBuilder('e');
        $qb->select('particular.id as commissionId');
        $qb->addSelect('SUM(e.payment) as payment');
        $qb->innerJoin('e.hmsInvoice','i');
        $qb->innerJoin('e.hmsCommission','particular');
        $qb->where('i.id IN (:invoices)');
        $qb->setParameter('invoices',$ids);
        $qb->groupBy('particular.name');
        $result = $qb->getQuery()->getArrayResult();
        return $result;

    }

}
