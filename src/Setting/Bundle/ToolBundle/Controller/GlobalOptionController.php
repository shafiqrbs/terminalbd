<?php

namespace Setting\Bundle\ToolBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Setting\Bundle\Tool\Service\RequestManager;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Setting\Bundle\ToolBundle\Form\GlobalOptionType;

/**
 * GlobalOption controller.
 *
 */
class GlobalOptionController extends Controller
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
     * Lists all GlobalOption entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SettingToolBundle:GlobalOption')->findAll();
        $entities = $this->paginate($entities);
        return $this->render('SettingToolBundle:GlobalOption:index.html.twig', array(
            'pagination' => $entities,
        ));
    }
    /**
     * Creates a new GlobalOption entity.
     *
     */
    public function createAction(Request $request)
    {

        $user = $this->get('security.context')->getToken()->getUser();

        $entity = new GlobalOption();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $entity->setUser($user);
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',"Data has been inserted successfully"
            );
            $this->get('settong.toolManageRepo')->createDirectory($user);
            $this->getDoctrine()->getRepository('SettingToolBundle:SiteSetting')->globalOptionSetting($entity);

            return $this->redirect($this->generateUrl('globaloption_show', array('id' => $entity->getId())));


        }

        return $this->render('SettingToolBundle:GlobalOption:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a GlobalOption entity.
     *
     * @param GlobalOption $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(GlobalOption $entity)
    {

        $form = $this->createForm(new GlobalOptionType(), $entity, array(
            'action' => $this->generateUrl('globaloption_create', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));

        return $form;
    }

    /**
     * Displays a form to create a new GlobalOption entity.
     *
     */
    public function newAction()
    {
        $entity = new GlobalOption();
        $form   = $this->createCreateForm($entity);

        return $this->render('SettingToolBundle:GlobalOption:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a GlobalOption entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:GlobalOption')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find GlobalOption entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SettingToolBundle:GlobalOption:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing GlobalOption entity.
     *
     */
    public function editAction(GlobalOption $entity)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find GlobalOption entity.');
        }
        $this->get('settong.toolManageRepo')->createDirectory($entity->getId());
        $this->getDoctrine()->getRepository('SettingToolBundle:SiteSetting')->globalOptionSetting($entity);

        $subSyndicates = $this->getSubSyndicateUnderVendor($entity);
        $editForm = $this->createEditForm($entity);

        return $this->render('SettingToolBundle:GlobalOption:new.html.twig', array(
            'entity'      => $entity,
            'subSyndicates'      => $subSyndicates,
            'edit_form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a GlobalOption entity.
    *
    * @param GlobalOption $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(GlobalOption $entity)
    {

        $form = $this->createForm(new GlobalOptionType(), $entity, array(
            'action' => $this->generateUrl('globaloption_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));

        return $form;

    }
    /**
     * Edits an existing GlobalOption entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:GlobalOption')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find GlobalOption entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $em->flush();
            //$this->getDoctrine()->getRepository('SettingContentBundle:HomePage')->globalOptionHome($user);
            //$this->getDoctrine()->getRepository('SettingContentBundle:ContactPage')->globalOptionContact($user);
            $this->getDoctrine()->getRepository('SettingToolBundle:SiteSetting')->updateSettingMenu($entity);
            $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->systemConfigUpdate($entity);
            $this->get('session')->getFlashBag()->add('success',"Data has been updated successfully");

            /*$referer = $request->headers->get('referer');
            return new RedirectResponse($referer);*/
            return $this->redirect($this->generateUrl('globaloption_edit', array('id' => $entity->getId())));


        }
        return $this->render('SettingToolBundle:GlobalOption:new.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));


    }
    /**
     * Deletes a GlobalOption entity.
     *
     */
    public function deleteAction(GlobalOption $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find GlobalOption entity.');
        }

        $em->remove($entity);
        $em->flush();
        $this->get('session')->getFlashBag()->add('error',"Data has been deleted successfully");
        return $this->redirect($this->generateUrl('tools_domain'));
    }

    /**
     * Creates a form to delete a GlobalOption entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('globaloption_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }


    /**
     * Displays a form to edit an existing GlobalOption entity.
     *
     */
    public function modifyAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $this->getUser()->getGlobalOption();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find GlobalOption entity.');
        }

        $this->get('settong.toolManageRepo')->createDirectory($entity->getId());
        $this->getDoctrine()->getRepository('SettingToolBundle:SiteSetting')->globalOptionSetting($entity);
        $editForm = $this->createEditForm($entity);

        $subSyndicates = $this->getSubSyndicateUnderVendor($entity);

        return $this->render('SettingToolBundle:GlobalOption:new.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'subSyndicates' => $subSyndicates
        ));
    }

    public function getSubSyndicateUnderVendor($entity)
    {
        $em = $this->getDoctrine()->getManager();
        $parentId       = $entity->getSyndicate()->getId();
        $syndicates     = $em->getRepository('SettingToolBundle:Syndicate')->findBy(array('status'=>1,'parent'=>$parentId),array('name'=>'asc'));
        return $subSyndicates  = $this->getDoctrine()->getRepository('SettingToolBundle:Syndicate')->getSelectedSubSyndicates($syndicates,$entity);

    }

    public function webmailAction()
    {
        return $this->render('SettingToolBundle:GlobalOption:webmail.html.twig', array(
            'entity'      => $this->getUser()->getGlobalOption(),

        ));
    }

    public function vendorSearchAction()
    {
        echo 'Test';
    }
}
