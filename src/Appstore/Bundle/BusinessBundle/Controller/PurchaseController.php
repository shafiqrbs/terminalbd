<?php

namespace Appstore\Bundle\BusinessBundle\Controller;

use Appstore\Bundle\BusinessBundle\Entity\BusinessParticular;
use Appstore\Bundle\BusinessBundle\Entity\BusinessPurchase;
use Appstore\Bundle\BusinessBundle\Entity\BusinessPurchaseItem;
use Appstore\Bundle\BusinessBundle\Form\PurchaseType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Vendor controller.
 *
 */
class PurchaseController extends Controller
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

        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entities = $this->getDoctrine()->getRepository('BusinessBundle:BusinessPurchase')->findBy(array('businessConfig' => $config,'mode'=>'medicine'),array('created'=>'DESC'));
        $pagination = $this->paginate($entities);

        return $this->render('BusinessBundle:Purchase:index.html.twig', array(
            'entities' => $pagination,
        ));
    }
    /**
     * Creates a new Vendor entity.
     *
     */
    public function createAction(Request $request)
    {
       
    }


    public function newAction()
    {

        $em = $this->getDoctrine()->getManager();
        $entity = new BusinessPurchase();
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entity->setBusinessConfig($config);
        $entity->setCreatedBy($this->getUser());
        $receiveDate = new \DateTime('now');
        $entity->setReceiveDate($receiveDate);
        $transactionMethod = $em->getRepository('SettingToolBundle:TransactionMethod')->find(1);
        $entity->setTransactionMethod($transactionMethod);
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('business_purchase_edit', array('id' => $entity->getId())));

    }


    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entity = $em->getRepository('BusinessBundle:BusinessPurchase')->findOneBy(array('businessConfig' => $config , 'id' => $id));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $editForm = $this->createEditForm($entity);
        $particulars = $em->getRepository('BusinessBundle:BusinessParticular')->getFindWithParticular($config,$type = array('consumable','stock'));

        return $this->render('BusinessBundle:Purchase:new.html.twig', array(
            'entity' => $entity,
            'id' => 'purchase',
            'particulars' => $particulars,
            'form' => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Invoice entity.wq
     *
     * @param Invoice $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(BusinessPurchase $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new PurchaseType($globalOption), $entity, array(
            'action' => $this->generateUrl('business_purchase_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'id' => 'purchase',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    public function returnResultData(BusinessPurchase $invoice,$msg=''){

        $invoiceParticulars = $this->getDoctrine()->getRepository('BusinessBundle:BusinessPurchaseItem')->getPurchaseItems($invoice);
        $subTotal = $invoice->getSubTotal() > 0 ? $invoice->getSubTotal() : 0;
        $netTotal = $invoice->getNetTotal() > 0 ? $invoice->getNetTotal() : 0;
        $due = $invoice->getDue() > 0 ? $invoice->getDue() : 0;
        $discount = $invoice->getDiscount() > 0 ? $invoice->getDiscount() : 0;
        $data = array(
            'subTotal' => $subTotal,
            'netTotal' => $netTotal,
            'due' => $due,
            'discount' => $discount,
            'invoiceParticulars' => $invoiceParticulars ,
            'msg' => $msg ,
            'success' => 'success'
        );

        return $data;

    }

    public function particularSearchAction(BusinessParticular $particular)
    {
	    $unit = !empty($particular->getUnit() && !empty($particular->getUnit()->getName())) ? $particular->getUnit()->getName():'Unit';
	    return new Response(json_encode(array('particularId'=> $particular->getId() ,'price'=> $particular->getSalesPrice(), 'purchasePrice'=> $particular->getPurchasePrice(), 'quantity'=> 1 , 'unit'=> $unit)));
    }

    public function addParticularAction(Request $request, BusinessPurchase $invoice)
    {
        $em = $this->getDoctrine()->getManager();
        $particularId = $request->request->get('particularId');
        $quantity = $request->request->get('quantity');
        $price = $request->request->get('price');
        $invoiceItems = array('particularId' => $particularId , 'quantity' => $quantity,'price' => $price );
        $this->getDoctrine()->getRepository('BusinessBundle:BusinessPurchaseItem')->insertPurchaseItems($invoice, $invoiceItems);
        $invoice = $this->getDoctrine()->getRepository('BusinessBundle:BusinessPurchase')->updatePurchaseTotalPrice($invoice);
        $result = $this->returnResultData($invoice);
        return new Response(json_encode($result));
        exit;
    }

    public function invoiceParticularDeleteAction(BusinessPurchase $invoice, BusinessPurchaseItem $particular){

        $em = $this->getDoctrine()->getManager();
        if (!$particular) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }
        $em->remove($particular);
        $em->flush();
        $invoice = $this->getDoctrine()->getRepository('BusinessBundle:BusinessPurchase')->updatePurchaseTotalPrice($invoice);
        $result = $this->returnResultData($invoice);
        return new Response(json_encode($result));
        exit;
    }

    /**
     * @Secure(roles="ROLE_BUSINESS")
     */
    public function invoiceDiscountUpdateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $discountType = $request->request->get('discountType');
        $discountCal = $request->request->get('discount');
        $invoice = $request->request->get('invoice');
        $entity = $em->getRepository('BusinessBundle:BusinessPurchase')->find($invoice);
        $subTotal = $entity->getSubTotal();
        if($discountType == 'flat'){
            $total = ($subTotal  - $discountCal);
            $discount = $discountCal;
        }else{
            $discount = ($subTotal*$discountCal)/100;
            $total = ($subTotal  - $discount);
        }
        $vat = 0;
        if($total > $discount ){
            $entity->setDiscountType($discountType);
            $entity->setDiscountCalculation($discountCal);
            $em->flush();
        }
        $entity = $this->getDoctrine()->getRepository('BusinessBundle:BusinessPurchase')->updatePurchaseTotalPrice($entity);
        $msg = 'Discount successfully';
        $result = $this->returnResultData($entity,$msg);
        return new Response(json_encode($result));
        exit;
    }

    public function updateAction(Request $request, BusinessPurchase $entity)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            $data = $request->request->all();
            $entity->setProcess('Done');
            $entity->setDue($entity->getNetTotal() - $entity->getPayment());
            $em->flush();
            return $this->redirect($this->generateUrl('business_purchase_show', array('id' => $entity->getId())));
        }
        $particulars = $em->getRepository('BusinessBundle:BusinessParticular')->getFindWithParticular($entity->getBusinessConfig(),$type = array('consumable','stock'));
        return $this->render('BusinessBundle:Purchase:new.html.twig', array(
            'entity' => $entity,
            'particulars' => $particulars,
            'form' => $editForm->createView(),
        ));
    }


    /**
     * Finds and displays a Vendor entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BusinessBundle:BusinessPurchase')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendor entity.');
        }
        return $this->render('BusinessBundle:Purchase:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    public function approvedAction(BusinessPurchase $purchase)
    {
        $em = $this->getDoctrine()->getManager();
        if (!empty($purchase)) {
            $em = $this->getDoctrine()->getManager();
            $purchase->setProcess('Approved');
            $purchase->setApprovedBy($this->getUser());
            $em->flush();
            $this->getDoctrine()->getRepository('BusinessBundle:BusinessPurchaseItem')->updatePurchaseItemPrice($purchase);
            $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticular')->getPurchaseUpdateQnt($purchase);
            $accountPurchase = $em->getRepository('AccountingBundle:AccountPurchase')->insertBusinessAccountPurchase($purchase);
            $em->getRepository('AccountingBundle:Transaction')->purchaseGlobalTransaction($accountPurchase);
            return new Response('success');
        } else {
            return new Response('failed');
        }
        exit;
    }

    /**
     * Deletes a Vendor entity.
     *
     */
    public function deleteAction(BusinessPurchase $entity)
    {

        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendor entity.');
        }
        $em->remove($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('business_purchase'));
    }


    /**
     * Status a Page entity.
     *
     */
    public function statusAction(Request $request, $id)
    {


        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BusinessBundle:HmsVendor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find District entity.');
        }

        $status = $entity->isStatus();
        if($status == 1){
            $entity->setStatus(false);
        } else{
            $entity->setStatus(true);
        }
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',"Status has been changed successfully"
        );
        return $this->redirect($this->generateUrl('business_vendor'));
    }

    public function autoSearchAction(Request $request)
    {
        $item = $_REQUEST['q'];
        if ($item) {
            $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
            $item = $this->getDoctrine()->getRepository('BusinessBundle:HmsVendor')->searchAutoComplete($item,$inventory);
        }
        return new JsonResponse($item);
    }

    public function searchVendorNameAction($vendor)
    {
        return new JsonResponse(array(
            'id'=>$vendor,
            'text'=>$vendor
        ));
    }

}
