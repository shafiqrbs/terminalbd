<?php

namespace Appstore\Bundle\RestaurantBundle\Controller;

use Appstore\Bundle\RestaurantBundle\Entity\Particular;
use Appstore\Bundle\RestaurantBundle\Form\AccessoriesType;
use Appstore\Bundle\RestaurantBundle\Form\StockType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * StockController controller.
 *
 */
class StockController extends Controller
{

    public function paginate($entities)
    {

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            25  /*limit per page*/
        );
        return $pagination;
    }

    /**
     * Lists all Particular entities.
     *
     */
    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();
        $hospital = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        $entities = $this->getDoctrine()->getRepository('RestaurantBundle:Particular')->getAccessoriesParticular($hospital);
        $pagination = $this->paginate($entities);
        $entity = new Particular();
        $form = $this->createCreateForm($entity);
        return $this->render('RestaurantBundle:Stock:index.html.twig', array(
            'pagination' => $pagination,
            'entity' => $entity,
            'formShow'            => 'hide',
            'form'   => $form->createView(),
        ));

    }

    /**
     * Creates a new Particular entity.
     *
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        $entities = $this->getDoctrine()->getRepository('RestaurantBundle:Particular')->getAccessoriesParticular($config);
        $pagination = $this->paginate($entities);
        $entity = new Particular();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setRestaurantConfig($config);
            $entity->upload();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('restaurant_stock'));
        }
        $this->get('session')->getFlashBag()->add(
            'error',"Required field does not input"
        );
        return $this->render('RestaurantBundle:Stock:index.html.twig', array(
            'entity' => $entity,
            'pagination' => $pagination,
            'formShow'            => 'show',
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Particular entity.
     *
     * @param Particular $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Particular $entity)
    {

        $global = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new StockType($global), $entity, array(
            'action' => $this->generateUrl('restaurant_stock_create', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }


    /**
     * Displays a form to edit an existing Particular entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('RestaurantBundle:Particular')->find($id);
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        $entities = $this->getDoctrine()->getRepository('RestaurantBundle:Particular')->getAccessoriesParticular($config);
        $pagination = $this->paginate($entities);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Particular entity.');
        }
        $editForm = $this->createEditForm($entity);
        return $this->render('RestaurantBundle:Stock:index.html.twig', array(
            'pagination'        => $pagination,
            'entity'            => $entity,
            'formShow'            => 'show',
            'form'              => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Particular entity.
     *
     * @param Particular $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Particular $entity)
    {
        $global = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new StockType($global), $entity, array(
            'action' => $this->generateUrl('restaurant_stock_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing Particular entity.
     *
     */
    public function updateAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        $entities = $this->getDoctrine()->getRepository('RestaurantBundle:Particular')->getAccessoriesParticular($config);
        $pagination = $this->paginate($entities);
        $entity = $em->getRepository('RestaurantBundle:Particular')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Particular entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            if($entity->upload() && !empty($entity->getFile())){
                $entity->removeUpload();
            }
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('restaurant_stock'));
        }
        return $this->render('RestaurantBundle:Stock:index.html.twig', array(
            'pagination'      => $pagination,
            'entity'      => $entity,
            'formShow'            => 'show',
            'form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a Particular entity.
     *
     */
    public function deleteAction(Particular $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Particular entity.');
        }
        try {

            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'error',"Data has been deleted successfully"
            );

        } catch (ForeignKeyConstraintViolationException $e) {
            $this->get('session')->getFlashBag()->add(
                'notice',"Data has been relation another Table"
            );
        }catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add(
                'notice', 'Please contact system administrator further notification.'
            );
        }
        return $this->redirect($this->generateUrl('restaurant_stock'));
    }

   
    /**
     * Status a Page entity.
     *
     */
    public function statusAction(Particular $entity)
    {

        $em = $this->getDoctrine()->getManager();
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
        return $this->redirect($this->generateUrl('restaurant_stock'));
    }

    public function inlineUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('RestaurantBundle:Particular')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find particular entity.');
        }
        $setField = 'set'.$data['name'];
        $entity->$setField(abs($data['value']));
        $em->flush();
        exit;

    }
}
