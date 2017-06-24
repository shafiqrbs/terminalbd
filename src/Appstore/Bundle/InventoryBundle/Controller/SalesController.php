<?php

namespace Appstore\Bundle\InventoryBundle\Controller;

use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Appstore\Bundle\InventoryBundle\Entity\Item;
use Appstore\Bundle\InventoryBundle\Service\PosItemManager;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use Frontend\FrontentBundle\Service\MobileDetect;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Appstore\Bundle\InventoryBundle\Entity\SalesItem;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Appstore\Bundle\InventoryBundle\Entity\Sales;
use Appstore\Bundle\InventoryBundle\Form\SalesType;
use Symfony\Component\HttpFoundation\Response;
use Hackzilla\BarcodeBundle\Utility\Barcode;
/**
 * Sales controller.
 *
 */
class SalesController extends Controller
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

        $user = $this->getUser();
        $entities = $em->getRepository('InventoryBundle:Sales')->salesLists( $user , $mode = 'pos', $data);
        $pagination = $this->paginate($entities);
        $transactionMethods = $em->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status' => 1), array('name' => 'ASC'));
        return $this->render('InventoryBundle:Sales:index.html.twig', array(
            'entities' => $pagination,
            'transactionMethods' => $transactionMethods,
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
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $globalOption = $this->getUser()->getGlobalOption();
        $customer = $em->getRepository('DomainUserBundle:Customer')->defaultCustomer($globalOption);
        $entity->setCustomer($customer);
        $transactionMethod = $em->getRepository('SettingToolBundle:TransactionMethod')->find(1);
        $entity->setTransactionMethod($transactionMethod);
        $entity->setSalesMode('pos');
        $entity->setPaymentStatus('Pending');
        $entity->setInventoryConfig($inventory);
        $entity->setSalesBy($this->getUser());
        if(!empty($this->getUser()->getProfile()->getBranches())){
            $entity->setBranches($this->getUser()->getProfile()->getBranches());
        }
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('inventory_sales_edit', array('code' => $entity->getInvoice())));

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

    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $sales = $request->request->get('sales');
        $barcode = $request->request->get('barcode');
        $sales = $em->getRepository('InventoryBundle:Sales')->find($sales);
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $purchaseItem = $em->getRepository('InventoryBundle:PurchaseItem')->returnPurchaseItemDetails($inventory, $barcode);
        $checkQuantity = $this->getDoctrine()->getRepository('InventoryBundle:SalesItem')->checkSalesQuantity($purchaseItem);
        $itemStock = $purchaseItem->getItemStock();

        $branch = $this->getUser()->getProfile()->getBranches();
        $branchStockItem = $this->getDoctrine()->getRepository('InventoryBundle:DeliveryItem')->checkItem($this->getUser(),$purchaseItem);

        /* Device Detection code desktop or mobile */
        $detect = new MobileDetect();
        $device = '';
        if( $detect->isMobile() || $detect->isTablet() ) {
            $device = 'mobile' ;
        }
        if (!empty($branch) and $branchStockItem == 'invalid' ) {

            $sales = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->updateSalesTotalPrice($sales);
            $salesItems = $em->getRepository('InventoryBundle:SalesItem')->getSalesItems($sales,$device);
            $msg = '<div class="alert"><strong>Warning!</strong> There is no product in '.$branch->getName().' inventory.</div>';

        }elseif(!empty($purchaseItem) and $itemStock >= $checkQuantity) {

            $this->getDoctrine()->getRepository('InventoryBundle:SalesItem')->insertSalesItems($sales, $purchaseItem);
            $sales = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->updateSalesTotalPrice($sales);
            $salesItems = $em->getRepository('InventoryBundle:SalesItem')->getSalesItems($sales,$device);
            $msg = '<div class="alert alert-success"><strong>Success!</strong> Product added successfully.</div>';

        } else {

            $sales = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->updateSalesTotalPrice($sales);
            $salesItems = $em->getRepository('InventoryBundle:SalesItem')->getSalesItems($sales,$device);
            $msg = '<div class="alert"><strong>Warning!</strong> There is no product in our inventory.</div>';
        }

        $salesTotal = $sales->getTotal() > 0 ? $sales->getTotal() : 0;
        $salesSubTotal = $sales->getSubTotal() > 0 ? $sales->getSubTotal() : 0;
        $vat = $sales->getVat() > 0 ? $sales->getVat() : 0;
        return new Response(json_encode(array('salesSubTotal' => $salesSubTotal,'salesTotal' => $salesTotal,'purchaseItem' => $purchaseItem, 'salesItem' => $salesItems,'salesVat' => $vat, 'msg' => $msg , 'success' => 'success')));
        exit;
    }


    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function salesDiscountUpdateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $discount = $request->request->get('discount');
        $sales = $request->request->get('sales');

        $sales = $em->getRepository('InventoryBundle:Sales')->find($sales);
        $total = ($sales->getSubTotal() - $discount);
        $vat = 0;
        if($total > $discount ){
            if ($sales->getInventoryConfig()->getVatEnable() == 1 && $sales->getInventoryConfig()->getVatPercentage() > 0) {
                $vat = $em->getRepository('InventoryBundle:Sales')->getCulculationVat($sales,$total);
                $sales->setVat($vat);
            }
            $sales->setDiscount($discount);
            $sales->setTotal($total+$vat);
            $sales->setDue($total+$vat);
            $em->persist($sales);
            $em->flush();
        }


        $salesTotal = $sales->getTotal() > 0 ? $sales->getTotal() : 0;
        $salesSubTotal = $sales->getSubTotal() > 0 ? $sales->getSubTotal() : 0;
        $vat = $sales->getVat() > 0 ? $sales->getVat() : 0;
        return new Response(json_encode(array('salesSubTotal' => $salesSubTotal,'salesTotal' => $salesTotal,'salesVat' => $vat, 'msg' => 'Discount updated successfully' , 'success' => 'success')));
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

        $salesItem = $em->getRepository('InventoryBundle:SalesItem')->find($salesItemId);
        $checkOngoingSalesQuantity = $this->getDoctrine()->getRepository('InventoryBundle:SalesItem')->checkSalesQuantity($salesItem->getPurchaseItem());
        $itemStock = $salesItem->getPurchaseItem()->getItemStock();
        $currentRemainingQnt = ($itemStock + $salesItem->getQuantity()) - ($checkOngoingSalesQuantity + $quantity) ;

        if(!empty($salesItem) && $itemStock > 0 && $currentRemainingQnt >= 0 ){

            $salesItem->setQuantity($quantity);
            $salesItem->setSalesPrice($salesPrice);
            if (!empty($customPrice)) {
                $salesItem->setCustomPrice($customPrice);
            }
            $salesItem->setSubTotal($quantity * $salesPrice);
            $em->persist($salesItem);
            $em->flush();

            $sales = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->updateSalesTotalPrice($salesItem->getSales());
            $salesTotal = $sales->getTotal() > 0 ? $sales->getTotal() : 0;
            $salesSubTotal = $sales->getSubTotal() > 0 ? $sales->getSubTotal() : 0;
            $vat = $sales->getVat() > 0 ? $sales->getVat() : 0;
            $msg = '<div class="alert alert-success"><strong>Success!</strong> Product added successfully.</div>';

            return new Response(json_encode(array('salesSubTotal' => $salesSubTotal,'salesTotal' => $salesTotal,'salesVat' => $vat, 'msg' => $msg , 'success' => 'success')));

        } else {

            $sales = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->updateSalesTotalPrice($salesItem->getSales());
            $salesTotal = $sales->getTotal() > 0 ? $sales->getTotal() : 0;
            $salesSubTotal = $sales->getSubTotal() > 0 ? $sales->getSubTotal() : 0;
            $vat = $sales->getVat() > 0 ? $sales->getVat() : 0;
            $msg = '<div class="alert"><strong>Warning!</strong> There is no product in our inventory.</div>';

            return new Response(json_encode(array('salesSubTotal' => $salesSubTotal,'salesTotal' => $salesTotal,'salesVat' => $vat, 'msg' => $msg , 'success' => 'success')));
        }

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
            return $this->render('InventoryBundle:Sales:show.html.twig', array(
                'entity' => $entity,
            ));
        } else {
            return $this->redirect($this->generateUrl('inventory_sales'));
        }

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

        $editForm = $this->createEditForm($entity);
        $todaySales = $em->getRepository('InventoryBundle:Sales')->todaySales($this->getUser(),$mode = 'pos');
        $todaySalesOverview = $em->getRepository('InventoryBundle:Sales')->todaySalesOverview($this->getUser(),$mode = 'pos');

        if ($entity->getProcess() != "In-progress") {
            return $this->redirect($this->generateUrl('inventory_sales_show', array('id' => $entity->getId())));
        }
        return $this->render('InventoryBundle:Sales:pos.html.twig', array(
            'entity' => $entity,
            'todaySales' => $todaySales,
            'todaySalesOverview' => $todaySalesOverview,
            'form' => $editForm->createView(),
        ));
    }

    public function resetAction(Sales $sales)
    {
        $em = $this->getDoctrine()->getManager();
        foreach ($sales->getSalesItems() as $salesItem ) {
            $em->remove($salesItem);
        }
        $em->flush();
        $this->getDoctrine()->getRepository('InventoryBundle:Sales')->updateSalesTotalPrice($sales);
        return $this->redirect($this->generateUrl('inventory_sales_edit', array('code' => $sales->getInvoice())));

    }

    public function salesInlineMobileUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('InventoryBundle:Sales')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseItem entity.');
        }

        $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['value']);
        $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findExistingCustomer($entity, $mobile);
        $entity->setCustomer($customer);
        $entity->setMobile($mobile);
        $em->flush();
        exit;

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
        $form = $this->createForm(new SalesType($globalOption), $entity, array(
            'action' => $this->generateUrl('inventory_sales_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'id' => 'posForm',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
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
        if ($editForm->isValid() and $data['paymentTotal'] > 0 ) {

            if (!empty($data['sales']['mobile'])) {

                $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['sales']['mobile']);
                $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findExistingCustomer($entity, $mobile);
                $entity->setCustomer($customer);
                $entity->setMobile($mobile);
            } else {
                $globalOption = $this->getUser()->getGlobalOption();
                $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $globalOption, 'name' => 'Default'));
                $entity->setCustomer($customer);
            }

            if ($entity->getInventoryConfig()->getVatEnable() == 1 && $entity->getInventoryConfig()->getVatPercentage() > 0) {
                $vat = $em->getRepository('InventoryBundle:Sales')->getCulculationVat($entity,$data['paymentTotal']);
                $entity->setVat($vat);
            }
            $entity->setDue($data['dueAmount']);
            $entity->setDiscount($data['discount']);
            $entity->setTotal($data['paymentTotal']);
            $entity->setPayment($data['paymentTotal'] - $data['dueAmount']);

            if ($data['paymentTotal'] <= $data['paymentAmount']) {
                $entity->setPaymentStatus('Paid');
            } else if ($data['paymentTotal'] > $data['paymentAmount']) {
                $entity->setPaymentStatus('Due');
            }
            $entity->setProcess('Paid');
            if (empty($data['sales']['salesBy'])) {
                $entity->setSalesBy($this->getUser());
            }
            if ($entity->getTransactionMethod()->getId() != 4) {
                $entity->setApprovedBy($this->getUser());
            }
            $amountInWords = $this->get('settong.toolManageRepo')->intToWords($entity->getTotal());
            $entity->setPaymentInWord($amountInWords);

            $em->flush();

            if (in_array('CustomerSales', $entity->getInventoryConfig()->getDeliveryProcess())) {

                if(!empty($this->getUser()->getGlobalOption()->getNotificationConfig()) and  !empty($this->getUser()->getGlobalOption()->getSmsSenderTotal())) {
                    $dispatcher = $this->container->get('event_dispatcher');
                    $dispatcher->dispatch('setting_tool.post.posorder_sms', new \Setting\Bundle\ToolBundle\Event\PosOrderSmsEvent($entity));
                }
            }
            if ($entity->getTransactionMethod()->getId() == 4) {
                return $this->redirect($this->generateUrl('inventory_sales_show', array('id' => $entity->getId())));
            } else {

                $em->getRepository('InventoryBundle:Item')->getItemSalesUpdate($entity);
                $em->getRepository('InventoryBundle:StockItem')->insertSalesStockItem($entity);
                $em->getRepository('InventoryBundle:GoodsItem')->updateEcommerceItem($entity);
                $accountSales = $em->getRepository('AccountingBundle:AccountSales')->insertAccountSales($entity);
                $em->getRepository('AccountingBundle:Transaction')->salesTransaction($entity, $accountSales);
                return $this->redirect($this->generateUrl('inventory_sales_new'));
            }
        }

        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $todaySales = $em->getRepository('InventoryBundle:Sales')->todaySales($inventory,$mode='pos');
        $todaySalesOverview = $em->getRepository('InventoryBundle:Sales')->todaySalesOverview($inventory , $mode='pos');
        return $this->render('InventoryBundle:Sales:pos.html.twig', array(
            'entity' => $entity,
            'todaySales' => $todaySales,
            'todaySalesOverview' => $todaySalesOverview,
            'form' => $editForm->createView(),
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
            $em->getRepository('InventoryBundle:Item')->getItemSalesUpdate($entity);
            $em->getRepository('InventoryBundle:StockItem')->insertSalesStockItem($entity);
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
        $entity = $em->getRepository('InventoryBundle:SalesItem')->find($salesItem);
        if (!$salesItem) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }

        $em->remove($entity);
        $em->flush();
        $sales = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->updateSalesTotalPrice($sales);
        $salesTotal = $sales->getTotal() > 0 ? $sales->getTotal() : 0;
        $salesSubTotal = $sales->getSubTotal() > 0 ? $sales->getSubTotal() : 0;
        $vat = $sales->getVat() > 0 ? $sales->getVat() : 0;
        return new Response(json_encode(array('salesSubTotal' => $salesSubTotal,'salesTotal' => $salesTotal,'salesVat' => $vat, 'success' => 'success')));
        exit;

    }

    public function itemPurchaseDetailsAction(Request $request)
    {
        $securityContext = $this->container->get('security.authorization_checker');
        $item = $request->request->get('item');
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $customer = isset($_REQUEST['customer']) ? $_REQUEST['customer'] : '';

        /* Device Detection code desktop or mobile */
        $detect = new MobileDetect();
        $device = '';
        if( $detect->isMobile() || $detect->isTablet() ) {
            $device = 'mobile' ;
        }
        $data = $this->getDoctrine()->getRepository('InventoryBundle:Item')->itemPurchaseDetails($securityContext,$inventory, $item, $customer, $device);
        return new Response($data);
    }

    public function branchStockItemDetailsAction(Item $item)
    {
        $user = $this->getUser();
        $data = $this->getDoctrine()->getRepository('InventoryBundle:Item')->itemDeliveryPurchaseDetails($user,$item);
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
        return $this->redirect($this->generateUrl('inventory_sales'));
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
        if ($data['value'] == 'Paid' or $data['value'] == 'Returned'){
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
        if($entity->getProcess() == 'Paid'){
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
            $entity->setProcess('Paid');
            $entity->setPayment($entity->getTotal());
            $entity->setDue(0);
            $entity->setApprovedBy($this->getUser());
            $amountInWords = $this->get('settong.toolManageRepo')->intToWords($entity->getTotal());
            $entity->setPaymentInWord($amountInWords);
            $em->flush();
            $em->getRepository('InventoryBundle:Item')->getItemSalesUpdate($entity);
            $em->getRepository('InventoryBundle:StockItem')->insertSalesStockItem($entity);
            $em->getRepository('InventoryBundle:GoodsItem')->updateEcommerceItem($entity);
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

    public function salesInvoiceSearchAction()
    {
        $item = $_REQUEST['q'];
        if ($item) {
            $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
            $item = $this->getDoctrine()->getRepository('InventoryBundle:Vendor')->searchAutoComplete($item,$inventory);
        }
        return new JsonResponse($item);
        exit;
    }

    public function searchSalesInvoiceNameAction($invoice)
    {
        return new JsonResponse(array(
            'id'        => $invoice,
            'text'      => $invoice
        ));
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

    public function invoicePrintAction(Sales $entity)
    {


        $barcode = $this->getBarcode($entity->getInvoice());
        $totalAmount = ( $entity->getTotal() + $entity->getDeliveryCharge());
        $inWard = $this->get('settong.toolManageRepo')->intToWords($totalAmount);

        return $this->render('InventoryBundle:Sales:invoice.html.twig', array(
            'entity'      => $entity,
            'barcode'     => $barcode,
            'inWard'     => $inWard,
        ));
    }

    /**
     * @param $code
     * @return Response
     */
    public function printAction($code)
    {

        $connector = new \Mike42\Escpos\PrintConnectors\DummyPrintConnector();
        $printer = new Printer($connector);
        $printer -> initialize();


        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entity = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->findOneBy(array('inventoryConfig' => $inventory, 'invoice' => $code));
        $option = $entity->getInventoryConfig()->getGlobalOption();
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


        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $option = $entity->getInventoryConfig()->getGlobalOption();

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

