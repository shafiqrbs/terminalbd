<?php

namespace Appstore\Bundle\AssetsBundle\Controller;

use Appstore\Bundle\AccountingBundle\Entity\AccountVendor;
use Appstore\Bundle\AssetsBundle\Form\PurchaseType;
use Appstore\Bundle\AssetsBundle\Form\ReceiveType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Vendor controller.
 *
 */
class ReceiveItemController extends Controller
{

    public function paginate($entities)
    {
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            25  /*limit per page*/
        );
        $pagination->setTemplate('SettingToolBundle:Widget:pagination.html.twig');
        return $pagination;
    }


    /**
     * Lists all Vendor entities.
     *
     */

    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getAssetsConfig()->getId();
        $data = $_REQUEST;
        $entities = $this->getDoctrine()->getRepository('AssetsBundle:Receive')->findAll();
        $pagination = $this->paginate($entities);
        return $this->render('AssetsBundle:Receive:index.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
        ));
    }


    /**
     * Lists all PurchaseItem entities.
     *
     */

    public function itemReceiveAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $global = $this->getUser()->getGlobalOption();
        $config = $global->getAssetsConfig()->getId();
        $vendors = $this->getDoctrine()->getRepository(AccountVendor::class)->findBy(array('globalOption'=>$global));
        $entities = $em->getRepository('AssetsBundle:PurchaseItem')->findWithItemReceive($config,$data);
        $pagination = $this->paginate($entities);
        $selected = explode(',', $request->cookies->get('barcodes', ''));
        return $this->render('AssetsBundle:Receive:openitem.html.twig', array(
            'config' => $config,
            'vendors' => $vendors,
            'selected' => $selected,
            'entities' => $pagination,
            'searchForm' => $data,
        ));
    }

    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $vendor = $_REQUEST['vendor'];
        $data = explode( ',', $request->cookies->get( 'barcodes' ) );
        if ( is_null( $data ) ) {
            return $this->redirect( $this->generateUrl( 'assets_itemreceive_item' ) );
        }
        $accountVendor = $this->getDoctrine()->getRepository(AccountVendor::class)->find($vendor);
        if($accountVendor){
            $entity = new Receive();
            $config = $this->getUser()->getGlobalOption()->getAssetsConfig();
            $entity->setConfig($config);
            $entity->setVendor($accountVendor);
            $entity->setCreatedBy($this->getUser());
            $entity->setProcessType('Local');
            $entity->setUpdated($entity->getCreated());
            $entity->setReceiveDate($entity->getCreated());
            $em->persist($entity);
            $em->flush();
            foreach ($data as $key => $value ){
                $stock = $em->getRepository(PurchaseItem::class)->find($value);
                if(!empty($stock)){
                    $this->getDoctrine()->getRepository(ReceiveItem::class)->insertReceiveItem($entity,$stock);
                }
            }
            $this->getDoctrine()->getRepository(Receive::class)->updateTotalAmount($entity);
            return $this->redirect($this->generateUrl('assets_itemreceive_edit', array('id' => $entity->getId())));
        }else{
            return $this->redirect($this->generateUrl('assets_itemreceive_item'));
        }
    }


    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $option = $this->getUser()->getGlobalOption();
        $config = $this->getUser()->getGlobalOption()->getAssetsConfig()->getId();
        $entity = $em->getRepository('AssetsBundle:Receive')->findOneBy(array('config' => $config , 'id' => $id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        unset($_COOKIE['barcodes']);
        setcookie('barcodes', '', time() - 3600, '/');
        $editForm = $this->createEditForm($entity);
        return $this->render("AssetsBundle:Receive:new.html.twig",array(
            'entity' => $entity,
            'id' => 'purchase',
            'form' => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Invoice entity.wq
     *
     * @param Purchase $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Receive $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new ReceiveType($globalOption), $entity, array(
            'action' => $this->generateUrl('assets_itemreceive_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'id' => 'purchase',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    public function returnResultData(Receive $invoice,$msg=''){

        $invoiceParticulars =  $this->renderView("AssetsBundle:Receive:item.html.twig",array(
            'entity' => $invoice,
        ));
        $data = array(
            'invoiceParticulars' => $invoiceParticulars ,
            'msg' => $msg ,
            'success' => 'success'
        );

        return $data;

    }


    public function invoiceParticularDeleteAction(Purchase $invoice, PurchaseItem $particular){

        $em = $this->getDoctrine()->getManager();
        if (!$particular) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }
        $em->remove($particular);
        $em->flush();
        $invoice = $this->getDoctrine()->getRepository('AssetsBundle:PurchaseItem')->updatePurchaseTotalPrice($invoice);
        $result = $this->returnResultData($invoice);
        return new Response(json_encode($result));

    }

    /**
     * @Secure(roles="ROLE_ASSESTS,ROLE_DOMAIN")
     */
    public function inlineUpdateAction(Request $request, ReceiveItem $item)
    {
        $em = $this->getDoctrine()->getManager();
        $quantity = $request->request->get('quantity');
        $item->setQuantity($quantity);
        $item->setSubTotal($quantity * $item->getPrice());
        $em->flush();
        $this->getDoctrine()->getRepository(Receive::class)->updateTotalAmount($item->getReceive());
        $result = $this->returnResultData($item->getReceive());
        return new Response(json_encode($result));

    }

    /**
     * @Secure(roles="ROLE_ASSESTS,ROLE_DOMAIN")
     */
    public function invoiceVatUpdateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $vatPercent = (float)$request->request->get('vat');
        $invoice = $request->request->get('purchase');
        $entity = $em->getRepository('AssetsBundle:Receive')->find($invoice);
        $subTotal = $entity->getSubTotal();
        $vat = (($subTotal * $vatPercent)/100);
        $total = ($subTotal  - $vat);
        if($total > $vat ){
            $entity->setVatPercent($vatPercent);
            $entity->setVat(round($vat));
            $entity->setNetTotal(round($total));
        }else{
            $entity->setNetTotal($entity->getSubTotal());
        }
        $em->persist($entity);
        $em->flush();
        $result = $this->returnResultData($entity);
        return new Response(json_encode($result));

    }

    public function updateAction(Request $request, Receive $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountVendor entity.');
        }
        $form = $this->createEditForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setProcess('Complete');
            $entity->upload();
            $em->flush();
            return $this->redirect($this->generateUrl('assets_itemreceive_show',array('id'=>$entity->getId())));
        }
        return $this->render("AssetsBundle:Receive:new.html.twig",array(
            'entity' => $entity,
            'id' => 'purchase',
            'form' => $form->createView(),
        ));
    }


    /**
     * Finds and displays a Vendor entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $config = $this->getUser()->getGlobalOption()->getAssetsConfig();
        $entity = $em->getRepository('AssetsBundle:Receive')->findOneBy(array('config' => $config , 'id' => $id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Purchase entity.');
        }
        return $this->render('AssetsBundle:Receive:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    public function approvedAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getAssetsConfig();

        /* @var $purchase Purchase */

        $purchase = $em->getRepository('AssetsBundle:Receive')->findOneBy(array('config' => $config , 'id' => $id));
        if (!empty($purchase) and empty($purchase->getApprovedBy())) {

            $em = $this->getDoctrine()->getManager();
          //  $purchase->setProcess('Approved');
         //   $purchase->setApprovedBy($this->getUser());
            $accountConfig = $this->getUser()->getGlobalOption()->getAccountingConfig()->isAccountClose();
            if($accountConfig == 1){
                $datetime = new \DateTime("yesterday 30:30:30");
                $purchase->setCreated($datetime);
                $purchase->setUpdated($datetime);
            }
            $em->flush();
            $accountPurchase = $em->getRepository('AccountingBundle:AccountPurchase')->insertAssetsAccountPurchase($purchase);
            $em->getRepository('AccountingBundle:Transaction')->itemDistributionTransaction($purchase,$accountPurchase);
            $this->getDoctrine()->getRepository('AssetsBundle:ReceiveItem')->insertProductSerialNo($purchase);
            $this->getDoctrine()->getRepository('AssetsBundle:StockItem')->getPurchaseInsertQnt($purchase);
            $this->getDoctrine()->getRepository('AssetsBundle:Product')->insertReceiveItem($purchase);
            exit;
            $this->getDoctrine()->getRepository('AssetsBundle:Item')->getPurchaseUpdateQnt($purchase);
            return new Response('success');

        } else {

            return new Response('failed');
        }

    }

    /**
     * Deletes a Vendor entity.
     *
     */
    public function deleteAction($id)
    {

        $config = $this->getUser()->getGlobalOption();
        $entity = $this->getDoctrine()->getRepository('AssetsBundle:Receive')->findOneBy(array('globalOption' => $config , 'id' => $id));

        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendor entity.');
        }
        $em->remove($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('purchase'));
    }



     /**
     * Deletes a Vendor entity.
     *
     */
    public function itemDeleteAction($id)
    {
        $entity = $this->getDoctrine()->getRepository('AssetsBundle:ReceiveItem')->find($id);
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendor entity.');
        }
        $em->remove($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('purchase'));
    }



    public function reverseAction($id)
    {


    }


    /**
     * Status a Page entity.
     *
     */
    public function statusAction(Request $request, $id)
    {

    }

    public function autoSearchAction(Request $request)
    {

    }

    public function searchVendorNameAction($vendor)
    {

    }

}
