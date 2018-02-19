<?php

namespace Appstore\Bundle\DmsBundle\Controller;
use Appstore\Bundle\DmsBundle\Entity\DmsInvoiceAccessories;
use Appstore\Bundle\HospitalBundle\Entity\InvoiceParticular;
use Knp\Snappy\Pdf;
use Appstore\Bundle\DmsBundle\Entity\DmsInvoice;
use Appstore\Bundle\DmsBundle\Entity\DmsInvoiceMedicine;
use Appstore\Bundle\DmsBundle\Entity\DmsInvoiceParticular;
use Appstore\Bundle\DmsBundle\Entity\DmsParticular;
use Appstore\Bundle\DmsBundle\Entity\DmsTreatmentPlan;
use Appstore\Bundle\DmsBundle\Form\InvoiceType;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use Frontend\FrontentBundle\Service\MobileDetect;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * DmsInvoiceController controller.
 *
 */
class InvoiceController extends Controller
{

    public function paginate($entities)
    {
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            25  /*limit per page*/
        );
        $pagination->setTemplate('SettingToolBundle:Widget:pagination.html.twig');
        return $pagination;
    }

    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $dmsConfig = $user->getGlobalOption()->getDmsConfig();
        $entities = $em->getRepository('DmsBundle:DmsInvoice')->invoiceLists( $user,$data);
        $pagination = $this->paginate($entities);
        $assignDoctors = $this->getDoctrine()->getRepository('DmsBundle:DmsParticular')->getFindWithParticular($dmsConfig,array('doctor'));

        return $this->render('DmsBundle:Invoice:index.html.twig', array(
            'entities' => $pagination,
            'salesTransactionOverview' => '',
            'previousSalesTransactionOverview' => '',
            'assignDoctors' => $assignDoctors,
            'searchForm' => $data,
        ));

    }


    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new DmsInvoice();
        $option = $this->getUser()->getGlobalOption();
        $dmsConfig = $option->getDmsConfig();
        $patient = isset($_REQUEST['patient']) ? $_REQUEST['patient']:'';
        $lastObject = $em->getRepository('DmsBundle:DmsInvoice')->getLastInvoice($dmsConfig);

        if(!empty($patient)){
            $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $option,'id' => $patient));
            $entity->setCustomer($customer);
            $entity->setMobile($customer->getMobile());
        }
        $entity->setDmsConfig($dmsConfig);
        $transactionMethod = $em->getRepository('SettingToolBundle:TransactionMethod')->find(1);
        $entity->setTransactionMethod($transactionMethod);
        $entity->setPaymentStatus('Pending');
        $entity->setCreatedBy($this->getUser());
        if(!empty($this->getUser()->getDmsParticularDoctor())){
            $entity->setAssignDoctor($this->getUser()->getDmsParticularDoctor());
        }
        $entity->setCreatedBy($this->getUser());
        $em->persist($entity);
        $em->flush();

        if($dmsConfig->getIsDefaultMedicine() == 1 ){
            $this->getDoctrine()->getRepository('DmsBundle:DmsInvoiceMedicine')->defaultSetBeforeMedicine($entity,$lastObject);
        }
        return $this->redirect($this->generateUrl('dms_invoice_edit', array('id' => $entity->getId())));

    }

    /**
     * Creates a form to edit a Invoice entity.wq
     *
     * @param DmsInvoice $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(DmsInvoice $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $form = $this->createForm(new InvoiceType($globalOption,$location), $entity, array(
            'action' => $this->generateUrl('dms_invoice_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'id' => 'invoiceForm',
                'novalidate' => 'novalidate',
                'enctype' => 'multipart/form-data',

            )
        ));
        return $form;
    }




    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $dmsConfig = $this->getUser()->getGlobalOption()->getDmsConfig();
        $entity = $em->getRepository('DmsBundle:DmsInvoice')->findOneBy(array('dmsConfig' => $dmsConfig , 'id' => $id));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $editForm = $this->createEditForm($entity);

        /** @var  $invoiceParticularArr */
        $invoiceParticularArr = array();

        /** @var DmsInvoiceParticular $row */
        if (!empty($entity->getInvoiceParticulars())){
            foreach ($entity->getInvoiceParticulars() as $row):
                if(!empty($row->getDmsParticular())){
                    $invoiceParticularArr[$row->getDmsParticular()->getId()] = $row;
                }
            endforeach;
        }

        if (in_array($entity->getProcess(), array('Done','Canceled'))) {
            return $this->redirect($this->generateUrl('dms_invoice_show', array('id' => $entity->getId())));
        }

        $teethPlans ='';
        if($entity->getCustomer()){
            $ageGroup = $entity->getCustomer()->getAgeGroup();
            $teethPlans         = $em->getRepository('DmsBundle:DmsTeethPlan')->findBy(array('ageGroup' => $ageGroup),array('sorting'=>'ASC'));
        }

        $services    = $em->getRepository('DmsBundle:DmsService')->getServiceLists($dmsConfig);
        $treatmentPlans     = $em->getRepository('DmsBundle:DmsParticular')->getServices($dmsConfig,array('treatment','other-service'));
        $accessories        = $em->getRepository('DmsBundle:DmsParticular')->getAccessoriesParticular($dmsConfig,array('accessories'));
        $attributes         = $em->getRepository('DmsBundle:DmsPrescriptionAttribute')->findAll();
        return $this->render('DmsBundle:Invoice:new.html.twig', array(
            'entity' => $entity,
            'teethPlans' => $teethPlans,
            'particularService' => $treatmentPlans,
            'invoiceParticularArr' => $invoiceParticularArr,
            'services' => $services,
            'accessories' => $accessories,
            'attributes' => $attributes,
            'form' => $editForm->createView(),
        ));
    }

    public function particularSearchAction(DmsParticular $particular)
    {
        return new Response(json_encode(array('particularId'=> $particular->getId() ,'price'=> $particular->getPrice() , 'quantity'=> $particular->getQuantity(), 'minimumPrice'=> $particular->getMinimumPrice(), 'instruction'=> $particular->getInstruction())));
    }

    public function returnResultData(DmsInvoice $entity,$msg=''){

        $invoiceParticulars = $this->getDoctrine()->getRepository('DmsBundle:DmsTreatmentPlan')->getSalesItems($entity);
        $subTotal = $entity->getSubTotal() > 0 ? $entity->getSubTotal() : 0;
        $netTotal = $entity->getTotal() > 0 ? $entity->getTotal() : 0;
        $payment = $entity->getPayment() > 0 ? $entity->getPayment() : 0;
        $vat = $entity->getVat() > 0 ? $entity->getVat() : 0;
        $due = $entity->getDue() > 0 ? $entity->getDue() : 0;
        $discount = $entity->getDiscount() > 0 ? $entity->getDiscount() : 0;
        $data = array(
           'subTotal' => $subTotal,
           'netTotal' => $netTotal,
           'payment' => $payment ,
           'due' => $due,
           'vat' => $vat,
           'discount' => $discount,
           'invoiceParticulars' => $invoiceParticulars ,
           'msg' => $msg ,
           'success' => 'success'
       );

       return $data;

    }

    public function particularProcedureAction(Request $request, DmsInvoice $invoice,$service)
    {

        $em = $this->getDoctrine()->getManager();
        $procedure = $request->request->get('procedure');
        $teethNo = $request->request->get('teethNo');
        $invoiceItems = array('service'=> $service, 'procedure' => $procedure ,'teethNo' => $teethNo);
        $this->getDoctrine()->getRepository('DmsBundle:DmsInvoiceParticular')->insertInvoiceParticularSingle($invoice, $invoiceItems);
        $data = $this->getDoctrine()->getRepository('DmsBundle:DmsInvoiceParticular')->insertInvoiceParticularReturn($invoice, $invoiceItems);
        return new Response($data);
        exit;

    }

    public function addParticularAction(Request $request, DmsInvoice $invoice)
    {

        $em = $this->getDoctrine()->getManager();
        $particularId = $request->request->get('particularId');
        $price = $request->request->get('price');
        $appointmentDate = $request->request->get('appointmentDate');
        $appointmentTime = $request->request->get('appointmentTime');
        $invoiceItems = array('particularId' => $particularId , 'quantity' => 1,'price' => $price,'appointmentDate'=>$appointmentDate , 'appointmentTime'=> $appointmentTime );
        $this->getDoctrine()->getRepository('DmsBundle:DmsTreatmentPlan')->insertInvoiceItems($invoice, $invoiceItems);
        $invoice = $this->getDoctrine()->getRepository('DmsBundle:DmsInvoice')->updateInvoiceTotalPrice($invoice);
        $msg = 'Particular added successfully';
        $result = $this->returnResultData($invoice,$msg);
        return new Response(json_encode($result));
        exit;

    }

    public function invoiceParticularDeleteAction( $invoice, DmsInvoiceParticular $particular){


        $em = $this->getDoctrine()->getManager();
        if (!$particular) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }
        if(!empty($particular->getPath())){
            $particular->removeFileImage();
        }
        $em->remove($particular);
        $em->flush();
        exit;
    }

    public function addMedicineAction(Request $request, DmsInvoice $invoice)
    {

        $em = $this->getDoctrine()->getManager();
        $medicine = $request->request->get('medicine');
        $medicineId = $request->request->get('medicineId');
        $generic = $request->request->get('generic');
        $medicineQuantity = $request->request->get('medicineQuantity');
        $medicineDose = $request->request->get('medicineDose');
        $medicineDoseTime = $request->request->get('medicineDoseTime');
        $medicineDuration = $request->request->get('medicineDuration');
        $medicineDurationType = $request->request->get('medicineDurationType');
        if(!empty($medicine)  OR $medicineId > 0){
            $invoiceItems = array('medicine' => $medicine ,'medicineId' => $medicineId , 'generic' => $generic,'medicineQuantity' => $medicineQuantity,'medicineDose' => $medicineDose,'medicineDoseTime' => $medicineDoseTime ,'medicineDuration' => $medicineDuration,'medicineDurationType' => $medicineDurationType);
            $this->getDoctrine()->getRepository('DmsBundle:DmsInvoiceMedicine')->insertInvoiceMedicine($invoice, $invoiceItems);
            $result = $this->getDoctrine()->getRepository('DmsBundle:DmsInvoiceMedicine')->getInvoiceMedicines($invoice);
            return new Response($result);
        }
        exit;

    }

    public function deleteMedicineAction(DmsInvoiceMedicine $medicine){


        $em = $this->getDoctrine()->getManager();
        if (!$medicine) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }
        $em->remove($medicine);
        $em->flush();
        exit;
    }

    public function addPaymentAction(Request $request , DmsInvoice $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $invoiceParticular = $request->request->get('invoiceParticular');
        $payment = $request->request->get('payment');
        $discount = $request->request->get('discount');
        $discount = $discount !="" ? $discount : 0 ;
        $discount = $discount !="" ? $discount : 0 ;
        $process = $request->request->get('process');

        if ( (!empty($entity) and !empty($payment)) or (!empty($entity) and $discount > 0 ) ) {
            $em = $this->getDoctrine()->getManager();
            $entity->setProcess('In-progress');
            $em->flush();
            $transactionData = array('process'=> 'In-progress','invoiceParticular' => $invoiceParticular,'payment' => $payment, 'discount' => $discount);
            $this->getDoctrine()->getRepository('DmsBundle:DmsTreatmentPlan')->insertPaymentTransaction($transactionData);
            $this->getDoctrine()->getRepository('DmsBundle:DmsInvoice')->updateInvoiceTotalPrice($entity);
            return new Response('success');

        } elseif(!empty($entity) and $process == 'Done' and $entity->getTotal() <= $entity->getPayment()  ) {

            $em = $this->getDoctrine()->getManager();
            $entity->setProcess($process);
            $entity->setPaymentStatus('Paid');
            $em->flush();
            return new Response('success');

        } else {
            return new Response('failed');
        }
        exit;
    }

    public function treatmentApprovedAction(DmsTreatmentPlan $treatmentPlan){


        $em = $this->getDoctrine()->getManager();
        if (!$treatmentPlan) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }
        if($treatmentPlan->getPayment() > 0){
        $treatmentPlan->setStatus(true);
        $em->flush();
        $this->getDoctrine()->getRepository('DmsBundle:DmsInvoice')->updatePaymentReceive($treatmentPlan->getDmsInvoice());
        }
        exit;
    }

    public function inlineUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('DmsBundle:DmsTreatmentPlan')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find particular entity.');
        }
        $datetime = !empty($data['name']) ? $data['name'] : '' ;
        if($datetime == 'AppointmentDate'){
            $val = new \DateTime($data['value']);
        }else{
            $val = $data['value'];
        }

        $setField = 'set'.$data['name'];
        $entity->$setField($val);
        $em->flush();
        exit;

    }

    public function treatmentDeleteAction(DmsInvoice $invoice ,DmsTreatmentPlan $treatmentPlan){

        $em = $this->getDoctrine()->getManager();
        if (!$treatmentPlan) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }
        $em->remove($treatmentPlan);
        $em->flush();
        $this->getDoctrine()->getRepository('DmsBundle:DmsInvoice')->updateInvoiceTotalPrice($invoice);
        $result = $this->returnResultData($invoice);
        return new Response(json_encode($result));
        exit;
    }

    public function treatmentAppointmentDatetimeAction(Request $request){

        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $appointmentDate = $data['appointmentDate'];
        $appointmentTime = $data['appointmentTime'];
        $entity = $this->getDoctrine()->getRepository('DmsBundle:DmsTreatmentPlan')->findOneBy(array('status'=> 0 ,'appointmentTime'=>$appointmentTime,'appointmentDate'=> $appointmentDate ));
        if($entity){
            $res = 'invalid';
        }
        $res = 'valid';
        return new Response($res);
        exit;
    }

    public function updateAction(Request $request, DmsInvoice $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        $data = $request->request->all();

        $this->getDoctrine()->getRepository('DmsBundle:DmsInvoiceParticular')->insertInvoiceItems($entity,$data);
        if($editForm->isValid() and !empty($entity->getInvoiceParticulars())) {

            if (!empty($data['customer']['name'])) {

                $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['customer']['mobile']);
                $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findHmsExistingCustomerDiagnostic($this->getUser()->getGlobalOption(), $mobile, $data);
                $entity->setCustomer($customer);
                $entity->setMobile($mobile);
            }
            $amountInWords = $this->get('settong.toolManageRepo')->intToWords($entity->getTotal());
            $entity->setPaymentInWord($amountInWords);
            if (!in_array($entity->getProcess(), array('Canceled', 'Created')) and $entity->isSendSms() != 1) {
                /* @var $option GlobalOption */
                $option = $this->getUser()->getGlobalOption();
                if(!empty($option->getNotificationConfig()) and  !empty($option->getSmsSenderTotal()->getRemaining() > 0) and $option->getNotificationConfig()->getSmsActive() == 1 ) {
                    $dispatcher = $this->container->get('event_dispatcher');
                    $dispatcher->dispatch('setting_tool.post.dms_invoice_sms', new \Setting\Bundle\ToolBundle\Event\DmsInvoiceSmsEvent($entity));
                    $entity->setSendSms(1);
                }
            }
            $em->flush();
            $file = $request->files->all();
            if(!empty($file) and !empty($data['investigation'])){
                $this->getDoctrine()->getRepository('DmsBundle:DmsInvoiceParticular')->fileUpload($entity,$data,$file);
                $data = $this->getDoctrine()->getRepository('DmsBundle:DmsInvoiceParticular')->insertInvoiceInvestigationUpload($entity, $data);
                return new Response($data);
            }
        }
        exit;
    }

    public function showAction(DmsInvoice $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $dmsConfig = $this->getUser()->getGlobalOption()->getDmsConfig();
        if ($dmsConfig->getId() == $entity->getDmsConfig()->getId()) {
            return $this->render('DmsBundle:Invoice:show.html.twig', array(
                'entity' => $entity,
            ));
        } else {
            return $this->redirect($this->generateUrl('dms_invoice'));
        }

    }

    public function patientLoadAction(DmsInvoice $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $dmsConfig = $this->getUser()->getGlobalOption()->getDmsConfig();
        if ($dmsConfig->getId() == $entity->getDmsConfig()->getId()) {

            /** @var  $invoiceParticularArr */
            $invoiceParticularArr = array();

            /** @var DmsInvoiceParticular $row */
            if (!empty($entity->getInvoiceParticulars())) {
                foreach ($entity->getInvoiceParticulars() as $row):
                    if (!empty($row->getDmsParticular())) {
                        $invoiceParticularArr[$row->getDmsParticular()->getId()] = $row;
                    }
                endforeach;
            }

            $services = $em->getRepository('DmsBundle:DmsService')->getServiceLists($dmsConfig);
            $treatmentPlans = $em->getRepository('DmsBundle:DmsParticular')->getServices($dmsConfig, array('treatment-plan', 'other-service'));
            $treatmentSchedule = $em->getRepository('DmsBundle:DmsTreatmentPlan')->findTodaySchedule($dmsConfig);
            $html = $this->renderView('DmsBundle:Invoice:patient-overview.html.twig',
                array(
                    'entity' => $entity,
                    'invoiceParticularArr' => $invoiceParticularArr,
                    'particularService' => $treatmentPlans,
                    'services' => $services,
                    'treatmentSchedule' => $treatmentSchedule,
                )
            );
            return New Response($html);
        }
    }

    public function confirmAction(DmsInvoice $entity)
    {
        $inventory = $this->getUser()->getGlobalOption()->getDmsConfig()->getId();
        if ($inventory == $entity->getDmsConfig()->getId()) {
            return $this->render('DmsBundle:DmsInvoice:confirm.html.twig', array(
                'entity' => $entity,
            ));
        } else {
            return $this->redirect($this->generateUrl('dms_invoice'));
        }

    }

    public function approveAction(Request $request , DmsInvoice $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $payment = $request->request->get('payment');
        $discount = $request->request->get('discount');
        $discount = $discount !="" ? $discount : 0 ;
        $process = $request->request->get('process');

        if ( (!empty($entity) and !empty($payment)) or (!empty($entity) and $discount > 0 ) ) {
            $em = $this->getDoctrine()->getManager();
            $entity->setProcess('In-progress');
            $em->flush();
            $transactionData = array('process'=> 'In-progress','payment' => $payment, 'discount' => $discount);
         //   $this->getDoctrine()->getRepository('DmsBundle:DmsInvoiceTransaction')->insertPaymentTransaction($entity,$transactionData);
            return new Response('success');

        } elseif(!empty($entity) and $process == 'Done' and $entity->getTotal() <= $entity->getPayment()  ) {

            $em = $this->getDoctrine()->getManager();
            $entity->setProcess($process);
            $entity->setPaymentStatus('Paid');
            $em->flush();
            return new Response('success');

        } else {
            return new Response('failed');
        }
        exit;
    }


    /**
     * @Secure(roles="ROLE_DMS")
     */

    public function deleteAction(DmsInvoice $entity)
    {

        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $em->remove($entity);
        $em->flush();
        return new Response(json_encode(array('success' => 'success')));
        exit;
    }

    public function invoiceReverseAction(DmsInvoice $invoice)
    {
        $dmsConfig = $this->getUser()->getGlobalOption()->getDmsConfig();
        $entity = $this->getDoctrine()->getRepository('DmsBundle:HmsReverse')->findOneBy(array('dmsConfig' => $dmsConfig, 'hmsInvoice' => $invoice));
        return $this->render('DmsBundle:Reverse:show.html.twig', array(
            'entity' => $entity,
        ));

    }

    public function invoiceReverseShowAction(DmsInvoice $invoice)
    {
        $dmsConfig = $this->getUser()->getGlobalOption()->getDmsConfig();
        $entity = $this->getDoctrine()->getRepository('DmsBundle:HmsReverse')->findOneBy(array('dmsConfig' => $dmsConfig, 'hmsInvoice' => $invoice));
        return $this->render('DmsBundle:Reverse:show.html.twig', array(
            'entity' => $entity,
        ));

    }

    /**
     * @Secure(roles="ROLE_DMS")
     */

    public function deleteEmptyInvoiceAction()
    {
        $dmsConfig = $this->getUser()->getGlobalOption()->getDmsConfig();
        $entities = $this->getDoctrine()->getRepository('DmsBundle:DmsInvoice')->findBy(array('dmsConfig' => $dmsConfig, 'process' => 'Created'));
        $em = $this->getDoctrine()->getManager();
        foreach ($entities as $entity) {
            $em->remove($entity);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('dms_invoice'));
    }

    public function statusSelectAction()
    {
        $items  = array();
        $items[]= array('value' => 'In-progress','text'=>'In-progress');
        $items[]= array('value' => 'Done','text'=>'Done');
        $items[]= array('value' => 'Canceled','text'=>'Canceled');
        return new JsonResponse($items);
    }

    public function invoiceProcessUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('DmsBundle:DmsInvoice')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $entity->setProcess($data['value']);
        $em->flush();
        exit;

    }

    public function addPatientAction(Request $request,DmsInvoice $invoice)
    {
        $data = $request->request->all();
        $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->patientInsertUpdate($data,$invoice);
        $this->getDoctrine()->getRepository('DmsBundle:DmsInvoice')->patientAdmissionUpdate($data,$invoice);
        return new Response(json_encode(array('patient' => $customer->getId())));
        exit;
    }

    public function getBarcode($value)
    {
        $barcode = new BarcodeGenerator();
        $barcode->setText($value);
        $barcode->setType(BarcodeGenerator::Code39Extended);
        $barcode->setScale(1);
        $barcode->setThickness(25);
        $barcode->setFontSize(8);
        $code = $barcode->generate();
        $data = '';
        $data .= '<img src="data:image/png;base64,'.$code .'" />';
        return $data;
    }

    public function invoicePrintAction(DmsInvoice $entity)
    {

        $em = $this->getDoctrine()->getManager();
        $dmsConfig = $this->getUser()->getGlobalOption()->getDmsConfig();
        if ($dmsConfig->getId() == $entity->getDmsConfig()->getId()) {

            /** @var  $invoiceParticularArr */
            $invoiceParticularArr = array();

            /** @var $row DmsInvoiceParticular  */
            if (!empty($entity->getInvoiceParticulars())) {
                foreach ($entity->getInvoiceParticulars() as $row):
                    if (!empty($row->getDmsParticular())) {
                        $invoiceParticularArr[$row->getDmsParticular()->getId()] = $row;
                    }
                endforeach;
            }

            $services = $em->getRepository('DmsBundle:DmsService')->findBy(array('dmsConfig'=>$dmsConfig,'serviceShow'=>1,'status'=>1),array('serviceSorting'=>'ASC'));
            $treatmentSchedule = $em->getRepository('DmsBundle:DmsTreatmentPlan')->findTodaySchedule($dmsConfig);

            if($dmsConfig->isCustomPrescription() == 1){
                $template = $dmsConfig->getGlobalOption()->getSlug();
            }else{
                $template = 'print';
            }

            return  $this->render('DmsBundle:Print:'.$template.'.html.twig',
                array(
                    'entity' => $entity,
                    'print' => 'print',
                    'invoiceParticularArr' => $invoiceParticularArr,
                    'services' => $services,
                    'treatmentSchedule' => $treatmentSchedule,
                )
            );

        }

    }

    public function invoicePrintPreviewAction(DmsInvoice $entity)
    {

        $em = $this->getDoctrine()->getManager();
        $dmsConfig = $this->getUser()->getGlobalOption()->getDmsConfig();
        if ($dmsConfig->getId() == $entity->getDmsConfig()->getId()) {

            /** @var  $invoiceParticularArr */
            $invoiceParticularArr = array();

            /** @var $row DmsInvoiceParticular  */
            if (!empty($entity->getInvoiceParticulars())) {
                foreach ($entity->getInvoiceParticulars() as $row):
                    if (!empty($row->getDmsParticular())) {
                        $invoiceParticularArr[$row->getDmsParticular()->getId()] = $row;
                    }
                endforeach;
            }

            $services = $em->getRepository('DmsBundle:DmsService')->findBy(array('dmsConfig'=>$dmsConfig,'serviceShow'=>1,'status'=>1),array('serviceSorting'=>'ASC'));
            $treatmentSchedule = $em->getRepository('DmsBundle:DmsTreatmentPlan')->findTodaySchedule($dmsConfig);

            if($dmsConfig->isCustomPrescription() == 1){
                $template = $dmsConfig->getGlobalOption()->getSlug();
            }else{
                $template = 'print';
            }
            $html =  $this->renderView('DmsBundle:Print:'.$template.'.html.twig',
                array(
                    'entity' => $entity,
                    'print' => 'preview',
                    'invoiceParticularArr' => $invoiceParticularArr,
                    'services' => $services,
                    'treatmentSchedule' => $treatmentSchedule,
                )
            );
            return  New Response($html);
            exit;

        }

    }

    public function invoicePrintPdfAction(DmsInvoice $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $dmsConfig = $this->getUser()->getGlobalOption()->getDmsConfig();
        if ($dmsConfig->getId() == $entity->getDmsConfig()->getId()) {

            /** @var  $invoiceParticularArr */
            $invoiceParticularArr = array();

            /** @var $row DmsInvoiceParticular */
            if (!empty($entity->getInvoiceParticulars())) {
                foreach ($entity->getInvoiceParticulars() as $row):
                    if (!empty($row->getDmsParticular())) {
                        $invoiceParticularArr[$row->getDmsParticular()->getId()] = $row;
                    }
                endforeach;
            }

            $services = $em->getRepository('DmsBundle:DmsService')->findBy(array('dmsConfig' => $dmsConfig, 'serviceShow' => 1, 'status' => 1), array('serviceSorting' => 'ASC'));
            $treatmentSchedule = $em->getRepository('DmsBundle:DmsTreatmentPlan')->findTodaySchedule($dmsConfig);

            if ($dmsConfig->isCustomPrescription() == 1) {
                $template = $dmsConfig->getGlobalOption()->getSlug();
            } else {
                $template = 'print';
            }

            $html = $this->renderView(
                'DmsBundle:Print:dental-care.html.twig', array(
                    'entity' => $entity,
                    'invoiceParticularArr' => $invoiceParticularArr,
                    'services' => $services,
                    'treatmentSchedule' => $treatmentSchedule,
                )
            );

            $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
            $snappy = new Pdf($wkhtmltopdfPath);
            $pdf = $snappy->getOutputFromHtml($html);

            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="incomePdf.pdf"');
            echo $pdf;

            return new Response('');
        }
    }

    public function appointmentTimeAction()
    {
        $user = $this->getUser();
        $data = $_REQUEST;
        $dmsConfig = $user->getGlobalOption()->getDmsConfig();
        $appointments = $this->getDoctrine()->getRepository('DmsBundle:DmsTreatmentPlan')->findFreeAppointmentTime($dmsConfig,$data);
        $arrays = ['12.00 PM','12.15 PM',
            '12.30 PM','12.45 PM','1.00 PM','1.15 PM','1.30 PM','1.45 PM','2.00 PM','2.15 PM',
            '2.30 PM','2.45 PM','3.00 PM','4.15 PM','4.30 PM','4.45 PM','5.00 PM','5.15 PM',
            '5.30 PM','5.45 PM','6.00 PM','6.15 PM','6.30 PM','6.45 PM','7.00 PM','7.15 PM',
            '7.30 PM','7.45 PM','8.00 PM','8.15 PM','8.30 PM','8.45 PM','9.00 PM','9.15 PM','9.30 PM','9.45 PM','10.00 PM','10.15 PM','10.30 PM','10.45 PM','11.00 PM',
            '8.00 AM','8.15 AM','8.30 AM','8.45 AM','9.00 AM','9.15 AM','9.30 AM','9.45 AM','10.00 AM','10.15 AM','10.30 AM',
            '10.45 AM','11.00 AM','11.15 AM','11.30 AM','11.45 AM',
            ];
        $array = array_diff($arrays,$appointments);
        $items  = array();
        foreach ($array as $value):
            $items[]= array('value' => $value ,'text'=> $value);
        endforeach;
        return new JsonResponse($items);
    }

    public function typeaheadAction()
    {
        $array = ['12.00 PM','12.15 PM',
            '12.30 PM','12.45 PM','1.00 PM','1.15 PM','1.30 PM','1.45 PM','2.00 PM','2.15 PM',
            '2.30 PM','2.45 PM','3.00 PM','4.15 PM','4.30 PM','4.45 PM','5.00 PM','5.15 PM',
            '5.30 PM','5.45 PM','6.00 PM','6.15 PM','6.30 PM','6.45 PM','7.00 PM','7.15 PM',
            '7.30 PM','7.45 PM','8.00 PM','8.15 PM','8.30 PM','8.45 PM','9.00 PM','9.15 PM','9.30 PM','9.45 PM','10.00 PM','10.15 PM','10.30 PM','10.45 PM','11.00 PM',
            '8.00 AM','8.15 AM','8.30 AM','8.45 AM','9.00 AM','9.15 AM','9.30 AM','9.45 AM','10.00 AM','10.15 AM','10.30 AM',
            '10.45 AM','11.00 AM','11.15 AM','11.30 AM','11.45 AM',
        ];

        $items  = array();
        foreach ($array as $value):
            $items[]= array('value' => $value ,'text'=> $value);
        endforeach;
        return new JsonResponse($array);
    }

    public function procedureSearchAction()
    {
        $q = $_REQUEST['term'];
        $config = $this->getUser()->getGlobalOption()->getDmsConfig();
        $entities = $this->getDoctrine()->getRepository('DmsBundle:DmsInvoiceParticular')->searchAutoComplete($config,$q);
        $items = array();
        foreach ($entities as $entity):
            $items[]=array('value' => $entity['id']);
        endforeach;
        return new JsonResponse($items);

    }

    public function autoParticularSearchAction()
    {
        $q = $_REQUEST['term'];
        $config = $this->getUser()->getGlobalOption()->getDmsConfig();
        $entities = $this->getDoctrine()->getRepository('DmsBundle:DmsInvoiceParticular')->searchAutoComplete($config,$q);
        $items = array();
        foreach ($entities as $entity):
            $items[]=array('value' => $entity['id']);
        endforeach;
        return new JsonResponse($items);

    }

    public function autoInvestigationSearchAction()
    {
        $q = $_REQUEST['term'];
        $config = $this->getUser()->getGlobalOption()->getDmsConfig();
        $entities = $this->getDoctrine()->getRepository('DmsBundle:DmsParticular')->searchAutoComplete($config,$q);
        $items = array();
        foreach ($entities as $entity):
            $items[]=array('value' => $entity['id']);
        endforeach;
        return new JsonResponse($items);

    }

    public function investigationProcedureAction(Request $request, DmsInvoiceParticular $particular)
    {
        $file = $request->request->all();
        if(isset($file['file'])){
            $img = $file['file'];
            $fileName = $img->getClientOriginalName();
            $imgName =  uniqid(). '.' .$fileName;
            $img->move($particular->getUploadDir(), $imgName);
            $particular->setFile($imgName);
        }
       exit;
    }

    public function addAccessoriesAction(Request $request, DmsInvoice $invoice)
    {

        $em = $this->getDoctrine()->getManager();
        $accessories = $request->request->get('accessories');
        echo $quantity = $request->request->get('quantity');
        if(!empty($accessories)){
            $invoiceItems = array('accessories' => $accessories ,'quantity' => $quantity);
            $this->getDoctrine()->getRepository('DmsBundle:DmsInvoiceAccessories')->insertInvoiceAccessories($invoice, $invoiceItems);
            $result = $this->getDoctrine()->getRepository('DmsBundle:DmsInvoiceAccessories')->getInvoiceAccessories($invoice);
            return new Response($result);
        }
        exit;

    }

    public function deleteAccessoriesAction(DmsInvoiceAccessories $accessories){

        $em = $this->getDoctrine()->getManager();
        if (!$accessories) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }
        $em->remove($accessories);
        $em->flush();
        exit;
    }

    public function approvedAccessoriesAction(DmsInvoiceAccessories $accessories){

        $em = $this->getDoctrine()->getManager();
        if (!$accessories) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }
        $accessories->setStatus(1);
        $em->flush();
        $this->getDoctrine()->getRepository('DmsBundle:DmsParticular')->getSalesUpdateQnt($accessories);
        exit;
    }

}

