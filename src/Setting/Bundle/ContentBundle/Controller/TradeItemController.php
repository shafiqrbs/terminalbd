<?php

namespace Setting\Bundle\ContentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Setting\Bundle\ContentBundle\Entity\TradeItem;
use Setting\Bundle\ContentBundle\Form\TradeItemType;

/**
 * TradeItem controller.
 *
 */
class TradeItemController extends Controller
{


    public function paginate($entities)
    {

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            20  /*limit per page*/
        );
        return $pagination;
    }


    /**
     * Lists all TradeItem entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('SettingContentBundle:TradeItem')->findBy(array('globalOption'=>$globalOption));
        $entities = $this->paginate($entities);
        return $this->render('SettingContentBundle:TradeItem:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new TradeItem entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new TradeItem();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $data = $request->request->all();
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $entity->setGlobalOption($user->getGlobalOption());
            $entity->upload();
            $em->persist($entity);
            $em->flush();
            $this->getDoctrine()->getRepository('SettingContentBundle:PageMeta')->tradeItemPageMeta($entity,$data);
            return $this->redirect($this->generateUrl('tradeitem'));
        }

        return $this->render('SettingContentBundle:TradeItem:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a TradeItem entity.
     *
     * @param TradeItem $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(TradeItem $entity)
    {

        $form = $this->createForm(new TradeItemType($this->getUser()->getGlobalOption()), $entity, array(
            'action' => $this->generateUrl('tradeitem_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new TradeItem entity.
     *
     */
    public function newAction()
    {
        $entity = new TradeItem();
        $form   = $this->createCreateForm($entity);

        return $this->render('SettingContentBundle:TradeItem:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a TradeItem entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingContentBundle:TradeItem')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TradeItem entity.');
        }
        return $this->render('SettingContentBundle:TradeItem:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * Displays a form to edit an existing TradeItem entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingContentBundle:TradeItem')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TradeItem entity.');
        }

        $editForm = $this->createEditForm($entity);

        return $this->render('SettingContentBundle:TradeItem:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }


    /**
     * Creates a form to edit a TradeItem entity.
     *
     * @param TradeItem $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(TradeItem $entity)
    {

        $form = $this->createForm(new TradeItemType($this->getUser()->getGlobalOption()), $entity, array(
            'action' => $this->generateUrl('tradeitem_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing TradeItem entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SettingContentBundle:TradeItem')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TradeItem entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $data = $request->request->all();
            if($entity->upload()){
                $entity->removeUpload();
            }
            $entity->upload();
            $em->flush();
            $this->getDoctrine()->getRepository('SettingContentBundle:PageMeta')->tradeItemPageMeta($entity,$data);
            return $this->redirect($this->generateUrl('tradeitem_edit', array('id' => $id)));
        }

        return $this->render('SettingContentBundle:TradeItem:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }


    /**
     * Deletes a TradeItem entity.
     *
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $entity = $em->getRepository('SettingContentBundle:TradeItem')->findOneBy(array('globalOption'=>$globalOption,'id'=>$id));
        if (!empty($entity)) {

            $entity->removeUpload();
            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Status has been deleted successfully"
            );
        }else{
            $this->get('session')->getFlashBag()->add(
                'error',"Sorry! Data not deleted"
            );
        }
        return $this->redirect($this->generateUrl('tradeitem'));
    }



    /**
     * Status a news entity.
     *
     */
    public function statusAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SettingContentBundle:TradeItem')->find($id);

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
            'error',"Status has been changed successfully"
        );
        return $this->redirect($this->generateUrl('tradeitem'));
    }


}
