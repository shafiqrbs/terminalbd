<?php

namespace Appstore\Bundle\HospitalBundle\Repository;
use Appstore\Bundle\HospitalBundle\Entity\Invoice;
use Doctrine\ORM\EntityRepository;


/**
 * PathologyRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class InvoiceRepository extends EntityRepository
{
    public function invoiceLists($user , $mode , $data)
    {
        $hospital = $user->getGlobalOption()->getHospitalConfig()->getId();
        $invoice = isset($data['invoice'])? $data['invoice'] :'';
        $service = isset($data['service'])? $data['service'] :'';

        $qb = $this->createQueryBuilder('e');
        $qb->where('e.hospitalConfig = :hospital')->setParameter('hospital', $hospital) ;
        $qb->andWhere('e.invoiceMode = :mode')->setParameter('mode', $mode) ;
        if (!empty($invoice)) {
            $qb->andWhere($qb->expr()->like("e.invoice", "'%$invoice%'"  ));
        }
        if(!empty($service)){
            $qb->andWhere("e.service = :service");
            $qb->setParameter('service', $service);
        }
        $qb->orderBy('e.updated','DESC');
        $qb->getQuery();
        return  $qb;
    }

    public function updateInvoiceTotalPrice(Invoice $invoice)
    {
        $em = $this->_em;
        $total = $em->createQueryBuilder()
            ->from('HospitalBundle:InvoiceParticular','si')
            ->select('sum(si.subTotal) as total')
            ->where('si.invoice = :invoice')
            ->setParameter('invoice', $invoice ->getId())
            ->getQuery()->getSingleResult();

        if ($invoice->getHospitalConfig()->getVatEnable() == 1 && $invoice->getHospitalConfig()->getVatPercentage() > 0) {
            $totalAmount = ($total['total'] - $invoice->getDiscount());
            $vat = $this->getCulculationVat($invoice,$totalAmount);
            $invoice->setVat($vat);
        }
        if($total['total'] > 0){

            $invoice->setSubTotal($total['total']);
            $invoice->setTotal($invoice->getSubTotal() + $invoice->getVat() - $invoice->getDiscount());
            $invoice->setDue($invoice->getTotal() - $invoice->getPayment() );

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

    public function getCulculationVat(Invoice $sales,$totalAmount)
    {
        $vat = ( ($totalAmount * (int)$sales->getHospitalConfig()->getVatPercentage())/100 );
        return round($vat);
    }


}
