<?php

namespace Appstore\Bundle\HospitalBundle\Controller;

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
class AdmissionPatientController extends Controller
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

}

