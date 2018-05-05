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
        $cashOverview = $em->getRepository('MedicineBundle:MedicineSales')->reportSalesOverview($user,$data);
        $purchaseSalesPrice = $em->getRepository('MedicineBundle:MedicineSales')->reportSalesItemPurchaseSalesOverview($user,$data);
        $transactionCash = $em->getRepository('MedicineBundle:MedicineSales')->reportSalesTransactionOverview($user,$data);
        $salesProcess = $em->getRepository('MedicineBundle:MedicineSales')->reportSalesProcessOverview($user,$data);
        $transactionMethods = $em->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status' => 1), array('name' => 'ASC'));

        return $this->render('MedicineBundle:Default:index.html.twig', array(
            'option'                    => $user->getGlobalOption() ,
            'globalOption'              => $globalOption,
            'cashOverview'              => $cashOverview ,
            'purchaseSalesPrice'        => $purchaseSalesPrice ,
            'transactionCash'           => $transactionCash ,
            'salesProcess'              => $salesProcess ,
            'transactionMethods'        => $transactionMethods ,
            'branches'                  => $this->getUser()->getGlobalOption()->getBranches(),
            'searchForm'                => $data ,
        ));
    }
}
