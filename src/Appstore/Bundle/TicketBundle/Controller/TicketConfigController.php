<?php

namespace Appstore\Bundle\TicketBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\TicketBundle\Entity\TicketConfig;
use Appstore\Bundle\TicketBundle\Form\TicketConfigType;

/**
 * TicketConfig controller.
 *
 */
class TicketConfigController extends Controller
{

    /**
     * Lists all TicketConfig entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('TicketBundle:TicketConfig')->findAll();

        return $this->render('TicketBundle:TicketConfig:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new TicketConfig entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new TicketConfig();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('ticketconfig_show', array('id' => $entity->getId())));
        }

        return $this->render('TicketBundle:TicketConfig:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a TicketConfig entity.
     *
     * @param TicketConfig $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(TicketConfig $entity)
    {
        $form = $this->createForm(new TicketConfigType(), $entity, array(
            'action' => $this->generateUrl('ticketconfig_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new TicketConfig entity.
     *
     */
    public function newAction()
    {
        $entity = new TicketConfig();
        $form   = $this->createCreateForm($entity);

        return $this->render('TicketBundle:TicketConfig:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a TicketConfig entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TicketBundle:TicketConfig')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TicketConfig entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('TicketBundle:TicketConfig:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing TicketConfig entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TicketBundle:TicketConfig')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TicketConfig entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('TicketBundle:TicketConfig:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a TicketConfig entity.
    *
    * @param TicketConfig $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(TicketConfig $entity)
    {
        $form = $this->createForm(new TicketConfigType(), $entity, array(
            'action' => $this->generateUrl('ticketconfig_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing TicketConfig entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TicketBundle:TicketConfig')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TicketConfig entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('ticketconfig_edit', array('id' => $id)));
        }

        return $this->render('TicketBundle:TicketConfig:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a TicketConfig entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('TicketBundle:TicketConfig')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find TicketConfig entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('ticketconfig'));
    }

    /**
     * Creates a form to delete a TicketConfig entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('ticketconfig_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
