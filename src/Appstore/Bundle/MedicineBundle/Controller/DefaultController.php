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

        $user = $this->getUser();
        $salesCashOverview = $em->getRepository('MedicineBundle:MedicineSales')->reportSalesOverview($user,$data);
        $purchaseCashOverview = $em->getRepository('MedicineBundle:MedicinePurchase')->reportPurchaseOverview($user,$data);
        $transactionCashOverview = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->transactionWiseOverview( $this->getUser(),$data);
        $expenditureOverview = $em->getRepository('AccountingBundle:Expenditure')->reportForExpenditure($user->getGlobalOption(),$data);

        return $this->render('BusinessBundle:Default:index.html.twig', array(
            'option'                    => $user->getGlobalOption() ,
            'globalOption'              => $globalOption,
            'transactionCashOverviews'  => $transactionCashOverview,
            'expenditureOverview'       => $expenditureOverview ,
            'salesCashOverview'         => $salesCashOverview ,
            'purchaseCashOverview'      => $purchaseCashOverview ,
            'searchForm'                => $data ,
        ));
    }
}
