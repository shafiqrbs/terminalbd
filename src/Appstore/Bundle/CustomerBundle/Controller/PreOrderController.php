<?php

namespace Appstore\Bundle\CustomerBundle\Controller;
use Appstore\Bundle\EcommerceBundle\Entity\PreOrderItem;
use Appstore\Bundle\EcommerceBundle\Form\PreOrderItemType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\EcommerceBundle\Entity\PreOrder;
use Appstore\Bundle\EcommerceBundle\Form\PreOrderType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * PreOrder controller.
 *
 */
class PreOrderController extends Controller
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
     * Lists all PreOrder entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $entities = $em->getRepository('EcommerceBundle:PreOrder')->findBy(array('createdBy' => $user), array('updated' => 'desc'));
        $pagination = $this->paginate($entities);
        return $this->render('CustomerBundle:PreOrder:index.html.twig', array(
            'entities' => $pagination,
        ));
    }

    /**
     * Creates a new PreOrder entity.
     *
     */
    public function createAction(Request $request)
    {

        $entity = new PreOrder();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('preorder_show', array('id' => $entity->getId())));
        }

        return $this->render('CustomerBundle:PreOrder:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a PreOrder entity.
     *
     * @param PreOrder $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(PreOrder $entity)
    {
        $form = $this->createForm(new PreOrderType(), $entity, array(
            'action' => $this->generateUrl('preorder_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new PreOrder entity.
     *
     */
    public function newAction($domain)
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('slug'=>$domain));
        $entity = new PreOrder();
        $ecommerce = $globalOption->getEcommerceConfig();
        $entity->setGlobalOption($globalOption);
        $entity->setEcommerceConfig($ecommerce);
        $entity->setCustomer($this->getUser());
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('preorder_item', array('id' => $entity->getId())));

    }

    /**
     * Finds and displays a PreOrder entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EcommerceBundle:PreOrder')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PreOrder entity.');
        }
        return $this->render('CustomerBundle:PreOrder:show.html.twig', array(
            'entity' => $entity,

        ));
    }

    /**
     * Displays a form to edit an existing PreOrder entity.
     *
     */
    public function editAction(PreOrder $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PreOrder entity.');
        }

        $editForm = $this->createEditForm($entity);
        return $this->render('CustomerBundle:PreOrder:new.html.twig', array(
            'entity' => $entity,
            'form' => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a PreOrder entity.
     *
     * @param PreOrder $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(PreOrder $entity)
    {

        $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $form = $this->createForm(new PreOrderType($entity->getGlobalOption(),$location), $entity, array(
            'action' => $this->generateUrl('preorder_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        return $form;
    }

    /**
     * Edits an existing PreOrder entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $entity = $em->getRepository('EcommerceBundle:PreOrder')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PreOrder entity.');
        }


        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $entity->setProcess($data['submitProcess']);
            $entity->setComment('Confirm your order within short time');
            $em->flush();
            return $this->redirect($this->generateUrl('preorder_item', array('id' => $id)));
        }

        if($data['submitProcess'] == 'confirm'){

         $this->get('session')->getFlashBag()->add('success',"Message has been sent successfully");
         $dispatcher = $this->container->get('event_dispatcher');
         $dispatcher->dispatch('setting_tool.post.preorder_sms', new \Setting\Bundle\ToolBundle\Event\EcommercePreOrderSmsEvent($entity));

         }


    }

    /**
     * Deletes a PreOrder entity.
     *
     */
    public function deleteAction(PreOrder $preOrder)
    {

        if ($preOrder) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($preOrder);
            $em->flush();
            return new Response('success');

        }else{

            return new Response('failed');

        }
    }

    private function createItemForm(PreOrderItem $entity,$preOrder)
    {
        $form = $this->createForm(new PreOrderItemType(), $entity, array(
            'action' => $this->generateUrl('preorder_item_create', array('id' => $preOrder)),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    public function itemAction(PreOrder $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PreOrder entity.');
        }
        $preOrderItem = new PreOrderItem();
        $form = $this->createItemForm($preOrderItem,$entity->getId());
        $preOrderform = $this->createEditForm($entity);

        $banks = $this->getDoctrine()->getRepository('EcommerceBundle:BankAccount')->findBy(array('status'=>1),array('name'=>'asc'));
        $bkashs = $this->getDoctrine()->getRepository('EcommerceBundle:BkashAccount')->findBy(array('status'=>1),array('name'=>'asc'));
        $paymentTypes = $this->getDoctrine()->getRepository('SettingToolBundle:PaymentType')->findBy(array('status'=>1),array('name'=>'asc'));
        return $this->render('CustomerBundle:PreOrder:item.html.twig', array(
            'entity'      => $entity,
            'banks'      => $banks,
            'bkashs'      => $bkashs,
            'paymentTypes'      => $paymentTypes,
            'form'   => $form->createView(),
            'preOrderform'   => $preOrderform->createView(),
        ));
    }

    public function createItemAction(Request $request,PreOrder $preOrder)
    {
        $preOrderItem = new PreOrderItem();
        $form = $this->createItemForm($preOrderItem,$preOrder->getId());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $preOrderItem->setPreOrder($preOrder);
            $preOrderItem->setTotal($preOrderItem->getQuantity() * $preOrderItem->getPrice());
            $preOrderItem->setTotalDollar($preOrderItem->getQuantity() * $preOrderItem->getDollar());
            $em->persist($preOrderItem);
            $em->flush();
            $this->getDoctrine()->getRepository('EcommerceBundle:PreOrder')->updatePreOder($preOrder);
            return $this->redirect($this->generateUrl('preorder_item', array('id' => $preOrder->getId())));
        }

        return $this->render('CustomerBundle:PreOrder:item.html.twig', array(
            'entity' => $preOrder,
            'form'   => $form->createView(),
        ));
    }

    public function deleteItemAction(PreOrder $preOrder, PreOrderItem $preOrderItem)
    {
        if ($preOrderItem) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($preOrderItem);
            $em->flush();
            $this->getDoctrine()->getRepository('EcommerceBundle:PreOrder')->updatePreOder($preOrder);
            return new Response('success');
        }else{
            return new Response('failed');
        }

    }


    public function processAction(PreOrder $preOrder,$process)
    {
        $data = $_REQUEST;
        $em = $this->getDoctrine()->getManager();
        $preOrder->setProcess($process);
        if(!empty( $_GET['delivery'])){
            $address = $data['address'];
            $preOrder->setAddress($address);
            $delivery = $_GET['delivery'];
            $preOrder->setDelivery($delivery);
        }
        $em->persist($preOrder);
        $em->flush();
        return new Response('success');
    }

    public function approveAction(PreOrder $preOrder)
    {

        $em = $this->getDoctrine()->getManager();
        $preOrder->setApprovedBy($this->getUser());
        $preOrder->setProcess('approved');
        $em->persist($preOrder);
        $em->flush();
        return new Response('success');

    }

    public function confirmItemAction(PreOrder $preOrder, PreOrderItem $preOrderItem)
    {
        $em = $this->getDoctrine()->getManager();
        if ($preOrderItem->isStatus() == 1){
            $preOrderItem->setStatus(0);
        }else{
            $preOrderItem->setStatus(1);
        }
        $em->persist($preOrderItem);
        $em->flush();
        $this->getDoctrine()->getRepository('EcommerceBundle:PreOrder')->updatePreOder($preOrder);
        return new Response('success');

    }


    public function paymentAction(Request $request ,PreOrder $preOrder)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        if(!empty( $data['paymentType'])){
            $paymentType =     $paymentTypes = $this->getDoctrine()->getRepository('SettingToolBundle:PaymentType')->findOneBy(array('slug'=>$data['paymentType']));
            $preOrder->setPaymentType($paymentType);
            if($data['paymentType'] == 'cash-on-hand'){}
            if($data['paymentType'] == 'cash-on-delivery'){}
            if($data['paymentType'] == 'cash-on-bank'){
                $bank =     $paymentTypes = $this->getDoctrine()->getRepository('EcommerceBundle:BankAccount')->find($data['bank']);
                $preOrder->setBankAccount($bank);
            }
            if($data['paymentType'] == 'cash-on-bkash'){
                $bkash =     $paymentTypes = $this->getDoctrine()->getRepository('EcommerceBundle:BkashAccount')->find($data['bkash']);
                $preOrder->setBankAccount($bkash);
            }
            if($data['paymentType'] == 'cash-on-mobile-bank'){}
        }
        if(!empty( $data['advanceAmount']) && $preOrder->getGrandTotal() >  $data['advanceAmount'] ){
            $advanceAmount = $data['advanceAmount'];
            $preOrder->setAdvanceAmount($advanceAmount);
            $preOrder->setProcess('wfc');
            $date = strtotime($data['deliveryDate']);
            $deliveryDate = date('d-m-Y H:i:s',$date);
            $preOrder->setDeliveryDate(new \DateTime(($deliveryDate)));
        }
        $em->persist($preOrder);
        $em->flush();
        return new Response('success');
    }

    public function invoiceAction(PreOrder $preOrder)
    {
        return $this->render('CustomerBundle:PreOrder:invoice.html.twig', array(
            'entity' => $preOrder
        ));
    }



}
