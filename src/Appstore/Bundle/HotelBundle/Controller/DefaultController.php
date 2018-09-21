<?php

namespace Appstore\Bundle\HotelBundle\Controller;

use Knp\Snappy\Pdf;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;

class DefaultController extends Controller
{

	/**
	 *
	 * @Secure(roles="ROLE_HOTEL,ROLE_DOMAIN");
	 *
	 */

	public function indexAction()
    {

        /* @var GlobalOption $globalOption */

        $globalOption = $this->getUser()->getGlobalOption();
        $em = $this->getDoctrine()->getManager();
	    $data = $_REQUEST;
	    $datetime = new \DateTime("now");
	    $data['startDate'] = $datetime->format('Y-m-d');
	    $data['endDate'] = $datetime->format('Y-m-d');

        $user = $this->getUser();
        $salesCashOverview = $em->getRepository('HotelBundle:HotelInvoice')->reportSalesOverview($user,$data);
        $purchaseCashOverview = $em->getRepository('HotelBundle:HotelPurchase')->reportPurchaseOverview($user,$data);
	    $transactionMethods = array(1,2,3,4);
	    $transactionCashOverview = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->cashOverview( $this->getUser(),$transactionMethods,$data);
	    $expenditureOverview = $em->getRepository('AccountingBundle:Expenditure')->reportForExpenditure($user->getGlobalOption(),$data);

	    $startMonthDate = $datetime->format('Y-m-01 00:00:00');
	    $endMonthDate = $datetime->format('Y-m-t 23:59:59');
	    $monthlySales = $this->getDoctrine()->getRepository('HotelBundle:HotelInvoice')->monthlySales($user,$data = array('startDate'=>$startMonthDate,'endDate'=>$endMonthDate));
	    $monthlyPurchase = $this->getDoctrine()->getRepository('HotelBundle:HotelPurchase')->monthlyPurchase($user,$data = array('startDate'=>$startMonthDate,'endDate'=>$endMonthDate));

	    $monthlySalesArr = array();
	    foreach($monthlySales as $row) {
		    $monthlySalesArr[$row['month']] = $row['total'];
	    }
	    $monthlyPurchaseArr = array();
	    foreach($monthlyPurchase as $row) {
		    $monthlyPurchaseArr[$row['month']] = $row['total'];
	    }

	    return $this->render('HotelBundle:Default:index.html.twig', array(
            'option'                    => $user->getGlobalOption() ,
            'globalOption'              => $globalOption,
            'transactionCashOverviews'  => $transactionCashOverview,
            'expenditureOverview'       => $expenditureOverview ,
            'salesCashOverview'         => $salesCashOverview ,
            'purchaseCashOverview'      => $purchaseCashOverview ,
            'monthlyPurchase'           => $monthlyPurchaseArr ,
            'monthlySales'              => $monthlySalesArr ,
            'searchForm'                => $data ,
        ));
    }

    public function pdfTodaySalesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $config = $user->getGlobalOption()->getHotelConfig();
        $salesTotalTransactionOverview = $em->getRepository('HotelBundle:HotelTreatmentPlan')->transactionOverview($config,$data);
        $html = $this->renderView(
            'HotelBundle:Default:today-sales-overview.html.twig', array(
                'salesOverview' => $salesTotalTransactionOverview,
                'previousSalesTransactionOverview' => '',
                'assignDoctors' => '',
                'searchForm' => $data,
            )
        );
        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy          = new Pdf($wkhtmltopdfPath);
        $pdf             = $snappy->getOutputFromHtml($html);
        $fileName ='sales-overview-'.date('d-m-Y').'.pdf';
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="'.$fileName.'"');
        echo $pdf;
        return new Response('');
    }




}
