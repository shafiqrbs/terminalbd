<?php

namespace Appstore\Bundle\BusinessBundle\Controller;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoiceAccessories;
use Appstore\Bundle\HospitalBundle\Entity\InvoiceParticular;
use Knp\Snappy\Pdf;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoice;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoiceMedicine;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoiceParticular;
use Appstore\Bundle\BusinessBundle\Entity\BusinessParticular;
use Appstore\Bundle\BusinessBundle\Entity\BusinessTreatmentPlan;
use Appstore\Bundle\BusinessBundle\Form\InvoiceType;
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
 * BusinessInvoiceController controller.
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
        $businessConfig = $user->getGlobalOption()->getBusinessConfig();
        $entities = $em->getRepository('BusinessBundle:BusinessInvoice')->invoiceLists( $user,$data);
        $pagination = $this->paginate($entities);

        return $this->render('BusinessBundle:Invoice:index.html.twig', array(
            'entities' => $pagination,
            'salesTransactionOverview' => '',
            'previousSalesTransactionOverview' => '',
            'searchForm' => $data,
        ));

    }


    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new BusinessInvoice();
        $option = $this->getUser()->getGlobalOption();
        $businessConfig = $option->getBusinessConfig();
        if(!empty($patient)){
            $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $option,'id' => $patient));
            $entity->setCustomer($customer);
            $entity->setMobile($customer->getMobile());
        }
        $entity->setBusinessConfig($businessConfig);
        $entity->setPaymentStatus('Pending');
        $entity->setCreatedBy($this->getUser());
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('business_invoice_edit', array('id' => $entity->getId())));

    }

    /**
     * Creates a form to edit a Invoice entity.wq
     *
     * @param BusinessInvoice $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(BusinessInvoice $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $form = $this->createForm(new InvoiceType($globalOption,$location), $entity, array(
            'action' => $this->generateUrl('business_invoice_update', array('id' => $entity->getId())),
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




    /**
     * @Secure(roles="ROLE_DMS")
     */

    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $businessConfig = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entity = $em->getRepository('BusinessBundle:BusinessInvoice')->findOneBy(array('businessConfig' => $businessConfig , 'id' => $id));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $editForm = $this->createEditForm($entity);
        if (in_array($entity->getProcess(), array('Done','Canceled'))) {
            return $this->redirect($this->generateUrl('business_invoice_show', array('id' => $entity->getId())));
        }
        return $this->render('BusinessBundle:Invoice:new.html.twig', array(
            'entity' => $entity,
            'form' => $editForm->createView(),
        ));
    }

    /**
     * @Secure(roles="ROLE_DMS")
     */
    public function updateAction(Request $request, BusinessInvoice $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        $data = $request->request->all();
        $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoiceParticular')->insertInvoiceItems($entity,$data);
        if($editForm->isValid() and !empty($entity->getInvoiceParticulars())) {

            if (!empty($data['customer']['name'])) {

                $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['customer']['mobile']);
                $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findHmsExistingCustomerDiagnostic($this->getUser()->getGlobalOption(), $mobile, $data);
                $entity->setCustomer($customer);
                $entity->setMobile($mobile);
            }
            $amountInWords = $this->get('settong.toolManageRepo')->intToWords($entity->getTotal());
            $entity->setPaymentInWord($amountInWords);
            if(!empty($entity->getCustomer()) and  $entity->getProcess() == 'Created'){
                $entity->setProcess('Visit');
            }
            if (!in_array($entity->getProcess(), array('Canceled', 'Created')) and $entity->isSendSms() != 1) {
                /* @var $option GlobalOption */
                $option = $this->getUser()->getGlobalOption();
                if(!empty($option->getNotificationConfig()) and  !empty($option->getSmsSenderTotal()->getRemaining() > 0) and $option->getNotificationConfig()->getSmsActive() == 1 ) {
                    $dispatcher = $this->container->get('event_dispatcher');
                    $dispatcher->dispatch('setting_tool.post.business_invoice_sms', new \Setting\Bundle\ToolBundle\Event\BusinessInvoiceSmsEvent($entity));
                    $entity->setSendSms(1);
                }
            }
            $em->flush();
            $file = $request->files->all();
            if(!empty($file) and !empty($data['investigation'])){
                $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoiceParticular')->fileUpload($entity,$data,$file);
                $data = $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoiceParticular')->insertInvoiceInvestigationUpload($entity, $data);
                return new Response($data);
            }
        }
        exit;
    }
    /**
     * @Secure(roles="ROLE_DMS")
     */
    public function showAction(BusinessInvoice $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $businessConfig = $this->getUser()->getGlobalOption()->getBusinessConfig();
        if ($businessConfig->getId() == $entity->getBusinessConfig()->getId()) {
            return $this->render('BusinessBundle:Invoice:show.html.twig', array(
                'entity' => $entity,
            ));
        } else {
            return $this->redirect($this->generateUrl('business_invoice'));
        }

    }
    /**
     * @Secure(roles="ROLE_DMS")
     */
    public function deleteAction(BusinessInvoice $entity)
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

    public function particularSearchAction(BusinessParticular $particular)
    {
        return new Response(json_encode(array('particularId'=> $particular->getId() ,'price'=> $particular->getPrice() , 'quantity'=> $particular->getQuantity(), 'minimumPrice'=> $particular->getMinimumPrice(), 'instruction'=> $particular->getInstruction())));
    }

    public function returnResultData(BusinessInvoice $entity,$msg=''){

        $invoiceParticulars = $this->getDoctrine()->getRepository('BusinessBundle:BusinessTreatmentPlan')->getSalesItems($entity);
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

    public function particularProcedureAction(Request $request, BusinessInvoice $invoice,$service)
    {

        $em = $this->getDoctrine()->getManager();
        $procedure = $request->request->get('procedure');
        $teethNo = $request->request->get('teethNo');
        $invoiceItems = array('service'=> $service, 'procedure' => $procedure ,'teethNo' => $teethNo);
        $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoiceParticular')->insertInvoiceParticularSingle($invoice, $invoiceItems);
        $data = $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoiceParticular')->insertInvoiceParticularReturn($invoice, $invoiceItems);
        return new Response($data);
        exit;

    }

    public function addParticularAction(Request $request, BusinessInvoice $invoice)
    {

        $em = $this->getDoctrine()->getManager();
        $particularId = $request->request->get('particularId');
        $price = $request->request->get('price');
        $appointmentDate = $request->request->get('appointmentDate');
        $appointmentTime = $request->request->get('appointmentTime');
        $invoiceItems = array('particularId' => $particularId , 'quantity' => 1,'price' => $price,'appointmentDate'=>$appointmentDate , 'appointmentTime'=> $appointmentTime );
        $this->getDoctrine()->getRepository('BusinessBundle:BusinessTreatmentPlan')->insertInvoiceItems($invoice, $invoiceItems);
        $invoice = $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoice')->updateInvoiceTotalPrice($invoice);
        $msg = 'Particular added successfully';
        $result = $this->returnResultData($invoice,$msg);
        return new Response(json_encode($result));
        exit;

    }

    public function treatmentApprovedAction(BusinessTreatmentPlan $treatmentPlan){


        $em = $this->getDoctrine()->getManager();
        if (!$treatmentPlan) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }
        if($treatmentPlan->getPayment() > 0){
            $treatmentPlan->setStatus(true);
            $method = $this->getDoctrine()->getRepository('SettingToolBundle:TransactionMethod')->find(1);
            $treatmentPlan->setTransactionMethod($method);
            $em->flush();
            $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoice')->updatePaymentReceive($treatmentPlan->getBusinessInvoice());
            $em->getRepository('AccountingBundle:AccountCash')->dmsInsertSalesCash($treatmentPlan);
            $em->getRepository('AccountingBundle:Transaction')->dmsTransaction($treatmentPlan);
            return new Response('success');
        }
        return new Response('failed');
        exit;
    }

    public function inlineUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BusinessBundle:BusinessTreatmentPlan')->find($data['pk']);
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

    public function treatmentDeleteAction(BusinessInvoice $invoice ,BusinessTreatmentPlan $treatmentPlan){

        $em = $this->getDoctrine()->getManager();
        if (!$treatmentPlan) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }
        $em->remove($treatmentPlan);
        $em->flush();
        $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoice')->updateInvoiceTotalPrice($invoice);
        $result = $this->returnResultData($invoice);
        return new Response(json_encode($result));
        exit;
    }

    public function treatmentAppointmentDatetimeAction(Request $request){

        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $appointmentDate = $data['appointmentDate'];
        $appointmentTime = $data['appointmentTime'];
        $entity = $this->getDoctrine()->getRepository('BusinessBundle:BusinessTreatmentPlan')->findOneBy(array('status'=> 0 ,'appointmentTime'=>$appointmentTime,'appointmentDate'=> $appointmentDate ));
        if($entity){
            $res = 'invalid';
        }
        $res = 'valid';
        return new Response($res);
        exit;
    }

    public function invoiceParticularDeleteAction( $invoice, BusinessInvoiceParticular $particular){


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

    public function addMedicineAction(Request $request, BusinessInvoice $invoice)
    {

        $em = $this->getDoctrine()->getManager();
        $particular = $request->request->get('particular');
        $quantity = $request->request->get('quantity');
        $price = $request->request->get('price');
        $unit = $request->request->get('unit');
        if(!empty($medicine)  OR $medicineId > 0){
            $invoiceItems = array('medicine' => $medicine ,'medicineId' => $medicineId , 'generic' => $generic,'medicineQuantity' => $medicineQuantity,'medicineDose' => $medicineDose,'medicineDoseTime' => $medicineDoseTime ,'medicineDuration' => $medicineDuration,'medicineDurationType' => $medicineDurationType);
            $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoiceMedicine')->insertInvoiceMedicine($invoice, $invoiceItems);
            $result = $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoiceMedicine')->getInvoiceMedicines($invoice);
            return new Response($result);
        }
        exit;

    }

    public function deleteMedicineAction(BusinessInvoice $invoice){


        $em = $this->getDoctrine()->getManager();
        if (!$invoice) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }
        $em->remove($invoice);
        $em->flush();
        exit;
    }

    public function addPaymentAction(Request $request , BusinessInvoice $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $invoiceParticular = $request->request->get('invoiceParticular');
        $payment = $request->request->get('payment');
        $discount = $request->request->get('discount');
        $discount = $discount !="" ? $discount : 0 ;
        $process = $request->request->get('process');

        if ( (!empty($entity) and !empty($payment)) or (!empty($entity) and $discount > 0 ) ) {
            $em = $this->getDoctrine()->getManager();
            $entity->setProcess('In-progress');
            $em->flush();
            $transactionData = array('process'=> 'In-progress','invoiceParticular' => $invoiceParticular,'payment' => $payment, 'discount' => $discount);
            $this->getDoctrine()->getRepository('BusinessBundle:BusinessTreatmentPlan')->insertPaymentTransaction($transactionData);
            $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoice')->updateInvoiceTotalPrice($entity);
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

    public function confirmAction(BusinessInvoice $entity)
    {
        $inventory = $this->getUser()->getGlobalOption()->getBusinessConfig()->getId();
        if ($inventory == $entity->getBusinessConfig()->getId()) {
            return $this->render('BusinessBundle:BusinessInvoice:confirm.html.twig', array(
                'entity' => $entity,
            ));
        } else {
            return $this->redirect($this->generateUrl('business_invoice'));
        }

    }

    public function approveAction(Request $request , BusinessInvoice $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $payment = $request->request->get('payment');
        $discount = $request->request->get('discount');
        $discount = $discount !="" ? $discount : 0 ;
        $process = $request->request->get('process');

        if ( (!empty($entity) and !empty($payment)) or (!empty($entity) and $discount > 0 ) ) {
            $em = $this->getDoctrine()->getManager();
            $entity->setProcess($process);
            $em->flush();
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

    public function invoiceReverseAction(BusinessInvoice $invoice)
    {
        $businessConfig = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entity = $this->getDoctrine()->getRepository('BusinessBundle:HmsReverse')->findOneBy(array('businessConfig' => $businessConfig, 'hmsInvoice' => $invoice));
        return $this->render('BusinessBundle:Reverse:show.html.twig', array(
            'entity' => $entity,
        ));

    }

    public function invoiceReverseShowAction(BusinessInvoice $invoice)
    {
        $businessConfig = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entity = $this->getDoctrine()->getRepository('BusinessBundle:HmsReverse')->findOneBy(array('businessConfig' => $businessConfig, 'hmsInvoice' => $invoice));
        return $this->render('BusinessBundle:Reverse:show.html.twig', array(
            'entity' => $entity,
        ));

    }

    /**
     * @Secure(roles="ROLE_DMS")
     */

    public function deleteEmptyInvoiceAction()
    {
        $businessConfig = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entities = $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoice')->findBy(array('businessConfig' => $businessConfig, 'process' => 'Created'));
        $em = $this->getDoctrine()->getManager();
        foreach ($entities as $entity) {
            $em->remove($entity);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('business_invoice'));
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
        $entity = $em->getRepository('BusinessBundle:BusinessInvoice')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $entity->setProcess($data['value']);
        $em->flush();
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

    public function invoicePrintAction(BusinessInvoice $entity)
    {

        $em = $this->getDoctrine()->getManager();
        $businessConfig = $this->getUser()->getGlobalOption()->getBusinessConfig();
        if ($businessConfig->getId() == $entity->getBusinessConfig()->getId()) {

            /** @var  $invoiceParticularArr */
            $invoiceParticularArr = array();

            /** @var $row BusinessInvoiceParticular  */
            if (!empty($entity->getInvoiceParticulars())) {
                foreach ($entity->getInvoiceParticulars() as $row):
                    if (!empty($row->getBusinessParticular())) {
                        $invoiceParticularArr[$row->getBusinessParticular()->getId()] = $row;
                    }
                endforeach;
            }

            $services = $em->getRepository('BusinessBundle:BusinessService')->findBy(array('businessConfig'=>$businessConfig,'serviceShow'=>1,'status'=>1),array('serviceSorting'=>'ASC'));
            $treatmentSchedule = $em->getRepository('BusinessBundle:BusinessTreatmentPlan')->findTodaySchedule($businessConfig);

            if($businessConfig->isCustomPrescription() == 1){
                $template = $businessConfig->getGlobalOption()->getSlug();
            }else{
                $template = 'print';
            }

            return  $this->render('BusinessBundle:Print:'.$template.'.html.twig',
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

    public function invoicePrintPreviewAction(BusinessInvoice $entity)
    {

        $em = $this->getDoctrine()->getManager();
        $businessConfig = $this->getUser()->getGlobalOption()->getBusinessConfig();
        if ($businessConfig->getId() == $entity->getBusinessConfig()->getId()) {

            /** @var  $invoiceParticularArr */
            $invoiceParticularArr = array();

            /** @var $row BusinessInvoiceParticular  */
            if (!empty($entity->getInvoiceParticulars())) {
                foreach ($entity->getInvoiceParticulars() as $row):
                    if (!empty($row->getBusinessParticular())) {
                        $invoiceParticularArr[$row->getBusinessParticular()->getId()] = $row;
                    }
                endforeach;
            }

            $services = $em->getRepository('BusinessBundle:BusinessService')->findBy(array('businessConfig'=>$businessConfig,'serviceShow'=>1,'status'=>1),array('serviceSorting'=>'ASC'));
            $treatmentSchedule = $em->getRepository('BusinessBundle:BusinessTreatmentPlan')->findTodaySchedule($businessConfig);

            if($businessConfig->isCustomPrescription() == 1){
                $template = $businessConfig->getGlobalOption()->getSlug();
            }else{
                $template = 'print';
            }
            $html =  $this->renderView('BusinessBundle:Print:'.$template.'.html.twig',
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

    public function invoicePrintPdfAction(BusinessInvoice $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $businessConfig = $this->getUser()->getGlobalOption()->getBusinessConfig();
        if ($businessConfig->getId() == $entity->getBusinessConfig()->getId()) {

            /** @var  $invoiceParticularArr */
            $invoiceParticularArr = array();

            /** @var $row BusinessInvoiceParticular */
            if (!empty($entity->getInvoiceParticulars())) {
                foreach ($entity->getInvoiceParticulars() as $row):
                    if (!empty($row->getBusinessParticular())) {
                        $invoiceParticularArr[$row->getBusinessParticular()->getId()] = $row;
                    }
                endforeach;
            }

            $services = $em->getRepository('BusinessBundle:BusinessService')->findBy(array('businessConfig' => $businessConfig, 'serviceShow' => 1, 'status' => 1), array('serviceSorting' => 'ASC'));
            $treatmentSchedule = $em->getRepository('BusinessBundle:BusinessTreatmentPlan')->findTodaySchedule($businessConfig);

            if ($businessConfig->isCustomPrescription() == 1) {
                $template = $businessConfig->getGlobalOption()->getSlug();
            } else {
                $template = 'print';
            }

            $html = $this->renderView(
                'BusinessBundle:Print:dental-care.html.twig', array(
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

    public function procedureSearchAction()
    {
        $q = $_REQUEST['term'];
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entities = $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoiceParticular')->searchAutoComplete($config,$q);
        $items = array();
        foreach ($entities as $entity):
            $items[]=array('value' => $entity['id']);
        endforeach;
        return new JsonResponse($items);

    }

    public function autoParticularSearchAction()
    {
        $q = $_REQUEST['term'];
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entities = $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoiceParticular')->searchAutoComplete($config,$q);
        $items = array();
        foreach ($entities as $entity):
            $items[]=array('value' => $entity['id']);
        endforeach;
        return new JsonResponse($items);

    }

    public function autoUnitSearchAction()
    {
        $q = $_REQUEST['term'];
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entities = $this->getDoctrine()->getRepository('SettingToolBundle:ProductUnit')->searchAutoComplete($config,$q);
        $items = array();
        foreach ($entities as $entity):
            $items[]=array('value' => $entity['id']);
        endforeach;
        return new JsonResponse($items);

    }

    public function addAccessoriesAction(Request $request, BusinessInvoice $invoice)
    {

        $em = $this->getDoctrine()->getManager();
        $accessories = $request->request->get('accessories');
        $quantity = $request->request->get('quantity');
        if(!empty($accessories)){
            $invoiceItems = array('accessories' => $accessories ,'quantity' => $quantity);
            $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoiceAccessories')->insertInvoiceAccessories($invoice, $invoiceItems);
            $result = $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoiceAccessories')->getInvoiceAccessories($invoice);
            return new Response($result);
        }
        exit;

    }

    public function deleteAccessoriesAction(BusinessInvoiceAccessories $accessories){

        $em = $this->getDoctrine()->getManager();
        if (!$accessories) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }
        $em->remove($accessories);
        $em->flush();
        exit;
    }

    public function approvedAccessoriesAction(BusinessInvoiceAccessories $accessories){

        $em = $this->getDoctrine()->getManager();
        if (!$accessories) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }
        $accessories->setStatus(1);
        $em->flush();
        $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticular')->getSalesUpdateQnt($accessories);
        exit;
    }

}

