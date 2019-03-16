<?php

namespace Appstore\Bundle\AccountingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Appstore\Bundle\AccountingBundle\Entity\AccountSalesAdjustment;
use Appstore\Bundle\AccountingBundle\Form\AccountSalesAdjustmentType;
use Symfony\Component\HttpFoundation\Response;

/**
 * AccountSalesAdjustment controller.
 *
 */
class AccountSalesAdjustmentController extends Controller
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
	 * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_JOURNAL,ROLE_DOMAIN")
	 */

	public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $entities = $em->getRepository('AccountingBundle:AccountSalesAdjustment')->findWithSearch( $this->getUser()->getGlobalOption(),$data);
        $pagination = $this->paginate($entities);
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSalesAdjustment')->accountCashOverview($this->getUser(),'Debit',$data);
        return $this->render('AccountingBundle:AccountSalesAdjustment:index.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
            'overview' => $overview,
        ));
    }
    /**
     * Creates a new AccountSalesAdjustment entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new AccountSalesAdjustment();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
	    if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setGlobalOption($this->getUser()->getGlobalOption());
            if(!empty($this->getUser()->getProfile()->getBranches())){
                $entity->setBranches($this->getUser()->getProfile()->getBranches());
            }
            $profit = (($entity->getSales() * $entity->getProfitPercentage())/100);
            $entity->setProfit($profit);
            $purchase = ($entity->getSales() - $profit);
            $entity->setPurchase($purchase);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('account_salesadjustment'));
        }

        return $this->render('AccountingBundle:AccountSalesAdjustment:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a AccountSalesAdjustment entity.
     *
     * @param AccountSalesAdjustment $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(AccountSalesAdjustment $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new AccountSalesAdjustmentType($globalOption), $entity, array(
            'action' => $this->generateUrl('account_salesadjustment_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form purchase',
                'novalidate' => 'novalidate',
            )
        ));
      return $form;
    }

    /**
     * Displays a form to create a new AccountSalesAdjustment entity.
     *
     */


	/**
	 * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_JOURNAL,ROLE_DOMAIN")
	 */

    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new AccountSalesAdjustment();
        $form   = $this->createCreateForm($entity);
        return $this->render('AccountingBundle:AccountSalesAdjustment:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }



    /**
     * Displays a form to edit an existing AccountSalesAdjustment entity.
     *
     */
    public function inlineUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AccountingBundle:AccountSalesAdjustment')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountSalesAdjustment entity.');
        }
        $entity->setSales($data['value']);
        $profit = (($entity->getSales() * $entity->getProfitPercentage())/100);
        $entity->setProfit($profit);
        $purchase = ($entity->getSales() - $profit);
        $entity->setPurchase($purchase);
        $em->flush();
        exit;
    }

    public function approveAction(AccountSalesAdjustment $entity)
    {
        if (!empty($entity) and $entity->getProcess() != 'approved') {
            $em = $this->getDoctrine()->getManager();
            $entity->setProcess('approved');
            $entity->setApprovedBy($this->getUser());
            $em->flush();
            $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->insertSalesAdjustment($entity);
            $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->salesAdjustmentTransaction($entity);
            return new Response('success');
        } else {
            return new Response('failed');
        }
        exit;
    }

	/**
	 * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_JOURNAL,ROLE_DOMAIN")
	 */

    public function deleteAction(AccountSalesAdjustment $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountSalesAdjustment entity.');
        }
        $em->remove($entity);
        $em->flush();
        return new Response('success');
        exit;
    }


    /**
     * Deletes a AccountSalesAdjustment entity.
     *
     */
    public function approveDeleteAction(AccountSalesAdjustment $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountSalesAdjustment entity.');
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

	public function journalReverseAction(AccountSalesAdjustment $entity){

		$em = $this->getDoctrine()->getManager();
		$this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->accountReverse($entity);
		$entity->setProcess(null);
		$entity->setApprovedBy(null);
		$entity->setAmount(0);
		$em->flush();
		return $this->redirect($this->generateUrl('account_salesadjustment'));

	}

}
