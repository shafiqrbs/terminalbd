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
        $treatment = isset($data['treatment'])? $data['treatment'] :'';
        $status = isset($data['status'])? $data['status'] :'';
        $customerName = isset($data['name'])? $data['name'] :'';
        $customerMobile = isset($data['mobile'])? $data['mobile'] :'';
        $startDate = isset($data['startDate'])? $data['startDate'] :'';
        $endDate = isset($data['endDate'])? $data['endDate'] :'';
        $appointmentDate = isset($data['appointmentDate'])? $data['appointmentDate'] :'';

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

        if (!empty($invoice)) {
            $qb->andWhere($qb->expr()->like("invoice.invoice", "'$invoice%'"  ));
        }
        if (!empty($customerName)) {
            $qb->andWhere($qb->expr()->like("customer.name", "'$customerName%'"  ));
        }

        if (!empty($customerMobile)) {
            $qb->andWhere($qb->expr()->like("customer.mobile", "'$customerMobile%'"  ));
        }


        if (!empty($startDate) ) {
            $qb->andWhere("appointment.appointmentDate >= :startDate");
            $qb->setParameter('startDate', $startDate);
        }
        if (!empty($endDate)) {
            $qb->andWhere("appointment.appointmentDate <= :endDate");
            $qb->setParameter('endDate', $endDate);
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
        if(!empty($treatment)){
            $qb->andWhere("appointment.dmsParticular = :treatment");
            $qb->setParameter('treatment', $treatment);
        }
        if($status == 'done'){
            $qb->andWhere("appointment.status = :status");
            $qb->setParameter('status', 1);
        }
        if($status == 'pending'){
            $qb->andWhere("appointment.status = :status");
            $qb->setParameter('status', 0);
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
            $qb->andWhere("appointment.updated >= :startDate");
            $qb->setParameter('startDate', $startDate);
        }
        if (!empty($endDate)) {
            $qb->andWhere("appointment.updated <= :endDate");
            $qb->setParameter('endDate', $endDate);
        }
    }

    public function findFreeAppointmentTime(DmsConfig $config,$data)
    {

        $start = new \DateTime($data['appointmentDate']);
        $startDate = $start->format('Y-m-d 00:00:00');
        $end = new \DateTime($data['appointmentDate']);
        $endDate = $end->format('Y-m-d 23:59:59');

        $qb = $this->createQueryBuilder('appointment');
        $qb->join('appointment.dmsInvoice','invoice');
        $qb->select('appointment.appointmentTime as appointmentTime');
        $qb->where('invoice.dmsConfig ='.$config->getId());
        $qb->andWhere('invoice.assignDoctor ='.$data['assignDoctor']);
        $qb->andWhere('appointment.status = 0');
        $qb->andWhere("invoice.process IN (:process)");
        $qb->setParameter('process', array('Done','Visit','Appointment'));
        if (!empty($startDate) ) {
            $qb->andWhere("appointment.appointmentDate >= :startDate");
            $qb->setParameter('startDate', $startDate);
        }
        if (!empty($endDate)) {
            $qb->andWhere("appointment.appointmentDate <= :endDate");
            $qb->setParameter('endDate', $endDate);
        }
        $appointmentTimes =array();
        $result = $qb->getQuery()->getArrayResult();
        foreach ($result as $res){
            $appointmentTimes[] = $res['appointmentTime'];
        }
        return $appointmentTimes;

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
        $qb->addSelect('invoice.id as invoiceId');
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
      //  $this->handleDateRangeFind($qb,$data);
        $result = $qb->getQuery()->getArrayResult();
        return $result;



    }

    public function appointmentDate(DmsConfig $config , $search = array())
    {

        $results = $this->findTodaySchedule($config,$search);
        $data = '';
        $i = 1;
        /* @var $entity DmsTreatmentPlan */

        foreach ($results as $entity) {

            $href ='';
            $processIntArr = ['Appointment','Created','Done','Visit'];
            if (in_array($entity['process'],$processIntArr)){
                $href= '<a href="/dms/invoice/'.$entity['invoiceId'].'/edit" class="btn purple mini" ><i class="icon-user"></i> Manage Patient</a>';
            }
            $action ='<a class="btn blue sms-confirm mini   " href="javascript:" data-url="/dms/invoice/'.$entity['patientId'].'/'.$entity['id'].'/send-sms"><i class="icon-phone"></i> Send SMS</a>';
            if ($entity['appointmentStatus'] == 1)  {
                $appointmentDate = $entity['appointmentDate']->format('d-m-Y');
                $appointmentTime = $entity['appointmentTime'];
            }else{
                $appointmentDate ='<a  class="btn mini blue-stripe btn-action editable editable-click" data-name="AppointmentDate" href="javascript:"  data-url="/dms/invoice/inline-update" data-type="text" data-pk="'.$entity['id'].'" data-original-title="Change Appointment Date">'.$entity['appointmentDate']->format('d-m-Y').'</a>';
                $appointmentTime ='<a data-type="select" class="btn mini purple-stripe btn-action editable editable-click" data-name="AppointmentTime" data-source="/dms/invoice/inline-appointment-datetime-select" href="javascript:"  data-url="/dms/invoice/inline-update"  data-value="'.$entity['appointmentTime'].'" data-pk="'.$entity['id'].'" data-original-title="Change Appointment Time">'.$entity['appointmentTime'].'</a>';
            }
            $status = ($entity['appointmentStatus'] == 1)?'Yes':'No';
            $sendSms = ($entity['sendSms'] = 1)?'Yes':'No';

            $data .= '<tr>';
            $data .= '<td class="numeric" >' . $i . '</td>';
            $data .= '<td class="numeric" >' . $entity['patientId']. '</td>';
            $data .= '<td class="numeric" >' . $entity['customerName']. '</td>';
            $data .= '<td class="numeric" >' . $entity['doctorName']. '</td>';
            $data .= '<td class="numeric" >' . $entity['particularCode'].' - '. $entity['particularName']. '</td>';
            $data .= '<td class="numeric" >' . $appointmentDate. '</td>';
            $data .= '<td class="numeric" >' . $appointmentTime. '</td>';
            $data .= '<td class="numeric" >' . $entity['process'] . '</td>';
            $data .= '<td class="numeric" >' . $status . '</td>';
            $data .= '<td class="numeric" >' . $sendSms . '</td>';
            $data .= '<td class="numeric" >'.$href.$action.'</td>';
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
        $entity->setQuantity(1);
        $entity->setPrice($data['price']);
        $entity->setSubTotal($data['price'] * $data['quantity']);
        $datetime = !empty($data['appointmentDate']) ? $data['appointmentDate'] : '' ;
        $appointmentDate = new \DateTime($datetime);
        $appointmentTime = !empty($data['appointmentTime']) ? $data['appointmentTime'] : '' ;
        $entity->setAppointmentDate($appointmentDate);
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
                $appointmentDate = $entity->getAppointmentDate()->format('d-m-Y');
                $appointmentTime = $entity->getAppointmentTime();
            }else{
                $appointmentDate ='<a  class="btn mini blue-stripe btn-action editable editable-click" data-name="AppointmentDate" href="javascript:"  data-url="/dms/invoice/inline-update" data-type="text" data-pk="'.$entity->getId().'" data-original-title="Change Appointment Date">'.$entity->getAppointmentDate()->format('d-m-Y').'</a>';
                $appointmentTime ='<a data-type="select"  class="btn mini purple-stripe btn-action editable editable-click" data-name="AppointmentTime" data-source="/dms/invoice/inline-appointment-datetime-select" href="javascript:"  data-url="/dms/invoice/inline-update" data-value="'.$entity->getAppointmentTime().'" data-pk="'.$entity->getId().'" data-original-title="Change Appointment Time">'.$entity->getAppointmentTime().'</a>';
            }

            if ($entity->getStatus() == 1)  {
                $action ='Done';
            }else{
                $action ='<a id="'.$entity->getId().'" data-id="'.$entity->getId().'" title="Are you sure went to approve ?" data-url="/dms/invoice/' . $entity->getId() . '/treatment-approved" href="javascript:" class="btn blue mini approve" >Approve</a>
                        <a id="'.$entity->getId().'" data-id="'.$entity->getId().'" title="Are you sure went to delete ?" data-url="/dms/invoice/' . $sales->getId() . '/' . $entity->getId() . '/treatment-delete" href="javascript:" class="btn red mini treatmentDelete" ><i class="icon-trash"></i></a>';
            }

            $data .= '<tr id="remove-'. $entity->getId() . '">';
            $data .= '<td class="numeric" >' . $i . '</td>';
            $data .= '<td class="numeric" >' . $entity->getUpdated()->format('d-m-Y'). '</td>';
            $data .= '<td class="numeric" >' . $entity->getDmsParticular()->getParticularCode().' - '. $entity->getDmsParticular()->getName(). '</td>';
            $data .= '<td class="numeric" >' . $appointmentDate .$appointmentTime. '</td>';
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

    public function transactionOverview(DmsConfig $config , $data =array())
    {
        $qb = $this->createQueryBuilder('appointment');
        $qb->join('appointment.dmsInvoice','invoice');
        $qb->select('SUM(appointment.subTotal) as subTotal');
        $qb->addSelect('SUM(appointment.discount) as discount');
        $qb->addSelect('SUM(appointment.payment) as payment');
        $qb->where('invoice.dmsConfig ='.$config->getId());
        $qb->andWhere('appointment.status =1');
        if (empty($data)) {
            $datetime = new \DateTime("now");
            $data['startDate'] = $datetime->format('Y-m-d');
            $data['endDate'] = $datetime->format('Y-m-d');
        } elseif (!empty($data['startDate']) and !empty($data['endDate'])) {
            $data['startDate'] = date('Y-m-d', strtotime($data['startDate']));
            $data['endDate'] = date('Y-m-d', strtotime($data['endDate']));
        }
        if (!empty($data['startDate'])) {
            $qb->andWhere("appointment.updated >= :startDate");
            $qb->setParameter('startDate', $data['startDate'] . ' 00:00:00');
        }
        if (!empty($data['endDate'])) {
            $qb->andWhere("appointment.updated <= :endDate");
            $qb->setParameter('endDate', $data['endDate'] . ' 23:59:59');
        }
        $result = $qb->getQuery()->getOneOrNullResult();
        return $result;

    }

    public function salesSummaryOverview(DmsConfig $config , $type = 'today', $data =array())
    {

        $emConfig = $this->getEntityManager()->getConfiguration();
        $emConfig->addCustomDatetimeFunction('YEAR', 'DoctrineExtensions\Query\Mysql\Year');
        $emConfig->addCustomDatetimeFunction('MONTH', 'DoctrineExtensions\Query\Mysql\Month');
        $emConfig->addCustomDatetimeFunction('DAY', 'DoctrineExtensions\Query\Mysql\Day');


        $qb = $this->createQueryBuilder('appointment');
        $qb->join('appointment.dmsInvoice','invoice');
        $qb->select('SUM(appointment.subTotal) as subTotal');
        $qb->addSelect('SUM(appointment.discount) as discount');
        $qb->addSelect('SUM(appointment.payment) as payment');
        $qb->where('invoice.dmsConfig ='.$config->getId());
        $qb->andWhere('appointment.status =1');
        if (empty($data)) {
            $datetime = new \DateTime("now");
            $data['startDate'] = $datetime->format('Y-m-d');
            $data['endDate'] = $datetime->format('Y-m-d');
        } elseif (!empty($data['startDate']) and !empty($data['endDate'])) {
            $data['startDate'] = date('Y-m-d', strtotime($data['startDate']));
            $data['endDate'] = date('Y-m-d', strtotime($data['endDate']));
        }

        if (!empty($data['startDate'])) {
            $qb->andWhere("appointment.updated >= :startDate");
            $qb->setParameter('startDate', $data['startDate'] . ' 00:00:00');
        }
        if (!empty($data['endDate'])) {
            $qb->andWhere("appointment.updated <= :endDate");
            $qb->setParameter('endDate', $data['endDate'] . ' 23:59:59');
        }
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


    public function findWithServiceOverview(DmsConfig $config, $data)
    {
        $qb = $this->createQueryBuilder('appointment');
        $qb->leftJoin('appointment.dmsParticular','d');
        $qb->leftJoin('d.service','s');
        $qb->select('sum(appointment.subTotal) as subTotal');
        $qb->addSelect('sum(appointment.discount) as discount');
        $qb->addSelect('sum(appointment.payment) as payment');
        $qb->addSelect('d.name as particularName');
        $qb->where('d.dmsConfig = :config')->setParameter('config', $config->getId());
        $qb->andWhere('appointment.status = 1');
        $qb->andWhere("s.serviceFormat = 'treatment'");
        //$qb->andWhere("e.process IN (:process)");
        //$qb->setParameter('process', array('Done','Visit','In-progress','Diagnostic','Admitted','Release','Death','Released','Dead'));
        $this->handleDateRangeFind($qb,$data);
        $qb->groupBy('d.id');
        $qb->orderBy('d.name','ASC');
        $result = $qb->getQuery()->getArrayResult();
        return $result;
    }

    public function monthlySales(DmsConfig $config , $data =array())
    {

        $compare = new \DateTime();
        $month =  $compare->format('F');
        $year =  $compare->format('Y');
        $month = isset($data['month'])? $data['month'] :$month;
        $year = isset($data['year'])? $data['year'] :$year;

        $sql = "SELECT DATE(appointment.updated) as date,SUM(appointment.subTotal) as subTotal,SUM(appointment.discount) as discount ,SUM(appointment.payment) as payment
                FROM dms_treatment_plan as appointment
                INNER JOIN dms_invoice as invoice ON appointment.dmsInvoice_id = invoice.id
                WHERE invoice.dmsConfig_id = :dmsConfig AND appointment.status = :status AND MONTHNAME(appointment.updated) =:month AND YEAR(appointment.updated) =:year
                GROUP BY date";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('dmsConfig', $config->getId());
        $stmt->bindValue('status', 1);
        $stmt->bindValue('month', $month);
        $stmt->bindValue('year', $year);
        $stmt->execute();
        $result =  $stmt->fetchAll();
        return $result;


    }


    public function allYearlySales(DmsConfig $config , $data =array())
    {

        $compare = new \DateTime();
        $year =  $compare->format('Y');
        $year = isset($data['year'])? $data['year'] :$year;

        $sql = "SELECT YEAR(appointment.updated) as year,SUM(appointment.subTotal) as subTotal,SUM(appointment.discount) as discount ,SUM(appointment.payment) as payment
                FROM dms_treatment_plan as appointment
                INNER JOIN dms_invoice as invoice ON appointment.dmsInvoice_id = invoice.id
                WHERE invoice.dmsConfig_id = :dmsConfig AND appointment.status = :status  AND YEAR(appointment.updated) =:year
                GROUP BY year ORDER BY year ASC";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('dmsConfig', $config->getId());
        $stmt->bindValue('status', 1);
        $stmt->bindValue('year', $year);
        $stmt->execute();
        $result =  $stmt->fetchAll();
        return $result;


    }

    public function yearlySales(DmsConfig $config , $data =array())
    {

        $compare = new \DateTime();
        $year =  $compare->format('Y');
        $year = isset($data['year'])? $data['year'] :$year;

        $sql = "SELECT MONTHNAME(appointment.updated) as date,SUM(appointment.subTotal) as subTotal,SUM(appointment.discount) as discount ,SUM(appointment.payment) as payment
                FROM dms_treatment_plan as appointment
                INNER JOIN dms_invoice as invoice ON appointment.dmsInvoice_id = invoice.id
                WHERE invoice.dmsConfig_id = :dmsConfig AND appointment.status = :status  AND YEAR(appointment.updated) =:year
                GROUP BY date ORDER BY date ASC";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('dmsConfig', $config->getId());
        $stmt->bindValue('status', 1);
        $stmt->bindValue('year', $year);
        $stmt->execute();
        $result =  $stmt->fetchAll();
        return $result;


    }

    public function salesDetails(DmsConfig $config , $data =array())
    {
        $qb = $this->createQueryBuilder('appointment');
        $qb->join('appointment.dmsInvoice','dmsInvoice');
        $qb->join('dmsInvoice.customer','customer');
        $qb->join('dmsInvoice.assignDoctor','doctor');
        $qb->join('appointment.dmsParticular','particular');
        $qb->select('appointment.id as id');
        $qb->addSelect('appointment.updated as created');
        $qb->addSelect('(dmsInvoice.invoice) as invoice');
        $qb->addSelect('(doctor.name) as doctorName');
        $qb->addSelect('(customer.name) as customerName');
        $qb->addSelect('(particular.name) as particularName');
        $qb->addSelect('(appointment.subTotal) as subTotal');
        $qb->addSelect('(appointment.discount) as discount');
        $qb->addSelect('(appointment.payment) as payment');
        $qb->where('dmsInvoice.dmsConfig ='.$config->getId());
        $qb->andWhere('appointment.status =1');
        $this->handleSearchBetween($qb,$data);
        $this->handleDateRangeFind($qb,$data);
        $qb->orderBy('appointment.updated','ASC');
        $result = $qb->getQuery()->getArrayResult();
        return $result;

    }



}
