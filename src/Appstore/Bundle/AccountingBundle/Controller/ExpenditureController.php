<?php

namespace Appstore\Bundle\AccountingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\AccountingBundle\Entity\Expenditure;
use Appstore\Bundle\AccountingBundle\Form\ExpenditureType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Expenditure controller.
 *
 */
class ExpenditureController extends Controller
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
     * Lists all Expenditure entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $entity = new Expenditure();
        $form = $this->createCreateForm($entity);
        $entities = $em->getRepository('AccountingBundle:Expenditure')->findWithSearch($user,$data);
        $pagination = $this->paginate($entities);
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:Expenditure')->expenditureOverview($user,$data);
        //$flatExpenseCategoryTree = $this->getDoctrine()->getRepository('AccountingBundle:ExpenseCategory')->getCategoryOptions();
        $transactionMethods = $this->getDoctrine()->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status'=>1),array('name'=>'asc'));
        return $this->render('AccountingBundle:Expenditure:index.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
            'flatExpenseCategoryTree' => '',
            'transactionMethods' => $transactionMethods,
            'overview' => $overview,
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }
    /**
     * Creates a new Expenditure entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Expenditure();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isValid()) {
            $lastBalance = $em->getRepository('AccountingBundle:Expenditure')->lastInsertExpenditure($entity);
            $em = $this->getDoctrine()->getManager();
            $entity->setGlobalOption( $this->getUser()->getGlobalOption());
            $entity->setBalance($lastBalance + $entity->getAmount());
            $entity->setAccountHead($entity->getExpenseCategory()->getAccountHead());
            if(!empty($this->getUser()->getProfile()->getBranches())){
                $entity->setBranches($this->getUser()->getProfile()->getBranches());
            }
            $entity->upload();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('account_expenditure'));
        }

        return $this->render('AccountingBundle:Expenditure:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Expenditure entity.
     *
     * @param Expenditure $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Expenditure $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $expenseCategory = $this->getDoctrine()->getRepository('AccountingBundle:ExpenseCategory');
        $form = $this->createForm(new ExpenditureType($globalOption,$expenseCategory), $entity, array(
            'action' => $this->generateUrl('account_expenditure_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form purchase',
                'novalidate' => 'novalidate',
            )
        ));
      return $form;
    }

    /**
     * Displays a form to create a new Expenditure entity.
     *
     */
    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new Expenditure();
        $form   = $this->createCreateForm($entity);
        $banks = $em->getRepository('SettingToolBundle:Bank')->findAll();
        return $this->render('AccountingBundle:Expenditure:new.html.twig', array(
            'entity' => $entity,
            'banks' => $banks,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Expenditure entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AccountingBundle:Expenditure')->find($id);
         if (!$entity) {
            throw $this->createNotFoundException('Unable to find Expenditure entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('AccountingBundle:Expenditure:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Expenditure entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AccountingBundle:Expenditure')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Expenditure entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('AccountingBundle:Expenditure:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Expenditure entity.
    *
    * @param Expenditure $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Expenditure $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $expenseCategory = $this->getDoctrine()->getRepository('AccountingBundle:ExpenseCategory');
        $form = $this->createForm(new ExpenditureType($globalOption,$expenseCategory), $entity, array(
            'action' => $this->generateUrl('account_expenditure_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form purchase',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing Expenditure entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AccountingBundle:Expenditure')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Expenditure entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            if($entity->upload() && !empty($entity->getFile())){
                $entity->removeUpload();
            }
            $entity->upload();
            $em->flush();
            return $this->redirect($this->generateUrl('account_expenditure_edit', array('id' => $id)));
        }

        return $this->render('AccountingBundle:Expenditure:new.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }


    /**
     * Creates a form to delete a Expenditure entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('account_expenditure_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    /**
     * Displays a form to edit an existing Expenditure entity.
     *
     */
    public function inlineUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $Expenditure = $em->getRepository('AccountingBundle:Expenditure')->find($data['pk']);
        if (!$Expenditure) {
            throw $this->createNotFoundException('Unable to find Expenditure entity.');
        }
        $Expenditure->setAmount($data['value']);
        $em->flush();
        exit;
    }

    public function approveAction(Expenditure $expenditure)
    {
        if (!empty($expenditure)) {
            $em = $this->getDoctrine()->getManager();
            $expenditure->setProcess('approved');
            $expenditure->setApprovedBy($this->getUser());
            $em->flush();
            $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->insertExpenditureCash($expenditure);
            $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->insertExpenditureTransaction($expenditure);
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
    public function deleteAction(Expenditure $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Expenditure entity.');
        }
        if($entity->upload() && !empty($entity->getFile())){
            $entity->removeUpload();
        }
        $em->remove($entity);
        $em->flush();
        return new Response('success');
        exit;
    }
}
