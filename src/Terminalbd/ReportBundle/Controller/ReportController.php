<?php

namespace Terminalbd\ReportBundle\Controller;

use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/mis")
 * @Security("is_granted('ROLE_DOMAIN') or is_granted('ROLE_REPOTRT_ACCOUNTING')")
 */
class ReportController extends Controller
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
     * @Route("/dashboard", methods={"GET", "POST"}, name="report_dashboard")
     * @Secure(roles="ROLE_REPORT,ROLE_DOMAIN")
     */
    public function indexAction()
    {

        $data = $_REQUEST;
        if(empty($data)){
            $date = new \DateTime("now");
            $start = $date->format('d-m-Y');
            $end = $date->format('d-m-Y');
            $data = array('startDate'=> $start , 'endDate' => $end);
        }
        $todayCustomerSales = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->dailySalesReceive($this->getUser(),$data);
        $todayVendorSales = $this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->dailyPurchasePayment($this->getUser(),$data);
        $todayExpense = $this->getDoctrine()->getRepository('AccountingBundle:Expenditure')->dailyPurchasePayment($this->getUser(),$data);
        $todayJournal = $this->getDoctrine()->getRepository('AccountingBundle:AccountJournalItem')->dailyJournal($this->getUser(),$data);
        $todayLoan = $this->getDoctrine()->getRepository('AccountingBundle:AccountLoan')->dailyLoan($this->getUser(),$data);
        $transactionMethods = array(1,4);
        $globalOption = $this->getUser()->getGlobalOption();
        $transactionCashOverview = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->transactionWiseOverview( $this->getUser(),$data);
        $transactionBankCashOverviews = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->transactionBankCashOverview( $this->getUser(),$data);
        $transactionMobileBankCashOverviews = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->transactionMobileBankCashOverview( $this->getUser(),$data);
        $transactionAccountHeadCashOverviews = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->transactionAccountHeadCashOverview( $this->getUser(),$data);
        $employees = $this->getDoctrine()->getRepository('UserBundle:User')->getEmployees($globalOption);
        return $this->render('ReportBundle:Default:index.html.twig', array(
            'transactionCashOverviews'                  => $transactionCashOverview,
            'transactionBankCashOverviews'              => $transactionBankCashOverviews,
            'transactionMobileBankCashOverviews'        => $transactionMobileBankCashOverviews,
            'transactionAccountHeadCashOverviews'       => $transactionAccountHeadCashOverviews,
            'todayCustomerSales'       => $todayCustomerSales,
            'todayVendorSales'       => $todayVendorSales,
            'todayExpense'       => $todayExpense,
            'todayJournal'       => $todayJournal,
            'todayLoan'       => $todayLoan,
            'employees'       => $employees,
            'option' => $globalOption,
            'searchForm' => $data,
        ));

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
     * @Route("/cash-flow", methods={"GET", "POST"}, name="accounting_report_cash_flow")
     * @Secure(roles="ROLE_ACCOUNTING_REPORT,ROLE_DOMAIN")
     */

    public function cashFlowAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $transactionMethods = array(1,2,3,4);
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->findWithSearch($user,$transactionMethods,$data);
        $pagination = $entities->getResult();
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->cashOverview($user,$transactionMethods,$data);
        $globalOption = $this->getUser()->getGlobalOption();
        $employees = $em->getRepository('UserBundle:User')->getEmployees($globalOption);
        return $this->render('ReportBundle:Accounting/Cash:cashFlow.html.twig', array(
            'entities' => $pagination,
            'overview' => $overview,
            'employees' => $employees,
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
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = '';
        $customer = "";
        $overview = "";
        $customers = $this->getDoctrine()->getRepository("AccountingBundle:AccountSales")->customerOutstanding($globalOption);
        if(isset($data['submit']) and $data['submit'] == 'search' and isset($data['customerId']) and $data['customerId']) {
            $customerId = $data['customerId'];
            $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $globalOption,'id'=> $customerId));
            $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportCustomerLedger($globalOption->getId(),$data);
            $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->salesOverview($this->getUser(),$data);

        }
        return $this->render('ReportBundle:Accounting/Sales:ledger.html.twig', array(
                'entities' => $entities,
                'overview' => $overview,
                'customer' => $customer,
                'customers' => $customers,
                'option' => $globalOption,
                'searchForm' => $data,
        ));

    }

    /**
     * @Route("/user-sales-receive", methods={"GET", "POST"}, name="accounting_report_sales_user_summary")
     * @Secure(roles="ROLE_ACCOUNTING_REPORT,ROLE_ACCOUNTING_REPORT_SALES, ROLE_DOMAIN")
     */

    public function userSummaryAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
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
     * @Route("/customer-sales-receive", methods={"GET", "POST"}, name="accounting_report_sales_customer_summary")
     * @Secure(roles="ROLE_ACCOUNTING_REPORT,ROLE_ACCOUNTING_REPORT_SALES, ROLE_DOMAIN")
     */

    public function userSalesDetailsAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = "";
        if(isset($data['submit']) and $data['submit'] == 'search' and isset($data['user']) and $data['user']) {
            $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportCustomerDetails($globalOption,$data);
        }
        $employees = $em->getRepository('UserBundle:User')->getEmployees($globalOption);
        return $this->render('ReportBundle:Accounting/Sales:customerDetails.html.twig', array(
            'entities' => $entities,
            'employees' => $employees,
            'option' => $globalOption,
            'searchForm' => $data,
        ));
    }

    /**
     * @Route("/sales-details", methods={"GET", "POST"}, name="accounting_report_sales_details")
     * @Secure(roles="ROLE_ACCOUNTING_REPORT,ROLE_ACCOUNTING_REPORT_SALES, ROLE_DOMAIN")
     */

    public function salesDetailsAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = "";
        $transactionMethods = $this->getDoctrine()->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status'=>1),array('name'=>'asc'));
        $customers = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->getSalesCustomers($globalOption);
        $groups = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->getProcessModes($globalOption);
        $users = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->getCreatedUsers($globalOption);
        return $this->render('ReportBundle:Accounting/Sales:sales.html.twig', array(
            'entities' => $entities,
            'transactionMethods' => $transactionMethods,
            'groups' => $groups,
            'users' => $users,
            'customers' => $customers,
            'option' => $globalOption,
            'searchForm' => $data,
        ));
    }

    /**
     * @Route("/sales-details-load", methods={"GET", "POST"}, name="accounting_report_sales_ajax")
     * @Secure(roles="ROLE_ACCOUNTING_REPORT,ROLE_ACCOUNTING_REPORT_SALES, ROLE_DOMAIN")
     */

    public function salesDetailsLoadAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = "";
        if(isset($data['startDate']) and $data['startDate'] and isset($data['endDate']) and $data['endDate'] ) {
            $entities = $em->getRepository('AccountingBundle:AccountSales')->reportFindWithSearch($globalOption,$data);
            $htmlProcess = $this->renderView(
                'ReportBundle:Accounting/Sales:sales-data.html.twig', array(
                    'entities' => $entities,
                )
            );
            return new Response($htmlProcess);

        }
        return new Response('Record Does not found');
    }


    /**
     * @Route("/purchase-details", methods={"GET", "POST"}, name="accounting_report_purchase_details")
     * @Secure(roles="ROLE_ACCOUNTING_REPORT,ROLE_ACCOUNTING_REPORT_SALES, ROLE_DOMAIN")
     */

    public function purchaseDetailsAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = "";
        if(isset($data['submit']) and $data['submit'] == 'search' and isset($data['user']) and $data['user']) {
            $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportCustomerDetails($globalOption,$data);
        }
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->findWithSearch($globalOption,$data);
        $transactionMethods = $this->getDoctrine()->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status'=>1),array('name'=>'asc'));
        $groups = $this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->getProcessModes($globalOption);
        $users = $this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->getCreatedUsers($globalOption);
        return $this->render('ReportBundle:Accounting/Sales:customerDetails.html.twig', array(
            'entities' => $entities,
            'transactionMethods' => $transactionMethods,
            'groups' => $groups,
            'users' => $users,
            'option' => $globalOption,
            'searchForm' => $data,
        ));
    }


    /**
     * @Route("/hospital-dashboard", methods={"GET", "POST"}, name="hms_report_dashboard")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */

    public function hmsDashboardAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $globalOption = $this->getUser()->getGlobalOption();
        if (!empty($data['date'])) {
            $datetime = new \DateTime($data['date']);
            $data['startDate'] = $datetime->format('Y-m-d');
            $data['endDate'] = $datetime->format('Y-m-d');
        }

       // $salesTotalTransactionOverview = $em->getRepository('HospitalBundle:InvoiceTransaction')->todaySalesOverview($user, $data);
       // $salesTodayTransactionOverview = $em->getRepository('HospitalBundle:InvoiceTransaction')->todaySalesOverview($user, $data, 'false', array('diagnostic', 'admission'));
      //  $previousSalesTransactionOverview = $em->getRepository('HospitalBundle:InvoiceTransaction')->todaySalesOverview($user, $data, 'true', array('diagnostic', 'admission'));

        $summary = $em->getRepository('HospitalBundle:Invoice')->salesSummary($user, $data);
        $diagnosticOverview = $em->getRepository('HospitalBundle:Invoice')->findWithSalesOverview($user, $data, 'diagnostic');
        $admissionOverview = $em->getRepository('HospitalBundle:Invoice')->findWithSalesOverview($user, $data, 'admission');
        $serviceOverview = $em->getRepository('HospitalBundle:Invoice')->findWithServiceOverview($user, $data);
        $transactionOverview = $em->getRepository('HospitalBundle:InvoiceTransaction')->findWithTransactionOverview($user, $data);
        $commissionOverview = $em->getRepository('HospitalBundle:Invoice')->findWithCommissionOverview($user, $data);

        return $this->render('ReportBundle:Hospital:index.html.twig', array(

           // 'salesTotalTransactionOverview' => $salesTotalTransactionOverview,
         //   'salesTodayTransactionOverview' => $salesTodayTransactionOverview,
           // 'previousSalesTransactionOverview' => $previousSalesTransactionOverview,
            'diagnosticOverview' => $diagnosticOverview,
            'admissionOverview' => $admissionOverview,
            'serviceOverview' => $serviceOverview,
            'transactionOverview' => $transactionOverview,
            'commissionOverview' => $commissionOverview,
            'summary' => $summary,
            'searchForm' => $data,
            'option' => $globalOption,

        ));
    }

    /**
     * @Route("/hospital-diagnostic-invoice", methods={"GET", "POST"}, name="hms_report_diagnostic_invoice")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */

    public function hmsDiagnosticInvoiceAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();

        $globalOption = $this->getUser()->getGlobalOption();
        $hospital = $globalOption->getHospitalConfig();
        if (empty($data)) {
            $datetime = new \DateTime("now");
            $data['startDate'] = $datetime->format('Y-m-d');
            $data['endDate'] = $datetime->format('Y-m-d');
        }
        $employees = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getFindEmployees($hospital->getId());
        $entities = $em->getRepository('HospitalBundle:Invoice')->reportInvoiceLists($user , $mode = 'diagnostic' , $data);
        return $this->render('ReportBundle:Hospital/Sales:diagnostic-invoice.html.twig', array(
            'entities' => $entities,
            'employees' => $employees,
            'searchForm' => $data,
            'option' => $globalOption,

        ));
    }

    /**
     * @Route("/hospital-visit-invoice", methods={"GET", "POST"}, name="hms_report_visit_invoice")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */

    public function hmsVisitInvoiceAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $globalOption = $this->getUser()->getGlobalOption();
        if (empty($data)) {
            $datetime = new \DateTime("now");
            $data['startDate'] = $datetime->format('Y-m-d');
            $data['endDate'] = $datetime->format('Y-m-d');
        }
        $hospital = $user->getGlobalOption()->getHospitalConfig()->getId();
        $entities = $em->getRepository('HospitalBundle:Invoice')->reportVisitLists($user , $mode = 'visit' , $data);
        $assignDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getAssignDoctor($hospital);
        $employees = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getFindEmployees($hospital);
        return $this->render('ReportBundle:Hospital/Sales:visit-invoice.html.twig', array(
            'employees' => $employees,
            'assignDoctors' => $assignDoctors,
            'entities' => $entities,
            'searchForm' => $data,
            'option' => $globalOption,

        ));
    }

    /**
     * @Route("/hospital-admission-invoice", methods={"GET", "POST"}, name="hms_report_admission_invoice")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */

    public function hmsAdmissionInvoiceAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $globalOption = $this->getUser()->getGlobalOption();
        if (empty($data)) {
            $datetime = new \DateTime("now");
            $data['startDate'] = $datetime->format('Y-m-d');
            $data['endDate'] = $datetime->format('Y-m-d');
        }
        $hospital = $user->getGlobalOption()->getHospitalConfig()->getId();
        $entities = $em->getRepository('HospitalBundle:Invoice')->reportAdmissionLists($user , $mode = 'admission' , $data);
        $assignDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getAssignDoctor($hospital);
        $anesthesiaDoctor = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getAanesthesiaDoctor($hospital);
        $assistantDoctor = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getAssistantDoctor($hospital);
        $departments = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getDepartments($hospital);
        $employees = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getFindEmployees($hospital);
        return $this->render('ReportBundle:Hospital/Sales:admission-invoice.html.twig', array(
            'employees' => $employees,
            'anesthesiaDoctor' => $anesthesiaDoctor,
            'assignDoctors' => $assignDoctors,
            'assistantDoctor' => $assistantDoctor,
            'departments' => $departments,
            'entities' => $entities,
            'searchForm' => $data,
            'option' => $globalOption,

        ));
    }

    /**
     * @Route("/hospital-admission-invoice-service", methods={"GET", "POST"}, name="hms_report_admission_service")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */

    public function hmsAdmissionServiceAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $globalOption = $this->getUser()->getGlobalOption();
        if (empty($data)) {
            $datetime = new \DateTime("now");
            $data['startDate'] = $datetime->format('Y-m-d');
            $data['endDate'] = $datetime->format('Y-m-d');
        }
        $hospital = $user->getGlobalOption()->getHospitalConfig()->getId();
        $serviceGroups = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getServiceGroups($hospital);
        $surgeryDepartment = $this->getDoctrine()->getRepository('HospitalBundle:HmsServiceGroup')->findBy(array('hospitalConfig'=> $hospital,'service'=>10),array('name'=>"ASC"));
        $entities = $em->getRepository('HospitalBundle:Invoice')->reportAdmissionService($user , $mode = 'admission' , $data);
        return $this->render('ReportBundle:Hospital/Sales:service.html.twig', array(
            'entities' => $entities,
            'serviceGroups' => $serviceGroups,
            'surgeryDepartment' => $surgeryDepartment,
            'searchForm' => $data,
            'option' => $globalOption,

        ));
    }

    /**
     * @Route("/hospital-admission-surgery-department", methods={"GET", "POST"}, name="hms_report_admission_surgery_department")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */

    public function hmsAdmissionSurgeryDepartmentAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $globalOption = $this->getUser()->getGlobalOption();
        if (empty($data)) {
            $datetime = new \DateTime("now");
            $data['startDate'] = $datetime->format('Y-m-d');
            $data['endDate'] = $datetime->format('Y-m-d');
        }
        $hospital = $user->getGlobalOption()->getHospitalConfig()->getId();
        $serviceGroups = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getServiceGroups($hospital);
        $services = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getServices($hospital);
        $entities = $em->getRepository('HospitalBundle:Invoice')->reportAdmissionDepartment($user , $mode = 'admission' , $data);
        return $this->render('ReportBundle:Hospital/Sales:surgery-department.html.twig', array(
            'entities' => $entities,
            'searchForm' => $data,
            'option' => $globalOption,

        ));
    }

    /**
     * @Route("/hospital-admission-surgery-department-details", methods={"GET", "POST"}, name="hms_report_admission_surgery_department_details")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */

    public function hmsAdmissionSurgeryDepartmentDetailsAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $globalOption = $this->getUser()->getGlobalOption();
        if (empty($data)) {
            $datetime = new \DateTime("now");
            $data['startDate'] = $datetime->format('Y-m-d');
            $data['endDate'] = $datetime->format('Y-m-d');
        }
        $hospital = $user->getGlobalOption()->getHospitalConfig()->getId();
        $assignDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getAssignDoctor($hospital);
        $employees = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getFindEmployees($hospital);
        $surgeryDepartment = $this->getDoctrine()->getRepository('HospitalBundle:HmsServiceGroup')->findBy(array('hospitalConfig'=> $hospital,'service'=>10),array('name'=>"ASC"));
        $serviceGroups = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getServiceGroups($hospital);
        $particulars = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getParticulars($hospital);
        $entities = $em->getRepository('HospitalBundle:Invoice')->reportAdmissionDepartmentDetails($user , $mode = 'admission' , $data);
        return $this->render('ReportBundle:Hospital/Sales:surgery-department-details.html.twig', array(
            'entities' => $entities,
            'particulars' => $particulars,
            'serviceGroups' => $serviceGroups,
            'surgeryDepartment' => $surgeryDepartment,
            'employees' => $employees,
            'assignDoctors' => $assignDoctors,
            'searchForm' => $data,
            'option' => $globalOption,

        ));
    }

    /**
     * @Route("/hospital-diseases", methods={"GET", "POST"}, name="hms_report_diseases")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */

    public function hmsDiseasesAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $globalOption = $this->getUser()->getGlobalOption();
        if (empty($data)) {
            $datetime = new \DateTime("now");
            $data['startDate'] = $datetime->format('Y-m-d');
            $data['endDate'] = $datetime->format('Y-m-d');
        }
        $hospital = $user->getGlobalOption()->getHospitalConfig()->getId();
        $assignDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getAssignDoctor($hospital);
        $entities = $em->getRepository('HospitalBundle:Invoice')->reportDiseases($user , $mode = 'admission' , $data);
        return $this->render('ReportBundle:Hospital/Sales:diseases.html.twig', array(
            'entities' => $entities,
            'assignDoctors' => $assignDoctors,
            'searchForm' => $data,
            'option' => $globalOption,

        ));
    }

    /**
     * @Route("/hospital-diseases-details", methods={"GET", "POST"}, name="hms_report_diseases_details")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */

    public function hmsDiseasesDetailsAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $globalOption = $this->getUser()->getGlobalOption();
        if (empty($data)) {
            $datetime = new \DateTime("now");
            $data['startDate'] = $datetime->format('Y-m-d');
            $data['endDate'] = $datetime->format('Y-m-d');
        }
        $hospital = $user->getGlobalOption()->getHospitalConfig()->getId();
        $assignDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->getAssignDoctor($hospital);
        $diseaseses = $this->getDoctrine()->getRepository('HospitalBundle:HmsServiceGroup')->findBy(array('hospitalConfig'=>$hospital,'service'=>11),array('name'=>'ASC'));
        $entities = $em->getRepository('HospitalBundle:Invoice')->reportDiseasesDetails($user , $mode = 'admission' , $data);
        return $this->render('ReportBundle:Hospital/Sales:diseases-details.html.twig', array(
            'entities' => $entities,
            'assignDoctors' => $assignDoctors,
            'diseaseses' => $diseaseses,
            'searchForm' => $data,
            'option' => $globalOption,

        ));
    }


    /* Inventory Report */

    /**
     * @Route("/inv-system-overview", methods={"GET", "POST"}, name="inv_system_overview")
     * @Secure(roles="ROLE_REPORT,ROLE_REPORT_OPERATION_SALES, ROLE_DOMAIN")
     */

    public function invSystemOverviewAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $data = $_REQUEST;
        $inventory = $globalOption->getInventoryConfig()->getId();
        $purchaseOverview = $em->getRepository('ReportBundle:Report')->invReportPurchaseOverview($inventory,$data);
        $priceOverview = $em->getRepository('ReportBundle:Report')->invReportStockPriceOverview($inventory,$data);
        $salesPurchasePrice = $em->getRepository('ReportBundle:Report')->invReportPurchasePrice($inventory,$data);
        $stockOverview = $em->getRepository('InventoryBundle:StockItem')->getStockOverview($inventory,$data);
        return $this->render('ReportBundle:Inventory:index.html.twig', array(
            'priceOverview' => $priceOverview[0],
            'stockOverview' => $stockOverview,
            'purchaseOverview' => $purchaseOverview,
            'salesPurchasePrice' => $salesPurchasePrice,
            'option' => $globalOption,
        ));
    }

}
