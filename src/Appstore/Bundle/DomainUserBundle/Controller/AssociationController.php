<?php

namespace Appstore\Bundle\DomainUserBundle\Controller;

use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoice;
use Appstore\Bundle\BusinessBundle\Form\AssociationInvoiceType;
use Appstore\Bundle\DomainUserBundle\Form\MemberEditProfileType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Appstore\Bundle\DomainUserBundle\Form\CustomerType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Customer controller.
 *
 */
class AssociationController extends Controller
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
     * @Secure(roles="ROLE_CRM,ROLE_DOMAIN,ROLE_CRM_ASSOCIATION")
     */

    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $data['type'] = "member";
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('DomainUserBundle:Customer')->findWithSearch($globalOption,$data);
        $pagination = $this->paginate($entities);
        return $this->render('DomainUserBundle:Association:index.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
        ));
    }


    /**
     * Creates a new Customer entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Customer();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $globalOption = $this->getUser()->getGlobalOption();
            $entity->setGlobalOption($globalOption);
	        $mobile = $this->get('settong.toolManageRepo')->specialExpClean($entity->getMobile());
	        $entity->setMobile($mobile);
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('domain_customer'));
        }

        return $this->render('DomainUserBundle:Customer:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Customer entity.
     *
     * @param Customer $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Customer $entity)
    {
        $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $form = $this->createForm(new CustomerType($location), $entity, array(
            'action' => $this->generateUrl('domain_customer_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * @Secure(roles="ROLE_CRM,ROLE_DOMAIN")
     */
    public function newAction()
    {
        $entity = new Customer();
        $form   = $this->createCreateForm($entity);

        return $this->render('DomainUserBundle:Customer:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Customer entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption();
        $entity = $em->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $config,'id'=> $id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ElectionMember entity.');
        }
        $html = $this->renderView('DomainUserBundle:Association:profile.html.twig',
            array('entity' => $entity)
        );
        return New Response($html);
    }

     /**
     * Finds and displays a Customer entity.
     *
     */
    public function processAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption();
        $entity = $em->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $config, 'id' => $id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ElectionMember entity.');
        }
        if ($entity->getProcess() == 'Pending'){
            $entity->setProcess('Checked');
            $entity->setCheckedBy($this->getUser());
        }elseif ($entity->getProcess() == 'Checked'){
            $entity->setProcess('Approved');
            $entity->setApprovedBy($this->getUser());
        }
        $em->persist($entity);
        $em->flush();
        return New Response("success");
    }

    /**
     * Finds and displays a Customer entity.
     *
     */

    public function memberShowAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DomainUserBundle:Customer')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Customer entity.');
        }
        return $this->render('DomainUserBundle:Customer:memberShow.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * @Secure(roles="ROLE_CRM,ROLE_DOMAIN")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DomainUserBundle:Customer')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Customer entity.');
        }

        $editForm = $this->createEditForm($entity);
        return $this->render('DomainUserBundle:Association:editProfile.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Customer entity.
    *
    * @param Customer $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Customer $entity)
    {
        $em = $this->getDoctrine()->getRepository('DomainUserBundle:Customer');
        $form = $this->createForm(new MemberEditProfileType($em), $entity, array(
            'action' => $this->generateUrl('domain_association_profile_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing Customer entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DomainUserBundle:Customer')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Customer entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
	        $mobile = $this->get('settong.toolManageRepo')->specialExpClean($entity->getMobile());
	        $entity->setMobile($mobile);
            $em->flush();
            return $this->redirect($this->generateUrl('domain_customer'));
        }

        return $this->render('DomainUserBundle:Association:editProfile.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * @Secure(roles="ROLE_CRM,ROLE_DOMAIN")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $entity = $em->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption'=>$globalOption,'id' => $id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Customer entity.');
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
        }
        return $this->redirect($this->generateUrl('customer'));
    }

    public function autoSearchAction(Request $request)
    {
        $item = $_REQUEST['q'];
        if ($item) {
            $go = $this->getUser()->getGlobalOption();
            $item = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->searchAutoComplete($go,$item);
        }
        return new JsonResponse($item);
    }

    public function searchCustomerNameAction($customer)
    {
        return new JsonResponse(array(
            'id'=> $customer,
            'text' => $customer
        ));
    }

    public function autoMobileSearchAction(Request $request)
    {
        $item = $_REQUEST['q'];
        if ($item) {
            $go = $this->getUser()->getGlobalOption();
            $item = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->searchAutoCompleteName($go,$item);
        }
        return new JsonResponse($item);
    }

    public function searchCustomerMobileAction($customer)
    {
        return new JsonResponse(array(
            'id'=> $customer,
            'text' => $customer
        ));
    }

    public function autoCodeSearchAction(Request $request)
    {

        $q = $_REQUEST['term'];
        $option = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->searchAutoCompleteCode($option,$q);
        $items = array();
        foreach ($entities as $entity):
            $items[]=array('id' => $entity['customer'],'value' => $entity['text']);
        endforeach;
        return new JsonResponse($items);

    }

    public function searchCodeAction($customer)
    {
        return new JsonResponse(array(
            'id'=> $customer,
            'text' => $customer
        ));
    }


    public function autoLocationSearchAction(Request $request)
    {
        $item = $_REQUEST['q'];
        if ($item) {
            $item = $this->getDoctrine()->getRepository('SettingLocationBundle:Location')->searchAutoComplete($item);
        }
        return new JsonResponse($item);

    }

    public function searchLocationNameAction($location)
    {
        return new JsonResponse(array(
            'id'=> $location,
            'text' => $location
        ));
    }

    public function searchAutoCompleteNameAction()
    {
        $q = $_REQUEST['q'];
        $option = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->searchAutoCompleteName($option,$q);
        $items = array();
        foreach ($entities as $entity):
            $items[]=array('id' => $entity['id'],'value' => $entity['id']);
        endforeach;
        return new JsonResponse($entities);

    }

    public function searchAutoCompleteMobileAction()
    {
        $q = $_REQUEST['term'];
        $option = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->searchAutoComplete($option,$q);
        $items = array();
        foreach ($entities as $entity):
            $items[]=array('id' => $entity['customer'],'value' => $entity['id']);
        endforeach;
        return new JsonResponse($items);

    }

    public function invoiceNewAction($customer)
    {

        $em = $this->getDoctrine()->getManager();
        $option =  $this->getUser()->getGlobalOption();
        $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array("globalOption" => $option,"customerId" => $customer));

        $lastInvoice = $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoice')->getLastInvoiceParticular($customer);

        $entity = new BusinessInvoice();
        $editForm = $this->createInvoiceCreateForm($entity,$customer);
        $outstanding = 0;

        return $this->render("DomainUserBundle:Association/Invoice:new.html.twig", array(

            'globalOption'      => $this->getUser()->getGlobalOption(),
            'lastInvoice'       => $lastInvoice,
            'entity'            => $entity,
            'customer'          => $customer,
            'outstanding'       => $outstanding,
            'form'              => $editForm->createView(),

        ));

    }

    /**
     * Creates a form to edit a Invoice entity.wq
     *
     * @param BusinessInvoice $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createInvoiceCreateForm(BusinessInvoice $entity, Customer $customer)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new AssociationInvoiceType($globalOption), $entity, array(
            'action' => $this->generateUrl('domain_association_invoice_create', array('customer' => $customer->getCustomerId())),
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

    public function invoiceCreateAction(Request $request, $customer)
    {
        $data = $request->request->all();
        $user = $this->getUser();
        $config = $user->getGlobalOption()->getBusinessConfig();

        $option =  $this->getUser()->getGlobalOption();
        $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array("globalOption" => $option,"customerId" => $customer));
        $lastInvoice = $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoice')->getLastInvoiceParticular($customer);
        $entity = new BusinessInvoice();
        $form = $this->createInvoiceCreateForm($entity,$customer);
        $form->handleRequest($request);
        $method = empty($entity->getTransactionMethod()) ? '' : $entity->getTransactionMethod()->getSlug();
        if (($form->isValid() && $method == 'cash') ||
            ($form->isValid() && $method == 'bank' && $entity->getAccountBank()) ||
            ($form->isValid() && $method == 'mobile' && $entity->getAccountMobileBank())
        ) {
            $em = $this->getDoctrine()->getManager();
            $entity->setBusinessConfig($config);
            $entity->setCustomer($customer);
            $entity->setMobile($customer->getMobile());
            $entity->setReceived($data['paymentTotal']);
            $entity->setDue(0);
            $entity->setEndDate(new \DateTime("now"));
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success', "Data has been inserted successfully"
            );
            $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoiceParticular')->insertStudentMonthlyParticular($entity,$lastInvoice, $data);
            $this->getDoctrine()->getRepository( 'BusinessBundle:BusinessInvoice' )->updateInvoiceTotalPrice($entity);
            return $this->redirect($this->generateUrl('domain_association_invoice'));
        }
        $this->get('session')->getFlashBag()->add(
            'warning', "Payment information does not valid"
        );
        return $this->render("DomainUserBundle:Association/Invoice:new.html.twig", array(
            'globalOption' => $user->getGlobalOption(),
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    public function invoiceAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entities = $em->getRepository( 'BusinessBundle:BusinessInvoice' )->invoiceLists( $config->getId(),$data);
        $pagination = $this->paginate($entities);
        return $this->render("DomainUserBundle:Association/Invoice:index.html.twig", array(
            'entities' => $pagination,
            'searchForm' => $data,
        ));
    }


}
