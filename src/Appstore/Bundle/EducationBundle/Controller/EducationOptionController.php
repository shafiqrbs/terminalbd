<?php

namespace Appstore\Bundle\EducationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\EducationBundle\Entity\EducationOption;
use Appstore\Bundle\EducationBundle\Form\EducationOptionType;

/**
 * EducationOption controller.
 *
 */
class EducationOptionController extends Controller
{

    /**
     * Lists all EducationOption entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('EducationBundle:EducationOption')->findAll();

        return $this->render('EducationBundle:EducationOption:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new EducationOption entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new EducationOption();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('education_show', array('id' => $entity->getId())));
        }

        return $this->render('EducationBundle:EducationOption:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a EducationOption entity.
     *
     * @param EducationOption $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(EducationOption $entity)
    {
        $form = $this->createForm(new EducationOptionType(), $entity, array(
            'action' => $this->generateUrl('education_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new EducationOption entity.
     *
     */
    public function newAction()
    {
        $entity = new EducationOption();
        $form   = $this->createCreateForm($entity);

        return $this->render('EducationBundle:EducationOption:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a EducationOption entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EducationBundle:EducationOption')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EducationOption entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EducationBundle:EducationOption:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing EducationOption entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EducationBundle:EducationOption')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EducationOption entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EducationBundle:EducationOption:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a EducationOption entity.
    *
    * @param EducationOption $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(EducationOption $entity)
    {
        $form = $this->createForm(new EducationOptionType(), $entity, array(
            'action' => $this->generateUrl('education_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing EducationOption entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EducationBundle:EducationOption')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EducationOption entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('education_edit', array('id' => $id)));
        }

        return $this->render('EducationBundle:EducationOption:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a EducationOption entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('EducationBundle:EducationOption')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find EducationOption entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('education'));
    }

    /**
     * Creates a form to delete a EducationOption entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('education_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
