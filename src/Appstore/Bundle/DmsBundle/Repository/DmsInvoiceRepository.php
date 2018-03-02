<?php

namespace Appstore\Bundle\DmsBundle\Repository;
use Appstore\Bundle\DmsBundle\DmsBundle;
use Appstore\Bundle\DmsBundle\Entity\DmsConfig;
use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Appstore\Bundle\DmsBundle\Entity\DmsInvoice;
use Appstore\Bundle\DmsBundle\Entity\DmsParticular;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;


/**
 * DmsInvoiceRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DmsInvoiceRepository extends EntityRepository
{

    public function getLastInvoice(DmsConfig $config)
    {
        $entity = $this->findOneBy(
            array('dmsConfig' => $config),
            array('id' => 'DESC')
        );
        return $entity;
    }

    /**
     * @param $qb
     * @param $data
     */

    protected function handleSearchBetween($qb,$data)
    {

        $invoice = isset($data['invoice'])? $data['invoice'] :'';
        $assignDoctor = isset($data['doctor'])? $data['doctor'] :'';
        $process = isset($data['process'])? $data['process'] :'';
        $customerName = isset($data['name'])? $data['name'] :'';
        $customerMobile = isset($data['mobile'])? $data['mobile'] :'';
        $createdStart = isset($data['createdStart'])? $data['createdStart'] :'';
        $createdEnd = isset($data['createdEnd'])? $data['createdEnd'] :'';

        if (!empty($invoice)) {
            $qb->andWhere($qb->expr()->like("e.invoice", "'%$invoice%'"  ));
        }
        if (!empty($customerName)) {
            $qb->join('e.customer','c');
            $qb->andWhere($qb->expr()->like("c.name", "'$customerName%'"  ));
        }

        if (!empty($customerMobile)) {
            $qb->join('e.customer','m');
            $qb->andWhere($qb->expr()->like("m.mobile", "'%$customerMobile%'"  ));
        }
        if (!empty($createdStart)) {
            $compareTo = new \DateTime($createdStart);
            $created =  $compareTo->format('Y-m-d');
            $qb->andWhere("e.created >= :created");
            $qb->setParameter('created', $created);
        }

        if (!empty($createdEnd)) {
            $compareTo = new \DateTime($createdEnd);
            $createdEnd =  $compareTo->format('Y-m-d');
            $qb->andWhere("e.created <= :createdEnd");
            $qb->setParameter('createdEnd', $createdEnd);
        }

        if(!empty($assignDoctor)){
            $qb->andWhere("e.assignDoctor = :assignDoctor");
            $qb->setParameter('assignDoctor', $assignDoctor);
        }

        if(!empty($process)){
            $qb->andWhere("e.process = :process");
            $qb->setParameter('process', $process);
        }


    }

    public function handleDateRangeFind($qb,$data)
    {
        if(empty($data)){
            $datetime = new \DateTime("now");
            $data['startDate'] = $datetime->format('Y-m-d 00:00:00');
            $data['endDate'] = $datetime->format('Y-m-d 23:59:59');
        }else{
            $data['startDate'] = date('Y-m-d',strtotime($data['startDate']));
            $data['endDate'] = date('Y-m-d',strtotime($data['endDate']));
        }

        if (!empty($data['startDate']) ) {
            $qb->andWhere("it.created >= :startDate");
            $qb->setParameter('startDate', $data['startDate'].' 00:00:00');
        }

        if (!empty($data['endDate'])) {
            $qb->andWhere("it.created <= :endDate");
            $qb->setParameter('endDate', $data['endDate'].' 23:59:59');
        }
    }


    public function findWithOverview(User $user , $data , $mode='')
    {

        $config = $user->getGlobalOption()->getDmsConfig()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->leftJoin('e.invoiceTransactions','it');
        $qb->select('sum(e.subTotal) as subTotal ,sum(e.discount) as discount ,sum(it.total) as netTotal , sum(it.payment) as netPayment , sum(e.due) as netDue , sum(e.commission) as netCommission');
        $qb->where('e.dmsConfig = :config')->setParameter('config', $config);
        if (!empty($mode)){
            $qb->andWhere('e.invoiceMode = :mode')->setParameter('mode', $mode);
        }
       // $this->handleSearchBetween($qb,$data);
        $this->handleDateRangeFind($qb,$data);
        $qb->andWhere("e.process IN (:process)");
        $qb->setParameter('process', array('Done','Paid','In-progress','Diagnostic','Admitted','Release','Released','Death','Dead'));
        $result = $qb->getQuery()->getOneOrNullResult();

        $subTotal = !empty($result['subTotal']) ? $result['subTotal'] :0;
        $netTotal = !empty($result['netTotal']) ? $result['netTotal'] :0;
        $netPayment = !empty($result['netPayment']) ? $result['netPayment'] :0;
        $netDue = !empty($result['netDue']) ? $result['netDue'] :0;
        $discount = !empty($result['discount']) ? $result['discount'] :0;
        $vat = !empty($result['vat']) ? $result['vat'] :0;
        $netCommission = !empty($result['netCommission']) ? $result['netCommission'] :0;
        $data = array('subTotal'=> $subTotal ,'discount'=> $discount ,'vat'=> $vat ,'netTotal'=> $netTotal , 'netPayment'=> $netPayment , 'netDue'=> $netDue , 'netCommission'=> $netCommission);

        return $data;
    }

    public function findWithSalesOverview(User $user , $data , $mode='')
    {
        $config = $user->getGlobalOption()->getDmsConfig()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->leftJoin('e.invoiceTransactions','it');
        $qb->select('sum(e.subTotal) as subTotal ,sum(e.discount) as discount ,sum(e.total) as netTotal , sum(e.payment) as netPayment , sum(e.due) as netDue , sum(e.commission) as netCommission');
        $qb->where('e.dmsConfig = :config')->setParameter('config', $config);
        if (!empty($mode)){
            $qb->andWhere('e.invoiceMode = :mode')->setParameter('mode', $mode);
        }
        $this->handleDateRangeFind($qb,$data);
        $qb->andWhere("e.process IN (:process)");
        $qb->setParameter('process', array('Done','Paid','In-progress','Diagnostic','Admitted'));
        $result = $qb->getQuery()->getOneOrNullResult();
        $subTotal = !empty($result['subTotal']) ? $result['subTotal'] :0;
        $netTotal = !empty($result['netTotal']) ? $result['netTotal'] :0;
        $netPayment = !empty($result['netPayment']) ? $result['netPayment'] :0;
        $netDue = !empty($result['netDue']) ? $result['netDue'] :0;
        $discount = !empty($result['discount']) ? $result['discount'] :0;
        $vat = !empty($result['vat']) ? $result['vat'] :0;
        $netCommission = !empty($result['netCommission']) ? $result['netCommission'] :0;
        $data = array('subTotal'=> $subTotal ,'discount'=> $discount ,'vat'=> $vat ,'netTotal'=> $netTotal , 'netPayment'=> $netPayment , 'netDue'=> $netDue , 'netCommission'=> $netCommission);

        return $data;
    }

    public function findWithServiceOverview(User $user, $data)
    {
        $config = $user->getGlobalOption()->getDmsConfig()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->leftJoin('e.invoiceTransactions','it');
        $qb->leftJoin('e.invoiceParticulars','ip');
        $qb->leftJoin('ip.particular','p');
        $qb->leftJoin('p.service','s');
        $qb->select('sum(ip.subTotal) as subTotal');
        $qb->addSelect('s.name as serviceName');
        $qb->where('e.dmsConfig = :config')->setParameter('config', $config);
        if (!empty($mode)){
            $qb->andWhere('e.invoiceMode = :mode')->setParameter('mode', $mode);
        }
        $qb->andWhere("e.process IN (:process)");
        $qb->setParameter('process', array('Done','Paid','In-progress','Diagnostic','Admitted','Release','Death','Released','Dead'));
        $this->handleDateRangeFind($qb,$data);
        $qb->groupBy('s.id');
        $result = $qb->getQuery()->getArrayResult();
        return $result;
    }

    public function findWithTransactionOverview(User $user, $data)
    {
        $config = $user->getGlobalOption()->getDmsConfig()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->leftJoin('e.invoiceTransactions','it');
        $qb->leftJoin('ip.transactionMethod','p');
        $qb->select('sum(ip.payment) as paymentTotal');
        $qb->addSelect('p.name as transName');
        $qb->where('e.dmsConfig = :config')->setParameter('config', $config);
        if (!empty($mode)){
            $qb->andWhere('e.invoiceMode = :mode')->setParameter('mode', $mode);
        }
        $qb->andWhere("e.process IN (:process)");
        $qb->setParameter('process', array('Done','Paid','In-progress','Diagnostic','Admitted'));
        $this->handleDateRangeFind($qb,$data);
        $qb->groupBy('p.id');
        $result = $qb->getQuery()->getArrayResult();
        return $result;
    }

    public function findWithCommissionOverview(User $user, $data)
    {
        if(empty($data)){
            $datetime = new \DateTime("now");
            $data['startDate'] = $datetime->format('Y-m-d 00:00:00');
            $data['endDate'] = $datetime->format('Y-m-d 23:59:59');
        }else{
            $data['startDate'] = date('Y-m-d',strtotime($data['startDate']));
            $data['endDate'] = date('Y-m-d',strtotime($data['endDate']));
        }


        $config = $user->getGlobalOption()->getDmsConfig()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->leftJoin('e.doctorInvoices','ip');
        $qb->leftJoin('ip.assignDoctor','d');
        $qb->select('sum(ip.payment) as paymentTotal');
        $qb->addSelect('d.name as referredName');
        $qb->where('e.dmsConfig = :config')->setParameter('config', $config);
        $qb->andWhere('ip.process = :mode')->setParameter('mode', 'Paid');
        if (!empty($data['startDate']) ) {
            $qb->andWhere("ip.updated >= :startDate");
            $qb->setParameter('startDate', $data['startDate'].' 00:00:00');
        }

        if (!empty($data['endDate'])) {
            $qb->andWhere("ip.updated <= :endDate");
            $qb->setParameter('endDate', $data['endDate'].' 23:59:59');
        }

        $qb->groupBy('ip.assignDoctor');
        $result = $qb->getQuery()->getArrayResult();
        return $result;
    }


    public function invoiceLists(User $user, $data)
    {
        $config = $user->getGlobalOption()->getDmsConfig()->getId();

        $qb = $this->createQueryBuilder('e');
        $qb->where('e.dmsConfig = :config')->setParameter('config', $config) ;
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('e.created','DESC');
        $qb->getQuery();
        return  $qb;
    }

    public function invoicePathologicalReportLists(User $user , $mode , $data)
    {
        $config = $user->getGlobalOption()->getDmsConfig()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->where('e.dmsConfig = :config')->setParameter('config', $config) ;
        $this->handleSearchBetween($qb,$data);
        $qb->andWhere("e.process IN (:process)");
        $qb->setParameter('process', array('Done','Paid','In-progress','Diagnostic','Admitted'));
        $qb->orderBy('e.created','DESC');
        $qb->getQuery();
        return  $qb;
    }


    public function doctorInvoiceLists(User $user,$data)
    {
        $config = $user->getGlobalOption()->getDmsConfig()->getId();

        $qb = $this->createQueryBuilder('e');
        $qb->where('e.dmsConfig = :config')->setParameter('config', $config) ;
        $qb->andWhere('e.paymentStatus != :status')->setParameter('status', 'pending') ;
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('e.updated','DESC');
        $qb->getQuery();
        return  $qb;
    }

    public function updateInvoiceTotalPrice(DmsInvoice $invoice)
    {
        $em = $this->_em;
        $total = $em->createQueryBuilder()
            ->from('DmsBundle:DmsTreatmentPlan','si')
            ->select('sum(si.price) as estimateTotal')
            ->where('si.dmsInvoice = :invoice')
            ->setParameter('invoice', $invoice ->getId())
            ->getQuery()->getOneOrNullResult();

        $estimateTotal = !empty($total['estimateTotal']) ? $total['estimateTotal'] :0;
        if($estimateTotal > 0){
            $invoice->setEstimateTotal($estimateTotal);
            $this->updatePaymentReceive($invoice);
        }else{
            $invoice->setSubTotal(0);
            $invoice->setTotal(0);
            $invoice->setDue(0);
            $invoice->setDiscount(0);
            $invoice->setVat(0);
        }

        $em->persist($invoice);
        $em->flush();

        return $invoice;

    }

    public function updatePaymentReceive(DmsInvoice $invoice)
    {
        $em = $this->_em;
        $res = $em->createQueryBuilder()
            ->from('DmsBundle:DmsTreatmentPlan','si')
            ->select('sum(si.price) as subTotal ,sum(si.payment) as payment ,sum(si.discount) as discount')
            ->where('si.dmsInvoice = :invoice')
            ->setParameter('invoice', $invoice ->getId())
            ->andWhere('si.status = :status')
            ->setParameter('status', 1)
            ->getQuery()->getOneOrNullResult();
        $subTotal = !empty($res['subTotal']) ? $res['subTotal'] :0;
        $payment = !empty($res['payment']) ? $res['payment'] :0;
        $discount = !empty($res['discount']) ? $res['discount'] :0;
        $invoice->setSubTotal($subTotal);
        $invoice->setPayment($payment);
        $invoice->setDiscount($discount);
        $invoice->setTotal($invoice->getSubTotal() - $discount);
        $invoice->setDue($invoice->getTotal() - $invoice->getPayment());
        $em->flush();

    }

    public function updateCommissionPayment(DmsInvoice $invoice)
    {
        $em = $this->_em;
        $res = $em->createQueryBuilder()
            ->from('DmsBundle:DmsDoctorInvoice','si')
            ->select('sum(si.payment) as payment')
            ->where('si.dmsInvoice = :invoice')
            ->setParameter('invoice', $invoice ->getId())
            ->andWhere('si.process = :process')
            ->setParameter('process', 'Paid')
            ->getQuery()->getOneOrNullResult();
        $payment = !empty($res['payment']) ? $res['payment'] :0;
        $invoice->setCommission($payment);
        $em->persist($invoice);
        $em->flush();

    }


    public function getCulculationVat(DmsInvoice $sales,$totalAmount)
    {
        $vat = ( ($totalAmount * (int)$sales->getDmsConfig()->getVatPercentage())/100 );
        return round($vat);
    }

    public function getInvoiceDetails(DmsInvoice $invoice){

        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from('DmsBundle:InvoiceParticular','ip');
        $qb->innerJoin('ip.particular','particular');
        $qb->where('si.dmsInvoice = :invoice');
        $qb->setParameter('invoice', $invoice ->getId());
        $qb->groupBy('particular.service');

    }

    public function updatePatientInfo($invoice,Customer $patient)
    {
        $em = $this->_em;
        $invoice = $this->_em->getRepository('DmsBundle:DmsInvoice')->find($invoice);
        $invoice->setCustomer($patient);
        $invoice->setMobile($patient->getMobile());
        $em->persist($invoice);
        $em->flush($invoice);

    }

    public function patientAdmissionUpdate($data,DmsInvoice $entity)
    {
        $em = $this->_em;
        $invoiceInfo = $data['appstore_bundle_configbundle_invoice'];
        if($invoiceInfo['cabin']){
            $cabin = $em->getRepository('DmsBundle:Particular')->find($invoiceInfo['cabin']);
            $entity->setCabin($cabin);
        }
        if($invoiceInfo['assignDoctor']){
            $assignDoctor = $em->getRepository('DmsBundle:Particular')->find($invoiceInfo['assignDoctor']);
            $entity->setAssignDoctor($assignDoctor);
        }
        if($invoiceInfo['department']){
            $department = $em->getRepository('DmsBundle:HmsCategory')->find($invoiceInfo['department']);
            $entity->setDepartment($department);
        }
        if($invoiceInfo['cabinNo']){
            $entity->setCabinNo($invoiceInfo['cabinNo']);
        }
        if($invoiceInfo['disease']){
            $entity->setDisease($invoiceInfo['disease']);
        }
        $em->persist($entity);
        $em->flush($entity);

    }

    public function checkCabinBooking($invoice , $cabin)
    {
        $invoice = $this->_em->getRepository('DmsBundle:Invoice')->find($invoice);
        $cabin = $this->_em->getRepository('DmsBundle:Particular')->find($cabin);
        $qb = $this->createQueryBuilder('e');
        $qb->select('COUNT(e.cabin) AS cabinCount');
        $qb->where('e.dmsConfig = :config')->setParameter('config', $invoice ->getDmsConfig()->getId());
        $qb->andWhere('e.cabin = :cabin')->setParameter('cabin', $cabin ->getId());
        $qb->andWhere('e.process = :process')->setParameter('process', 'Admitted');
        $res = $qb->getQuery()->getOneOrNullResult();
        if(!empty($res) and $res['cabinCount'] > 0 ){
            echo 'invalid';
        }else{
            echo 'valid';
        }
        exit;

    }


}
