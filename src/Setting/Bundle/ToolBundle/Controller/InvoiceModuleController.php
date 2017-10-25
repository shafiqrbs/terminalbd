<?php

namespace Setting\Bundle\ToolBundle\Controller;

use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Setting\Bundle\ToolBundle\Entity\InvoiceModuleItem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Setting\Bundle\ToolBundle\Entity\InvoiceModule;
use Symfony\Component\HttpFoundation\Response;


/**
 * InvoiceModule controller.
 *
 */
class InvoiceModuleController extends Controller
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
     * Lists all InvoiceModule entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SettingToolBundle:InvoiceModule')->findBy(array(),array('updated'=>'DESC'));
        $entities = $this->paginate($entities);
        return $this->render('SettingToolBundle:InvoiceModule:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Lists all InvoiceModule entities.
     *
     */
    public function domainAction()
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('SettingToolBundle:InvoiceModule')->domainInvoice($globalOption);
        $entities = $this->paginate($entities);
        return $this->render('SettingToolBundle:InvoiceModule:domain.html.twig', array(
            'entities' => $entities,
        ));
    }
   
    /**
     * Displays a form to create a new InvoiceModule entity.
     *
     */
    public function newAction(GlobalOption $option)
    {
        $billMonth = date('F,Y');
        $exits = $this->getDoctrine()->getRepository('SettingToolBundle:InvoiceModule')->findBy(array('billMonth'=>$billMonth,'globalOption'=>$option));
        if(empty($exits)){

            $entity = new InvoiceModule();
            $em = $this->getDoctrine()->getManager();
            $entity->setGlobalOption($option);
            $entity->setBillMonth($billMonth);
            $entity->setProcess('Created');
            $em->persist($entity);
            $em->flush();
            $this->getDoctrine()->getRepository('SettingToolBundle:InvoiceModule')->insertInvoiceItem($entity);
            $this->get('session')->getFlashBag()->add(
                'success',"Invoice  has been generated successfully"
            );
            return $this->redirect($this->generateUrl('invoicemodule_edit', array('invoice' => $entity->getInvoice())));

        }else{
            $this->get('session')->getFlashBag()->add(
                'success',"Invoice  has been already created"
            );
            return $this->redirect($this->generateUrl('tools_domain'));
        }


    }

     /**
     * Displays a form to create a new InvoiceModule entity.
     *
     */
    public function editAction(InvoiceModule $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InvoiceModule entity.');
        }
        $banks = $em->getRepository('SettingToolBundle:PortalBankAccount')->findBy(array('status'=>1));
        $bkashs = $em->getRepository('SettingToolBundle:PortalBkashAccount')->findBy(array('status'=>1));
        return $this->render('SettingToolBundle:InvoiceModule:new.html.twig', array(
            'entity'      => $entity,
            'banks'      => $banks,
            'bkashs'      => $bkashs,

        ));
    }

    /**
     * Displays a form to create a new InvoiceModule entity.
     *
     */
    public function generateInvoiceAction(InvoiceModule $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InvoiceModule entity.');
        }
        $entity->setProcess('Pending');
        $em->persist($entity);
        $em->flush();
        $this->getDoctrine()->getRepository('SettingToolBundle:InvoiceModule')->updateInvoice($entity);
        return $this->redirect($this->generateUrl('invoicemodule_show', array('invoice' => $entity->getInvoice())));
    }

    /**
     * Finds and displays a InvoiceModule entity.
     *
     */
    public function showAction(InvoiceModule $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InvoiceModule entity.');
        }
        $banks = $em->getRepository('SettingToolBundle:PortalBankAccount')->findBy(array('status'=>1));
        $bkashs = $em->getRepository('SettingToolBundle:PortalBkashAccount')->findBy(array('status'=>1));
        return $this->render('SettingToolBundle:InvoiceModule:show.html.twig', array(
            'entity'      => $entity,
            'banks'      => $banks,
            'bkashs'      => $bkashs,

        ));
    }



    public function deleteAction(InvoiceModule $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InvoiceModule entity.');
        }

        $em->remove($entity);
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',"Invoice  has been deleted successfully"
        );
        return $this->redirect($this->generateUrl('invoicemodule'));
    }



    /**
     * Edits an existing InvoiceModule entity.
     *
     */
    public function paymentAction(Request $request,InvoiceModule $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InvoiceModule entity.');
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
            $entity->setCreatedBy($this->getUser());
            $entity->setProcess('Paid');
            $em->flush();
            $this->getDoctrine()->getRepository('SettingToolBundle:InvoiceModule')->updateInvoice($entity);
        }

        exit;

    }

    /**
     * Deletes a InvoiceModule entity.
     *
     */
    public function approveAction(InvoiceModule $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InvoiceModule entity.');
        }
        if($entity->getProcess() == 'Paid'){
            $entity->setProcess('In-progress');
        }elseif($entity->getProcess() == 'In-progress'){
            $entity->setProcess('Done');
            $entity->setReceivedBy($this->getUser());
        }
        $em->flush();
        return new Response('success');
    }

    public function inlineUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SettingToolBundle:InvoiceModuleItem')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Item entity.');
        }
        $entity->setAmount($data['value']);
        $em->flush();
        exit;
    }

    public function printAction(InvoiceModule $entity)
    {

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InvoiceModule entity.');
        }
        return $this->render('SettingToolBundle:InvoiceModule:print.html.twig', array(
            'entity'      => $entity
        ));
    }



}
