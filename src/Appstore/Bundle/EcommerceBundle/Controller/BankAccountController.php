<?php

namespace Appstore\Bundle\EcommerceBundle\Controller;

use Appstore\Bundle\EcommerceBundle\Form\BankAccountType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\EcommerceBundle\Entity\BankAccount;


/**
 * BankAccount controller.
 *
 */
class BankAccountController extends Controller
{

    /**
     * Lists all BankAccount entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('EcommerceBundle:BankAccount')->findAll();

        return $this->render('EcommerceBundle:BankAccount:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new BankAccount entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new BankAccount();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $name = $entity->getBank()->getName().','.$entity->getBranch();
            $entity->setName($name);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been inserted successfully"
            );
            return $this->redirect($this->generateUrl('ecommerce_bank'));
        }

        return $this->render('EcommerceBundle:BankAccount:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a BankAccount entity.
     *
     * @param BankAccount $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(BankAccount $entity)
    {
        $form = $this->createForm(new BankAccountType(), $entity, array(
            'action' => $this->generateUrl('ecommerce_bank_create'),
            'method' => 'POST',
        ));
         return $form;
    }

    /**
     * Displays a form to create a new BankAccount entity.
     *
     */
    public function newAction()
    {
        $entity = new BankAccount();
        $form   = $this->createCreateForm($entity);

        return $this->render('EcommerceBundle:BankAccount:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a BankAccount entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EcommerceBundle:BankAccount')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BankAccount entity.');
        }
       return $this->render('EcommerceBundle:BankAccount:show.html.twig', array(
            'entity'      => $entity,
       ));
    }

    /**
     * Displays a form to edit an existing BankAccount entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EcommerceBundle:BankAccount')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BankAccount entity.');
        }
        $editForm = $this->createEditForm($entity);
        return $this->render('EcommerceBundle:BankAccount:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a BankAccount entity.
    *
    * @param BankAccount $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(BankAccount $entity)
    {
        $form = $this->createForm(new BankAccountType(), $entity, array(
            'action' => $this->generateUrl('ecommerce_bank_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }
    /**
     * Edits an existing BankAccount entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EcommerceBundle:BankAccount')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BankAccount entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $name = $entity->getBank()->getName().','.$entity->getBranch();
            $entity->setName($name);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('ecommerce_bank_edit', array('id' => $id)));
        }

        return $this->render('EcommerceBundle:BankAccount:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a BankAccount entity.
     *
     */
    public function deleteAction(BankAccount $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BankAccount entity.');
        }

        $em->remove($entity);
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'error',"Data has been deleted successfully"
        );
        return $this->redirect($this->generateUrl('ecommerce_bank'));
    }

    /**
     * Status a BankAccount entity.
     *
     */
    public function statusAction(BankAccount $entity)
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
        return $this->redirect($this->generateUrl('ecommerce_bank'));
    }



}
