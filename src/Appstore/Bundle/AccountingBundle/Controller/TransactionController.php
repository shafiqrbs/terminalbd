<?php

namespace Appstore\Bundle\AccountingBundle\Controller;

use Appstore\Bundle\AccountingBundle\Entity\AccountHead;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\AccountingBundle\Entity\Transaction;
use Symfony\Component\HttpFoundation\Response;

/**
 * Transaction controller.
 *
 */
class TransactionController extends Controller
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
     * Lists all Transaction entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('AccountingBundle:Transaction')->getGroupByAccountHead($globalOption);

	    $invoiceDetails = [];
	   // $invoiceDetails = ['Pathology' => ['items' => [], 'total'=> 0, 'hasQuantity' => false ]];

	    foreach ($entities as $key => $item) {
		    $serviceName = $item['parentName'];
		    $invoiceDetails[$serviceName][$key] = $item;
		}
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->transactionOverview($globalOption);
        $accountHead = $em->getRepository('AccountingBundle:AccountHead')->findBy(array('status'=>1),array('name'=>'asc'));
        return $this->render('AccountingBundle:Transaction:balancesheet.html.twig', array(
            'entities' => $invoiceDetails,
            'accountHead' => $accountHead,
            'overview' => $overview,
        ));
    }

	/**
	 * Lists all Transaction entities.
	 *
	 */
	public function accountCashAction()
	{
		$em = $this->getDoctrine()->getManager();
		$data = $_REQUEST;
		$user = $this->getUser();
		$transactionMethods = array(1,2,3,4);
		$entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->findWithSearch($user,$transactionMethods,$data);
		$pagination = $this->paginate($entities);
	//	$overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->cashOverview($user,$transactionMethods,$data);
	//	$processHeads = $this->getDoctrine()->getRepository('AccountingBundle:ProcessHead')->findBy(array('status'=>1));

		$overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->cashOverview($user,$transactionMethods,$data);
		$globalOption = $this->getUser()->getGlobalOption();
		$globalOption->getId();
	//	$overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountJournal')->finalTransaction($globalOption);
	//	$accountPurchase = $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->finalTransaction($globalOption,'Purchase');
	//	$accountSales = $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->finalTransaction($globalOption,'Sales');
	//	$accountExpenditure = $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->finalTransaction($globalOption,'Expenditure');

		return $this->render('AccountingBundle:Transaction:accountcash.html.twig', array(
			'entities' => $pagination,
			'overview' => $overview,
		//	'purchases' => $accountPurchase,
		//	'sales' => $accountSales,
		//	'expenditures' => $accountExpenditure,
			'searchForm' => $data,
		));
	}
   
    /**
     * Finds and displays a Transaction entity.
     *
     */
    public function showAction(AccountHead $entity )
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->specificAccountHead($globalOption,$entity->getId());
        $pagination = $this->paginate($entities);
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->transactionOverview($globalOption,$entity->getId());
        $accountHead = $em->getRepository('AccountingBundle:AccountHead')->findBy(array('status'=> 1),array('name'=>'asc'));
        return $this->render('AccountingBundle:Transaction:show.html.twig', array(
            'entity' => $entity,
            'entities' => $pagination,
            'accountHead' => $accountHead,
            'overview' => $overview,
        ));

    }

    /**
     * Lists all Transaction entities.
     *
     */
    public function transactionCashOverviewAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $transactionMethods = array(1,4);
        $globalOption = $this->getUser()->getGlobalOption();
        $transactionCashOverview = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->transactionWiseOverview( $this->getUser(),$data);
        $transactionBankCashOverviews = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->transactionBankCashOverview( $this->getUser(),$data);
        $transactionMobileBankCashOverviews = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->transactionMobileBankCashOverview( $this->getUser(),$data);
        $transactionAccountHeadCashOverviews = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->transactionAccountHeadCashOverview( $this->getUser(),$data);

        return $this->render('AccountingBundle:Transaction:cashoverview.html.twig', array(
            'transactionCashOverviews'                  => $transactionCashOverview,
            'transactionBankCashOverviews'              => $transactionBankCashOverviews,
            'transactionMobileBankCashOverviews'        => $transactionMobileBankCashOverviews,
            'transactionAccountHeadCashOverviews'       => $transactionAccountHeadCashOverviews,
            'searchForm' => $data,
        ));

    }


    public function transactionCashOverviewPdfAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $transactionMethods = array(1,4);
        $globalOption = $this->getUser()->getGlobalOption();
        $transactionCashOverview = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->transactionWiseOverview( $this->getUser(),$data);
        $transactionBankCashOverviews = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->transactionBankCashOverview( $this->getUser(),$data);
        $transactionMobileBankCashOverviews = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->transactionMobileBankCashOverview( $this->getUser(),$data);
        $transactionAccountHeadCashOverviews = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->transactionAccountHeadCashOverview( $this->getUser(),$data);
        $html = $this->renderView('AccountingBundle:Transaction:cashoverviewPdf.html.twig', array(
            'globalOption'                          => $this->getUser()->getGlobalOption(),
            'transactionCashOverviews'              => $transactionCashOverview,
            'transactionBankCashOverviews'          => $transactionBankCashOverviews,
            'transactionMobileBankCashOverviews'    => $transactionMobileBankCashOverviews,
            'transactionAccountHeadCashOverviews'   => $transactionAccountHeadCashOverviews,
            'searchForm' => $data,
        ));

        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy          = new Pdf($wkhtmltopdfPath);
        $pdf             = $snappy->getOutputFromHtml($html);
        $fileName ='cash-summary-'.date('d-m-Y').'.pdf';
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="'.$fileName.'"');
        echo $pdf;
        return new Response('');
        exit;
    }


    /**
     * Lists all Transaction entities.
     *
     */
    public function cashAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $transactionMethods = array(1,4);
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->findWithSearch($user,$transactionMethods,$data);
        $pagination = $this->paginate($entities);
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->cashOverview($user,$transactionMethods,$data);
        $processHeads = $this->getDoctrine()->getRepository('AccountingBundle:ProcessHead')->findBy(array('status'=>1));
        return $this->render('AccountingBundle:Transaction:cash.html.twig', array(
            'entities' => $pagination,
            'overview' => $overview,
            'processHeads' => $processHeads,
            'searchForm' => $data,
        ));

    }

    /**
     * Lists all Transaction entities.
     *
     */
    public function bankAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;

        $user = $this->getUser();
        $globalOption = $user->getGlobalOption();
        $transactionMethods = array(2);
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->findWithSearch($user,$transactionMethods,$data);
        $pagination = $this->paginate($entities);
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->cashOverview($user,$transactionMethods,$data);
        $processHeads = $this->getDoctrine()->getRepository('AccountingBundle:ProcessHead')->findBy(array('status'=>1));
        $accountBanks = $this->getDoctrine()->getRepository('AccountingBundle:AccountBank')->findBy(array('globalOption'=>$globalOption,'status'=>1));

        return $this->render('AccountingBundle:Transaction:bank.html.twig', array(
            'entities' => $pagination,
            'overview' => $overview,
            'processHeads' => $processHeads,
            'accountBanks' => $accountBanks,
            'searchForm' => $data,
        ));

    }

    /**
     * Lists all Transaction entities.
     *
     */
    public function mobileBankAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $globalOption = $user->getGlobalOption();
        $transactionMethods = [3];
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->findWithSearch($user,$transactionMethods,$data);
        $pagination = $this->paginate($entities);
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->cashOverview($user,$transactionMethods,$data);
        $processHeads = $this->getDoctrine()->getRepository('AccountingBundle:ProcessHead')->findBy(['status'=>1]);
        $accountMobileBanks = $this->getDoctrine()->getRepository('AccountingBundle:AccountMobileBank')->findBy(['globalOption' => $globalOption,'status' => 1]);
        return $this->render('AccountingBundle:Transaction:mobilebank.html.twig',[
            'entities'              => $pagination,
            'overview'              => $overview,
            'processHeads'          => $processHeads,
            'accountMobileBanks'    => $accountMobileBanks,
            'searchForm'            => $data,
        ]);

    }

    public function monthlyAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        if(empty($data)){
            $compare = new \DateTime();
            $end =  $compare->format('j');
            $data['monthYear'] = $compare->format('Y-m-d');
            $data['month'] =  $compare->format('F');
            $data['year'] = $compare->format('Y');
        }else{
            $month = $data['month'];
            $year = $data['year'];
            $compare = new \DateTime("{$year}-{$month}-01");
            $end =  $compare->format('t');
            $data['monthYear'] = $compare->format('Y-m-d');
        }
        $openingBalance = [];
        for ($i = 1; $end >= $i ; $i++ ){
            $no = sprintf("%s", str_pad($i,2, '0', STR_PAD_LEFT));
            $start =  $compare->format("Y-m-{$no}");
            $day =  $compare->format("{$no}-m-Y");
            $data['startDate'] = $start;
            $openingBalance[$day] = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->openingBalanceGroup($user,'',$data);
        }
        $sales = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->dailyProcessHead($user,'Sales',$data);
        $purchase = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->dailyProcessHead($user,'Purchase',$data);
        $purchaseCommission = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->dailyProcessHead($user,'Purchase-Commission',$data);
        $expenditure = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->dailyProcessHead($user,'Expenditure',$data);
        $purchaseExpenditure = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->dailyProcessHead($user,'Purchase-Expenditure',$data);
        $journal = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->dailyProcessHead($user,'Journal',$data);

        if(empty($data['pdf'])){

            return $this->render('AccountingBundle:Transaction/Report:monthly.html.twig',[
                'globalOption'                  => $this->getUser()->getGlobalOption(),
                'openingBalanceTrans'           => $openingBalance,
                'salesTrans'                    => $sales,
                'purchaseTrans'                 => $purchase,
                'purchaseCommissionTrans'       => $purchaseCommission,
                'expenditureTrans'              => $expenditure,
                'purchaseExpenditureTrans'      => $purchaseExpenditure,
                'journalTrans'                  => $journal,
                'searchForm'                    => $data,
            ]);
        }else{
            $html = $this->renderView(
                'AccountingBundle:Transaction/Report:monthlyPdf.html.twig', array(
                    'globalOption'                  => $this->getUser()->getGlobalOption(),
                    'openingBalanceTrans'           => $openingBalance,
                    'salesTrans'                    => $sales,
                    'purchaseTrans'                 => $purchase,
                    'purchaseCommissionTrans'       => $purchaseCommission,
                    'expenditureTrans'              => $expenditure,
                    'purchaseExpenditureTrans'      => $purchaseExpenditure,
                    'journalTrans'                  => $journal,
                    'searchForm'                    => $data,
                )
            );
            $this->downloadPdf($html,'monthlyCashPdf.pdf');
        }

    }

    public function yearlyAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        if(empty($data)){
            $compare = new \DateTime();
            $year =  $compare->format('Y');
            $data['year'] = $year;
        }else{
            $year = $data['year'];
        }
        $months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        $openingBalance = [];
        $i = 1;
        foreach ($months as $month){
            $compare = new \DateTime("{$year}-{$month}-01");
            $start =  $compare->format("Y-m-01");
            $data['startDate'] = $start;
            $openingBalance[$i] = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->openingBalanceGroup($user,'',$data);
            $i++;
        }
        $sales = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->monthlyProcessHead($user,'Sales',$data);
        $purchase = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->monthlyProcessHead($user,'Purchase',$data);
        $purchaseCommission = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->monthlyProcessHead($user,'Purchase-Commission',$data);
        $expenditure = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->monthlyProcessHead($user,'Expenditure',$data);
        $purchaseExpenditure = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->monthlyProcessHead($user,'Purchase-Expenditure',$data);
        $journal = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->monthlyProcessHead($user,'Journal',$data);
        if(empty($data['pdf'])){

            return $this->render('AccountingBundle:Transaction/Report:yearly.html.twig',[
                'globalOption'                  => $this->getUser()->getGlobalOption(),
                'openingBalanceTrans'           => $openingBalance,
                'salesTrans'                    => $sales,
                'purchaseTrans'                 => $purchase,
                'purchaseCommissionTrans'       => $purchaseCommission,
                'expenditureTrans'              => $expenditure,
                'purchaseExpenditureTrans'      => $purchaseExpenditure,
                'journalTrans'                  => $journal,
                'searchForm'                    => $data,
            ]);
        }else{
            $html = $this->renderView(
                'AccountingBundle:Transaction/Report:yearlyPdf.html.twig', array(
                    'globalOption'                  => $this->getUser()->getGlobalOption(),
                    'openingBalanceTrans'           => $openingBalance,
                    'salesTrans'                    => $sales,
                    'purchaseTrans'                 => $purchase,
                    'purchaseCommissionTrans'       => $purchaseCommission,
                    'expenditureTrans'              => $expenditure,
                    'purchaseExpenditureTrans'      => $purchaseExpenditure,
                    'journalTrans'                  => $journal,
                    'searchForm'                    => $data,
                )
            );
            $this->downloadPdf($html,'monthlyCashPdf.pdf');
        }

    }

    public function downloadPdf($html,$fileName = '')
    {
        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy          = new Pdf($wkhtmltopdfPath);
        $pdf             = $snappy->getOutputFromHtml($html);
        header('Content-Type: application/pdf');
        header("Content-Disposition: attachment; filename='{$fileName}'");
        echo $pdf;
        return new Response('');
    }

}
