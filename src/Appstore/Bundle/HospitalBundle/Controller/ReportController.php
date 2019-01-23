<?php

namespace Appstore\Bundle\HospitalBundle\Controller;

use Knp\Snappy\Pdf;
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
        $pagination->setTemplate('SettingToolBundle:Widget:pagination.html.twig');
        return $pagination;
    }

    public function salesSummaryAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;

        $user = $this->getUser();

        $salesTotalTransactionOverview = $em->getRepository('HospitalBundle:InvoiceTransaction')->todaySalesOverview($user,$data);
        $salesTodayTransactionOverview = $em->getRepository('HospitalBundle:InvoiceTransaction')->todaySalesOverview($user,$data,'false',array('diagnostic','admission'));
        $previousSalesTransactionOverview = $em->getRepository('HospitalBundle:InvoiceTransaction')->todaySalesOverview($user,$data,'true',array('diagnostic','admission'));
        $diagnosticOverview = $em->getRepository('HospitalBundle:Invoice')->findWithSalesOverview($user,$data,'diagnostic');
        $admissionOverview = $em->getRepository('HospitalBundle:Invoice')->findWithSalesOverview($user,$data,'admission');
        $serviceOverview = $em->getRepository('HospitalBundle:Invoice')->findWithServiceOverview($user,$data);
        $transactionOverview = $em->getRepository('HospitalBundle:InvoiceTransaction')->findWithTransactionOverview($user,$data);
        $commissionOverview = $em->getRepository('HospitalBundle:Invoice')->findWithCommissionOverview($user,$data);

        return $this->render('HospitalBundle:Report:salesSumary.html.twig', array(

            'salesTotalTransactionOverview'      => $salesTotalTransactionOverview,
            'salesTodayTransactionOverview'      => $salesTodayTransactionOverview,
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
        $serviceGroup = $em->getRepository('HospitalBundle:InvoiceParticular')->serviceParticularDetails($user,$data);
        return $this->render('HospitalBundle:Report:serviceBaseSales.html.twig', array(
            'serviceGroup'          => $serviceGroup,
            'services'              => $services,
            'entity'                => $entity,
            'searchForm'            => $data,
        ));
    }

    public function salesDetailsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;

        $user = $this->getUser();
        $hospital = $user->getGlobalOption()->getHospitalConfig();
        if(empty($data['created'])){
            $datetime = new \DateTime("now");
            $data['created'] = $datetime->format('Y-m-d');
        }
        $entities = $em->getRepository('HospitalBundle:Invoice')->invoiceLists( $user , $mode = 'diagnostic' , $data);
        $pagination = $entities->getQuery()->getResult();
        $commissionSummary = $this->getDoctrine()->getRepository('HospitalBundle:DoctorInvoice')->getInvoiceBaseCommissionSummary($hospital , $entities);
        $commissions = $this->getDoctrine()->getRepository('HospitalBundle:DoctorInvoice')->getInvoiceBaseCommission($hospital , $entities);
        $referredDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->getFindWithParticular($hospital,array(6));
        $commissionEntity = $this->getDoctrine()->getRepository('HospitalBundle:HmsCommission')->findBy(array('hospitalConfig'=>$hospital));
        return $this->render('HospitalBundle:Report/Sales:details.html.twig', array(
            'entities' => $pagination,
            'commissions' => $commissions,
            'commissionSummary' => $commissionSummary,
            'commissionEntity' => $commissionEntity,
            'referredDoctors' => $referredDoctors,
            'searchForm' => $data,
        ));
    }

    public function salesDetailsPdfAction()
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $data = $_REQUEST;
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;

        $user = $this->getUser();
        $hospital = $user->getGlobalOption()->getHospitalConfig();
        $datetime = new \DateTime("now");
        if(empty($data['created'])){
            $data['created'] = $datetime->format('Y-m-d');
        }

        $entities = $em->getRepository('HospitalBundle:Invoice')->invoiceLists( $user , $mode = 'diagnostic' , $data);
        $pagination = $entities->getQuery()->getResult();
        $commissions = $this->getDoctrine()->getRepository('HospitalBundle:DoctorInvoice')->getInvoiceBaseCommission($hospital , $entities);
        $referredDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->getFindWithParticular($hospital,array(6));
        $commissionEntity = $this->getDoctrine()->getRepository('HospitalBundle:HmsCommission')->findBy(array('hospitalConfig'=>$hospital));
        $commissionSummary = $this->getDoctrine()->getRepository('HospitalBundle:DoctorInvoice')->getInvoiceBaseCommissionSummary($hospital , $entities);
        $html = $this->renderView(
            'HospitalBundle:Report/Sales:detailsPdf.html.twig', array(
                'globalOption' => $user->getGlobalOption(),
                'entities' => $pagination,
                'commissionSummary' => $commissionSummary,
                'commissions' => $commissions,
                'commissionEntity' => $commissionEntity,
                'referredDoctors' => $referredDoctors,
                'searchForm' => $data,
            )
        );
        $date = $datetime->format('d-m-y');
        $date = "{$date}-daily-sales.pdf";
        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy          = new Pdf($wkhtmltopdfPath);
        $pdf             = $snappy->getOutputFromHtml($html);
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="'.$date.'"');
        echo $pdf;
        return new Response('');
    }

    public function referredCommissionAction()
    {

    }

}

