<?php

namespace Appstore\Bundle\HospitalBundle\Controller;

use Setting\Bundle\ToolBundle\Entity\AppModule;
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
        $income = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportMedicineIncome($this->getUser(),$data);
        $user = $this->getUser();
        $salesCashOverview = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->reportSalesOverview($user,$data);
        $purchaseCashOverview = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchase')->reportPurchaseOverview($user,$data);
        $transactionMethods = array(1);
        $transactionCashOverview = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->cashOverview( $this->getUser(),$transactionMethods,$data);
        $expenditureOverview = $this->getDoctrine()->getRepository('AccountingBundle:Expenditure')->expenditureOverview($user,$data);

        $salesTransactionOverview = $em->getRepository('HospitalBundle:InvoiceTransaction')->todaySalesOverview($user,$data,'true','diagnostic');
        $previousSalesTransactionOverview = $em->getRepository('HospitalBundle:InvoiceTransaction')->todaySalesOverview($user,$data,'false','diagnostic');


        return $this->render('HospitalBundle:Default:index.html.twig', array(
            'option'                    => $user->getGlobalOption() ,
            'globalOption'              => $globalOption,
            'salesTransactionOverview' => $salesTransactionOverview,
            'previousSalesTransactionOverview' => $previousSalesTransactionOverview,
            'transactionCashOverviews' => $transactionCashOverview,
            'expenditureOverview'       => $expenditureOverview ,
            'salesCashOverview'         => $salesCashOverview ,
            'purchaseCashOverview'      => $purchaseCashOverview ,
            'searchForm'                => $data ,
        ));

    }

}
