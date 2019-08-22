<?php

namespace Appstore\Bundle\TallyBundle\Controller;

use Appstore\Bundle\TallyBundle\Entity\Purchase;
use Appstore\Bundle\TallyBundle\Entity\PurchaseItem;
use Appstore\Bundle\TallyBundle\Form\PurchaseType;
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
        $config = $this->getUser()->getGlobalOption()->getTallyConfig()->getId();
        $data = $_REQUEST;
        $entities = $this->getDoctrine()->getRepository('TallyBundle:Purchase')->findWithSearch($config,$data);
        $pagination = $this->paginate($entities);
        return $this->render('TallyBundle:Purchase:index.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
        ));
    }

    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new Purchase();
        $config = $this->getUser()->getGlobalOption()->getTallyConfig();
        $entity->setConfig($config);
        $entity->setCreatedBy($this->getUser());
        $entity->setProcessType('Local');
        $entity->setUpdated($entity->getCreated());
        $entity->setReceiveDate($entity->getCreated());
        $transactionMethod = $em->getRepository('SettingToolBundle:TransactionMethod')->find(1);
        $entity->setTransactionMethod($transactionMethod);
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('tally_purchase_edit', array('id' => $entity->getId())));
    }


    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getTallyConfig()->getId();
        $entity = $em->getRepository('TallyBundle:Purchase')->findOneBy(array('config' => $config , 'id' => $id));
        $products = $em->getRepository('TallyBundle:Item')->findAll(array('config' => $config));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $editForm = $this->createEditForm($entity);
        return $this->render("TallyBundle:Purchase:new.html.twig",array(
            'entity' => $entity,
            'products' => $products,
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
    private function createEditForm(Purchase $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new PurchaseType($globalOption), $entity, array(
            'action' => $this->generateUrl('tally_purchase_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'id' => 'purchase',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    public function returnResultData(Purchase $invoice,$msg=''){

        $invoiceParticulars = $this->getDoctrine()->getRepository('TallyBundle:PurchaseItem')->getPurchaseItems($invoice);
        $subTotal = $invoice->getSubTotal() > 0 ? $invoice->getSubTotal() : 0;
        $tti = $invoice->getTotalTaxIncidence() > 0 ? $invoice->getTotalTaxIncidence() : 0;
        $rebate = $invoice->getRebate() > 0 ? $invoice->getRebate() : 0;
        $netTotal = $invoice->getNetTotal() > 0 ? $invoice->getNetTotal() : 0;
        $payment = $invoice->getPayment() > 0 ? $invoice->getPayment() : 0;
        $discount = $invoice->getDiscount() > 0 ? $invoice->getDiscount() : 0;
        $data = array(
            'subTotal'  => $subTotal,
            'tti'       => $tti,
            'rebate'    => $rebate,
            'netTotal'  => $netTotal,
            'payment'   => $payment,
            'discount'  => $discount,
            'due'       => ($netTotal-$payment),
            'invoiceParticulars' => $invoiceParticulars ,
            'msg' => $msg ,
            'success' => 'success'
        );

        return $data;

    }


    public function addParticularAction(Request $request, $invoice)
    {

        $purchase = $this->getDoctrine()->getRepository('TallyBundle:Purchase')->find($invoice);
        $em = $this->getDoctrine()->getManager();
        $productItem = $request->request->get('productItem');
        $particular = $request->request->get('name');
        $quantity = $request->request->get('quantity');
        $price = $request->request->get('price');
        $invoiceItems = array('productItem' => $productItem , 'name' => $particular , 'quantity' => $quantity,'price' => $price);
        $this->getDoctrine()->getRepository('TallyBundle:PurchaseItem')->insertPurchaseItems($purchase, $invoiceItems);
        $invoice = $this->getDoctrine()->getRepository('TallyBundle:PurchaseItem')->updatePurchaseTotalPrice($purchase);
        $result = $this->returnResultData($invoice);
        return new Response(json_encode($result));
        exit;

    }


    public function invoiceParticularDeleteAction(Purchase $invoice, PurchaseItem $particular){

        $em = $this->getDoctrine()->getManager();
        if (!$particular) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }
        $em->remove($particular);
        $em->flush();
        $invoice = $this->getDoctrine()->getRepository('TallyBundle:PurchaseItem')->updatePurchaseTotalPrice($invoice);
        $result = $this->returnResultData($invoice);
        return new Response(json_encode($result));

    }

    /**
     * @Secure(roles="ROLE_TALLY,ROLE_DOMAIN")
     */
    public function invoiceDiscountUpdateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $discount = (float)$request->request->get('discount');
        $invoice = $request->request->get('purchase');
        $entity = $em->getRepository('TallyBundle:Purchase')->find($invoice);
        $subTotal = $entity->getSubTotal();
        $total = ($subTotal  - $discount);
        $vat = 0;
        if($total > $discount ){
            $entity->setDiscount(round($discount));
            $entity->setNetTotal(round($total + $entity->getTotalTaxIncidence()));
        }else{
            $entity->setDiscount(0);
            $entity->setNetTotal($entity->getSubTotal() + $entity->getTotalTaxIncidence());
        }
        $em->flush();
        $result = $this->returnResultData($entity);
        return new Response(json_encode($result));

    }

    public function updateAction(Request $request, Purchase $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountVendor entity.');
        }

        $form = $this->createEditForm($entity);
        $form->handleRequest($request);

        $data = $request->request->all();

        $method = empty($entity->getTransactionMethod()) ? '' : $entity->getTransactionMethod()->getSlug();
        $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['companyMobile']);
        if($mobile and $data['companyName'] ) {
            $exist = $this->getDoctrine()->getRepository('AccountingBundle:AccountVendor')->newVendorCreate($globalOption, $mobile, $data);
            if ($exist){
                $entity->setAccountVendor($exist);
            }
        }
        if (($form->isValid() && $method == 'cash') ||
            ($form->isValid() && $method == 'bank' && $entity->getAccountBank()) ||
            ($form->isValid() && $method == 'mobile' && $entity->getAccountMobileBank())
        ) {
            $entity->setUpdated($entity->getCreated());
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('tally_purchase_show',['id' => $entity->getId()]));
        }
        $this->get('session')->getFlashBag()->add(
            'notice',"May be you are missing to select bank or mobile account"
        );
        if($entity->getProcess() == 'Approved'){
            $this->approvedAction($entity->getId());
        }
        $products = $em->getRepository('TallyBundle:Item')->findAll(array('globalOption' => $entity->getGlobalOption()));
        return $this->render("TallyBundle:Purchase:new.html.twig",array(
            'entity' => $entity,
            'id' => 'purchase',
            'products' => $products,
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

        $config = $this->getUser()->getGlobalOption()->getTallyConfig();
        $entity = $em->getRepository('TallyBundle:Purchase')->findOneBy(array('config' => $config , 'id' => $id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Purchase entity.');
        }
        return $this->render('TallyBundle:Purchase:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    public function approvedAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getTallyConfig();

        /* @var $purchase Purchase */

        $purchase = $em->getRepository('TallyBundle:Purchase')->findOneBy(array('config' => $config , 'id' => $id));
        if (!empty($purchase) and empty($purchase->getApprovedBy())) {

            $em = $this->getDoctrine()->getManager();
            $purchase->setProcess('Approved');
            $purchase->setApprovedBy($this->getUser());
            if($purchase->getPayment() === 0 ){
                $purchase->setTransactionMethod(NULL);
            }
            $accountConfig = $this->getUser()->getGlobalOption()->getAccountingConfig()->isAccountClose();
            if($accountConfig == 1){
                $datetime = new \DateTime("yesterday 30:30:30");
                $purchase->setCreated($datetime);
                $purchase->setUpdated($datetime);
            }
            $em->flush();
            $accountPurchase = $em->getRepository('AccountingBundle:AccountPurchase')->insertTallyAccountPurchase($purchase);
            $em->getRepository('AccountingBundle:Transaction')->purchaseGlobalTransaction($accountPurchase);
            $this->getDoctrine()->getRepository('TallyBundle:StockItem')->getPurchaseInsertQnt($purchase);
            $this->getDoctrine()->getRepository('TallyBundle:Item')->getPurchaseUpdateQnt($purchase);
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
        $entity = $this->getDoctrine()->getRepository('TallyBundle:Purchase')->findOneBy(array('globalOption' => $config , 'id' => $id));

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

        /*
         * Item Remove Total quantity
         * Stock Details
         * Purchase Item
         * Purchase Vendor Item
         * Purchase
         * Account Purchase
         * Account Journal
         * Transaction
         * Delete Journal & Account Purchase
         */
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $purchase = $this->getDoctrine()->getRepository('TallyBundle:BusinessPurchase')->findOneBy(array('businessConfig' => $config , 'id' => $id));

        set_time_limit(0);
        ignore_user_abort(true);

        $em = $this->getDoctrine()->getManager();
        if($purchase->getAsInvestment() == 1 ) {
            $this->getDoctrine()->getRepository('TallyBundle:AccountJournal')->removeApprovedBusinessPurchaseJournal($purchase);
        }
        $this->getDoctrine()->getRepository('TallyBundle:Purchase')->accountBusinessPurchaseReverse($purchase);
        $purchase->setIsReversed(true);
        $purchase->setProcess('created');
        $purchase->setApprovedBy(NULL);
        $em->flush();
        //$this->getDoctrine()->getRepository('TallyBundle:BusinessParticular')->getPurchaseUpdateQnt($purchase);
        $template = $this->get('twig')->render('TallyBundle:Purchase:purchaseReverse.html.twig', array(
            'entity' => $purchase,
            'config' => $purchase->getBusinessConfig(),
        ));
        //$this->getDoctrine()->getRepository('TallyBundle:BusinessReverse')->purchaseReverse($purchase, $template);
        return $this->redirect($this->generateUrl('tally_purchase_edit',array('id' => $purchase->getId())));
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
