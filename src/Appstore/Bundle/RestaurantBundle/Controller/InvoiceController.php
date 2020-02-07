<?php

namespace Appstore\Bundle\RestaurantBundle\Controller;

use Appstore\Bundle\RestaurantBundle\Entity\Invoice;
use Appstore\Bundle\RestaurantBundle\Entity\InvoiceParticular;
use Appstore\Bundle\RestaurantBundle\Entity\Particular;
use Appstore\Bundle\RestaurantBundle\Form\InvoiceType;
use Appstore\Bundle\RestaurantBundle\Form\RestaurantParticularType;
use Appstore\Bundle\RestaurantBundle\Service\PosItemManager;
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
 * InvoiceController controller.
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
        $config = $user->getGlobalOption()->getRestaurantConfig();
        $entities = $em->getRepository('RestaurantBundle:Invoice')->invoiceLists( $user,$data);
        $pagination = $this->paginate($entities);

        return $this->render('RestaurantBundle:Invoice:index.html.twig', array(
            'entities' => $pagination,
            'salesTransactionOverview' => '',
            'previousSalesTransactionOverview' => '',
            'assignDoctors' => '',
            'searchForm' => $data,
        ));

    }

    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new Invoice();
        $option = $this->getUser()->getGlobalOption();
        $config = $option->getRestaurantConfig();
        $entity->setRestaurantConfig($config);
        $transactionMethod = $em->getRepository('SettingToolBundle:TransactionMethod')->find(1);
        $entity->setTransactionMethod($transactionMethod);
        $entity->setPaymentStatus('Pending');
        $entity->setCreatedBy($this->getUser());
        $entity->setSalesBy($this->getUser());
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('restaurant_invoice_edit', array('id' => $entity->getId())));

    }

    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        $entity = $em->getRepository('RestaurantBundle:Invoice')->findOneBy(array('restaurantConfig' => $config , 'id' => $id));

        $categories = $em->getRepository('RestaurantBundle:Category')->findBy(array('restaurantConfig' => $config , 'status' => 1));
        $tables = $em->getRepository('RestaurantBundle:Particular')->findBy(array('restaurantConfig' => $config , 'service' => 1));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $editForm = $this->createEditForm($entity);
        $invoiceParticularForm = $this->createInvoiceParticularForm(New InvoiceParticular(),$entity);
        if ($entity->getProcess() == "Done") {
            return $this->redirect($this->generateUrl('restaurant_invoice_show', array('id' => $entity->getId())));
        }
        return $this->render('RestaurantBundle:Invoice:editPos.html.twig', array(
            'entity'        => $entity,
            'categories'    => $categories,
            'tables'    => $tables,
            'user'          => $this->getUser(),
            'form'          => $editForm->createView(),
            'itemForm'      => $invoiceParticularForm->createView(),
        ));

    }

    public function particularSearchAction(Particular $particular)
    {
        return new Response(json_encode(array('particularId'=> $particular->getId() ,'price'=> $particular->getPrice() , 'quantity'=> $particular->getQuantity(), 'minimumPrice'=> $particular->getMinimumPrice(), 'instruction'=> $particular->getInstruction())));
    }

    public function returnResultData(Invoice $entity,$msg=''){

        $invoiceParticulars = $this->getDoctrine()->getRepository('RestaurantBundle:InvoiceParticular')->invoiceParticularLists($entity);
        $subTotal = $entity->getSubTotal() > 0 ? $entity->getSubTotal() : 0;
        $netTotal = $entity->getTotal() > 0 ? $entity->getTotal() : 0;
        $payment = $entity->getPayment() > 0 ? $entity->getPayment() : 0;
        $vat = $entity->getVat() > 0 ? $entity->getVat() : 0;
        $due = !empty($entity->getDue()) ? $entity->getDue() : 0;
        $discount = $entity->getDiscount() > 0 ? $entity->getDiscount() : 0;

       $data = array(
           'subTotal'               => $subTotal,
           'netTotal'               => $netTotal,
           'payment'                => $payment ,
           'due'                    => $due,
           'vat'                    => $vat,
           'discount'               => $discount,
           'invoiceParticulars'     => $invoiceParticulars ,
           'msg'                    => $msg ,
           'success'                => 'success'
       );

       return $data;

    }

    public function addParticularAction(Request $request, Invoice $invoice)
    {
        $em = $this->getDoctrine()->getManager();
        $particularId = $request->request->get('particularId');
        $quantity = $request->request->get('quantity');
        $process = $request->request->get('process');
        $invoiceItems = array('particularId' => $particularId , 'quantity' => $quantity,'process'=>$process);
        $this->getDoctrine()->getRepository('RestaurantBundle:InvoiceParticular')->insertInvoiceItems($invoice, $invoiceItems);
        $invoice = $this->getDoctrine()->getRepository('RestaurantBundle:Invoice')->updateInvoiceTotalPrice($invoice);
        $msg = 'Particular added successfully';
        $result = $this->returnResultData($invoice,$msg);
        return new Response(json_encode($result));


    }

    public function addProductAction(Request $request, Invoice $invoice, $product)
    {
        $em = $this->getDoctrine()->getManager();
        // $particularId = $request->request->get('particularId');
        $invoiceItems = array('particularId' => $product , 'quantity' => 1,'process'=>'create');
        $this->getDoctrine()->getRepository('RestaurantBundle:InvoiceParticular')->insertInvoiceItems($invoice, $invoiceItems);
        $invoice = $this->getDoctrine()->getRepository('RestaurantBundle:Invoice')->updateInvoiceTotalPrice($invoice);
        $msg = 'Particular added successfully';
        $result = $this->returnResultData($invoice,$msg);
        return new Response(json_encode($result));


    }

    public function updateProductAction(Request $request, Invoice $invoice, $product)
    {
        $em = $this->getDoctrine()->getManager();
        $quantity = $_REQUEST['quantity'];
        $invoiceItems = array('particularId' => $product , 'quantity' => $quantity,'process'=>'update');
        $this->getDoctrine()->getRepository('RestaurantBundle:InvoiceParticular')->insertInvoiceItems($invoice, $invoiceItems);
        $invoice = $this->getDoctrine()->getRepository('RestaurantBundle:Invoice')->updateInvoiceTotalPrice($invoice);
        $msg = 'Particular added successfully';
        $result = $this->returnResultData($invoice,$msg);
        return new Response(json_encode($result));


    }

    public function invoiceParticularDeleteAction( $invoice, InvoiceParticular $particular){


        $em = $this->getDoctrine()->getManager();
        if (!$particular) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }
        $em->remove($particular);
        $em->flush();
        $entity =  $this->getDoctrine()->getRepository('RestaurantBundle:Invoice')->find($invoice);
        $this->getDoctrine()->getRepository('RestaurantBundle:Invoice')->updateInvoiceTotalPrice($entity);
        $msg = 'Product deleted successfully';
        $result = $this->returnResultData($entity,$msg);
        return new Response(json_encode($result));
    }

    public function updateAction(Request $request, Invoice $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $editForm = $this->createEditForm($entity);
        $invoiceParticularForm = $this->createInvoiceParticularForm(New InvoiceParticular(),$entity);
        $editForm->handleRequest($request);
        $data = $request->request->all();
        if($editForm->isValid()) {
            $newCustomerMobile = isset($data['new_customer_mobile'])?$data['new_customer_mobile']:'';
            $newCustomerName = isset($data['new_customer_name'])?$data['new_customer_name']:'';
            $customerMobile = isset($data['customerMobile'])?$data['customerMobile']:'';
            if(!empty($customerMobile)){
                $mobile = $this->get('settong.toolManageRepo')->specialExpClean($customerMobile);
                $customer = $em->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $globalOption ,'mobile' => $mobile));
                $em->getRepository('RestaurantBundle:Invoice')->insertNewCustomerWithDiscount($entity,$customer);
            }
            if(!empty($newCustomerMobile)){
                $mobile = $this->get('settong.toolManageRepo')->specialExpClean($newCustomerMobile);
                $customer = $em->getRepository('DomainUserBundle:Customer')->newExistingRestaurantCustomer($globalOption,$mobile,$newCustomerName);
                $em->getRepository('RestaurantBundle:Invoice')->insertNewCustomerWithDiscount($entity,$customer);
            }else{
                $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $globalOption, 'name' => 'Default'));
                $entity->setCustomer($customer);
            }

            $amountInWords = $this->get('settong.toolManageRepo')->intToWords($entity->getTotal());
            $entity->setPaymentInWord($amountInWords);
            $due = $entity->getTotal() - $entity->getPayment();
            $entity->setDue($due);
            $em->flush();
            if(!empty($entity->getInvoiceParticulars()) and in_array($entity->getProcess(),array('Delivered','Done'))) {
                $this->getDoctrine()->getRepository('RestaurantBundle:Particular')->insertAccessories($entity);
                $accountInvoice = $em->getRepository('AccountingBundle:AccountSales')->insertRestaurantAccountInvoice($entity);
                $em->getRepository('AccountingBundle:Transaction')->restaurantSalesTransaction($entity, $accountInvoice);
            }
            return $this->redirect($this->generateUrl('restaurant_invoice'));
        }
        return $this->render('RestaurantBundle:Invoice:editPos.html.twig', array(
            'entity'        => $entity,
            'form'          => $editForm->createView(),
            'itemForm'      => $invoiceParticularForm->createView(),
        ));

    }

    public function invoiceDiscountUpdateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $discountCalculation = (float)$request->request->get('discount');
        $invoice = $request->request->get('invoice');
        $discountType = $request->request->get('discountType');
        $entity = $em->getRepository('RestaurantBundle:Invoice')->find($invoice);
        $subTotal = $entity->getSubTotal();
        $discount = 0;
        if($discountType == 'flat' and $discountCalculation > 0 ){
            $total = ($subTotal  - $discountCalculation);
            $discount = $discountCalculation;
        }elseif($discountType == 'percentage' and $discountCalculation > 0 ){
            $discount = ($subTotal * $discountCalculation)/100;
            $total = ($subTotal  - $discount);
        }
        if( $subTotal > $discount and $discount > 0 ){
            $entity->setDiscountType($discountType);
            $entity->setDiscountCalculation($discountCalculation);
            $entity->setDiscount($discount);
            $entity->setTotal($total);
            $entity->setDue($entity->getTotal() - $entity->getPayment());
            $em->flush();
            $msg = 'Discount added successfully';
        }else{
            $msg = 'Discount is not use properly';
        }
        $returnEntity =  $this->getDoctrine()->getRepository('RestaurantBundle:Invoice')->updateInvoiceTotalPrice($entity);
        $result = $this->returnResultData($returnEntity,$msg);
        return new Response(json_encode($result));
        exit;
    }

    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $sales = $request->request->get('sales');
        $barcode = $request->request->get('barcode');
        $sales = $em->getRepository('RestaurantBundle:Invoice')->find($sales);
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $purchaseItem = $em->getRepository('RestaurantBundle:PurchaseItem')->returnPurchaseItemDetails($inventory, $barcode);
        $checkQuantity = $this->getDoctrine()->getRepository('RestaurantBundle:InvoiceItem')->checkInvoiceQuantity($purchaseItem);
        $itemStock = $purchaseItem->getItemStock();

        /* Device Detection code desktop or mobile */
        $detect = new MobileDetect();
        $device = '';
        if( $detect->isMobile() || $detect->isTablet() ) {
            $device = 'mobile' ;
        }

        if (!empty($purchaseItem) && $itemStock > $checkQuantity) {

            $this->getDoctrine()->getRepository('RestaurantBundle:InvoiceItem')->insertInvoiceItems($sales, $purchaseItem);
            $sales = $this->getDoctrine()->getRepository('RestaurantBundle:Invoice')->updateInvoiceTotalPrice($sales);
            $salesItems = $em->getRepository('RestaurantBundle:InvoiceItem')->getInvoiceItems($sales,$device);
            $msg = '<div class="alert alert-success"><strong>Success!</strong> Product added successfully.</div>';

        } else {

            $sales = $this->getDoctrine()->getRepository('RestaurantBundle:Invoice')->updateInvoiceTotalPrice($sales);
            $salesItems = $em->getRepository('RestaurantBundle:InvoiceItem')->getInvoiceItems($sales,$device);
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
        $inventory = $this->getUser()->getGlobalOption()->getRestaurantConfig()->getId();
        if ($inventory == $entity->getRestaurantConfig()->getId()) {
            return $this->render('RestaurantBundle:Invoice:show.html.twig', array(
                'entity' => $entity,
            ));
        } else {
            return $this->redirect($this->generateUrl('restaurant_invoice'));
        }

    }

    private function createEditForm(Invoice $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $particular = $this->getDoctrine()->getRepository('RestaurantBundle:Particular');
        $form = $this->createForm(new InvoiceType($globalOption,$particular), $entity, array(
            'action' => $this->generateUrl('restaurant_invoice_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'id' => 'invoiceForm',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    private function createInvoiceParticularForm(InvoiceParticular $entity , Invoice $invoice)
    {
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        $particular = $this->getDoctrine()->getRepository('RestaurantBundle:Particular');
        $form = $this->createForm(new RestaurantParticularType($config,$particular), $entity, array(
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'id' => 'particularForm',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }


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

    public function deleteEmptyInvoiceAction()
    {
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantConfig')->invoiceDelete($config);
        return $this->redirect($this->generateUrl('restaurant_invoice'));
    }

    public function checkTokenBookingAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $tokenNo = $request->request->get('tokenNo');
        $invoice = $request->request->get('invoice');
        $status = $em->getRepository('RestaurantBundle:Invoice')->checkTokenBooking($invoice,$tokenNo);
        echo $status;
        exit;
    }

    public function reverseAction($invoice){

        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        $entity = $this->getDoctrine()->getRepository('RestaurantBundle:Invoice')->findOneBy(array('restaurantConfig' => $config, 'invoice' => $invoice));
        $em->getRepository('RestaurantBundle:Invoice')->salesTransactionReverse($entity);
        $em->getRepository('RestaurantBundle:InvoiceParticular')->reverseInvoiceParticularUpdate($entity);
        $em = $this->getDoctrine()->getManager();
        $entity->setRevised(true);
        $entity->setProcess('Revised');
        $entity->setRevised(true);
        $entity->setTotal($entity->getSubTotal());
        $entity->setPaymentStatus('Due');
        $entity->setDiscount(null);
        $entity->setDue($entity->getSubTotal());
        $entity->setPaymentInWord(null);
        $em->flush();
        $template = $this->get('twig')->render('RestaurantBundle:Invoice:reverse.html.twig',array(
            'entity' => $entity,
        ));
        $em->getRepository('RestaurantBundle:Reverse')->insertInvoice($entity,$template);
        return $this->redirect($this->generateUrl('restaurant_invoice_edit',array('id'=>$entity->getId())));

    }

    public function PosPrintAction(Request $request,$invoice)
    {
        $connector = new \Mike42\Escpos\PrintConnectors\DummyPrintConnector();
        $printer = new Printer($connector);
        $printer -> initialize();
        $dataInvoice = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $option = $this->getUser()->getGlobalOption();
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();

        $entity = $em->getRepository('RestaurantBundle:Invoice')->findOneBy(array('restaurantConfig'=>$config,'invoice'=>$invoice));
        $data = $dataInvoice['restaurant_invoice'];
        if(!empty($entity)){
            $this->cashPayment($entity);
        }
        $currentPayment = !empty($data['payment']) ? $data['payment'] :0;

        $address       = $config->getAddress();
        $vatRegNo       = $config->getVatRegNo();
        $companyName    = $option->getName();
        $website        = $option->getDomain();


        /** ===================Customer Information=================================== */

        $invoice            = $entity->getInvoice();
        $subTotal           = $entity->getSubTotal();
        $total              = $entity->getTotal();
        $discount           = $entity->getDiscount();
        $vat                = $entity->getVat();
        $due                = $entity->getDue();
        $payment            = $entity->getPayment();
        $transaction        = $entity->getTransactionMethod()->getName();
        $salesBy            = $entity->getSalesBy();


        /** ===================Invoice Sales Item Information========================= */


        /* Date is kept the same for testing */
        $date = date('d-m-Y h:i:s A');

        /* Name of shop */
        $printer -> setUnderline(Printer::UNDERLINE_NONE);
        $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> text($companyName."\n");
        $printer -> selectPrintMode();
        $printer -> text($address."\n");
        /* $printer -> text($mobile."\n");*/
        $printer -> feed();

        /* Title of receipt */
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> setEmphasis(true);
        if(!empty($vatRegNo)){
            $printer -> text("BIN No. ".$vatRegNo.".\n");
            $printer -> setEmphasis(false);
        }

        $printer -> feed();
        $slipNo ='xxx';
        $tableNo ='00';
        if($entity->getTokenNo()){
            $tableNo = $entity->getTokenNo()->getName();
        }
        if($entity->getSlipNo()){
            $slipNo = $entity->getSlipNo();
        }
        $table = $slipNo.'/'.$tableNo;
        $transaction    = new PosItemManager('Payment Mode: '.$transaction,'','');
        $subTotal       = new PosItemManager('Sub Total: ','Tk.',number_format($subTotal));
        $vat            = new PosItemManager('Vat: ','Tk.',number_format($vat));
        $discount       = new PosItemManager('Discount: ','Tk.',number_format($discount));
        $grandTotal     = new PosItemManager('Net Payable: ','Tk.',number_format($total));
        $payment        = new PosItemManager('Received: ','Tk.',number_format($payment));
        $due            = new PosItemManager('Due: ','Tk.',number_format($due));
        $return         = new PosItemManager('Return: ','Tk.',number_format($currentPayment-$total));

        /* Title of receipt */
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> setEmphasis(true);
        $printer -> text("INVOICE NO. ".$entity->getInvoice().".\n");
        $printer -> setEmphasis(false);
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> setEmphasis(true);
        $printer -> text("Table No. ".$table.".\n\n");
        $printer -> setEmphasis(false);

        $printer -> setJustification(Printer::JUSTIFY_LEFT);
        $printer -> setEmphasis(true);
        $printer -> setUnderline(Printer::UNDERLINE_DOUBLE);
        $printer -> text(new PosItemManager('Item Name', 'Qnt', 'Amount'));
        $printer -> setEmphasis(false);
        $printer -> setUnderline(Printer::UNDERLINE_NONE);;
        $printer -> setEmphasis(false);
        $printer -> feed();
        $i=1;
        /* @var $row InvoiceParticular */
        foreach ( $entity->getInvoiceParticulars() as $row){
            $productName = "{$i}. {$row->getParticular()->getName()}";
            $printer -> setUnderline(Printer::UNDERLINE_SINGLE);
            $printer -> text(new PosItemManager($productName,$row->getQuantity(),number_format($row->getSubTotal())));
            $i++;
        }
        $printer -> feed();
        $printer -> setUnderline(Printer::UNDERLINE_NONE);
        $printer -> setEmphasis(true);
        $printer -> text ( "\n" );
        $printer -> setUnderline(Printer::UNDERLINE_DOUBLE);
        $printer -> text($subTotal);
        $printer -> setEmphasis(false);
        if($vat){
            $printer -> setUnderline(Printer::UNDERLINE_SINGLE);
            $printer->text($vat);
            $printer->setEmphasis(false);
        }
        if($discount){
            $printer -> setUnderline(Printer::UNDERLINE_DOUBLE);
            $printer->text($discount);
            $printer -> setEmphasis(false);
            $printer -> text ( "\n" );
        }
        $printer -> setEmphasis(true);
        $printer -> setUnderline(Printer::UNDERLINE_DOUBLE);
        $printer -> text($grandTotal);
        $printer -> setUnderline(Printer::UNDERLINE_NONE);
        $printer->text("\n");
        $printer -> feed();
        $printer->text($transaction);
        $printer->selectPrintMode();
        /* Barcode Print */
        $printer->text ( "\n" );
        $printer->selectPrintMode ();
        $printer->setBarcodeHeight (30);
        $hri = array (Printer::BARCODE_TEXT_BELOW => "");
        $printer -> feed();
        foreach ( $hri as $position => $caption){
            $printer->selectPrintMode ();
            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer->text ($caption);
            $printer->feed ();
        }
        $printer -> feed();
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> text("Served By: ".$salesBy."\n");
        $printer -> text("Thanks for being here\n");
        if($website){
            $printer -> text("Please visit www.".$website."\n");
        }
        $printer -> text($date . "\n");
        $response =  base64_encode($connector->getData());
        $printer -> close();
        return new Response($response);

    }

    public function approvedOrder(Invoice $entity,$data)
    {
        $globalOption = $entity->getRestaurantConfig()->getGlobalOption();
        $em =  $em = $this->getDoctrine()->getManager();
        $payment = !empty($data['payment']) ? $data['payment'] :0;
        if($entity->getPayment() >= $entity->getTotal()){
            $entity->setPayment($entity->getTotal());
            $entity->setPaymentStatus('Paid');
            $entity->setDue(null);
        }else{
            $entity->setPayment($payment);
            if($entity->getTotal() > $payment) {
                $entity->setDue($entity->getTotal() - $payment);
            }else{
                $entity->setDue(null);
            }
            $entity->setPaymentStatus('Due');
        }
        $entity->setApprovedBy($this->getUser());
        $entity->setProcess('Done');
        $amountInWords = $this->get('settong.toolManageRepo')->intToWords($entity->getTotal());
        $entity->setPaymentInWord($amountInWords);
        $em->flush();
        if ($entity->getTotal() > 0) {
            $this->getDoctrine()->getRepository('RestaurantBundle:Particular')->insertAccessories($entity);
            $accountInvoice = $em->getRepository('AccountingBundle:AccountSales')->insertRestaurantAccountInvoice($entity);
            $em->getRepository('AccountingBundle:Transaction')->restaurantSalesTransaction($entity, $accountInvoice);
        }

    }

    public function PaymentPrintAction($id)
    {

        $connector = new \Mike42\Escpos\PrintConnectors\DummyPrintConnector();
        $printer = new Printer($connector);
        $printer -> initialize();

        $em = $this->getDoctrine()->getManager();
        $option = $this->getUser()->getGlobalOption();
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();

        $entity = $em->getRepository('RestaurantBundle:Invoice')->findOneBy(array('restaurantConfig' => $config,'id' => $id));
        if(!empty($entity)){
           $this->cashPayment($entity);
        }
        $address      = $config->getAddress();
        $vatRegNo       = $config->getVatRegNo();
        $companyName    = $option->getName();
        $mobile         = $option->getMobile();
        $website        = $option->getDomain();


        /** ===================Customer Information=================================== */

        $invoice            = $entity->getInvoice();
        $subTotal           = $entity->getSubTotal();
        $total              = $entity->getTotal();
        $discount           = $entity->getDiscount();
        $vat                = $entity->getVat();
        $due                = $entity->getDue();
        $payment            = $entity->getPayment();
        $transaction        = $entity->getTransactionMethod()->getName();
        $salesBy            = $entity->getSalesBy();

        $slipNo ='';
        $tableNo ='';
        if($entity->getTokenNo()){
            $tableNo = $entity->getTokenNo()->getName();
        }
        if($entity->getSlipNo()){
            $slipNo = $entity->getSlipNo();
        }
        $table = $slipNo.'/'.$tableNo;

        $transaction    = new PosItemManager('Pay Mode: '.$transaction,'','');
        $subTotal       = new PosItemManager('Sub Total: ','Tk.',number_format($subTotal));
        $vat            = new PosItemManager('Vat: ','Tk.',number_format($vat));
        $discount       = new PosItemManager('Discount: ','Tk.',number_format($discount));
        $grandTotal     = new PosItemManager('Net Payable: ','Tk.',number_format($total));
        $payment        = new PosItemManager('Received: ','Tk.',number_format($payment));
        $due            = new PosItemManager('Due: ','Tk.',number_format($due));


        /** ===================Invoice Sales Item Information========================= */


        /* Date is kept the same for testing */
        $date = date('d-m-Y h:i:s A');

        /* Name of shop */
        $printer -> setUnderline(Printer::UNDERLINE_NONE);
        $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> text($companyName."\n");
        $printer -> selectPrintMode();
        $printer -> setFont(Printer::FONT_B);
        $printer -> text($address."\n");
        $printer -> feed();
        /* Title of receipt */
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        if(!empty($vatRegNo)){
            $printer -> text("BIN No - ".$vatRegNo."\n");
        }
        /* Title of receipt */
        $printer -> setJustification(Printer::JUSTIFY_LEFT);
        $printer->setFont(Printer::FONT_B);
        $printer -> setJustification(Printer::JUSTIFY_LEFT);
        $printer -> text("Invoice no. {$entity->getInvoice()}        {$table}\n");
        $printer -> text("Date: {$date}          {$transaction}\n\n");
        $printer -> text(new PosItemManager('Item Name', 'Qnt', 'Amount'));
        $printer -> text("---------------------------------------------------------------\n");
        $i=1;
        /* @var $row InvoiceParticular */
        foreach ( $entity->getInvoiceParticulars() as $row){
            $productName = "{$i}. {$row->getParticular()->getName()}";
            $printer -> text(new PosItemManager($productName,$row->getQuantity(),number_format($row->getSubTotal())));
            $i++;
        }
        $printer -> text("---------------------------------------------------------------\n");
        $printer -> text($subTotal);
        $printer -> setEmphasis(false);
        if($vat){
            $printer->text($vat);
            $printer->setEmphasis(false);
        }
        if($discount){
            $printer->text($discount);
            $printer -> setEmphasis(false);
        }
        $printer -> text("---------------------------------------------------------------\n");
        $printer -> text($grandTotal);
        $printer -> setUnderline(Printer::UNDERLINE_NONE);
        $printer->text($transaction);
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> text("Served By: ".$salesBy."\n");
        $printer -> text("Thanks for being here\n");
        if($website){
            $printer -> text("** Visit www.".$website."**\n");
        }
        $printer -> text($date . "\n");
        $printer -> text("Powered by - www.terminalbd.com - 01828148148 \n");
        $response =  base64_encode($connector->getData());
        $printer -> close();
        return new Response($response);
    }

    public function KitchenPrintAction($invoice)
    {

        $connector = new \Mike42\Escpos\PrintConnectors\DummyPrintConnector();
        $printer = new Printer($connector);
        $printer -> initialize();

        $em = $this->getDoctrine()->getManager();
        $option = $this->getUser()->getGlobalOption();
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();

        $entity = $em->getRepository('RestaurantBundle:Invoice')->findOneBy(array('restaurantConfig'=>$config,'invoice'=>$invoice));
        if(!empty($entity)){
            $entity->setProcess('Kitchen');
            $em->flush();
        }
        $address1       = $option->getContactPage()->getAddress1();
        $thana          = !empty($option->getContactPage()->getLocation()) ? ', '.$option->getContactPage()->getLocation()->getName():'';
        $district       = !empty($option->getContactPage()->getLocation()) ? ', '.$option->getContactPage()->getLocation()->getParent()->getName():'';
        $address = $address1.$thana.$district;

        $vatRegNo       = $config->getVatRegNo();
        $companyName    = $option->getName();
        $mobile         = $option->getMobile();
        $website        = $option->getDomain();


        /** ===================Customer Information=================================== */
        $salesBy            = $entity->getCreatedBy();

        /** ===================Invoice Sales Item Information========================= */

        /* Date is kept the same for testing */
        $date = date('d-m-Y h:i:s A');

        /* Name of shop */
        $printer -> setUnderline(Printer::UNDERLINE_NONE);
        $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> text($companyName."\n");
        $printer -> selectPrintMode();
        $printer -> text($address."\n");
        /* $printer -> text($mobile."\n");*/
        $printer -> feed();

        /* Title of receipt */
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> setEmphasis(true);
        $printer -> text("INVOICE NO. ".$entity->getInvoice().".\n");
        $printer -> setEmphasis(false);
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> setEmphasis(true);
        $printer -> text("Table No. ".$entity->getSlipNo().'/'.$entity->getTokenNo()->getName().".\n\n");
        $printer -> setEmphasis(false);

        $printer -> setJustification(Printer::JUSTIFY_LEFT);
        $printer -> setEmphasis(true);
        $printer -> setUnderline(Printer::UNDERLINE_DOUBLE);
        $printer -> text(new PosItemManager('Item Code', 'Qnt', 'Amount'));
        $printer -> setEmphasis(false);
        $printer -> setUnderline(Printer::UNDERLINE_NONE);;
        $printer -> setEmphasis(false);
        $printer -> feed();
        $i=1;
        foreach ( $entity->getInvoiceParticulars() as $row){

            $printer -> setUnderline(Printer::UNDERLINE_NONE);
            $printer -> text( new PosItemManager($i.'. '.$row->getParticular()->getName(),"",""));
            $printer -> setUnderline(Printer::UNDERLINE_SINGLE);
            $printer -> text(new PosItemManager($row->getParticular()->getParticularCode(),$row->getQuantity(),number_format($row->getSubTotal())));
            $i++;
        }
        $printer -> feed();
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> text("Sales By: ".$salesBy."\n");
        $printer -> text("Thank you for shopping\n");
        if($website){
            $printer -> text("Please visit www.".$website."\n");
        }
        $printer -> text($date . "\n");
        $printer -> text("Powered by - www.terminalbd.com - 01828148148 \n");
        $response =  base64_encode($connector->getData());
        $printer -> close();
        return new Response($response);
    }

    public function saveAction($invoice)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        $entity = $em->getRepository('RestaurantBundle:Invoice')->findOneBy(array('restaurantConfig'=>$config,'invoice'=>$invoice));
        if(!empty($entity)){
            $entity->setProcess('Kitchen');
            $em->flush();
        }
        exit;

    }

    public function cashPayment(Invoice $entity){

        $em =  $em = $this->getDoctrine()->getManager();
        $entity->setPayment($entity->getTotal());
        $entity->setPaymentStatus('Paid');
        $entity->setApprovedBy($this->getUser());
        $entity->setProcess('Done');
        $entity->setDue(0);
        $amountInWords = $this->get('settong.toolManageRepo')->intToWords($entity->getTotal());
        $entity->setPaymentInWord($amountInWords);
        $em->flush();
        if ($entity->getTotal() > 0) {
            $this->getDoctrine()->getRepository('RestaurantBundle:Particular')->insertAccessories($entity);
            $accountInvoice = $em->getRepository('AccountingBundle:AccountSales')->insertRestaurantAccountInvoice($entity);
            $em->getRepository('AccountingBundle:Transaction')->restaurantSalesTransaction($entity, $accountInvoice);
        }

    }

    public function productsAction(Invoice $invoice)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        $pagination         = $em->getRepository('RestaurantBundle:Particular')->getServiceLists($invoice ,array('product','stockable'));
        return new Response($pagination);

    }

    public function todaySalesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $salesOverview      = $this->getDoctrine()->getRepository('RestaurantBundle:Invoice')->findWithSalesOverview($this->getUser());
        $created            = date('y-m-d');
        $sales              = $em->getRepository('RestaurantBundle:Invoice')->invoiceLists( $this->getUser() , array('created' => $created));
        $salesLists         = $this->paginate($sales);
        $template = $this->get('twig')->render('RestaurantBundle:Invoice:product.html.twig',array(
            'salesLists' => $salesLists,
        ));
        $data = array('overview'=> $salesOverview ,'products' =>$template );
        return new Response($template);

    }

}

