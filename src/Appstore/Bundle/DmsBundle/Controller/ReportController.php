<?php


namespace Appstore\Bundle\DmsBundle\Controller;


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
class ReportController extends Controller
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

    public function salesSummaryAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;

        $user = $this->getUser();
        $config = $user->getGlobalOption()->getDmsConfig();
        $salesTotalTransactionOverview = $em->getRepository('DmsBundle:DmsTreatmentPlan')->transactionOverview($config,$data);
        $serviceOverview = $em->getRepository('DmsBundle:DmsTreatmentPlan')->findWithServiceOverview($config,$data);
        return $this->render('DmsBundle:Report:salesSumary.html.twig', array(

            'salesOverview'      => $salesTotalTransactionOverview,
            'serviceOverview'               => $serviceOverview,
            'searchForm'                    => $data,

        ));

    }
    public function serviceBaseSummaryAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;

        $user = $this->getUser();
        $entity = '';
        if(!empty($data) and $data['service']){
            $entity = $em->getRepository('HospitalBundle:Service')->find($data['service']);
        }
        $services = $em->getRepository('HospitalBundle:Service')->findBy(array(),array('name'=>'ASC'));
        $serviceOverview = $em->getRepository('HospitalBundle:Invoice')->findWithServiceOverview($user,$data);
        $serviceGroup = $em->getRepository('HospitalBundle:InvoiceParticular')->serviceParticularDetails($user,$data);
        return $this->render('HospitalBundle:Report:serviceBaseSales.html.twig', array(
            'serviceOverview'       => $serviceOverview,
            'serviceGroup'          => $serviceGroup,
            'services'              => $services,
            'entity'                => $entity,
            'searchForm'            => $data,
        ));

    }

    public function allyearlySalesAction()
    {
        $data = $_REQUEST;
        $user = $this->getUser();
        $dmsConfig = $user->getGlobalOption()->getDmsConfig();
        $dailyReceive = $this->getDoctrine()->getRepository('DmsBundle:DmsTreatmentPlan')->allYearlySales($dmsConfig,$data);
        return $this->render('DmsBundle:Report:allYearlySales.html.twig', array(
            'entities' => $dailyReceive,
            'searchForm' => $data,
        ));

    }

    public function yearlySalesAction()
    {
        $data = $_REQUEST;
        $user = $this->getUser();
        $dmsConfig = $user->getGlobalOption()->getDmsConfig();
        $dailyReceive = $this->getDoctrine()->getRepository('DmsBundle:DmsTreatmentPlan')->yearlySales($dmsConfig,$data);
        return $this->render('DmsBundle:Report:yearlySales.html.twig', array(
            'entities' => $dailyReceive,
            'searchForm' => $data,
        ));

    }

    public function monthlySalesAction()
    {
        $data = $_REQUEST;
        $user = $this->getUser();
        $dmsConfig = $user->getGlobalOption()->getDmsConfig();
        $dailyReceive = $this->getDoctrine()->getRepository('DmsBundle:DmsTreatmentPlan')->monthlySales($dmsConfig,$data);
        return $this->render('DmsBundle:Report:monthlySales.html.twig', array(
            'entities' => $dailyReceive,
            'searchForm' => $data,
        ));

    }

    public function salesAction()
    {
        $data = $_REQUEST;
        $user = $this->getUser();
        $config = $user->getGlobalOption()->getDmsConfig();
        $dailyReceive = $this->getDoctrine()->getRepository('DmsBundle:DmsTreatmentPlan')->salesDetails($config,$data);
        $assignDoctors = $this->getDoctrine()->getRepository('DmsBundle:DmsParticular')->getFindWithParticular($config,array('doctor'));
        $treatments = $this->getDoctrine()->getRepository('DmsBundle:DmsParticular')->getFindDentalServiceParticular($config,array('treatment'));

        return $this->render('DmsBundle:Report:sales.html.twig', array(
            'entities' => $dailyReceive,
            'assignDoctors' => $assignDoctors,
            'treatments' => $treatments,
            'searchForm' => $data,
        ));

    }

    public function treatmentWiseSalesAction()
    {
        $data = $_REQUEST;
        $user = $this->getUser();
        $dmsConfig = $user->getGlobalOption()->getDmsConfig();
        $entities = $this->getDoctrine()->getRepository('DmsBundle:DmsTreatmentPlan')->dailySales($dmsConfig,$data);
        $assignDoctors = $this->getDoctrine()->getRepository('DmsBundle:DmsParticular')->getFindWithParticular($dmsConfig,array('doctor'));
        $treatments = $this->getDoctrine()->getRepository('DmsBundle:DmsParticular')->getFindWithParticular($config,array('treatment'));

        return $this->render('DmsBundle:Report:treatmentWiseSales.html.twig', array(
            'entities' => $entities,
            'assignDoctors' => $assignDoctors,
            'treatments' => $treatments,
            'searchForm' => $data,
        ));

    }






}

