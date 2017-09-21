<?php

namespace Appstore\Bundle\EcommerceBundle\Controller;

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
     * Creates a new Order entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Order();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('customer_order_show', array('id' => $entity->getId())));
        }

        return $this->render('EcommerceBundle:Order:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Order entity.
     *
     * @param Order $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Order $entity)
    {
        $form = $this->createForm(new OrderType(), $entity, array(
            'action' => $this->generateUrl('customer_order_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Order entity.
     *
     */
    public function newAction()
    {
        $entity = new Order();
        $form   = $this->createCreateForm($entity);

        return $this->render('EcommerceBundle:Order:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
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
     * Displays a form to edit an existing Order entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EcommerceBundle:Order')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Order entity.');
        }

        $editForm = $this->createEditForm($entity);

        return $this->render('EcommerceBundle:Order:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
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
        $form = $this->createForm(new OrderType(), $entity, array(
            'action' => $this->generateUrl('customer_order_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Order entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EcommerceBundle:Order')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Order entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('customer_order_edit', array('id' => $id)));
        }

        return $this->render('EcommerceBundle:Order:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Order entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('EcommerceBundle:Order')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Order entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('customer_order'));
    }

    /**
     * Creates a form to delete a Order entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('customer_order_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    public function paymentAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $order = $em->getRepository('EcommerceBundle:Order')->find($id);
        return $this->render('EcommerceBundle:Order:payment.html.twig', array(
            'entity'                => $order,
        ));

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
        if($data['submitProcess'] == 'confirm'){
            $order->setProcess('confirm');
            $em->getRepository('EcommerceBundle:OrderItem')->itemOrderUpdateBarcode($order);
        }else{
            $order->setProcess($data['submitProcess']);
        }
        if(isset($data['comment']) and !empty($data['comment'])){
            $order->setComment($data['comment']);
        }else{
            $order->setComment(null);
        }
        $em->persist($order);
        $em->flush();

        if($data['submitProcess'] == 'confirm'){

            $em->getRepository('InventoryBundle:Item')->onlineOrderUpdate($order);
            $em->getRepository('InventoryBundle:StockItem')->insertOnlineOrder($order);
            $online = $em->getRepository('AccountingBundle:AccountOnlineOrder')->insertAccountOnlineOrder($order);
            $em->getRepository('AccountingBundle:Transaction')->onlineOrderTransaction($order,$online);

            $this->get('session')->getFlashBag()->add(
                'success',"Customer has been confirmed"
            );

            $dispatcher = $this->container->get('event_dispatcher');
            $dispatcher->dispatch('setting_tool.post.order_confirm', new \Setting\Bundle\ToolBundle\Event\EcommerceOrderConfirmEvent($order));

        }else{

            $this->get('session')->getFlashBag()->add(
                'success',"Message has been sent successfully"
            );

            $dispatcher = $this->container->get('event_dispatcher');
            $dispatcher->dispatch('setting_tool.post.order_sms', new \Setting\Bundle\ToolBundle\Event\EcommerceOrderSmsEvent($order));

        }
        return $this->redirect($this->generateUrl('customer_order'));

    }

    public function orderProcesSourceAction()
    {

        $items[]=array('value' => 'Created','text'=> 'Created');
        $items[]=array('value' => 'WfC','text'=> 'WfC');
        $items[]=array('value' => 'Confirm','text'=> 'Confirm');
        $items[]=array('value' => 'Delivered','text'=> 'Delivered');
        $items[]=array('value' => 'Returned','text'=> 'Returned');
        $items[]=array('value' => 'Cancel','text'=> 'Cancel');
        $items[]=array('value' => 'Delete','text'=> 'Delete');
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

    public function pdfAction(Order $order)
    {

        $html = $this->renderView(
            'EcommerceBundle:Order:invoice.html.twig', array(
                'entity' => $order,
                'print' => ''
            )
        );
        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy          = new Pdf($wkhtmltopdfPath);
        $pdf             = $snappy->getOutputFromHtml($html);

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="incomePdf.pdf"');
        echo $pdf;
        return new Response('');

    }

    public function printAction(Order $order)
    {
        return $this->render('EcommerceBundle:Order:invoice.html.twig', array(
            'entity' => $order,
            'print' => '<script>window.print();</script>'
        ));

    }





}
