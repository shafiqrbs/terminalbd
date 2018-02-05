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

        $salesTotalTransactionOverview = $em->getRepository('DmsBundle:DmsTreatmentPlan')->todaySalesOverview($user,$data);
        $salesTransactionOverview = $em->getRepository('HospitalBundle:InvoiceTransaction')->todaySalesOverview($user,$data,'true');
        $previousSalesTransactionOverview = $em->getRepository('HospitalBundle:InvoiceTransaction')->todaySalesOverview($user,$data,'false');

        $diagnosticOverview = $em->getRepository('HospitalBundle:Invoice')->findWithSalesOverview($user,$data,$mode = 'diagnostic');
        $admissionOverview = $em->getRepository('HospitalBundle:Invoice')->findWithSalesOverview($user,$data,$mode = 'admission');
        $serviceOverview = $em->getRepository('HospitalBundle:Invoice')->findWithServiceOverview($user,$data);
        $transactionOverview = $em->getRepository('HospitalBundle:InvoiceTransaction')->findWithTransactionOverview($user,$data);
        $commissionOverview = $em->getRepository('HospitalBundle:Invoice')->findWithCommissionOverview($user,$data);

        return $this->render('HospitalBundle:Report:salesSumary.html.twig', array(

            'salesTotalTransactionOverview'      => $salesTotalTransactionOverview,
            'salesTransactionOverview'      => $salesTransactionOverview,
            'previousSalesTransactionOverview' => $previousSalesTransactionOverview,
            'diagnosticOverview'            => $diagnosticOverview,
            'admissionOverview'             => $admissionOverview,
            'serviceOverview'               => $serviceOverview,
            'transactionOverview'           => $transactionOverview,
            'commissionOverview'            => $commissionOverview,
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






}

