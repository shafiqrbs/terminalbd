<?php

namespace Appstore\Bundle\CustomerBundle\Controller;

use Appstore\Bundle\EcommerceBundle\Entity\OrderItem;
use Appstore\Bundle\EcommerceBundle\Entity\OrderPayment;
use Appstore\Bundle\EcommerceBundle\Form\CustomerOrderPaymentType;
use Appstore\Bundle\EcommerceBundle\Form\CustomerOrderType;
use Appstore\Bundle\EcommerceBundle\Form\OrderPaymentType;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use Knp\Snappy\Pdf;
use Appstore\Bundle\EcommerceBundle\Entity\Order;
use Appstore\Bundle\EcommerceBundle\Form\OrderType;
use Frontend\FrontentBundle\Service\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Date;

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

    public function indexAction($shop)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('EcommerceBundle:Order')->findBy(array('createdBy' => $user), array('updated' => 'desc'));
        $globalOption = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('slug' => $shop));

        $pagination = $this->paginate($entities);
        return $this->render('CustomerBundle:Order:index.html.twig', array(
            'entities' => $pagination,
            'globalOption' => $globalOption,
        ));

    }

    public function productAction($domain)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('slug'=>$domain));
        $inventory = $globalOption->getInventoryConfig();
        $entities = $em->getRepository('InventoryBundle:PurchaseVendorItem')->findGoodsWithSearch($inventory,$data);
        $pagination = $this->paginate($entities);
        return $this->render('CustomerBundle:Order:product.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
        ));

    }

    public function cartAction(Request $request)
    {
        $cart = new Cart($request->getSession());
        return $this->render('CustomerBundle:Order:cart.html.twig', array(
            'cart'   => $cart,
        ));
    }

    /**
     * @param $shop
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function cartToOrderAction($shop , Request $request)
    {
        $couponCode = $_REQUEST['couponCode'];
        $em = $this->getDoctrine()->getManager();
        $cart = new Cart($request->getSession());
        $user = $this->getUser();
        $order = $em->getRepository('EcommerceBundle:Order')->insertNewCustomerOrder($user,$shop,$cart,$couponCode);
        $cart->destroy();
        return $this->redirect($this->generateUrl('order_payment',array('id' => $order->getId(),'shop' => $order->getGlobalOption()->getUniqueCode())));

    }

    /**
     * @param $shop
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function wishToOrderAction($shop , Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('slug' => $shop));
        $em = $this->getDoctrine()->getManager();
        $cart = new Cart($request->getSession());
        $user = $this->getUser();
        $order = $em->getRepository('EcommerceBundle:Order')->insertNewCustomerOrder($user,$globalOption->getuniqueCode(),$cart);
        $cart->destroy();
        return $this->redirect($this->generateUrl('order_payment',array('id' => $order->getId(),'shop' => $order->getGlobalOption()->getUniqueCode())));

    }


    public function showAction(Order $entity)
    {
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Expenditure entity.');
        }
        return $this->render('CustomerBundle:Order:show.html.twig', array(
            'globalOption' => $entity->getGlobalOption(),
            'entity' => $entity,
        ));
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
        $form = $this->createForm(new CustomerOrderPaymentType($order->getGlobalOption(),$location), $entity, array(
            'action' => $this->generateUrl('order_ajax_payment', array('shop' => $order->getGlobalOption()->getUniqueCode(),'id' => $order->getId())),
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
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('EcommerceBundle:Order')->findOneBy(array('createdBy' => $user,'id' => $id));
        $paymentEntity = new  OrderPayment();
        $order = $this->createEditForm($entity);
        $payment = $this->createEditPaymentForm($paymentEntity,$entity);

        return $this->render('CustomerBundle:Order:payment.html.twig', array(
            'globalOption' => $entity->getGlobalOption(),
            'entity'      => $entity,
            'orderForm'   => $order->createView(),
            'paymentForm'   => $payment->createView(),
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
        $form = $this->createForm(new CustomerOrderType($globalOption,$location), $entity, array(
            'action' => $this->generateUrl('order_process', array('shop' => $entity->getGlobalOption()->getUniqueCode(),'id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'id' => 'orderProcess',
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    public function paymentProcessAction(Request $request ,Order $order)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = new OrderPayment();
        if($data['amount']){
            $entity->setOrder($order);
            $entity->setTransactionType('Payment');
            $entity->setAmount($data['amount']);
            if(!empty($data['accountMobileBank'])){
                $accountMobileBank =$this->getDoctrine()->getRepository('AccountingBundle:AccountMobileBank')->find($data['accountMobileBank']);
                $entity->setAccountMobileBank($accountMobileBank);
            }
            $entity->setMobileAccount($data['mobileAccount']);
            $entity->setTransaction($data['transaction']);
            $em->persist($entity);
            $em->flush();
            $this->getDoctrine()->getRepository('EcommerceBundle:Order')->updateOrderPayment($order);
            $dispatcher = $this->container->get('event_dispatcher');
            $dispatcher->dispatch('setting_tool.post.order_payment_sms', new \Setting\Bundle\ToolBundle\Event\EcommerceOrderPaymentSmsEvent($entity));

            return new Response('success');
        }else{
            return new Response('invalid');
        }

    }

    public function processConfirmAction(Request $request ,Order $order)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $order->setProcess($data['process']);
        if(isset($data['address']) and !empty($data['address'])){
            $order->setAddress($data['address']);
        }
        if(isset($data['location']) and !empty($data['location'])){
            $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location')->find($data['location']);
            $order->setLocation($location);
        }
        if($data['cashOnDelivery'] == 1){
            $order->setCashOnDelivery(true);
        }
        $order->setDeliveryDate(new \DateTime($data['deliveryDate']));
        $em->persist($order);
        $em->flush();

        $created = $order->getCreated()->format('d-m-Y');
        $invoice = $order->getInvoice();
        $items = $order->getItem();
        
        if($data['process'] == 'wfc'){
            $this->get('session')->getFlashBag()->add('success',"Dear customer, We have received your order form '.$invoice.' for ('.$items.') dated '.$created.' and we thank you very much.");
            $dispatcher = $this->container->get('event_dispatcher');
            $dispatcher->dispatch('setting_tool.post.order_sms', new \Setting\Bundle\ToolBundle\Event\EcommerceOrderSmsEvent($order));
        //    $dispatcher->dispatch('setting_tool.post.order_sms', new \Setting\Bundle\ToolBundle\Event\EcommerceOrderSmsEvent($order));

        }
        return new Response('success');

    }


    public function deleteAction(Order $entity)
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

    public function itemUpdateAction(Order $order , OrderItem $item)
    {

        $em = $this->getDoctrine()->getManager();
        if (!$item) {
            throw $this->createNotFoundException('Unable to find Expenditure entity.');
        }
        $data = $_REQUEST;
        $item->setQuantity($data['quantity']);
        $item->setSubTotal($item->getPrice() * $data['quantity']);
        if(!empty($data['size']) and $data['size'] !='undefined'){
            $itemSize = $data['size'];
            $goodsItem = $em->getRepository('InventoryBundle:GoodsItem')->find($itemSize);
            $item->setGoodsItem($goodsItem);
            $item->setSize($goodsItem ->getSize());
            $item->setPrice($goodsItem ->getSalesPrice());
            $qnt = (int)$data['quantity'];
            $item->setSubTotal($goodsItem->getSalesPrice() * $qnt );
        }
        if(!empty($data['color']) and $data['color'] !='undefined'){
            $color = $em->getRepository('InventoryBundle:ItemColor')->find($data['color']);
            $item->setColor($color);
        }
        $em->persist($item);
        $em->flush($item);
        $this->getDoctrine()->getRepository('EcommerceBundle:Order')->updateOrder($order);

        $this->get('session')->getFlashBag()->add(
            'success',"Item has been updated successfully"
        );
        return new Response('success');
        exit;
    }

    public function itemDeleteAction($order , OrderItem $item)
    {
        $msg ='itemDelete';
        $em = $this->getDoctrine()->getManager();
        /* @var Order $orderEntity */
        $orderEntity = $this->getDoctrine()->getRepository('EcommerceBundle:Order')->find($order);
        if (!$item) {
            throw $this->createNotFoundException('Unable to find Expenditure entity.');
        }
        $em->remove($item);
        $em->flush();
        $this->getDoctrine()->getRepository('EcommerceBundle:Order')->updateOrder($orderEntity);
        if($orderEntity->getTotalAmount() == 0 and $orderEntity->getItem() == 0 ){
            $em->remove($orderEntity);
            $em->flush();
            $msg ='orderDelete';
        }
        $this->get('session')->getFlashBag()->add(
            'error',"Data has been deleted successfully"
        );
        return new Response($msg);
    }


    public function pdfAction($invoice)
    {

        /* @var Order $order */

        $order = $this->getDoctrine()->getRepository('EcommerceBundle:Order')->findOneBy(array('createdBy' => $this->getUser(),'invoice'=>$invoice));
        $amountInWords = $this->get('settong.toolManageRepo')->intToWords($order->getGrandTotalAmount());
        $barcode = $this->getBarcode($order->getInvoice());
        $html =  $this->renderView('CustomerBundle:Order:invoice.html.twig', array(
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
        header('Content-Disposition: attachment; filename="online-invoice-'.$invoice.'.pdf"');
        echo $pdf;
        return new Response('');

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


    public function printAction($invoice)
    {
        /* @var Order $order */

        $order = $this->getDoctrine()->getRepository('EcommerceBundle:Order')->findOneBy(array('createdBy' => $this->getUser(),'invoice'=>$invoice));
        $amountInWords = $this->get('settong.toolManageRepo')->intToWords($order->getGrandTotalAmount());
        $barcode = $this->getBarcode($order->getInvoice());
        $html =  $this->renderView('CustomerBundle:Order:invoice.html.twig', array(
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
        header('Content-Disposition: attachment; filename="online-invoice-'.$invoice.'.pdf"');
        echo $pdf;
        return new Response('');


    }



}
