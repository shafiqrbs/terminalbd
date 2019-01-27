<?php

namespace Appstore\Bundle\MedicineBundle\Controller;


use Appstore\Bundle\AccountingBundle\Entity\AccountPurchase;
use Appstore\Bundle\AccountingBundle\Entity\AccountSales;
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
        $cashOverview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->salesOverview($user, $data);

        $salesPrice = $em->getRepository('MedicineBundle:MedicineSales')->reportSalesOverview($user, $data);
        $purchasePrice = $em->getRepository('MedicineBundle:MedicineSales')->reportSalesItemPurchaseSalesOverview($user, $data);
        return $this->render('MedicineBundle:Report:sales/salesOverview.html.twig', array(
            'option' => $user->getGlobalOption(),
            'cashOverview' => $cashOverview,
            'salesPrice' => $salesPrice,
            'purchasePrice' => $purchasePrice,
            'branches' => $this->getUser()->getGlobalOption()->getBranches(),
            'searchForm' => $data,
        ));

    }

    public function salesDetailsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $entities = $em->getRepository('MedicineBundle:MedicineSales')->salesReport($user, $data);
        $pagination = $this->paginate($entities);
        $salesPurchasePrice = $em->getRepository('MedicineBundle:MedicineSales')->salesPurchasePriceReport($user, $data, $pagination);
        $purchaseSalesPrice = $em->getRepository('MedicineBundle:MedicineSales')->reportSalesItemPurchaseSalesOverview($user, $data);
        $transactionMethods = $em->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status' => 1), array('name' => 'ASC'));
        $cashOverview = $em->getRepository('MedicineBundle:MedicineSales')->reportSalesOverview($user, $data);
        return $this->render('MedicineBundle:Report:sales/sales.html.twig', array(
            'option' => $user->getGlobalOption(),
            'entities' => $pagination,
            'purchasePrice' => $salesPurchasePrice,
            'cashOverview' => $cashOverview,
            'purchaseSalesPrice' => $purchaseSalesPrice,
            'transactionMethods' => $transactionMethods,
            'branches' => $this->getUser()->getGlobalOption()->getBranches(),
            'searchForm' => $data,
        ));
    }

    public function salesStockItemAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $purchaseSalesPrice = $em->getRepository('MedicineBundle:MedicineSales')->reportSalesItemPurchaseSalesOverview($user, $data);
        $cashOverview = $em->getRepository('MedicineBundle:MedicineSales')->reportSalesOverview($user, $data);
        $entities = $em->getRepository('MedicineBundle:MedicineSalesItem')->reportSalesStockItem($user, $data);
        $pagination = $this->paginate($entities);
        return $this->render('MedicineBundle:Report:sales/salesStock.html.twig', array(
            'option' => $user->getGlobalOption(),
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
        $salesPurchasePrice = $em->getRepository('MedicineBundle:MedicineSales')->salesUserPurchasePriceReport($user, $data);
        $entities = $em->getRepository('MedicineBundle:MedicineSales')->salesUserReport($user, $data);
        return $this->render('MedicineBundle:Report:sales/salesUser.html.twig', array(
            'option' => $user->getGlobalOption(),
            'entities' => $entities,
            'salesPurchasePrice' => $salesPurchasePrice,
            'branches' => $this->getUser()->getGlobalOption()->getBranches(),
            'searchForm' => $data,
        ));
    }

    public function monthlyUserSalesAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $config = $user->getGlobalOption()->getBusinessConfig();
        $employees = $em->getRepository('DomainUserBundle:DomainUser')->getSalesUser($user->getGlobalOption());
        $entities = $em->getRepository('MedicineBundle:MedicineSales')->monthlySales($user, $data);
        $salesAmount = array();
        foreach ($entities as $row) {
            $salesAmount[$row['salesBy'] . $row['month']] = $row['total'];
        }
        return $this->render('MedicineBundle:Report:sales/salesMonthlyUser.html.twig', array(
            'inventory' => $config,
            'salesAmount' => $salesAmount,
            'employees' => $employees,
            'branches' => $this->getUser()->getGlobalOption()->getBranches(),
            'searchForm' => $data,
        ));

    }


    public function purchaseOverviewAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $purchaseCashOverview = $em->getRepository('MedicineBundle:MedicinePurchase')->reportPurchaseOverview($user, $data);
        $transactionCash = $em->getRepository('MedicineBundle:MedicinePurchase')->reportPurchaseTransactionOverview($user, $data);
        $purchaseMode = $em->getRepository('MedicineBundle:MedicinePurchase')->reportPurchaseModeOverview($user, $data);
        $stockMode = $em->getRepository('MedicineBundle:MedicinePurchase')->reportStockModeOverview($user, $data);
        return $this->render('MedicineBundle:Report:purchase/overview.html.twig', array(
            'option' => $user->getGlobalOption(),
            'purchaseCashOverview' => $purchaseCashOverview,
            'transactionCash' => $transactionCash,
            'purchaseMode' => $purchaseMode,
            'stockMode' => $stockMode,
            'searchForm' => $data,

        ));
    }

    public function systemOverviewAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $transactionMethods = array(1,2,3);
        $datetime = new \DateTime("now");
        $dataIncome['startDate'] = $user->getGlobalOption()->getCreated()->format('Y-m-d');
        $dataIncome['endDate'] = $datetime->format('Y-m-d');
        $income = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportMedicineTotalIncome($this->getUser(),$dataIncome);
        $transactionCashOverview = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->cashOverview( $this->getUser(),$transactionMethods,$data);
        $currentStockPrice = $em->getRepository('MedicineBundle:MedicineStock')->reportCurrentStockPrice($user);
        $accountPurchase = $em->getRepository('AccountingBundle:AccountPurchase')->accountPurchaseOverview($user, $data);
        $accountSales = $em->getRepository('AccountingBundle:AccountSales')->salesOverview($user, $data);
        $accountExpenditure = $em->getRepository('AccountingBundle:Expenditure')->expenditureOverview($user, $data);
        return $this->render('MedicineBundle:Report:systemOverview.html.twig', array(
            'option'                => $user->getGlobalOption(),
            'income'     => $income,
            'transactionCashOverviews'     => $transactionCashOverview,
            'currentStockPrice'     => $currentStockPrice,
            'accountPurchase'       => $accountPurchase,
            'accountSales'          => $accountSales,
            'accountExpenditure'    => $accountExpenditure,
            'searchForm'            => $data,
        ));
    }

    public function purchaseVendorAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $entities = $em->getRepository('MedicineBundle:MedicinePurchase')->purchaseVendorReport($user, $data);
        return $this->render('MedicineBundle:Report:purchase/purchaseVendor.html.twig', array(
            'option'        => $user->getGlobalOption(),
            'entities'      => $entities,
            'searchForm'    => $data,
        ));
    }

    public function salesVendorCustomerAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $entities = $em->getRepository('MedicineBundle:MedicinePurchase')->salesVendorCustomerReport($user, $data);
        $salesVendors = $em->getRepository('MedicineBundle:MedicinePurchase')->vendorCustomerSalesReport($user, $entities);
        return $this->render('MedicineBundle:Report:sales/salesVendorCustomer.html.twig', array(
            'option'        => $user->getGlobalOption(),
            'entities'      => $entities,
            'salesVendors'  => $salesVendors,
            'searchForm'    => $data,
        ));
    }

    public function purchaseVendorDetailsAction()
    {
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->findWithSearch($globalOption, $data);
        $pagination = $this->paginate($entities);
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->accountPurchaseOverview($globalOption, $data);
        $accountHead = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->getChildrenAccountHead($parent = array(5));
        $transactionMethods = $this->getDoctrine()->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status' => 1), array('name' => 'asc'));
        return $this->render('MedicineBundle:Report:purchase/purchase.html.twig', array(
            'globalOption'          => $globalOption,
            'entities'              => $pagination,
            'accountHead'           => $accountHead,
            'transactionMethods'    => $transactionMethods,
            'searchForm'            => $data,
            'overview'              => $overview,
        ));
    }

    public function purchaseVendorSalesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $purchasePrice = $em->getRepository('MedicineBundle:MedicinePurchase')->getPurchaseVendorPrice($user, $data);
        $salesPrice = $em->getRepository('MedicineBundle:MedicinePurchase')->getSalesVendorPrice($user, $data);
        return $this->render('MedicineBundle:Report:purchase/purchaseSalesVendor.html.twig', array(
            'option' => $user->getGlobalOption(),
            'purchasePrice' => $purchasePrice,
            'salesPrice' => $salesPrice,
            'searchForm' => $data,
        ));
    }

    public function purchaseBrandSalesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $brands = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->getBrandLists($user);
        $pagination = $this->paginate($brands);
        $purchasePrice = $em->getRepository('MedicineBundle:MedicinePurchase')->getPurchaseBrandReport($user, $pagination, $data);
        $salesPrice = $em->getRepository('MedicineBundle:MedicinePurchase')->getSalesBrandReport($user, $pagination, $data);
        return $this->render('MedicineBundle:Report:purchase/purchaseSalesBrand.html.twig', array(
            'option' => $user->getGlobalOption(),
            'purchasePrice' => $purchasePrice,
            'salesPrice' => $salesPrice,
            'brands' => $pagination,
            'searchForm' => $data,
        ));
    }

    public function purchaseBrandSalesDetailsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $purchasePrice = $em->getRepository('MedicineBundle:MedicinePurchase')->getPurchaseBrandDetailsReport($user, $data);
        $pagination = $this->paginate($purchasePrice);
        $salesPrice = $em->getRepository('MedicineBundle:MedicinePurchase')->getSalesBrandDetailsReport($user, $pagination, $data);
        $modeFor = $this->getDoctrine()->getRepository('MedicineBundle:MedicineParticularType')->findBy(array('modeFor' => 'brand'));

        return $this->render('MedicineBundle:Report:purchase/purchaseSalesBrandDetails.html.twig', array(
            'option' => $user->getGlobalOption(),
            'salesPrice' => $salesPrice,
            'pagination' => $pagination,
            'modeFor' => $modeFor,
            'searchForm' => $data,
        ));
    }

    public function productPurchaseStockSalesAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $config = $user->getGlobalOption()->getMedicineConfig();
        $entities = $em->getRepository('MedicineBundle:MedicinePurchase')->productPurchaseStockSalesReport($user, $data);
        $pagination = $this->paginate($entities);
        $racks = $this->getDoctrine()->getRepository('MedicineBundle:MedicineParticular')->findBy(array('medicineConfig' => $config, 'particularType' => '1'));
        $modeFor = $this->getDoctrine()->getRepository('MedicineBundle:MedicineParticularType')->findBy(array('modeFor' => 'brand'));
        return $this->render('MedicineBundle:Report:purchase/purchaseVendorStockSales.html.twig', array(
            'option' => $user->getGlobalOption(),
            'pagination' => $pagination,
            'racks' => $racks,
            'modeFor' => $modeFor,
            'searchForm' => $data,
        ));
    }


    public function productPurchaseStockSalesPriceAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $config = $user->getGlobalOption()->getMedicineConfig();
        $entities = $em->getRepository('MedicineBundle:MedicinePurchase')->productPurchaseStockSalesPriceReport($user, $data);
        $pagination = $this->paginate($entities);
        $racks = $this->getDoctrine()->getRepository('MedicineBundle:MedicineParticular')->findBy(array('medicineConfig' => $config, 'particularType' => '1'));
        $modeFor = $this->getDoctrine()->getRepository('MedicineBundle:MedicineParticularType')->findBy(array('modeFor' => 'brand'));
        return $this->render('MedicineBundle:Report:purchase/purchaseVendorStockSales.html.twig', array(
            'option' => $user->getGlobalOption(),
            'pagination' => $pagination,
            'racks' => $racks,
            'modeFor' => $modeFor,
            'searchForm' => $data,
        ));
    }


    public function purchaseBrandStockSalesAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $entities = $em->getRepository('MedicineBundle:MedicinePurchase')->purchaseVendorStockReport($user, $data);
        return $this->render('MedicineBundle:Report:purchase/purchaseVendorStockSales.html.twig', array(
            'option' => $user->getGlobalOption(),
            'entities' => $entities,
            'searchForm' => $data,
        ));
    }


    public function purchaseBrandAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $entities = $em->getRepository('MedicineBundle:MedicinePurchase')->purchaseVendorReport($user, $data);
        return $this->render('MedicineBundle:Report:purchase/purchaseBrand.html.twig', array(
            'option' => $user->getGlobalOption(),
            'entities' => $entities,
            'searchForm' => $data,
        ));
    }

    public function purchaseDetailsAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $entities = $em->getRepository('MedicineBundle:MedicinePurchase')->purchaseReport($user, $data);
        $pagination = $this->paginate($entities);
        $cashOverview = $em->getRepository('MedicineBundle:MedicinePurchase')->reportPurchaseOverview($user, $data);
        $transactionMethods = $em->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status' => 1), array('name' => 'ASC'));
        return $this->render('MedicineBundle:Report:purchase/purchase.html.twig', array(
            'option' => $user->getGlobalOption(),
            'entities' => $pagination,
            'cashOverview' => $cashOverview,
            'transactionMethods' => $transactionMethods,
            'searchForm' => $data,
        ));
    }
}