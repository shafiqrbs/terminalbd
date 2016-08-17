<?php

namespace Appstore\Bundle\InventoryBundle\Controller;

use Appstore\Bundle\AccountingBundle\Entity\AccountPurchase;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseItem;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseVendorItem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\InventoryBundle\Entity\Purchase;
use Appstore\Bundle\InventoryBundle\Form\PurchaseType;
use Symfony\Component\HttpFoundation\Response;

/**
 * PurchaseOrder controller.
 *
 */
class PurchaseController extends Controller
{

    public function paginate($entities)
    {

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            25  /*limit per page*/
        );
        return $pagination;
    }

    /**
     * Lists all PurchaseOrder entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities = $em->getRepository('InventoryBundle:Purchase')->findWithSearch($inventory,$data);
        $purchaseOverview = $this->getDoctrine()->getRepository('InventoryBundle:Purchase')->purchaseOverview($inventory,$data);
        $pagination = $this->paginate($entities);

        return $this->render('InventoryBundle:Purchase:index.html.twig', array(
            'entities' => $pagination,
            'purchaseOverview' => $purchaseOverview,
            'searchForm' => $data
        ));
    }
    /**
     * Creates a new Purchase entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Purchase();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
            $entity->setInventoryConfig($inventory);
            $due = ($entity->getTotalAmount() - $entity->getPaymentAmount());
            $entity->setDueAmount($due);
            $entity->setProcess('created');
            $entity->upload();
            $em->persist($entity);
            $em->flush();
           // $em->getRepository('InventoryBundle:PurchaseVendorItem')->insertPurchaseVendorItem($entity,$data);
            return $this->redirect($this->generateUrl('inventory_purchasevendoritem_new', array('purchase' => $entity->getId())));
        }

        return $this->render('InventoryBundle:Purchase:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    public function checkItemQuantityAction(Request $request)
    {
        $data = $request->request->all();
        $itemQnt = 0;
        $purchasePrice = 0;
        $salesPrice = 0;
        $countItem=0;
        foreach ($data['quantity'] as $key=>$quantity) {
            $itemQnt += $quantity;
            $purchasePrice += $data['purchasePrice'][$key];
            $salesPrice += $data['salesPrice'][$key];
            $countItem++;
        }
        if( $data["appstore_bundle_inventorybundle_purchase"]["totalQnt"] != $itemQnt ){
            $msg = "Purchase total quantity and added item quantity does not match";
        }elseif ($countItem != $data["appstore_bundle_inventorybundle_purchase"]["totalItem"]){
            $msg = "Purchase total item and added item does not match";
        }elseif ($purchasePrice != $data["appstore_bundle_inventorybundle_purchase"]["totalAmount"]){
            $msg = "Purchase total item price and total amount does not match";
        }elseif($purchasePrice == 0){
            $msg = "Purchase item amount does not blank";
        }elseif ( $data["appstore_bundle_inventorybundle_purchase"]["totalAmount"] == 0){
            $msg = "Purchase total amount does not blank";
        }elseif ( $salesPrice < $data["appstore_bundle_inventorybundle_purchase"]["totalAmount"]){
            $msg = "Sales amount must be more then purchase price";
        }elseif ( $salesPrice == 0){
            $msg = "Purchase sales price does not blank";;
        }else{
            $msg = 'success';
        }
        return new Response($msg);
        exit;

    }


    /**
     * Creates a form to create a Purchase entity.
     *
     * @param Purchase $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Purchase $entity)
    {
        $inventoryConfig = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $form = $this->createForm(new PurchaseType($inventoryConfig), $entity, array(
            'action' => $this->generateUrl('purchase_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form purchase',
                'novalidate' => 'novalidate',
            )
        ));
       return $form;
    }

    /**
     * Displays a form to create a new Purchase entity.
     *
     */
    public function newAction()
    {
        $entity = new Purchase();
        $form   = $this->createCreateForm($entity);

        return $this->render('InventoryBundle:Purchase:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Purchase entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:Purchase')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Purchase entity.');
        }
        return $this->render('InventoryBundle:Purchase:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * Displays a form to edit an existing Purchase entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:Purchase')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Purchase entity.');
        }
        $editForm = $this->createEditForm($entity);

        return $this->render('InventoryBundle:Purchase:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Purchase entity.
    *
    * @param Purchase $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Purchase $entity)
    {
        $inventoryConfig =  $this->getUser()->getGlobalOption()->getInventoryConfig();
        $form = $this->createForm(new PurchaseType($inventoryConfig), $entity, array(
            'action' => $this->generateUrl('purchase_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form purchase',
                'novalidate' => 'novalidate',
            )

        ));
        return $form;
    }
    /**
     * Edits an existing Purchase entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:Purchase')->find($id);
        $data = $request->request->all();
        if( $entity->getVendor()->getId() == $data['appstore_bundle_inventorybundle_purchase']['vendor']){
            $em->remove($entity->getPurchaseVendorItems());
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            return $this->redirect($this->generateUrl('purchase_edit', array('id' => $id)));
        }

        return $this->render('InventoryBundle:Purchase:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),

        ));
    }



    public function approveAction(Purchase $purchase)
    {

        $em = $this->getDoctrine()->getManager();
        $purchase->setApprovedBy($this->getUser());
        $purchase->setProcess('approved');
        $em->persist($purchase);
        $em->flush();

        $em->getRepository('InventoryBundle:Item')->getItemUpdatePriceQnt($purchase);
        $em->getRepository('InventoryBundle:StockItem')->insertPurchaseStockItem($purchase);
        $em->getRepository('AccountingBundle:Transaction')->purchaseTransaction($purchase,$purchase->getInventoryConfig(),'Purchase');
        $em->getRepository('AccountingBundle:AccountPurchase')->insertAccountPurchase($purchase,$purchase->getInventoryConfig());
        return new Response(json_encode(array('success'=>'success')));

    }

    /**
     * Deletes a Purchase entity.
     *
     */
    public function deleteAction(Purchase $purchase)
    {
        if($purchase){
            $em = $this->getDoctrine()->getManager();
            $em->remove($purchase);
            $em->flush();
            return new Response('success');
        }else{
            return new Response('failed');
        }
    }


    /**
     * Deletes a PurchaseVendorItem entity.
     *
     */
    public function deleteVendorAction(PurchaseVendorItem $vendorItem)
    {

        if($vendorItem){
            $em = $this->getDoctrine()->getManager();
            $em->remove($vendorItem);
            $em->flush();
            return new Response('success');
        }else{
            return new Response('failed');
        }

    }

    /**
     * Deletes a PurchaseItem entity.
     *
     */
    public function deleteItemAction(PurchaseItem $purchaseItem)
    {

        if($purchaseItem){
            $em = $this->getDoctrine()->getManager();
            $em->remove($purchaseItem);
            $em->flush();
            return new Response('success');
        }else{
            return new Response('failed');
        }

    }



}
