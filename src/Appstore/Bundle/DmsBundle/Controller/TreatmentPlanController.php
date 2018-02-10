<?php

namespace Appstore\Bundle\DmsBundle\Controller;
use Knp\Snappy\Pdf;
use Appstore\Bundle\DmsBundle\Entity\DmsInvoice;
use Appstore\Bundle\DmsBundle\Entity\DmsInvoiceMedicine;
use Appstore\Bundle\DmsBundle\Entity\DmsInvoiceParticular;
use Appstore\Bundle\DmsBundle\Entity\DmsParticular;
use Appstore\Bundle\DmsBundle\Entity\DmsTreatmentPlan;
use Appstore\Bundle\DmsBundle\Form\InvoiceType;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use Frontend\FrontentBundle\Service\MobileDetect;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * DmsInvoiceController controller.
 *
 */
class TreatmentPlanController extends Controller
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

    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $dmsConfig = $user->getGlobalOption()->getDmsConfig();
        $treatmentSchedule  = $em->getRepository('DmsBundle:DmsTreatmentPlan')->findTodaySchedule($dmsConfig,$data);
        $assignDoctors = $this->getDoctrine()->getRepository('DmsBundle:DmsParticular')->getFindWithParticular($dmsConfig,array('doctor'));

        return $this->render('DmsBundle:Invoice:treatmentSchedule.html.twig', array(
            'treatmentSchedule' => $treatmentSchedule,
            'assignDoctors' => $assignDoctors,
        ));

    }

    public function sendSmsPlanAction($patient, DmsTreatmentPlan $plan)
    {
        $em = $this->getDoctrine()->getManager();
        $dispatcher = $this->container->get('event_dispatcher');
        $dispatcher->dispatch('setting_tool.post.dms_treatment_plan_sms', new \Setting\Bundle\ToolBundle\Event\DmsTreatmentPlanSmsEvent($plan));
        $plan->setSendSms(1);
        $em->flush();
        exit;
    }

    public function appointmentDateScheduleAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $appointmentDate = isset($data['schedule']) ? $data['schedule'] : '';
        $curDate =  New \DateTime("now");
        $curDate = $curDate->format('d-m-Y');
        $appointmentDate = !empty($appointmentDate)? $appointmentDate :(string)$curDate;
        $data = array('appointmentDate' => $appointmentDate);
        $user = $this->getUser();
        $dmsConfig = $user->getGlobalOption()->getDmsConfig();
     //   $appointments = $this->getDoctrine()->getRepository('DmsBundle:DmsTreatmentPlan')->appointmentDate($dmsConfig,$appointmentDate);
        $treatmentSchedule  = $em->getRepository('DmsBundle:DmsTreatmentPlan')->findTodaySchedule($dmsConfig,$data);
        $html =  $this->renderView('DmsBundle:Invoice:schedule-plan.html.twig',
            array('treatmentSchedule'=> $treatmentSchedule)
        );
        return  New Response($html);
        exit;
    }

    public function dateWiseScheduleAction(Request $request)
    {

        $data = $request->request->all();
        $appointmentDate = isset($data['dateSchedule']) ? $data['dateSchedule'] : '';
        $user = $this->getUser();
        $dmsConfig = $user->getGlobalOption()->getDmsConfig();
        $appointments = $this->getDoctrine()->getRepository('DmsBundle:DmsTreatmentPlan')->appointmentDate($dmsConfig,$appointmentDate);
        return  New Response($appointments);
        exit;
    }


}

