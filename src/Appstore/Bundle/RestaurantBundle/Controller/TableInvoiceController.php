<?php

namespace Appstore\Bundle\RestaurantBundle\Controller;

use Appstore\Bundle\RestaurantBundle\Entity\Invoice;
use Appstore\Bundle\RestaurantBundle\Entity\InvoiceParticular;
use Appstore\Bundle\RestaurantBundle\Entity\Particular;
use Appstore\Bundle\RestaurantBundle\Entity\RestaurantAndroidProcess;
use Appstore\Bundle\RestaurantBundle\Entity\RestaurantConfig;
use Appstore\Bundle\RestaurantBundle\Entity\RestaurantTableInvoice;
use Appstore\Bundle\RestaurantBundle\Entity\RestaurantTableInvoiceItem;
use Appstore\Bundle\RestaurantBundle\Entity\RestaurantTemporary;
use Appstore\Bundle\RestaurantBundle\Form\InvoiceType;
use Appstore\Bundle\RestaurantBundle\Form\RestaurantParticularType;
use Appstore\Bundle\RestaurantBundle\Form\RestaurantTemporaryParticularType;
use Appstore\Bundle\RestaurantBundle\Form\TableInvoiceType;
use Appstore\Bundle\RestaurantBundle\Form\TemporaryType;
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
class TableInvoiceController extends Controller
{


    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;

