<?php

namespace Appstore\Bundle\HospitalBundle\Controller;

use Appstore\Bundle\HospitalBundle\Entity\Invoice;
use Appstore\Bundle\HospitalBundle\Entity\InvoiceParticular;
use Appstore\Bundle\HospitalBundle\Entity\Particular;
use Appstore\Bundle\HospitalBundle\Form\InvoiceAdmissionType;
use Appstore\Bundle\HospitalBundle\Form\InvoiceType;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use Frontend\FrontentBundle\Service\MobileDetect;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Invoice controller.
 *
 */
class InvoiceAdmissionController extends Controller
{

    public function paginate($entities)
    {

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            25  /*limit per page*/
        );
        return $pagination;
    }

    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;

        $user = $this->getUser();
        $hospital = $user->getGlobalOption()->getHospitalConfig();
        $entities = $em->getRepository('HospitalBundle:Invoice')->invoiceLists( $user,$mode ='admission', $data);
        $pagination = $this->paginate($entities);

        $salesTransactionOverview = $em->getRepository('HospitalBundle:InvoiceTransaction')->todaySalesOverview($user,$data,'true','admission');
        $previousSalesTransactionOverview = $em->getRepository('HospitalBundle:InvoiceTransaction')->todaySalesOverview($user,$data,'false','admission');

        $assignDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->getFindWithParticular($hospital,array(5));
        $referredDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->getFindWithParticular($hospital,array(6));
        $cabins = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->getFindWithParticular($hospital,array(2));
        $cabinGroups = $this->getDoctrine()->getRepository('HospitalBundle:HmsServiceGroup')->findBy(array('hospitalConfig'=>$hospital,'service'=>2),array('name'=>'ASC'));

        return $this->render('HospitalBundle:InvoiceAdmission:index.html.twig', array(
            'entities' => $pagination,
            'salesTransactionOverview' => $salesTransactionOverview,
            'previousSalesTransactionOverview' => $previousSalesTransactionOverview,
            'assignDoctors' => $assignDoctors,
            'referredDoctors' => $referredDoctors,
            'cabinGroups' => $cabinGroups,
            'cabins' => $cabins,
            'searchForm' => $data,
        ));

    }


    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new Invoice();
        $hospital = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $entity->setHospitalConfig($hospital);
        $service = $this->getDoctrine()->getRepository('HospitalBundle:Service')->find(1);
        $entity->setService($service);
        $referredDoctor = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->findOneBy(array('hospitalConfig' => $hospital,'name'=>'Self','service' => 6));
        $entity->setReferredDoctor($referredDoctor);
        $transactionMethod = $em->getRepository('SettingToolBundle:TransactionMethod')->find(1);
        $entity->setTransactionMethod($transactionMethod);
        $entity->setPaymentStatus('Pending');
        $entity->setInvoiceMode('admission');
        $entity->setPrintFor('admission');
        $entity->setCreatedBy($this->getUser());
        if(!empty($this->getUser()->getProfile()->getBranches())){
            $entity->setBranches($this->getUser()->getProfile()->getBranches());
        }
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('hms_invoice_admission_edit', array('id' => $entity->getId())));

    }


    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $hospital = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $entity = $em->getRepository('HospitalBundle:Invoice')->findOneBy(array('hospitalConfig' => $hospital , 'id' => $id));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }

        $editForm = $this->createEditForm($entity);
        if ($entity->getProcess() != "Admitted" and $entity->getProcess() != "Created") {
            return $this->redirect($this->generateUrl('hms_invoice_admission_show', array('id' => $entity->getId())));
        }
        $services        = $em->getRepository('HospitalBundle:Particular')->getServices($hospital,array(2,3,4,8,7));
        $referredDoctors = $em->getRepository('HospitalBundle:Particular')->findBy(array('hospitalConfig' => $hospital,'status' => 1,'service' => 6),array('name'=>'ASC'));
        return $this->render('HospitalBundle:InvoiceAdmission:new.html.twig', array(
            'entity' => $entity,
            'particularService' => $services,
            'referredDoctors' => $referredDoctors,
            'admissionForm' => 'hide',
            'form' => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Invoice entity.wq
     *
     * @param Invoice $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Invoice $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $category = $this->getDoctrine()->getRepository('HospitalBundle:HmsCategory');
        $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $form = $this->createForm(new InvoiceAdmissionType($globalOption,$category ,$location), $entity, array(
            'action' => $this->generateUrl('hms_invoice_admission_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'id' => 'invoiceForm',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    public function updateAction(Request $request, Invoice $entity)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        $referredId = $request->request->get('referredId');
        $data = $request->request->all()['appstore_bundle_hospitalbundle_invoice'];
        if ($editForm->isValid()) {

            if (!empty($data['customer']['name'])) {

                 $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['customer']['mobile']);
                 $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findHmsExistingCustomer($this->getUser()->getGlobalOption(), $mobile,$data);
                 $entity->setCustomer($customer);
                 $entity->setMobile($mobile);

            }
            if(!empty($data['referredDoctor']['name']) && !empty($data['referredDoctor']['mobile'])) {

                $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['referredDoctor']['mobile']);
                $referred = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->findHmsExistingCustomer($entity->getHospitalConfig() , $mobile,$data);
                $entity->setReferredDoctor($referred);
            }else{
                $referred = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->findOneBy(array('hospitalConfig' => $entity->getHospitalConfig() , 'service' => 6, 'id' => $referredId ));
                $entity->setReferredDoctor($referred);
            }
            if($entity->getTotal() > 0 ) {
                $amountInWords = $this->get('settong.toolManageRepo')->intToWords($entity->getTotal());
                $entity->setProcess('Admitted');
                $entity->setPrintFor('admitted');
                $entity->setPaymentInWord($amountInWords);
            }
            $em->flush();

            if($entity->getTotal() > 0 ){

                $transaction = $this->getDoctrine()->getRepository('HospitalBundle:InvoiceTransaction')->initialInsertInvoiceTransaction($entity);
                $this->getDoctrine()->getRepository('HospitalBundle:AdmissionPatientParticular')->initialUpdateInvoiceParticulars($entity,$transaction);
                $this->getDoctrine()->getRepository('HospitalBundle:Particular')->insertAccessories($entity);
                $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->updatePaymentReceive($entity);
            }

            if(!empty($this->getUser()->getGlobalOption()->getNotificationConfig()) and  !empty($this->getUser()->getGlobalOption()->getSmsSenderTotal())) {
                 $dispatcher = $this->container->get('event_dispatcher');
                 $dispatcher->dispatch('setting_tool.post.hms_invoice_sms', new \Setting\Bundle\ToolBundle\Event\HmsInvoiceSmsEvent($entity));
            }
            if(!empty($entity->getInvoiceParticulars()) and count($entity->getInvoiceParticulars()) > 0){
                return $this->redirect($this->generateUrl('hms_invoice_admission_confirm', array('id' => $entity->getId())));
            }
            return $this->redirect($this->generateUrl('hms_invoice_admission_edit', array('id' => $entity->getId())));
        }

        $referredDoctors = $em->getRepository('HospitalBundle:Particular')->findBy(array('hospitalConfig' => $entity->getHospitalConfig(),'status'=>1,'service'=> 6),array('name'=>'ASC'));
        $particulars = $em->getRepository('HospitalBundle:Particular')->getServices($entity->getHospitalConfig(),array(2,3,4,8,7));
        return $this->render('HospitalBundle:InvoiceAdmission:new.html.twig', array(
            'entity' => $entity,
            'particularService' => $particulars,
            'referredDoctors' => $referredDoctors,
            'admissionForm' => 'show',
            'form' => $editForm->createView(),
        ));
    }

    public function particularSearchAction(Particular $particular)
    {
        $quantity = $particular->getQuantity() > 0 ? $particular->getQuantity() :1;
        return new Response(json_encode(array('particularId'=> $particular->getId() ,'price'=> $particular->getPrice() , 'quantity'=> $quantity, 'minimumPrice'=> $particular->getMinimumPrice(), 'instruction'=> $particular->getInstruction())));
    }

    public function addParticularAction(Request $request, Invoice $invoice)
    {
    }

    public function invoiceParticularDeleteAction(Invoice $invoice, InvoiceParticular $particular){
    }

    public function invoiceDiscountUpdateAction(Request $request)
    {
    }
    public function deleteAction(Request $request)
    {
    }

    public function approveAction(Request $request , Invoice $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $payment = $request->request->get('payment');
        $discount = $request->request->get('discount');
        $discount = $discount !="" ? $discount : 0 ;
        $process = $request->request->get('process');

        if ((!empty($entity) and !empty($payment)) or !empty($entity) and !empty($discount)) {

            $em = $this->getDoctrine()->getManager();
            $entity->setProcess('Admitted');
            $em->flush();
            $transactionData = array('process'=> 'In-progress','payment' => $payment, 'discount' => $discount);
            $this->getDoctrine()->getRepository('HospitalBundle:InvoiceTransaction')->insertPaymentTransaction($entity,$transactionData);
            return new Response('success');
        } elseif (!empty($entity) and in_array($process , array('Release','Death')) and $entity->getTotal() <= $entity->getPayment() ) {
            $em = $this->getDoctrine()->getManager();
            $entity->setProcess($process);
            $em->flush();
            return new Response('success');
        } else {
            return new Response('failed');
        }
        exit;
    }



    public function showAction(Invoice $entity)
    {
        $inventory = $this->getUser()->getGlobalOption()->getHospitalConfig()->getId();
        if ($inventory == $entity->getHospitalConfig()->getId()) {
            return $this->render('HospitalBundle:InvoiceAdmission:show.html.twig', array(
                'entity' => $entity,
            ));
        } else {
            return $this->redirect($this->generateUrl('hms_invoice_admission'));
        }

    }

    public function confirmAction(Invoice $entity)
    {
        $inventory = $this->getUser()->getGlobalOption()->getHospitalConfig()->getId();
        if ($inventory == $entity->getHospitalConfig()->getId()) {
            return $this->render('HospitalBundle:InvoiceAdmission:confirm.html.twig', array(
                'entity' => $entity,
            ));
        } else {
            return $this->redirect($this->generateUrl('hms_invoice_admission'));
        }
    }

    public function releaseAction($invoice,$process)
    {
        $em = $this->getDoctrine()->getManager();
        $inventory = $this->getUser()->getGlobalOption()->getHospitalConfig()->getId();
        $entity = $em->getRepository('HospitalBundle:Invoice')->findOneBy(array('hospitalConfig'=>$inventory,'invoice'=>$invoice));
        $entity->setApprovedBy($this->getUser());
        if($process == 'confirm'){
            if($entity->getProcess() == 'Release'){
                $entity->setProcess('Released');
            }elseif($entity->getProcess() == 'Death'){
                $entity->setProcess('Dead');
            }
            $this->getDoctrine()->getRepository('HospitalBundle:InvoiceTransaction')->removePendingTransaction($entity);
        }elseif($process == 'cancel'){
            $entity->setProcess('Admitted');
        }

        $em->flush();
        exit;
    }

    public function admittedAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;

        $user = $this->getUser();
        $hospital = $user->getGlobalOption()->getHospitalConfig();
        $entities = $em->getRepository('HospitalBundle:Invoice')->invoiceLists( $user,$mode ='admission', $data);
        $pagination = $this->paginate($entities);
        $overview = $em->getRepository('HospitalBundle:DoctorInvoice')->findWithOverview($user,$data);
        $invoiceOverview = $em->getRepository('HospitalBundle:Invoice')->findWithOverview($user,$data);

        $assignDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->getFindWithParticular($hospital,array(5));
        $referredDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->getFindWithParticular($hospital,array(6));

        return $this->render('HospitalBundle:InvoiceAdmission:admitted.html.twig', array(
            'entities' => $pagination,
            'invoiceOverview' => $invoiceOverview,
            'overview' => $overview,
            'assignDoctors' => $assignDoctors,
            'referredDoctors' => $referredDoctors,
            'searchForm' => $data,
        ));

    }


    public function getBarcode($invoice)
    {
        $barcode = new BarcodeGenerator();
        $barcode->setText($invoice);
        $barcode->setType(BarcodeGenerator::Code39Extended);
        $barcode->setScale(1);
        $barcode->setThickness(25);
        $barcode->setFontSize(8);
        $code = $barcode->generate();
        $data = '';
        $data .= '<img src="data:image/png;base64,'.$code .'" />';
        return $data;
    }

    public function deleteEmptyInvoiceAction()
    {
        $hospital = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $entities = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->findBy(array('hospitalConfig' => $hospital, 'process' => 'Created','invoiceMode'=>'admission'));
        $em = $this->getDoctrine()->getManager();
        foreach ($entities as $entity) {
            $em->remove($entity);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('hms_invoice_admission'));
    }


    public function invoicePrintAction(Invoice $entity)
    {

        $barcode = $this->getBarcode($entity->getInvoice());
        $patientId = $this->getBarcode($entity->getCustomer()->getCustomerId());
        $inWords = $this->get('settong.toolManageRepo')->intToWords($entity->getPayment());

        $invoiceDetails = ['Pathology' => ['items' => [], 'total'=> 0, 'hasQuantity' => false ]];

        foreach ($entity->getInvoiceParticulars() as $item) {

            /** @var InvoiceParticular $item */
            $serviceName = $item->getParticular()->getService()->getName();
            $hasQuantity = $item->getParticular()->getService()->getHasQuantity();

            if(!isset($invoiceDetails[$serviceName])) {
                $invoiceDetails[$serviceName]['items'] = [];
                $invoiceDetails[$serviceName]['total'] = 0;
                $invoiceDetails[$serviceName]['hasQuantity'] = ( $hasQuantity == 1);
            }

            $invoiceDetails[$serviceName]['items'][] = $item;
            $invoiceDetails[$serviceName]['total'] += $item->getSubTotal();
        }

        if(count($invoiceDetails['Pathology']['items']) == 0) {
            unset($invoiceDetails['Pathology']);
        }
        return $this->render('HospitalBundle:Print:'.$entity->getPrintFor().'.html.twig', array(
            'entity'      => $entity,
            'invoiceDetails'      => $invoiceDetails,
            'invoiceBarcode'     => $barcode,
            'patientBarcode'     => $patientId,
            'inWords'           => $inWords,
        ));
    }

    public function invoicePrintForAction(Request $request, Invoice $invoice)
    {
        $em = $this->getDoctrine()->getManager();
        $print = $request->request->get('printFor');
        $invoice->setPrintFor($print);
        $em->persist($invoice);
        $em->flush();
        exit;
    }

    public function checkPatientCabinBookingAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $cabin = $request->request->get('cabin');
        $invoice = $request->request->get('invoice');
        $status = $em->getRepository('HospitalBundle:Invoice')->checkCabinBooking($invoice,$cabin);
        echo $status;
        exit;
    }


    public function admissionInvoiceReverseAction($invoice){

        $em = $this->getDoctrine()->getManager();
        $hospital = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $entity = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->findOneBy(array('hospitalConfig' => $hospital, 'invoice' => $invoice));
        $em->getRepository('HospitalBundle:InvoiceTransaction')->hmsAdmissionSalesTransactionReverse($entity);
        $em->getRepository('HospitalBundle:InvoiceParticular')->hmsInvoiceParticularReverse($entity);
        $em = $this->getDoctrine()->getManager();
        $entity->setRevised(true);
        $entity->setTotal($entity->getSubTotal());
        $entity->setProcess('Admitted');
        $entity->setPaymentStatus('Due');
        $entity->setDiscount(null);
        $entity->setPaymentInWord(null);
        $entity->setPayment(null);
        $em->flush();
        $template = $this->get('twig')->render('HospitalBundle:Reverse:admission.html.twig',array(
            'entity' => $entity,
        ));
        $em->getRepository('HospitalBundle:HmsReverse')->insertAdmissionInvoice($entity,$template);
        return $this->redirect($this->generateUrl('hms_invoice_admission_confirm',array('id' => $entity->getId())));

    }

    public function admissionInvoiceReverseShowAction($invoice)
    {

        $hospital = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $admission = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->findOneBy(array('hospitalConfig' => $hospital, 'invoice' => $invoice));
        $entity = $this->getDoctrine()->getRepository('HospitalBundle:HmsReverse')->findOneBy(array('hospitalConfig' => $hospital, 'hmsInvoice' => $admission->getId()));
        return $this->render('HospitalBundle:Reverse:admissionShow.html.twig', array(
            'entity' => $entity
        ));

    }

    public function invoiceReverseShowAction(Invoice $invoice)
    {
        $hospital = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $entity = $this->getDoctrine()->getRepository('HospitalBundle:HmsReverse')->findOneBy(array('hospitalConfig' => $hospital, 'hmsInvoice' => $invoice));
        return $this->render('HospitalBundle:Reverse:show.html.twig', array(
            'entity' => $entity,
        ));

    }
}

