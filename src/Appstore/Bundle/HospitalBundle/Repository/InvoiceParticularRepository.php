<?php

namespace Appstore\Bundle\HospitalBundle\Repository;
use Appstore\Bundle\HospitalBundle\Controller\InvoiceController;
use Appstore\Bundle\HospitalBundle\Entity\AdmissionPatientParticular;
use Appstore\Bundle\HospitalBundle\Entity\Invoice;
use Appstore\Bundle\HospitalBundle\Entity\InvoiceParticular;
use Doctrine\ORM\EntityRepository;


/**
 * PathologyRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class InvoiceParticularRepository extends EntityRepository
{

    public function insertInvoiceItems($invoice, $data)
    {
        $particular = $this->_em->getRepository('HospitalBundle:Particular')->find($data['particularId']);
        $em = $this->_em;
        $entity = new InvoiceParticular();
        $invoiceParticular = $this->_em->getRepository('HospitalBundle:InvoiceParticular')->findOneBy(array('hmsInvoice'=>$invoice ,'particular' => $particular));
        if(!empty($invoiceParticular)) {
            $entity = $invoiceParticular;
            if ($particular->getService()->getHasQuantity() == 1){
                $entity->setQuantity($invoiceParticular->getQuantity() + $data['quantity']);
            }else{
                $entity->setQuantity(1);
            }
            $entity->setSubTotal($data['price'] * $entity->getQuantity());

        }else{

            if ($particular->getService()->getHasQuantity() == 1){
                $entity->setQuantity($data['quantity']);
            }else{
                $entity->setQuantity(1);
            }
            $entity->setSalesPrice($data['price']);
            $entity->setSubTotal($data['price'] * $data['quantity']);
        }
        $entity->setHmsInvoice($invoice);
        if($particular->getService()->getCode() == '01'){
            $datetime = new \DateTime("now");
            $entity->setCode($invoice);
            $lastCode = $this->getLastCode($invoice,$datetime);
            $entity->setCode($lastCode+1);
            $reportCode = sprintf("%s%s", $datetime->format('dmy'), str_pad($entity->getCode(),3, '0', STR_PAD_LEFT));
            $entity->setReportCode($reportCode);
        }
        $entity->setParticular($particular);
        $entity->setEstimatePrice($particular->getPrice());
        if($particular->getCommission()){
            $entity->setCommission($particular->getCommission() * $entity->getQuantity());
        }
        $em->persist($entity);
        $em->flush();

    }

    public function insertInvoiceParticularMasterUpdate(AdmissionPatientParticular $patientParticular)
    {
        $em = $this->_em;
        $invoice = $patientParticular->getInvoiceTransaction()->getHmsInvoice();
        $entity = $this->findOneBy(array('hmsInvoice' => $invoice,'particular' => $patientParticular->getParticular()));
        /* @var $entity InvoiceParticular */
        if(empty($entity)){

            $entity = new InvoiceParticular();
            $entity->setSubTotal($patientParticular->getSubTotal());
            $entity->setQuantity($patientParticular->getQuantity());
            $entity->setHmsInvoice($invoice);
            $entity->setParticular($patientParticular->getParticular());
            $entity->setSalesPrice($patientParticular->getSalesPrice());
            $entity->setEstimatePrice($patientParticular->getParticular()->getPrice());
            if($patientParticular->getParticular()->getCommission()){
                $entity->setCommission($patientParticular->getParticular()->getCommission() * $entity->getQuantity());
            }

        }else{

            $entity->setSubTotal( $entity->getSubTotal() + $patientParticular->getSubTotal());
            $entity->setQuantity( $entity->getQuantity() + $patientParticular->getQuantity());
            if($entity->getCommission()){
                $entity->setCommission($entity->getCommission() * $entity->getQuantity());
            }
        }
        $em->persist($entity);
        $em->flush();
    }

    public function getSalesItems(Invoice $sales)
    {
        $entities = $sales->getInvoiceParticulars();
        $data = '';
        $i = 1;
        foreach ($entities as $entity) {
            $data .= '<tr id="delete-'. $entity->getId() . '">';
            $data .= '<td class="span1"><span class="badge badge-warning toggle badge-custom" id='. $entity->getId() .'" ><span>[+]</span></span></td>';
            $data .= '<td class="span1" >' . $i . '</td>';
            $data .= '<td class="span1" >' . $entity->getParticular()->getParticularCode() . '</td>';
            $data .= '<td class="span4" >' . $entity->getParticular()->getName() . '</td>';
            $data .= '<td class="span2" >' . $entity->getParticular()->getService()->getName() . '</td>';
            $data .= '<td class="span1" >' . $entity->getQuantity() . '</td>';
            $data .= '<td class="span2" >' . $entity->getSalesPrice() . '</td>';
            $data .= '<td class="span2" >' . $entity->getSubTotal() . '</td>';
            $data .= '<td class="span1" >
            <a id="'.$entity->getId().'" data-id="'.$entity->getId().'" title="Are you sure went to delete ?" data-url="/hms/invoice/' . $sales->getId() . '/' . $entity->getId() . '/particular-delete" href="javascript:" class="btn red mini particularDelete" ><i class="icon-trash"></i></a>
            </td>';
            $data .= '</tr>';
            $i++;
        }
        return $data;
    }

    public function invoiceParticularLists($user){


    }

    public function getLastCode($entity,$datetime)
    {

        $today_startdatetime = $datetime->format('Y-m-d 00:00:00');
        $today_enddatetime = $datetime->format('Y-m-d 23:59:59');


        $qb = $this->createQueryBuilder('ip');
        $qb
            ->select('MAX(ip.code)')
            ->join('ip.hmsInvoice','s')
            ->where('s.hospitalConfig = :hospital')
            ->andWhere('s.updated >= :today_startdatetime')
            ->andWhere('s.updated <= :today_enddatetime')
            ->setParameter('hospital', $entity->getHospitalConfig())
            ->setParameter('today_startdatetime', $today_startdatetime)
            ->setParameter('today_enddatetime', $today_enddatetime);
        $lastCode = $qb->getQuery()->getSingleScalarResult();

        if (empty($lastCode)) {
            return 0;
        }

        return $lastCode;
    }
}
