<?php

namespace Appstore\Bundle\AccountingBundle\Controller;

use Appstore\Bundle\AccountingBundle\Entity\AccountPurchase;
use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Appstore\Bundle\DomainUserBundle\Event\AssociationSmsEvent;
use Knp\Snappy\Pdf;
use Proxies\__CG__\Appstore\Bundle\AccountingBundle\Entity\AccountSales;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Setting\Bundle\ToolBundle\Event\CustomerOutstandingSmsEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

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

    public function balanceAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportIncome($globalOption,$data);
        return $this->render('AccountingBundle:Report:incomePdf.html.twig', array(
            'overview' => $overview,
            'searchForm' => $data,
        ));
    }

    public function incomeAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportSalesIncome($this->getUser(),$data);
        return $this->render('AccountingBundle:Report:income.html.twig', array(
            'overview' => $overview,
            'searchForm' => $data,
        ));
    }


    public function pdfIncomeAction()
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $data = $_REQUEST;
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportSalesIncome($this->getUser(),$data);
        $html = $this->renderView(
            'AccountingBundle:Report:incomePdf.html.twig', array(
                'overview' => $overview,
                'globalOption' => $globalOption,
                'print' => ''
            )
        );
        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy          = new Pdf($wkhtmltopdfPath);
        $pdf             = $snappy->getOutputFromHtml($html);

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="incomePdf.pdf"');
        echo $pdf;

        return new Response('');
    }

    public function printIncomeAction()
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $data = $_REQUEST;
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportIncome($globalOption,$data);
        return $this->render('AccountingBundle:Report:incomePdf.html.twig', array(
            'overview' => $overview,
            'print' => '<script>window.print();</script>'
        ));

    }

    public function monthlyIncomeAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportMonthlyIncome( $this->getUser(),$data);
        if(!empty($data['startMonth']) and !empty($data['endMonth'])){
            $sm = "01-{$data['startMonth']}-{$data['year']}";
            $compareTo = new \DateTime($sm);
            $startMonth =  $compareTo->format('F');
            $endm = "01-{$data['endMonth']}-{$data['year']}";
            $compareTo = new \DateTime($endm);
            $endMonth =  $compareTo->format('F,Y');
            $dateRange = $startMonth .' To '.$endMonth;
        }else{
            $compareTo = new \DateTime("now");
            $dateRange =  $compareTo->format('F,Y');
        }
        return $this->render('AccountingBundle:Report:monthlyIncome.html.twig', array(
            'overview' => $overview,
            'dateRange' => $dateRange,
            'searchForm' => $data,
        ));
    }

    public function monthlyIncomePdfAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportMonthlyIncome( $this->getUser(),$data);
        if(!empty($data['startMonth']) and !empty($data['endMonth'])){
            $sm = "01-{$data['startMonth']}-{$data['year']}";
            $compareTo = new \DateTime($sm);
            $startMonth =  $compareTo->format('F');
            $endm = "01-{$data['endMonth']}-{$data['year']}";
            $compareTo = new \DateTime($endm);
            $endMonth =  $compareTo->format('F,Y');
            $dateRange = $startMonth .' To '.$endMonth;
        }else{
            $compareTo = new \DateTime("now");
            $dateRange =  $compareTo->format('F,Y');
        }
        $html = $this->renderView(
            'AccountingBundle:Report:monthlyIncomePdf.html.twig', array(
                'globalOption' => $globalOption,
                'dateRange' => $dateRange,
                'overview' => $overview,
                'searchForm' => $data,
                'print' => ''
            )
        );
        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy          = new Pdf($wkhtmltopdfPath);
        $pdf             = $snappy->getOutputFromHtml($html);
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="monthlyIncomePdf.pdf"');
        echo $pdf;
        return new Response('');
    }

    public function cashReceivePaymentAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->cashReceivePayment( $this->getUser(),$data);
        return $this->render('AccountingBundle:Report:accountCashDetails.html.twig', array(
            'overview' => $overview,
            'searchForm' => $data,
        ));
    }

	public function cashReceivePaymentDetailsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportMonthlyIncome( $this->getUser(),$data);
        return $this->render('AccountingBundle:Report:monthlyIncome.html.twig', array(
            'overview' => $overview,
            'searchForm' => $data,
        ));
    }

    public function expenditureSummaryAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:Expenditure')->expenditureOverview($user,$data);
        $parent = array(23,37);
        $expenditureHead = $em->getRepository('AccountingBundle:Expenditure')->parentsExpenseAccountHead($user->getGlobalOption(),$parent,$data);
        if(empty($data['pdf'])){
            return $this->render('AccountingBundle:Report/Expenditure:accountHead.html.twig', array(
                'overview' => $overview,
                'expenditureHead' => $expenditureHead,
                'searchForm' => $data,
            ));
        }else{
            $html = $this->renderView(
                'AccountingBundle:Report/Expenditure:accountHeadPdf.html.twig', array(
                    'overview' => $overview,
                    'globalOption' => $user->getGlobalOption(),
                    'expenditureHead' => $expenditureHead,
                    'searchForm' => $data,
                )
            );
            $datetime = new \DateTime("now");
            $date = $datetime->format('d-m-y');
            $date = "{$date}-expenditure-summary.pdf";
            $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
            $snappy = new Pdf($wkhtmltopdfPath);
            $pdf = $snappy->getOutputFromHtml($html);
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $date . '"');
            echo $pdf;
            return new Response('');
        }

    }

    public function expenditureCategoryAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $expenditureOverview = $em->getRepository('AccountingBundle:Expenditure')->reportForExpenditure($user->getGlobalOption(),$data);
        if(empty($data['pdf'])) {
            return $this->render('AccountingBundle:Report/Expenditure:category.html.twig', array(
                'expenditureOverview' => $expenditureOverview,
                'searchForm' => $data,
            ));
        }else {
            $html = $this->renderView(
                'AccountingBundle:Report/Expenditure:categoryPdf.html.twig', array(
                    'globalOption' => $user->getGlobalOption(),
                    'expenditureOverview' => $expenditureOverview,
                    'searchForm' => $data,
                )
            );
            $datetime = new \DateTime("now");
            $date = $datetime->format('d-m-y');
            $date = "{$date}-expenditure-summary.pdf";
            $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
            $snappy = new Pdf($wkhtmltopdfPath);
            $pdf = $snappy->getOutputFromHtml($html);
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $date . '"');
            echo $pdf;
            return new Response('');
        }
    }

    public function expenditureDetailsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
	    $option = $this->getUser()->getGlobalOption();
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:Expenditure')->expenditureOverview( $this->getUser(),$data);
        $entities = $em->getRepository('AccountingBundle:Expenditure')->findWithSearch( $this->getUser(),$data);
        $pagination = $this->paginate($entities);
        $transactionMethods = $this->getDoctrine()->getRepository('SettingToolBundle:TransactionMethod')->findByQuery();
        $categories = $this->getDoctrine()->getRepository('AccountingBundle:ExpenseCategory')->findBy(array('globalOption'=> $option, 'status'=>1),array('name'=>'asc'));
        $heads = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->getExpenseAccountHead();
        $employees = $em->getRepository('UserBundle:User')->getEmployees($option);

        if(empty($data['pdf'])) {

            return $this->render('AccountingBundle:Report/Expenditure:expenditure.html.twig', array(
            'overview' => $overview,
            'entities' => $pagination,
            'transactionMethods' => $transactionMethods,
            'heads' => $heads,
            'categories' => $categories,
            'employees' => $employees,
            'global' => $option->getId(),
            'searchForm' => $data,
            ));
        }else {
            $entities = $em->getRepository('AccountingBundle:Expenditure')->findWithSearch( $this->getUser(),$data);
            $pagination = $entities->getResult();
            $html = $this->renderView(
                'AccountingBundle:Report/Expenditure:expenditurePdf.html.twig', array(
                    'globalOption' => $option,
                    'entities' => $pagination,
                    'searchForm' => $data,
                )
            );
            $datetime = new \DateTime("now");
            $date = $datetime->format('d-m-y');
            $date = "{$date}-expenditure-details.pdf";
            $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
            $snappy = new Pdf($wkhtmltopdfPath);
            $pdf = $snappy->getOutputFromHtml($html);
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $date . '"');
            echo $pdf;
            return new Response('');
        }
    }

    public function expenditurePurchaseHeadAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:Expenditure')->expenditureOverview($user,$data);
        $parent = array(23,37);
        $expenditureHead = $em->getRepository('AccountingBundle:Transaction')->parentsAccountHead($user->getGlobalOption(),$parent,$data);
        if(empty($data['pdf'])){
            return $this->render('AccountingBundle:Report/Expenditure:accountHead.html.twig', array(
                'overview' => $overview,
                'expenditureHead' => $expenditureHead,
                'searchForm' => $data,
            ));
        }else{
            $html = $this->renderView(
                'AccountingBundle:Report/Expenditure:accountHeadPdf.html.twig', array(
                    'overview' => $overview,
                    'globalOption' => $user->getGlobalOption(),
                    'expenditureHead' => $expenditureHead,
                    'searchForm' => $data,
                )
            );
            $datetime = new \DateTime("now");
            $date = $datetime->format('d-m-y');
            $date = "{$date}-expenditure-summary.pdf";
            $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
            $snappy = new Pdf($wkhtmltopdfPath);
            $pdf = $snappy->getOutputFromHtml($html);
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $date . '"');
            echo $pdf;
            return new Response('');
        }

    }

    public function expenditurePurchaseDetailsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $option = $this->getUser()->getGlobalOption();
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:Expenditure')->expenditureOverview( $this->getUser(),$data);
        $entities = $em->getRepository('AccountingBundle:Expenditure')->findWithSearch( $this->getUser(),$data);
        $pagination = $this->paginate($entities);
        $transactionMethods = $this->getDoctrine()->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status'=>1),array('name'=>'asc'));
        $categories = $this->getDoctrine()->getRepository('AccountingBundle:ExpenseCategory')->findBy(array('globalOption'=> $option, 'status'=>1),array('name'=>'asc'));
        $heads = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->getExpenseAccountHead();
        if(empty($data['pdf'])) {

            return $this->render('AccountingBundle:Report/Expenditure:expenditure.html.twig', array(
                'overview' => $overview,
                'entities' => $pagination,
                'transactionMethods' => $transactionMethods,
                'heads' => $heads,
                'categories' => $categories,
                'searchForm' => $data,
            ));
        }else {
            $entities = $em->getRepository('AccountingBundle:Expenditure')->findWithSearch( $this->getUser(),$data);
            $pagination = $entities->getResult();
            $html = $this->renderView(
                'AccountingBundle:Report/Expenditure:expenditurePdf.html.twig', array(
                    'globalOption' => $option,
                    'entities' => $pagination,
                    'searchForm' => $data,
                )
            );
            $datetime = new \DateTime("now");
            $date = $datetime->format('d-m-y');
            $date = "{$date}-expenditure-details.pdf";
            $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
            $snappy = new Pdf($wkhtmltopdfPath);
            $pdf = $snappy->getOutputFromHtml($html);
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $date . '"');
            echo $pdf;
            return new Response('');
        }
    }


    /**
	 * Lists all AccountSales entities.
	 *
	 */
	public function customerOutstandingAction()
	{
        set_time_limit(0);
        ignore_user_abort(true);
	    $em = $this->getDoctrine()->getManager();
		$data =$_REQUEST;
		$globalOption = $this->getUser()->getGlobalOption();
		$entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->customerOutstanding($globalOption,$data);
		$pagination = $this->paginate($entities);
		$summary = $this->getDoctrine()->getRepository(AccountSales::class)->customerOutstandingSummary($globalOption);
		return $this->render('AccountingBundle:Report/Outstanding:customerOutstanding.html.twig', array(
			'global' => $globalOption,
			'entities' => $pagination,
			'summary' => $summary,
			'searchForm' => $data,
		));
	}

	/**
	 * Lists all AccountSales entities.
	 *
	 */
	public function customerOutstandingSmsAction(Customer $customer)
	{
        $global = $this->getUser()->getGlobalOption();
        $invoice = $customer;
        $name = $customer->getName();
        $orgName = $global->getName();
        $hotline = $global->getHotline();
        $mobile = "+88".$invoice->getMobile();

       // $mobile = "+8801828148148"; //.$invoice->getCustomer()->getMobile();
        $date = date('d-m-Y');
        $balance = $this->getDoctrine()->getRepository(AccountSales::class)->customerSingleOutstanding( $invoice->getGlobalOption(),$invoice->getId());
        $outstanding = number_format($balance,2);
        $msg = "Dear Sir As-salamu Alaykum, Your present Due Balance TK. {$outstanding}. Please Contact:  {$hotline}.Thanks for being with our.";
        $msg = $orgName .'\nDear '.$msg;
        if($global->getSmsSenderTotal() and $global->getSmsSenderTotal()->getRemaining() > 0 and $global->getNotificationConfig()->getSmsActive() == 1) {
            $status = $this->send($msg,$mobile);
            $this->getDoctrine()->getRepository('SettingToolBundle:SmsSender')->insertCustomerOutstandingSms($invoice, $status);
            $this->get('session')->getFlashBag()->add(
                'success',"{ $name } SMS has benn sent successfully"
            );
        }
        return new Response($status);
	}
    function send($msg, $phone, $sender = ""){

        if(empty($sender)){
            $from = "03590602016";
        }else{
            $from = $sender;
        }
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://api.icombd.com/api/v1/campaigns/sms/1/text/single",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS =>"{\"from\":\"{$from}\",\"text\":\"{$msg}\",\"to\":\"{$phone}\"}",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Basic dW1hcml0OnVtYXJpdDE0OA=="
            ),
        ));
        $response = curl_exec($curl);
        return $response;

    }

	/**
	 * Lists all AccountSales entities.
	 *
	 */
	public function customerSummaryAction()
	{
        set_time_limit(0);
        ignore_user_abort(true);
	    $em = $this->getDoctrine()->getManager();
		$data =$_REQUEST;
		$globalOption = $this->getUser()->getGlobalOption();
		$entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->customerSummary($globalOption,$data);
		$pagination = $this->paginate($entities);
        $summary = $this->getDoctrine()->getRepository(AccountSales::class)->customerOutstandingSummary($globalOption);
        return $this->render('AccountingBundle:Report/Outstanding:customerSummary.html.twig', array(
			'entities' => $pagination,
			'summary' => $summary,
			'searchForm' => $data,
		));
	}

	public function customerOutstandingPdfAction()
	{
        set_time_limit(0);
        ignore_user_abort(true);
	    $data =$_REQUEST;
		$globalOption = $this->getUser()->getGlobalOption();
		$entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->customerOutstanding($globalOption,$data);
        $html = $this->renderView(
		    'AccountingBundle:Report/Outstanding:customerOutstandingPdf.html.twig', array(
			'globalOption'  => $globalOption,
			'entities'      => $entities,
			'searchForm'    => $data,
		));
        $date = "customer-outstanding.pdf";
        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy = new Pdf($wkhtmltopdfPath);
        $pdf = $snappy->getOutputFromHtml($html);
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $date . '"');
        echo $pdf;
        return new Response('');

    }

	public function customerLedgerAction()
	{
        set_time_limit(0);
        ignore_user_abort(true);

        $em = $this->getDoctrine()->getManager();
		$data = $_REQUEST;
		$user = $this->getUser();
		$customer ='';
		$overview = '';
		if(isset($data['mobile']) and !empty($data['mobile'])){
			$customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('mobile'=>$data['mobile']));
			$overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->salesOverview($user,$data);
		}
		$entities = $em->getRepository('AccountingBundle:AccountSales')->customerLedger($user,$data);

        if(empty($data['pdf'])) {

            return $this->render('AccountingBundle:Report/Outstanding:customerLedger.html.twig', array(
                'entities' => $entities->getResult(),
                'globalOption' => $this->getUser()->getGlobalOption(),
                'overview' => $overview,
                'customer' => $customer,
                'searchForm' => $data,
            ));

        }else{

            $html = $this->renderView(
                'AccountingBundle:Report/Outstanding:customerLedgerPdf.html.twig', array(
                    'globalOption' => $this->getUser()->getGlobalOption(),
                    'entities' => $entities->getResult(),
                    'overview' => $overview,
                    'customer' => $customer,
                    'searchForm' => $data,
                )
            );

            $this->downloadPdf($html,"{$customer->getName()}.pdf");

        }
	}
	/**
	 * Lists all AccountSales entities.
	 *
	 */


	public function customerOutstandingPrintAction()
	{
        set_time_limit(0);
        ignore_user_abort(true);

        $em = $this->getDoctrine()->getManager();
		$data = $_REQUEST;
		$user = $this->getUser();
		$customer ='';
		$overview = '';
		if(isset($data['mobile']) and !empty($data['mobile'])){
			$customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('mobile'=>$data['mobile']));
			$overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->salesOverview($user,$data);
		}

		$amount = ($overview['totalAmount'] - $overview['receiveAmount']);
        $amountInWord = $this->get('settong.toolManageRepo')->intToWords($amount);

        return $this->render('AccountingBundle:Report/Outstanding:customerOutstandingPrint.html.twig', array(
            'globalOption' => $this->getUser()->getGlobalOption(),
            'config' => $this->getUser()->getGlobalOption()->getAccountingConfig(),
            'overview' => $overview,
            'entity' => $customer,
            'amountInWord' => $amountInWord,
            'searchForm' => $data,
        ));


	}
	/**
	 * Lists all AccountSales entities.
	 *
	 */


	public function vendorOutstandingAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        /* @var $globalOption GlobalOption */
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->vendorLedgerOutstanding($globalOption,$data);
        $pagination = $this->paginate($entities);
        $summary = $this->getDoctrine()->getRepository(AccountPurchase::class)->vendorOutstandingSummary($globalOption);
        return $this->render('AccountingBundle:Report/Outstanding:vendorOutstanding.html.twig', array(
            'option' => $globalOption,
            'entities' => $pagination,
            'summary' => $summary,
			'searchForm' => $data,
		));
	}

    public function vendorSummaryAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        /* @var $globalOption GlobalOption */
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->vendorSummary($globalOption,$data);
        $pagination = $this->paginate($entities);
        $summary = $this->getDoctrine()->getRepository(AccountPurchase::class)->vendorOutstandingSummary($globalOption);

        return $this->render('AccountingBundle:Report/Outstanding:vendorSummary.html.twig', array(
            'option' => $globalOption,
            'entities' => $pagination,
            'summary' => $summary,
            'searchForm' => $data,
        ));
    }

    public function vendorOutstandingPdfAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        /* @var $globalOption GlobalOption */
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->vendorLedgerOutstanding($globalOption,$data);
        $html = $this->renderView(
            'AccountingBundle:Report/Outstanding:vendorOutstandingPdf.html.twig', array(
            'globalOption'  => $globalOption,
            'entities'      => $entities,
			'searchForm'    => $data,
		));
        $date = "vendor-outstanding.pdf";
        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy = new Pdf($wkhtmltopdfPath);
        $pdf = $snappy->getOutputFromHtml($html);
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $date . '"');
        echo $pdf;
        return new Response('');
	}


	public function vendorLedgerAction()
	{
		$em = $this->getDoctrine()->getManager();
		$data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
		$overview = '';
        $vendor = '';
        $entities = '';
        if ($globalOption->getMainApp()->getSlug() == 'inventory' and !empty($data['vendor'])){
            $vendor = $this->getDoctrine()->getRepository('InventoryBundle:Vendor')->findOneBy(array('companyName'=> $data['vendor']));
        }else if ($globalOption->getMainApp()->getSlug() == 'miss' and !empty($data['vendor'])){
            $vendor = $this->getDoctrine()->getRepository('MedicineBundle:MedicineVendor')->findOneBy(array('companyName'=> $data['vendor']));
        }elseif(!empty($data['vendor'])){
            $vendor = $this->getDoctrine()->getRepository('AccountingBundle:AccountVendor')->findOneBy(array('companyName'=> $data['vendor']));
        }
		if(!empty($data) and !empty($data['vendor'])){
            $entities = $em->getRepository('AccountingBundle:AccountPurchase')->vendorLedger($globalOption,$data);
            $entities = $entities->getResult();
		}
		if(isset($data['pdf']) and $data['pdf']){
            $html = $this->renderView(
                'AccountingBundle:Report/Outstanding:vendorLedgerPdf.html.twig', array(
                    'globalOption' => $globalOption,
                    'vendor' => $vendor,
                    'entities' => $entities,
                    'overview' => $overview,
                    'option' => $globalOption,
                    'searchForm' => $data,
                )
            );
            $this->downloadPdf($html,"{$vendor->getCompanyName()}.pdf");

        }else{

            return $this->render('AccountingBundle:Report/Outstanding:vendorLedger.html.twig', array(
                'globalOption' => $globalOption,
                'vendor' => $vendor,
                'entities' => $entities,
                'overview' => $overview,
                'option' => $globalOption,
                'searchForm' => $data,
            ));
        }

	}

    /**
     * Lists all AccountSales entities.
     *
     */
    public function customerSalesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->customerSummary($globalOption,$data);
        return $this->render('AccountingBundle:Report/Sales:customerSummary.html.twig', array(
            'entities' => $entities,
            'option' => $globalOption,
            'searchForm' => $data,
        ));
    }

    /**
     * Lists all AccountSales entities.
     *
     */
    public function salesBoardAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->customerSummary($globalOption,$data);
        return $this->render('AccountingBundle:Report/Sales:salesBoard.html.twig', array(
            'entities' => $entities,
            'option' => $globalOption,
            'searchForm' => $data,
        ));
    }

    /**
     * Lists all AccountSales entities.
     *
     */
    public function userSummaryAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->userSummary($globalOption,$data);
        return $this->render('AccountingBundle:Report/Sales:userSummary.html.twig', array(
            'entities' => $entities,
            'option' => $globalOption,
            'searchForm' => $data,
        ));
    }
    public function downloadPdf($html,$fileName = '')
    {
        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy          = new Pdf($wkhtmltopdfPath);
        $pdf             = $snappy->getOutputFromHtml($html);
        header('Content-Type: application/pdf');
        header("Content-Disposition: attachment; filename={$fileName}");
        echo $pdf;
        exit;
    }


}
