<?php

namespace Syndicate\Bundle\ComponentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Syndicate\Bundle\ComponentBundle\Entity\Tutorial;
use Syndicate\Bundle\ComponentBundle\Form\TutorialType;

/**
 * Tutorial controller.
 *
 */
class TutorialController extends Controller
{

    /**
     * Lists all Tutorial entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SyndicateComponentBundle:Tutorial')->findAll();

        return $this->render('SyndicateComponentBundle:Tutorial:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Tutorial entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Tutorial();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('tutorial-author_show', array('id' => $entity->getId())));
        }

        return $this->render('SyndicateComponentBundle:Tutorial:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Tutorial entity.
     *
     * @param Tutorial $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Tutorial $entity)
    {
        $form = $this->createForm(new TutorialType(), $entity, array(
            'action' => $this->generateUrl('tutorial-author_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Tutorial entity.
     *
     */
    public function newAction()
    {
        $entity = new Tutorial();
        $form   = $this->createCreateForm($entity);

        return $this->render('SyndicateComponentBundle:Tutorial:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Tutorial entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SyndicateComponentBundle:Tutorial')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tutorial entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SyndicateComponentBundle:Tutorial:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Tutorial entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SyndicateComponentBundle:Tutorial')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tutorial entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SyndicateComponentBundle:Tutorial:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Tutorial entity.
    *
    * @param Tutorial $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Tutorial $entity)
    {
        $form = $this->createForm(new TutorialType(), $entity, array(
            'action' => $this->generateUrl('tutorial-author_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Tutorial entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SyndicateComponentBundle:Tutorial')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tutorial entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('tutorial-author_edit', array('id' => $id)));
        }

        return $this->render('SyndicateComponentBundle:Tutorial:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Tutorial entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SyndicateComponentBundle:Tutorial')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Tutorial entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('tutorial-author'));
    }

    /**
     * Creates a form to delete a Tutorial entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('tutorial-author_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
