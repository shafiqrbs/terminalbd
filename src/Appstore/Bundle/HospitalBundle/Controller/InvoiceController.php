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
        $entities = $em->getRepository('HospitalBundle:Invoice')->invoiceLists( $user , $data);
        $pagination = $this->paginate($entities);
        $transactionMethods = $em->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status' => 1), array('name' => 'ASC'));
        return $this->render('HospitalBundle:Invoice:index.html.twig', array(
            'entities' => $pagination,
            'transactionMethods' => $transactionMethods,
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
        if ($entity->getProcess() != "In-progress") {
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

    public function addParticularAction(Request $request, Invoice $invoice)
    {
        $em = $this->getDoctrine()->getManager();
        $particularId = $request->request->get('particularId');
        $quantity = $request->request->get('quantity');
        $price = $request->request->get('price');
        $invoiceItems = array('particularId' => $particularId , 'quantity' => $quantity,'price' => $price );
        $this->getDoctrine()->getRepository('HospitalBundle:InvoiceParticular')->insertInvoiceItems($invoice, $invoiceItems);
        $invoice = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->updateInvoiceTotalPrice($invoice);
        $invoiceParticulars = $this->getDoctrine()->getRepository('HospitalBundle:InvoiceParticular')->getSalesItems($invoice);
        $msg = 'Particular added successfully';

        $subTotal = $invoice->getSubTotal() > 0 ? $invoice->getSubTotal() : 0;
        $grandTotal = $invoice->getTotal() > 0 ? $invoice->getTotal() : 0;
        $vat = $invoice->getVat() > 0 ? $invoice->getVat() : 0;

        return new Response(json_encode(array('subTotal' => $subTotal,'grandTotal' => $grandTotal, 'vat' => $vat,'invoiceParticulars' => $invoiceParticulars, 'msg' => $msg )));
        exit;
    }

    public function invoiceParticularDeleteAction(Invoice $invoice, InvoiceParticular $particular){

        $em = $this->getDoctrine()->getManager();
        if (!$particular) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }

        $em->remove($particular);
        $em->flush();
        $invoice = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->updateInvoiceTotalPrice($invoice);
        $invoiceParticulars = $this->getDoctrine()->getRepository('HospitalBundle:InvoiceParticular')->getSalesItems($invoice);

        $msg = 'Particular deleted successfully';
        $subTotal = $invoice->getSubTotal() > 0 ? $invoice->getSubTotal() : 0;
        $grandTotal = $invoice->getTotal() > 0 ? $invoice->getTotal() : 0;
        $vat = $invoice->getVat() > 0 ? $invoice->getVat() : 0;

        return new Response(json_encode(array('subTotal' => $subTotal,'grandTotal' => $grandTotal, 'vat' => $vat,'invoiceParticular' => $invoiceParticulars, 'msg' => $msg )));
        exit;


    }

    public function invoiceDiscountUpdateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $discount = $request->request->get('discount');
        $invoice = $request->request->get('invoice');

        $sales = $em->getRepository('HospitalBundle:Invoice')->find($invoice);
        $total = ($sales->getSubTotal() - $discount);
        $vat = 0;
        if($total > $discount ){
            if ($sales->getHospitalConfig()->getVatEnable() == 1 && $sales->getHospitalConfig()->getVatPercentage() > 0) {
                $vat = $em->getRepository('HospitalBundle:Invoice')->getCulculationVat($sales,$total);
                $sales->setVat($vat);
            }
            $sales->setDiscount($discount);
            $sales->setTotal($total + $vat);
            $sales->setDue($total + $vat);
            $em->persist($sales);
            $em->flush();
        }

        $invoiceParticulars = $this->getDoctrine()->getRepository('HospitalBundle:InvoiceParticular')->getSalesItems($sales);
        $subTotal = $sales->getSubTotal() > 0 ? $sales->getSubTotal() : 0;
        $grandTotal = $sales->getTotal() > 0 ? $sales->getTotal() : 0;
        $vat = $sales->getVat() > 0 ? $sales->getVat() : 0;
        return new Response(json_encode(array('subTotal' => $subTotal,'grandTotal' => $grandTotal,'vat' => $vat,'invoiceParticulars' => $invoiceParticulars, 'msg' => 'Discount updated successfully' , 'success' => 'success')));
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
            if (!empty($referredId)) {
                $referred = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->findOneBy(array('hospitalConfig' => $entity->getHospitalConfig() , 'service' => 6, 'id' => $referredId ));
                $entity->setReferredDoctor($referred);

            }elseif(!empty($data['referredDoctor']['name']) && !empty($data['referredDoctor']['mobile'])) {

                $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['referredDoctor']['mobile']);
                $referred = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->findHmsExistingCustomer($entity->getHospitalConfig() , $mobile,$data);
                $entity->setReferredDoctor($referred);
            }


            if ($entity->getHospitalConfig()->getVatEnable() == 1 && $entity->getHospitalConfig()->getVatPercentage() > 0) {
                $vat = $em->getRepository('HospitalBundle:Invoice')->getCulculationVat($entity,$entity->getTotal());
                $entity->setVat($vat);
            }

            $entity->setDue($entity->getTotal() - $entity->getPayment());

            if ($entity->getTotal() <= $entity->getPayment() ) {
                $entity->setPaymentStatus('Paid');
            } else if ($entity->getTotal() > $entity->getPayment() ) {
                $entity->setPaymentStatus('Due');
            }
            $entity->setProcess('In-progress');
            $amountInWords = $this->get('settong.toolManageRepo')->intToWords($entity->getTotal());
            $entity->setPaymentInWord($amountInWords);
            $em->flush();

            if(!empty($this->getUser()->getGlobalOption()->getNotificationConfig()) and  !empty($this->getUser()->getGlobalOption()->getSmsSenderTotal())) {
               $dispatcher = $this->container->get('event_dispatcher');
               $dispatcher->dispatch('setting_tool.post.hms_invoice_sms', new \Setting\Bundle\ToolBundle\Event\HmsInvoiceSmsEvent($entity));
            }
            return $this->redirect($this->generateUrl('hms_invoice_show', array('id' => $entity->getId())));
        }
        $referredDoctors = $em->getRepository('HospitalBundle:Particular')->findBy(array('hospitalConfig' => $entity->getHospitalConfig(),'status'=>1,'service'=>6),array('name'=>'ASC'));
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


    public function salesDiscountUpdateAction(Request $request)
    {

    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function salesItemUpdateAction(Request $request)
    {

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
            if($payment){

                $entity->setDiscount($entity->getDiscount() + $discount);
                $entity->setTotal($entity->getTotal() - $entity->getDiscount());
                $entity->setPayment($entity->getPayment() + $payment);
                $entity->setDue($entity->getTotal() - $entity->getPayment());
                if($entity->getPayment() >= $entity->getTotal()) {
                    $entity->setPaymentStatus('Paid');
                }
            }
            $entity->setProcess($process);
            $entity->setApprovedBy($this->getUser());
            $em->flush();
            //$accountInvoice = $em->getRepository('AccountingBundle:AccountInvoice')->insertAccountInvoice($entity);
            //$em->getRepository('AccountingBundle:Transaction')->salesTransaction($entity, $accountInvoice);
            return new Response('success');
        } else {
            return new Response('failed');
        }
        exit;
    }


    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function deleteAction(Invoice $sales)
    {


        $em = $this->getDoctrine()->getManager();
        if (!$sales) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        if (!empty($sales->getInvoiceImport())) {
            $salesImport = $sales->getInvoiceImport();
            $em->remove($salesImport);
        }
        $em->remove($sales);
        $em->flush();
        return new Response(json_encode(array('success' => 'success')));
        exit;
    }

    /**
     * Deletes a InvoiceItem entity.
     *
     */
    public function itemDeleteAction(Invoice $sales, $salesItem)
    {

    }

    public function itemPurchaseDetailsAction(Request $request)
    {

    }

    public function branchStockItemDetailsAction(Item $item)
    {

    }
    public function salesItemAction(Request $request)
    {

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
        $data .= '<img src="data:image/png;base64,' . $code . '" />';
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

    public function salesInlineUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('HospitalBundle:Invoice')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseItem entity.');
        }
        $entity->setCourierInvoice($data['value']);
        $em->flush();
        exit;

    }



    public function approvedOrder(Invoice $entity)
    {
        if (!empty($entity)) {

            $em = $this->getDoctrine()->getManager();

            $entity->setPaymentStatus('Paid');
            $entity->setProcess('Paid');
            $entity->setPayment($entity->getTotal());
            $entity->setDue(0);
            $entity->setApprovedBy($this->getUser());
            $amountInWords = $this->get('settong.toolManageRepo')->intToWords($entity->getTotal());
            $entity->setPaymentInWord($amountInWords);
            $em->flush();
            $em->getRepository('HospitalBundle:Item')->getItemInvoiceUpdate($entity);
            $em->getRepository('HospitalBundle:StockItem')->insertInvoiceStockItem($entity);
            $em->getRepository('HospitalBundle:GoodsItem')->updateEcommerceItem($entity);
            $accountInvoice = $em->getRepository('AccountingBundle:AccountInvoice')->insertAccountInvoice($entity);
            $em->getRepository('AccountingBundle:Transaction')->salesTransaction($entity, $accountInvoice);
            return new Response('success');
        } else {
            return new Response('failed');
        }
        exit;
    }

    public function returnCancelOrder(Invoice $entity)
    {
        if (!empty($entity)) {
            $em = $this->getDoctrine()->getManager();
            $entity->setPaymentStatus('Cancel');
            $entity->setApprovedBy($this->getUser());
            $em->flush();
            return new Response('success');
        } else {
            return new Response('failed');
        }
        exit;
    }

    public function salesSelectAction()
    {
        $items  = array();
        $items[]= array('value' => 'Paid','text'=>'Paid');
        $items[]= array('value' => 'In-progress','text'=>'In-progress');
        $items[]= array('value' => 'Courier','text'=>'Courier');
        $items[]= array('value' => 'Returned','text'=>'Returned');
        return new JsonResponse($items);
    }

    public function invoicePrintAction(Invoice $entity)
    {

        $barcode = $this->getBarcode($entity->getInvoice());
        $inWords = $this->get('settong.toolManageRepo')->intToWords($entity->getPayment());
        return $this->render('HospitalBundle:Invoice:print.html.twig', array(
            'entity'      => $entity,
            'barcode'     => $barcode,
            'inWords'     => $inWords,
        ));
    }
}

