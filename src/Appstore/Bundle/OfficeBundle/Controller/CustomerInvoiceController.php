<?php

namespace Appstore\Bundle\OfficeBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\OfficeBundle\Entity\CustomerInvoice;
use Appstore\Bundle\OfficeBundle\Form\CustomerInvoiceType;

/**
 * CustomerInvoice controller.
 *
 */
class CustomerInvoiceController extends Controller
{

    /**
     * Lists all CustomerInvoice entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('OfficeBundle:CustomerInvoice')->findAll();

        return $this->render('OfficeBundle:CustomerInvoice:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new CustomerInvoice entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new CustomerInvoice();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('customerinvoice_show', array('id' => $entity->getId())));
        }

        return $this->render('OfficeBundle:CustomerInvoice:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a CustomerInvoice entity.
     *
     * @param CustomerInvoice $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CustomerInvoice $entity)
    {
        $form = $this->createForm(new CustomerInvoiceType(), $entity, array(
            'action' => $this->generateUrl('customerinvoice_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new CustomerInvoice entity.
     *
     */
    public function newAction()
    {
        $entity = new CustomerInvoice();
        $form   = $this->createCreateForm($entity);

        return $this->render('OfficeBundle:CustomerInvoice:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a CustomerInvoice entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OfficeBundle:CustomerInvoice')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CustomerInvoice entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('OfficeBundle:CustomerInvoice:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing CustomerInvoice entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OfficeBundle:CustomerInvoice')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CustomerInvoice entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('OfficeBundle:CustomerInvoice:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a CustomerInvoice entity.
    *
    * @param CustomerInvoice $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(CustomerInvoice $entity)
    {
        $form = $this->createForm(new CustomerInvoiceType(), $entity, array(
            'action' => $this->generateUrl('customerinvoice_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing CustomerInvoice entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OfficeBundle:CustomerInvoice')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CustomerInvoice entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('customerinvoice_edit', array('id' => $id)));
        }

        return $this->render('OfficeBundle:CustomerInvoice:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a CustomerInvoice entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('OfficeBundle:CustomerInvoice')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find CustomerInvoice entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('customerinvoice'));
    }

    /**
     * Creates a form to delete a CustomerInvoice entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('customerinvoice_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
