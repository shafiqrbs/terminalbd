<?php

namespace Appstore\Bundle\AccountingBundle\Controller;

use Appstore\Bundle\AccountingBundle\Entity\ExpenseCategory;
use Appstore\Bundle\AccountingBundle\Form\ExpenseCategoryType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Validator\Constraints\Null;

/**
 * ExpenseCategory controller.
 *
 */
class ExpenseCategoryController extends Controller
{

    /**
     * Lists all ExpenseCategory entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $option = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('AccountingBundle:ExpenseCategory')->findBy(array('globalOption' => $option),array( 'parent'=>'asc' , 'name' =>'asc' ));
        $pagination = $this->paginate($entities);
        return $this->render('AccountingBundle:ExpenseCategory:index.html.twig', array(
            'entities' => $pagination,
        ));

    }

    public function paginate($entities)
    {
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            50  /*limit per page*/
        );
        return $pagination;
    }


    /**
     * Creates a new ExpenseCategory entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new ExpenseCategory();
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createCreateForm($entity,$globalOption);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setGlobalOption($globalOption);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('expensecategory_new', array('id' => $entity->getId())));
        }

        return $this->render('AccountingBundle:ExpenseCategory:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a ExpenseCategory entity.
     *
     * @param ExpenseCategory $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ExpenseCategory $entity, $globalOption)
    {

        $em = $this->getDoctrine()->getRepository('AccountingBundle:ExpenseCategory');
        $form = $this->createForm(new ExpenseCategoryType($em,$globalOption), $entity, array(
            'action' => $this->generateUrl('expensecategory_create', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new ExpenseCategory entity.
     *
     */
    public function newAction()
    {
        $entity = new ExpenseCategory();
        $globalOption = $this->getUser()->getGlobalOption();
        $form   = $this->createCreateForm($entity,$globalOption);

        return $this->render('AccountingBundle:ExpenseCategory:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ExpenseCategory entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AccountingBundle:ExpenseCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ExpenseCategory entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('AccountingBundle:ExpenseCategory:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ExpenseCategory entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AccountingBundle:ExpenseCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ExpenseCategory entity.');
        }
        $globalOption = $this->getUser()->getGlobalOption();
        $editForm = $this->createEditForm($entity,$globalOption);

        return $this->render('AccountingBundle:ExpenseCategory:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a ExpenseCategory entity.
     *
     * @param ExpenseCategory $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(ExpenseCategory $entity,$globalOption)
    {
        $em = $this->getDoctrine()->getRepository('AccountingBundle:ExpenseCategory');

        $form = $this->createForm(new ExpenseCategoryType($em,$globalOption), $entity, array(
            'action' => $this->generateUrl('expensecategory_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));


        return $form;
    }
    /**
     * Edits an existing ExpenseCategory entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AccountingBundle:ExpenseCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ExpenseCategory entity.');
        }

        $globalOption = $this->getUser()->getGlobalOption();
        $editForm = $this->createEditForm($entity,$globalOption);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('expensecategory_edit', array('id' => $id)));
        }

        return $this->render('AccountingBundle:ExpenseCategory:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a ExpenseCategory entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AccountingBundle:ExpenseCategory')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ExpenseCategory entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('expensecategory'));
    }

    /**
     * Creates a form to delete a ExpenseCategory entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('expensecategory_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
            ;
    }

    /**
     * Lists all ExpenseCategory entities.
     *
     */
    public function sortingAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('AccountingBundle:ExpenseCategory')->findBy(array('level'=> 1 ),array('sorting'=>'asc'));
        $pagination = $this->paginate($entities);
        return $this->render('AccountingBundle:ExpenseCategory:sorting.html.twig', array(
            'entities' => $pagination,
        ));

    }

    public function sortedAction(Request $request){

        $data = $request->request->get('menuItem');
        $this->getDoctrine()->getRepository('AccountingBundle:ExpenseCategory')->setFeatureOrdering($data);
        exit;

    }

    public function featureAction()
    {
        $data = $_REQUEST;
        $this->getDoctrine()->getRepository('AccountingBundle:ExpenseCategory')->setExpenseCategoryFeature($data);
        return $this->redirect($this->generateUrl('expensecategory_sorting'));

    }
}
