<?php

namespace Appstore\Bundle\InventoryBundle\Controller;

use Appstore\Bundle\InventoryBundle\Entity\PurchaseItem;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseItemAttribute;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Appstore\Bundle\InventoryBundle\Form\PurchaseItemAttributeType;

/**
 * PurchaseItemAttribute controller.
 *
 */
class PurchaseItemAttributeController extends Controller
{

    /**
     * Lists all PurchaseItemAttribute entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('InventoryBundle:PurchasePurchaseItemAttribute')->findBy(array(),array('name'=>'ASC'));
        return $this->render('InventoryBundle:PurchaseItemAttribute:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new PurchaseItemAttribute entity.
     *
     */
    public function createAction(Request $request,PurchaseItem $purchaseItem)
    {
        $entity = new PurchaseItemAttribute();
        $form = $this->createCreateForm($entity,$purchaseItem);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setPurchaseItem($purchaseItem);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('itemattribute'));
        }

        return $this->render('InventoryBundle:PurchaseItemAttribute:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a PurchaseItemAttribute entity.
     *
     * @param PurchaseItemAttribute $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(PurchaseItemAttribute $entity,PurchaseItem $purchaseItem)
    {
        $form = $this->createForm(new PurchaseItemAttributeType(), $entity, array(
            'action' => $this->generateUrl('purchaseitemattribute_create',array('purchaseItem' => $purchaseItem->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new PurchaseItemAttribute entity.
     *
     */
    public function newAction(PurchaseItem $purchaseItem)
    {

        $existEntity = $this->getDoctrine()->getRepository('InventoryBundle:PurchaseItemAttribute')->findOneBy(array('purchaseItem'=>$purchaseItem));
        if($existEntity){
            return $this->redirect($this->generateUrl('purchaseitemattribute_edit', array('id' => $existEntity->getId())));
        }
        $entity = new PurchaseItemAttribute();
        $form   = $this->createCreateForm($entity,$purchaseItem);

        return $this->render('InventoryBundle:PurchaseItemAttribute:new.html.twig', array(
            'purchaseItem' => $purchaseItem,
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a PurchaseItemAttribute entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:PurchaseItemAttribute')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseItemAttribute entity.');
        }
        return $this->render('InventoryBundle:PurchaseItemAttribute:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * Displays a form to edit an existing PurchaseItemAttribute entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:PurchaseItemAttribute')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseItemAttribute entity.');
        }

        $editForm = $this->createEditForm($entity);
        return $this->render('InventoryBundle:PurchaseItemAttribute:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a PurchaseItemAttribute entity.
    *
    * @param PurchaseItemAttribute $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(PurchaseItemAttribute $entity)
    {
        $form = $this->createForm(new PurchaseItemAttributeType(), $entity, array(
            'action' => $this->generateUrl('purchaseitemattribute_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
         return $form;
    }
    /**
     * Edits an existing PurchaseItemAttribute entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:PurchaseItemAttribute')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseItemAttribute entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('purchaseitemattribute_edit', array('id' => $id)));
        }

        return $this->render('InventoryBundle:PurchaseItemAttribute:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a PurchaseItemAttribute entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('InventoryBundle:PurchaseItemAttribute')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find PurchaseItemAttribute entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('itemattribute'));
    }

    /**
     * Creates a form to delete a PurchaseItemAttribute entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('purchaseitemattribute_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    /**
     * Status a PurchaseItemAttribute entity.
     *
     */
    public function statusAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('InventoryBundle:PurchaseItemAttribute')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find District entity.');
        }

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
        return $this->redirect($this->generateUrl('itemattribute'));
    }

}
