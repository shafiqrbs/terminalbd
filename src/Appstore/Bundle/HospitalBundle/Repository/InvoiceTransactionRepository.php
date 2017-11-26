<?php

namespace Appstore\Bundle\HospitalBundle\Repository;
use Appstore\Bundle\AccountingBundle\Entity\AccountSales;
use Appstore\Bundle\HospitalBundle\Entity\Invoice;
use Appstore\Bundle\HospitalBundle\Entity\InvoiceTransaction;
use Doctrine\ORM\EntityRepository;


/**
 * InvoiceTransactionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class InvoiceTransactionRepository extends EntityRepository
{

    public function initialInsertInvoiceTransaction(Invoice $invoice){

        $code = $this->getLastCode($invoice);
        $entity = New InvoiceTransaction();
        $entity->setHmsInvoice($invoice);
        $entity->setCode($code + 1);
        $transactionCode = sprintf("%s", str_pad($entity->getCode(),2, '0', STR_PAD_LEFT));
        $entity->setTransactionCode($transactionCode);
        $entity->setTotal($invoice->getSubTotal());
        $this->_em->persist($entity);
        $this->_em->flush($entity);
        return $entity;

    }

    public function insertAdmissionTransaction(InvoiceTransaction $entity, $data)
    {
        if ($data['discount'] > 0) {
            $process = $data['process'];
            $entity->setProcess($process);
            $entity->setDiscount($data['discount']);
            $this->_em->persist($entity);
            $this->_em->flush($entity);

        }

        if($data['payment'] > 0){

            $process = $data['process'];
            $invoice = $entity->getHmsInvoice();
            $entity->setProcess($process);
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
            $accountInvoice = $this->_em->getRepository('AccountingBundle:AccountSales')->insertAccountInvoice($entity);
            $this->_em->getRepository('AccountingBundle:Transaction')->hmsSalesTransaction($entity, $accountInvoice);
        }

    }
    public function insertTransaction(Invoice $invoice, $data)
    {
        if ($data['discount'] > 0) {
            $process = $data['process'];
            $entity = New InvoiceTransaction();
            $existDiscount = $this->_em->getRepository('HospitalBundle:InvoiceTransaction')->findOneBy(array('process'=> $process));
            if($existDiscount){
                $entity = $existDiscount;
            }
            $entity->setHmsInvoice($invoice);
            $entity->setProcess($process);
            $entity->setDiscount($data['discount']);
            $this->_em->persist($entity);
            $this->_em->flush($entity);

        }

        if($data['payment'] > 0){

            $process = $data['process'];
            $entity = New InvoiceTransaction();
            $entity->setHmsInvoice($invoice);
            $entity->setProcess($process);
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
            $accountInvoice = $this->_em->getRepository('AccountingBundle:AccountSales')->insertAccountInvoice($entity);
            $this->_em->getRepository('AccountingBundle:Transaction')->hmsSalesTransaction($entity, $accountInvoice);
        }

    }

    public function admissionInvoiceTransactionUpdate(InvoiceTransaction $entity )
    {
        $invoice = $entity->getHmsInvoice();
        if ($invoice->getHospitalConfig()->getVatEnable() == 1 && $invoice->getHospitalConfig()->getVatPercentage() > 0) {
            $vat = $this->getCulculationVat($invoice, $entity->getPayment());
            $entity->setVat($vat);
        }
        $this->_em->persist($entity);
        $this->_em->flush($entity);
        $accountInvoice = $this->_em->getRepository('AccountingBundle:AccountSales')->insertAccountInvoice($entity);
        $this->_em->getRepository('AccountingBundle:Transaction')->hmsSalesTransaction($entity, $accountInvoice);

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

        $transaction = $em->createQuery('DELETE HospitalBundle:InvoiceTransaction e WHERE e.hmsInvoice = '.$entity->getId());
        if(!empty($transaction)) {
            $transaction->execute();
        }
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
}