<?php

namespace Appstore\Bundle\AccountingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Appstore\Bundle\AccountingBundle\Entity\AccountBalanceTransfer;
use Appstore\Bundle\AccountingBundle\Form\AccountBalanceTransferType;
use Symfony\Component\HttpFoundation\Response;

/**
 * AccountBalanceTransfer controller.
 *
 */
class AccountBalanceTransferController extends Controller
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
     * Lists all AccountBalanceTransfer entities.
     *
     */

	/**
	 * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_JOURNAL,ROLE_DOMAIN")
	 */

	public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $entities = $em->getRepository('AccountingBundle:AccountBalanceTransfer')->findWithSearch( $this->getUser(),$data);
        $pagination = $this->paginate($entities);
        $accountHead = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->findBy(array('isParent'=>1),array('name'=>'ASC'));
        $debit = $this->getDoctrine()->getRepository('AccountingBundle:AccountBalanceTransfer')->accountCashOverview($this->getUser(),'Debit',$data);
        $credit = $this->getDoctrine()->getRepository('AccountingBundle:AccountBalanceTransfer')->accountCashOverview($this->getUser(),'Credit',$data);
        $overview = array('debit' => $debit,'credit' => $credit);
        return $this->render('AccountingBundle:AccountBalanceTransfer:index.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
            'overview' => $overview,
            'accountHead' => $accountHead,
        ));
    }
    /**
     * Creates a new AccountBalanceTransfer entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new AccountBalanceTransfer();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
	    if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setGlobalOption($this->getUser()->getGlobalOption());
            if(!empty($this->getUser()->getProfile()->getBranches())){
                $entity->setBranches($this->getUser()->getProfile()->getBranches());
            }
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('account_balancetransfer'));
        }

        return $this->render('AccountingBundle:AccountBalanceTransfer:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a AccountBalanceTransfer entity.
     *
     * @param AccountBalanceTransfer $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(AccountBalanceTransfer $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new AccountBalanceTransferType($globalOption), $entity, array(
            'action' => $this->generateUrl('account_balancetransfer_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form purchase',
                'novalidate' => 'novalidate',
            )
        ));
      return $form;
    }

    /**
     * Displays a form to create a new AccountBalanceTransfer entity.
     *
     */


	/**
	 * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_JOURNAL,ROLE_DOMAIN")
	 */

    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new AccountBalanceTransfer();
        $form   = $this->createCreateForm($entity);
        $banks = $em->getRepository('SettingToolBundle:Bank')->findAll();
        return $this->render('AccountingBundle:AccountBalanceTransfer:new.html.twig', array(
            'entity' => $entity,
            'banks' => $banks,
            'form'   => $form->createView(),
        ));
    }



    /**
     * Displays a form to edit an existing AccountBalanceTransfer entity.
     *
     */
    public function inlineUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AccountingBundle:AccountBalanceTransfer')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountBalanceTransfer entity.');
        }
        $entity->setAmount($data['value']);
        $em->flush();
        exit;
    }

    public function approveAction(AccountBalanceTransfer $entity)
    {
        if (!empty($entity) and $entity->getProcess() != 'approved') {
            $em = $this->getDoctrine()->getManager();
            $entity->setProcess('approved');
            $entity->setApprovedBy($this->getUser());
            $em->flush();
            $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->balanceTransferAccountCash($entity,'BalanceTransfer');
            $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->insertAccountBalanceTransferTransaction($entity);
            return new Response('success');
        } else {
            return new Response('failed');
        }
        exit;
    }

	/**
	 * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_JOURNAL,ROLE_DOMAIN")
	 */

    public function deleteAction(AccountBalanceTransfer $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountBalanceTransfer entity.');
        }
        $em->remove($entity);
        $em->flush();
        return new Response('success');
        exit;
    }


    /**
     * Deletes a AccountBalanceTransfer entity.
     *
     */
    public function approveDeleteAction(AccountBalanceTransfer $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountBalanceTransfer entity.');
        }
        $em->remove($entity);
        $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->approvedDeleteRecord($entity,'BalanceTransfer');
        $em->flush();
        return new Response('success');
        exit;
    }


	/**
	 * @Secure(roles="ROLE_DOMAIN_ACCOUNT_REVERSE,ROLE_DOMAIN")
	 */

	public function journalReverseAction(AccountBalanceTransfer $entity){

		$em = $this->getDoctrine()->getManager();
		$this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->accountReverse($entity);
		$entity->setProcess(null);
		$entity->setApprovedBy(null);
		$entity->setAmount(0);
		$em->flush();
		return $this->redirect($this->generateUrl('account_balancetransfer'));

	}

}