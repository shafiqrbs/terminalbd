<?php

namespace Appstore\Bundle\TallyBundle\Controller;

use Appstore\Bundle\TallyBundle\Entity\PurchaseItem;
use Appstore\Bundle\TallyBundle\Form\PurchaseItemType;
use Appstore\Bundle\TallyBundle\Form\PurchaseOpeningItemType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * PurchaseItem controller.
 *
 */
class PurchaseItemController extends Controller
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
     * Lists all PurchaseItem entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $config = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('TallyBundle:PurchaseItem')->findWithSearch($config,$data);
        $pagination = $this->paginate($entities);
        return $this->render('TallyBundle:PurchaseItem:index.html.twig', array(
            'config' => $config,
            'pagination' => $pagination,
            'searchForm' => $data,
        ));
    }


    /**
     * Creates a new Particular entity.
     *
     */
    public function createAction(Request $request)
    {

        $entity = new PurchaseItem();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setGlobalOption($this->getUser()->getGlobalOption());
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('PurchaseItem'));
        }
        return $this->render('TallyBundle:PurchaseItem:opening.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Particular entity.
     *
     * @param PurchaseItem $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(PurchaseItem $entity)
    {
        $config = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new PurchaseOpeningItemType($config), $entity, array(
            'action' => $this->generateUrl('tally_purchaseitem_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }


    /**
     * Displays a form to create a new Particular entity.
     *
     */
    public function newAction()
    {
        $entity = new PurchaseItem();
        $form   = $this->createCreateForm($entity);

        return $this->render('TallyBundle:PurchaseItem:opening.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }


    /**
     * Displays a form to edit an existing PurchaseItem entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var $entity PurchaseItem */

        $entity = $em->getRepository('TallyBundle:PurchaseItem')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseItem entity.');
        }

        $editForm = $this->createEditForm($entity);
        $this->getDoctrine()->getRepository('TallyBundle:PurchaseItem')->generateSerialNo($entity);
        return $this->render('TallyBundle:PurchaseItem:new.html.twig', array(
            'entity'      => $entity,
            'purchaseInfo'      => $entity->getPurchase(),
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a PurchaseItem entity.
    *
    * @param PurchaseItem $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(PurchaseItem $entity)
    {
        $option = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new PurchaseItemType($option), $entity, array(
            'action' => $this->generateUrl('tally_purchaseitem_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        return $form;
    }


    /**
     * Edits an existing PurchaseItem entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TallyBundle:PurchaseItem')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseItem entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        $data = $request->request->all();
        if ($editForm->isValid()) {
            $em->flush();
            $this->getDoctrine()->getRepository('TallyBundle:ItemMetaAttribute')->insertProductMeta($entity,$data);
            return $this->redirect($this->generateUrl('tally_purchaseitem_attribute', array('id' => $id)));
        }
        return $this->render('TallyBundle:PurchaseItem:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    public function inlineUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('TallyBundle:PurchaseItem')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseItem entity.');
        }
        if($data['name'] == 'SalesPrice' and 0 < (float)$data['value']){
            $process = 'set'.$data['name'];
            $entity->$process((float)$data['value']);
            $entity->setSalesSubTotal((float)$data['value'] * $entity->getQuantity());
            $em->flush();
        }

        if($data['name'] == 'PurchasePrice' and 0 < (float)$data['value']){
            $entity->setPurchasePrice((float)$data['value']);
            $entity->setPurchaseSubTotal((float)$data['value'] * $entity->getQuantity());
            $em->flush();
            $em->getRepository('InventoryBundle:Purchase')->purchaseSimpleUpdate($entity->getPurchase());
        }
        $salesQnt = $this->getDoctrine()->getRepository('InventoryBundle:StockItem')->getPurchaseItemQuantity($entity,array('sales','damage','purchaseReturn'));
        if($data['name'] == 'Quantity' and $salesQnt <= (int)$data['value']){
            $entity->setQuantity((int)$data['value']);
            $entity->setPurchaseSubTotal((int)$data['value'] * $entity->getPurchasePrice());
            $entity->setSalesSubTotal((int)$data['value'] * $entity->getSalesPrice());
            $em->flush();
            $em->getRepository('InventoryBundle:Purchase')->purchaseSimpleUpdate($entity->getPurchase());
        }

        if($data['name'] == 'Barcode'){
            $existBarcode = $this->getDoctrine()->getRepository('TallyBundle:PurchaseItem')->findBy(array('barcode' => $data['value']));
            if(empty($existBarcode)){
                $process = 'set'.$data['name'];
                $entity->$process($data['value']);
                $em->flush();
            }
        }
        exit;

    }


    /**
     * Deletes a Item entity.
     *
     */
    public function deleteAction(PurchaseItem $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find item entity.');
        }

        try {

            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'error',"Data has been deleted successfully"
            );

        } catch (ForeignKeyConstraintViolationException $e) {
            $this->get('session')->getFlashBag()->add(
                'notice',"Data has been relation another Table"
            );
        }
        exit;

    }


    /**
     * Status a Page entity.
     *
     */
    public function approveAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('TallyBundle:PurchaseItem')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find District entity.');
        }
        $entity->setProcess("Approved");
        $em->flush();
        $this->getDoctrine()->getRepository('TallyBundle:Item')->updateRemovePurchaseQuantity($entity->getProductGroup(),'opening');
        $this->get('session')->getFlashBag()->add(
            'success',"Item has been approved successfully"
        );
        exit;

    }



}
