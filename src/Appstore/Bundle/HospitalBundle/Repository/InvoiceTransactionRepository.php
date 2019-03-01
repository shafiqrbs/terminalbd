<?php

namespace Appstore\Bundle\HospitalBundle\Repository;
use Appstore\Bundle\AccountingBundle\Entity\AccountSales;
use Appstore\Bundle\HospitalBundle\Entity\HospitalConfig;
use Appstore\Bundle\HospitalBundle\Entity\Invoice;
use Appstore\Bundle\HospitalBundle\Entity\InvoiceTransaction;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;


/**
 * InvoiceTransactionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class InvoiceTransactionRepository extends EntityRepository
{

    public function todaySalesOverview(User $user , $data , $previous ='', $modes = array())
    {

        if (empty($data)) {
            $datetime = new \DateTime("now");
            $data['startDate'] = $datetime->format('Y-m-d');
            $data['endDate'] = $datetime->format('Y-m-d');
        }

        $hospital = $user->getGlobalOption()->getHospitalConfig()->getId();
        $qb = $this->createQueryBuilder('it');
        $qb->join('it.hmsInvoice', 'e');
        $qb->select('sum(it.total) as total ,sum(it.discount) as discount , sum(it.payment) as payment');
        $qb->where('e.hospitalConfig = :hospital')->setParameter('hospital', $hospital);

        if ($previous == 'true'){

            if (!empty($data['startDate'])) {
                $compareTo = new \DateTime($data['startDate']);
                $startDate =  $compareTo->format('Y-m-d 00:00:00');
                $qb->andWhere("e.created <:startDate");
                $qb->setParameter('startDate', $startDate);
                $qb->andWhere("it.updated >= :startDate");
                $qb->setParameter('startDate', $startDate);


            }
            if (!empty($data['endDate'])) {

                $compareTo = new \DateTime($data['endDate']);
                $endDate =  $compareTo->format('Y-m-d 23:59:59');
                $qb->andWhere("it.updated <= :endDate");
                $qb->setParameter('endDate', $endDate);
            }

        }elseif ($previous == 'false'){

            if (!empty($data['startDate'])) {
                $compareTo = new \DateTime($data['startDate']);
                $startDate =  $compareTo->format('Y-m-d 00:00:00');
                $qb->andWhere("e.created >= :startDate");
                $qb->setParameter('startDate', $startDate);
                $qb->andWhere("it.updated >= :startDate");
                $qb->setParameter('startDate', $startDate);
            }
            if (!empty($data['endDate'])) {
                $compareTo = new \DateTime($data['endDate']);
                $endDate =  $compareTo->format('Y-m-d 23:59:59');
                $qb->andWhere("e.created <= :endDate");
                $qb->setParameter('endDate', $startDate);
                $qb->andWhere("it.updated <= :endDate");
                $qb->setParameter('endDate', $endDate);
            }
        }

        if (!empty($mode)){
            $qb->andWhere('e.invoiceMode IN (:modes)')->setParameter('modes', $modes);
        }
        $qb->andWhere('it.process = :process')->setParameter('process', 'Done');
        $result = $qb->getQuery()->getOneOrNullResult();
        $total = !empty($result['total']) ? $result['total'] :0;
        $discount = !empty($result['discount']) ? $result['discount'] :0;
        $receive = !empty($result['payment']) ? $result['payment'] :0;
        $data = array('total'=> $total,'discount'=> $discount ,'receive'=> $receive);
        return $data;
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
            $qb->andWhere("ip.updated >= :startDate");
            $qb->setParameter('startDate', $data['startDate'].' 00:00:00');
        }
        if (!empty($data['endDate'])) {
            $qb->andWhere("ip.updated <= :endDate");
            $qb->setParameter('endDate', $data['endDate'].' 23:59:59');
        }
    }

    public function findWithTransactionOverview(User $user, $data)
    {
        $hospital = $user->getGlobalOption()->getHospitalConfig()->getId();
        $qb = $this->createQueryBuilder('ip');
        $qb->join('ip.hmsInvoice','e');
        $qb->leftJoin('ip.transactionMethod','p');
        $qb->select('sum(ip.payment) as paymentTotal');
        $qb->addSelect('p.name as transName');
        $qb->where('e.hospitalConfig = :hospital')->setParameter('hospital', $hospital);
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



    public function initialInsertInvoiceTransaction(Invoice $invoice){

        $code = $this->getLastCode($invoice);
        $entity = New InvoiceTransaction();
        $entity->setHmsInvoice($invoice);
        $entity->setCode($code + 1);
        $transactionCode = sprintf("%s", str_pad($entity->getCode(),2, '0', STR_PAD_LEFT));
        $entity->setProcess('Done');
        $entity->setTransactionCode($transactionCode);
        $entity->setDiscount($invoice->getDiscount());
        $entity->setTotal($invoice->getSubTotal());
        $entity->setPayment($invoice->getPayment());
        $this->_em->persist($entity);
        $this->_em->flush($entity);
        return $entity;

    }

    public function admissionPaymentTransaction(Invoice $invoice,$data){

        $code = $this->getLastCode($invoice);
        $entity = New InvoiceTransaction();
        $entity->setHmsInvoice($invoice);
        $entity->setCode($code + 1);
        $transactionCode = sprintf("%s", str_pad($entity->getCode(),2, '0', STR_PAD_LEFT));
        $entity->setTransactionCode($transactionCode);
        $entity->setDiscount($data['discount']);
        $entity->setPayment($data['payment']);
        $entity->setProcess('In-progress');
        $transactionMethod = $this->_em->getRepository('SettingToolBundle:TransactionMethod')->find(1);
        $entity->setTransactionMethod($transactionMethod);
        $this->_em->persist($entity);
        $this->_em->flush($entity);
        return $entity;

    }



    public function insertAdmissionTransaction(InvoiceTransaction $entity, $data)
    {

        $process = $data['process'];
        $invoice = $entity->getHmsInvoice();
        $entity->setProcess($process);
        $entity->setPayment($data['discount']);
        $entity->setPayment($data['payment']);
        $entity->setTransactionMethod($invoice->getTransactionMethod());
        $entity->setAccountBank($invoice->getAccountBank());
        $entity->setPaymentCard($invoice->getPaymentCard());
        $entity->setCardNo($invoice->getCardNo());
        $entity->setBank($invoice->getBank());
        $entity->setAccountMobileBank($invoice->getAccountMobileBank());
        $entity->setPaymentMobile($invoice->getPaymentMobile());
        $entity->setTransactionId($invoice->getTransactionId());
        $entity->setComment($invoice->getComment());
        if ($invoice->getHospitalConfig()->getVatEnable() == 1 && $invoice->getHospitalConfig()->getVatPercentage() > 0) {
            $vat = $this->getCulculationVat($invoice, $entity->getPayment());
            $entity->setVat($vat);
        }
        $this->_em->persist($entity);
        $this->_em->flush($entity);

    }
    public function insertTransaction(Invoice $invoice)
    {
            $entity = New InvoiceTransaction();
            $code = $this->getLastCode($invoice);
            $entity->setHmsInvoice($invoice);
            $entity->setCode($code + 1);
            $transactionCode = sprintf("%s", str_pad($entity->getCode(),2, '0', STR_PAD_LEFT));
            $entity->setTransactionCode($transactionCode);
            $entity->setProcess('Done');
            $entity->setDiscount($invoice->getDiscount());
            $entity->setPayment($invoice->getPayment());
            $entity->setTotal($invoice->getTotal());
            $entity->setTransactionMethod($invoice->getTransactionMethod());
            $entity->setAccountBank($invoice->getAccountBank());
            $entity->setPaymentCard($invoice->getPaymentCard());
            $entity->setCardNo($invoice->getCardNo());
            $entity->setBank($invoice->getBank());
            $entity->setAccountMobileBank($invoice->getAccountMobileBank());
            $entity->setPaymentMobile($invoice->getPaymentMobile());
            $entity->setTransactionId($invoice->getTransactionId());
            $entity->setComment($invoice->getComment());
            $entity->setIsMaster(true);
            if ($invoice->getHospitalConfig()->getVatEnable() == 1 && $invoice->getHospitalConfig()->getVatPercentage() > 0) {
                $vat = $this->getCulculationVat($invoice, $entity->getPayment());
                $entity->setVat($vat);
            }
            $this->_em->persist($entity);
            $this->_em->flush($entity);
            if($invoice->getPayment() > 0){
                $accountInvoice = $this->_em->getRepository('AccountingBundle:AccountSales')->insertHospitalAccountInvoice($entity);
                $this->_em->getRepository('AccountingBundle:Transaction')->hmsSalesTransaction($entity, $accountInvoice);
            }
    }
    public function insertPaymentTransaction(Invoice $invoice, $data)
    {
        $entity = New InvoiceTransaction();
        $code = $this->getLastCode($invoice);
        $entity->setHmsInvoice($invoice);
        $entity->setCode($code + 1);
        $transactionCode = sprintf("%s", str_pad($entity->getCode(),2, '0', STR_PAD_LEFT));
        $entity->setTransactionCode($transactionCode);
        $entity->setProcess($data['process']);
        $entity->setDiscount($data['discount']);
        $entity->setPayment($data['payment']);
        $entity->setTransactionMethod($invoice->getTransactionMethod());
        $entity->setAccountBank($invoice->getAccountBank());
        $entity->setPaymentCard($invoice->getPaymentCard());
        $entity->setCardNo($invoice->getCardNo());
        $entity->setBank($invoice->getBank());
        $entity->setAccountMobileBank($invoice->getAccountMobileBank());
        $entity->setPaymentMobile($invoice->getPaymentMobile());
        $entity->setTransactionId($invoice->getTransactionId());
        $entity->setComment($invoice->getComment());
        if ($invoice->getHospitalConfig()->getVatEnable() == 1 && $invoice->getHospitalConfig()->getVatPercentage() > 0) {
            $vat = $this->getCulculationVat($invoice, $entity->getPayment());
            $entity->setVat($vat);
        }
        $this->_em->persist($entity);
        $this->_em->flush($entity);

    }

    public function admissionInvoiceTransactionUpdate(InvoiceTransaction $entity )
    {
        $invoice = $entity->getHmsInvoice();
        if ($invoice->getHospitalConfig()->getVatEnable() == 1 && $invoice->getHospitalConfig()->getVatPercentage() > 0) {
            $vat = $this->getCulculationVat($invoice, $entity->getPayment());
            $entity->setVat($vat);
        }
        if(empty($entity->getTransactionMethod())){
            $entity->setTransactionMethod($this->_em->getRepository('SettingToolBundle:TransactionMethod')->find(1));
        }
        $this->_em->persist($entity);
        $this->_em->flush($entity);
        $accountInvoice = $this->_em->getRepository('AccountingBundle:AccountSales')->insertHospitalAccountInvoice($entity);
        $this->_em->getRepository('AccountingBundle:Transaction')->hmsSalesTransaction($entity, $accountInvoice);

    }

    public function hmsEditInvoiceTransaction(Invoice $entity)
    {
        $em = $this->_em;
        if(!empty($entity->getAccountSales())){
            /* @var AccountSales $sales*/
            foreach ($entity->getAccountSales() as $sales ){
                $globalOption = $sales->getGlobalOption()->getId();
                $accountRefNo = $sales->getAccountRefNo();
                $transaction = $em->createQuery("DELETE AccountingBundle:Transaction e WHERE e.globalOption = ".$globalOption ." AND e.accountRefNo =".$accountRefNo." AND e.processHead = 'Sales'");
                $transaction->execute();
                $accountCash = $em->createQuery("DELETE AccountingBundle:AccountCash e WHERE e.globalOption = ".$globalOption ." AND e.accountRefNo =".$accountRefNo." AND e.processHead = 'Sales'");
                $accountCash->execute();
            }
        }
        $accountCash = $em->createQuery('DELETE AccountingBundle:AccountSales e WHERE e.hmsInvoices = '.$entity->getId());
        if(!empty($accountCash)){
            $accountCash->execute();
        }
        $docter = $em->createQuery('DELETE HospitalBundle:InvoiceTransaction e WHERE e.hmsInvoice = '.$entity->getId());
        if(!empty( $docter)){
            $docter->execute();
        }
        $docter = $em->createQuery('DELETE HospitalBundle:DoctorInvoice e WHERE e.hmsInvoice = '.$entity->getId());
        if(!empty( $docter)){
            $docter->execute();
        }
    }

    public function hmsSalesTransactionReverse(Invoice $entity)
    {
        $em = $this->_em;
        if(!empty($entity->getAccountSales())){
            /* @var AccountSales $sales*/
            foreach ($entity->getAccountSales() as $sales ){
                $globalOption = $sales->getGlobalOption()->getId();
                $accountRefNo = $sales->getAccountRefNo();
                $transaction = $em->createQuery("DELETE AccountingBundle:Transaction e WHERE e.globalOption = ".$globalOption ." AND e.accountRefNo =".$accountRefNo." AND e.processHead = 'Sales'");
                $transaction->execute();
                $accountCash = $em->createQuery("DELETE AccountingBundle:AccountCash e WHERE e.globalOption = ".$globalOption ." AND e.accountRefNo =".$accountRefNo." AND e.processHead = 'Sales'");
                $accountCash->execute();
            }
        }
        $accountCash = $em->createQuery('DELETE AccountingBundle:AccountSales e WHERE e.hmsInvoices = '.$entity->getId());
        if(!empty($accountCash)){
            $accountCash->execute();
        }
        $docter = $em->createQuery('DELETE HospitalBundle:InvoiceTransaction e WHERE e.hmsInvoice = '.$entity->getId());
        if(!empty( $docter)){
            $docter->execute();
        }
        $docter = $em->createQuery('DELETE HospitalBundle:DoctorInvoice e WHERE e.hmsInvoice = '.$entity->getId());
        if(!empty( $docter)){
            $docter->execute();
        }
    }

    public function hmsAdmissionSalesTransactionReverse(Invoice $entity)
    {

        $em = $this->_em;

        if(!empty($entity->getAccountSales())){
            /* @var AccountSales $sales*/
            foreach ($entity->getAccountSales() as $sales ){

                $globalOption = $sales->getGlobalOption()->getId();
                $accountRefNo = $sales->getAccountRefNo();
                $transaction = $em->createQuery("DELETE AccountingBundle:Transaction e WHERE e.globalOption = ".$globalOption ." AND e.accountRefNo =".$accountRefNo." AND e.processHead = 'Sales'");
                $transaction->execute();
                $accountCash = $em->createQuery("DELETE AccountingBundle:AccountCash e WHERE e.globalOption = ".$globalOption ." AND e.accountRefNo =".$accountRefNo." AND e.processHead = 'Sales'");
                $accountCash->execute();
            }
        }
        $accountCash = $em->createQuery('DELETE AccountingBundle:AccountSales e WHERE e.hmsInvoices = '.$entity->getId());
        if(!empty($accountCash)){
            $accountCash->execute();
        }
        $qb = $this->createQueryBuilder('it');
        $q = $qb->update()
            ->set('it.process', $qb->expr()->literal('In-progress'))
            ->set('it.revised', $qb->expr()->literal(1))
            ->where('it.hmsInvoice = :invoice')
            ->setParameter('invoice', $entity->getId())
            ->getQuery();
        $q->execute();
    }

    public function updateInvoiceTransactionDiscount(Invoice $entity)
    {

        /** @var InvoiceTransaction $transaction */
        foreach ($entity->getInvoiceTransactions() as $transaction) {

            $transaction->setDiscount(0);
            $this->_em->persist($transaction);
            $this->_em->flush($transaction);
        }
        foreach ($entity->getInvoiceTransactions() as $transaction) {

            if( empty($transaction->getPayment()) and empty($transaction->getDiscount())){

                $qb = $this->_em->createQueryBuilder();
                $qb->delete('HospitalBundle:InvoiceTransaction', 'trans')
                    ->where($qb->expr()->eq('trans.id', ':id'))
                    ->setParameter('id', $transaction->getId());
                $qb->getQuery()->execute();
            }
        }
    }

    public function removePendingTransaction(Invoice $entity)
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $query = $qb->delete('HospitalBundle:InvoiceTransaction', 'e')
            ->where('e.hmsInvoice = :hmsInvoice')
            ->setParameter('hmsInvoice', $entity->getId())
            ->andWhere('e.process IN (:process)')
            ->setParameter('process',array('Pending','In-progress'))
            ->getQuery();
        if(!empty($query)) {
            $query->execute();
        }
    }

    public function getCulculationVat(Invoice $invoice, $totalAmount)
    {
        $vat = (($totalAmount * (int)$invoice->getHospitalConfig()->getVatPercentage()) / 100);
        return round($vat);
    }

    public function getInvoiceTransactionItems(Invoice $invoice)
    {
        $entities = $invoice->getInvoiceTransactions();
        $data = '';
        $i = 1;

        /** @var InvoiceTransaction $transaction */

        foreach ($entities as $transaction) {

            $date = $transaction->getUpdated()->format('d-m-Y');
            $transactionMethod ='';
            if(!empty($transaction->getTransactionMethod())){
                $transactionMethod = $transaction->getTransactionMethod()->getName();
            }
            $data .= '<tr>';
            $data .= '<td class="numeric" >' . $i . '</td>';
            $data .= '<td class="numeric" >' . $date . '</td>';
            $data .= '<td class="numeric" >' . $transactionMethod. '</td>';
            $data .= '<td class="numeric" >' . $transaction->getDiscount() . '</td>';
            $data .= '<td class="numeric" >' . $transaction->getVat() . '</td>';
            $data .= '<td class="numeric" >' . $transaction->getPayment() . '</td>';
            $data .= '<td class="numeric" >' . $transaction->getCreatedBy() . '</td>';

            $i++;
        }
        return $data;
    }

    public function getLastCode(Invoice $invoice)
    {
        $qb = $this->_em->getRepository('HospitalBundle:InvoiceTransaction')->createQueryBuilder('s');
        $qb
            ->select('MAX(s.code)')
            ->where('s.hmsInvoice = :invoice')
            ->setParameter('invoice', $invoice->getId());
            $lastCode = $qb->getQuery()->getSingleScalarResult();
        if (empty($lastCode)) {
            return 0;
        }
        return $lastCode;
    }

    public function monthlySales(User $user , $data =array())
    {
        $config = $user->getGlobalOption()->getHospitalConfig()->getId();
        $compare = new \DateTime();
        $month =  $compare->format('F');
        $year =  $compare->format('Y');
        $month = isset($data['month'])? $data['month'] :$month;
        $year = isset($data['year'])? $data['year'] :$year;

        $sql = "SELECT DATE_FORMAT(invoice.updated,'%d-%m-%Y') as date ,SUM(invoice.total) as total,SUM(invoice.discount) as discount,SUM(invoice.payment) as receive, SUM(invoice.due) as due
                FROM hms_invoice as invoice
                WHERE invoice.hospitalConfig_id = :hmsConfig AND MONTHNAME(invoice.updated) =:month AND YEAR(invoice.updated) =:year AND invoice.commissionApproved =:approved
                GROUP BY date";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('hmsConfig', $config);
        $stmt->bindValue('approved', 1);
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

}