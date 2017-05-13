<?php

namespace Appstore\Bundle\HospitalBundle\Repository;
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
    public function insertTransaction(Invoice $invoice, $data)
    {
        if ($data['payment'] > 0 || $data['discount'] > 0) {

            $entity = New InvoiceTransaction();
            $entity->setInvoice($invoice);
            $entity->setPayment($data['payment']);
            $entity->setTransactionMethod($invoice->getTransactionMethod());
            $entity->setDiscount($data['discount']);
            $entity->setAccountBank($invoice->getAccountBank());
            $entity->setPaymentCard($invoice->getPaymentCard());
            $entity->setCardNo($invoice->getCardNo());
            $entity->setBank($invoice->getBank());
            $entity->setAccountMobileBank($invoice->getAccountMobileBank());
            $entity->setPaymentMobile($invoice->getPaymentMobile());
            $entity->setTransactionId($invoice->getTransactionId());
            $entity->setComment($invoice->getComment());
            if ($invoice->getHospitalConfig()->getVatEnable() == 1 && $invoice->getHospitalConfig()->getVatPercentage() > 0) {
                $vat = $this->getCulculationVat($invoice, $invoice->getPayment());
                $entity->setVat($vat);
            }
            $this->_em->persist($entity);
            $this->_em->flush($entity);
        }
        if ($data['payment'] > 0) {
            $accountInvoice = $this->_em->getRepository('AccountingBundle:AccountSales')->insertAccountInvoice($entity);
            $this->_em->getRepository('AccountingBundle:Transaction')->hmsSalesTransaction($entity, $accountInvoice);
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
                //echo $transaction->getId();

                $qb = $this->_em->createQueryBuilder();
                $qb->delete('HospitalBundle:InvoiceTransaction', 'trans')
                    ->where($qb->expr()->eq('trans.id', ':id'))
                    ->setParameter('id', $transaction->getId());

                $qb->getQuery()->execute();
            }

        }

    }


    public function getCulculationVat(Invoice $sales, $totalAmount)
    {
        $vat = (($totalAmount * (int)$sales->getHospitalConfig()->getVatPercentage()) / 100);
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
            $data .= '<tr>';
            $data .= '<td class="numeric" >' . $i . '</td>';
            $data .= '<td class="numeric" >' . $date . '</td>';
            $data .= '<td class="numeric" >' . $transaction->getTransactionMethod()->getName() . '</td>';
            $data .= '<td class="numeric" >' . $transaction->getDiscount() . '</td>';
            $data .= '<td class="numeric" >' . $transaction->getVat() . '</td>';
            $data .= '<td class="numeric" >' . $transaction->getPayment() . '</td>';
            $data .= '<td class="numeric" >' . $transaction->getCreatedBy() . '</td>';

            $i++;
        }
        return $data;
    }
}