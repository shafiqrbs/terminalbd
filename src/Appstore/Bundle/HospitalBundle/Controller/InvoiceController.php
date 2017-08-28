<?php

namespace Appstore\Bundle\HospitalBundle\Controller;

use Appstore\Bundle\HospitalBundle\Entity\Invoice;
use Appstore\Bundle\HospitalBundle\Entity\InvoiceParticular;
use Appstore\Bundle\HospitalBundle\Entity\Particular;
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
use Hackzilla\BarcodeBundle\Utility\Barcode;
/**
 * Invoice controller.
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
        return $pagination;
    }

    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;

        $user = $this->getUser();
        $hospital = $user->getGlobalOption()->getHospitalConfig();
        $entities = $em->getRepository('HospitalBundle:Invoice')->invoiceLists( $user , $mode = 'pathology' , $data);
        $pagination = $this->paginate($entities);
        $overview = $em->getRepository('HospitalBundle:DoctorInvoice')->findWithOverview($user,$data);
        $invoiceOverview = $em->getRepository('HospitalBundle:Invoice')->findWithOverview($user,$data);

        $assignDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->getFindWithParticular($hospital,array(5));
        $referredDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->getFindWithParticular($hospital,array(6));

        return $this->render('HospitalBundle:Invoice:index.html.twig', array(
            'entities' => $pagination,
            'invoiceOverview' => $invoiceOverview,
            'overview' => $overview,
            'assignDoctors' => $assignDoctors,
            'referredDoctors' => $referredDoctors,
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
        $entity->setInvoiceMode('pathology');
        $entity->setCreatedBy($this->getUser());
        if(!empty($this->getUser()->getProfile()->getBranches())){
            $entity->setBranches($this->getUser()->getProfile()->getBranches());
        }
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('hms_invoice_edit', array('id' => $entity->getId())));

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
        if ($entity->getProcess() != "In-progress" and $entity->getProcess() != "Created") {
            return $this->redirect($this->generateUrl('hms_invoice_show', array('id' => $entity->getId())));
        }
        $particulars = $em->getRepository('HospitalBundle:Particular')->getServiceWithParticular($hospital);
        $referredDoctors = $em->getRepository('HospitalBundle:Particular')->findBy(array('hospitalConfig' => $hospital,'status' => 1,'service' => 6),array('name'=>'ASC'));
        return $this->render('HospitalBundle:Invoice:new.html.twig', array(
            'entity' => $entity,
            'particulars' => $particulars,
            'referredDoctors' => $referredDoctors,
            'form' => $editForm->createView(),
        ));
    }

    public function particularSearchAction(Particular $particular)
    {
        return new Response(json_encode(array('particularId'=> $particular->getId() ,'price'=> $particular->getPrice() , 'quantity'=> $particular->getQuantity(), 'minimumPrice'=> $particular->getMinimumPrice(), 'instruction'=> $particular->getInstruction())));
    }

    public function returnResultData(Invoice $entity,$msg=''){

        $invoiceParticulars = $this->getDoctrine()->getRepository('HospitalBundle:InvoiceParticular')->getSalesItems($entity);
        $invoiceTransaction = $this->getDoctrine()->getRepository('HospitalBundle:InvoiceTransaction')->getInvoiceTransactionItems($entity);

        $subTotal = $entity->getSubTotal() > 0 ? $entity->getSubTotal() : 0;
        $netTotal = $entity->getNetTotal() > 0 ? $entity->getNetTotal() : 0;
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
           'invoiceTransaction' => $invoiceTransaction,
           'invoiceParticulars' => $invoiceParticulars ,
           'msg' => $msg ,
           'success' => 'success'
       );

       return $data;

    }

    public function addParticularAction(Request $request, Invoice $invoice)
    {

        $em = $this->getDoctrine()->getManager();
        $particularId = $request->request->get('particularId');
        $quantity = $request->request->get('quantity');
        $price = $request->request->get('price');
        $invoiceItems = array('particularId' => $particularId , 'quantity' => $quantity,'price' => $price );
        $this->getDoctrine()->getRepository('HospitalBundle:InvoiceParticular')->insertInvoiceItems($invoice, $invoiceItems);
        $invoice = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->updateInvoiceTotalPrice($invoice);
        $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->updatePaymentReceive($invoice);

        $msg = 'Particular added successfully';
        $result = $this->returnResultData($invoice,$msg);
        return new Response(json_encode($result));
        exit;

    }

    public function invoiceParticularDeleteAction( $invoice, InvoiceParticular $particular){


        $em = $this->getDoctrine()->getManager();
        if (!$particular) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }
        $em->remove($particular);
        $em->flush();
        $entity =  $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->find($invoice);
        $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->updateInvoiceTotalPrice($entity);
        $this->getDoctrine()->getRepository('HospitalBundle:InvoiceTransaction')->updateInvoiceTransactionDiscount($entity);
        $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->updatePaymentReceive($entity);

        $msg = 'Particular deleted successfully';
        $result = $this->returnResultData($entity,$msg);
        return new Response(json_encode($result));
        exit;




    }

    public function invoiceDiscountUpdateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $discount = $request->request->get('discount');
        $invoice = $request->request->get('invoice');

        $entity = $em->getRepository('HospitalBundle:Invoice')->find($invoice);
        $total = ($entity->getSubTotal() - $discount);
        if($total > $discount && $discount > 0 ){
            $em->persist($entity);
            $em->flush();

            $discount = $request->request->get('discount');
            $transactionData = array('payment' => 0, 'discount' => $discount);
            $this->getDoctrine()->getRepository('HospitalBundle:InvoiceTransaction')->insertTransaction($entity, $transactionData);
            $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->updatePaymentReceive($entity);
        }

        $msg = 'Discount added successfully';
        $result = $this->returnResultData($entity,$msg);
        return new Response(json_encode($result));
        exit;


    }

    public function discountDeleteAction(Invoice $entity)
    {

        $this->getDoctrine()->getRepository('HospitalBundle:InvoiceTransaction')->updateInvoiceTransactionDiscount($entity);
        $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->updatePaymentReceive($entity);

        $msg = 'Discount deleted successfully';
        $result = $this->returnResultData($entity,$msg);
        return new Response(json_encode($result));
        exit;
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
            
            $entity->setProcess('In-progress');
            $amountInWords = $this->get('settong.toolManageRepo')->intToWords($entity->getTotal());
            $entity->setPaymentInWord($amountInWords);
            $em->flush();

            $payment = $data['payment'];
            if($payment > 0) {

                $transactionData = array('payment' => $payment, 'discount' => 0);
                $this->getDoctrine()->getRepository('HospitalBundle:InvoiceTransaction')->insertTransaction($entity, $transactionData);
                $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->updatePaymentReceive($entity);
            }

            if(!empty($this->getUser()->getGlobalOption()->getNotificationConfig()) and  !empty($this->getUser()->getGlobalOption()->getSmsSenderTotal())) {
                $dispatcher = $this->container->get('event_dispatcher');
                $dispatcher->dispatch('setting_tool.post.hms_invoice_sms', new \Setting\Bundle\ToolBundle\Event\HmsInvoiceSmsEvent($entity));
            }
            return $this->redirect($this->generateUrl('hms_invoice_show', array('id' => $entity->getId())));
        }

        $referredDoctors = $em->getRepository('HospitalBundle:Particular')->findBy(array('hospitalConfig' => $entity->getHospitalConfig(),'status'=>1,'service'=> 6),array('name'=>'ASC'));
        $particulars = $em->getRepository('HospitalBundle:Particular')->getServiceWithParticular($entity->getHospitalConfig());
        return $this->render('HospitalBundle:Invoice:new.html.twig', array(
            'entity' => $entity,
            'particulars' => $particulars,
            'referredDoctors' => $referredDoctors,
            'form' => $editForm->createView(),
        ));
    }

    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $sales = $request->request->get('sales');
        $barcode = $request->request->get('barcode');
        $sales = $em->getRepository('HospitalBundle:Invoice')->find($sales);
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $purchaseItem = $em->getRepository('HospitalBundle:PurchaseItem')->returnPurchaseItemDetails($inventory, $barcode);
        $checkQuantity = $this->getDoctrine()->getRepository('HospitalBundle:InvoiceItem')->checkInvoiceQuantity($purchaseItem);
        $itemStock = $purchaseItem->getItemStock();

        /* Device Detection code desktop or mobile */
        $detect = new MobileDetect();
        $device = '';
        if( $detect->isMobile() || $detect->isTablet() ) {
            $device = 'mobile' ;
        }

        if (!empty($purchaseItem) && $itemStock > $checkQuantity) {

            $this->getDoctrine()->getRepository('HospitalBundle:InvoiceItem')->insertInvoiceItems($sales, $purchaseItem);
            $sales = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->updateInvoiceTotalPrice($sales);
            $salesItems = $em->getRepository('HospitalBundle:InvoiceItem')->getInvoiceItems($sales,$device);
            $msg = '<div class="alert alert-success"><strong>Success!</strong> Product added successfully.</div>';

        } else {

            $sales = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->updateInvoiceTotalPrice($sales);
            $salesItems = $em->getRepository('HospitalBundle:InvoiceItem')->getInvoiceItems($sales,$device);
            $msg = '<div class="alert"><strong>Warning!</strong> There is no product in our inventory.</div>';
        }

        $salesTotal = $sales->getTotal() > 0 ? $sales->getTotal() : 0;
        $salesSubTotal = $sales->getSubTotal() > 0 ? $sales->getSubTotal() : 0;
        $vat = $sales->getVat() > 0 ? $sales->getVat() : 0;
        return new Response(json_encode(array('salesSubTotal' => $salesSubTotal,'salesTotal' => $salesTotal,'purchaseItem' => $purchaseItem, 'salesItem' => $salesItems,'salesVat' => $vat, 'msg' => $msg , 'success' => 'success')));
        exit;
    }

    public function showAction(Invoice $entity)
    {
        $inventory = $this->getUser()->getGlobalOption()->getHospitalConfig()->getId();
        if ($inventory == $entity->getHospitalConfig()->getId()) {
            return $this->render('HospitalBundle:Invoice:show.html.twig', array(
                'entity' => $entity,
            ));
        } else {
            return $this->redirect($this->generateUrl('hms_invoice'));
        }

    }

    public function confirmAction(Invoice $entity)
    {
        $inventory = $this->getUser()->getGlobalOption()->getHospitalConfig()->getId();
        if ($inventory == $entity->getHospitalConfig()->getId()) {
            return $this->render('HospitalBundle:Invoice:confirm.html.twig', array(
                'entity' => $entity,
            ));
        } else {
            return $this->redirect($this->generateUrl('hms_invoice'));
        }

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
        $form = $this->createForm(new InvoiceType($globalOption,$category ,$location), $entity, array(
            'action' => $this->generateUrl('hms_invoice_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'id' => 'posForm',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    public function approveAction(Request $request , Invoice $entity)
    {

        $em = $this->getDoctrine()->getManager();
        $payment = $request->request->get('payment');
        $discount = $request->request->get('discount');
        $process = $request->request->get('process');

        if (!empty($entity)) {
            $em = $this->getDoctrine()->getManager();
            $entity->setProcess($process);
            $entity->setApprovedBy($this->getUser());
            $em->flush();
            if($payment > 0 || $discount > 0) {
                $transactionData = array('payment' => $payment, 'discount' => $discount);
                $this->getDoctrine()->getRepository('HospitalBundle:InvoiceTransaction')->insertTransaction($entity,$transactionData);
                $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->updatePaymentReceive($entity);
            }
            if($entity->getProcess() == 'Done' && $entity->getPaymentStatus() == 'Paid'){
                $this->getDoctrine()->getRepository('HospitalBundle:Particular')->getSalesUpdateQnt($entity);
            }
            return new Response('success');
        } else {
            return new Response('failed');
        }
        exit;
    }


    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function deleteAction(Invoice $entity)
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
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->findBy(array('inventoryConfig' => $inventory, 'paymentStatus' => 'Pending'));
        $em = $this->getDoctrine()->getManager();
        foreach ($entities as $entity) {
            $em->remove($entity);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('hms_invoice'));
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
        $entity = $em->getRepository('HospitalBundle:Invoice')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $entity->setProcess($data['value']);
        $em->flush();
        exit;

    }


    public function invoicePrintAction(Invoice $entity)
    {

        $barcode = $this->getBarcode($entity->getInvoice());
        $inWords = $this->get('settong.toolManageRepo')->intToWords($entity->getPayment());

        return $this->render('HospitalBundle:Invoice:'.$entity->getPrintFor().'.html.twig', array(
            'entity'      => $entity,
            'barcode'     => $barcode,
            'inWords'     => $inWords,
        ));
    }
}

