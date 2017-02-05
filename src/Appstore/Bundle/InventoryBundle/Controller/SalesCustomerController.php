<?php

namespace Appstore\Bundle\InventoryBundle\Controller;

use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
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
class SalesCustomerController extends Controller
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
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities = $em->getRepository('InventoryBundle:Sales')->salesLists($inventory, $data);
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


        if (!empty($purchaseItem) && $itemStock > $checkQuantity) {

            $this->getDoctrine()->getRepository('InventoryBundle:SalesItem')->insertSalesItems($sales, $purchaseItem);
            $sales = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->updateSalesTotalPrice($sales);
            $salesItems = $em->getRepository('InventoryBundle:SalesItem')->getSalesItems($sales);
            $msg = 'Product added successfully';

        } else {

            $sales = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->updateSalesTotalPrice($sales);
            $salesItems = $em->getRepository('InventoryBundle:SalesItem')->getSalesItems($sales);
            $msg = 'There is no product in our inventory';
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
        $checkQuantity = $this->getDoctrine()->getRepository('InventoryBundle:SalesItem')->checkSalesQuantity($salesItem->getPurchaseItem());
        $itemStock = $salesItem->getPurchaseItem()->getItemStock();


        if (!empty($salesItem) && $itemStock > $checkQuantity || !empty($salesItem) && $itemStock == $checkQuantity && $checkQuantity > $quantity ) {

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
            return new Response(json_encode(array('salesSubTotal' => $salesSubTotal,'salesTotal' => $salesTotal,'salesVat' => $vat, 'msg' => 'Product added successfully' , 'success' => 'success')));

        } else {

            $sales = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->updateSalesTotalPrice($salesItem->getSales());
            $salesTotal = $sales->getTotal() > 0 ? $sales->getTotal() : 0;
            $salesSubTotal = $sales->getSubTotal() > 0 ? $sales->getSubTotal() : 0;
            $vat = $sales->getVat() > 0 ? $sales->getVat() : 0;
            return new Response(json_encode(array('salesSubTotal' => $salesSubTotal,'salesTotal' => $salesTotal,'salesVat' => $vat, 'msg' => 'There is no product in our inventory' , 'success' => 'success')));
        }

        exit;
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function newAction()
    {
        $customer = isset($_REQUEST['customer']) ? $_REQUEST['customer'] : '';
        $em = $this->getDoctrine()->getManager();
        $entity = new Sales();
        $globalOption = $this->getUser()->getGlobalOption();

        $customerEntity = $em->getRepository('DomainUserBundle:Customer')->find($customer);
        if (!empty($customer) && !empty($customerEntity)) {
            $entity->setCustomer($customer);
        }else{
            $customer = $em->getRepository('DomainUserBundle:Customer')->defaultCustomer($globalOption);
            $entity->setCustomer($customer);
        }

        $transactionMethod = $em->getRepository('SettingToolBundle:TransactionMethod')->find(1);
        $entity->setTransactionMethod($transactionMethod);
        $entity->setSalesMode('general');
        $entity->setPaymentStatus('Pending');
        $entity->setInventoryConfig($globalOption->getInventoryConfig());
        $entity->setSalesBy($this->getUser());
        if(!empty($this->getUser()->getProfile()->getBranches())){
            $entity->setBranches($this->getUser()->getProfile()->getBranches());
        }
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('inventory_salescustomer_edit', array('code' => $entity->getInvoice())));

    }

    /**
     * Finds and displays a Sales entity.
     *
     */
    public function showAction(Sales $entity)
    {
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig()->getId();
        if ($inventory == $entity->getInventoryConfig()->getId()) {
            return $this->render('InventoryBundle:SalesGeneral:show.html.twig', array(
                'entity' => $entity,
            ));
        } else {
            return $this->redirect($this->generateUrl('inventory_salescustomer'));
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
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $todaySales = $em->getRepository('InventoryBundle:Sales')->todaySales($inventory);
        $todaySalesOverview = $em->getRepository('InventoryBundle:Sales')->todaySalesOverview($inventory);

        if ($entity->getProcess() != "In-progress") {
            return $this->redirect($this->generateUrl('inventory_salescustomer_show', array('id' => $entity->getId())));
        }
        return $this->render('InventoryBundle:SalesGeneral:sales.html.twig', array(
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
        $form = $this->createForm(new SalesType($globalOption), $entity, array(
            'action' => $this->generateUrl('inventory_salescustomer_update', array('id' => $entity->getId())),
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

            $entity->setSubTotal($data['paymentSubTotal']);
            if ($entity->getInventoryConfig()->getVatEnable() == 1 && $entity->getInventoryConfig()->getVatPercentage() > 0) {
                $vat = $em->getRepository('InventoryBundle:Sales')->getCulculationVat($entity,$data['paymentTotal']);
                $entity->setVat($vat);
            }
            $entity->setDue($data['dueAmount']);
            $entity->setDiscount($data['discount']);
            $entity->setTotal($data['paymentTotal']);
            $entity->setPayment($data['paymentTotal'] - $data['dueAmount']);
            $amountInWords = $this->get('settong.toolManageRepo')->intToWords($entity->getPayment());
            $entity->setPaymentInWord($amountInWords);

            if ($data['paymentTotal'] <= $data['paymentAmount']) {
                $entity->setPaymentStatus('Paid');
            } else if ($data['paymentTotal'] > $data['paymentAmount']) {
                $entity->setPaymentStatus('Due');
            }
            if (empty($data['sales']['salesBy'])) {
                $entity->setSalesBy($this->getUser());
            }
            if ($entity->getTransactionMethod()->getId() != 4) {
                $entity->setApprovedBy($this->getUser());
            } else if ($entity->getTransactionMethod()->getId() == 4) {
                $amountInWords = $this->get('settong.toolManageRepo')->intToWords($entity->getTotal());
                $entity->setPaymentInWord($amountInWords);
            }
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
        $todaySales = $em->getRepository('InventoryBundle:Sales')->todaySales($inventory);
        $todaySalesOverview = $em->getRepository('InventoryBundle:Sales')->todaySalesOverview($inventory);
        return $this->render('InventoryBundle:SalesGeneral:sales.html.twig', array(
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
        return $this->redirect($this->generateUrl('inventory_salescustomer'));
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
            $entity->setPayment($entity->getPayment() + $entity->getDue());
            $entity->setDue($entity->getTotal() - $entity->getPayment());
            $entity->setApprovedBy($this->getUser());
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
        return $this->render('InventoryBundle:SalesGeneral:invoice.html.twig', array(
            'entity'      => $entity,
            'barcode'     => $barcode,
        ));
    }

}
