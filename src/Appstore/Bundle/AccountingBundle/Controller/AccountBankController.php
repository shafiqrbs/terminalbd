<?php

namespace Appstore\Bundle\AccountingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\AccountingBundle\Entity\AccountBank;
use Appstore\Bundle\AccountingBundle\Form\AccountBankType;
use Symfony\Component\HttpFoundation\Response;

/**
 * AccountBank controller.
 *
 */
class AccountBankController extends Controller
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
     * Lists all AccountBank entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('AccountingBundle:AccountBank')->findWithSearch($globalOption,$data);
        $pagination = $this->paginate($entities);
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountBank')->entityOverview($globalOption,$data);
        $accountHead = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->getChildrenAccountHead($parent =array(1));
        return $this->render('AccountingBundle:AccountBank:index.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
            'accountHead' => $accountHead,
            'overview' => $overview,
        ));
    }
    /**
     * Creates a new AccountBank entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new AccountBank();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $toIncrease = $entity->getAccountHead()->getToIncrease();

            $em = $this->getDoctrine()->getManager();
            $entity->setGlobalOption($this->getUser()->getGlobalOption());
            $entity->setToIncrease($toIncrease);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('account_bank'));
        }

        return $this->render('AccountingBundle:AccountBank:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a AccountBank entity.
     *
     * @param AccountBank $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(AccountBank $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new AccountBankType($globalOption), $entity, array(
            'action' => $this->generateUrl('account_bank_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form purchase',
                'novalidate' => 'novalidate',
            )
        ));
      return $form;
    }

    /**
     * Displays a form to create a new AccountBank entity.
     *
     */
    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new AccountBank();
        $form   = $this->createCreateForm($entity);
        $banks = $em->getRepository('SettingToolBundle:Bank')->findAll();
        return $this->render('AccountingBundle:AccountBank:new.html.twig', array(
            'entity' => $entity,
            'banks' => $banks,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a AccountBank entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AccountingBundle:AccountBank')->find($id);
       if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountBank entity.');
        }

        return $this->render('AccountingBundle:AccountBank:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * Displays a form to edit an existing AccountBank entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AccountingBundle:AccountBank')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountBank entity.');
        }

        $editForm = $this->createEditForm($entity);
        return $this->render('AccountingBundle:AccountBank:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a AccountBank entity.
    *
    * @param AccountBank $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(AccountBank $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new AccountBankType($globalOption), $entity, array(
            'action' => $this->generateUrl('account_bank_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form purchase',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing AccountBank entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AccountingBundle:AccountBank')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountBank entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('account_bank_edit', array('id' => $id)));
        }

        return $this->render('AccountingBundle:AccountBank:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
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
        $entity = $em->getRepository('AccountingBundle:AccountBank')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountPurchase entity.');
        }
        $entity->setAmount($data['value']);
        $em->flush();
        exit;
    }

    public function approveAction(AccountBank $entity)
    {
        if (!empty($entity)) {
            $em = $this->getDoctrine()->getManager();
            $entity->setProcess('approved');
            $entity->setApprovedBy($this->getUser());
            $em->flush();
            $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->insertAccountBankTransaction($entity);
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
    public function deleteAction(AccountBank $entity)
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

}
