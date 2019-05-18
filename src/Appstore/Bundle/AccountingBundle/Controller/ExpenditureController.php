<?php

namespace Appstore\Bundle\AccountingBundle\Controller;

use JMS\SecurityExtraBundle\Annotation\Secure;
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
     //   $flatExpenseCategoryTree = $this->getDoctrine()->getRepository('AccountingBundle:ExpenseCategory')->getCategoryOptions( $user->getGlobalOption());
        $transactionMethods = $this->getDoctrine()->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status'=>1),array('name'=>'asc'));
	    $categories = $this->getDoctrine()->getRepository('AccountingBundle:ExpenseCategory')->findBy(array('globalOption'=> $user->getGlobalOption(), 'status'=>1),array('name'=>'asc'));
	    $heads = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->getExpenseAccountHead();
        $employees = $em->getRepository('UserBundle:User')->getEmployees($user->getGlobalOption());
        return $this->render('AccountingBundle:Expenditure:index.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
            'flatExpenseCategoryTree' => '',
            'transactionMethods' => $transactionMethods,
            'overview' => $overview,
            'entity' => $entity,
            'heads' => $heads,
            'employees'=> $employees,
            'categories' => $categories,
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
        $method = empty($entity->getTransactionMethod()) ? '' : $entity->getTransactionMethod()->getSlug();
        if (($form->isValid() && $method == 'cash') ||
            ($form->isValid() && $method == 'bank' && $entity->getAccountBank()) ||
            ($form->isValid() && $method == 'mobile' && $entity->getAccountMobileBank())
        ) {
            $lastBalance = $em->getRepository('AccountingBundle:Expenditure')->lastInsertExpenditure($entity);
            $em = $this->getDoctrine()->getManager();
            $entity->setGlobalOption( $this->getUser()->getGlobalOption());
            $entity->setBalance($lastBalance + $entity->getAmount());
            $entity->setAccountHead($entity->getExpenseCategory()->getAccountHead());
            if($this->getUser()->getProfile()->getBranches()){
                $entity->setBranches($this->getUser()->getProfile()->getBranches());
            }
            $entity->setUpdated($entity->getCreated());
            $entity->upload();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('account_expenditure'));
        }
        $this->get('session')->getFlashBag()->add(
            'notice',"May be you are missing to select bank or mobile account"
        );
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
        if (!empty($expenditure) and $expenditure->getProcess() != "approved" ) {
            $em = $this->getDoctrine()->getManager();
            $expenditure->setProcess('approved');
            $expenditure->setApprovedBy($this->getUser());
            $accountConfig = $this->getUser()->getGlobalOption()->getAccountingConfig()->isAccountClose();
            if($accountConfig == 1){
                $datetime = new \DateTime("yesterday 23:59:59");
                $expenditure->setCreated($datetime);
                $expenditure->setUpdated($datetime);
            }
            $em->flush();
            $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->insertExpenditureCash($expenditure);
            $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->insertExpenditureTransaction($expenditure);
            return new Response('success');
        } else {
            return new Response('failed');
        }

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

    }

    /**
     * @Secure(roles="ROLE_DOMAIN_ACCOUNT_REVERSE,ROLE_DOMAIN")
     */

    public function expenditureReverseAction(Expenditure $entity){

        $em = $this->getDoctrine()->getManager();
        $this->getDoctrine()->getRepository('AccountingBundle:Expenditure')->accountReverse($entity);
        $entity->setProcess(null);
        $entity->setApprovedBy(null);
        $entity->setAmount(0);
        $entity->setBalance(0);
        $em->flush();
        return $this->redirect($this->generateUrl('account_expenditure'));

    }

}
