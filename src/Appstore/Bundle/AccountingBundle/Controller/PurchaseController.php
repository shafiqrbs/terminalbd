<?php

namespace Appstore\Bundle\AccountingBundle\Controller;

use Appstore\Bundle\AccountingBundle\Entity\AccountPurchase;
use Appstore\Bundle\AccountingBundle\Entity\BusinessParticular;
use Appstore\Bundle\AccountingBundle\Entity\BusinessPurchase;
use Appstore\Bundle\AccountingBundle\Entity\BusinessPurchaseItem;
use Appstore\Bundle\AccountingBundle\Entity\ExpenditureItem;
use Appstore\Bundle\AccountingBundle\Form\PurchaseType;
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
        $config = $this->getUser()->getGlobalOption();
	    $data = $_REQUEST;
        $data['processHead'] = 'Expense';
	    $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->findWithSearch($config,$data);
        $pagination = $this->paginate($entities);
        return $this->render('AccountingBundle:Purchase:index.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
        ));
    }

    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new AccountPurchase();
        $config = $this->getUser()->getGlobalOption();
        $entity->setGlobalOption($config);
        $entity->setCreatedBy($this->getUser());
        $entity->setProcessHead('Expense');
        $transactionMethod = $em->getRepository('SettingToolBundle:TransactionMethod')->find(1);
        $entity->setTransactionMethod($transactionMethod);
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('account_expense_purchase_edit', array('id' => $entity->getId())));
    }


    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption();
        $entity = $em->getRepository('AccountingBundle:AccountPurchase')->findOneBy(array('globalOption' => $config , 'id' => $id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $editForm = $this->createEditForm($entity);
        return $this->render("AccountingBundle:Purchase:new.html.twig",[
            'entity' => $entity,
            'id' => 'purchase',
            'form' => $editForm->createView(),
        ]);
    }

    /**
     * Creates a form to edit a Invoice entity.wq
     *
     * @param Invoice $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(AccountPurchase $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $emHead = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead');
        $form = $this->createForm(new PurchaseType($globalOption,$emHead), $entity, array(
            'action' => $this->generateUrl('account_expense_purchase_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'id' => 'purchase',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    public function returnResultData(AccountPurchase $invoice,$msg=''){

        $invoiceParticulars = $this->getDoctrine()->getRepository('AccountingBundle:ExpenditureItem')->getPurchaseItems($invoice);
        $subTotal = $invoice->getTotalAmount() > 0 ? $invoice->getTotalAmount() : 0;
        $payment = $invoice->getPayment() > 0 ? $invoice->getPayment() : 0;
        $data = array(
            'subTotal' => $subTotal,
            'payment' => $payment,
            'invoiceParticulars' => $invoiceParticulars ,
            'msg' => $msg ,
            'success' => 'success'
        );

        return $data;

    }


    public function addParticularAction(Request $request, AccountPurchase $invoice)
    {
        $em = $this->getDoctrine()->getManager();
        $particular = $request->request->get('particular');
        $quantity = $request->request->get('quantity');
        $price = $request->request->get('price');
        $invoiceItems = array('particular' => $particular , 'quantity' => $quantity,'price' => $price);
        $this->getDoctrine()->getRepository('AccountingBundle:ExpenditureItem')->insertPurchaseItems($invoice, $invoiceItems);
        $invoice = $this->getDoctrine()->getRepository('AccountingBundle:ExpenditureItem')->updatePurchaseTotalPrice($invoice);
        $result = $this->returnResultData($invoice);
        return new Response(json_encode($result));
        exit;

    }


    public function invoiceParticularDeleteAction(AccountPurchase $invoice, ExpenditureItem $particular){

        $em = $this->getDoctrine()->getManager();
        if (!$particular) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }
        $em->remove($particular);
        $em->flush();
        $invoice = $this->getDoctrine()->getRepository('AccountingBundle:ExpenditureItem')->updatePurchaseTotalPrice($invoice);
        $result = $this->returnResultData($invoice);
        return new Response(json_encode($result));
        exit;
    }

    /**
     * @Secure(roles="ROLE_BUSINESS")
     */
    public function invoiceDiscountUpdateAction(Request $request)
    {

    }

    public function updateAction(Request $request, AccountPurchase $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountVendor entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entity->setCompanyName($entity->getAccountVendor()->getCompanyName());
            $em->flush();
            return $this->redirect($this->generateUrl('account_expense_purchase_show',['id'=>$entity->getId()]));
        }

        if($entity->getProcess() == 'Approved'){
           $this->approvedAction($entity->getId());
        }
        return $this->render("AccountingBundle:Purchase:new.html.twig",[
            'entity' => $entity,
            'id' => 'purchase',
            'form' => $editForm->createView(),
        ]);
    }


    /**
     * Finds and displays a Vendor entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

	    $config = $this->getUser()->getGlobalOption();
	    $entity = $em->getRepository('AccountingBundle:AccountPurchase')->findOneBy(array('globalOption' => $config , 'id' => $id));
	    if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendor entity.');
        }
        return $this->render('AccountingBundle:Purchase:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    public function approvedAction($id)
    {
        $em = $this->getDoctrine()->getManager();
		$config = $this->getUser()->getGlobalOption();

		/* @var $purchase AccountPurchase */

		$purchase = $em->getRepository('AccountingBundle:AccountPurchase')->findOneBy(array('globalOption' => $config , 'id' => $id));
	    if (!empty($purchase) and empty($purchase->getApprovedBy())) {
            $em = $this->getDoctrine()->getManager();
            $purchase->setProcess('approved');
            $purchase->setApprovedBy($this->getUser());
            if($purchase->getPayment() === 0 ){
	            $purchase->setTransactionMethod(NULL);
            }
            $accountConfig = $this->getUser()->getGlobalOption()->getAccountingConfig()->isAccountClose();
            if($accountConfig == 1){
                $datetime = new \DateTime("yesterday 23:59:59");
                $purchase->setCreated($datetime);
                $purchase->setUpdated($datetime);
            }
		    $em->flush();
            $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->insertPurchaseExpenditureCash($purchase);
            $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->insertPurchaseExpenditureTransaction($purchase);
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
    public function deleteAction($id)
    {

    	$config = $this->getUser()->getGlobalOption();
	    $entity = $this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->findOneBy(array('globalOption' => $config , 'id' => $id));

        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendor entity.');
        }
        $em->remove($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('account_expense_purchase'));
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
		$purchase = $this->getDoctrine()->getRepository('AccountingBundle:BusinessPurchase')->findOneBy(array('businessConfig' => $config , 'id' => $id));

		set_time_limit(0);
		ignore_user_abort(true);

		$em = $this->getDoctrine()->getManager();
		if($purchase->getAsInvestment() == 1 ) {
			$this->getDoctrine()->getRepository('AccountingBundle:AccountJournal')->removeApprovedBusinessPurchaseJournal($purchase);
		}
		$this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->accountBusinessPurchaseReverse($purchase);
		$purchase->setIsReversed(true);
		$purchase->setProcess('created');
		$purchase->setApprovedBy(NULL);
		$em->flush();
		$this->getDoctrine()->getRepository('AccountingBundle:BusinessParticular')->getPurchaseUpdateQnt($purchase);
		$template = $this->get('twig')->render('AccountingBundle:Purchase:purchaseReverse.html.twig', array(
			'entity' => $purchase,
			'config' => $purchase->getBusinessConfig(),
		));
		$this->getDoctrine()->getRepository('AccountingBundle:BusinessReverse')->purchaseReverse($purchase, $template);
		return $this->redirect($this->generateUrl('account_expense_purchase_edit',array('id' => $purchase->getId())));
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