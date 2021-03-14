<?php

namespace Appstore\Bundle\InventoryBundle\Controller;


use Appstore\Bundle\InventoryBundle\Entity\Item;
use Appstore\Bundle\InventoryBundle\Entity\Pos;
use Appstore\Bundle\InventoryBundle\Form\PosType;
use Appstore\Bundle\InventoryBundle\Service\Cart;
use Appstore\Bundle\InventoryBundle\Service\PosItemManager;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use Frontend\FrontentBundle\Service\MobileDetect;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * InvoiceController controller.
 *
 */
class PosController extends Controller
{


    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $config = $user->getGlobalOption()->getinventoryConfig();
        $entity = new Pos();
        $form = $this->createTemporaryForm($entity);
        $categories = $em->getRepository('InventoryBundle:Item')->groupByItemCategory($config);
        $cart = new Cart($request->getSession());
        return $this->render('InventoryBundle:Pos:new.html.twig', array(
            'config'                => $config,
            'categories'            => $categories,
            'cart' => $cart,
            'tables'                => '',
            'servings'              => '',
            'entity'                => $entity,
            'tableEntities'         => '',
            'form'                  => $form->createView(),

        ));

    }

    public function posItemsAction(Request $request){

        $user = $this->getUser();
        $config = $user->getGlobalOption();
        $data = "";
        if($config->getInventoryConfig()->getUsingBarcode() == "item"){
            $data = $this->getDoctrine()->getRepository("InventoryBundle:Item")->getApiStock($config);
        }
        $cart = new Cart($request->getSession());
        $html = $this->renderView(
            'InventoryBundle:Pos:item.html.twig', array(
                'entities' => $data,
                'cart' => $cart,
                'config' => $config->getInventoryConfig()
            )
        );
        return new Response($html);
    }


    public function productCartAction(Request $request,Item $product)
    {

        $cart = new Cart($request->getSession());
        $quantity = $_REQUEST['quantity'];
        $salesPrice = $product->getDiscountPrice() > 0 ?  $product->getDiscountPrice() : $product->getSalesPrice();
        $productUnit = ($product->getMasterItem()) ? $product->getMasterItem()->getUnit(): '';
        $data = array(
            'id' => $product->getId(),
            'name' => $product->getName(),
            'unit' => $productUnit,
            'price' => $salesPrice,
            'quantity' => $quantity,
        );
        $cart->insert($data);
        $array = $this->returnCartSummaryAjaxData($cart);
        return new Response($array);

    }

    public function productUpdateCartAction(Request $request,$product)
    {

        $cart = new Cart($request->getSession());
        $quantity = (int)$_REQUEST['quantity'];
        $data = array(
            'rowid' => $product,
            'quantity' => $quantity,
        );
        $cart->update($data);
        $array = $this->returnCartSummaryAjaxData($cart);
        return new Response($array);

    }

    public function productRemoveCartAction(Request $request, $cartid)
    {
        $cart = new Cart($request->getSession());
        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('subDomain'=>$subdomain));
        $cart->remove($cartid);
        $array = $this->returnCartSummaryAjaxData($cart);
        return new Response($array);
    }

    public function updateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $cart = new Cart($request->getSession());
        $form = $data['pos'];
        echo $calculation = (float)$form['discountCalculation'];
        $discountType = $form['discountType'];
        $discountCoupon = $form['discountCoupon'];
        $data = array(
            'discountPercent' => $calculation,
            'discount' => $calculation,
            'vat' => $calculation,
        );
        $cart->entityUpdate($data);
        $result = $this->returnCartSummaryAjaxData($cart);
        return new Response(json_encode($result));

    }



    private function returnCartSummaryAjaxData(Request $request)
    {
        $cart = new Cart($request->getSession());
        $data = array(
            'cartTotal' =>  (string)$amount,
            'grandTotal' =>  (string)$grandTotal,
            'totalItems' => count($cart->contents()),
            'totalQuantity' => (string)$cart->total_items(),
            'cartResult' => count($cart->contents())." | ৳ ".(string)$amount,
            'process' => "success"
        );

        $subTotal = number_format($cart->total(), 2, '.', '');
        $discount = number_format($cart->discount(), 2, '.', '');
        $percent = number_format($cart->discountPercent(), 2, '.', '');
        $vat = number_format($cart->vat(), 2, '.', '');
        $total = ($subTotal-$discount+$vat);
        $total = number_format($total, 2, '.', '');


        $htmlProcess = $this->renderView(
            'InventoryBundle:Pos:ajaxTableItem.html.twig', array(
                'entity'         => $entity
            )
        );
        $data = array(
            'subTotal'           => $subTotal,
            'discountPercent'    => $percent,
            'discount'           => $discount,
            'invoiceParticulars' => $htmlProcess ,
            'vat'                => $entity->getVat() ,
            'total'              => $entity->getTotal() ,
            'process'            => $process ,
            'entity'             => $entity->getId() ,
            'success'            => 'success'
        );
        return $data;

        $array = json_encode($data);
        return $array;
    }


    private function createTemporaryForm(Pos $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new PosType($globalOption), $entity, array(
            'action' => $this->generateUrl('restaurant_tableinvoice_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'id' => 'invoiceForm',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    public function invoiceLoadAction(Pos $entity)
    {

        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $config = $user->getGlobalOption()->getinventoryConfig();
        $form = $this->createTemporaryForm($entity);
        $categories = $em->getRepository('InventoryBundle:Category')->findBy(array('inventoryConfig' => $config , 'status' => 1));
        $tables = $em->getRepository('InventoryBundle:Particular')->findBy(array('inventoryConfig' => $config , 'service' => 1),array('id'=>'ASC'));
        $servings = $em->getRepository('UserBundle:User')->getEmployeeEntities($user->getGlobalOption());
        $invoice = $em->getRepository('InventoryBundle:Pos')->updateInvoiceActive($entity);
        $html = $this->renderView(
            'InventoryBundle:TableInvoice:ajaxTransaction.html.twig', array(
                'config'                => $config,
                'categories'            => $categories,
                'tables'                => $tables,
                'servings'              => $servings,
                'entity'                => $invoice,
                'form'                  => $form->createView(),
            )
        );
        $htmlProcess = $this->renderView(
            'InventoryBundle:TableInvoice:ajaxProcess.html.twig', array(
                'config'                => $config,
                'entity'                => $invoice
            )
        );
        $array = array(
            'body' => $html,'htmlProcess' => $htmlProcess,'total' => $entity->getTotal()
        );
        return new Response(json_encode($array));
    }





    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $masterData = $request->request->all();
        $id = $masterData['invoiceEntity'];
        $entity = new Invoice();
        $invoice = $this->getDoctrine()->getRepository('InventoryBundle:Pos')->find($id);
        $user = $this->getUser();
        $data = $masterData['restaurant_invoice'];
        $btn = $request->request->get('buttonType');
        $tableNos = $request->request->get('tableNos');
        $entity->setinventoryConfig($invoice->getinventoryConfig());
        if($tableNos){
            $entity->setTableNos($tableNos);
        }
        if(empty($invoice->getSalesBy())){
            $entity->setSalesBy($this->getUser());
        }else{
            $entity->setSalesBy($invoice->getSalesBy());
        }
        if (isset($masterData['customerMobile']) and !empty($masterData['customerMobile'])) {
            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($masterData['customerMobile']);
            $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->newExistingCustomerForSales($user->getGlobalOption(), $mobile, $masterData);
            $entity->setCustomer($customer);
        } elseif (isset($masterData['mobile']) and !empty($masterData['mobile'])) {
            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($masterData['mobile']);
            $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $user->getGlobalOption(), 'mobile' => $mobile));
            $entity->setCustomer($customer);
        }else{
            $customer = $em->getRepository('DomainUserBundle:Customer')->defaultCustomer($user->getGlobalOption());
            $entity->setCustomer($customer);
        }
        $entity->setSubTotal($invoice->getSubTotal());
        $entity->setDiscountType($invoice->getDiscountType());
        $entity->setDiscountCalculation($invoice->getDiscountCalculation());
        $entity->setDiscount($invoice->getDiscount());
        $entity->setVat($invoice->getVat());
        $entity->setSd($invoice->getSd());
        $entity->setInvoiceMode($invoice->getInvoiceMode());
        $entity->setTransactionMethod($invoice->getTransactionMethod());
        $entity->setTotal($invoice->getTotal());
        $entity->setTable($invoice->getTable());
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
        }elseif($entity->getinventoryConfig()->isAutoPayment() == 1 and empty($entity->getPayment()) and $entity->isHold() != 1){
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
            $this->getDoctrine()->getRepository('InventoryBundle:InvoiceParticular')->tableInvoiceItems($entity,$invoice);
            $this->getDoctrine()->getRepository('InventoryBundle:Pos')->resetData($invoice);
        }else{
            return new Response("failed");
        }
        $this->getDoctrine()->getRepository('InventoryBundle:RestaurantTemporary')->removeInitialParticular($this->getUser());
        if($entity->isHold() != 1){
            $this->getDoctrine()->getRepository('InventoryBundle:Particular')->insertAccessories($entity);
            $em->getRepository('AccountingBundle:AccountSales')->insertRestaurantAccountInvoice($entity);
        }
        if($entity->getinventoryConfig()->isStockHistory() == 1 ) {
            $this->getDoctrine()->getRepository('InventoryBundle:RestaurantStockHistory')->processInsertSalesItem($entity);
        }
        if($btn == "posBtn" and $entity->isHold() != 1 ){
            $pos = $this->posPrint($entity);
            return new Response($pos);
        }
    }


    public function particularSearchAction(Particular $particular)
    {
        return new Response(json_encode(array('particularId'=> $particular->getId() ,'price'=> $particular->getPrice() , 'quantity'=> $particular->getQuantity(), 'minimumPrice'=> $particular->getMinimumPrice(), 'instruction'=> $particular->getInstruction())));
    }

    public function returnResultData(Pos $invoice){

        $entity = $this->getDoctrine()->getRepository('InventoryBundle:Pos')->updateInvoiceTotalPrice($invoice);
        $process = empty($entity->getProcess()) ? "Free" : $entity->getProcess();
        $htmlProcess = $this->renderView(
            'InventoryBundle:TableInvoice:ajaxTableItem.html.twig', array(
                'entity'         => $entity
            )
        );
        $data = array(
            'subTotal'           => $entity->getSubTotal(),
            'discount'           => $entity->getDiscount(),
            'invoiceParticulars' => $htmlProcess ,
            'vat'                => $entity->getVat() ,
            'total'              => $entity->getTotal() ,
            'process'            => $process ,
            'entity'             => $entity->getId() ,
            'success'            => 'success'
        );
        return $data;

    }

    public function invoiceProcessAction(Pos $invoice)
    {
        $em = $this->getDoctrine()->getManager();
        $process = $_REQUEST['process'];
        if($process == "Free"){
            $this->getDoctrine()->getRepository('InventoryBundle:Pos')->resetData($invoice);
            $result = $this->returnResultData($invoice);
            return new Response(json_encode($result));
        }else{
            if(empty($invoice->getOrderDate())){
                $now = new \DateTime("now");
                $invoice->setOrderDate($now);
            }
            $invoice->setProcess($process);
            $em->flush();
            $d = strtotime($invoice->getOrderDate()->format('d-m-Y h:i:A'));
            $date = new \DateTime($d, new \DateTimeZone('Asia/Dhaka'));
            $time = $date->format('h:i A');
            $result = array('process'=>$process,'orderTime' => $time);
            return new Response(json_encode($result));
        }

    }

    public function addProductAction($product)
    {
        $id = $_REQUEST['invoice'];
        $invoice = $this->getDoctrine()->getRepository('InventoryBundle:Pos')->find($id);
        $entity = $this->getDoctrine()->getRepository('InventoryBundle:Particular')->find($product);
        $invoiceItems = array('particularId' => $product , 'quantity' => 1,'price' => $entity->getPrice(),'process'=>'create');
        $this->getDoctrine()->getRepository('InventoryBundle:Pos')->insertInvoiceItems($invoice, $invoiceItems);
        $result = $this->returnResultData($invoice);
        return new Response(json_encode($result));
    }

    public function updateProductAction(Request $request , $product)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $quantity = $_REQUEST['quantity'];
        /* @var $entity PosItem */
        $entity = $this->getDoctrine()->getRepository('InventoryBundle:PosItem')->find($product);
        $invoiceItems = array('particularId' => $entity->getParticular()->getId() , 'quantity' => $quantity,'price' => $entity->getSalesPrice(),'process' => 'update');
        $this->getDoctrine()->getRepository('InventoryBundle:Pos')->insertInvoiceItems($entity->getTableInvoice(), $invoiceItems);
        $result = $this->returnResultData($entity->getTableInvoice());
        return new Response(json_encode($result));
    }

    public function invoiceParticularDeleteAction(Pos $invoice,PosItem $particular){

        $em = $this->getDoctrine()->getManager();
        if (!$particular) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }
        $em->remove($particular);
        $em->flush();
        $result = $this->returnResultData($invoice);
        return new Response(json_encode($result));

    }

    public function kitchenPrintAction(Pos $entity)
    {

        $isPrints = $_REQUEST['isPrint'];
        $this->getDoctrine()->getRepository('InventoryBundle:Pos')->updateKitchenPrint($entity,$isPrints);
        $connector = new \Mike42\Escpos\PrintConnectors\DummyPrintConnector();
        $printer = new Printer($connector);
        $printer -> initialize();

        $em = $this->getDoctrine()->getManager();
        $option = $this->getUser()->getGlobalOption();
        $config = $entity->getinventoryConfig();

        $address        = $config->getAddress();
        $companyName    = $option->getName();

        /** ===================Customer Information=================================== */


        $salesBy    = $entity->getSalesBy();
        $tableNo    = $entity->getTable()->getName();
        $table = "Table No. {$tableNo}";

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
        $printer->setFont(Printer::FONT_A);
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $printer -> text("KITCHEN PRINT");
        $printer -> text("\n");
        $printer -> selectPrintMode();
        $printer -> setEmphasis(true);
        $printer -> text("{$table}\n");
        $printer -> setJustification(Printer::JUSTIFY_LEFT);
        $printer -> setFont(Printer::FONT_B);
        $printer -> setEmphasis(true);
        $printer -> text(new PosItemManager('Item Name', 'Qnt', 'Amount'));
        $printer -> text("------------------------------------------------------------\n");
        $i = 1;
        $invoiceItems = $this->getDoctrine()->getRepository("InventoryBundle:PosItem")->findBy(array('tableInvoice'=>$entity,'isPrint' => 1));
        /* @var $row PosItem */

        foreach ( $invoiceItems as $row){
            $productName = "{$i}. {$row->getParticular()->getName()}";
            $printer -> text(new PosItemManager($productName,$row->getQuantity(),number_format($row->getSubTotal())));
            $i++;
        }
        $printer -> text("------------------------------------------------------------\n");
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> text($date."\n");
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> text("Served By: ".$salesBy."\n");
        $response =  base64_encode($connector->getData());
        $printer -> close();
        return new Response($response) ;
    }

    private function posPrint(Invoice $entity)
    {

        $invoiceParticulars = $this->getDoctrine()->getRepository('InventoryBundle:InvoiceParticular')->findBy(array('invoice' => $entity->getId()));
        $connector = new \Mike42\Escpos\PrintConnectors\DummyPrintConnector();
        $printer = new Printer($connector);
        $printer -> initialize();

        $em = $this->getDoctrine()->getManager();
        $option = $this->getUser()->getGlobalOption();
        $config = $entity->getinventoryConfig();

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
        $transaction        = empty($entity->getTransactionMethod()) ? "Cash" : $entity->getTransactionMethod()->getName();
        $salesBy            = $entity->getSalesBy();
        $table              = $entity->getTable()->getName();

        $slipNo ='';
        $tableNo ='';
        if($entity->getSlipNo()){
            $slipNo = "{$entity->getSlipNo()} / ";
        }
        if($entity->getTableNos()){
            $tableNo = implode(",",$entity->getTableNos());
        }
        $table = "Table no. {$table}";

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
            $printer -> text("BIN No - ".$vatRegNo." Mushak - 6.3\n\n");
        }
        if($entity->getinventoryConfig()->isPrintToken() == 1){
            $token = $this->getDoctrine()->getRepository('InventoryBundle:Invoice')->getLastCode($entity);
            $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer -> text("Token No-{$token}\n\n");
            $printer -> selectPrintMode();
            $printer -> feed();
        }
        /* Title of receipt */
        $printer -> setJustification(Printer::JUSTIFY_LEFT);
        $printer -> setFont(Printer::FONT_B);
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
        if($config->getVatEnable() and $config->getVatMode() == "excluding"){
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
        if($config->getVatMode() == "including"){
            $printer -> setJustification(Printer::JUSTIFY_LEFT);
            $printer -> text("{$config->getVatPercentage()}% VAT Including\n");
        }
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
        if($config->isDeliveryPrint() == 1 ){
            $printer->cut();
            $printer->setFont(Printer::FONT_A);
            $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer -> text("DELIVERY PRINT");
            $printer -> text("\n");
            if($entity->getinventoryConfig()->isPrintToken() == 1){
                $token = $this->getDoctrine()->getRepository('InventoryBundle:Invoice')->getLastCode($entity);
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
