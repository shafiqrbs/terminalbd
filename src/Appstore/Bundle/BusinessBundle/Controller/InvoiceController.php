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
        $entity->setCreatedBy($this->getUser());
        $customer = $em->getRepository('DomainUserBundle:Customer')->defaultCustomer($this->getUser()->getGlobalOption());
        $entity->setCustomer($customer);
        $transactionMethod = $em->getRepository('SettingToolBundle:TransactionMethod')->find(1);
        $entity->setTransactionMethod($transactionMethod);
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
     * @Secure(roles="ROLE_BUSINESS")
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
        if (in_array($entity->getProcess(), array('Done','Delivered','Canceled'))) {
            return $this->redirect($this->generateUrl('business_invoice_show', array('id' => $entity->getId())));
        }
        return $this->render('BusinessBundle:Invoice:new.html.twig', array(
            'entity' => $entity,
            'form' => $editForm->createView(),
        ));
    }

    /**
     * @Secure(roles="ROLE_BUSINESS")
     */
    public function updateAction(Request $request, BusinessInvoice $entity)
    {

        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Business Ivoice entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        $data = $request->request->all();
        if ($editForm->isValid()) {
            if (!empty($data['customerMobile'])) {
                $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['customerMobile']);
                $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->newExistingCustomerForSales($globalOption, $mobile, $data);
                $entity->setCustomer($customer);

            } elseif (!empty($data['mobile'])) {
                $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['mobile']);
                $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $globalOption, 'mobile' => $mobile));
                $entity->setCustomer($customer);
            }
            if ($entity->getTotal() <= $entity->getReceived()) {
                $entity->setReceived($entity->getTotal());
                $entity->setDue(0);
                $entity->setPaymentStatus('Paid');
            } else {
                $entity->setPaymentStatus('Due');
                $entity->setDue($entity->getTotal() - $entity->getReceived());
            }
            $em->flush();
            $done = array('Done', 'Delivered');
            if (in_array($entity->getProcess(), $done)) {
                $accountSales = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->insertBusinessAccountInvoice($entity);
                $em->getRepository('AccountingBundle:Transaction')->salesGlobalTransaction($accountSales);
            }
            $inProgress = array('Hold', 'Created');
            if (in_array($entity->getProcess(), $inProgress)) {
                return $this->redirect($this->generateUrl('business_invoice_new'));
            } else {
                return $this->redirect($this->generateUrl('business_invoice_print_invoice', array('id' => $entity->getId())));
            }
        }
        return $this->render('BusinessBundle:Invoice:new.html.twig', array(
            'entity' => $entity,
            'form' => $editForm->createView(),
        ));
    }


    /**
     * @Secure(roles="ROLE_BUSINESS")
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
     * @Secure(roles="ROLE_BUSINESS")
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

        $invoiceParticulars = $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoiceParticular')->getSalesItems($entity);
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

    public function addParticularAction(Request $request, BusinessInvoice $invoice)
    {

        $em = $this->getDoctrine()->getManager();
        $particular = $request->request->get('particular');
        $price = $request->request->get('price');
        $unit = $request->request->get('unit');
        $quantity = $request->request->get('quantity');
        $invoiceItems = array('particular' => $particular, 'quantity' => $quantity,'price' => $price,'unit' => $unit);
        $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoiceParticular')->insertInvoiceParticular($invoice, $invoiceItems);
        $invoice = $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoice')->updateInvoiceTotalPrice($invoice);
        $msg = 'Particular added successfully';
        $result = $this->returnResultData($invoice,$msg);
        return new Response(json_encode($result));
        exit;

    }

    public function invoiceParticularDeleteAction(BusinessInvoice $invoice, BusinessInvoiceParticular $particular){


        $em = $this->getDoctrine()->getManager();
        if (!$particular) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }
        $em->remove($particular);
        $em->flush();
        $invoice = $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoice')->updateInvoiceTotalPrice($invoice);
        $result = $this->returnResultData($invoice,$msg ='');
        return new Response(json_encode($result));
        exit;
    }

    public function invoiceDiscountUpdateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $discountType = $request->request->get('discountType');
        $discountCal = $request->request->get('discount');
        $invoice = $request->request->get('invoice');
        $entity = $em->getRepository('BusinessBundle:BusinessInvoice')->find($invoice);
        $subTotal = $entity->getSubTotal();
        if($discountType == 'flat'){
            $total = ($subTotal  - $discountCal);
            $discount = $discountCal;
        }else{
            $discount = ($subTotal*$discountCal)/100;
            $total = ($subTotal  - $discount);
        }
        $vat = 0;
        if($total > $discount ){
            $entity->setDiscountType($discountType);
            $entity->setDiscountCalculation($discountCal);
            $entity->setDiscount(round($discount));
            $entity->setTotal(round($total + $vat));
            $entity->setDue(round($total + $vat));
            $em->flush();
        }
        $msg = 'Discount successfully';
        $result = $this->returnResultData($entity,$msg);
        return new Response(json_encode($result));
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
     * @Secure(roles="ROLE_BUSINESS")
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

            if($businessConfig->isCustomInvoice() == 1){
                $template = $businessConfig->getGlobalOption()->getSlug();
            }else{
                $template = 'print';
            }
            return  $this->render('BusinessBundle:Print:'.$template.'.html.twig',
                array(
                    'entity' => $entity,
                    'print' => 'print',
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

