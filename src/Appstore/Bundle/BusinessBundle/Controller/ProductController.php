<?php

namespace Appstore\Bundle\BusinessBundle\Controller;

use Appstore\Bundle\BusinessBundle\Entity\BusinessParticular;
use Appstore\Bundle\BusinessBundle\Form\ProductType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * ProductController controller.
 *
 */
class ProductController extends Controller
{

    public function paginate($entities)
    {

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            25  /*limit per page*/
        );
        $pagination->setTemplate('SettingToolBundle:Widget:pagination.html.twig');
        return $pagination;
    }

    /**
     * Lists all BusinessParticular entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entities = $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticular')->findWithSearch($config,$data);
        $pagination = $this->paginate($entities);
        $entity = new BusinessParticular();
        $form = $this->createCreateForm($entity);
        return $this->render('BusinessBundle:Product:index.html.twig', array(
            'pagination' => $pagination,
            'entity' => $entity,
            'formShow'            => 'hide',
            'form'   => $form->createView(),
        ));

    }

    /**
     * Creates a new BusinessParticular entity.
     *
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entity = new BusinessParticular();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setBusinessConfig($config);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('business_product'));
        }
        return $this->render('BusinessBundle:Product:index.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a BusinessParticular entity.
     *
     * @param BusinessParticular $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(BusinessParticular $entity)
    {

        $form = $this->createForm(new ProductType(), $entity, array(
            'action' => $this->generateUrl('business_product_create', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }


    /**
     * Displays a form to edit an existing BusinessParticular entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BusinessBundle:BusinessParticular')->find($id);
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entities = $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticular')->getProductParticular($config,array('accessories'));
        $pagination = $this->paginate($entities);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BusinessParticular entity.');
        }
        $editForm = $this->createEditForm($entity);
        return $this->render('BusinessBundle:Product:index.html.twig', array(
            'pagination'        => $pagination,
            'entity'            => $entity,
            'formShow'            => 'show',
            'form'              => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a BusinessParticular entity.
     *
     * @param BusinessParticular $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(BusinessParticular $entity)
    {
        $form = $this->createForm(new ProductType(), $entity, array(
            'action' => $this->generateUrl('business_product_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing BusinessParticular entity.
     *
     */
    public function updateAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entities = $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticular')->getProductParticular($config,array('accessories'));
        $pagination = $this->paginate($entities);
        $entity = $em->getRepository('BusinessBundle:BusinessParticular')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BusinessParticular entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('business_product'));
        }
        return $this->render('BusinessBundle:Product:index.html.twig', array(
            'pagination'      => $pagination,
            'entity'      => $entity,
            'formShow'            => 'show',
            'form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a BusinessParticular entity.
     *
     */
    public function deleteAction(BusinessParticular $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BusinessParticular entity.');
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
        return $this->redirect($this->generateUrl('business_product'));
    }

   
    /**
     * Status a Page entity.
     *
     */
    public function statusAction(BusinessParticular $entity)
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
        return $this->redirect($this->generateUrl('business_product'));
    }

    public function inlineUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BusinessBundle:BusinessParticular')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find particular entity.');
        }
        $setField = 'set'.$data['name'];
        $entity->$setField(abs($data['value']));
        $em->flush();
        exit;

    }
}
