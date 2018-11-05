<?php

namespace Appstore\Bundle\MedicineBundle\Controller;

use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;

class DefaultController extends Controller
{

	/**
	 * Lists all HotelCategory entities.
	 *
	 * @Secure(roles="ROLE_MEDICINE,ROLE_DOMAIN");
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
	    $salesCashOverview = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->reportSalesOverview($user,$data);
        $purchaseCashOverview = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchase')->reportPurchaseOverview($user,$data);
	    $transactionMethods = array(1);
        $transactionCashOverview = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->cashOverview( $this->getUser(),$transactionMethods,$data);
	    $expenditureOverview = $this->getDoctrine()->getRepository('AccountingBundle:Expenditure')->expenditureOverview($user,$data);
	    $salesUserReport = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->salesUserReport($user,array('startDate'=>$data['startDate'],'endDate'=>$data['endDate']));
	 //   $userEntities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->salesUserReport($user,$data);
	    $startMonthDate = $datetime->format('Y-m-01 00:00:00');
	    $endMonthDate = $datetime->format('Y-m-t 23:59:59');
	    $medicineSalesMonthly = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->medicineSalesMonthly($user,$data = array('startDate'=>$startMonthDate,'endDate'=>$endMonthDate));
	    $medicinePurchaseMonthly = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchase')->medicinePurchaseMonthly($user,$data = array('startDate'=>$startMonthDate,'endDate'=>$endMonthDate));
	    $shortMedicineCount = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->findMedicineShortListCount($user);
	    $expiryMedicineCount = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseItem')->expiryMedicineCount($user);

	    //   $purchaseUserReport = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->salesUserPurchasePriceReport($user,$data = array('startDate'=>$startMonthDate,'endDate'=>$endMonthDate));
	  //  $userSalesPurchasePrice = $em->getRepository('MedicineBundle:MedicineSales')->salesUserPurchasePriceReport($user,$data = array('startDate'=>$startMonthDate,'endDate'=>$endMonthDate));

	    $employees = $this->getDoctrine()->getRepository('DomainUserBundle:DomainUser')->getSalesUser($user->getGlobalOption());

	    $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->currentMonthSales($user,$data);
	    $userSalesAmount = array();
	    foreach($entities as $row) {
		    $userSalesAmount[$row['salesBy'].$row['month']] = $row['total'];
	    }

	    $medicineSalesMonthlyArr = array();
	    foreach($medicineSalesMonthly as $row) {
		    $medicineSalesMonthlyArr[$row['month']] = $row['total'];
	    }
	    $medicinePurchaseMonthlyArr = array();
	    foreach($medicinePurchaseMonthly as $row) {
		    $medicinePurchaseMonthlyArr[$row['month']] = $row['total'];
	    }


	    return $this->render('MedicineBundle:Default:index.html.twig', array(
            'option'                    => $user->getGlobalOption() ,
            'globalOption'              => $globalOption,
            'transactionCashOverviews'  => $transactionCashOverview,
            'expenditureOverview'       => $expenditureOverview ,
            'salesCashOverview'         => $salesCashOverview ,
            'purchaseCashOverview'      => $purchaseCashOverview ,
            'medicineSalesMonthly'      => $medicineSalesMonthlyArr ,
            'medicinePurchaseMonthly'   => $medicinePurchaseMonthlyArr ,
            'salesUserReport'           => $salesUserReport ,
            'userSalesAmount'           => $userSalesAmount ,
            'employees'                 => $employees ,
            'shortMedicineCount'        => $shortMedicineCount ,
            'expiryMedicineCount'       => $expiryMedicineCount ,
            'searchForm'                => $data ,
        ));


    }
}
