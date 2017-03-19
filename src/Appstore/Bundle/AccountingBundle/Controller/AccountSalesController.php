<?php

namespace Appstore\Bundle\AccountingBundle\Controller;

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
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->salesOverview($user,$data);
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
            $entity->setProcessHead('Account Sales');
            if(!empty($this->getUser()->getProfile()->getBranches())){
                $entity->setBranches($this->getUser()->getProfile()->getBranches());
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
        return $this->render('AccountingBundle:AccountSales:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
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
        $form = $this->createForm(new AccountSalesType($globalOption), $entity, array(
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
            $em->flush();

            return $this->redirect($this->generateUrl('account_sales_edit', array('id' => $id)));
        }

        return $this->render('AccountingBundle:AccountSales:edit.html.twig', array(
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
        $entity = $em->getRepository('AccountingBundle:AccountSales')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountPurchase entity.');
        }
        $lastBalance = $em->getRepository('AccountingBundle:AccountSales')->lastInsertSales($this->getUser()->getGlobalOption(),$entity);
        $entity->setAmount($data['value']);
        $entity->setBalance($lastBalance - floatval($data['value']));
        $em->flush();
        exit;
    }

    public function approveAction(AccountSales $entity)
    {
        if (!empty($entity)) {
            $em = $this->getDoctrine()->getManager();

            $data = array('mobile'=> $entity->getCustomer()->getMobile());
            $result = $em->getRepository('AccountingBundle:AccountSales')->salesOverview($this->getUser(),$data);
            $balance = $result['totalAmount'] - ($result['receiveAmount'] + $entity->getAmount());
            $entity->setBalance($balance);
            $entity->setProcess('approved');
            $entity->setApprovedBy($this->getUser());
            $em->flush();
            $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->insertAccountSalesTransaction($entity);
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

}
