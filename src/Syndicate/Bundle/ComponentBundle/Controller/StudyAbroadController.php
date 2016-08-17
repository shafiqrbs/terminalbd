<?php

namespace Syndicate\Bundle\ComponentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Syndicate\Bundle\ComponentBundle\Entity\StudyAbroad;
use Syndicate\Bundle\ComponentBundle\Form\StudyAbroadType;

/**
 * StudyAbroad controller.
 *
 */
class StudyAbroadController extends Controller
{


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
     * Lists all StudyAbroad entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SyndicateComponentBundle:StudyAbroad')->findAll();
        $pagination = $this->paginate($entities);
        return $this->render('SyndicateComponentBundle:StudyAbroad:index.html.twig', array(
            'pagination' => $pagination,
        ));
    }
    /**
     * Creates a new StudyAbroad entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new StudyAbroad();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $entity->upload();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('studyabroad_show', array('id' => $entity->getId())));
        }

        return $this->render('SyndicateComponentBundle:StudyAbroad:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a StudyAbroad entity.
     *
     * @param StudyAbroad $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(StudyAbroad $entity)
    {
        $em = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $syn = $this->getDoctrine()->getRepository('SettingToolBundle:Syndicate');
        $form = $this->createForm(new StudyAbroadType($em,$syn), $entity, array(
            'action' => $this->generateUrl('studyabroad_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));

        return $form;
    }

    /**
     * Displays a form to create a new StudyAbroad entity.
     *
     */
    public function newAction()
    {
        $entity = new StudyAbroad();
        $form   = $this->createCreateForm($entity);

        return $this->render('SyndicateComponentBundle:StudyAbroad:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a StudyAbroad entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SyndicateComponentBundle:StudyAbroad')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find StudyAbroad entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SyndicateComponentBundle:StudyAbroad:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing StudyAbroad entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SyndicateComponentBundle:StudyAbroad')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find StudyAbroad entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SyndicateComponentBundle:StudyAbroad:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a StudyAbroad entity.
    *
    * @param StudyAbroad $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(StudyAbroad $entity)
    {
        $em = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $syn = $this->getDoctrine()->getRepository('SettingToolBundle:Syndicate');
        $form = $this->createForm(new StudyAbroadType($em,$syn), $entity, array(
            'action' => $this->generateUrl('studyabroad_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing StudyAbroad entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SyndicateComponentBundle:StudyAbroad')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find StudyAbroad entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $entity->upload();
            $em->flush();

            return $this->redirect($this->generateUrl('studyabroad_edit', array('id' => $id)));
        }

        return $this->render('SyndicateComponentBundle:StudyAbroad:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a StudyAbroad entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SyndicateComponentBundle:StudyAbroad')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find StudyAbroad entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('studyabroad'));
    }

    /**
     * Creates a form to delete a StudyAbroad entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('studyabroad_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }


    /**
     * Displays a form to edit an existing Education entity.
     *
     */
    public function modifyAction()
    {

        $user = $this->get('security.context')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $this->getDoctrine()->getRepository('SyndicateComponentBundle:StudyAbroad')->insertVendor($user);
        $entity = $em->getRepository('SyndicateComponentBundle:StudyAbroad')->findOneBy(array('user'=>$user));
        $editForm = $this->createEditForm($entity);
        return $this->render('SyndicateComponentBundle:StudyAbroad:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView()

        ));

    }
}
