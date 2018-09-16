<?php

namespace Appstore\Bundle\InventoryBundle\Controller;

use Appstore\Bundle\InventoryBundle\Entity\Item;
use Appstore\Bundle\InventoryBundle\Form\InventoryItemType;
use Appstore\Bundle\InventoryBundle\Form\SalesGeneralType;
use Appstore\Bundle\InventoryBundle\Form\SalesItemType;
use Appstore\Bundle\InventoryBundle\Form\SalesManualType;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Appstore\Bundle\InventoryBundle\Entity\SalesItem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Appstore\Bundle\InventoryBundle\Entity\Sales;
use Appstore\Bundle\InventoryBundle\Form\SalesType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sales controller.
 *
 */
class SalesManualController extends Controller
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

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $entities = $em->getRepository('InventoryBundle:Sales')->salesLists($this->getUser(), $mode ='manual',$data);
        $pagination = $this->paginate($entities);
        $transactionMethods = $em->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status' => 1), array('name' => 'ASC'));
        return $this->render('InventoryBundle:SalesManual:index.html.twig', array(
            'entities' => $pagination,
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
        return $this->render('InventoryBundle:SalesManual:customer.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
        ));
    }


    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function newAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $entity = new Sales();
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $globalOption = $this->getUser()->getGlobalOption();
        $customer = $em->getRepository('DomainUserBundle:Customer')->defaultCustomer($globalOption);
        $entity->setCustomer($customer);
        $entity->setMobile($customer->getMobile());
        $transactionMethod = $em->getRepository('SettingToolBundle:TransactionMethod')->find(1);
        $entity->setTransactionMethod($transactionMethod);
        $entity->setSalesMode('Manual');
        $entity->setPaymentStatus('Pending');
        $entity->setInventoryConfig($inventory);
        $entity->setSalesBy($this->getUser());
        if(!empty($this->getUser()->getProfile()->getBranches())){
            $entity->setBranches($this->getUser()->getProfile()->getBranches());
        }
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('inventory_salesmanual_edit', array('code' => $entity->getInvoice())));

    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function addItemAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $entity = new Sales();
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $globalOption = $this->getUser()->getGlobalOption();
        $customer = $em->getRepository('DomainUserBundle:Customer')->defaultCustomer($globalOption);
        $entity->setCustomer($customer);
        $transactionMethod = $em->getRepository('SettingToolBundle:TransactionMethod')->find(1);
        $entity->setTransactionMethod($transactionMethod);
        $entity->setSalesMode('Manual');
        $entity->setPaymentStatus('Pending');
        $entity->setInventoryConfig($inventory);
        $entity->setSalesBy($this->getUser());
        if(!empty($this->getUser()->getProfile()->getBranches())){
            $entity->setBranches($this->getUser()->getProfile()->getBranches());
        }
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('inventory_salesmanual_edit', array('code' => $entity->getInvoice())));

    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function editAction($code)
    {

        $em = $this->getDoctrine()->getManager();
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entity = $em->getRepository('InventoryBundle:Sales')->findOneBy(array('inventoryConfig' => $inventory, 'invoice' => $code));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sales entity.');
        }
        $createItemForm = $this->createItemForm(New SalesItem(),$entity);
        $editForm = $this->createEditForm($entity);
        return $this->render('InventoryBundle:SalesManual:sales.html.twig', array(
            'entities' => '',
            'entity' => $entity,
            'form' => $editForm->createView(),
            'itemForm' => $createItemForm->createView(),
        ));

    }

    public function searchAction(Request $request)
    {
        $product = $request->request->get('item');
        $item = $this->getDoctrine()->getRepository('InventoryBundle:Item')->find($product);
        $unit='';
        if(!empty($item->getMasterItem()->getProductUnit())){
            $unit = $item->getMasterItem()->getProductUnit()->getName();
        }
        return new Response(json_encode(array('itemId'=> $item->getId() ,'price'=> $item->getSalesPrice(),'purchasePrice'=> $item->getAvgPurchasePrice(), 'quantity'=> $item->getRemainingQuantity(),'unit'=>$unit)));
    }

    private function createItemForm(SalesItem $item , Sales $entity)
    {
        $form = $this->createForm(new SalesItemType($entity->getInventoryConfig()), $item, array(
            'action' => $this->generateUrl('inventory_salesmanual_insert_item', array('sales' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
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
        $entities = $em->getRepository('InventoryBundle:SalesItem')->salesItems($inventory, $data);
        $pagination = $this->paginate($entities);
        return $this->render('InventoryBundle:Sales:salesItem.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
        ));
    }


    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function insertManualSalesItemAction(Request $request, Sales $sales)
    {
        $em = $this->getDoctrine()->getManager();
        $item = $request->request->get('item');
        $quantity = $request->request->get('quantity');
        $salesPrice = $request->request->get('salesPrice');
        $purchasePrice = $request->request->get('purchasePrice');
        $data = array('quantity' => $quantity ,'salesPrice' => $salesPrice,'purchasePrice' => $purchasePrice);
        $item = $em->getRepository('InventoryBundle:Item')->find($item);
        $remainingQuantity = $item->getRemainingQuantity();
        $checkQuantity = $this->getDoctrine()->getRepository('InventoryBundle:SalesItem')->checkSalesItemQuantity($item);
        $salesItemStock =  $checkQuantity + $quantity;
        if (!empty($item) && $remainingQuantity >= $salesItemStock) {
            $this->getDoctrine()->getRepository('InventoryBundle:SalesItem')->insertSalesManualItems($sales, $item ,$data);
            $this->getDoctrine()->getRepository('InventoryBundle:Sales')->updateSalesTotalPrice($sales);
            $this->get('session')->getFlashBag()->add('success', 'Product added successfully.');
        }
        $result = $this->returnResultData($sales);
        return new Response(json_encode($result));
        exit;
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function searchBarcodeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $salesId = $request->request->get('sales');
	    $barcode = trim($request->request->get('barcode'));
	    $sales = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->find($salesId);
	    $config = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $stock = $this->getDoctrine()->getRepository('InventoryBundle:Item')->findOneBy(array('inventoryConfig' => $config,'barcode' => $barcode));
        $data = array('quantity' => 1 ,'salesPrice' => $stock->getSalesPrice(),'purchasePrice' => $stock->getAvgPurchasePrice());
        $remainingQuantity = $stock->getRemainingQuantity();
        $checkQuantity = $this->getDoctrine()->getRepository('InventoryBundle:SalesItem')->checkSalesItemQuantity($stock);
	    $salesItemStock =  $checkQuantity + 1;
        if (!empty($stock) && $remainingQuantity >= $salesItemStock and $stock->getSalesPrice() > 0 ) {
            $this->getDoctrine()->getRepository('InventoryBundle:SalesItem')->insertSalesManualItems($sales, $stock ,$data);
            $this->getDoctrine()->getRepository('InventoryBundle:Sales')->updateSalesTotalPrice($sales);
            $this->get('session')->getFlashBag()->add('success', 'Product added successfully.');
        }
        $result = $this->returnResultData($sales);
        return new Response(json_encode($result));
        exit;
    }

    public function salesItemUpdateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
	    $salesItem = $this->getDoctrine()->getRepository('InventoryBundle:SalesItem')->find($data['itemId']);
	    $status = $this->getDoctrine()->getRepository('InventoryBundle:SalesItem')->manualSalesItemUpdate($salesItem,$data);
		if($status == 'valid'){
	        $this->get('session')->getFlashBag()->add('success', "Data has been added successfully");
        }else{
			$this->get( 'session' )->getFlashBag()->add('error', " There is no product in our inventory");
		}
	    $this->getDoctrine()->getRepository('InventoryBundle:Sales')->updateSalesTotalPrice($salesItem->getSales());
	    $result = $this->returnResultData($salesItem->getSales());
	    return new Response(json_encode($result));
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
	    $sales = $request->request->get('sales');
	    $sales = $em->getRepository('InventoryBundle:Sales')->find($sales);
	    $subTotal = $sales->getSubTotal();
	    $total = 0;
	    if($discountType == 'Flat' and $discountCal > 0){
		    $total = ($subTotal  - $discountCal);
		    $discount = $discountCal;
	    }elseif($discountType == 'Percentage' and $discountCal > 0){
		    $discount = ($subTotal * $discountCal)/100;
		    $total = ($subTotal  - $discount);
	    }
	    $vat = 0;
	    if($total > 0 ){

		    if ($sales->getInventoryConfig()->getVatEnable() == 1 && $sales->getInventoryConfig()->getVatPercentage() > 0) {
			    $vat = $em->getRepository('InventoryBundle:Sales')->getCulculationVat($sales,$total);
			    $sales->setVat($vat);
		    }
		    $sales->setDiscountType($discountType);
		    $sales->setDiscountCalculation($discountCal);
		    $sales->setDiscount(round($discount));
		    $sales->setTotal(round($total + $vat));
		    $sales->setDue(round($sales->getTotal()));
		    $em->persist($sales);
		    $em->flush();
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
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig()->getId();
        if ($inventory == $entity->getInventoryConfig()->getId()) {
            return $this->render('InventoryBundle:SalesManual:show.html.twig', array(
                'entity' => $entity,
            ));
        } else {
            return $this->redirect($this->generateUrl('inventory_salesmanual'));
        }

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
        $form = $this->createForm(new SalesGeneralType($globalOption,$location), $entity, array(
            'action' => $this->generateUrl('inventory_salesmanual_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'id' => 'salesForm',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    public function returnResultData(Sales $entity){

        $salesItems = $this->getDoctrine()->getRepository('InventoryBundle:SalesItem')->getManualSalesItems($entity);
        $subTotal = $entity->getSubTotal() > 0 ? $entity->getSubTotal() : 0;
        $netTotal = $entity->getTotal() > 0 ? $entity->getTotal() : 0;
        $payment = $entity->getPayment() > 0 ? $entity->getPayment() : 0;
        $vat = $entity->getVat() > 0 ? $entity->getVat() : 0;
        $due = $entity->getDue() > 0 ? $entity->getDue() : 0;
        $discount = $entity->getDiscount() > 0 ? $entity->getDiscount() : 0;
        $data = array(
            'subTotal' => $subTotal,
            'netTotal' => $netTotal,
            'vat' => $vat,
            'due' => $due,
            'discount' => $discount,
            'payment' => $payment ,
            'salesItems' => $salesItems,
            'success' => 'success'
        );
        return $data;

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

            $entity->setTotal($entity->getSubTotal() - $entity->getDiscount());
            if ($entity->getInventoryConfig()->getVatEnable() == 1 && $entity->getInventoryConfig()->getVatPercentage() > 0) {
                $vat = $em->getRepository('InventoryBundle:Sales')->getCulculationVat($entity,$entity->getTotal());
                $entity->setVat($vat);
            }
            $entity->setTotal($entity->getTotal() + $entity->getVat());
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
            if($entity->getProcess() =='Done'){
                $entity->setApprovedBy($this->getUser());
            }
            $em->flush();

            /*
            if (in_array('CustomerSales', $entity->getInventoryConfig()->getDeliveryProcess())) {
                if(!empty($this->getUser()->getGlobalOption()->getNotificationConfig()) and  !empty($this->getUser()->getGlobalOption()->getSmsSenderTotal())) {
                    $dispatcher = $this->container->get('event_dispatcher');
                    $dispatcher->dispatch('setting_tool.post.posorder_sms', new \Setting\Bundle\ToolBundle\Event\PosOrderSmsEvent($entity));
                }
            }*/

            if($entity->getProcess() =='Done' or $entity->getProcess() == 'Delivered'){
                $em->getRepository('InventoryBundle:StockItem')->insertSalesManualStockItem($entity);
                $em->getRepository('InventoryBundle:Item')->getItemSalesUpdate($entity);
                $accountSales = $em->getRepository('AccountingBundle:AccountSales')->insertAccountSales($entity);
                $em->getRepository('AccountingBundle:Transaction')->salesTransaction($entity, $accountSales);
                return $this->redirect($this->generateUrl('inventory_salesmanual_show', array('id' => $entity->getId())));
            }elseif($entity->getProcess() =='Created' or $entity->getProcess() =='In-progress' ){
                return $this->redirect($this->generateUrl('inventory_salesmanual_edit', array('code' => $entity->getInvoice())));
            }elseif($entity->getProcess() =='Courier' or $entity->getProcess() =='Returned'or $entity->getProcess() =='Cancel' ){
                return $this->redirect($this->generateUrl('inventory_salesmanual'));
            }
        }
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities = $em->getRepository('InventoryBundle:Item')->findWithSearch($inventory,$data);
        $pagination = $this->paginate($entities);
        $items = array();
        foreach ($pagination as $value){
            $items[] = $value->getId();
        }
        $ongoingItem = $em->getRepository('InventoryBundle:SalesItem')->ongoingSalesManuelQuantity($inventory,$items);
        $createItemForm = $this->createItemForm(New SalesItem(),$entity);
        return $this->render('InventoryBundle:SalesManual:sales.html.twig', array(
            'entities' => $pagination,
            'entity' => $entity,
            'ongoingItem' => $ongoingItem,
            'searchForm' => $data,
            'form' => $editForm->createView(),
            'itemForm' => $createItemForm->createView(),
        ));

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
            $em->getRepository('InventoryBundle:StockItem')->insertSalesStockItem($entity);
            $em->getRepository('InventoryBundle:Item')->getItemSalesUpdate($entity);
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

    public function itemDeleteAction(Sales $sales, $salesItem)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('InventoryBundle:SalesItem')->find($salesItem);
        if (!$salesItem) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }

        $em->remove($entity);
        $em->flush();
        $sales = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->updateSalesTotalPrice($sales);
        $result = $this->returnResultData($sales);
        return new Response(json_encode($result));
        exit;
    }

    public function itemPurchaseDetailsAction(Request $request)
    {
        $item = $request->request->get('item');
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $customer = isset($_REQUEST['customer']) ? $_REQUEST['customer'] : '';
        $data = $this->getDoctrine()->getRepository('InventoryBundle:Item')->itemPurchaseDetails($inventory, $item, $customer);
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
        $entities = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->findBy(array('inventoryConfig' => $inventory, 'paymentStatus' => 'Pending'));
        $em = $this->getDoctrine()->getManager();
        foreach ($entities as $entity) {
            $em->remove($entity);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('inventory_salesmanual'));
    }

    public function salesInlineUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('InventoryBundle:Sales')->find($data['pk']);
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
        $entity = $em->getRepository('InventoryBundle:Sales')->find($data['pk']);
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
        if($entity->getProcess() == 'Done' or $entity->getProcess() == 'Delivered'){
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
            $entity->setApprovedBy($this->getUser());
            $em->flush();
            $em->getRepository('InventoryBundle:StockItem')->insertSalesManualStockItem($entity);
            $em->getRepository('InventoryBundle:Item')->getItemSalesUpdate($entity);
        //  $em->getRepository('InventoryBundle:GoodsItem')->updateEcommerceItem($entity);
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
        $items[]= array('value' => 'In-progress','text'=>'In-progress');
        $items[]= array('value' => 'Courier','text'=>'Courier');
        $items[]= array('value' => 'Delivered','text'=>'Delivered');
        $items[]= array('value' => 'Done','text'=>'Done');
        $items[]= array('value' => 'Returned','text'=>'Returned');
        $items[]= array('value' => 'Canceled','text'=>'Canceled');
        return new JsonResponse($items);
    }

    public function invoicePrintAction(Sales $entity)
    {
        $barcode = $this->getBarcode($entity->getInvoice());
        $this->get('settong.toolManageRepo')->intToWords($entity->getTotal());
        return $this->render('InventoryBundle:SalesManual:invoice.html.twig', array(
            'entity'      => $entity,
            'barcode'     => $barcode,
        ));
    }

}
