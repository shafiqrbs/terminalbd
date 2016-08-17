<?php

namespace Syndicate\Bundle\ComponentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Syndicate\Bundle\ComponentBundle\Entity\Vendor;
use Syndicate\Bundle\ComponentBundle\Form\VendorType;

/**
 * Vendor controller.
 *
 */
class VendorController extends Controller
{


    /**
     * Lists all Education entities.
     *
     */

    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $keyword = $request -> query->get('search');

        $entities = $em->getRepository('SyndicateComponentBundle:Vendor')->findBy(array(),array('name'=>'asc'));

        $pagination = $this->paginate($entities);

        return $this->render('SyndicateComponentBundle:Vendor:index.html.twig', array(
            'pagination' => $pagination
        ));

    }

    public function deleteListAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SyndicateComponentBundle:Vendor')->findBy(array(),array('name'=>'asc'));

        $pagination = $this->paginate($entities);

        return $this->render('SyndicateComponentBundle:Vendor:delete.html.twig', array(
            'pagination' => $pagination
        ));

    }



    public function paginate($entities)
    {

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            30  /*limit per page*/
        );
        return $pagination;
    }

    /**
     * Creates a new Vendor entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Vendor();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->upload();
            $entity->setUser($this->get('security.context')->getToken()->getUser());
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('vendor_show', array('id' => $entity->getId())));
        }

        return $this->render('SyndicateComponentBundle:Vendor:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Vendor entity.
     *
     * @param Vendor $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Vendor $entity)
    {
        $em = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $form = $this->createForm(new VendorType($em), $entity, array(
            'action' => $this->generateUrl('vendor_create', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new Vendor entity.
     *
     */
    public function newAction()
    {
        $entity = new Vendor();
        $form   = $this->createCreateForm($entity);

        return $this->render('SyndicateComponentBundle:Vendor:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Vendor entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SyndicateComponentBundle:Vendor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendor entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SyndicateComponentBundle:Vendor:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Vendor entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SyndicateComponentBundle:Vendor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendor entity.');
        }

        $editForm = $this->createEditForm($entity);

        return $this->render('SyndicateComponentBundle:Vendor:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Education entity.
     *
     */
    public function modifyAction()
    {

        $user = $this->get('security.context')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $this->getDoctrine()->getRepository('SyndicateComponentBundle:Vendor')->insertVendor($user);
        $entity = $em->getRepository('SyndicateComponentBundle:Vendor')->findOneBy(array('user'=>$user));
        $editForm = $this->createEditForm($entity);

        return $this->render('SyndicateComponentBundle:Vendor:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView()

        ));
    }

    /**
    * Creates a form to edit a Vendor entity.
    *
    * @param Vendor $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Vendor $entity)
    {
        $em = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $form = $this->createForm(new VendorType($em), $entity, array(
            'action' => $this->generateUrl('vendor_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));

        return $form;
    }
    /**
     * Edits an existing Vendor entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SyndicateComponentBundle:Vendor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendor entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entity->upload();
            $em->flush();

            return $this->redirect($this->generateUrl('vendor_edit', array('id' => $id)));
        }

        return $this->render('SyndicateComponentBundle:Vendor:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Vendor entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SyndicateComponentBundle:Vendor')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Vendor entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('vendor'));
    }

    /**
     * Creates a form to delete a Vendor entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('vendor_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }


}
