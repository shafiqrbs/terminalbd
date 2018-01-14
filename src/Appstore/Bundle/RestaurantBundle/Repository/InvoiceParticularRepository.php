<?php

namespace Appstore\Bundle\RestaurantBundle\Repository;
use Appstore\Bundle\RestaurantBundle\Controller\InvoiceController;
use Appstore\Bundle\RestaurantBundle\Entity\Invoice;
use Appstore\Bundle\RestaurantBundle\Entity\InvoiceParticular;
use Appstore\Bundle\RestaurantBundle\Entity\Particular;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;


/**
 * PathologyRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class InvoiceParticularRepository extends EntityRepository
{

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
            $qb->andWhere("e.created >= :startDate");
            $qb->setParameter('startDate', $data['startDate'].' 00:00:00');
        }
        if (!empty($data['endDate'])) {
            $qb->andWhere("e.created <= :endDate");
            $qb->setParameter('endDate', $data['endDate'].' 23:59:59');
        }
    }

    public function invoicePathologicalReportLists(User $user , $mode , $data)
    {
        $hospital = $user->getGlobalOption()->getRestaurantConfig()->getId();
        $qb = $this->createQueryBuilder('ip');
        $qb->join('ip.invoice','e');
        $qb->join('ip.particular','p');
        $qb->where('e.hospitalConfig = :hospital')->setParameter('hospital', $hospital) ;
        $qb->andWhere('p.service = :service')->setParameter('service', 1) ;
       // $this->handleSearchBetween($qb,$data);
        $qb->andWhere("e.process IN (:process)");
        $qb->setParameter('process', array('Done','Paid','In-progress','Diagnostic','Admitted'));
        $qb->orderBy('e.created','DESC');
        $qb->getQuery();
        return  $qb;
    }



    public function insertInvoiceItems($invoice, $data)
    {
        $particular = $this->_em->getRepository('RestaurantBundle:Particular')->find($data['particularId']);
        $em = $this->_em;
        $entity = new InvoiceParticular();
        $invoiceParticular = $this->_em->getRepository('RestaurantBundle:InvoiceParticular')->findOneBy(array('invoice'=>$invoice ,'particular' => $particular));
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
        $entity->setInvoice($invoice);
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
        $entity = $this->findOneBy(array('invoice' => $invoice,'particular' => $patientParticular->getParticular()));
        /* @var $entity InvoiceParticular */
        if(empty($entity)){

            $entity = new InvoiceParticular();
            $entity->setSubTotal($patientParticular->getSubTotal());
            $entity->setQuantity($patientParticular->getQuantity());
            $entity->setInvoice($invoice);
            $entity->setParticular($patientParticular->getParticular());
            $entity->setSalesPrice($patientParticular->getSalesPrice());
            $entity->setEstimatePrice($patientParticular->getParticular()->getPrice());

        }else{

            $entity->setSubTotal( $entity->getSubTotal() + $patientParticular->getSubTotal());
            $entity->setQuantity( $entity->getQuantity() + $patientParticular->getQuantity());

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
            $data .= '<tr id="remove-'. $entity->getId().'">';
            $data .= '<td class="span4" >'.$i.'. '. $entity->getParticular()->getParticularCode() . '</td>';
            $data .= '<td class="span1" >' . $entity->getSalesPrice().'</td>';
            $data .= '<td class="span1" >' . $entity->getQuantity().'</td>';
            $data .= '<td class="span2" >' . $entity->getSubTotal() . '</td>';
            $data .= '<td class="span1" >
            <a id="'.$entity->getId().'" data-id="'.$entity->getId().'" title="Are you sure went to delete ?" data-url="/restaurant/invoice/' . $sales->getId() . '/' . $entity->getId() . '/particular-delete" href="javascript:" class="btn red mini particularDelete" ><i class="icon-trash"></i></a>
            </td>';
            $data .= '</tr>';
            $i++;
        }
        return $data;


        /*$entities = $sales->getInvoiceParticulars();
        $data = '';
        $i = 1;
        foreach ($entities as $entity) {
            $data .= '<tr id="remove-'. $entity->getId() . '">';
            $data .= '<td class="span1"><span class="badge badge-warning toggle badge-custom" id='. $entity->getId() .'" ><span>[+]</span></span></td>';
            $data .= '<td class="span1" >' . $i . '</td>';
            $data .= '<td class="span1" >' . $entity->getParticular()->getParticularCode() . '</td>';
            $data .= '<td class="span4" >' . $entity->getParticular()->getName() . '</td>';
            $data .= '<td class="span2" >' . $entity->getParticular()->getCategory()->getName() . '</td>';
            $data .= '<td class="span1" >' . $entity->getQuantity() . '</td>';
            $data .= '<td class="span2" >' . $entity->getSalesPrice() . '</td>';
            $data .= '<td class="span2" >' . $entity->getSubTotal() . '</td>';
            $data .= '<td class="span1" >
            <a id="'.$entity->getId().'" data-id="'.$entity->getId().'" title="Are you sure went to delete ?" data-url="/restaurant/invoice/' . $sales->getId() . '/' . $entity->getId() . '/particular-delete" href="javascript:" class="btn red mini particularDelete" ><i class="icon-trash"></i></a>
            </td>';
            $data .= '</tr>';
            $i++;
        }
        return $data;*/
    }

    public function invoiceParticularLists($user){


    }

    public function invoiceParticularReverse(Invoice $invoice)
    {

        $em = $this->_em;

        /** @var InvoiceParticular $item */

        foreach($invoice->getInvoiceParticulars() as $item ){

            /** @var Particular  $particular */

            $particular = $item->getParticular();
            if( $particular->getService()->getId() == 4 ){
                $qnt = ($particular->getSalesQuantity() - $item->getQuantity());
                $particular->setSalesQuantity($qnt);
                $em->persist($particular);
                $em->flush();
            }
        }

    }

    public function getLastCode($entity,$datetime)
    {

        $today_startdatetime = $datetime->format('Y-m-d 00:00:00');
        $today_enddatetime = $datetime->format('Y-m-d 23:59:59');


        $qb = $this->createQueryBuilder('ip');
        $qb
            ->select('MAX(ip.code)')
            ->join('ip.invoice','s')
            ->where('s.restaurantConfig = :hospital')
            ->andWhere('s.updated >= :today_startdatetime')
            ->andWhere('s.updated <= :today_enddatetime')
            ->setParameter('hospital', $entity->getRestaurantConfig())
            ->setParameter('today_startdatetime', $today_startdatetime)
            ->setParameter('today_enddatetime', $today_enddatetime);
        $lastCode = $qb->getQuery()->getSingleScalarResult();

        if (empty($lastCode)) {
            return 0;
        }

        return $lastCode;
    }


    public function reportSalesAccessories(GlobalOption $option ,$data)
    {
        $startDate = isset($data['startDate'])  ? $data['startDate'] : '';
        $endDate =   isset($data['endDate'])  ? $data['endDate'] : '';
        $qb = $this->createQueryBuilder('ip');
        $qb->join('ip.particular','p');
        $qb->join('ip.invoice','i');
        $qb->select('SUM(ip.quantity * p.purchasePrice ) AS totalPurchaseAmount');
        $qb->where('i.hospitalConfig = :hospital');
        $qb->setParameter('hospital', $option->getRestaurantConfig());
        $qb->andWhere("i.process IN (:process)");
        $qb->setParameter('process', array('Done','Paid','In-progress','Diagnostic','Admitted','Release','Death','Released','Dead'));
        if (!empty($data['startDate']) ) {
            $qb->andWhere("i.updated >= :startDate");
            $qb->setParameter('startDate', $startDate.' 00:00:00');
        }
        if (!empty($data['endDate'])) {
            $qb->andWhere("i.updated <= :endDate");
            $qb->setParameter('endDate', $endDate.' 23:59:59');
        }
        $res = $qb->getQuery()->getOneOrNullResult();
        return $res['totalPurchaseAmount'];

    }

    public function serviceParticularDetails(User $user, $data)
    {

        $hospital = $user->getGlobalOption()->getRestaurantConfig()->getId();
        $startDate = isset($data['startDate'])  ? $data['startDate'] : '';
        $endDate =   isset($data['endDate'])  ? $data['endDate'] : '';
        if(!empty($data['service'])){

            $qb = $this->createQueryBuilder('ip');
            $qb->leftJoin('ip.particular','p');
            $qb->leftJoin('ip.invoice','e');
            $qb->select('SUM(ip.quantity) AS totalQuantity');
            $qb->addSelect('SUM(ip.quantity * p.purchasePrice ) AS purchaseAmount');
            $qb->addSelect('SUM(ip.quantity * ip.salesPrice ) AS salesAmount');
            $qb->addSelect('p.name AS serviceName');
            $qb->where('e.hospitalConfig = :hospital');
            $qb->setParameter('hospital', $hospital);
            $qb->andWhere('p.service = :service');
            $qb->setParameter('service', $data['service']);
            $qb->andWhere("e.process IN (:process)");
            $qb->setParameter('process', array('Done','Paid','In-progress','Diagnostic','Admitted','Release','Death'));
            $this->handleDateRangeFind($qb,$data);
            $qb->groupBy('p.id');
            $res = $qb->getQuery()->getArrayResult();
            return $res;

        }else{

            return false;
        }

    }

    public function reverseInvoiceParticularUpdate(Invoice $invoice)
    {
        $em = $this->_em;

        /** @var InvoiceParticular $item */
        foreach($invoice->getInvoiceParticulars() as $item ){
            /** @var Particular  $particular */
            $particular = $item->getParticular();
            if( $particular->getService()->getSlug() == 'stockable' ){
                $qnt = ($particular->getSalesQuantity() - $item->getQuantity());
                $particular->setSalesQuantity($qnt);
                $em->persist($particular);
                $em->flush();
            }
        }
    }


}
