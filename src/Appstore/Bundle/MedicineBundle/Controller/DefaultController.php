<?php

namespace Appstore\Bundle\MedicineBundle\Controller;

use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {



        /* @var GlobalOption $globalOption */

        $globalOption = $this->getUser()->getGlobalOption();
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $datetime = new \DateTime("now");
        $data['startDate'] = $datetime->format('Y-m-d');
        $data['endDate'] = $datetime->format('Y-m-d');

	    $startMonthDate = $datetime->format('Y-m-01 00:00:00');
	    $endMonthDate = $datetime->format('Y-m-t 23:59:59');
	    $user = $this->getUser();
	    $salesCashOverview = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->reportSalesOverview($user,$data);
        $purchaseCashOverview = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchase')->reportPurchaseOverview($user,$data);
	    $transactionMethods = array(1,2,3,4);
        $transactionCashOverview = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->cashOverview( $this->getUser(),$transactionMethods,$data);
	    $expenditureOverview = $this->getDoctrine()->getRepository('AccountingBundle:Expenditure')->expenditureOverview($user,$data);
	    $purchaseUserReport = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->salesUserPurchasePriceReport($user,$data = array('startDate'=>$startMonthDate,'endDate'=>$endMonthDate));
	    $salesUserReport = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->salesUserReport($user,$data = array('startDate'=>$startMonthDate,'endDate'=>$endMonthDate));

	    $userSalesPurchasePrice = $em->getRepository('MedicineBundle:MedicineSales')->salesUserPurchasePriceReport($user,$data = array('startDate'=>$startMonthDate,'endDate'=>$endMonthDate));
	    $userEntities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->salesUserReport($user,$data = array('startDate'=>$startMonthDate,'endDate'=>$endMonthDate));

	    $employees = $this->getDoctrine()->getRepository('DomainUserBundle:DomainUser')->getSalesUser($user->getGlobalOption());

	    $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->currentMonthSales($user,$data);
	    $salesAmount = array();
	    foreach($entities as $row) {
		    $salesAmount[$row['salesBy'].$row['month']] = $row['total'];
	    }
	    return $this->render('MedicineBundle:Default:index.html.twig', array(
            'option'                    => $user->getGlobalOption() ,
            'globalOption'              => $globalOption,
            'transactionCashOverviews'  => $transactionCashOverview,
            'expenditureOverview'       => $expenditureOverview ,
            'salesCashOverview'         => $salesCashOverview ,
            'purchaseCashOverview'      => $purchaseCashOverview ,
            'salesUserReport'           => $salesUserReport ,
            'purchaseUserReport'        => $purchaseUserReport ,
            'userSalesPurchasePrice'    => $userSalesPurchasePrice ,
            'userEntities'              => $userEntities ,
            'salesAmount'      => $salesAmount ,
            'employees'      => $employees ,
            'searchForm'                => $data ,
        ));


    }
}
