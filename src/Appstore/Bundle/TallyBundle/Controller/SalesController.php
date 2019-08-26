<?php

namespace Appstore\Bundle\TallyBundle\Controller;

use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Appstore\Bundle\TallyBundle\Entity\Item;
use Appstore\Bundle\TallyBundle\Entity\StockItem;
use Appstore\Bundle\TallyBundle\Form\IssueType;
use Appstore\Bundle\TallyBundle\Service\PosItemManager;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use Frontend\FrontentBundle\Service\MobileDetect;
use function GuzzleHttp\Psr7\str;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Appstore\Bundle\TallyBundle\Entity\SalesItem;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Appstore\Bundle\TallyBundle\Entity\Sales;
use Appstore\Bundle\TallyBundle\Form\SalesType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sales controller.
 *
 */
class SalesController extends Controller
{


    public function customerUpdateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $sales = $data['salesId'];
        $payment = $data['sales']['payment'];
        $globalOption = $this->getUser()->getGlobalOption();
        $entity = $this->getDoctrine()->getRepository('TallyBundle:Sales')->find($sales);
        if(!empty($data['mobile'])){
            $exp = explode("-",$data['mobile']);
            $mobile = $exp[0];
            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($mobile);
            $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $globalOption, 'mobile' => $mobile));
            $entity->setCustomer($customer);
        }elseif(!empty($data['customerMobile'])){
            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['customerMobile']);
            $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->newExistingCustomerForSales($globalOption, $mobile, $data);
            $entity->setCustomer($customer);
        }
        if($entity->getNetTotal() > $payment and $payment > 0){
            $entity->setPayment($payment);
            $entity->setPaymentStatus("Due");
            $entity->setDue($entity->getNetTotal() - $payment);
        }else{
            $entity->setPayment($payment);
            $entity->setPaymentStatus("Paid");
        }
        $em->persist($entity);
        $em->flush();
        exit;
    }


    /**
     * @Secure(roles="ROLE_TALLY_ISSUE,ROLE_DOMAIN")
     */

    public function barcodeSearchAction(Request $request)
    {
        $msg ='';
        $em = $this->getDoctrine()->getManager();
        $sales = $request->request->get('sales');
        $barcode = $request->request->get('barcode');
        $sales = $em->getRepository('TallyBundle:Sales')->find($sales);
        $inventory = $this->getUser()->getGlobalOption()->getTallyConfig();
        $purchaseItem = $em->getRepository('TallyBundle:PurchaseItem')->returnPurchaseItemDetails($inventory, $barcode);
        $checkQuantity = $this->getDoctrine()->getRepository('TallyBundle:StockItem')->checkSalesQuantity($purchaseItem['id']);
        if(!empty($purchaseItem) and $purchaseItem['remainingQuantity'] > 0 and  $purchaseItem['remainingQuantity'] >= $checkQuantity) {
            $this->getDoctrine()->getRepository('TallyBundle:StockItem')->insertSalesItems($sales,$purchaseItem['id']);
            $sales = $this->getDoctrine()->getRepository('TallyBundle:Sales')->updateSalesTotalPrice($sales);
            $msg = '<div class="alert alert-success"><strong>Success!</strong> Product added successfully.</div>';

        } else {

            $sales = $this->getDoctrine()->getRepository('TallyBundle:Sales')->updateSalesTotalPrice($sales);
            $msg = '<div class="alert"><strong>Warning!</strong> There is no product in our inventory.</div>';
        }
        $data = $this->returnResultData($sales,$msg);
        return new Response(json_encode($data));

    }


    /**
     * @Secure(roles="ROLE_TALLY_ISSUE,ROLE_DOMAIN")
     */

    public function salesItemSaveAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $salesId = $request->request->get('salesId');
        $purchaseItemId = $request->request->get('purchaseItemId');
        $quantity = $request->request->get('quantity');
        $salesPrice = $request->request->get('salesPrice');
        $serial = $request->request->get('serialNo');
        $purchaseItem = $this->getDoctrine()->getRepository('TallyBundle:PurchaseItem')->find($purchaseItemId);
        $sales = $this->getDoctrine()->getRepository('TallyBundle:Sales')->find($salesId);
        $checkQuantity = $this->getDoctrine()->getRepository('TallyBundle:StockItem')->checkSalesQuantity($purchaseItemId);
        $purchaseItemDetails = $em->getRepository('TallyBundle:PurchaseItem')->returnPurchaseItemDetails($purchaseItem->getConfig()->getId(), $purchaseItem->getBarcode());
        if(!empty($purchaseItem) and $purchaseItemDetails['remainingQuantity'] > 0 and  $purchaseItemDetails['remainingQuantity'] >= $checkQuantity) {
            $existSalesItem = $this->getDoctrine()->getRepository('TallyBundle:StockItem')->findOneBy(array('sales'=>$sales,'purchaseItem'=>$purchaseItem,'mode'=>'sales'));
            if($existSalesItem){
                $salesItem = $existSalesItem;
            }else{
                $salesItem = new StockItem();
            }
            if($serial){
                $ser = explode(",",$serial);
                $quantity = COUNT($ser);
                $em = $this->getDoctrine()->getManager();
                $salesItem->setQuantity("-{$quantity}");
                $salesItem->setSalesQuantity($quantity);
                $salesItem->setSerialNo($serial);
            }else{
                $salesItem->setQuantity("-{$quantity}");
                $salesItem->setSalesQuantity($quantity);
            }
            $salesItem->setSales($sales);
            $salesItem->setPurchaseItem($purchaseItem);
            if($purchaseItem->getItem()->getBrand()){
                $salesItem->setBrand($purchaseItem->getItem()->getBrand());
            }
            if($purchaseItem->getItem()->getCategory()){
                $salesItem->setCategory($purchaseItem->getItem()->getCategory());
            }
            if($purchaseItem->getPurchase()) {
                $salesItem->setPurchase($purchaseItem->getPurchase());
                $salesItem->setVendor($purchaseItem->getPurchase()->getVendor());
            }
            $salesItem->setItem($purchaseItem->getItem());
            $salesItem->setSalesPrice($salesPrice);
            $salesItem->setPrice($salesPrice);
            $salesItem->setSubTotal($salesItem->getSalesQuantity() * $salesPrice);
            $salesItem->setConfig($sales->getConfig());
            $salesItem->setMode('sales');
            $em->persist($salesItem);
            $em->flush();
            $this->getDoctrine()->getRepository('TallyBundle:StockItem')->updateSalesItemPrice($salesItem);
            $sales = $this->getDoctrine()->getRepository('TallyBundle:Sales')->updateSalesTotalPrice($sales);
            $msg = '<div class="alert alert-success"><strong>Success!</strong> Product added successfully.</div>';
        } else {
            $msg = '<div class="alert"><strong>Warning!</strong> There is no product in our inventory.</div>';
        }
        $data = $this->returnResultData($sales,$msg);
        return new Response(json_encode($data));

    }


    /**
     * @Secure(roles="ROLE_TALLY_ISSUE,ROLE_DOMAIN")
     */

    public function itemSerialNoUpdateAction(StockItem  $salesItem)
    {
        $serial = $_REQUEST['serial'];
        if(!empty($serial)){
            $quantity = COUNT($serial);
            $ser = implode(",",$serial);
            $em = $this->getDoctrine()->getManager();
            $salesItem->setQuantity("-{$quantity}");
            $salesItem->setSalesQuantity($quantity);
            $salesItem->setSerialNo($ser);
            $em->flush();
            $this->getDoctrine()->getRepository('TallyBundle:StockItem')->updateSalesItemPrice($salesItem);
            $sales = $this->getDoctrine()->getRepository('TallyBundle:Sales')->updateSalesTotalPrice($salesItem->getSales());
            $data = $this->returnResultData($sales);
            return new Response(json_encode($data));
        }
        exit;
    }


    /**
     * @Secure(roles="ROLE_TALLY_ISSUE,ROLE_DOMAIN")
     */

    public function salesItemUpdateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $salesItemId = $request->request->get('salesItemId');
        $quantity = $request->request->get('quantity');
        $salesPrice = $request->request->get('salesPrice');
        /* @var $salesItem StockItem */
        $salesItem = $em->getRepository('TallyBundle:StockItem')->find($salesItemId);
        $pItem = $salesItem->getPurchaseItem();
        $purchaseItem = $em->getRepository('TallyBundle:PurchaseItem')->returnPurchaseItemDetails($pItem->getConfig()->getId(), $pItem->getBarcode());
        $checkQuantity = $this->getDoctrine()->getRepository('TallyBundle:StockItem')->checkSalesQuantity($pItem->getId());
        if(!empty($purchaseItem) and $purchaseItem['remainingQuantity'] > 0 and  $purchaseItem['remainingQuantity'] >= $checkQuantity) {
            $salesItem->setQuantity("-{$quantity}");
            $salesItem->setSalesQuantity($quantity);
            $salesItem->setSalesPrice($salesPrice);
            $salesItem->setSubTotal($quantity * $salesPrice);
            $em->persist($salesItem);
            $em->flush();
            $this->getDoctrine()->getRepository('TallyBundle:StockItem')->updateSalesItemPrice($salesItem);
            $msg = '<div class="alert alert-success"><strong>Success!</strong> Product added successfully.</div>';
        } else {
            $msg = '<div class="alert"><strong>Warning!</strong> There is no product in our inventory.</div>';
        }
        $sales = $this->getDoctrine()->getRepository('TallyBundle:Sales')->updateSalesTotalPrice($salesItem->getSales());
        $data = $this->returnResultData($sales,$msg);
        return new Response(json_encode($data));

    }


    public function returnResultData(Sales $entity,$msg ='' ){

        $salesItems = $this->getDoctrine()->getRepository('TallyBundle:StockItem')->getSalesItems($entity);
        $subTotal               = $entity->getSubTotal() > 0 ? $entity->getSubTotal() : 0;
        $total                  = $entity->getTotal() > 0 ? $entity->getTotal() : 0;
        $netTotal               = $entity->getNetTotal() > 0 ? $entity->getNetTotal() : 0;
        $payment                = $entity->getPayment() > 0 ? $entity->getPayment() : 0;
        $vat                    = $entity->getValueAddedTax() > 0 ? $entity->getValueAddedTax() : 0;
        $tti                    = $entity->getTotalTaxIncidence() > 0 ? $entity->getTotalTaxIncidence() : 0;
        $discount               = $entity->getDiscount() > 0 ? $entity->getDiscount() : 0;
        $type    = $entity->getDiscountType();
        if($type == "Percentage"){
            $discountCalculation    = $entity->getDiscountCalculation() > 0 ? $entity->getDiscountCalculation()."%" : 0;
        }else{
            $discountCalculation    = $entity->getDiscountCalculation() > 0 ? $entity->getDiscountCalculation() : 0;
        }
        $data = array(
            'msg' => $msg,
            'subTotal' => $subTotal,
            'total' => $total,
            'netTotal' => $netTotal,
            'payment' => $payment ,
            'due' => ($netTotal-$payment),
            'discount' => $discount,
            'discountCalculation' => $discountCalculation,
            'vat' => $vat,
            'tti' => $tti,
            'salesItems' => $salesItems ,
            'success' => 'success'
        );

        return $data;

    }

    /**
     * @Secure(roles="ROLE_TALLY_ISSUE,ROLE_DOMAIN")
     */

	public function salesDiscountUpdateAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$discountType = $request->request->get('discountType');
		$discountCal = (float)$request->request->get('discount');
		$sales = $request->request->get('sales');
		$sales = $em->getRepository('TallyBundle:Sales')->find($sales);
		$subTotal = $sales->getSubTotal();
		$total      = 0;
        $discount   = 0;
		if($discountType == 'Flat' and $discountCal > 0){
			$total = ($subTotal  - $discountCal);
			$discount = $discountCal;
		}elseif($discountType == 'Percentage' and $discountCal > 0){
			$discount = ($subTotal * $discountCal)/100;
			$total = ($subTotal  - $discount);
		}
		$vat = 0;
		if($total > 0  and $total > $discountCal ){
			$sales->setDiscountType($discountType);
			$sales->setDiscountCalculation($discountCal);
			$sales->setDiscount(round($discount));
			$sales->setNetTotal(round($sales->getTotal() - $sales->getDiscount()));
			$sales->setDue(round($sales->getTotal()));

		}else{

			$sales->setDiscountType('Flat');
			$sales->setDiscountCalculation(0);
			$sales->setDiscount(0);
            $sales->setNetTotal(round($sales->getTotal() - $sales->getDiscount()));
            $sales->setDue(round($sales->getNetTotal()));
		}
		$em->persist($sales);
		$em->flush();
		$data = $this->returnResultData($sales);
		return new Response(json_encode($data));

	}


    /**
     * Deletes a SalesItem entity.
     *
     */
    public function itemDeleteAction(Sales $sales, $salesItem)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('TallyBundle:StockItem')->find($salesItem);
        if (!$salesItem) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }
        $em->remove($entity);
        $em->flush();
        $sales = $this->getDoctrine()->getRepository('TallyBundle:Sales')->updateSalesTotalPrice($sales);
        $data = $this->returnResultData($sales);
        return new Response(json_encode($data));

    }



    public function branchStockItemDetailsAction(Item $item)
    {
        $user = $this->getUser();
        $data = $this->getDoctrine()->getRepository('TallyBundle:Item')->itemDeliveryPurchaseDetails($user,$item);
        return new Response($data);
    }

    public function getBarcode($invoice)
    {
        $barcode = new BarcodeGenerator();
        $barcode->setText($invoice);
        $barcode->setType(BarcodeGenerator::Code128);
        $barcode->setScale(1);
        $barcode->setThickness(34);
        $barcode->setFontSize(8);
        $code = $barcode->generate();
        $data = '';
        $data .= '<img src="data:image/png;base64,' . $code . '" />';
        return $data;
    }

    public function salesInlineUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('TallyBundle:Sales')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseItem entity.');
        }
        $entity->setCourierInvoice($data['value']);
        $em->flush();
        exit;

    }

    public function salesInlineProcessUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('TallyBundle:Sales')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseItem entity.');
        }
        if ($data['value'] == 'Done' or $data['value'] == 'Returned'){
            $entity->setProcess($data['value']);
        }elseif (!empty($entity->getCourierInvoice()) and $data['value'] == 'Courier'){
            $entity->setProcess($data['value']);
        }
        $em->flush();
        if($entity->getProcess() == 'Courier'){
            if(!empty($this->getUser()->getGlobalOption()->getNotificationConfig()) and  !empty($this->getUser()->getGlobalOption()->getSmsSenderTotal())) {
                $dispatcher = $this->container->get('event_dispatcher');
                $dispatcher->dispatch('setting_tool.post.courier_sms', new \Setting\Bundle\ToolBundle\Event\PosOrderSmsEvent($entity));
            }
        }
        if($entity->getProcess() == 'Done' or $entity->getProcess() == 'Delivered' ){
            $this->approvedOrder($entity);
            if(!empty($this->getUser()->getGlobalOption()->getNotificationConfig()) and  !empty($this->getUser()->getGlobalOption()->getSmsSenderTotal())) {
                $dispatcher = $this->container->get('event_dispatcher');
                $dispatcher->dispatch('setting_tool.post.process_sms', new \Setting\Bundle\ToolBundle\Event\PosOrderSmsEvent($entity));
            }
        }elseif($entity->getProcess() == 'Returned'){
         //   $this->returnCancelOrder($entity);
            if(!empty($this->getUser()->getGlobalOption()->getNotificationConfig()) and  !empty($this->getUser()->getGlobalOption()->getSmsSenderTotal())) {

                $dispatcher = $this->container->get('event_dispatcher');
                $dispatcher->dispatch('setting_tool.post.process_sms', new \Setting\Bundle\ToolBundle\Event\PosOrderSmsEvent($entity));
            }
        }
        exit;

    }

    public function salesInvoiceSearchAction()
    {
        $item = $_REQUEST['q'];
        if ($item) {
            $inventory = $this->getUser()->getGlobalOption()->getConfig();
            $item = $this->getDoctrine()->getRepository('TallyBundle:Sales')->searchAutoComplete($item,$inventory);
        }
        return new JsonResponse($item);
    }

    public function searchSalesInvoiceNameAction($invoice)
    {
        return new JsonResponse(array(
            'id'        => $invoice,
            'text'      => $invoice
        ));
    }

    public function locationSearchAction(Request $request)
    {
        $item = $_REQUEST['q'];
        if ($item) {
            $item = $this->getDoctrine()->getRepository('SettingLocationBundle:Location')->searchAutoComplete($item);
        }
        return new JsonResponse($item);
    }

    public function salesSelectAction()
    {
        $items  = array();
        $items[]= array('value' => 'In-progress','text'=>'In-progress');
        $items[]= array('value' => 'Courier','text'=>'Courier');
        $items[]= array('value' => 'Delivered','text'=>'Delivered');
        $items[]= array('value' => 'Done','text'=>'Done');
        $items[]= array('value' => 'Returned','text'=>'Returned');
        $items[]= array('value' => 'Canceled','text'=>'Canceled');
        return new JsonResponse($items);
    }

    public function invoicePrintAction($invoice)
    {
        $em = $this->getDoctrine()->getManager();
        $inventory = $this->getUser()->getGlobalOption()->getConfig();
        $entity = $em->getRepository('TallyBundle:Sales')->findOneBy(array('inventoryConfig' => $inventory, 'invoice' => $invoice));
        $barcode = $this->getBarcode($entity->getInvoice());
        $totalAmount = ( $entity->getTotal() + $entity->getDeliveryCharge());
        $inWard = $this->get('settong.toolManageRepo')->intToWords($totalAmount);
        if($inventory->isCustomPrint() == 1){
            $print = $this->getUser()->getGlobalOption()->getSlug();
        }else{
            $print = 'invoice';
        }
        return $this->render('TallyBundle:SalesPrint:'.$print.'.html.twig', array(
            'entity'      => $entity,
            'inventory'   => $inventory,
            'barcode'     => $barcode,
            'inWard'      => $inWard,
        ));
    }

    public function chalanPrintAction($invoice)
    {

        $config = $this->getUser()->getGlobalOption()->getConfig();
        $entity = $this->getDoctrine()->getRepository('TallyBundle:Sales')->findOneBy(array('inventoryConfig' => $config,'invoice' => $invoice));
        $barcode = $this->getBarcode($entity->getInvoice());
        $totalAmount = ( $entity->getTotal() + $entity->getDeliveryCharge());
        $inWard = $this->get('settong.toolManageRepo')->intToWords($totalAmount);

        return $this->render('TallyBundle:SalesPrint:chalan.html.twig', array(
            'entity'      => $entity,
            'barcode'     => $barcode,
            'inWard'     => $inWard,
        ));
    }

    public function printAction($code)
    {

        $connector = new \Mike42\Escpos\PrintConnectors\DummyPrintConnector();
        $printer = new Printer($connector);
        $printer -> initialize();


        $inventory = $this->getUser()->getGlobalOption()->getConfig();
        $entity = $this->getDoctrine()->getRepository('TallyBundle:Sales')->findOneBy(array('inventoryConfig' => $inventory, 'invoice' => $code));
        $option = $entity->getConfig()->getGlobalOption();
        $this->approvedOrder($entity);

        /** ===================Company Information=================================== */
        if(!empty($entity->getBranches())){

            $branch = $entity->getBranches();
            $branchName     = $branch->getName();
            $address1       = $branch->getAddress();
            $thana          = !empty($branch->getLocation()) ? ', '.$branch->getLocation()->getName():'';
            $district       = !empty($branch->getLocation()) ? ', '.$branch->getLocation()->getParent()->getName():'';
            $address = $address1.$thana.$district;

        }else{

            $address1       = $option->getContactPage()->getAddress1();
            $thana          = !empty($option->getContactPage()->getLocation()) ? ', '.$option->getContactPage()->getLocation()->getName():'';
            $district       = !empty($option->getContactPage()->getLocation()) ? ', '.$option->getContactPage()->getLocation()->getParent()->getName():'';
            $address = $address1.$thana.$district;

        }

        $vatRegNo       = $inventory->getVatRegNo();
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

        /* Information for the receipt */

        $transaction    = new PosItemManager('Payment Mode: '.$transaction,'','');
        $subTotal       = new PosItemManager('Sub Total: ','Tk.',number_format($subTotal));
        $vat            = new PosItemManager('Add Vat: ','Tk.',number_format($vat));
        $discount       = new PosItemManager('Discount: ','Tk.',number_format($discount));
        $grandTotal     = new PosItemManager('Net Payable: ','Tk.',number_format($total));
        $payment        = new PosItemManager('Received: ','Tk.',number_format($payment));
        $due            = new PosItemManager('Due: ','Tk.',number_format($due));


        /* Date is kept the same for testing */
        $date = date('l jS \of F Y h:i:s A');

        /* Name of shop */
        /* Name of shop */
        $printer -> setUnderline(Printer::UNDERLINE_NONE);
        $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> text($companyName."\n");
        $printer -> selectPrintMode();
        if(!empty($entity->getBranches())) {
            $printer->text($branchName . "\n");
        }else{
            $printer -> text($address."\n");
        }

        $printer -> feed();

        /* Title of receipt */
        if(!empty($vatRegNo)){
            $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer -> setEmphasis(false);
            $printer -> selectPrintMode();
            $printer -> text("Vat Reg No. ".$vatRegNo.".\n");
            $printer -> setEmphasis(false);
        }

        /* Title of receipt */
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> setEmphasis(true);
        $printer -> text("SALES INVOICE\n\n");
        $printer -> setEmphasis(false);

        $printer -> selectPrintMode();
        $printer -> setJustification(Printer::JUSTIFY_LEFT);
        $printer -> setEmphasis(true);
        $printer -> setUnderline(Printer::UNDERLINE_DOUBLE);
        $printer -> text(new PosItemManager('Product', 'Qnt', 'Amount'));
        $printer -> setEmphasis(false);
        $printer -> setUnderline(Printer::UNDERLINE_NONE);;
        $printer -> setEmphasis(false);
        $printer -> feed();
        $i=1;
        foreach ( $entity->getSalesItems() as $row){

            $printer -> setUnderline(Printer::UNDERLINE_NONE);
            $printer -> text( new PosItemManager($i.'. '.$row->getItem()->getName(),"",""));
            $printer -> setUnderline(Printer::UNDERLINE_SINGLE);
            $printer -> text(new PosItemManager($row->getPurchaseItem()->getBarcode(),$row->getQuantity(),number_format($row->getSubTotal())));
            $i++;
        }
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
        $printer->setEmphasis(false);
        $printer->text($transaction);
        $printer->selectPrintMode();


        /* Barcode Print */
        $printer->selectPrintMode ( Printer::MODE_DOUBLE_HEIGHT | Printer::MODE_DOUBLE_WIDTH );
        $printer->text ( "\n" );
        $printer->selectPrintMode ();
        $printer->setBarcodeHeight (60);
        $hri = array (Printer::BARCODE_TEXT_BELOW => "");
        $printer -> feed();
        foreach ( $hri as $position => $caption){
            $printer->selectPrintMode ();
            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer->text ($caption);
            $printer->setBarcodeTextPosition ( $position );
            $printer->barcode ($invoice , Printer::BARCODE_JAN13 );
            $printer->feed ();
        }
        /* Footer */

        $printer -> feed();
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> text("Sales By: ".$salesBy."\n");
        $printer -> text("Thank you for shopping\n");
        if($website){
            $printer -> text("Please visit www.".$website."\n");
        }
        $printer -> text($date . "\n");
        $response =  base64_encode($connector->getData());
        $printer -> close();
        return new Response($response);

    }

    public function posPrint(Sales $entity){



        $connector = new \Mike42\Escpos\PrintConnectors\DummyPrintConnector();
        $printer = new Printer($connector);
        $printer -> initialize();


        $inventory = $this->getUser()->getGlobalOption()->getConfig();
        $option = $entity->getConfig()->getGlobalOption();

        /** ===================Company Information=================================== */
        if(!empty($entity->getBranches())){

            $branch = $entity->getBranches();
            $branchName     = $branch->getName();
            $address1       = $branch->getAddress();
            $thana          = !empty($branch->getLocation()) ? ', '.$branch->getLocation()->getName():'';
            $district       = !empty($branch->getLocation()) ? ', '.$branch->getLocation()->getParent()->getName():'';
            $address = $address1.$thana.$district;

        }else{

            $address1       = $option->getContactPage()->getAddress1();
            $thana          = !empty($option->getContactPage()->getLocation()) ? ', '.$option->getContactPage()->getLocation()->getName():'';
            $district       = !empty($option->getContactPage()->getLocation()) ? ', '.$option->getContactPage()->getLocation()->getParent()->getName():'';
            $address = $address1.$thana.$district;

        }

        $vatRegNo       = $inventory->getVatRegNo();
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
        $salesBy            = $entity->getSalesBy()->getProfile()->getName();;


        /** ===================Invoice Sales Item Information========================= */

        $i = 1;
        $items = array();
        foreach ( $entity->getSalesItems() as $row){
            $items[]  = new PosItemManager($i.'. '.$row->getItem()->getName() ,$row->getQuantity(),$row->getSubTotal());
        }

        /* Date is kept the same for testing */
        $date = date('l jS \of F Y h:i:s A');

        /* Name of shop */
        $printer -> setUnderline(Printer::UNDERLINE_NONE);
        $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> text($companyName."\n");
        $printer -> selectPrintMode();
        if(!empty($entity->getBranches())) {
            $printer->text($branchName . "\n");
        }else{
            $printer -> text($address."\n");
        }
        /* $printer -> text($mobile."\n");*/
        $printer -> feed();

        /* Title of receipt */
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> setEmphasis(true);
        if(!empty($vatRegNo)){
            $printer -> text("Vat Reg No. ".$vatRegNo.".\n");
            $printer -> setEmphasis(false);
        }
        $printer -> feed();
        /*
        if(!empty($mobile)){
            $printer -> text ( "----------------------------------" );
            $printer -> text("Mobile: ".$mobile. "\n");
            $printer -> setEmphasis(false);
        }
        */

        /* Information for the receipt */

        $transaction    = new PosItemManager('Payment Mode: '.$transaction,'','');
        $subTotal       = new PosItemManager('Sub Total: ','Tk.',number_format($subTotal));
        $vat            = new PosItemManager('Add Vat: ','Tk.',number_format($vat));
        $discount       = new PosItemManager('Discount: ','Tk.',number_format($discount));
        $grandTotal     = new PosItemManager('Net Payable: ','Tk.',number_format($total));
        $payment        = new PosItemManager('Received: ','Tk.',number_format($payment));
        $due            = new PosItemManager('Due: ','Tk.',number_format($due));

        /* Title of receipt */
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> setEmphasis(true);
        $printer -> text("SALES INVOICE\n\n");
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
        foreach ( $entity->getSalesItems() as $row){

            $printer -> setUnderline(Printer::UNDERLINE_NONE);
            $printer -> text( new PosItemManager($i.'. '.$row->getItem()->getName(),"",""));
            $printer -> setUnderline(Printer::UNDERLINE_SINGLE);
            $printer -> text(new PosItemManager($row->getPurchaseItem()->getBarcode(),$row->getQuantity(),number_format($row->getSubTotal())));
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
        $printer->selectPrintMode ( Printer::MODE_DOUBLE_HEIGHT | Printer::MODE_DOUBLE_WIDTH );
        $printer->text ( "\n" );
        $printer->selectPrintMode ();
        $printer->setBarcodeHeight (60);
        $hri = array (Printer::BARCODE_TEXT_BELOW => "");
        $printer -> feed();
        foreach ( $hri as $position => $caption){
            $printer->selectPrintMode ();
            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer->text ($caption);
            $printer->setBarcodeTextPosition ( $position );
            $printer->barcode ($invoice , Printer::BARCODE_JAN13 );
            $printer->feed ();
        }
        /* Footer */

        $printer -> feed();
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> text("Sales By: ".$salesBy."\n");
        $printer -> text("Thank you for shopping\n");
        if($website){
            $printer -> text("Please visit www.".$website."\n");
        }
        $printer -> text($date . "\n");
        $response =  base64_encode($connector->getData());
        $printer -> close();
        return $response;

    }

}

