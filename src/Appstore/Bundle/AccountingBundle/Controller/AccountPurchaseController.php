<?php

namespace Appstore\Bundle\AccountingBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\AccountingBundle\Entity\AccountPurchase;
use Appstore\Bundle\AccountingBundle\Form\AccountPurchaseType;
use Symfony\Component\HttpFoundation\Response;

/**
 * AccountPurchase controller.
 *
 */
class AccountPurchaseController extends Controller
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
     * Lists all AccountPurchase entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->findWithSearch($globalOption,$data);
        $pagination = $this->paginate($entities);
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->accountPurchaseOverview($globalOption,$data);
        $accountHead = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->getChildrenAccountHead($parent =array(5));
        $transactionMethods = $this->getDoctrine()->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status'=>1),array('name'=>'asc'));
        return $this->render('AccountingBundle:AccountPurchase:index.html.twig', array(
            'entities' => $pagination,
            'overview' => $overview,
            'accountHead' => $accountHead,
            'transactionMethods' => $transactionMethods,
            'searchForm' => $data,
        ));
    }



    /**
     * Lists all AccountPurchase entities.
     *
     */

    /**
     * Lists all AccountSales entities.
     *
     */
    public function vendorOutstandingAction()
    {

        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->vendorInventoryOutstanding($globalOption,'inventory',$data);
        $pagination = $this->paginate($entities);
        return $this->render('AccountingBundle:AccountPurchase:purchaseOutstanding.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
        ));
    }

    public function purchaseReturnAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountPurchaseReturn')->findWithSearch($globalOption,$data);
        $pagination = $this->paginate($entities);
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountPurchaseReturn')->accountPurchaseOverview($globalOption,$data);
        return $this->render('AccountingBundle:AccountPurchase:purchaseReturn.html.twig', array(
            'entities' => $pagination,
            'overview' => $overview,
            'searchForm' => $data,
        ));
    }

    /**
     * Creates a new AccountPurchase entity.
     *
     */

    public function createAction(Request $request)
    {
        $entity = new AccountPurchase();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
	    $global = $this->getUser()->getGlobalOption();
	    $data = $request->request->all();
	    $company = $data['purchase']['companyName'];
	    $exitVendor = $this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->findOneBy(array('globalOption' => $global,'companyName' => $company));
		$head = $exitVendor->getProcessHead();
		if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setGlobalOption($global);
		    $entity->setProcessType('Payment');
            if($head == "medicine"){
	            $entity->setProcessHead($head);
	            $entity->setCompanyName($company);
	            $entity->setMedicineVendor($exitVendor->getMedicineVendor());
            }
            if($head == "inventory"){
	            $entity->setProcessHead($head);
	            $entity->setCompanyName($company);
	            $entity->setVendor($exitVendor->getVendor());
            }
            if($head == "dms"){
	            $entity->setProcessHead($head);
	            $entity->setCompanyName($company);
	            $entity->setDmsVendor($exitVendor->getDmsVendor());
            }
            if($head == "hms"){
	            $entity->setProcessHead($head);
	            $entity->setCompanyName($company);
	            $entity->setHmsVendor($exitVendor->getHmsVendor());
            }
            if($head == "restaurant"){
	            $entity->setProcessHead($head);
	            $entity->setCompanyName($company);
	            $entity->setRestaurantVendor($exitVendor->getRestaurantVendor());
            }
            if($head == "business" or $head == "hotel" ){
	            $entity->setProcessHead($head);
	            $entity->setCompanyName($company);
	            $entity->setAccountVendor($exitVendor->getAccountVendor());
            }
            if($entity->getPayment() < 0){
                $entity->setPurchaseAmount(abs($entity->getPayment()));
                $entity->setPayment(0);
	            $entity->setProcessType('Opening');
                $entity->setTransactionMethod(null);
            }
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('account_purchase'));
        }

        return $this->render('AccountingBundle:AccountPurchase:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a AccountPurchase entity.
     *
     * @param AccountPurchase $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(AccountPurchase $entity)
    {
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $form = $this->createForm(new AccountPurchaseType($inventory), $entity, array(
            'action' => $this->generateUrl('account_purchase_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form purchase',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new AccountPurchase entity.
     *
     */
    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new AccountPurchase();
        $form   = $this->createCreateForm($entity);
        $banks = $em->getRepository('SettingToolBundle:Bank')->findAll();
        return $this->render('AccountingBundle:AccountPurchase:new.html.twig', array(
            'entity'    => $entity,
            'banks'     => $banks,
            'form'      => $form->createView(),
        ));
    }

    /**
     * Finds and displays a AccountPurchase entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AccountingBundle:AccountPurchase')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountPurchase entity.');
        }
        return $this->render('AccountingBundle:AccountPurchase:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * Displays a form to edit an existing AccountPurchase entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AccountingBundle:AccountPurchase')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountPurchase entity.');
        }

        $editForm = $this->createEditForm($entity);

        return $this->render('AccountingBundle:AccountPurchase:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a AccountPurchase entity.
    *
    * @param AccountPurchase $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(AccountPurchase $entity)
    {
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $form = $this->createForm(new AccountPurchaseType($inventory), $entity, array(
            'action' => $this->generateUrl('account_purchase_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form purchase',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing AccountPurchase entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AccountingBundle:AccountPurchase')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountPurchase entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('account_purchase_edit', array('id' => $id)));
        }

        return $this->render('AccountingBundle:AccountPurchase:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),

        ));
    }

    /**
     * Displays a form to edit an existing Expenditure entity.
     *
     */
    public function inlineUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AccountingBundle:AccountPurchase')->find($data['pk']);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Account Purchase entity.');
        }
        if($data['value'] < 0){
            $entity->setPurchaseAmount(abs($data['value']));
            $entity->setPayment(0);
        }else{
            $entity->setPayment($data['value']);
        }
        //$currentBalance = $entity->getBalance() + $entity->getPayment();
        //$entity->setBalance($currentBalance - floatval($data['value']));
        $em->flush();

        exit;

    }

    public function approveAction(AccountPurchase $entity)
    {
        if (!empty($entity) and $entity->getProcess() != 'approved') {
            $em = $this->getDoctrine()->getManager();
            $entity->setProcess('approved');
            $entity->setApprovedBy($this->getUser());
	        if(in_array($entity->getProcessHead(),array( 'Due Payment','Advance'))){
		        $method = $this->getDoctrine()->getRepository('SettingToolBundle:TransactionMethod')->find(1);
		        $entity->setTransactionMethod($method);
	        }
            $em->flush();
            $accountPurchase = $em->getRepository('AccountingBundle:AccountPurchase')->updateVendorBalance($entity);
	        if($entity->getProcessType() == 'Outstanding'){
		        $this->getDoctrine()->getRepository('AccountingBundle:Transaction')-> insertVendorOpeningTransaction($entity);
	        }elseif($entity->getProcessType() == 'Discount'){
		        $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->insertVendorDiscountTransaction($entity);
	        }elseif($entity->getPayment() > 0 ){
		        $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->insertPurchaseCash($accountPurchase);
		        $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->insertPurchaseVendorTransaction($accountPurchase);
	        }

            return new Response('success');
        } else {
            return new Response('failed');
        }
        exit;
    }

    /**
     * Deletes a Expenditure entity.
     *
     */
    public function deleteAction(AccountPurchase $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountPurchase entity.');
        }
        $em->remove($entity);
        $em->flush();
        return new Response('success');
        exit;
    }

	public function autoSearchAction(Request $request)
	{
		$item = $_REQUEST['q'];
		if ($item) {
			$global = $this->getUser()->getGlobalOption();
			$item = $this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->searchAutoComplete($item,$global);
		}
		return new JsonResponse($item);
	}

	public function searchVendorNameAction($vendor)
	{
		return new JsonResponse(array(
			'id' => $vendor,
			'text' => $vendor
		));
	}

}
