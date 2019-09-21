<?php

namespace Appstore\Bundle\EcommerceBundle\Controller;

use Appstore\Bundle\EcommerceBundle\Entity\OrderPayment;
use Appstore\Bundle\EcommerceBundle\Form\MedicineItemType;
use Appstore\Bundle\EcommerceBundle\Form\OrderPaymentType;
use Appstore\Bundle\MedicineBundle\Entity\MedicineStock;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Knp\Snappy\Pdf;
use Appstore\Bundle\EcommerceBundle\Entity\Order;
use Appstore\Bundle\EcommerceBundle\Entity\OrderItem;
use Appstore\Bundle\EcommerceBundle\Form\OrderType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Order controller.
 *
 */
class OrderController extends Controller
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
     * Lists all Item entities.
     *
     * @Secure(roles = "ROLE_ECOMMERCE,ROLE_DOMAIN")
     */

    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $data = $_REQUEST;
        $entities = $em->getRepository('EcommerceBundle:Order')->findWithSearch($globalOption->getId(),$data);
        $pagination = $this->paginate($entities);
        return $this->render('EcommerceBundle:Order:index.html.twig', array(
            'entities' => $pagination,
        ));
    }

    public function newAction()
    {

        $em = $this->getDoctrine()->getManager();
        $entity = new Order();
        $config = $this->getUser()->getGlobalOption();
        $entity->setEcommerceConfig($config->getEcommerceConfig());
        $entity->setGlobalOption($config);
        $entity->setCreatedBy($this->getUser());
        $customer = $em->getRepository('DomainUserBundle:Customer')->defaultCustomer($this->getUser()->getGlobalOption());
        $entity->setCustomer($customer);
        $entity->setCashOnDelivery(true);
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('customer_order_edit', array('id' => $entity->getId())));

    }

    public function editAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $order = $em->getRepository('EcommerceBundle:Order')->find($id);
        $paymentEntity = new  OrderPayment();
        $orderForm = $this->createEditForm($order);
        $payment = $this->createEditPaymentForm($paymentEntity,$order);

        if( $order->getGlobalOption()->getDomainType() == 'medicine' ) {
            $theme = 'medicine';
        }else{
            $theme = 'ecommerce';
        }
        $salesItemForm = $this->createMedicineSalesItemForm(new OrderItem(),$order);
        return $this->render("EcommerceBundle:Order/{$theme}:new.html.twig", array(
            'globalOption' => $order->getGlobalOption(),
            'entity'                => $order,
            'orderForm'             => $orderForm->createView(),
            'salesItem'             => $salesItemForm->createView(),
            'paymentForm'           => $payment->createView(),
        ));

    }


    /**
     * Finds and displays a Order entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EcommerceBundle:Order')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Order entity.');
        }

        if( $entity->getGlobalOption()->getDomainType() == 'medicine' ) {
            $theme = 'medicine';
        }else{
            $theme = 'ecommerce';
        }
        return $this->render("EcommerceBundle:Order/{$theme}:show.html.twig", array(
            'globalOption' => $entity->getGlobalOption(),
            'entity'      => $entity,
        ));

    }


    /**
    * Creates a form to edit a Order entity.
    *
    * @param Order $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Order $entity)
    {
        $globalOption = $entity->getGlobalOption();
        $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $form = $this->createForm(new OrderType($globalOption,$location), $entity, array(
            'action' => $this->generateUrl('customer_order_confirm', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'id' => 'orderProcess',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Creates a form to edit a PreOrder entity.
     *
     * @param Order $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditPaymentForm(OrderPayment $entity,Order $order)
    {
        $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $form = $this->createForm(new OrderPaymentType($order->getGlobalOption(),$location), $entity, array(
            'action' => $this->generateUrl('customer_order_ajax_payment', array('id' => $order->getId())),
            'method' => 'POST',
            'attr' => array(
                'id' => 'ecommerce-payment',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    public function paymentAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $order = $em->getRepository('EcommerceBundle:Order')->find($id);
        $paymentEntity = new  OrderPayment();
        $orderForm = $this->createEditForm($order);
        $payment = $this->createEditPaymentForm($paymentEntity,$order);

        if( $order->getGlobalOption()->getDomainType() == 'medicine' ) {
            $theme = 'medicine';
        }else{
            $theme = 'ecommerce';
        }
        $salesItemForm = $this->createMedicineSalesItemForm(new OrderItem(),$order);
        return $this->render("EcommerceBundle:Order/{$theme}:payment.html.twig", array(
            'globalOption' => $order->getGlobalOption(),
            'entity'                => $order,
            'orderForm'             => $orderForm->createView(),
            'salesItem'             => $salesItemForm->createView(),
            'paymentForm'           => $payment->createView(),
        ));

    }


    private function createMedicineSalesItemForm(OrderItem $orderItem,Order $order )
    {

        $form = $this->createForm(new MedicineItemType(), $orderItem, array(
            'action' => $this->generateUrl('customer_medicine_order_item',array('id' => $order->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'id' => 'orderItem',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to edit an existing OrderItem entity.
     *
     */
    public function medicineItemAddAction(Order $entity , Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $orderItem = new OrderItem();
        $data = $request->request->all()['orderItem'];
        $stockId = $data['itemName'];
        $stock = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->find($stockId);
        $unit = ($stock->getUnit()) ? $stock->getUnit()->getName() :"";
        $orderItem->setOrder($entity);
        $orderItem->setItemName($stock->getName());
        $orderItem->setBrandName($stock->getBrandName());
        $orderItem->setUnitName($unit);
        $orderItem->setQuantity($data['quantity']);
        $orderItem->setPrice($data['price']);
        $orderItem->setSubTotal($data['price'] * $data['quantity']);
        $em->persist($orderItem);
        $em->flush();
        $em->getRepository('EcommerceBundle:Order')->updateOrder($entity);
        return new Response('success');

    }


    public function stockDetailsAction(MedicineStock $stock)
    {
        $unit = ($stock->getUnit()) ? $stock->getUnit()->getName() : '';
        return new Response(json_encode(array('unit' => $unit , 'price' => $stock->getSalesPrice())));
    }

    public function autoSearchAction(Request $request)
    {
        $item = trim($_REQUEST['q']);
        if ($item) {
            $inventory = $this->getUser()->getGlobalOption()->getMedicineConfig();
            $item = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->ecommerceSearchAutoComplete($item,$inventory);
        }
        return new JsonResponse($item);
    }

    public function inlineOrderUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('EcommerceBundle:Order')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseItem entity.');
        }
        $setName = 'set'.$data['name'];
        $entity->$setName($data['value']);
        $em->persist($entity);
        $em->flush();
        if($entity->getShippingCharge() > 0 ){
            $em->getRepository('EcommerceBundle:Order')->updateOrder($entity);
        }
        exit;

    }

    public function paymentProcessAction(Request $request ,Order $order)
    {
        $payment = $request->request->all();
        $data = $payment['ecommerce_payment'];

        $em = $this->getDoctrine()->getManager();
        if($data['transactionType'] and $order->getGrandTotalAmount() > 0){
            $entity = new OrderPayment();
            $entity->setOrder($order);
            if (!empty($data['customerMobile']) or !empty($data['mobile']) ) {
                $this->updateOrderInformation($order, $data);
            }
            if($data['transactionType'] == 'Return'){
                $entity->setTransactionType('Return');
                $entity->setAmount('-'.$data['amount']);
            }elseif($data['transactionType'] == 'Receive'){
                $entity->setTransactionType('Receive');
                $entity->setAmount($data['amount']);
            }
            if(!empty($data['accountMobileBank'])){
                $accountMobileBank =$this->getDoctrine()->getRepository('AccountingBundle:AccountMobileBank')->find($data['accountMobileBank']);
                $entity->setAccountMobileBank($accountMobileBank);
            }
            $entity->setMobileAccount($data['mobileAccount']);
            $entity->setTransaction($data['transaction']);
            $em->persist($entity);
            $em->flush();
        }
        $cashDelivery = isset($payment['cashOnDelivery']) and $payment['cashOnDelivery'] == 1 ? $payment['cashOnDelivery'] : 0;
        if($cashDelivery == 1 ) {
            $order->setCashOnDelivery(true);
        }else {
            $order->setCashOnDelivery(false);
        }
        $em->persist($order);
        $em->flush();
        $this->getDoctrine()->getRepository('EcommerceBundle:Order')->updateOrderPayment($order);
        return $this->redirect($this->generateUrl('customer_order_payment',array('id' => $order->getId())));
    }

    public function updateOrderInformation(Order $order,$data)
    {
        $em = $this->getDoctrine()->getManager();
        if (!empty($data['customerMobile'])) {
            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['customerMobile']);
            $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->newExistingCustomerForSales($order->getGlobalOption(),$mobile,$data);
        } elseif(!empty($data['mobile'])) {
            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['mobile']);
            $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $order->getGlobalOption(), 'mobile' => $mobile ));
        }
        $order->setCustomer($customer);
        $order->setCustomerName($customer->getName());
        $order->setCustomerMobile($customer->getMobile());
        $order->setAddress($customer->getAddress());
        $em->persist($order);
        $em->flush();
    }



    /**
     * Displays a form to edit an existing OrderItem entity.
     *
     */
    public function inlineUpdateAction(Request $request,OrderItem $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $entity->setQuantity($data['value']);
        $entity->setSubTotal($entity->getPrice() *  floatval($data['value']));
        $em->flush();
        $em->getRepository('EcommerceBundle:Order')->updateOrder($entity->getOrder());
        exit;
    }

    /**
     * Displays a form to edit an existing OrderItem entity.
     *
     */
    public function inlineItemAddAction(Request $request,OrderItem $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $purchaseItem = $em->getRepository('InventoryBundle:PurchaseItem')->findOneBy(array('barcode' => $data['value']));
        if(!empty($purchaseItem)){
        $entity->setPurchaseItem($purchaseItem);
        }
        $em->flush();
        exit;
    }



    /**
     * Displays a form to edit an existing OrderItem entity.
     *
     */
    public function inlineDisableAction(Request $request,OrderItem $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if($entity->getStatus() == 1){
            $entity->setStatus(false);
        }else{
            $entity->setStatus(true);
        }
        $em->flush();
        $em->getRepository('EcommerceBundle:Order')->updateOrder($entity->getOrder());
        return new Response('success');
        exit;
    }

    /**
     * Displays a form to edit an existing Order entity.
     *
     */
    public function shippingChargeAction(Request $request,Order $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $entity->setShippingCharge($data['value']);
        $em->flush();
        $em->getRepository('EcommerceBundle:Order')->updateOrder($entity);
        exit;
    }

    public function confirmAction(Request $request ,Order $order)
    {
        $em = $this->getDoctrine()->getManager();
        $process = $order->getProcess();
        $paymentEntity = new  OrderPayment();
        $orderForm = $this->createEditForm($order);
        $payment = $this->createEditPaymentForm($paymentEntity,$order);
        $orderForm->handleRequest($request);
        if ($orderForm->isValid()) {
            $em->persist($order);
            if($order->getProcess() == 'sms'){
                $order->setProcess($process);
            }
            $em->flush();
            if($order->getProcess() == 'confirm'){

                $em->getRepository('EcommerceBundle:OrderItem')->updateOrderItem($order);
                $em->getRepository('EcommerceBundle:Order')->updateOrder($order);
                $em->getRepository('EcommerceBundle:OrderPayment')->updateOrderPayment($order);
                $em->getRepository('EcommerceBundle:Order')->updateOrderPayment($order);

                //$em->getRepository('InventoryBundle:Item')->onlineOrderUpdate($order);
                //$em->getRepository('InventoryBundle:StockItem')->insertOnlineOrder($order);
                //$online = $em->getRepository('AccountingBundle:AccountOnlineOrder')->insertAccountOnlineOrder($order);
                //$em->getRepository('AccountingBundle:Transaction')->onlineOrderTransaction($order,$online);

                $this->get('session')->getFlashBag()->add('success',"Customer has been confirmed");
                $dispatcher = $this->container->get('event_dispatcher');
                $dispatcher->dispatch('setting_tool.post.order_confirm_sms', new \Setting\Bundle\ToolBundle\Event\EcommerceOrderSmsEvent($order));

            }else{

                $this->get('session')->getFlashBag()->add('success',"Message has been sent successfully");
                $dispatcher = $this->container->get('event_dispatcher');
                $dispatcher->dispatch('setting_tool.post.order_comment_sms', new \Setting\Bundle\ToolBundle\Event\EcommerceOrderSmsEvent($order));

            }
            return $this->redirect($this->generateUrl('customer_order_payment',array('id' => $order->getId())));
        }
        return $this->render('EcommerceBundle:Order:payment.html.twig', array(
            'entity'                => $order,
            'orderForm'             => $orderForm->createView(),
            'paymentForm'           => $payment->createView(),
        ));


    }

    public function orderProcessSourceAction()
    {

        $items[]=array('value' => 'created','text'=> 'Created');
        $items[]=array('value' => 'wfc','text'=> 'Waiting for Confirm');
        $items[]=array('value' => 'confirm','text'=> 'Confirm');
        $items[]=array('value' => 'delivered','text'=> 'Delivered');
        $items[]=array('value' => 'returned','text'=> 'Returned');
        $items[]=array('value' => 'cancel','text'=> 'Cancel');
        $items[]=array('value' => 'delete','text'=> 'Delete');
        return new JsonResponse($items);

    }

    public function inlineProcessUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $order = $em->getRepository('EcommerceBundle:Order')->find($data['pk']);
        if (!$order) {
            throw $this->createNotFoundException('Unable to find Order entity.');
        }
        $order->setProcess($data['value']);
        $em->flush();

        if($order->getProcess() == 'confirm') {

            $em->getRepository('EcommerceBundle:OrderItem')->updateOrderItem($order);
            $em->getRepository('EcommerceBundle:Order')->updateOrder($order);
            $em->getRepository('EcommerceBundle:OrderPayment')->updateOrderPayment($order);
            $em->getRepository('EcommerceBundle:Order')->updateOrderPayment($order);

            $dispatcher = $this->container->get('event_dispatcher');
            $dispatcher->dispatch('setting_tool.post.order_confirm_sms', new \Setting\Bundle\ToolBundle\Event\EcommerceOrderSmsEvent($order));
        }
        exit;

    }

    public function purchaseItemSelectAction(OrderItem $orderItem)
    {

        $entities = $this->getDoctrine()->getRepository('EcommerceBundle:OrderItem')->getPurchaseVendorItemList($orderItem);
        $items = array();
        $items[]=array('value' => '','text'=> 'Add Inventory Item');
        foreach ($entities as $entity):
            $items[]=array('value' => $entity['id'],'text'=> $entity['barcode'].'('.$entity['quantity'].')');
        endforeach;
        return new JsonResponse($items);
    }

    public function purchaseItemAddAction(Request $request , OrderItem $entity)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $purchaseItem = $this->getDoctrine()->getRepository('InventoryBundle:PurchaseItem')->find($data['value']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Order entity.');
        }
        $entity->setPurchaseItem($purchaseItem);
        $em->flush();
        exit;
    }


    public function confirmItemAction(Order $order, OrderItem $orderItem)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $orderItem->setStatus($data['status']);
        $em->persist($orderItem);
        $em->flush();
        $this->getDoctrine()->getRepository('EcommerceBundle:Order')->updateOrder($order);
        return new Response('success');

    }

    public function paymentDeleteAction(OrderPayment $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Expenditure entity.');
        }
        $em->remove($entity);
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'error',"Data has been deleted successfully"
        );
        return new Response('success');
    }

    public function confirmPaymentAction(OrderPayment $payment, $process)
    {
        $em = $this->getDoctrine()->getManager();
        $payment->setStatus($process);
        $em->persist($payment);
        $em->flush();
        $this->getDoctrine()->getRepository('EcommerceBundle:Order')->updateOrderPayment($payment->getOrder());
        $dispatcher = $this->container->get('event_dispatcher');
        $dispatcher->dispatch('setting_tool.post.order_payment_confirm_sms', new \Setting\Bundle\ToolBundle\Event\EcommerceOrderPaymentSmsEvent($payment));

        return new Response('success');

    }


    public function getBarcode($invoice)
    {
        $barcode = new BarcodeGenerator();
        $barcode->setText($invoice);
        $barcode->setType(BarcodeGenerator::Code128);
        $barcode->setScale(1);
        $barcode->setThickness(32);
        $barcode->setFontSize(7);
        $code = $barcode->generate();
        $data = '';
        $data .= '<img src="data:image/png;base64,' . $code . '" />';
        return $data;
    }

    public function pdfAction(Order $order)
    {


        /* @var Order $order */

        $amountInWords = $this->get('settong.toolManageRepo')->intToWords($order->getGrandTotalAmount());
        $barcode = $this->getBarcode($order->getInvoice());
        $html = $this->renderView( 'CustomerBundle:Order:invoice.html.twig', array(
            'globalOption' => $order->getGlobalOption(),
            'entity' => $order,
            'amountInWords' => $amountInWords,
            'barcode' => $barcode,
            'print' => ''
        ));

        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy          = new Pdf($wkhtmltopdfPath);
        $pdf             = $snappy->getOutputFromHtml($html);

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="online-invoice-'.$order->getInvoice().'.pdf"');
        echo $pdf;
        return new Response('');

    }

    public function printAction(Order $order)
    {
        $amountInWords = $this->get('settong.toolManageRepo')->intToWords($order->getGrandTotalAmount());
        $barcode = $this->getBarcode($order->getInvoice());
        return $this->render('EcommerceBundle:Order:invoice.html.twig', array(
            'globalOption' => $order->getGlobalOption(),
            'entity' => $order,
            'amountInWords' => $amountInWords,
            'barcode' => $barcode,
            'print' => ''
        ));

    }


}
