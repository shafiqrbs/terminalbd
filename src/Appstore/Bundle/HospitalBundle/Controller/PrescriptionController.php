<?php

namespace Appstore\Bundle\HospitalBundle\Controller;

use Appstore\Bundle\HospitalBundle\Entity\Invoice;
use Appstore\Bundle\HospitalBundle\Entity\InvoiceParticular;
use Appstore\Bundle\HospitalBundle\Entity\InvoiceTransaction;
use Appstore\Bundle\HospitalBundle\Entity\Particular;
use Appstore\Bundle\HospitalBundle\Form\DoctorAppointmentType;
use Appstore\Bundle\HospitalBundle\Form\InvoiceAdmissionType;
use Appstore\Bundle\HospitalBundle\Form\InvoiceType;
use Appstore\Bundle\HospitalBundle\Form\NewPatientAdmissionType;
use Appstore\Bundle\HospitalBundle\Form\PatientAdmissionType;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use Frontend\FrontentBundle\Service\MobileDetect;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Invoice controller.
 *
 */
class PrescriptionController extends Controller
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

    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $hospital = $user->getGlobalOption()->getHospitalConfig();
        $entities = $em->getRepository('HospitalBundle:Invoice')->invoiceLists( $user , $mode = 'visit' , $data);
        $pagination = $this->paginate($entities);
        $assignDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->getFindWithParticular($hospital,array(5));
        return $this->render('HospitalBundle:Prescription:index.html.twig', array(
            'entities'                          => $pagination,
            'assignDoctors'                     => $assignDoctors,
            'searchForm'                        => $data,
        ));

    }

    public function newAction()
    {
        $user = $this->getUser();
        $entity = new Invoice();
        $form = $this->createInvoiceCustomerForm($entity);
        return $this->render('HospitalBundle:Prescription:new.html.twig', array(
            'initialDiscount'   => 0,
            'user'   => $user,
            'entity'   => $entity,
            'form'   => $form->createView(),
        ));
    }

    private function createInvoiceCustomerForm(Invoice $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $category = $this->getDoctrine()->getRepository('HospitalBundle:HmsCategory');
        $form = $this->createForm(new DoctorAppointmentType($globalOption,$category), $entity, array(
            'action' => $this->generateUrl('hms_doctor_visit_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
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
        return $this->redirect($this->generateUrl('hms_prescription'));
    }

    public function doctorVisitAmountAction(Particular $particular)
    {
        return new Response($particular->getPrice());
    }


    public function oldPatientAction(Request $request)
    {
        $customerId = $request->request->get('customer');
        $option = $this->getUser()->getGlobalOption();
        $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption'=>$option,'mobile'=>$customerId));
        $em = $this->getDoctrine()->getManager();
        $entity = new Invoice();
        $hospital = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $entity->setHospitalConfig($hospital);
        $entity->setInvoiceMode('admission');
        $entity->setPrintFor('admission');
        $entity->setCreatedBy($this->getUser());
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('hms_prescription_edit', array('id' => $entity->getId())));

    }


    public function patientInvoiceAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $hospital = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $entity = $em->getRepository('HospitalBundle:Invoice')->findOneBy(array('hospitalConfig' => $hospital , 'id' => $id));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $editForm = $this->createEditForm($entity);
        $services        = $em->getRepository('HospitalBundle:Particular')->getServices($hospital,array(2,3,4,8,7));
        $referredDoctors = $em->getRepository('HospitalBundle:Particular')->findBy(array('hospitalConfig' => $hospital,'status' => 1,'service' => 6),array('name'=>'ASC'));
        return $this->render('HospitalBundle:InvoiceAdmission:new.html.twig', array(
            'entity' => $entity,
            'particularService' => $services,
            'referredDoctors' => $referredDoctors,
            'admissionForm' => 'hide',
            'form' => $editForm->createView(),
        ));
    }

    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $hospital = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $entity = $em->getRepository('HospitalBundle:Invoice')->findOneBy(array('hospitalConfig' => $hospital , 'id' => $id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $editForm = $this->createEditForm($entity);
        return $this->render('HospitalBundle:Prescription:new.html.twig', array(
            'entity' => $entity,
            'searchForm' => 'hide',
            'form' => $editForm->createView(),
        ));
    }


    /**
     * Creates a form to edit a Invoice entity.wq
     *
     * @param Invoice $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Invoice $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $category = $this->getDoctrine()->getRepository('HospitalBundle:HmsCategory');
        $form = $this->createForm(new DoctorAppointmentType($globalOption,$category), $entity, array(
            'action' => $this->generateUrl('hms_prescription_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'id' => 'invoiceForm',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    public function updateAction(Request $request, Invoice $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            $em->flush();
            return $this->redirect($this->generateUrl('hms_prescription_edit', array('id' => $entity->getId())));
        }
        return $this->render('HospitalBundle:Prescription:new.html.twig', array(
            'entity' => $entity,
            'searchForm' => 'hide',
            'form' => $editForm->createView(),
        ));
    }

    public function particularSearchAction()
    {
        $id = $_REQUEST['id'];
        $particular = $this->getDoctrine()->getRepository("HospitalBundle:Particular")->find($id);
        $quantity = $particular->getQuantity() > 0 ? $particular->getQuantity() :1;
        return new Response(json_encode(array('particularId'=> $particular->getId() ,'price'=> $particular->getPrice() , 'quantity'=> $quantity, 'minimumPrice'=> $particular->getMinimumPrice(), 'instruction'=> $particular->getInstruction())));
    }

    public function getBarcode($invoice)
    {
        $barcode = new BarcodeGenerator();
        $barcode->setText($invoice);
        $barcode->setType(BarcodeGenerator::Code39Extended);
        $barcode->setScale(1);
        $barcode->setThickness(25);
        $barcode->setFontSize(8);
        $code = $barcode->generate();
        $data = '';
        $data .= '<img src="data:image/png;base64,'.$code .'" />';
        return $data;
    }

    public function deleteAction(Request $request)
    {
    }
    public function printAction(Request $request)
    {
    }
    public function generateAction(Request $request)
    {
    }
    public function showAction(Request $request)
    {
    }



}

