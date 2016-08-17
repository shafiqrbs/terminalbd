<?php

namespace Setting\Bundle\ToolBundle\Controller;

use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Setting\Bundle\ToolBundle\Entity\InvoiceSmsEmailItem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Setting\Bundle\ToolBundle\Entity\InvoiceSmsEmail;
use Setting\Bundle\ToolBundle\Form\InvoiceSmsEmailType;
use Symfony\Component\HttpFoundation\Response;

/**
 * InvoiceSmsEmail controller.
 *
 */
class InvoiceSmsEmailController extends Controller
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
     * Lists all InvoiceSmsEmail entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SettingToolBundle:InvoiceSmsEmail')->findBy(array(),array('updated'=>'DESC'));
        $entities = $this->paginate($entities);
        return $this->render('SettingToolBundle:InvoiceSmsEmail:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Lists all InvoiceSmsEmail entities.
     *
     */
    public function domainAction()
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('SettingToolBundle:InvoiceSmsEmail')->findBy(array('globalOption'=>$globalOption),array('updated'=>'desc'));
        $entities = $this->paginate($entities);
        return $this->render('SettingToolBundle:InvoiceSmsEmail:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new InvoiceSmsEmail entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new InvoiceSmsEmail();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('invoicesmsemail_show', array('invoice' => $entity->getInvoice())));
        }

        return $this->render('SettingToolBundle:InvoiceSmsEmail:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a InvoiceSmsEmail entity.
     *
     * @param InvoiceSmsEmail $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(InvoiceSmsEmail $entity)
    {
        $form = $this->createForm(new InvoiceSmsEmailType(), $entity, array(
            'action' => $this->generateUrl('invoicesmsemail_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new InvoiceSmsEmail entity.
     *
     */
    public function newAction(GlobalOption $option)
    {
        $entity = new InvoiceSmsEmail();
        $em = $this->getDoctrine()->getManager();
        $entity->setGlobalOption($option);
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('invoicesmsemail_edit', array('invoice' => $entity->getInvoice())));

    }

    /**
     * Finds and displays a InvoiceSmsEmail entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:InvoiceSmsEmail')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InvoiceSmsEmail entity.');
        }
        $banks = $em->getRepository('SettingToolBundle:PortalBankAccount')->findBy(array('status'=>1));
        $bkashs = $em->getRepository('SettingToolBundle:PortalBkashAccount')->findBy(array('status'=>1));
        return $this->render('SettingToolBundle:InvoiceSmsEmail:show.html.twig', array(
            'entity'      => $entity,
            'banks'      => $banks,
            'bkashs'      => $bkashs,

        ));
    }

    /**
     * Displays a form to edit an existing InvoiceSmsEmail entity.
     *
     */
    public function editAction(InvoiceSmsEmail $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InvoiceSmsEmail entity.');
        }

        $editForm = $this->createEditForm($entity);
        $sePricing = $this->getDoctrine()->getRepository('SettingToolBundle:SmsEmailPricing')->findBy(array('status'=>1),array('name'=>'asc'));
        return $this->render('SettingToolBundle:InvoiceSmsEmail:new.html.twig', array(
            'entity'      => $entity,
            'packagePricing'      => $sePricing,
            'form'   => $editForm->createView(),

        ));
    }

    /**
    * Creates a form to edit a InvoiceSmsEmail entity.
    *
    * @param InvoiceSmsEmail $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(InvoiceSmsEmail $entity)
    {
        $form = $this->createForm(new InvoiceSmsEmailType(), $entity, array(
            'action' => $this->generateUrl('invoicesmsemail_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        return $form;
    }
    /**
     * Edits an existing InvoiceSmsEmail entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:InvoiceSmsEmail')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InvoiceSmsEmail entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $data = $request->request->all();
            $em->flush();
            $this->getDoctrine()->getRepository('SettingToolBundle:InvoiceSmsEmailItem')->insertItem($entity,$data);
            $this->getDoctrine()->getRepository('SettingToolBundle:InvoiceSmsEmail')->updateInvoice($entity);
            return $this->redirect($this->generateUrl('invoicesmsemail_show', array('id' => $id)));
        }

        return $this->render('SettingToolBundle:InvoiceSmsEmail:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a InvoiceSmsEmail entity.
     *
     */
    public function deleteAction(InvoiceSmsEmail $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InvoiceSmsEmail entity.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('invoicesmsemail'));
    }

    /**
     * Deletes a InvoiceSmsEmailItem entity.
     *
     */
    public function deleteItemAction(InvoiceSmsEmailItem $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InvoiceSmsEmail entity.');
        }

        $em->remove($entity);
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'error',"Data has been deleted successfully"
        );
        $this->getDoctrine()->getRepository('SettingToolBundle:InvoiceSmsEmail')->updateInvoice($entity->getInvoiceSmsEmail());
        exit;


    }

    /**
     * Edits an existing InvoiceSmsEmail entity.
     *
     */
    public function paymentAction(Request $request,InvoiceSmsEmail $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InvoiceSmsEmail entity.');
        }
        $data = $request->request->all();
        if( ($data['paymentMethod'] != "" && $data['bank'] != "") || ( $data['paymentMethod'] != "" && $data['bkash'] != "") ){

            $entity->setPaymentMethod($data['paymentMethod']);
            if($entity->getPaymentMethod() == 'Bank')
            {
                $bank = $this->getDoctrine()->getRepository('SettingToolBundle:PortalBankAccount')->find($data['bank']);
                $entity->setPortalBankAccount($bank);

            }else{

                $bkash = $this->getDoctrine()->getRepository('SettingToolBundle:PortalBkashAccount')->find($data['bkash']);
                $entity->setPortalBkash($bkash);
            }
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            $this->getDoctrine()->getRepository('SettingToolBundle:InvoiceSmsEmail')->updateInvoice($entity);
        }

        exit;

    }

    /**
     * Deletes a InvoiceSmsEmail entity.
     *
     */
    public function approveAction(InvoiceSmsEmail $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InvoiceSmsEmail entity.');
        }
        if($entity->getProcess() == 'Pending'){
            $entity->setProcess('In-progress');
        }elseif($entity->getProcess() == 'In-progress'){
            $entity->setProcess('Done');
            $entity->setReceivedBy($this->getUser());
        }
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',"Data has been updated successfully"
        );
        return new Response('success');
    }



}
