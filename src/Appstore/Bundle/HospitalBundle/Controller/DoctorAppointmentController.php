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
        $form = $this->createForm(new DoctorAppointmentType($globalOption), $entity, array(
            'action' => $this->generateUrl('hms_doctor_visit_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal',
                'id' => 'invoicePatientForm',
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
        if($entity->getTotal() > 0 and $entity->getPayment() >= $entity->getTotal() ){
	        $entity->setPayment($entity->getTotal());
	        $entity->setPaymentStatus("Paid");
	        $entity->setDue(0);
        }
	    $entity->setProcess('In-progress');
        $amountInWords = $this->get('settong.toolManageRepo')->intToWords($entity->getTotal());
        $entity->setPaymentInWord($amountInWords);
        $em->persist($entity);
        $em->flush();
        return new Response($entity->getId());
        exit;

    }

    public function doctorVisitAmountAction(Particular $particular)
    {
        return new Response($particular->getPrice());
    }


}

