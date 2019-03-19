<?php

namespace Appstore\Bundle\AccountingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Appstore\Bundle\AccountingBundle\Entity\CashReconciliation;
use Appstore\Bundle\AccountingBundle\Form\CashReconciliationType;
use Symfony\Component\HttpFoundation\Response;

/**
 * CashReconciliationController controller.
 *
 */
class CashReconciliationController extends Controller
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
        $entities = $em->getRepository('AccountingBundle:CashReconciliation')->findWithSearch( $this->getUser()->getGlobalOption(),$data);
        $pagination = $this->paginate($entities);
        return $this->render('AccountingBundle:CashReconciliation:index.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
        ));
    }
    /**
     * Creates a new CashReconciliation entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new CashReconciliation();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
	    if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $global = $this->getUser()->getGlobalOption();
            $entity->setGlobalOption($global);
            if(!empty($this->getUser()->getProfile()->getBranches())){
                $entity->setBranches($this->getUser()->getProfile()->getBranches());
            }
            if($global->getMainApp()->getSlug() == 'miss'){
                $entity->setCompanyName($entity->getMedicineVendor()->getCompanyName());
                $entity->setMedicineVendor($entity->getMedicineVendor());
            }elseif($global->getMainApp()->getSlug() == 'inventory'){
                $entity->setCompanyName($entity->getVendor()->getCompanyName());
                $entity->setVendor($entity->getVendor());
            }else{
                $entity->setCompanyName($entity->getAccountVendor()->getCompanyName());
                $entity->setAccountVendor($entity->getAccountVendor());
            }
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('account_purchasecommission'));
        }

        return $this->render('AccountingBundle:CashReconciliation:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a CashReconciliation entity.
     *
     * @param CashReconciliation $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CashReconciliation $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new CashReconciliationType($globalOption), $entity, array(
            'action' => $this->generateUrl('account_purchasecommission_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form purchase',
                'novalidate' => 'novalidate',
            )
        ));
      return $form;
    }

	/**
	 * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_JOURNAL,ROLE_DOMAIN")
	 */

    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        $global = $this->getUser()->getGlobalOption();
        $date = new \DateTime('now');
        $created = $date->format('Y-m-d');
        $entity = new CashReconciliation();
        $exist = $this->getDoctrine()->getRepository('AccountingBundle:CashReconciliation')->findOneBy(['globalOption'=>$global,'created'=>$date]);
        if(!$exist){
            $entity->setGlobalOption($global);
            $entity->setCreated($date);
            $em->persist($entity);
            $em->flush();
            $this->getDoctrine()->getRepository('AccountingBundle:CashReconciliation')->initialUpdate($this->getUser(),$entity);
        }else{
            $this->getDoctrine()->getRepository('AccountingBundle:CashReconciliation')->initialUpdate($this->getUser(),$exist);
        }
        exit;

    }



    /**
     * Displays a form to edit an existing CashReconciliation entity.
     *
     */
    public function inlineUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AccountingBundle:CashReconciliation')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CashReconciliation entity.');
        }
        $entity->setSales($data['value']);
        $profit = (($entity->getSales() * $entity->getProfitPercentage())/100);
        $entity->setProfit($profit);
        $purchase = ($entity->getSales() - $profit);
        $entity->setPurchase($purchase);
        $em->flush();
        exit;
    }

    public function approveAction(CashReconciliation $entity)
    {
        if (!empty($entity) and $entity->getProcess() != 'approved') {
            $em = $this->getDoctrine()->getManager();
            $entity->setProcess('approved');
            $entity->setApprovedBy($this->getUser());
            $em->flush();
            $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->insertpurchasecommission($entity);
            $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->purchasecommissionTransaction($entity);
            return new Response('success');
        } else {
            return new Response('failed');
        }
        exit;
    }

	/**
	 * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_JOURNAL,ROLE_DOMAIN")
	 */

    public function deleteAction(CashReconciliation $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CashReconciliation entity.');
        }
        $em->remove($entity);
        $em->flush();
        return new Response('success');
        exit;
    }


    /**
     * Deletes a CashReconciliation entity.
     *
     */
    public function approveDeleteAction(CashReconciliation $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CashReconciliation entity.');
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

	public function journalReverseAction(CashReconciliation $entity){

		$em = $this->getDoctrine()->getManager();
		$this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->accountReverse($entity);
		$entity->setProcess(null);
		$entity->setApprovedBy(null);
		$entity->setAmount(0);
		$em->flush();
		return $this->redirect($this->generateUrl('account_purchasecommission'));

	}

}
