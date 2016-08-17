<?php

namespace Appstore\Bundle\InventoryBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\InventoryBundle\Entity\ItemColor;
use Appstore\Bundle\InventoryBundle\Form\ItemColorType;

/**
 * ItemColor controller.
 *
 */
class ItemColorController extends Controller
{

    /**
     * Lists all ItemColor entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities = $em->getRepository('InventoryBundle:ItemColor')->findBy(array('inventoryConfig'=>$inventory),array('name'=>'asc'));
        return $this->render('InventoryBundle:ItemColor:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new ItemColor entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new ItemColor();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
            $entity->setInventoryConfig($inventory);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('itemcolor_show', array('id' => $entity->getId())));
        }

        return $this->render('InventoryBundle:ItemColor:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a ItemColor entity.
     *
     * @param ItemColor $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ItemColor $entity)
    {
        $form = $this->createForm(new ItemColorType(), $entity, array(
            'action' => $this->generateUrl('itemcolor_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new ItemColor entity.
     *
     */
    public function newAction()
    {
        $entity = new ItemColor();
        $form   = $this->createCreateForm($entity);

        return $this->render('InventoryBundle:ItemColor:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ItemColor entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:ItemColor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ItemColor entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('InventoryBundle:ItemColor:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ItemColor entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:ItemColor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ItemColor entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('InventoryBundle:ItemColor:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a ItemColor entity.
    *
    * @param ItemColor $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(ItemColor $entity)
    {
        $form = $this->createForm(new ItemColorType(), $entity, array(
            'action' => $this->generateUrl('itemcolor_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
         return $form;
    }
    /**
     * Edits an existing ItemColor entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:ItemColor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ItemColor entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('itemcolor_edit', array('id' => $id)));
        }

        return $this->render('InventoryBundle:ItemColor:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a ItemColor entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('InventoryBundle:ItemColor')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ItemColor entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('ItemColor'));
    }

    /**
     * Creates a form to delete a ItemColor entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('itemcolor_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    public function autoSearchAction(Request $request)
    {
        $item = $_REQUEST['q'];
        if ($item) {
            $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
            $item = $this->getDoctrine()->getRepository('InventoryBundle:ItemColor')->searchAutoComplete($item,$inventory);
        }
        return new JsonResponse($item);
    }

    public function searchItemColorNameAction($color)
    {
        return new JsonResponse(array(
            'id'=>$color,
            'text'=>$color
        ));
    }
}
