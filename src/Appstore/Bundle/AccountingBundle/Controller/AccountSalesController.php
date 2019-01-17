<?php

namespace Appstore\Bundle\AccountingBundle\Controller;

use Appstore\Bundle\AccountingBundle\Form\AccountSalesInvoiceType;
use Appstore\Bundle\InventoryBundle\Entity\Sales;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\AccountingBundle\Entity\AccountSales;
use Appstore\Bundle\AccountingBundle\Form\AccountSalesType;
use Symfony\Component\HttpFoundation\Response;

/**
 * AccountSales controller.
 *
 */
class AccountSalesController extends Controller
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
     * Lists all AccountSales entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $entities = $em->getRepository('AccountingBundle:AccountSales')->findWithSearch($user,$data);
        $pagination = $this->paginate($entities);
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->receiveModeOverview($user,$data);
        $accountHead = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->getChildrenAccountHead($parent =array(20,29));
        $transactionMethods = $this->getDoctrine()->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status'=>1),array('name'=>'asc'));
        return $this->render('AccountingBundle:AccountSales:index.html.twig', array(
            'entities' => $pagination,
            'accountHead' => $accountHead,
            'transactionMethods' => $transactionMethods,
            'searchForm' => $data,
            'overview' => $overview,
        ));
    }

    /**
     * Lists all AccountSales entities.
     *
     */
    public function customerOutstandingAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->customerOutstanding($globalOption,$data);
        $pagination = $this->paginate($entities);
        return $this->render('AccountingBundle:AccountSales:customerOutstanding.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
        ));
    }


    /**
     * Lists all AccountSalesReturn entities.
     *
     */
    public function salesReturnAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $entities = $em->getRepository('AccountingBundle:AccountSalesReturn')->findWithSearch($this->getUser(),$data);
        $pagination = $this->paginate($entities);
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSalesReturn')->salesReturnOverview($this->getUser(),$data);
        return $this->render('AccountingBundle:AccountSales:salesReturn.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
            'overview' => $overview,
        ));
    }
    /**
     * Creates a new AccountSales entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new AccountSales();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setGlobalOption( $this->getUser()->getGlobalOption());
            if(!empty($this->getUser()->getProfile()->getBranches())){
                $entity->setBranches($this->getUser()->getProfile()->getBranches());
            }
	        if($entity->getProcessHead() == 'Outstanding'){
		        $entity->setTotalAmount(abs($entity->getAmount()));
		        $entity->setAmount(0);
		        $entity->setTransactionMethod(null);
	        }
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('account_sales'));
        }
        return $this->render('AccountingBundle:AccountSales:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a AccountSales entity.
     *
     * @param AccountSales $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(AccountSales $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new AccountSalesType($globalOption), $entity, array(
            'action' => $this->generateUrl('account_sales_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form purchase',
                'novalidate' => 'novalidate',
            )
        ));
      return $form;
    }

    /**
     * Displays a form to create a new AccountSales entity.
     *
     */
    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new AccountSales();
        $form   = $this->createCreateForm($entity);
        $banks = $em->getRepository('SettingToolBundle:Bank')->findAll();
        return $this->render('AccountingBundle:AccountSales:new.html.twig', array(
            'entity' => $entity,
            'banks' => $banks,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to create a new AccountSales entity.
     *
     */
    public function duePaymentAction(Sales $sales)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new AccountSales();

        $entity->setGlobalOption( $this->getUser()->getGlobalOption());
        $entity->setSales($sales);
        $entity->setCustomer($sales->getCustomer());
        $entity->setTransactionMethod($this->getDoctrine()->getRepository('SettingToolBundle:TransactionMethod')->find(1));
        $entity->setProcessHead('Due');
        if(!empty($this->getUser()->getProfile()->getBranches())){
            $entity->setBranches($this->getUser()->getProfile()->getBranches());
        }
        $em->persist($entity);
        $em->flush();
        $form   = $this->createEditForm($entity);
        return $this->render('AccountingBundle:AccountSales:invoice.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a AccountSales entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AccountingBundle:AccountSales')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountSales entity.');
        }
        return $this->render('AccountingBundle:AccountSales:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * Displays a form to edit an existing AccountSales entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AccountingBundle:AccountSales')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountSales entity.');
        }

        $editForm = $this->createEditForm($entity);
        return $this->render('AccountingBundle:AccountSales:invoice.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a AccountSales entity.
    *
    * @param AccountSales $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(AccountSales $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new AccountSalesInvoiceType($globalOption), $entity, array(
            'action' => $this->generateUrl('account_sales_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form purchase',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing AccountSales entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AccountingBundle:AccountSales')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountSales entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        if ($editForm->isValid()) {

            if ($entity->getSales()->getDue() < $entity->getAmount() ){
                $this->get('session')->getFlashBag()->add(
                    'notice',"Payment amount receive must be same or less as due amount"
                );
                return $this->redirect($this->generateUrl('account_sales_edit',array('id' => $entity->getId())));
            }
            $em->flush();
            return $this->redirect($this->generateUrl('account_sales'));
        }

        return $this->render('AccountingBundle:AccountSales:invoice.html.twig', array(
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
        $entity = $em->getRepository('AccountingBundle:AccountSales')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountPurchase entity.');
        }
        if($data['value'] > 0 ){
	        $entity->setAmount($data['value']);
        }
        $em->flush();
        exit;
    }

    public function approveAction(AccountSales $entity)
    {
	    if (!empty($entity) and $entity->getProcess() != 'approved') {
		    $em = $this->getDoctrine()->getManager();
		    $entity->setProcess('approved');
		    $entity->setApprovedBy($this->getUser());
		    if(in_array($entity->getProcessHead(),array( 'Due','Advance'))){
			    $method = $this->getDoctrine()->getRepository('SettingToolBundle:TransactionMethod')->find(1);
			    $entity->setTransactionMethod($method);
		    }
		    $em->flush();
		    if(!empty($entity->getSales()) and $entity->getProcessHead() == 'Due'){
		    	$this->getDoctrine()->getRepository('InventoryBundle:Sales')->updateSalesPaymentReceive($entity);
		    }
		    $em->getRepository('AccountingBundle:AccountSales')->updateCustomerBalance($entity);

		    if($entity->getProcessHead() == 'Outstanding'){
			    $this->getDoctrine()->getRepository('AccountingBundle:Transaction')-> insertCustomerOutstandingTransaction($entity);
		    }elseif($entity->getProcessHead() == 'Discount'){
			    $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->insertCustomerDiscountTransaction($entity);
		    }elseif($entity->getAmount() > 0 ){
			    $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->insertAccountSalesTransaction($entity);
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
    public function deleteAction(AccountSales $entity)
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

    public function salesPrint(AccountSales $sales)
    {
    	$em = $this->getDoctrine()->getManager();
	    $template = $sales->getGlobalOption()->getSlug();
	    $result = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->customerOutstanding($sales->getGlobalOption(), $data = array('mobile'=> $sales->getCustomer()->getMobile()));
	    $balance = empty($result) ? 0 :$result[0]['customerBalance'];
	    $amountInWords = $this->get('settong.toolManageRepo')->intToWords($sales->getAmount());
	    return  $this->render("BusinessBundle:Print:{$template}.html.twig",
		    array(
			    'amountInWords' => $amountInWords,
			    'entity' => $sales,
			    'balance' => $balance,
			    'print' => 'print',
		    )
	    );
    }

    public function getCustomerLedgerAction()
    {

	    $customerId = $_REQUEST['customer'];
	    $globalOption = $this->getUser()->getGlobalOption();
	    $balance = 0;
	    $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $globalOption,'id'=> $customerId));
	    $customerMobile = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $globalOption,'mobile'=> $customerId));
	    if(!empty($customer)){
		    $result = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->customerOutstanding($globalOption, $data = array('mobile' => $customer->getMobile()));
		    $balance = empty($result) ? 0 : $result[0]['customerBalance'];
	    }elseif (!empty($customerMobile)){
		    $result = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->customerOutstanding($globalOption, $data = array('mobile' => $customerMobile->getMobile()));
		    $balance = empty($result) ? 0 : $result[0]['customerBalance'];
	    }
	    $taka = number_format($balance).' Taka';
	    return new Response($taka);
	    exit;

    }





}
