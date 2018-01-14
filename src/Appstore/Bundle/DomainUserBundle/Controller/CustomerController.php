<?php

namespace Appstore\Bundle\DomainUserBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Appstore\Bundle\DomainUserBundle\Form\CustomerType;

/**
 * Customer controller.
 *
 */
class CustomerController extends Controller
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
     * Lists all Customer entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('DomainUserBundle:Customer')->findWithSearch($globalOption,$data);
        $pagination = $this->paginate($entities);
        return $this->render('DomainUserBundle:Customer:index.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
        ));
    }
    /**
     * Creates a new Customer entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Customer();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('customer_show', array('id' => $entity->getId())));
        }

        return $this->render('DomainUserBundle:Customer:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Customer entity.
     *
     * @param Customer $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Customer $entity)
    {
        $form = $this->createForm(new CustomerType(), $entity, array(
            'action' => $this->generateUrl('customer_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Customer entity.
     *
     */
    public function newAction()
    {
        $entity = new Customer();
        $form   = $this->createCreateForm($entity);

        return $this->render('DomainUserBundle:Customer:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Customer entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DomainUserBundle:Customer')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Customer entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('DomainUserBundle:Customer:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Customer entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DomainUserBundle:Customer')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Customer entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('DomainUserBundle:Customer:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Customer entity.
    *
    * @param Customer $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Customer $entity)
    {

        $form = $this->createForm(new CustomerType(), $entity, array(
            'action' => $this->generateUrl('customer_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Customer entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DomainUserBundle:Customer')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Customer entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('customer_edit', array('id' => $id)));
        }

        return $this->render('DomainUserBundle:Customer:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Customer entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('DomainUserBundle:Customer')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Customer entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('customer'));
    }

    /**
     * Creates a form to delete a Customer entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('customer_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    public function autoSearchAction(Request $request)
    {
        $item = $_REQUEST['q'];
        if ($item) {
            $go = $this->getUser()->getGlobalOption();
            $type= 'pos';
            $item = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->searchAutoComplete($go,$item,$type);
        }
        return new JsonResponse($item);
    }



    public function searchCustomerNameAction($customer)
    {
        return new JsonResponse(array(
            'id'=> $customer,
            'text' => $customer
        ));
    }

    public function autoCodeSearchAction(Request $request)
    {

        /* $item = $_REQUEST['q'];
        if ($item) {
            $go = $this->getUser()->getGlobalOption();
            $item = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->searchAutoCompleteCode($go,$item);
        }
        return new JsonResponse($item);*/

        $q = $_REQUEST['term'];
        $option = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->searchAutoCompleteCode($option,$q);
        $items = array();
        foreach ($entities as $entity):
            $items[]=array('id' => $entity['customer'],'value' => $entity['text']);
        endforeach;
        return new JsonResponse($items);

    }



    public function searchCodeAction($customer)
    {
        return new JsonResponse(array(
            'id'=> $customer,
            'text' => $customer
        ));
    }


    public function autoLocationSearchAction(Request $request)
    {
        $item = $_REQUEST['q'];
        if ($item) {
            $item = $this->getDoctrine()->getRepository('SettingLocationBundle:Location')->searchAutoComplete($item);
        }
        return new JsonResponse($item);

    }

    public function searchLocationNameAction($location)
    {
        return new JsonResponse(array(
            'id'=> $location,
            'text' => $location
        ));
    }

    public function searchAutoCompleteNameAction()
    {
        $q = $_REQUEST['q'];
        $option = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->searchAutoCompleteName($option,$q);
        $items = array();
        foreach ($entities as $entity):
            $items[]=array('id' => $entity['id'],'value' => $entity['id']);
        endforeach;
        return new JsonResponse($entities);

    }

    public function searchAutoCompleteMobileAction()
    {
        $q = $_REQUEST['term'];
        $option = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->searchAutoComplete($option,$q);
        $items = array();
        foreach ($entities as $entity):
            $items[]=array('id' => $entity['customer'],'value' => $entity['id']);
        endforeach;
        return new JsonResponse($items);

    }

}
