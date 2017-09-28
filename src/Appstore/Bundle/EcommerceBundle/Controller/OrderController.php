<?php

namespace Appstore\Bundle\EcommerceBundle\Controller;

use Appstore\Bundle\EcommerceBundle\Entity\OrderPayment;
use Appstore\Bundle\EcommerceBundle\Form\OrderPaymentType;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseVendorItem;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
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
     * Lists all Order entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('EcommerceBundle:Order')->findBy(array('globalOption'=>$globalOption),array('updated'=>'desc'));
        $pagination = $this->paginate($entities);
        return $this->render('EcommerceBundle:Order:index.html.twig', array(
            'entities' => $pagination,
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
        return $this->render('EcommerceBundle:Order:show.html.twig', array(
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
            'method' => 'post',
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

        return $this->render('EcommerceBundle:Order:payment.html.twig', array(
            'entity'                => $order,
            'orderForm'             => $orderForm->createView(),
            'paymentForm'           => $payment->createView(),
        ));

    }

    public function paymentProcessAction(Request $request ,Order $order)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = new OrderPayment();
        $entity->setOrder($order);
        if($data['transactionType'] == 'Return'){
            $entity->setTransactionType('Return');
            $entity->setAmount('-'.$data['amount']);
        }else{
            $entity->setTransactionType('Payment');
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
        $this->getDoctrine()->getRepository('EcommerceBundle:Order')->updateOrderPayment($order);
        return new Response('success');
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
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        if($data['process'] != 'sms'){
            $order->setProcess($data['process']);
        }
        if(isset($data['comment']) and !empty($data['comment'])){
            $order->setComment($data['comment']);
        }else{
            $order->setComment(null);
        }
        $order->setDeliveryDate(new \DateTime($data['deliveryDate']));
        $em->persist($order);
        $em->flush();

        if($data['process'] == 'confirm'){

            //$em->getRepository('InventoryBundle:Item')->onlineOrderUpdate($order);
           // $em->getRepository('InventoryBundle:StockItem')->insertOnlineOrder($order);
            $online = $em->getRepository('AccountingBundle:AccountOnlineOrder')->insertAccountOnlineOrder($order);
            $em->getRepository('AccountingBundle:Transaction')->onlineOrderTransaction($order,$online);

            $this->get('session')->getFlashBag()->add('success',"Customer has been confirmed");
            $dispatcher = $this->container->get('event_dispatcher');
            $dispatcher->dispatch('setting_tool.post.order_confirm_sms', new \Setting\Bundle\ToolBundle\Event\EcommerceOrderSmsEvent($order));

        }else{

            $this->get('session')->getFlashBag()->add('success',"Message has been sent successfully");
            $dispatcher = $this->container->get('event_dispatcher');
            $dispatcher->dispatch('setting_tool.post.order_comment_sms', new \Setting\Bundle\ToolBundle\Event\EcommerceOrderSmsEvent($order));

        }
        return new Response('success');

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
        $entity = $em->getRepository('EcommerceBundle:Order')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Order entity.');
        }
        $entity->setProcess($data['value']);
        $em->flush();
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

    public function confirmPaymentAction(OrderPayment $payment)
    {
        $em = $this->getDoctrine()->getManager();
        $payment->setStatus(1);
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
