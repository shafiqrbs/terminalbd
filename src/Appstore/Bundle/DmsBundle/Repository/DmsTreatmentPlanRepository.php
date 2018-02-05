<?php

namespace Appstore\Bundle\DmsBundle\Repository;
use Appstore\Bundle\DmsBundle\Entity\DmsConfig;
use Appstore\Bundle\DmsBundle\Entity\DmsInvoice;
use Appstore\Bundle\DmsBundle\Entity\DmsTreatmentPlan;
use Doctrine\ORM\EntityRepository;



/**
 * PathologyRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DmsTreatmentPlanRepository extends EntityRepository
{

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
        $appointmentStartDate = isset($data['appointmentStartDate'])? $data['appointmentStartDate'] :'';
        $appointmentEndDate = isset($data['appointmentEndDate'])? $data['appointmentEndDate'] :'';
        $appointmentDate = isset($data['appointmentDate'])? $data['appointmentDate'] :'';

        if (!empty($invoice)) {
            $qb->andWhere($qb->expr()->like("invoice.invoice", "'$invoice%'"  ));
        }
        if (!empty($customerName)) {
            $qb->andWhere($qb->expr()->like("customer.name", "'$customerName%'"  ));
        }

        if (!empty($customerMobile)) {
            $qb->andWhere($qb->expr()->like("customer.mobile", "'$customerMobile%'"  ));
        }

        if (!empty($appointmentStartDate)) {
            $qb->andWhere("appointment.appointmentDate >= :appointmentStartDate");
            $qb->setParameter('appointmentStartDate', $appointmentStartDate);
        }

        if (!empty($appointmentEndDate)) {
            $qb->andWhere("appointment.appointmentDate <= :appointmentEndDate");
            $qb->setParameter('appointmentEndDate', $appointmentEndDate);
        }

        if (!empty($appointmentDate)) {
            $qb->andWhere("appointment.appointmentDate = :appointmentDate");
            $qb->setParameter('appointmentDate', $appointmentDate);
        }

        if(!empty($assignDoctor)){
            $qb->andWhere("invoice.assignDoctor = :assignDoctor");
            $qb->setParameter('assignDoctor', $assignDoctor);
        }
        if(!empty($process)){
            $qb->andWhere("invoice.process = :process");
            $qb->setParameter('process', $process);
        }

    }

    public function findTodaySchedule(DmsConfig $config,$data= array())
    {

        $qb = $this->createQueryBuilder('appointment');
        $qb->join('appointment.dmsInvoice','invoice');
        $qb->join('invoice.assignDoctor','doctor');
        $qb->join('appointment.dmsParticular','particular');
        $qb->join('invoice.customer','customer');
        $qb->select('customer.name as customerName');
        $qb->addSelect('doctor.name as doctorName');
        $qb->addSelect('invoice.invoice as patientId');
        $qb->addSelect('invoice.process as process');
        $qb->addSelect('particular.particularCode as particularCode');
        $qb->addSelect('particular.name as particularName');
        $qb->addSelect('appointment.id as id');
        $qb->addSelect('appointment.sendSms as sendSms');
        $qb->addSelect('appointment.appointmentDate as appointmentDate');
        $qb->addSelect('appointment.appointmentTime as appointmentTime');
        $qb->addSelect('appointment.status as appointmentStatus');
        $qb->where('invoice.dmsConfig ='.$config->getId());
        $this->handleSearchBetween($qb,$data);
        $result = $qb->getQuery()->getArrayResult();
        return $result;



    }

    public function appointmentDate(DmsConfig $config , $appointmentDate = '')
    {

        $curDate =  New \DateTime("now");
        $curDate = $curDate->format('d-m-Y');
        $appointmentDate = !empty($appointmentDate)? $appointmentDate :(string)$curDate;
        $data = array('appointmentDate' => $appointmentDate);
        $results = $this->findTodaySchedule($config,$data);
        $data = '';
        $i = 1;
        /* @var $entity DmsTreatmentPlan */

        foreach ($results as $entity) {

            $action ='<a class="btn blue sms-confirm mini" href="javascript:" data-url="/dms/invoice/'.$entity['patientId'].'/'.$entity['id'].'/send-sms"><i class="icon-phone"></i> Send SMS</a>';
            $status = ($entity['appointmentStatus'] = 1)?'Yes':'No';
            $data .= '<tr>';
            $data .= '<td class="numeric" >' . $i . '</td>';
            $data .= '<td class="numeric" >' . $entity['patientId']. '</td>';
            $data .= '<td class="numeric" >' . $entity['customerName']. '</td>';
            $data .= '<td class="numeric" >' . $entity['particularCode'].' - '. $entity['particularName']. '</td>';
            $data .= '<td class="numeric" >' . $entity['appointmentTime']. '</td>';
            $data .= '<td class="numeric" >' . $entity['process'] . '</td>';
            $data .= '<td class="numeric" >' . $status . '</td>';
            $data .= '<td class="numeric" >'.$action.'</td>';
            $data .= '</tr>';
            $i++;
        }
        return $data;

    }

    public function insertInvoiceItems($invoice, $data)
    {

        $particular = $this->_em->getRepository('DmsBundle:DmsParticular')->find($data['particularId']);
        $em = $this->_em;
        $entity = new DmsTreatmentPlan();
        $invoiceDmsParticular = $this->_em->getRepository('DmsBundle:DmsTreatmentPlan')->findOneBy(array('dmsInvoice'=>$invoice ,'dmsParticular' => $particular));
        if(!empty($invoiceDmsParticular)) {
            $entity = $invoiceDmsParticular;
            if ($particular->getService()->getHasQuantity() == 1){
                $entity->setQuantity($invoiceDmsParticular->getQuantity() + $data['quantity']);
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
            $entity->setPrice($data['price']);
            $entity->setSubTotal($data['price'] * $data['quantity']);
        }

        $datetime = !empty($data['appointmentDate']) ? $data['appointmentDate'] : '' ;
        $appointmentTime = !empty($data['appointmentTime']) ? $data['appointmentTime'] : '' ;
        $entity->setAppointmentDate($datetime);
        $entity->setAppointmentTime($appointmentTime);
        $entity->setDmsInvoice($invoice);
        $entity->setDmsParticular($particular);
        $entity->setStatus(false);
        $entity->setEstimatePrice($particular->getPrice());
        $em->persist($entity);
        $em->flush();

    }

    public function getSalesItems(DmsInvoice $sales)
    {
        $entities = $sales->getDmsTreatmentPlans();
        $data = '';
        $i = 1;

        /* @var $entity DmsTreatmentPlan */

        foreach ($entities as $entity) {

            if ($entity->getStatus() == 1)  {
               $discount = $entity->getDiscount();
            }else{
                $discount ='<a  class="editable" data-name="Discount" href="javascript:"  data-url="/dms/invoice/inline-update" data-type="text" data-pk="'.$entity->getId().'" data-original-title="Change discount amount">'.$entity->getDiscount().'</a>';
            }

            if ($entity->getStatus() == 1)  {
               $payment = $entity->getPayment();
            }else{
               $payment ='<a  class="editable" data-name="Payment" href="javascript:"  data-url="/dms/invoice/inline-update" data-type="text" data-pk="'.$entity->getId().'" data-original-title="Change payment amount">'.$entity->getPayment().'</a>';
            }

            if ($entity->getStatus() == 1)  {
                $appointmentDate = $entity->getAppointmentDate();
                $appointmentTime = $entity->getAppointmentTime();
            }else{
                $appointmentDate ='<a  class="btn mini blue-stripe btn-action editable editable-click" data-name="AppointmentDate" href="javascript:"  data-url="/dms/invoice/inline-update" data-type="text" data-pk="'.$entity->getId().'" data-original-title="Change Appointment Date">'.$entity->getAppointmentDate().'</a>';
                $appointmentTime ='<a  class="btn mini purple-stripe btn-action editable editable-click" data-name="AppointmentTime" data-source="/dms/invoice/inline-appointment-datetime-select" href="javascript:"  data-url="/dms/invoice/inline-update" data-type="text" data-value="'.$entity->getAppointmentTime().'" data-pk="'.$entity->getId().'" data-original-title="Change Appointment Time">'.$entity->getAppointmentTime().'</a>';
            }

            if ($entity->getStatus() == 1)  {
                $action ='Done';
            }else{
                $action ='<a id="'.$entity->getId().'" data-id="'.$entity->getId().'" title="Are you sure went to approve ?" data-url="/dms/invoice/' . $entity->getId() . '/treatment-approved" href="javascript:" class="btn blue mini approve" >Approve</a>
                        <a id="'.$entity->getId().'" data-id="'.$entity->getId().'" title="Are you sure went to delete ?" data-url="/dms/invoice/' . $sales->getId() . '/' . $entity->getId() . '/treatment-delete" href="javascript:" class="btn red mini treatmentDelete" ><i class="icon-trash"></i></a>';
            }

            $data .= '<tr id="remove-'. $entity->getId() . '">';
            $data .= '<td class="numeric" >' . $i . '</td>';
            $data .= '<td class="numeric" >' . $entity->getDmsParticular()->getParticularCode().' - '. $entity->getDmsParticular()->getName(). '</td>';
            $data .= '<td class="numeric" >' . $appointmentDate .'/'.$appointmentTime. '</td>';
            $data .= '<td class="numeric" >' . $entity->getPrice() . '</td>';
            $data .= '<td class="numeric" >' . $entity->getSubTotal() . '</td>';
            $data .= '<td class="numeric" >' . $discount . '</td>';
            $data .= '<td class="numeric" >' . $payment . '</td>';
            $data .= '<td class="numeric" >'.$action.'</td>';
            $data .= '</tr>';
            $i++;
        }
        return $data;
    }

    public function insertPaymentTransaction($data)
    {
        $em = $this->_em;
        $invoiceDmsParticular = $this->_em->getRepository('DmsBundle:DmsTreatmentPlan')->find($data['invoiceParticular']);
        $invoiceDmsParticular->setPayment($data['payment']);
        $invoiceDmsParticular->setDiscount($data['discount']);
        $em->persist($invoiceDmsParticular);
        $em->flush();
    }

    public function dailySales(DmsConfig $config , $data =array())
    {
        $qb = $this->createQueryBuilder('appointment');
        $qb->join('appointment.dmsInvoice','invoice');
        $qb->join('invoice.assignDoctor','doctor');
        $qb->join('appointment.dmsParticular','particular');
        $qb->join('invoice.customer','customer');
        $qb->select('appointment.updated as updated');
        $qb->addSelect('SUM(appointment.subTotal) as subTotal');
        $qb->addSelect('SUM(appointment.discount) as discount');
        $qb->addSelect('SUM(appointment.payment) as payment');
        $qb->where('invoice.dmsConfig ='.$config->getId());
        $qb->andWhere('appointment.status =1');
        $this->handleSearchBetween($qb,$data);
        $qb->groupBy('appointment.updated');
        $result = $qb->getQuery()->getArrayResult();
        return $result;

    }

    public function monthlySummaryDate($qb,$data)
    {
        $day = isset($data['day'])? $data['day'] :'';
        $month = isset($data['month'])? $data['month'] :'';
        $year = isset($data['year'])? $data['year'] :'';
        $compare = new \DateTime($year.' '.$month);
        $month =  $compare->format('m');
        $year =  $compare->format('Y');
        $qb->andWhere('YEAR(appointment.updated) = :year');
        $qb->andWhere('MONTH(appointment.updated) = :month');
        $qb->setParameter('month', $month);
        $qb->setParameter('year', $year);

    }

    public function monthlySales(DmsConfig $config , $data =array())
    {
        $emConfig = $this->getEntityManager()->getConfiguration();
        $emConfig->addCustomDatetimeFunction('YEAR', 'DoctrineExtensions\Query\Mysql\Year');
        $emConfig->addCustomDatetimeFunction('MONTH', 'DoctrineExtensions\Query\Mysql\Month');
        $emConfig->addCustomDatetimeFunction('DAY', 'DoctrineExtensions\Query\Mysql\Day');

        $qb = $this->createQueryBuilder('appointment');
        $qb->join('appointment.dmsInvoice','invoice');
        $qb->join('invoice.assignDoctor','doctor');
        $qb->join('appointment.dmsParticular','particular');
        $qb->join('invoice.customer','customer');
        $qb->select('DAY(appointment.updated) as date');
        $qb->addSelect('MONTH(appointment.updated) as month');
        $qb->addSelect('YEAR(appointment.updated) as year');
        $qb->addSelect('SUM(appointment.subTotal) as subTotal');
        $qb->addSelect('SUM(appointment.discount) as discount');
        $qb->addSelect('SUM(appointment.payment) as receive');
        $qb->where('invoice.dmsConfig ='.$config->getId());
        $qb->andWhere('appointment.status =1');
        $this->monthlySummaryDate($qb,$data);
        $qb->groupBy('date');
        $result = $qb->getQuery()->getArrayResult();
        return $result;


    }


    public function transactionOverview(DmsConfig $config , $data =array())
    {
        $qb = $this->createQueryBuilder('appointment');
        $qb->join('appointment.dmsInvoice','invoice');
  //      $qb->join('invoice.assignDoctor','doctor');
      //  $qb->join('appointment.dmsParticular','particular');
//        $qb->join('invoice.customer','customer');
        $qb->select('SUM(appointment.subTotal) as subTotal');
        $qb->addSelect('SUM(appointment.discount) as discount');
        $qb->addSelect('SUM(appointment.payment) as payment');
        $qb->where('invoice.dmsConfig ='.$config->getId());
        $qb->andWhere('appointment.status =1');
       // $this->handleSearchBetween($qb,$data);
      //  $qb->groupBy('appointment.updated');
        $result = $qb->getQuery()->getOneOrNullResult();
        return $result;

    }


    public function salesSummaryAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;

        $user = $this->getUser();

        $salesTotalTransactionOverview = $em->getRepository('HospitalBundle:InvoiceTransaction')->todaySalesOverview($user,$data);
        $salesTransactionOverview = $em->getRepository('HospitalBundle:InvoiceTransaction')->todaySalesOverview($user,$data,'true');
        $previousSalesTransactionOverview = $em->getRepository('HospitalBundle:InvoiceTransaction')->todaySalesOverview($user,$data,'false');

        $diagnosticOverview = $em->getRepository('HospitalBundle:Invoice')->findWithSalesOverview($user,$data,$mode = 'diagnostic');
        $admissionOverview = $em->getRepository('HospitalBundle:Invoice')->findWithSalesOverview($user,$data,$mode = 'admission');
        $serviceOverview = $em->getRepository('HospitalBundle:Invoice')->findWithServiceOverview($user,$data);
        $transactionOverview = $em->getRepository('HospitalBundle:InvoiceTransaction')->findWithTransactionOverview($user,$data);
        $commissionOverview = $em->getRepository('HospitalBundle:Invoice')->findWithCommissionOverview($user,$data);

        return $this->render('HospitalBundle:Report:salesSumary.html.twig', array(

            'salesTotalTransactionOverview'      => $salesTotalTransactionOverview,
            'salesTransactionOverview'      => $salesTransactionOverview,
            'previousSalesTransactionOverview' => $previousSalesTransactionOverview,
            'diagnosticOverview'            => $diagnosticOverview,
            'admissionOverview'             => $admissionOverview,
            'serviceOverview'               => $serviceOverview,
            'transactionOverview'           => $transactionOverview,
            'commissionOverview'            => $commissionOverview,
            'searchForm'                    => $data,

        ));

    }
    public function serviceBaseSummaryAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;

        $user = $this->getUser();
        $entity = '';
        if(!empty($data) and $data['service']){
            $entity = $em->getRepository('HospitalBundle:Service')->find($data['service']);
        }
        $services = $em->getRepository('HospitalBundle:Service')->findBy(array(),array('name'=>'ASC'));
        $serviceOverview = $em->getRepository('HospitalBundle:Invoice')->findWithServiceOverview($user,$data);
        $serviceGroup = $em->getRepository('HospitalBundle:InvoiceParticular')->serviceParticularDetails($user,$data);
        return $this->render('HospitalBundle:Report:serviceBaseSales.html.twig', array(
            'serviceOverview'       => $serviceOverview,
            'serviceGroup'          => $serviceGroup,
            'services'              => $services,
            'entity'                => $entity,
            'searchForm'            => $data,
        ));

    }


}
