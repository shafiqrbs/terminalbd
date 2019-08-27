<?php

namespace Appstore\Bundle\TallyBundle\Controller;

use Appstore\Bundle\DomainUserBundle\Entity\Branches;
use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use Frontend\FrontentBundle\Service\MobileDetect;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Appstore\Bundle\TallyBundle\Entity\SalesItem;
use Mike42\Escpos\Printer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Appstore\Bundle\TallyBundle\Entity\Sales;
use Symfony\Component\HttpFoundation\Response;
use Hackzilla\BarcodeBundle\Utility\Barcode;
/**
 * Sales controller.
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

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();
        $inventoryConfig = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $data = $_REQUEST;
        $entities = $em->getRepository('TallyBundle:Sales')->salesLists( $this->getUser() , $mode='general-sales', $data);
        $pagination = $this->paginate($entities);
        $transactionMethods = $em->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status' => 1), array('name' => 'ASC'));
        return $this->render('TallyBundle:Invoice:index.html.twig', array(
            'entities' => $pagination,
            'inventoryConfig' => $inventoryConfig,
            'transactionMethods' => $transactionMethods,
            'searchForm' => $data,
        ));

    }


    public function customerAction()
    {
        $em = $this->getDoctrine()->getManager();

        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('DomainUserBundle:Customer')->findWithSearch($globalOption,$data);
        $pagination = $this->paginate($entities);
        return $this->render('TallyBundle:SalesOnline:customer.html.twig', array(
            'entities' => $pagination,
            'inventory' => $globalOption->getInventoryConfig(),
            'searchForm' => $data,
        ));
    }


    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function newAction()
    {

        $em = $this->getDoctrine()->getManager();
        $entity = new Sales();
        $globalOption = $this->getUser()->getGlobalOption();
        $customer = $em->getRepository('DomainUserBundle:Customer')->defaultCustomer($globalOption);
        $entity->setCustomer($customer);
        $transactionMethod = $em->getRepository('SettingToolBundle:TransactionMethod')->find(1);
        $entity->setTransactionMethod($transactionMethod);
        $entity->setSalesMode('general-sales');
        $entity->setPaymentStatus('Pending');
        $entity->setInventoryConfig($globalOption->getInventoryConfig());
        $entity->setSalesBy($this->getUser());
        if(!empty($this->getUser()->getProfile()->getBranches())){
            $entity->setBranches($this->getUser()->getProfile()->getBranches());
        }
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('inventory_salesonline_edit', array('code' => $entity->getInvoice())));

    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function editAction($code)
    {
        $em = $this->getDoctrine()->getManager();
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entity = $em->getRepository('TallyBundle:Sales')->findOneBy(array('inventoryConfig' => $inventory, 'invoice' => $code));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sales entity.');
        }

        $editForm = $this->createEditForm($entity);
        $todaySales = $em->getRepository('TallyBundle:Sales')->todaySales($this->getUser(),$mode = 'general-sales');
        $todaySalesOverview = $em->getRepository('TallyBundle:Sales')->todaySalesOverview($this->getUser(),$mode = 'general-sales');
        if(!in_array($entity->getProcess(),array('In-progress','Created'))) {
            return $this->redirect($this->generateUrl('inventory_salesonline_show', array('id' => $entity->getId())));
        }

        /* Device Detection code desktop or mobile */

        $detect = new MobileDetect();

        if( $detect->isMobile() || $detect->isTablet() ) {
            $theme = 'm-sales';
        }else{
            $theme = 'sales';
        }
        return $this->render('TallyBundle:SalesOnline:'.$theme.'.html.twig', array(
            'entity' => $entity,
            'todaySales' => $todaySales,
            'todaySalesOverview' => $todaySalesOverview,
            'form' => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Sales entity.wq
     *
     * @param Sales $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Sales $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $form = $this->createForm(new SalesOnlineType($globalOption,$location), $entity, array(
            'action' => $this->generateUrl('inventory_salesonline_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'id' => 'salesForm',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }


    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function salesItemAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities = $em->getRepository('TallyBundle:SalesItem')->salesItems($inventory, $data);
        $pagination = $this->paginate($entities);
        return $this->render('TallyBundle:Sales:salesItem.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
        ));
    }


    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $sales = $request->request->get('sales');
        $barcode = $request->request->get('barcode');
        $sales = $em->getRepository('TallyBundle:Sales')->find($sales);
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $purchaseItem = $em->getRepository('TallyBundle:PurchaseItem')->returnPurchaseItemDetails($inventory, $barcode);
        $checkQuantity = $this->getDoctrine()->getRepository('TallyBundle:SalesItem')->checkSalesQuantity($purchaseItem);
        $itemStock = $purchaseItem->getItemStock();

        if (!empty($purchaseItem) && $itemStock > $checkQuantity) {

            $this->getDoctrine()->getRepository('TallyBundle:SalesItem')->insertSalesItems($sales, $purchaseItem);
            $sales = $this->getDoctrine()->getRepository('TallyBundle:Sales')->updateSalesTotalPrice($sales);
            $msg = 'Product added successfully';

        } else {

            $sales = $this->getDoctrine()->getRepository('TallyBundle:Sales')->updateSalesTotalPrice($sales);
            $msg = '<div class="alert"><strong>Warning!</strong> There is no product in our inventory.</div>';
        }
        $data = $this->returnResultData($sales,$msg);
        return new Response(json_encode($data));
        exit;
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function salesDiscountUpdateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $discountType = $request->request->get('discountType');
        $discountCal = (float)$request->request->get('discount');
        $salesId = $request->request->get('sales');

        /* @var $sales Sales */

        $sales = $em->getRepository('TallyBundle:Sales')->find($salesId);
        $subTotal = $sales->getSubTotal();
        if($discountType == 'Flat'){
            $total = ($subTotal  - $discountCal);
            $discount = $discountCal;
        }else{
            $discount = ($subTotal * $discountCal)/100;
            $total = ($subTotal  - $discount);
        }

        if($total > 0 ){

            if ($sales->getInventoryConfig()->getVatEnable() == 1 && $sales->getInventoryConfig()->getVatPercentage() > 0) {
                $vat = $em->getRepository('TallyBundle:Sales')->getCulculationVat($sales,$total);
                $sales->setVat($vat);
            }
            $sales->setDiscountType($discountType);
            $sales->setDiscountCalculation($discountCal);
            $sales->setDiscount(round($discount));
            $sales->setTotal(round($total + $vat));
            $sales->setDue(round($total+$vat));
        }else{
            if ($sales->getInventoryConfig()->getVatEnable() == 1 && $sales->getInventoryConfig()->getVatPercentage() > 0) {
                $vat = $em->getRepository('TallyBundle:Sales')->getCulculationVat($sales,$total);
                $sales->setVat($vat);
            }
            $sales->setDiscountType('Flat');
            $sales->setDiscountCalculation(0);
            $sales->setDiscount(0);
            $sales->setTotal(round($subTotal + $vat));
            $sales->setDue(round($sales->getTotal()));
        }
        $em->persist($sales);
        $em->flush();
        $data = $this->returnResultData($sales);
        return new Response(json_encode($data));
        exit;
    }


    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function salesItemUpdateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $salesItemId = $request->request->get('salesItemId');
        $quantity = $request->request->get('quantity');
        $salesPrice = $request->request->get('salesPrice');
        $customPrice = $request->request->get('customPrice');

        $salesItem = $em->getRepository('TallyBundle:SalesItem')->find($salesItemId);

        $checkQuantity = $this->getDoctrine()->getRepository('TallyBundle:SalesItem')->checkSalesQuantity($salesItem->getPurchaseItem());
        $itemStock = $salesItem->getPurchaseItem()->getItemStock();
        $sales = $salesItem->getSales();

        if (!empty($salesItem) && $itemStock > $checkQuantity || !empty($salesItem) && $itemStock == $checkQuantity && $checkQuantity > $quantity ) {

            $salesItem->setQuantity($quantity);
            $salesItem->setSalesPrice($salesPrice);
            if (!empty($customPrice)) {
                $salesItem->setCustomPrice($customPrice);
            }
            $salesItem->setSubTotal($quantity * $salesPrice);
            $em->persist($salesItem);
            $em->flush();
            $sales = $this->getDoctrine()->getRepository('TallyBundle:Sales')->updateSalesTotalPrice($sales);
        }
        $data = $this->returnResultData($sales);
        return new Response(json_encode($data));
        exit;
    }

    /**
     * Finds and displays a Sales entity.
     *
     */
    public function showAction(Sales $entity)
    {
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();

        if ($inventory->getId() == $entity->getInventoryConfig()->getId()) {
            return $this->render('TallyBundle:SalesOnline:show.html.twig', array(
                'entity' => $entity,
                'inventoryConfig' => $inventory,
            ));
        } else {
            return $this->redirect($this->generateUrl('inventory_salesonline'));
        }

    }

    /**
     * Finds and displays a Sales entity.
     *
     */
    public function showPreviewAction(Sales $entity)
    {
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig()->getId();

        if(!empty($this->getUser()->getProfile()->getBranches())){
            $itemBranchStock = $this->getDoctrine()->getRepository('TallyBundle:Delivery')->returnBranchSalesItem($this->getUser(),$entity);
        }else{
            $data = array('stockReceiveItem' => '' , 'stockSalesItem' => '' , 'stockSalesReturnItem' => '', 'stockReturnItem' => ''  );
            $itemBranchStock = $data ;
        }

        if ($inventory == $entity->getInventoryConfig()->getId()) {
            return $this->render('TallyBundle:SalesOnline:show-preview.html.twig', array(
                'entity'                => $entity,
                'itemBranchStocks'      => $itemBranchStock,
            ));
        }
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function updateAction(Request $request, Sales $entity)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sales entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        $data = $request->request->all();
        if ($editForm->isValid()) {

            $globalOption = $this->getUser()->getGlobalOption();
            if (!empty($data['customerMobile'])) {
                $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['customerMobile']);
                $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->newExistingCustomerForSales($globalOption,$mobile,$data);
                $entity->setCustomer($customer);

            } elseif(!empty($data['mobile'])) {

                $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['mobile']);
                $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $globalOption, 'mobile' => $mobile ));
                $entity->setCustomer($customer);

            }
            $entity->setDue($entity->getTotal() - $entity->getPayment());
            $amountInWords = $this->get('settong.toolManageRepo')->intToWords($entity->getPayment());
            $entity->setPaymentInWord($amountInWords);
            if ($entity->getTotal() <= $entity->getPayment()) {
                $entity->setPayment($entity->getTotal());
                $entity->setDue(0);
                $entity->setPaymentStatus('Paid');
            }else{
                $entity->setPaymentStatus('Due');
            }
            $entity->setApprovedBy($this->getUser());
            $entity->setProcess('Done');
            if ($data['process'] == 'In-progress') {
                $entity->setProcess('In-progress');
            }
            if($entity->getProcess() =="Done"){
                $datetime = new \DateTime("now");
                $entity->setCreated($datetime);
            }
            if(empty($entity->getPayment()) AND $entity->getProcess() =="Done"){
                $entity->setTransactionMethod(NULL);
            }
            $purchaseAmount = $this->getDoctrine()->getRepository('TallyBundle:SalesItem')->getItemPurchasePrice($entity);
            $entity->setPurchasePrice($purchaseAmount);
            $profit = ( $entity->getTotal()-($entity->getVat() + $purchaseAmount));
            $entity->setProfit($profit);
            $em->flush();
            if ($data['process'] != 'In-progress'){
                $em->getRepository('TallyBundle:StockItem')->insertSalesStockItem($entity);
                $em->getRepository('TallyBundle:Item')->getItemSalesUpdate($entity);
                $em->getRepository('TallyBundle:GoodsItem')->updateEcommerceItem($entity);
                $accountSales = $em->getRepository('AccountingBundle:AccountSales')->insertAccountSales($entity);
                $em->getRepository('AccountingBundle:Transaction')->salesTransaction($entity, $accountSales);
            }
            if ($data['process'] == 'print') {
                return $this->redirect($this->generateUrl('inventory_salesonline_show', array('id' => $entity->getId())));
            } else {
                return $this->redirect($this->generateUrl('inventory_salesonline_new'));
            }

        }


    }

    public function returnResultData(Sales $entity,$msg=''){

        $device ="";
        $detect = new MobileDetect();
        if( $detect->isMobile() || $detect->isTablet() ) {
            $device = 'mobile';
        }
        $salesItems = $this->getDoctrine()->getRepository('TallyBundle:SalesItem')->getSalesItems($entity,$device);
        $subTotal = $entity->getSubTotal() > 0 ? $entity->getSubTotal() : 0;
        $netTotal = $entity->getTotal() > 0 ? $entity->getTotal() : 0;
        $payment = $entity->getPayment() > 0 ? $entity->getPayment() : 0;
        $due = $entity->getDue();
        $vat = $entity->getVat() > 0 ? $entity->getVat() : 0;
        $discount = $entity->getDiscount() > 0 ? $entity->getDiscount() : 0;
        $data = array(
            'msg' => $msg,
            'salesSubTotal' => $subTotal,
            'salesTotal' => $netTotal,
            'payment' => $payment ,
            'due' => $due,
            'discount' => $discount,
            'vat' => $vat,
            'salesItems' => $salesItems ,
            'success' => 'success'
        );

        return $data;

    }


    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_APPROVE")
     */

    public function approveAction(Sales $entity)
    {
        if (!empty($entity)) {
            $em = $this->getDoctrine()->getManager();
            $entity->setPaymentStatus('Paid');
            $entity->setApprovedBy($this->getUser());
            $em->flush();
            $em->getRepository('TallyBundle:Item')->getItemSalesUpdate($entity);
            $em->getRepository('TallyBundle:StockItem')->insertSalesStockItem($entity);
            $accountSales = $em->getRepository('AccountingBundle:AccountSales')->insertAccountSales($entity);
            $em->getRepository('AccountingBundle:Transaction')->salesTransaction($entity, $accountSales);
            return new Response('success');
        } else {
            return new Response('failed');
        }
        exit;
    }


    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function deleteAction(Sales $sales)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$sales) {
            throw $this->createNotFoundException('Unable to find Sales entity.');
        }
        if (!empty($sales->getSalesImport())) {
            $salesImport = $sales->getSalesImport();
            $em->remove($salesImport);
        }
        $em->remove($sales);
        $em->flush();
        return new Response(json_encode(array('success' => 'success')));
        exit;
    }

    /**
     * Deletes a SalesItem entity.
     *
     */
    public function itemDeleteAction(Sales $sales, $salesItem)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('TallyBundle:SalesItem')->find($salesItem);
        if (!$salesItem) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }

        $em->remove($entity);
        $em->flush();
        $sales = $this->getDoctrine()->getRepository('TallyBundle:Sales')->updateSalesTotalPrice($sales);
        $salesTotal = $sales->getTotal() > 0 ? $sales->getTotal() : 0;
        $salesSubTotal = $sales->getSubTotal() > 0 ? $sales->getSubTotal() : 0;
        $vat = $sales->getVat() > 0 ? $sales->getVat() : 0;
        return new Response(json_encode(array('salesSubTotal' => $salesSubTotal,'salesTotal' => $salesTotal,'salesVat' => $vat, 'success' => 'success')));
        exit;

    }

    public function itemPurchaseDetailsAction(Request $request)
    {
        $item = $request->request->get('item');
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $customer = isset($_REQUEST['customer']) ? $_REQUEST['customer'] : '';
        $data = $this->getDoctrine()->getRepository('TallyBundle:Item')->itemPurchaseDetails($inventory, $item, $customer);
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

    public function deleteEmptyInvoiceAction()
    {
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities = $this->getDoctrine()->getRepository('TallyBundle:Sales')->findBy(array('inventoryConfig' => $inventory, 'paymentStatus' => 'Pending', 'process' => 'Created'));
        $em = $this->getDoctrine()->getManager();
        foreach ($entities as $entity) {
            $em->remove($entity);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('inventory_salesonline'));
    }

    public function salesInlineUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('TallyBundle:Sales')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseItem entity.');
        }
        if(!empty($this->getUser()->getProfile()->getBranches())){
            $entity->setBranches($this->getUser()->getProfile()->getBranches());
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
        $entity->setapprovedBy($this->getUser());
        if(!empty($this->getUser()->getProfile()->getBranches())){
            $entity->setBranches($this->getUser()->getProfile()->getBranches());
        }
        $em->flush();

        if($entity->getProcess() == 'Courier'){
            if(!empty($this->getUser()->getGlobalOption()->getNotificationConfig()) and  !empty($this->getUser()->getGlobalOption()->getSmsSenderTotal())) {
                $dispatcher = $this->container->get('event_dispatcher');
                $dispatcher->dispatch('setting_tool.post.courier_sms', new \Setting\Bundle\ToolBundle\Event\PosOrderSmsEvent($entity));
            }
        }
        if($entity->getProcess() == 'Done'){
            $this->approvedOrder($entity);
            if(!empty($this->getUser()->getGlobalOption()->getNotificationConfig()) and  !empty($this->getUser()->getGlobalOption()->getSmsSenderTotal())) {
                $dispatcher = $this->container->get('event_dispatcher');
                $dispatcher->dispatch('setting_tool.post.process_sms', new \Setting\Bundle\ToolBundle\Event\PosOrderSmsEvent($entity));
            }
        }elseif($entity->getProcess() == 'Returned'){
            $this->returnCancelOrder($entity);
            if(!empty($this->getUser()->getGlobalOption()->getNotificationConfig()) and  !empty($this->getUser()->getGlobalOption()->getSmsSenderTotal())) {

                $dispatcher = $this->container->get('event_dispatcher');
                $dispatcher->dispatch('setting_tool.post.process_sms', new \Setting\Bundle\ToolBundle\Event\PosOrderSmsEvent($entity));
            }
        }

        exit;

    }

    public function approvedOrder(Sales $entity)
    {
        if (!empty($entity)) {

            $em = $this->getDoctrine()->getManager();
            $entity->setPaymentStatus('Paid');
            $entity->setPayment($entity->getPayment() + $entity->getDue());
            $entity->setDue($entity->getTotal() - $entity->getPayment());
            $em->flush();
            $em->getRepository('TallyBundle:StockItem')->insertSalesStockItem($entity);
            $em->getRepository('TallyBundle:Item')->getItemSalesUpdate($entity);
            //  $em->getRepository('TallyBundle:GoodsItem')->updateEcommerceItem($entity);
            $accountSales = $em->getRepository('AccountingBundle:AccountSales')->insertAccountSales($entity);
            $em->getRepository('AccountingBundle:Transaction')->salesTransaction($entity, $accountSales);
            return new Response('success');

        } else {

            return new Response('failed');
        }
        exit;
    }

    public function returnCancelOrder(Sales $entity)
    {
        if (!empty($entity)) {
            $em = $this->getDoctrine()->getManager();
            $entity->setPaymentStatus('Cancel');
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
        $items[]= array('value' => 'Done','text'=>'Done');
        $items[]= array('value' => 'In-progress','text'=>'In-progress');
        $items[]= array('value' => 'Courier','text'=>'Courier');
        $items[]= array('value' => 'Returned','text'=>'Returned');
        return new JsonResponse($items);
    }

    public function invoicePrintAction(Sales $entity)
    {
        $barcode = $this->getBarcode($entity->getInvoice());
        return $this->render('TallyBundle:SalesGeneral:invoice.html.twig', array(
            'entity'      => $entity,
            'barcode'     => $barcode,
        ));
    }

    public function onlinePosPrintAction(Request $request)
    {

        $connector = new \Mike42\Escpos\PrintConnectors\DummyPrintConnector();
        $printer = new Printer($connector);
        $printer -> initialize();

        $data = $request->request->all();
        $salesId = $data['salesId'];
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entity = $this->getDoctrine()->getRepository('TallyBundle:Sales')->findOneBy(array('inventoryConfig' => $inventory, 'id' => $salesId));
        $this->updateOnlineSalesByPosPrint($entity,$data);
        $option = $entity->getInventoryConfig()->getGlobalOption();



        /** ===================Company Information=================================== */
        if(!empty($entity->getBranches())){

            /* @var Branches $branch **/

            $branch = $entity->getBranches();
            $branchName     = $branch->getName();
            $mobile         = $branch->getMobile();
            $address1       = $branch->getAddress();
            $thana          = !empty($branch->getLocation()) ? ', '.$branch->getLocation()->getName():'';
            $district       = !empty($branch->getLocation()) ? ', '.$branch->getLocation()->getParent()->getName():'';
            $address = $address1.$thana.$district;

        }else{

            $address1       = $option->getContactPage()->getAddress1();
            $mobile         = $option->getMobile();
            $thana          = !empty($option->getContactPage()->getLocation()) ? ', '.$option->getContactPage()->getLocation()->getName():'';
            $district       = !empty($option->getContactPage()->getLocation()) ? ', '.$option->getContactPage()->getLocation()->getParent()->getName():'';
            $address = $address1.$thana.$district;

        }

        $vatRegNo       = $inventory->getVatRegNo();
        $companyName    = $option->getName();
        $website        = $option->getDomain();


        /** ===================Customer Information=================================== */

        /* @var Customer $customer **/
        $customer = $entity->getCustomer();

        if( $entity->getSalesMode() == 'online' and !empty($customer) ){

            $name = 'Name: '. $customer->getName();
            $customerMobile = 'Mobile no: '. $customer->getMobile();
            $customerAddress = 'Address: '. $customer->getAddress();
            $thana          = !empty($customer->getLocation()) ? $customer->getLocation()->getName():'';
            $district       = !empty($customer->getLocation()) ? ' ,'. $customer->getLocation()->getParent()->getName():'';
            $customerLocation = $thana.$district;

        }

        /** ===================Transaction  Information=================================== */

        $invoice            = $entity->getInvoice();
        $subTotal           = $entity->getSubTotal();
        $total              = $entity->getTotal();
        $discount           = $entity->getDiscount();
        $vat                = $entity->getVat();
        $deliveryCharge     = $entity->getDeliveryCharge();
        $transaction        = $entity->getTransactionMethod()->getName();
        $salesBy            = $entity->getCreatedBy();

        /* Information for the receipt */

        $transaction    = new PosItemManager('Payment Mode: '.$transaction,'','');
        $subTotal       = new PosItemManager('Sub Total: ','Tk.',number_format($subTotal));
        $vat            = new PosItemManager('Add Vat: ','Tk.',number_format($vat));
        $discount       = new PosItemManager('Discount: ','Tk.',number_format($discount));
        $deliveryCharge = new PosItemManager('Delivery Charge: ','Tk.',number_format($deliveryCharge));

        if( $entity->getSalesMode() == 'online' ) {
            $grandTotal = new PosItemManager('Net Payable: ', 'Tk.', number_format($total + $entity->getDeliveryCharge()));
        }else{
            $grandTotal = new PosItemManager('Net Payable: ', 'Tk.', number_format($total));
        }

        /* Date is kept the same for testing */
        $date = date('l jS \of F Y h:i:s A');

        /* Customer Information */

        $printer -> setUnderline(Printer::UNDERLINE_NONE);
        $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> text($companyName."\n\n");
        $printer -> setUnderline(Printer::UNDERLINE_DOUBLE);
        $printer -> selectPrintMode();
        if(!empty($entity->getBranches())) {
            $printer->text($branchName . "\n");
        }else{
            $printer -> text($address."\n");
        }
        $printer->text($mobile . "\n");
        $printer -> setUnderline(Printer::UNDERLINE_DOUBLE);
        $printer -> setUnderline(Printer::UNDERLINE_NONE);

        /* Title of receipt */
        if(!empty($vatRegNo)){
            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer -> setEmphasis(true);
            $printer -> selectPrintMode();
            $printer->text ( "\n" );
            $printer -> text("Vat Reg No. ".$vatRegNo.".\n");
            $printer -> setEmphasis(false);
            $printer->text ( "\n" );
        }

        if( $entity->getSalesMode() == 'online' and !empty($customer) ){

            /* Customer Information */
            $billTo       = new PosItemManager('Bill To');

            $printer    ->setUnderline(Printer::UNDERLINE_NONE);
            $printer    ->setJustification(Printer::JUSTIFY_LEFT);
            $printer    ->setEmphasis(true);
            $printer    ->setUnderline(Printer::UNDERLINE_DOUBLE);
            $printer    ->text($billTo);
            $printer    ->text("\n");
            $printer    ->setEmphasis(false);
            $printer    ->selectPrintMode();
            $printer    ->setJustification(Printer::JUSTIFY_LEFT);
            $printer    ->text($name . "\n");
            $printer    ->text($customerMobile . "\n");
            $printer    ->text($customerAddress . "\n");
            $printer    ->text($customerLocation . "\n");
            $printer    ->text ( "\n" );
            $printer    ->setEmphasis(false);

        }

        /* Title of receipt */
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
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

        if($entity->getSalesMode() == 'online' and !empty($customer) and !empty($deliveryCharge)){
            $printer -> setUnderline(Printer::UNDERLINE_DOUBLE);
            $printer->text($deliveryCharge);
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

    public function onlinePosPrintIndividualAction(Sales $entity)
    {

        $connector = new \Mike42\Escpos\PrintConnectors\DummyPrintConnector();
        $printer = new Printer($connector);
        $printer -> initialize();

        $inventory = $entity->getInventoryConfig();
        $option = $entity->getInventoryConfig()->getGlobalOption();

        /** ===================Company Information=================================== */
        if(!empty($entity->getBranches())){

            /* @var Branches $branch **/

            $branch = $entity->getBranches();
            $branchName     = $branch->getName();
            $mobile         = $branch->getMobile();
            $address1       = $branch->getAddress();
            $thana          = !empty($branch->getLocation()) ? ', '.$branch->getLocation()->getName():'';
            $district       = !empty($branch->getLocation()) ? ', '.$branch->getLocation()->getParent()->getName():'';
            $address = $address1.$thana.$district;

        }else{

            $address1       = $option->getContactPage()->getAddress1();
            $mobile         = $option->getMobile();
            $thana          = !empty($option->getContactPage()->getLocation()) ? ', '.$option->getContactPage()->getLocation()->getName():'';
            $district       = !empty($option->getContactPage()->getLocation()) ? ', '.$option->getContactPage()->getLocation()->getParent()->getName():'';
            $address = $address1.$thana.$district;

        }

        $vatRegNo       = $inventory->getVatRegNo();
        $companyName    = $option->getName();
        $website        = $option->getDomain();


        /** ===================Customer Information=================================== */

        /* @var Customer $customer **/
        $customer = $entity->getCustomer();

        if( $entity->getSalesMode() == 'online' and !empty($customer) ){

            $name = 'Name: '. $customer->getName();
            $customerMobile = 'Mobile no: '. $customer->getMobile();
            $customerAddress = 'Address: '. $customer->getAddress();
            $thana          = !empty($customer->getLocation()) ? $customer->getLocation()->getName():'';
            $district       = !empty($customer->getLocation()) ? ' ,'. $customer->getLocation()->getParent()->getName():'';
            $customerLocation = $thana.$district;

        }

        /** ===================Transaction  Information=================================== */

        $invoice            = $entity->getInvoice();
        $subTotal           = $entity->getSubTotal();
        $total              = $entity->getTotal();
        $discount           = $entity->getDiscount();
        $vat                = $entity->getVat();
        $deliveryCharge     = $entity->getDeliveryCharge();
        $transaction        = $entity->getTransactionMethod()->getName();
        $salesBy            = $entity->getCreatedBy();

        /* Information for the receipt */

        $transaction    = new PosItemManager('Payment Mode: '.$transaction,'','');
        $subTotal       = new PosItemManager('Sub Total: ','Tk.',number_format($subTotal));
        $vat            = new PosItemManager('Add Vat: ','Tk.',number_format($vat));
        $discount       = new PosItemManager('Discount: ','Tk.',number_format($discount));
        $deliveryCharge = new PosItemManager('Delivery Charge: ','Tk.',number_format($deliveryCharge));

        if( $entity->getSalesMode() == 'online' ) {
            $grandTotal = new PosItemManager('Net Payable: ', 'Tk.', number_format($total + $entity->getDeliveryCharge()));
        }else{
            $grandTotal = new PosItemManager('Net Payable: ', 'Tk.', number_format($total));
        }

        /* Date is kept the same for testing */
        $date = date('l jS \of F Y h:i:s A');

        /* Customer Information */

        $printer -> setUnderline(Printer::UNDERLINE_NONE);
        $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> text($companyName."\n\n");
        $printer -> setUnderline(Printer::UNDERLINE_DOUBLE);
        $printer -> selectPrintMode();
        if(!empty($entity->getBranches())) {
            $printer->text($branchName . "\n");
        }else{
            $printer -> text($address."\n");
        }
        $printer->text($mobile . "\n");
        $printer -> setUnderline(Printer::UNDERLINE_DOUBLE);
        $printer -> setUnderline(Printer::UNDERLINE_NONE);

        /* Title of receipt */
        if(!empty($vatRegNo)){
            $printer -> setJustification(Printer::JUSTIFY_CENTER);
            $printer -> setEmphasis(true);
            $printer -> selectPrintMode();
            $printer->text ( "\n" );
            $printer -> text("Vat Reg No. ".$vatRegNo.".\n");
            $printer -> setEmphasis(false);
            $printer->text ( "\n" );
        }

        if( $entity->getSalesMode() == 'online' and !empty($customer) ){

            /* Customer Information */
            $billTo       = new PosItemManager('Bill To');

            $printer    ->setUnderline(Printer::UNDERLINE_NONE);
            $printer    ->setJustification(Printer::JUSTIFY_LEFT);
            $printer    ->setEmphasis(true);
            $printer    ->setUnderline(Printer::UNDERLINE_DOUBLE);
            $printer    ->text($billTo);
            $printer    ->text("\n");
            $printer    ->setEmphasis(false);
            $printer    ->selectPrintMode();
            $printer    ->setJustification(Printer::JUSTIFY_LEFT);
            $printer    ->text($name . "\n");
            $printer    ->text($customerMobile . "\n");
            $printer    ->text($customerAddress . "\n");
            $printer    ->text($customerLocation . "\n");
            $printer    ->text ( "\n" );
            $printer    ->setEmphasis(false);

        }

        /* Title of receipt */
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
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

        if($entity->getSalesMode() == 'online' and !empty($customer) and !empty($deliveryCharge)){
            $printer -> setUnderline(Printer::UNDERLINE_DOUBLE);
            $printer->text($deliveryCharge);
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

    public function updateOnlineSalesByPosPrint(Sales $entity,$data){


        $em = $this->getDoctrine()->getManager();

        $globalOption = $this->getUser()->getGlobalOption();
        if (!empty($data['sales_general']['customer']['mobile'])) {

            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['sales_general']['customer']['mobile']);
            $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->newExistingCustomer($globalOption,$mobile,$data);
            $entity->setCustomer($customer);

        } elseif(!empty($data['mobile'])) {

            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['mobile']);
            $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $globalOption, 'mobile' => $mobile ));
            $entity->setCustomer($customer);

        } else {

            $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $globalOption, 'name' => 'Default'));
            if(empty($customer)){
                $customer = $em->getRepository('DomainUserBundle:Customer')->defaultCustomer($globalOption);

            }
            $entity->setCustomer($customer);
        }

        if ($entity->getInventoryConfig()->getVatEnable() == 1 && $entity->getInventoryConfig()->getVatPercentage() > 0) {
            $vat = $em->getRepository('TallyBundle:Sales')->getCulculationVat($entity,$data['paymentTotal']);
            $entity->setVat($vat);
        }
        $entity->setDeliveryCharge($data['deliveryCharge']);
        $entity->setDue($data['dueAmount']);
        $entity->setDiscount($data['discount']);
        $entity->setTotal($data['paymentTotal']);
        $entity->setPayment($data['paymentTotal'] - $data['dueAmount']);
        $amountInWords = $this->get('settong.toolManageRepo')->intToWords($entity->getPayment());
        $entity->setPaymentInWord($amountInWords);

        if ($data['paymentTotal'] <= $data['paymentAmount']) {
            $entity->setPayment($entity->getTotal());
            $entity->setDue(0);
            $entity->setPaymentStatus('Paid');
        } else if ($data['paymentTotal'] > $data['paymentAmount']) {
            $entity->setPaymentStatus('Due');
        }
        if (empty($data['sales_general']['salesBy'])) {
            $entity->setSalesBy($this->getUser());
        }
        if ( $data['process'] != 'in-progress') {
            $entity->setApprovedBy($this->getUser());
        }
        $em->flush();

        /*
        if (in_array('OnlineSales', $entity->getInventoryConfig()->getDeliveryProcess())) {

            if(!empty($this->getUser()->getGlobalOption()->getNotificationConfig()) and  !empty($this->getUser()->getGlobalOption()->getSmsSenderTotal())) {
                $dispatcher = $this->container->get('event_dispatcher');
                $dispatcher->dispatch('setting_tool.post.posorder_sms', new \Setting\Bundle\ToolBundle\Event\PosOrderSmsEvent($entity));
            }
        }*/

        if ($data['process'] != 'in-progress' ) {

            $em->getRepository('TallyBundle:StockItem')->insertSalesStockItem($entity);
            $em->getRepository('TallyBundle:Item')->getItemSalesUpdate($entity);
            //   $em->getRepository('TallyBundle:GoodsItem')->updateEcommerceItem($entity);
            $accountSales = $em->getRepository('AccountingBundle:AccountSales')->insertAccountSales($entity);
            $em->getRepository('AccountingBundle:Transaction')->salesTransaction($entity, $accountSales);
            return $this->redirect($this->generateUrl('inventory_salesonline_new'));
        }
    }

    public function reverseAction($invoice)
    {
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entity = $this->getDoctrine()->getRepository('TallyBundle:Sales')->findOneBy(array('inventoryConfig' => $inventory,'invoice' => $invoice));
        $em = $this->getDoctrine()->getManager();
        $em->getRepository('TallyBundle:StockItem')->saleaItemStockReverse($entity);
        $em->getRepository('TallyBundle:Item')->getSalesItemReverse($entity);
        $em->getRepository('TallyBundle:GoodsItem')->ecommerceItemReverse($entity);
        $em->getRepository('AccountingBundle:AccountSales')->accountSalesReverse($entity);
        $em = $this->getDoctrine()->getManager();
        $entity->setRevised(true);
        $entity->setProcess('In-progress');
        $entity->setRevised(true);
        $entity->setTotal($entity->getSubTotal());
        $entity->setPaymentStatus('Due');
        $entity->setDiscount(null);
        $entity->setDue($entity->getSubTotal());
        $entity->setPaymentInWord(null);
        $entity->setPayment(null);
        $entity->setPaymentStatus('Pending');
        $em->flush();
        $template = $this->get('twig')->render('TallyBundle:Reverse:salesReverse.html.twig', array(
            'entity' => $entity,
            'inventoryConfig' => $inventory,
        ));
        $em->getRepository('TallyBundle:Reverse')->insertSales($entity, $template);
        return $this->redirect($this->generateUrl('inventory_salesonline_edit', array('code' => $entity->getInvoice())));
    }

    public function invoiceGroupReverseAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = ['startDate' => '2019-07-04','endateDate' => '2019-07-04','process'=>"Done"];
        $entities = $em->getRepository('TallyBundle:Sales')->salesLists( $this->getUser() , $mode='general-sales', $data);
        $pagination = $entities->getResult();

        /* @var $entity Sales */

        foreach ($pagination as $entity):
           $em->getRepository('TallyBundle:StockItem')->saleaItemStockReverse($entity);
            $em->getRepository('TallyBundle:Item')->getSalesItemReverse($entity);
            $em->getRepository('TallyBundle:GoodsItem')->ecommerceItemReverse($entity);
            $em->getRepository('AccountingBundle:AccountSales')->accountSalesReverse($entity);
            $em = $this->getDoctrine()->getManager();
            $entity->setRevised(true);
            $entity->setProcess("Revised");
            $em->flush();
        endforeach;
        exit;

      }

    public function invoiceGroupApprovedAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = ['startDate' => '2019-07-04','endateDate' => '2019-07-04','process'=>"Revised"];
        $entities = $em->getRepository('TallyBundle:Sales')->salesLists( $this->getUser() , $mode='general-sales', $data);
        $pagination = $entities->getResult();

        /* @var $entity Sales */

        foreach ($pagination as $entity):
            $em->getRepository('TallyBundle:StockItem')->insertSalesStockItem($entity);
            $em->getRepository('TallyBundle:Item')->getItemSalesUpdate($entity);
            $em->getRepository('TallyBundle:GoodsItem')->updateEcommerceItem($entity);
            $accountSales = $em->getRepository('AccountingBundle:AccountSales')->insertAccountSales($entity);
            $em->getRepository('AccountingBundle:Transaction')->salesTransaction($entity, $accountSales);
        endforeach;
        exit;
    }

}