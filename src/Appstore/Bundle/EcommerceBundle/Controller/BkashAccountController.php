<?php

namespace Appstore\Bundle\EcommerceBundle\Controller;

use Appstore\Bundle\EcommerceBundle\Entity\BkashAccount;
use Appstore\Bundle\EcommerceBundle\Form\BkashAccountType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * BkashAccount controller.
 *
 */
class BkashAccountController extends Controller
{

    /**
     * Lists all BkashAccount entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('EcommerceBundle:BkashAccount')->findAll();

        return $this->render('EcommerceBundle:BkashAccount:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new BkashAccount entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new BkashAccount();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $name = $entity->getAccountType().','.$entity->getMobile();
            $entity->setName($name);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been inserted successfully"
            );
            return $this->redirect($this->generateUrl('ecommerce_bkash'));
        }

        return $this->render('EcommerceBundle:BkashAccount:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a BkashAccount entity.
     *
     * @param BkashAccount $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(BkashAccount $entity)
    {
        $form = $this->createForm(new BkashAccountType(), $entity, array(
            'action' => $this->generateUrl('ecommerce_bkash_create'),
            'method' => 'POST',
        ));
        return $form;
    }

    /**
     * Displays a form to create a new BkashAccount entity.
     *
     */
    public function newAction()
    {
        $entity = new BkashAccount();
        $form   = $this->createCreateForm($entity);

        return $this->render('EcommerceBundle:BkashAccount:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a BkashAccount entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EcommerceBundle:BkashAccount')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BkashAccount entity.');
        }
        return $this->render('EcommerceBundle:BkashAccount:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * Displays a form to edit an existing BkashAccount entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EcommerceBundle:BkashAccount')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BkashAccount entity.');
        }

        $editForm = $this->createEditForm($entity);
        return $this->render('EcommerceBundle:BkashAccount:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a BkashAccount entity.
    *
    * @param BkashAccount $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(BkashAccount $entity)
    {
        $form = $this->createForm(new PortalBkashAccountType(), $entity, array(
            'action' => $this->generateUrl('ecommerce_bkash_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        return $form;
    }
    /**
     * Edits an existing BkashAccount entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EcommerceBundle:BkashAccount')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BkashAccount entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $name = $entity->getAccountType().','.$entity->getMobile();
            $entity->setName($name);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been changed successfully"
            );
            return $this->redirect($this->generateUrl('ecommerce_bkash_edit', array('id' => $id)));
        }

        return $this->render('EcommerceBundle:BkashAccount:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a BkashAccount entity.
     *
     */
    public function deleteAction(BkashAccount $entity)
    {

            $em = $this->getDoctrine()->getManager();
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find BkashAccount entity.');
            }

            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'error',"Data has been deleted successfully"
            );
            return $this->redirect($this->generateUrl('ecommerce_bkash'));
    }

    /**
     * Status a BkashAccount entity.
     *
     */
    public function statusAction(BkashAccount $entity)
    {

        $em = $this->getDoctrine()->getManager();
        $status = $entity->getStatus();
        if($status == 1){
            $entity->setStatus(0);
        } else{
            $entity->setStatus(1);
        }
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',"Status has been changed successfully"
        );
        return $this->redirect($this->generateUrl('ecommerce_bkash'));
    }



}
