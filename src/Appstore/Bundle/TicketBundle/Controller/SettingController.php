<?php

namespace Appstore\Bundle\TicketBundle\Controller;

use Appstore\Bundle\TicketBundle\Entity\Setting;
use Appstore\Bundle\TicketBundle\Form\SettingType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * SettingController controller.
 *
 */
class SettingController extends Controller
{

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
     * Lists all Setting entities.
     *
     */
    public function indexAction()
    {
        $entity = new Setting();
        $data = $_REQUEST;
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getTicketConfig();
        $pagination = $em->getRepository('TicketBundle:Setting')->findAll();
        //$pagination = $this->paginate($pagination);
        $editForm = $this->createCreateForm($entity);
        return $this->render('TicketBundle:Setting:index.html.twig', array(
            'pagination' => $pagination,
            'searchForm' => $data,
            'form'   => $editForm->createView(),
        ));

    }

    /**
     * Creates a new Setting entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Setting();
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getTicketConfig();
        $pagination = $em->getRepository('TicketBundle:Setting')->findAll();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setConfig($config);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('ticket_setting'));
        }

        return $this->render('TicketBundle:Setting:index.html.twig', array(
            'entity' => $entity,
            'pagination' => $pagination,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Setting entity.
     *
     * @param Setting $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Setting $entity)
    {
        $config = $this->getUser()->getGlobalOption()->getTicketConfig();
        $form = $this->createForm(new SettingType($config), $entity, array(
            'action' => $this->generateUrl('ticket_setting_create', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }



    /**
     * Displays a form to edit an existing Setting entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getTicketSetting();
         $pagination = $em->getRepository('TicketBundle:Setting')->findAll();
        //$pagination = $this->paginate($pagination);
        $entity = $em->getRepository('TicketBundle:Setting')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Setting entity.');
        }
        $editForm = $this->createEditForm($entity);

        return $this->render('TicketBundle:Setting:index.html.twig', array(
            'entity'      => $entity,
            'pagination'      => $pagination,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Setting entity.
     *
     * @param Setting $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Setting $entity)
    {

        $config = $this->getUser()->getGlobalOption()->getTicketSetting();
        $form = $this->createForm(new SettingType($config), $entity, array(
            'action' => $this->generateUrl('ticket_setting_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing Setting entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getTicketSetting();
        $pagination = $em->getRepository('TicketBundle:Setting')->findAll();
        $entity = $em->getRepository('TicketBundle:Setting')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Setting entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('ticket_setting'));
        }

        return $this->render('TicketBundle:Setting:index.html.twig', array(
            'entity'      => $entity,
            'pagination'      => $pagination,
            'form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a Setting entity.
     *
     */
    public function deleteAction(Setting $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Setting entity.');
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
        return $this->redirect($this->generateUrl('ticket_setting'));
    }

   
    /**
     * Status a Page entity.
     *
     */
    public function statusAction(Setting $entity)
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
        return $this->redirect($this->generateUrl('ticket_setting'));
    }
}
