<?php

namespace Terminalbd\ReportBundle\Controller;

use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/accounting")
 * @Security("is_granted('ROLE_DOMAIN') or is_granted('ROLE_REPOTRT_ACCOUNTING')")
 */
class AccountingController extends Controller
{


    public function paginate($entities)
    {

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            25  /*limit per page*/
        );
        return $pagination;
    }

    /**
     * @Route("/dashboard", name="accounting_report_sales_dashboard")
     * @Secure(roles="ROLE_ACCOUNTING_REPORT,ROLE_ACCOUNTING_REPORT_SALES,ROLE_DOMAIN")
     */
    public function dashboardAction()
    {
        echo "Test";
        exit;
       // return $this->render('ReportBundle:Default:index.html.twig', array('name' => $name));
    }


    /**
     * @Route("/customer-outstanding", methods={"GET", "POST"}, name="accounting_report_sales_outstanding")
     * @Secure(roles="ROLE_ACCOUNTING_REPORT,ROLE_ACCOUNTING_REPORT_SALES, ROLE_DOMAIN")
     */
    public function customerOutstandingAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $data =$_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->customerOutstanding($globalOption,$data);
        return $this->render('ReportBundle:Accounting/Sales:customerOutstanding.html.twig', array(
            'option' => $globalOption,
            'entities' => $entities,
            'searchForm' => $data,
        ));
    }


    /**
     * @Route("/customer-summary", methods={"GET", "POST"}, name="accounting_report_sales_summary")
     * @Secure(roles="ROLE_ACCOUNTING_REPORT,ROLE_ACCOUNTING_REPORT_SALES, ROLE_DOMAIN")
     */

    public function customerSummaryAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->customerSummary($globalOption,$data);
        return $this->render('ReportBundle:Accounting/Sales:customerSummary.html.twig', array(
            'entities' => $entities,
            'option' => $globalOption,
            'searchForm' => $data,
        ));

    }

    /**
     * @Route("/customer-ledger", methods={"GET", "POST"}, name="accounting_report_sales_customer_ledger")
     * @Secure(roles="ROLE_ACCOUNTING_REPORT,ROLE_ACCOUNTING_REPORT_SALES, ROLE_DOMAIN")
     */

    public function customerLedgerAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = '';
        $customer = "";
        if(isset($data['submit']) and $data['submit'] == 'search' and isset($data['customer']) and $data['customer']) {
            if(isset($data['mobile']) and !empty($data['mobile'])){
                $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('mobile'=>$data['mobile']));
                $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->salesOverview($user,$data);
            }
            $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $globalOption,'mobile'=> $customerId));
            $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->customerLedger($globalOption,$data);
        }
        return $this->render('ReportBundle:Accounting/Sales:ledger.html.twig', array(
                'entities' => $entities,
                'customer' => $customer,
                'option' => $globalOption,
                'searchForm' => $data,
        ));

    }

    /**
     * @Route("/user-sales-collection-summary", methods={"GET", "POST"}, name="accounting_report_sales_user_summary")
     * @Secure(roles="ROLE_ACCOUNTING_REPORT,ROLE_ACCOUNTING_REPORT_SALES, ROLE_DOMAIN")
     */

    public function userSummaryAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->userSummary($globalOption,$data);
        return $this->render('ReportBundle:Accounting/Sales:userSummary.html.twig', array(
                'entities' => $entities,
                'option' => $globalOption,
                'searchForm' => $data,
        ));

    }

    /**
     * @Route("/user-sales-collection-details", methods={"GET", "POST"}, name="accounting_report_sales_user_details")
     * @Secure(roles="ROLE_ACCOUNTING_REPORT,ROLE_ACCOUNTING_REPORT_SALES, ROLE_DOMAIN")
     */

    public function userSalesDetailsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = "";
        if(isset($data['submit']) and $data['submit'] == 'search' and isset($data['user']) and $data['user']) {
            $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->customerSummary($globalOption,$data);
        }
        $employees = $em->getRepository('UserBundle:User')->getEmployees($globalOption);
        return $this->render('ReportBundle:Accounting/Sales:customerDetails.html.twig', array(
            'entities' => $entities,
            'employees' => $employees,
            'option' => $globalOption,
            'searchForm' => $data,
        ));
    }

}
