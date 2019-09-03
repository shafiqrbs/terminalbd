<?php

namespace Appstore\Bundle\AssetsBundle\Controller;

use Appstore\Bundle\AssetsBundle\Entity\PurchaseItem;
use Appstore\Bundle\AssetsBundle\Form\OpeningItemEditType;
use Appstore\Bundle\AssetsBundle\Form\PurchaseItemType;
use Appstore\Bundle\AssetsBundle\Form\PurchaseOpeningItemType;
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
        $config = $this->getUser()->getGlobalOption()->getAssetsConfig()->getId();
        $entities = $em->getRepository('AssetsBundle:PurchaseItem')->findWithSearch($config,'opening',$data);
        $pagination = $this->paginate($entities);
        return $this->render('AssetsBundle:PurchaseItem:index.html.twig', array(
            'config' => $config,
            'pagination' => $pagination,
            'searchForm' => $data,
        ));
    }


    /**
     * Lists all PurchaseItem entities.
     *
     */

    public function purchaseIndexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $data = array('process'=>'Approved');
        $config = $this->getUser()->getGlobalOption()->getAssetsConfig()->getId();
        $entities = $em->getRepository('AssetsBundle:PurchaseItem')->findWithSearch($config,'purchase',$data);
        $pagination = $this->paginate($entities);
        return $this->render('AssetsBundle:PurchaseItem:index.html.twig', array(
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
            $config = $this->getUser()->getGlobalOption()->getAssetsConfig();
            $entity->setConfig($config);
            $entity->setMode('opening');
            $entity->setPurchasePrice($entity->getPrice());
            $entity->setSubTotal($entity->getPrice() * $entity->getQuantity());
            if($entity->getAssuranceFromVendor() and $entity->getAssuranceFromVendor()->getDays() > 0 and $entity->getEffectedDate()){
                $effected = $entity->getEffectedDate();
                $datetime = $effected->add(new \DateInterval("P{$entity->getAssuranceFromVendor()->getDays()}D"));
                $entity->setExpiredDate($datetime);
            }
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            $this->getDoctrine()->getRepository('AssetsBundle:PurchaseItem')->generateSerialNo($entity);
            return $this->redirect($this->generateUrl('assets_purchaseitem'));
        }
        return $this->render('AssetsBundle:PurchaseItem:opening.html.twig', array(
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
        $config = $this->getUser()->getGlobalOption()->getAssetsConfig();
        $form = $this->createForm(new PurchaseOpeningItemType($config), $entity, array(
            'action' => $this->generateUrl('assets_purchaseitem_create'),
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

        return $this->render('AssetsBundle:PurchaseItem:opening.html.twig', array(
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
    private function createEditForm(PurchaseItem $entity)
    {
        $config = $this->getUser()->getGlobalOption()->getAssetsConfig();
        $form = $this->createForm(new OpeningItemEditType($config), $entity, array(
            'action' => $this->generateUrl('assets_purchaseitem_update',array('id'=>$entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }



    /**
     * Displays a form to edit an existing PurchaseItem entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var $entity PurchaseItem */

        $entity = $em->getRepository('AssetsBundle:PurchaseItem')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseItem entity.');
        }

        $editForm = $this->createEditForm($entity);
        $this->getDoctrine()->getRepository('AssetsBundle:PurchaseItem')->generateSerialNo($entity);
        return $this->render('AssetsBundle:PurchaseItem:editOpening.html.twig', array(
            'entity'      => $entity,
            'purchaseInfo'      => $entity->getPurchase(),
            'form'   => $editForm->createView(),
        ));
    }


    /**
     * Edits an existing PurchaseItem entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AssetsBundle:PurchaseItem')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseItem entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        $data = $request->request->all();
        if ($editForm->isValid()) {
            $em->flush();
            $this->getDoctrine()->getRepository('AssetsBundle:ItemMetaAttribute')->insertProductMeta($entity,$data);
            return $this->redirect($this->generateUrl('assets_purchaseitem_edit', array('id' => $id)));
        }
        return $this->render('AssetsBundle:PurchaseItem:editOpening.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }



    /**
     * Displays a form to edit an existing PurchaseItem entity.
     *
     */
    public function attributeAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var $entity PurchaseItem */

        $entity = $em->getRepository('AssetsBundle:PurchaseItem')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseItem entity.');
        }

        $editForm = $this->createAttributeForm($entity);
        $this->getDoctrine()->getRepository('AssetsBundle:PurchaseItem')->generateSerialNo($entity);
        return $this->render('AssetsBundle:PurchaseItem:new.html.twig', array(
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
    private function createAttributeForm(PurchaseItem $entity)
    {

        $form = $this->createForm(new PurchaseItemType(), $entity, array(
            'action' => $this->generateUrl('assets_purchaseitem_attribute_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        return $form;
    }


    public function updateAttributeAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AssetsBundle:PurchaseItem')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseItem entity.');
        }
        $editForm = $this->createAttributeForm($entity);
        $editForm->handleRequest($request);
        $data = $request->request->all();
        if ($editForm->isValid()) {
            $em->flush();
            $this->getDoctrine()->getRepository('AssetsBundle:ItemMetaAttribute')->insertProductMeta($entity,$data);
            return $this->redirect($this->generateUrl('assets_purchaseitem_attribute', array('id' => $id)));
        }
        return $this->render('AssetsBundle:PurchaseItem:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }


    public function inlineUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AssetsBundle:PurchaseItem')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseItem entity.');
        }
        if($data['name'] == 'SalesPrice' and 0 < (float)$data['value']){
            $entity->setSalesPrice((float)$data['value']);
        }
        if($data['name'] == 'PurchasePrice' and 0 < (float)$data['value']){
            $entity->setPrice((float)$data['value']);
            $entity->setPurchasePrice((float)$data['value']);
            $entity->setSubTotal((float)$data['value'] * $entity->getQuantity());
        }
        if($data['name'] == 'Quantity' and 0 < (float)$data['value'] ){
            $entity->setQuantity((int)$data['value']);
            $entity->setPurchasePrice((int)$data['value'] * $entity->getPurchasePrice());
            $entity->setSubTotal((int)$data['value'] * $entity->getSalesPrice());
        }
        if($data['name'] == 'Barcode'){
            $existBarcode = $this->getDoctrine()->getRepository('AssetsBundle:PurchaseItem')->findBy(array('barcode' => $data['value']));
            if(empty($existBarcode)){
                $process = 'set'.$data['name'];
                $entity->$process($data['value']);
            }
        }
        $em->flush();
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
    public function approveAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AssetsBundle:PurchaseItem')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find District entity.');
        }
        $entity->setProcess("Approved");
        $entity->setApprovedBy($this->getUser());
        $em->flush();
        $journal = $em->getRepository('AccountingBundle:AccountJournal')->openingAssetsItemJournal($entity);
        $em->getRepository('AccountingBundle:Transaction')->openingItemDistributionTransaction($entity,$journal);
        $this->getDoctrine()->getRepository('AssetsBundle:PurchaseItem')->generateSerialNo($entity);
        $this->getDoctrine()->getRepository('AssetsBundle:StockItem')->processStockQuantity($entity->getConfig(),$entity->getId(),'opening');
        $this->getDoctrine()->getRepository('AssetsBundle:Item')->updateRemovePurchaseQuantity($entity->getItem(),'opening');
        $this->getDoctrine()->getRepository('AssetsBundle:Product')->insertPurchaseItemToAssetsProduct($entity);
        $this->get('session')->getFlashBag()->add(
            'success',"Item has been approved successfully"
        );
        exit;

    }



}
