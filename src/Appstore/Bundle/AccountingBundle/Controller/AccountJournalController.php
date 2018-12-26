<?php

namespace Appstore\Bundle\AccountingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Appstore\Bundle\AccountingBundle\Entity\AccountJournal;
use Appstore\Bundle\AccountingBundle\Form\AccountJournalType;
use Symfony\Component\HttpFoundation\Response;

/**
 * AccountJournal controller.
 *
 */
class AccountJournalController extends Controller
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
     * Lists all AccountJournal entities.
     *
     */

	/**
	 * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_JOURNAL,ROLE_DOMAIN")
	 */

	public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $entities = $em->getRepository('AccountingBundle:AccountJournal')->findWithSearch( $this->getUser(),$data);
        $pagination = $this->paginate($entities);
        $accountHead = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->findBy(array('isParent'=>1),array('name'=>'ASC'));
        $debit = $this->getDoctrine()->getRepository('AccountingBundle:AccountJournal')->accountCashOverview($this->getUser(),'Debit',$data);
        $credit = $this->getDoctrine()->getRepository('AccountingBundle:AccountJournal')->accountCashOverview($this->getUser(),'Credit',$data);
        $overview = array('debit' => $debit,'credit' => $credit);
        return $this->render('AccountingBundle:AccountJournal:index.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
            'overview' => $overview,
            'accountHead' => $accountHead,
        ));
    }
    /**
     * Creates a new AccountJournal entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new AccountJournal();
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
            return $this->redirect($this->generateUrl('account_journal'));
        }

        return $this->render('AccountingBundle:AccountJournal:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a AccountJournal entity.
     *
     * @param AccountJournal $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(AccountJournal $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new AccountJournalType($globalOption), $entity, array(
            'action' => $this->generateUrl('account_journal_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form purchase',
                'novalidate' => 'novalidate',
            )
        ));
      return $form;
    }

    /**
     * Displays a form to create a new AccountJournal entity.
     *
     */


	/**
	 * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_JOURNAL,ROLE_DOMAIN")
	 */

    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new AccountJournal();
        $form   = $this->createCreateForm($entity);
        $banks = $em->getRepository('SettingToolBundle:Bank')->findAll();
        return $this->render('AccountingBundle:AccountJournal:new.html.twig', array(
            'entity' => $entity,
            'banks' => $banks,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a AccountJournal entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AccountingBundle:AccountJournal')->find($id);
         if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountJournal entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('AccountingBundle:AccountJournal:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing AccountJournal entity.
     *
     */

	/**
	 * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_JOURNAL,ROLE_DOMAIN")
	 */

    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AccountingBundle:AccountJournal')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountJournal entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('AccountingBundle:AccountJournal:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a AccountJournal entity.
    *
    * @param AccountJournal $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(AccountJournal $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new AccountJournalType($globalOption), $entity, array(
            'action' => $this->generateUrl('account_journal_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form purchase',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing AccountJournal entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AccountingBundle:AccountJournal')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountJournal entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('account_journal_edit', array('id' => $id)));
        }

        return $this->render('AccountingBundle:AccountJournal:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }


    /**
     * Creates a form to delete a AccountJournal entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('account_journal_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    public function paymentAction(Request $request)
    {
        $data = $request->request->all();
        $entity = new AccountJournal();
        $em = $this->getDoctrine()->getManager();
        $entity->setGlobalOption($this->getUser()->getGlobalOption());
        $parent = $em->getRepository('AccountingBundle:AccountJournal')->find($data['parent']);
        $entity->setToUser($parent->getCreatedBy());
        $entity->setAmount($data['amount']);
        $entity->setRemark($data['remark']);
        $em->persist($entity);
        $em->flush();
        exit;
    }

    /**
     * Displays a form to edit an existing AccountJournal entity.
     *
     */
    public function inlineUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AccountingBundle:AccountJournal')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountJournal entity.');
        }
        $entity->setAmount($data['value']);
        $em->flush();
        exit;
    }

    public function approveAction(AccountJournal $entity)
    {
        if (!empty($entity) and $entity->getProcess() != 'approved') {
            $em = $this->getDoctrine()->getManager();
            $entity->setProcess('approved');
            $entity->setApprovedBy($this->getUser());
            $em->flush();
            if(!empty($entity->getTransactionMethod())){
	            $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->insertAccountCash($entity,'Journal');
            }
            $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->insertAccountJournalTransaction($entity);
            return new Response('success');
        } else {
            return new Response('failed');
        }
        exit;
    }

    /**
     * Deletes a AccountJournal entity.
     *
     */


	/**
	 * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_JOURNAL,ROLE_DOMAIN")
	 */

    public function deleteAction(AccountJournal $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountJournal entity.');
        }
        $em->remove($entity);
        $em->flush();
        return new Response('success');
        exit;
    }


    /**
     * Deletes a AccountJournal entity.
     *
     */
    public function approveDeleteAction(AccountJournal $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountJournal entity.');
        }
        $em->remove($entity);
        $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->approvedDeleteRecord($entity,'Journal');
        $em->flush();
        return new Response('success');
        exit;
    }


	/**
	 * @Secure(roles="ROLE_DOMAIN_ACCOUNT_REVERSE,ROLE_DOMAIN")
	 */

	public function journalReverseAction(AccountJournal $entity){

		$em = $this->getDoctrine()->getManager();
		$this->getDoctrine()->getRepository('AccountingBundle:AccountJournal')->accountReverse($entity);
		$entity->setProcess(null);
		$entity->setApprovedBy(null);
		$entity->setAmount(0);
		$em->flush();
		return $this->redirect($this->generateUrl('account_journal'));

	}

}
