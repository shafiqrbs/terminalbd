<?php

namespace Appstore\Bundle\RestaurantBundle\Controller;

use Appstore\Bundle\RestaurantBundle\Entity\Invoice;
use Appstore\Bundle\RestaurantBundle\Entity\InvoiceParticular;
use Appstore\Bundle\RestaurantBundle\Entity\Particular;
use Appstore\Bundle\RestaurantBundle\Form\InvoiceType;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
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

        $salesTotalTransactionOverview = $em->getRepository('RestaurantBundle:InvoiceTransaction')->todaySalesOverview($user,$data);
        $salesTransactionOverview = $em->getRepository('RestaurantBundle:InvoiceTransaction')->todaySalesOverview($user,$data,'true');
        $previousSalesTransactionOverview = $em->getRepository('RestaurantBundle:InvoiceTransaction')->todaySalesOverview($user,$data,'false');

        $diagnosticOverview = $em->getRepository('RestaurantBundle:Invoice')->findWithSalesOverview($user,$data,$mode = 'diagnostic');
        $admissionOverview = $em->getRepository('RestaurantBundle:Invoice')->findWithSalesOverview($user,$data,$mode = 'admission');
        $serviceOverview = $em->getRepository('RestaurantBundle:Invoice')->findWithServiceOverview($user,$data);
        $transactionOverview = $em->getRepository('RestaurantBundle:InvoiceTransaction')->findWithTransactionOverview($user,$data);
        $commissionOverview = $em->getRepository('RestaurantBundle:Invoice')->findWithCommissionOverview($user,$data);

        return $this->render('RestaurantBundle:Report:salesSumary.html.twig', array(

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
            $entity = $em->getRepository('RestaurantBundle:Service')->find($data['service']);
        }
        $services = $em->getRepository('RestaurantBundle:Service')->findBy(array(),array('name'=>'ASC'));
        $serviceOverview = $em->getRepository('RestaurantBundle:Invoice')->findWithServiceOverview($user,$data);
        $serviceGroup = $em->getRepository('RestaurantBundle:InvoiceParticular')->serviceParticularDetails($user,$data);
        return $this->render('RestaurantBundle:Report:serviceBaseSales.html.twig', array(
            'serviceOverview'       => $serviceOverview,
            'serviceGroup'          => $serviceGroup,
            'services'              => $services,
            'entity'                => $entity,
            'searchForm'            => $data,
        ));

    }

    



}

