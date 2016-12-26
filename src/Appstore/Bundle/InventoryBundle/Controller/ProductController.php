<?php

namespace Appstore\Bundle\InventoryBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\InventoryBundle\Entity\Product;
use Appstore\Bundle\InventoryBundle\Form\ProductType;

/**
 * Product controller.
 *
 */
class ProductController extends Controller
{

    public function paginate($entities)
    {

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            25  /*limit per page*/
        );
        return $pagination;
    }



    /**
     * Lists all Product entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities = $em->getRepository('InventoryBundle:Product')->findBy(array('inventoryConfig'=>$inventory),array('code'=>'ASC'));
        $pagination = $this->paginate($entities);
        return $this->render('InventoryBundle:Product:index.html.twig', array(
            'entities' => $pagination,
        ));
    }
    /**
     * Creates a new Product entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Product();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
            $entity->setInventoryConfig($inventory);
            $entity->upload();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('inventory_product', array('id' => $entity->getId())));
        }

        return $this->render('InventoryBundle:Product:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Product entity.
     *
     * @param Product $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Product $entity)
    {
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $em = $this->getDoctrine()->getRepository('ProductProductBundle:Category');
        $form = $this->createForm(new ProductType($em,$inventory), $entity, array(
            'action' => $this->generateUrl('inventory_product_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
      return $form;
    }

    public function getItemCategory($inventory){

        $em = $this->getDoctrine()->getManager();
        $productCategories = $em->getRepository('ProductProductBundle:Category')->findBy(array('status'=>1,'level'=>2),array('name'=>'asc'));
        if($productCategories)
            $grouping = $em->getRepository('InventoryBundle:ItemTypeGrouping')->findOneBy(array('inventoryConfig'=>$inventory));
        $array = array();
        if($grouping){

            $groups = $grouping->getCategories();
            foreach($groups as $row ){
                $array[] = $row->getId();
            }
        }

        return $categories = $this->getDoctrine()->getRepository('ProductProductBundle:Category')->getSelectdDropdownCategories($productCategories,$array);


    }


    /**
     * Displays a form to create a new Product entity.
     *
     */
    public function newAction()
    {
        $entity = new Product();
        $form   = $this->createCreateForm($entity);
        return $this->render('InventoryBundle:Product:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Product entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:Product')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('InventoryBundle:Product:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Product entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:Product')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }
        $editForm = $this->createEditForm($entity);
        return $this->render('InventoryBundle:Product:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Product entity.
    *
    * @param Product $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Product $entity)
    {
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $em = $this->getDoctrine()->getRepository('ProductProductBundle:Category');
        $form = $this->createForm(new ProductType($em,$inventory), $entity, array(
            'action' => $this->generateUrl('inventory_product_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));

        return $form;
    }
    /**
     * Edits an existing Product entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:Product')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            if(!empty( $entity->upload())){
                $entity->removeUpload();
            }
            $entity->upload();
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('inventory_product'));
        }

        return $this->render('InventoryBundle:Product:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a Product entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('InventoryBundle:Product')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Product entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('inventory_product'));
    }

    /**
     * Creates a form to delete a Product entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('inventory_product_delete', array('id' => $id)))
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
            $item = $this->getDoctrine()->getRepository('InventoryBundle:Product')->searchAutoComplete($item,$inventory);
        }
        return new JsonResponse($item);
    }

    public function searchProductNameAction($product)
    {
        return new JsonResponse(array(
            'id'=>$product,
            'text'=>$product
        ));
    }

    public function masterItemSelectAction()
    {
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities = $this->getDoctrine()->getRepository('InventoryBundle:Product')->findBy(
            array('inventoryConfig'=>$inventory,'status'=>1)
        );
        $items = array();
        foreach ($entities as $entity):
            $items[]=array('value' => $entity->getId(),'text'=> $entity->getName());
        endforeach;
        return new JsonResponse($items);


    }


}