        $user = $this->getUser();
        $config = $user->getGlobalOption()->getRestaurantConfig();
        $tables = $em->getRepository('RestaurantBundle:Particular')->findBy(array('restaurantConfig' => $config , 'service' => 1));
        $em->getRepository('RestaurantBundle:RestaurantTableInvoice')->generateTableInvoice( $config,$tables);
        return $this->redirect($this->generateUrl('restaurant_tableinvoice_new'));
    }

    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $config = $user->getGlobalOption()->getRestaurantConfig();
        $entity = $em->getRepository('RestaurantBundle:RestaurantTableInvoice')->fastTableInvoice( $config);
        $form = $this->createTemporaryForm($entity);
        $tempTotal = $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantTemporary')->getSubTotalAmount($user);
        $subTotal = !empty($tempTotal['subTotal']) ? $tempTotal['subTotal'] :0;
        $vat = $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantTemporary')->generateVat($user,$subTotal);
        $categories = $em->getRepository('RestaurantBundle:Category')->findBy(array('restaurantConfig' => $config , 'status' => 1));
        $tables = $em->getRepository('RestaurantBundle:Particular')->findBy(array('restaurantConfig' => $config , 'service' => 1),array('id'=>'ASC'));
        $servings = $em->getRepository('UserBundle:User')->getEmployeeEntities($user->getGlobalOption());
        $initialTotal = ($subTotal + $vat);
        $entities = $em->getRepository('RestaurantBundle:RestaurantTableInvoice')->findBy(array('restaurantConfig' => $config));

        return $this->render('RestaurantBundle:TableInvoice:new.html.twig', array(

            'config'                => $config,
            'categories'            => $categories,
            'tables'                => $tables,
            'servings'              => $servings,
            'entity'                => $entity,
            'tableEntities'         => $entities,
            'form'                  => $form->createView(),

        ));

    }

    public function invoiceLoadAction(RestaurantTableInvoice $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $config = $user->getGlobalOption()->getRestaurantConfig();
        $form = $this->createTemporaryForm($entity);
        $categories = $em->getRepository('RestaurantBundle:Category')->findBy(array('restaurantConfig' => $config , 'status' => 1));
        $tables = $em->getRepository('RestaurantBundle:Particular')->findBy(array('restaurantConfig' => $config , 'service' => 1),array('id'=>'ASC'));
        $servings = $em->getRepository('UserBundle:User')->getEmployeeEntities($user->getGlobalOption());
        $html = $this->renderView(
            'RestaurantBundle:TableInvoice:ajaxTransaction.html.twig', array(
                'config'                => $config,
                'categories'            => $categories,
                'tables'                => $tables,
                'servings'              => $servings,
                'entity'                => $entity,
                'form'                  => $form->createView(),
            )
        );
        return new Response($html);
    }


    private function createTemporaryForm(RestaurantTableInvoice $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new TableInvoiceType($globalOption), $entity, array(
            'action' => $this->generateUrl('restaurant_tableinvoice_create',array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'id' => 'invoiceForm',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    public function updateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $id = $data['invoiceEntity'];
        $form = $data['restaurant_invoice'];
        $entity = $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantTableInvoice')->find($id);
        $entity->setProcess($data['process']);
        if($form['invoiceMode']){
            $m = $form['invoiceMode'];
            $mode = $this->getDoctrine()->getRepository('RestaurantBundle:Particular')->find($m);
            $entity->setInvoiceMode($mode);
        }
        if($data['method']){
            $m = $data['method'];
            $mode = $this->getDoctrine()->getRepository('SettingToolBundle:TransactionMethod')->findOneBy(array('name'=>$m));
            $entity->setTransactionMethod($mode);
        }
        if($form['salesBy']){
            $salesBy = $this->getDoctrine()->getRepository('UserBundle:User')->find($form['salesBy']);
            $entity->setSalesBy($salesBy);
        }
        $calculation = (float)$form['discountCalculation'];
        $discountType = $form['discountType'];
        $discountCoupon = $form['discountCoupon'];
        $entity->setDiscountType($discountType);
        $entity->setDiscountCalculation($calculation);
        $entity->setDiscountCoupon($discountCoupon);
        $em->persist($entity);
        $em->flush();
        $result = $this->returnResultData($entity);
        return new Response(json_encode($result));

    }

    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = New Invoice();
        $user = $this->getUser();
        $option = $user->getGlobalOption();
        $config = $option->getRestaurantConfig();
        $tempTotal = $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantTemporary')->getSubTotalAmount($user);
        $subTotal = !empty($tempTotal['subTotal']) ? $tempTotal['subTotal'] :0;
        $purchasePrice = !empty($tempTotal['purchasePrice']) ? $tempTotal['purchasePrice'] :0;
        $masterData = $request->request->all();
        $data = $masterData['restaurant_invoice'];
        $btn = $request->request->get('buttonType');
        $tableNos = $request->request->get('tableNos');
        $form = $this->createTemporaryForm($entity);
        $form->handleRequest($request);
        $entity->setRestaurantConfig($config);
        if($tableNos){
            $entity->setTableNos($tableNos);
        }
        if(empty($entity->getSalesBy())){
            $entity->setSalesBy($this->getUser());
        }
        if (isset($masterData['customerMobile']) and !empty($masterData['customerMobile'])) {
            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($masterData['customerMobile']);
            $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->newExistingCustomerForSales($option, $mobile, $masterData);
            $entity->setCustomer($customer);
        } elseif (isset($masterData['mobile']) and !empty($masterData['mobile'])) {
            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($masterData['mobile']);
            $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $option, 'mobile' => $mobile));
            $entity->setCustomer($customer);
        }else{
            $customer = $em->getRepository('DomainUserBundle:Customer')->defaultCustomer($option);
            $entity->setCustomer($customer);
        }
        $entity->setPaymentStatus('Pending');
        $entity->setSubTotal($subTotal);
        $entity->setPurchasePrice($purchasePrice);
        $vat = $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantTemporary')->generateVat($user,$subTotal);
        $entity->setVat($vat);
        $total = round(($subTotal - $entity->getDiscount()) + $entity->getVat());
        $entity->setTotal($total);
        if ($entity->getTotal() > 0) {
            $entity->setProcess('Done');
        }
        $entity->setCreatedBy($this->getUser());
        $deliveryDateTime = $request->request->get('deliveryDateTime');
        $datetime = empty($deliveryDateTime) ? '' : $deliveryDateTime ;
        $entity->setDeliveryDateTime($datetime);
        if(($entity->getTotal() > 0 and $entity->getPayment() >= $entity->getTotal() and $entity->isHold() != 1)){
            $entity->setPayment($entity->getTotal());
            $entity->setPaymentStatus("Paid");
            $entity->setDue(0);
            if($data['payment'] > $entity->getTotal()){
                $amount = $data['payment'] - $entity->getTotal();
                $entity->setReturnAmount($amount);
            }

        }else if(($entity->getTotal() > 0 and !empty($entity->getPayment()) and $entity->isHold() != 1)){
            $payment = floatval($data['payment']);
            $entity->setPayment($payment);
            $entity->setPaymentStatus("Due");
            $amount = $entity->getTotal() -  $entity->getPayment();
            if($amount > 0){
                $entity->setDue($amount);
            }else{
                $entity->setReturnAmount(abs($amount));
            }
        }elseif($entity->getRestaurantConfig()->isAutoPayment() == 1 and empty($entity->getPayment()) and $entity->isHold() != 1){
            $entity->setPayment($entity->getTotal());
            $entity->setPaymentStatus("Paid");
            $entity->setDue(0);
        }
        if($entity->isHold() == 1){
            $entity->setProcess('Hold');
        }
        $amountInWords = $this->get('settong.toolManageRepo')->intToWords(round($entity->getTotal()));
        $entity->setPaymentInWord($amountInWords);
        if($entity->getSubTotal()){
            $em->persist($entity);
            $em->flush();
        }else{
            return new Response("failed");
        }
        $this->getDoctrine()->getRepository('RestaurantBundle:InvoiceParticular')->initialInvoiceItems($user,$entity);
        $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantTemporary')->removeInitialParticular($this->getUser());
        if($entity->isHold() != 1){
            $this->getDoctrine()->getRepository('RestaurantBundle:Particular')->insertAccessories($entity);
            $em->getRepository('AccountingBundle:AccountSales')->insertRestaurantAccountInvoice($entity);
        }
        if($entity->getRestaurantConfig()->isStockHistory() == 1 ) {
            $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantStockHistory')->processInsertSalesItem($entity);
        }
        if($btn == "posBtn" and $entity->isHold() != 1 ){
            $invoiceParticulars = $this->getDoctrine()->getRepository('RestaurantBundle:InvoiceParticular')->findBy(array('invoice' => $entity->getId()));
            $pos = $this->posPrint($entity,$invoiceParticulars);
            return new Response($pos);
        }
        return new Response("success");

    }



    public function invoiceDiscountCouponAction(Request $request)
    {
        $user = $this->getUser();
        /* @var $config RestaurantConfig */
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        $discount = $request->request->get('discount');
        $tempTotal = $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantTemporary')->getSubTotalAmount($user);
        $subTotal = !empty($tempTotal['subTotal']) ? $tempTotal['subTotal'] :0;
        if($config->getDiscountType() == 'flat' and !empty($discount)){
            $initialGrandTotal = ($subTotal  - $config->getDiscountPercentage());
        }elseif($config->getDiscountType() == 'percentage' and !empty($discount)){
            $discount = ($subTotal * $config->getDiscountPercentage())/100;
            $initialGrandTotal = ($subTotal  - $discount);
        }
        $result = $this->returnResultData($user);
        return new Response(json_encode($result));

    }

    public function particularSearchAction(Particular $particular)
    {
        return new Response(json_encode(array('particularId'=> $particular->getId() ,'price'=> $particular->getPrice() , 'quantity'=> $particular->getQuantity(), 'minimumPrice'=> $particular->getMinimumPrice(), 'instruction'=> $particular->getInstruction())));
    }

    public function returnResultData(RestaurantTableInvoice $invoice){


        $invoiceParticulars = $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantTableInvoice')->getSalesGridItems($invoice);
        $entity = $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantTableInvoice')->updateInvoiceTotalPrice($invoice);

        $data = array(
            'subTotal'           => $entity->getSubTotal(),
            'discount'           => $entity->getDiscount(),
            'invoiceParticulars' => $invoiceParticulars ,
            'vat'                => $entity->getVat() ,
            'total'              => $entity->getTotal() ,
            'process'            => $entity->getProcess() ,
            'entity'            => $entity->getId() ,
            'success'            => 'success'
        );
        return $data;

    }

    public function invoiceProcessAction(RestaurantTableInvoice $invoice)
    {
        $em = $this->getDoctrine()->getManager();
        $process = $_REQUEST['process'];
        $invoice->setProcess($process);
        $em->flush();
        $result = $this->returnResultData($invoice);
        var_dump($result);
        exit;
        return new Response(json_encode($result));
    }

    public function addProductAction($product)
    {
        $id = $_REQUEST['invoice'];
        $invoice = $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantTableInvoice')->find($id);
        $entity = $this->getDoctrine()->getRepository('RestaurantBundle:Particular')->find($product);
        $invoiceItems = array('particularId' => $product , 'quantity' => 1,'price' => $entity->getPrice(),'process'=>'create');
        $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantTableInvoice')->insertInvoiceItems($invoice, $invoiceItems);
        $result = $this->returnResultData($invoice);
        return new Response(json_encode($result));
    }

    public function updateProductAction(Request $request , $product)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $quantity = $_REQUEST['quantity'];
        $entity = $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantTableInvoiceItem')->find($product);
        $invoiceItems = array('particularId' => $product , 'quantity' => $quantity,'price' => $entity->getPrice(),'process' => 'update');
        $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantTableInvoice')->insertInvoiceItems($entity->getTableInvoice(), $invoiceItems);
        $result = $this->returnResultData($entity->getTableInvoice());
        return new Response(json_encode($result));
    }


    public function invoiceParticularDeleteAction(RestaurantTableInvoice $invoice,RestaurantTableInvoiceItem $particular){

        $em = $this->getDoctrine()->getManager();
        if (!$particular) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }
        $em->remove($particular);
        $em->flush();
        $result = $this->returnResultData($invoice);
        return new Response(json_encode($result));

    }

    public function kitchenPrintAction(RestaurantTableInvoice $entity)
    {

        $isPrints = $_REQUEST['isPrint'];
        $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantTableInvoice')->updateKitchenPrint($entity,$isPrints);
        exit;

        $connector = new \Mike42\Escpos\PrintConnectors\DummyPrintConnector();
        $printer = new Printer($connector);
        $printer -> initialize();

        $em = $this->getDoctrine()->getManager();
        $option = $this->getUser()->getGlobalOption();
        $config = $entity->getRestaurantConfig();

        $address        = $config->getAddress();
        $companyName    = $option->getName();



        /** ===================Customer Information=================================== */


        $salesBy    = $entity->getSalesBy();
        $tableNo    = $entity->getTable()->getName();
        $table = "Table no. {$tableNo}";
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
        if($entity->getRestaurantConfig()->isPrintToken() == 1){
            $token = $this->getDoctrine()->getRepository('RestaurantBundle:Invoice')->getLastCode($entity);
            $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer -> text("Token No-{$token}\n\n");
            $printer -> selectPrintMode();
            $printer -> feed();
        }

        /* Title of receipt */
        $printer->setFont(Printer::FONT_A);
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $printer -> text("KITCHEN PRINT");
        $printer -> text("\n");
        if($entity->getRestaurantConfig()->isPrintToken() == 1){
            $token = $this->getDoctrine()->getRepository('RestaurantBundle:Invoice')->getLastCode($entity);
            $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer -> text("Token No-{$token}\n\n");
            $printer -> selectPrintMode();
            $printer -> feed();
        }
        $printer -> text("Invoice no. {$entity->getInvoice()}\n");
        $printer -> selectPrintMode();
        $printer -> setEmphasis(true);
        $printer -> text("{$table}\n");
        $printer -> setJustification(Printer::JUSTIFY_LEFT);
        $printer -> setFont(Printer::FONT_B);
        $printer -> setEmphasis(true);
        $printer -> text(new PosItemManager('Item Name', 'Qnt', 'Amount'));
        $printer -> text("------------------------------------------------------------\n");
        $i=1;
        /* @var $row InvoiceParticular */
        foreach ( $invoiceParticulars as $row){
            $productName = "{$i}. {$row->getParticular()->getName()}";
            $printer -> text(new PosItemManager($productName,$row->getQuantity(),number_format($row->getSubTotal())));
            $i++;
        }
        $printer -> text("------------------------------------------------------------\n");
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> text("Served By: ".$salesBy."\n");
        $response =  base64_encode($connector->getData());
        $printer -> close();
        return $response;
    }

    private function posPrint(Invoice $entity,$invoiceParticulars)
    {
        $connector = new \Mike42\Escpos\PrintConnectors\DummyPrintConnector();
        $printer = new Printer($connector);
        $printer -> initialize();

        $em = $this->getDoctrine()->getManager();
        $option = $this->getUser()->getGlobalOption();
        $config = $entity->getRestaurantConfig();

        $address        = $config->getAddress();
        $vatRegNo       = $config->getVatRegNo();
        $companyName    = $option->getName();
        $website        = $option->getDomain();


        /** ===================Customer Information=================================== */

        $invoice            = $entity->getInvoice();
        $subTotal           = $entity->getSubTotal();
        $total              = $entity->getTotal();
        $discount           = $entity->getDiscount();
        $vat                = $entity->getVat();
        $dueBdt             = $entity->getDue();
        $payment            = $entity->getPayment();
        $returnBdt          = $entity->getReturnAmount();
        $transaction        = $entity->getTransactionMethod()->getName();
        $salesBy            = $entity->getSalesBy();

        $slipNo ='';
        $tableNo ='';
        if($entity->getSlipNo()){
            $slipNo = "{$entity->getSlipNo()} / ";
        }
        if($entity->getTableNos()){
            $tableNo = implode(",",$entity->getTableNos());
        }
        $table = "Table no. {$slipNo}{$tableNo}";

        $transaction    = new PosItemManager('Pay Mode: '.$transaction,'','');
        $subTotal       = new PosItemManager('SubTotal: ','Tk.',number_format($subTotal));
        $vat            = new PosItemManager('Vat: ','Tk.',number_format($vat));
        $discount       = new PosItemManager('Discount: ','Tk.',number_format($discount));
        $grandTotal     = new PosItemManager('Payable: ','Tk.',number_format($total));
        $payment        = new PosItemManager('Received: ','Tk.',number_format($payment));
        $due            = new PosItemManager('Due: ','Tk.',number_format($dueBdt));
        $returnTk       = new PosItemManager('Return: ','Tk.',number_format($returnBdt));


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
            $printer -> text("BIN No - ".$vatRegNo."\n\n");
        }
        if($entity->getRestaurantConfig()->isPrintToken() == 1){
            $token = $this->getDoctrine()->getRepository('RestaurantBundle:Invoice')->getLastCode($entity);
            $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer -> text("Token No-{$token}\n\n");
            $printer -> selectPrintMode();
            $printer -> feed();
        }
        /* Title of receipt */
        $printer -> setJustification(Printer::JUSTIFY_LEFT);
        $printer->setFont(Printer::FONT_B);
        $printer -> setJustification(Printer::JUSTIFY_LEFT);
        $printer -> setEmphasis(true);
        $printer -> text("Invoice no. {$entity->getInvoice()}                  {$table}\n");
        $printer -> setEmphasis(false);
        $printer -> text("Date: {$date}          {$transaction}\n");
        $printer -> text(new PosItemManager('Item Name', 'Qnt', 'Amount'));
        $printer -> text("---------------------------------------------------------------\n");
        $i=1;
        /* @var $row InvoiceParticular */
        foreach ( $invoiceParticulars as $row){
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
        $printer -> text($payment);
        $printer -> text("---------------------------------------------------------------\n");
        if($dueBdt > 0){
            $printer -> text($due);
        }
        if($returnBdt > 0){
            $printer -> text($returnTk);
        }
        $printer -> setUnderline(Printer::UNDERLINE_NONE);
        if($config->getInvoiceNote()){
            $printer -> setJustification(Printer::JUSTIFY_LEFT);
            $printer -> text($config->getInvoiceNote()."\n");
        }
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> text("Served By: ".$salesBy."\n");
        $printer -> text("Thanks for being here\n");
        if($website){
            $printer -> text("** Visit www.".$website."**\n");
        }
        $printer -> text("Powered by - www.terminalbd.com - 01828148148 \n");

        if($config->isKitchenPrint() == 1 ){
            $printer->cut();
            $printer->setFont(Printer::FONT_A);
            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer -> text("KITCHEN PRINT");
            $printer -> text("\n");
            if($entity->getRestaurantConfig()->isPrintToken() == 1){
                $token = $this->getDoctrine()->getRepository('RestaurantBundle:Invoice')->getLastCode($entity);
                $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
                $printer -> setJustification(Printer::JUSTIFY_CENTER);
                $printer -> text("Token No-{$token}\n\n");
                $printer -> selectPrintMode();
                $printer -> feed();
            }
            $printer -> text("Invoice no. {$entity->getInvoice()}\n");
            $printer -> selectPrintMode();
            $printer -> setEmphasis(true);
            $printer -> text("{$table}\n");
            $printer -> setJustification(Printer::JUSTIFY_LEFT);
            $printer->setFont(Printer::FONT_B);
            $printer -> setEmphasis(true);
            $printer -> text(new PosItemManager('Item Name', 'Qnt', 'Amount'));
            $printer -> text("------------------------------------------------------------\n");
            $i=1;
            /* @var $row InvoiceParticular */
            foreach ( $invoiceParticulars as $row){
                $productName = "{$i}. {$row->getParticular()->getName()}";
                $printer -> text(new PosItemManager($productName,$row->getQuantity(),number_format($row->getSubTotal())));
                $i++;
            }
            $printer -> text("------------------------------------------------------------\n");
        }
        if($config->isDeliveryPrint() == 1 ){
            $printer->cut();
            $printer->setFont(Printer::FONT_A);
            $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer -> text("DELIVERY PRINT");
            $printer -> text("\n");
            if($entity->getRestaurantConfig()->isPrintToken() == 1){
                $token = $this->getDoctrine()->getRepository('RestaurantBundle:Invoice')->getLastCode($entity);
                $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
                $printer -> setJustification(Printer::JUSTIFY_CENTER);
                $printer -> text("Token No-{$token}\n\n");
                $printer -> selectPrintMode();
                $printer -> feed();
            }
            $printer -> text("Invoice no. {$entity->getInvoice()}\n");
            $printer -> selectPrintMode();
            $printer -> setEmphasis(true);
            $printer -> text("{$table}\n");
            $printer -> setJustification(Printer::JUSTIFY_LEFT);
            $printer->setFont(Printer::FONT_B);
            $printer -> setEmphasis(true);
            $printer -> text(new PosItemManager('Item Name', 'Qnt', 'Amount'));
            $printer -> text("------------------------------------------------------------\n");
            $i=1;
            /* @var $row InvoiceParticular */
            foreach ( $invoiceParticulars as $row){
                $productName = "{$i}. {$row->getParticular()->getName()}";
                $printer -> text(new PosItemManager($productName,$row->getQuantity(),number_format($row->getSubTotal())));
                $i++;
            }
            $printer -> text("------------------------------------------------------------\n");
        }
        $response =  base64_encode($connector->getData());
        $printer -> close();
        return $response;
    }


}

