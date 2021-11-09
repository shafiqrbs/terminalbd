<?php

namespace Appstore\Bundle\HospitalBundle\Controller;

use Appstore\Bundle\HospitalBundle\Entity\HmsInvoiceTemporaryParticular;
use Appstore\Bundle\HospitalBundle\Entity\Invoice;
use Appstore\Bundle\HospitalBundle\Entity\InvoiceParticular;
use Appstore\Bundle\HospitalBundle\Entity\Particular;
use Appstore\Bundle\HospitalBundle\Form\DoctorAppointmentType;
use Appstore\Bundle\HospitalBundle\Form\InvoiceType;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use Core\UserBundle\Entity\User;
use Frontend\FrontentBundle\Service\MobileDetect;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Invoice controller.
 *
 */
class DoctorAppointmentController extends Controller
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
     * @Secure(roles="ROLE_HOSPITAL,ROLE_DOMAIN");
     */

    public function appointmentInvoiceAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $hospital = $user->getGlobalOption()->getHospitalConfig();
        $entities = $em->getRepository('HospitalBundle:Invoice')->invoiceLists( $user , $mode = 'visit' , $data);
        $pagination = $this->paginate($entities);
        $assignDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->getFindWithParticular($hospital,array(5));
        $referredDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->getFindWithParticular($hospital,array(5,6));
        return $this->render('HospitalBundle:Prescription:index.html.twig', array(
            'entities'                          => $pagination,
            'assignDoctors'                     => $assignDoctors,
            'searchForm'                        => $data,
        ));

    }



    public function newAction()
    {
        $user = $this->getUser();
        $hospital = $user->getGlobalOption()->getHospitalConfig();
        $entity = new Invoice();
        $em = $this->getDoctrine()->getManager();
        $form = $this->createInvoiceCustomerForm($entity);
        $html = $this->renderView('HospitalBundle:Invoice:appointment.html.twig', array(
            'initialDiscount'   => 0,
            'user'   => $user,
            'entity'   => $entity,
            'form'   => $form->createView(),
        ));
        return New Response($html);
    }


    private function createInvoiceCustomerForm(Invoice $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $category = $this->getDoctrine()->getRepository('HospitalBundle:HmsCategory');
        $form = $this->createForm(new DoctorAppointmentType($globalOption,$category), $entity, array(
            'action' => $this->generateUrl('hms_doctor_visit_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal',
                'id' => 'appointmentPatientForm',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = New Invoice();
        $user = $this->getUser();
        $option = $user->getGlobalOption();
        $hospital = $option->getHospitalConfig();
        $editForm = $this->createInvoiceCustomerForm($entity);
        $editForm->handleRequest($request);
        $data = $request->request->all()['appointment_invoice'];
        $entity->setHospitalConfig($hospital);
        $assignDoctor = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->find($data['assignDoctor']);
        $entity->setAssignDoctor($assignDoctor);
        $transactionMethod = $em->getRepository('SettingToolBundle:TransactionMethod')->find(1);
        $entity->setTransactionMethod($transactionMethod);
        $entity->setInvoiceMode('visit');
        $entity->setPrintFor('visit');
        $entity->setCreatedBy($this->getUser());
        if (!empty($data['customer']['name'])) {
            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['customer']['mobile']);
            $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findHmsExistingCustomerDiagnostic($this->getUser()->getGlobalOption(), $mobile,$data);
            $entity->setCustomer($customer);
            $entity->setMobile($mobile);
        }
        $entity->setSubTotal($assignDoctor->getPrice());
        $entity->setTotal($assignDoctor->getPrice());
        $entity->setPaymentStatus("Paid");
        $entity->setDue(0);
	    $entity->setProcess('In-progress');
        $amountInWords = $this->get('settong.toolManageRepo')->intToWords($entity->getTotal());
        $entity->setPaymentInWord($amountInWords);
        $em->persist($entity);
        $em->flush();
        return new Response($entity->getId());

    }

    public function doctorVisitAmountAction(Particular $particular)
    {
        return new Response($particular->getPrice());
    }


}

