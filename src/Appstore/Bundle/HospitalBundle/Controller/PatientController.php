<?php

namespace Appstore\Bundle\HospitalBundle\Controller;

use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Appstore\Bundle\HospitalBundle\Entity\HmsCategory;
use Appstore\Bundle\HospitalBundle\Entity\Invoice;
use Appstore\Bundle\HospitalBundle\Entity\InvoiceParticular;
use Appstore\Bundle\HospitalBundle\Entity\InvoiceTransaction;
use Appstore\Bundle\HospitalBundle\Entity\Particular;
use Appstore\Bundle\HospitalBundle\Form\InvoiceAdmissionType;
use Appstore\Bundle\HospitalBundle\Form\InvoicePaymentType;
use Appstore\Bundle\HospitalBundle\Form\NewPatientAdmissionType;
use Appstore\Bundle\HospitalBundle\Form\PatientAdmissionType;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Invoice controller.
 *
 */
class PatientController extends Controller
{

    public function patientAction(Invoice $entity)
    {

        $inventory = $this->getUser()->getGlobalOption()->getHospitalConfig()->getId();
        if ($inventory == $entity->getHospitalConfig()->getId()) {
            return $this->render('HospitalBundle:AdmissionPatient:index.html.twig', array(
                'entity' => $entity,
                'option' => $this->getUser()->getGlobalOption(),
            ));
        }else{
            return $this->redirect($this->generateUrl('hms_invoice_admission'));
        }
    }

    public function invoiceSearchAction(Request $request)
    {
        $item = $_REQUEST['q'];
        if ($item) {
            $inventory = $this->getUser()->getGlobalOption()->getHospitalConfig();
            $item = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->searchAutoComplete($item,$inventory);
        }
        return new JsonResponse($item);
    }

    public function invoiceAdmissionSearchAction(Request $request)
    {
        $item = $_REQUEST['q'];
        if ($item) {
            $inventory = $this->getUser()->getGlobalOption()->getHospitalConfig();
            $item = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->searchAdmissionAutoComplete($item,$inventory);
        }
        return new JsonResponse($item);
    }

    public function patientGlobalDetailsAction($invoice)
    {
        $config = $this->getUser()->getGlobalOption()->getHospitalConfig()->getId();
        $entity = $this->getDoctrine()->getRepository(Invoice::class)->findOneBy(array('hospitalConfig' => $config,'invoice' => $invoice));
        if ($entity) {
            $view =  $this->renderView('HospitalBundle:Patient:ajax-show.html.twig', array(
                'entity' => $entity,
            ));
            return new Response($view);
        } else {
            return new Response("No Record Found!");
        }
    }

    public function patientInfoAction($invoice)
    {
        $config = $this->getUser()->getGlobalOption()->getHospitalConfig()->getId();
        $entity = $this->getDoctrine()->getRepository(Invoice::class)->findOneBy(array('hospitalConfig' => $config,'invoice' => $invoice));
        if ($entity) {
            $customer = $entity->getCustomer();
            $data = array(
                'id' => $customer->getId(),
                'patientId' => $customer->getCustomerId(),
                'name' => $customer->getName(),
                'mobile' => $customer->getMobile(),
                'age' => $customer->getAge(),
                'ageType' => $customer->getAgeType(),
                'gender' => $customer->getGender(),
                'height' => $customer->getHeight(),
                'weight' => $customer->getWeight(),
                'bloodPressure' => $customer->getBloodPressure(),
                'address' => $customer->getAddress()
            );
        }
        return new Response(json_encode($data));

    }



}

