<?php

namespace Appstore\Bundle\AssetsBundle\Controller;


use Appstore\Bundle\AssetsBundle\Entity\AssetsCategory;
use Appstore\Bundle\AssetsBundle\Form\AssetsCategoryType;
use Appstore\Bundle\HospitalBundle\Entity\HmsCategory;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * AssetsCategory controller.
 *
 */
class AssetsCategoryController extends Controller
{

    /**
     * Lists all AssetsCategory entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $option = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('AssetsBundle:AssetsCategory')->findBy(array('globalOption' => $option),array( 'parent'=>'asc' , 'name' =>'asc' ));
        $pagination = $this->paginate($entities);
        return $this->render('AssetsBundle:AssetsCategory:index.html.twig', array(
            'entities' => $pagination,
        ));

    }

    public function paginate($entities)
    {
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            50  /*limit per page*/
        );
        return $pagination;
    }


    /**
     * Creates a new AssetsCategory entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new AssetsCategory();
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createCreateForm($entity,$globalOption);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setGlobalOption($globalOption);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('assetscategory_new', array('id' => $entity->getId())));
        }

        return $this->render('AssetsBundle:AssetsCategory:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a AssetsCategory entity.
     *
     * @param AssetsCategory $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(AssetsCategory $entity, $globalOption)
    {

        $em = $this->getDoctrine()->getRepository('AssetsBundle:AssetsCategory');
        $emHead = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead');
        $form = $this->createForm(new AssetsCategoryType($em,$emHead,$globalOption), $entity, array(
            'action' => $this->generateUrl('assetscategory_create', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new AssetsCategory entity.
     *
     */
    public function newAction()
    {
        $entity = new AssetsCategory();
        $globalOption = $this->getUser()->getGlobalOption();
        $form   = $this->createCreateForm($entity,$globalOption);

        return $this->render('AssetsBundle:AssetsCategory:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a AssetsCategory entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AssetsBundle:AssetsCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AssetsCategory entity.');
        }
        return $this->render('AssetsBundle:AssetsCategory:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * Displays a form to edit an existing AssetsCategory entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AssetsBundle:AssetsCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AssetsCategory entity.');
        }
        $globalOption = $this->getUser()->getGlobalOption();
        $editForm = $this->createEditForm($entity,$globalOption);

        return $this->render('AssetsBundle:AssetsCategory:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a AssetsCategory entity.
     *
     * @param AssetsCategory $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(AssetsCategory $entity,$globalOption)
    {
        $em = $this->getDoctrine()->getRepository('AssetsBundle:AssetsCategory');
        $emHead = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead');
        $form = $this->createForm(new AssetsCategoryType($em,$emHead,$globalOption), $entity, array(
            'action' => $this->generateUrl('assetscategory_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));


        return $form;
    }
    /**
     * Edits an existing AssetsCategory entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AssetsBundle:AssetsCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AssetsCategory entity.');
        }

        $globalOption = $this->getUser()->getGlobalOption();
        $editForm = $this->createEditForm($entity,$globalOption);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('assetscategory'));
        }

        return $this->render('AssetsBundle:AssetsCategory:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a AssetsCategory entity.
     *
     */
    public function deleteAction(HmsCategory $entity)
    {
        $em = $this->getDoctrine()->getManager();
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
        return $this->redirect($this->generateUrl('assetscategory'));
    }


    /**
     * Status a Page entity.
     *
     */
    public function statusAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AssetsBundle:AssetsCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find District entity.');
        }
        $status = $entity->isStatus();
        if($status == 1){
            $entity->setStatus(0);
        } else{
            $entity->setStatus(1);
        }
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',"Status has been changed successfully"
        );
        return $this->redirect($this->generateUrl('assetscategory'));
    }


}
