<?php

namespace Appstore\Bundle\MedicineBundle\Controller;


use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchase;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchaseItem;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSales;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSalesItem;
use Appstore\Bundle\MedicineBundle\Entity\MedicineStock;
use Appstore\Bundle\MedicineBundle\Form\SalesItemType;
use Appstore\Bundle\MedicineBundle\Form\SalesType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Vendor controller.
 *
 */
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

    public function salesOverviewAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $cashOverview = $em->getRepository('MedicineBundle:MedicineSales')->reportSalesOverview($user,$data);
        $purchaseSalesPrice = $em->getRepository('MedicineBundle:MedicineSales')->reportSalesItemPurchaseSalesOverview($user,$data);
        $transactionCash = $em->getRepository('MedicineBundle:MedicineSales')->reportSalesTransactionOverview($user,$data);
        $salesProcess = $em->getRepository('MedicineBundle:MedicineSales')->reportSalesProcessOverview($user,$data);
        $transactionMethods = $em->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status' => 1), array('name' => 'ASC'));

        return $this->render('MedicineBundle:Report:sales/salesOverview.html.twig', array(
            'option'                    => $user->getGlobalOption() ,
            'cashOverview'              => $cashOverview ,
            'purchaseSalesPrice'        => $purchaseSalesPrice ,
            'transactionCash'           => $transactionCash ,
            'salesProcess'              => $salesProcess ,
            'transactionMethods'        => $transactionMethods ,
            'branches'                  => $this->getUser()->getGlobalOption()->getBranches(),
            'searchForm'                => $data ,
        ));

    }

    public function salesDetailsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $entities = $em->getRepository('MedicineBundle:MedicineSales')->salesReport($user,$data);
        $pagination = $this->paginate($entities);
        $salesPurchasePrice = $em->getRepository('MedicineBundle:MedicineSales')->salesPurchasePriceReport($user,$data,$pagination);
        $purchaseSalesPrice = $em->getRepository('MedicineBundle:MedicineSales')->reportSalesItemPurchaseSalesOverview($user,$data);
        $transactionMethods = $em->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status' => 1), array('name' => 'ASC'));
        $cashOverview = $em->getRepository('MedicineBundle:MedicineSales')->reportSalesOverview($user,$data);
        return $this->render('InventoryBundle:Report:sales/sales.html.twig', array(
            'option'                => $user->getGlobalOption() ,
            'entities'              => $pagination ,
            'purchasePrice'         => $salesPurchasePrice ,
            'cashOverview'          => $cashOverview,
            'purchaseSalesPrice'    => $purchaseSalesPrice,
            'transactionMethods'    => $transactionMethods ,
            'branches'              => $this->getUser()->getGlobalOption()->getBranches(),
            'searchForm'            => $data,
        ));
    }

    public function salesStockItemAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $purchaseSalesPrice = $em->getRepository('MedicineBundle:MedicineSales')->reportSalesItemPurchaseSalesOverview($user,$data);
        $cashOverview = $em->getRepository('MedicineBundle:MedicineSales')->reportSalesOverview($user,$data);
        $entities = $em->getRepository('MedicineBundle:MedicineSales')->reportSalesItem($user,$data);
        $pagination = $this->paginate($entities);

        return $this->render('MedicineBundle:Report:sales/salesStock.html.twig', array(
            'option'  => $user->getGlobalOption() ,
            'entities' => $pagination,
            'cashOverview' => $cashOverview,
            'purchaseSalesItem' => $purchaseSalesPrice,
            'branches' => $this->getUser()->getGlobalOption()->getBranches(),
            'searchForm' => $data,
        ));
    }

    public function salesUserAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $inventory = $user->getGlobalOption()->getInventoryConfig();
        $salesPurchasePrice = $em->getRepository('MedicineBundle:MedicineSales')->salesUserPurchasePriceReport($user,$data);
        $entities = $em->getRepository('MedicineBundle:MedicineSales')->salesUserReport($user,$data);
        return $this->render('MedicineBundle:Report:sales/salesUser.html.twig', array(
            'option'  => $user->getGlobalOption() ,
            'entities'      => $entities ,
            'salesPurchasePrice'      => $salesPurchasePrice ,
            'branches' => $this->getUser()->getGlobalOption()->getBranches(),
            'searchForm'    => $data ,
        ));
    }

    public function purchaseOverviewAction(){

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $cashOverview = $em->getRepository('MedicineBundle:MedicinePurchase')->reportPurchaseOverview($user,$data);
        $transactionCash = $em->getRepository('MedicineBundle:MedicinePurchase')->reportPurchaseTransactionOverview($user,$data);
        $salesProcess = $em->getRepository('MedicineBundle:MedicinePurchase')->reportPurchaseProcessOverview($user,$data);
        $purchaseMode = $em->getRepository('MedicineBundle:MedicinePurchase')->reportPurchaseModeOverview($user,$data);
        $transactionMethods = $em->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status' => 1), array('name' => 'ASC'));
        return $this->render('MedicineBundle:Report:purchase/overview.html.twig', array(
            'option'                    => $user->getGlobalOption() ,
            'cashOverview'              => $cashOverview ,
            'transactionCash'           => $transactionCash ,
            'salesProcess'              => $salesProcess ,
            'purchaseMode'              => $purchaseMode ,
            'transactionMethods'        => $transactionMethods ,
            'searchForm'                => $data ,
        ));
    }

    public function purchaseDetailsAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $entities = $em->getRepository('MedicineBundle:MedicinePurchase')->purchaseReport($user,$data);
        $pagination = $this->paginate($entities);
        $cashOverview = $em->getRepository('MedicineBundle:MedicinePurchase')->reportPurchaseOverview($user,$data);
        $transactionMethods = $em->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status' => 1), array('name' => 'ASC'));
        return $this->render('MedicineBundle:Report:purchase/purchase.html.twig', array(
            'option'                => $user->getGlobalOption() ,
            'entities'              => $pagination ,
            'cashOverview'          => $cashOverview,
            'transactionMethods'    => $transactionMethods ,
            'searchForm'            => $data,
        ));
    }

}
