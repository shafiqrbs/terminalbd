<?php

namespace Appstore\Bundle\InventoryBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\InventoryBundle\Entity\ItemSize;
use Appstore\Bundle\InventoryBundle\Form\ItemSizeType;

/**
 * ItemSize controller.
 *
 */
class ItemSizeController extends Controller
{

    /**
     * Lists all ItemSize entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities = $em->getRepository('InventoryBundle:ItemSize')->findBy(array('inventoryConfig'=>$inventory),array('name'=>'asc'));

        return $this->render('InventoryBundle:ItemSize:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new ItemSize entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new ItemSize();
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
            return $this->redirect($this->generateUrl('itemsize'));
        }

        return $this->render('InventoryBundle:ItemSize:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a ItemSize entity.
     *
     * @param ItemSize $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ItemSize $entity)
    {
        $form = $this->createForm(new ItemSizeType(), $entity, array(
            'action' => $this->generateUrl('itemsize_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new ItemSize entity.
     *
     */
    public function newAction()
    {
        $entity = new ItemSize();
        $form   = $this->createCreateForm($entity);

        return $this->render('InventoryBundle:ItemSize:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ItemSize entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:ItemSize')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ItemSize entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('InventoryBundle:ItemSize:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ItemSize entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:ItemSize')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ItemSize entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('InventoryBundle:ItemSize:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a ItemSize entity.
    *
    * @param ItemSize $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(ItemSize $entity)
    {
        $form = $this->createForm(new ItemSizeType(), $entity, array(
            'action' => $this->generateUrl('itemsize_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
         return $form;
    }
    /**
     * Edits an existing ItemSize entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:ItemSize')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ItemSize entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('itemsize_edit', array('id' => $id)));
        }

        return $this->render('InventoryBundle:ItemSize:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a ItemSize entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('InventoryBundle:ItemSize')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ItemSize entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('ItemSize'));
    }

    /**
     * Creates a form to delete a ItemSize entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('itemsize_delete', array('id' => $id)))
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
            $item = $this->getDoctrine()->getRepository('InventoryBundle:ItemSize')->searchAutoComplete($item,$inventory);
        }
        return new JsonResponse($item);
    }

    public function searchItemSizeNameAction($size)
    {
        return new JsonResponse(array(
            'id'=>$size,
            'text'=>$size
        ));
    }
}
