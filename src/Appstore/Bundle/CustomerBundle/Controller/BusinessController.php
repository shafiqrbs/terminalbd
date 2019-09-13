<?php

namespace Appstore\Bundle\CustomerBundle\Controller;

use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoice;
use Appstore\Bundle\BusinessBundle\Form\CustomerInvoiceType;
use Appstore\Bundle\EcommerceBundle\Entity\Order;
use Appstore\Bundle\EcommerceBundle\Form\OrderType;
use Frontend\FrontentBundle\Service\Cart;
use Setting\Bundle\ToolBundle\Entity\Module;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BusinessController extends Controller
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

    public function indexAction($shop)
    {
        
        $user = $this->getUser();
        if(!empty($shop)){
            $globalOption = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('slug' => $shop));
        }else{
            $globalOption ='';
        }
        $businessModel =  $globalOption->getBusinessConfig()->getBusinessModel();
        if($businessModel == "student-association"){
            $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('user'=>$this->getUser()->getId()));
            $entity = new BusinessInvoice();
            $editForm = $this->createCreateForm($entity);
            $Exinvoice = $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoice')->getLastInvoice($globalOption->getBusinessConfig());
            $invoiceCheck = empty($Exinvoice) ? 'False' : "true";
            $template =  "Student";
            return $this->render("CustomerBundle:{$template}:dashboard.html.twig", array(
                'user'         => $user,
                'globalOption' => $globalOption,
                'customer' => $customer,
                'entity' => $entity,
                'invoiceCheck' => $invoiceCheck,
                'form' => $editForm->createView(),
            ));
        }else{
            $template =  "Customer";
            return $this->render("CustomerBundle:{$template}:dashboard.html.twig", array(
                'user'         => $user,
                'globalOption' => $globalOption,
            ));
        }


    }



    /**
     * Creates a form to edit a Invoice entity.wq
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(BusinessInvoice $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();

        $form = $this->createForm(new CustomerInvoiceType($globalOption), $entity, array(
            'action' => $this->generateUrl('student_invoice_create', array('shop' => $globalOption->getSlug())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'id' => 'invoiceForm',
                'novalidate' => 'novalidate',
                'enctype' => 'multipart/form-data',

            )
        ));
        return $form;
    }

    public function createAction(Request $request)
    {
        $data = $request->request->all();
        $config = $this->getUser()->getGlobalOption();
        $user = $this->getUser()->getId();
        $entity = new BusinessInvoice();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('user'=>$user));
        $method = empty($entity->getTransactionMethod()) ? '' : $entity->getTransactionMethod()->getSlug();
        if (($form->isValid() && $method == 'cash') ||
            ($form->isValid() && $method == 'bank' && $entity->getAccountBank()) ||
            ($form->isValid() && $method == 'mobile' && $entity->getAccountMobileBank())
        ) {

            $em = $this->getDoctrine()->getManager();
            $entity->setBusinessConfig($config->getBusinessConfig());
            $entity->setCustomer($customer);
            $entity->setMobile($customer->getMobile());
            $entity->setPayment(520);
            $entity->setDue(520);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success', "Data has been inserted successfully"
            );
            $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoiceParticular')->insertStudentInvoiceParticular($entity);
            $this->getDoctrine()->getRepository( 'BusinessBundle:BusinessInvoice' )->updateInvoiceTotalPrice($entity);
            return $this->redirect($this->generateUrl('customer_business_dashboard', array('shop' => $config->getSlug())));
        }

    }

    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('user'=>$this->getUser()->getId()));
        $entity = new BusinessInvoice();
        $editForm = $this->createCreateForm($entity);
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $outstanding = 0;
        return $this->render("CustomerBundle:Invoice:new.html.twig", array(
            'globalOption' => $this->getUser()->getGlobalOption(),
            'customer' => $customer,
            'entity' => $entity,
            'outstanding' => $outstanding,
            'form' => $editForm->createView(),
        ));

    }
}
