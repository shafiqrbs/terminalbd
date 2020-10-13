<?php


namespace Appstore\Bundle\BusinessBundle\Controller;


use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Invoice controller.
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
		$cashOverview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->salesOverview($user,$data);

		$salesPrice = $em->getRepository( 'BusinessBundle:BusinessInvoice' )->reportSalesOverview($user,$data);
		$purchasePrice = $em->getRepository( 'BusinessBundle:BusinessInvoice' )->reportSalesItemPurchaseSalesOverview($user,$data);


        if(empty($data['pdf'])){

            return $this->render('BusinessBundle:Report:sales/salesSummary.html.twig', array(
                'option'                    => $user->getGlobalOption() ,
                'cashOverview'              => $cashOverview ,
                'salesPrice'                => $salesPrice ,
                'purchasePrice'             => $purchasePrice ,
                'branches'                  => $this->getUser()->getGlobalOption()->getBranches(),
                'searchForm'                => $data ,
            ));

        }else{

            $html = $this->renderView(
                'BusinessBundle:Report:sales/salesSummaryPdf.html.twig', array(
                    'globalOption'                  => $this->getUser()->getGlobalOption(),
                    'option'                    => $user->getGlobalOption() ,
                    'cashOverview'              => $cashOverview ,
                    'salesPrice'                => $salesPrice ,
                    'purchasePrice'             => $purchasePrice ,
                    'branches'                  => $this->getUser()->getGlobalOption()->getBranches(),
                    'searchForm'                => $data ,
                )
            );
            $this->downloadPdf($html,'salesSummaryPdf.pdf');
        }

	}

	public function salesDetailsAction()
	{
		$em = $this->getDoctrine()->getManager();
		$data = $_REQUEST;
		$user = $this->getUser();
		$entities = $em->getRepository( 'BusinessBundle:BusinessInvoice' )->salesReport($user,$data);
		$pagination = $this->paginate($entities);
		$salesPurchasePrice = $em->getRepository( 'BusinessBundle:BusinessInvoice' )->salesPurchasePriceReport($user,$data,$pagination);
		$purchaseSalesPrice = $em->getRepository( 'BusinessBundle:BusinessInvoice' )->reportSalesItemPurchaseSalesOverview($user,$data);
		$transactionMethods = $em->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status' => 1), array('name' => 'ASC'));
		$cashOverview = $em->getRepository( 'BusinessBundle:BusinessInvoice' )->reportSalesOverview($user,$data);


        if(empty($data['pdf'])){

            return $this->render('BusinessBundle:Report:sales/sales.html.twig', array(
                'option'                => $user->getGlobalOption() ,
                'entities'              => $pagination ,
                'purchasePrice'         => $salesPurchasePrice ,
                'cashOverview'          => $cashOverview,
                'purchaseSalesPrice'    => $purchaseSalesPrice,
                'transactionMethods'    => $transactionMethods ,
                'branches'              => $this->getUser()->getGlobalOption()->getBranches(),
                'searchForm'            => $data,
            ));

        }else{
            $html = $this->renderView(
                'BusinessBundle:Report:sales/salesPdf.html.twig', array(
                    'globalOption'                  => $this->getUser()->getGlobalOption(),
                    'option'                => $user->getGlobalOption() ,
                    'entities'              => $pagination ,
                    'purchasePrice'         => $salesPurchasePrice ,
                    'cashOverview'          => $cashOverview,
                    'purchaseSalesPrice'    => $purchaseSalesPrice,
                    'transactionMethods'    => $transactionMethods ,
                    'branches'              => $this->getUser()->getGlobalOption()->getBranches(),
                    'searchForm'            => $data,
                )
            );
            $this->downloadPdf($html,'salesPdf.pdf');
        }
	}

	public function salesStockItemAction()
	{
		$em = $this->getDoctrine()->getManager();
		$data = $_REQUEST;
		$user = $this->getUser();
		$purchaseSalesPrice = $em->getRepository( 'BusinessBundle:BusinessInvoice' )->reportSalesItemPurchaseSalesOverview($user,$data);
		$cashOverview = $em->getRepository( 'BusinessBundle:BusinessInvoice' )->reportSalesOverview($user,$data);
		$entities = $em->getRepository('BusinessBundle:BusinessInvoiceParticular')->reportSalesStockItem($user,$data);
		$pagination = $this->paginate($entities);
		$type = $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticularType')->findBy(array('status'=>1));
		$category = $this->getDoctrine()->getRepository('BusinessBundle:Category')->findBy(array('status'=>1));

		return $this->render('BusinessBundle:Report:sales/salesStock.html.twig', array(
			'option'  => $user->getGlobalOption() ,
			'entities' => $pagination,
			'cashOverview' => $cashOverview,
			'purchaseSalesItem' => $purchaseSalesPrice,
			'types' => $type,
			'categories' => $category,
			'branches' => $this->getUser()->getGlobalOption()->getBranches(),
			'searchForm' => $data,
		));
	}

    public function salesCommissionStockItemAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $entities = $em->getRepository('BusinessBundle:BusinessInvoiceParticular')->reportCommissionSalesStockItem($user,$data);
        $pagination = $this->paginate($entities);
        $vendors = $this->getDoctrine()->getRepository('AccountingBundle:AccountVendor')->findBy(array('globalOption'=>$user->getGlobalOption(),'status'=>1),array('companyName'=>'ASC'));

        if(empty($data['pdf'])){
            return $this->render('BusinessBundle:Report:sales/commissionSalesStock.html.twig', array(
                'option'  => $user->getGlobalOption() ,
                'entities' => $pagination,
                'vendors' => $vendors,
                'searchForm' => $data,
            ));

        }else{

            $html = $this->renderView(
                'BusinessBundle:Report:sales/commissionSalesStockPdf.html.twig', array(
                    'option'  => $user->getGlobalOption() ,
                    'entities' => $pagination,
                    'vendors' => $vendors,
                    'searchForm' => $data,
                )
            );
            $this->downloadPdf($html,'commissionSalesStock.pdf');
        }
    }

	public function productLedgerAction()
	{
		$em = $this->getDoctrine()->getManager();
		$data = $_REQUEST;
		$user = $this->getUser();
		$entities = $em->getRepository('BusinessBundle:BusinessInvoiceParticular')->reportCustomerSalesItem($user,$data);
		$pagination = $this->paginate($entities);
		$type = $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticularType')->findBy(array('status'=>1));
		$category = $this->getDoctrine()->getRepository('BusinessBundle:Category')->findBy(array('status'=>1));

		return $this->render('BusinessBundle:Report:productLedger.html.twig', array(
			'option'  => $user->getGlobalOption() ,
			'entities' => $pagination,
			'types' => $type,
			'categories' => $category,
			'branches' => $this->getUser()->getGlobalOption()->getBranches(),
			'searchForm' => $data,
		));
	}




	public function salesUserAction()
	{
		$em = $this->getDoctrine()->getManager();
		$data = $_REQUEST;
		$user = $this->getUser();
		$salesPurchasePrice = $this->getDoctrine()->getRepository('BusinessBundle:BusinessInvoice')->salesUserPurchasePriceReport($user,$data);
		$entities = $em->getRepository('BusinessBundle:BusinessInvoice')->salesUserReport($user,$data);
		return $this->render('BusinessBundle:Report:sales/salesUser.html.twig', array(
			'option'  => $user->getGlobalOption() ,
			'entities'      => $entities ,
			'salesPurchasePrice'      => $salesPurchasePrice ,
			'branches' => $this->getUser()->getGlobalOption()->getBranches(),
			'searchForm'    => $data ,
		));
	}

    public function salesSrAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $entities = $em->getRepository('BusinessBundle:BusinessParticular')->salesSrReport($user,$data);
        $executives = $this->getDoctrine()->getRepository('BusinessBundle:Marketing')->findBy(array('businessConfig' => $user->getGlobalOption()->getBusinessConfig()));
        if(empty($data['pdf'])){

            return $this->render('BusinessBundle:Report:sales/salesSr.html.twig', array(
                'option'            => $user->getGlobalOption() ,
                'entities'          => $entities ,
                'executives'        => $executives ,
                'branches'          => $this->getUser()->getGlobalOption()->getBranches(),
                'searchForm'        => $data ,
            ));

        }else{

            $marketing = $this->getDoctrine()->getRepository('BusinessBundle:Marketing')->find($data['marketing']);
            $html = $this->renderView(
                'BusinessBundle:Report:sales/salesSrPdf.html.twig', array(
                    'option'        => $user->getGlobalOption() ,
                    'marketing'     => $marketing ,
                    'entities'      => $entities ,
                    'searchForm'    => $data ,
                )
            );
            $this->downloadPdf($html,'sales-sr.pdf');
        }

    }

    public function salesDsrAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $entities = $em->getRepository('BusinessBundle:BusinessParticular')->salesSrReport($user,$data);
        $executives = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findBy(array('globalOption' => $user->getGlobalOption()));

        if(empty($data['pdf'])){

            return $this->render('BusinessBundle:Report:sales/salesDsr.html.twig', array(
                'option'            => $user->getGlobalOption() ,
                'entities'          => $entities,
                'executives'        => $executives,
                'branches'          => $this->getUser()->getGlobalOption()->getBranches(),
                'searchForm'        => $data,
            ));

        }else{

            $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->find($data['customer']);
            $html = $this->renderView(
                'BusinessBundle:Report:sales/salesDsrPdf.html.twig', array(
                    'option'        => $user->getGlobalOption(),
                    'marketing'     => $customer,
                    'entities'      => $entities,
                    'searchForm'    => $data,
                )
            );
            $this->downloadPdf($html,'sales-dsr.pdf');

        }
    }

    public function salesAreaAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $entities = $em->getRepository('BusinessBundle:BusinessParticular')->salesSrReport($user,$data);
        $executives = $this->getDoctrine()->getRepository('BusinessBundle:BusinessArea')->findBy(array('businessConfig' => $user->getGlobalOption()->getBusinessConfig()));
        if(empty($data['pdf'])){

            return $this->render('BusinessBundle:Report:sales/salesArea.html.twig', array(
                'option'            => $user->getGlobalOption() ,
                'entities'          => $entities,
                'executives'        => $executives,
                'branches'          => $this->getUser()->getGlobalOption()->getBranches(),
                'searchForm'        => $data,
            ));

        }else{

            $customer = $this->getDoctrine()->getRepository('BusinessBundle:BusinessArea')->find($data['area']);
            $html = $this->renderView(
                'BusinessBundle:Report:sales/salesAreaPdf.html.twig', array(
                    'option'        => $user->getGlobalOption(),
                    'marketing'     => $customer,
                    'entities'      => $entities,
                    'searchForm'    => $data,
                )
            );
            $this->downloadPdf($html,'sales-area.pdf');

        }
    }

	public function monthlyUserSalesAction(){

		$em = $this->getDoctrine()->getManager();
		$data = $_REQUEST;
		$user = $this->getUser();
		$config = $user->getGlobalOption()->getBusinessConfig();
		$employees = $em->getRepository('DomainUserBundle:DomainUser')->getSalesUser($user->getGlobalOption());
		$entities = $em->getRepository('BusinessBundle:BusinessInvoice')->currentMonthSales($user,$data);
		$salesAmount = array();
		foreach($entities as $row) {
			$salesAmount[$row['salesBy'].$row['month']] = $row['total'];
		}
		return $this->render('BusinessBundle:Report:sales/salesMonthlyUser.html.twig', array(
			'inventory'      => $config ,
			'salesAmount'      => $salesAmount ,
			'employees'      => $employees ,
			'branches' => $this->getUser()->getGlobalOption()->getBranches(),
			'searchForm'    => $data ,
		));

	}


	public function purchaseOverviewAction(){

		$em = $this->getDoctrine()->getManager();
		$data = $_REQUEST;
		$user = $this->getUser();

		$purchaseCashOverview   = $em->getRepository('BusinessBundle:BusinessPurchase')->reportPurchaseOverview($user,$data);
		$transactionCash        = $em->getRepository('BusinessBundle:BusinessPurchase')->reportPurchaseTransactionOverview($user,$data);
		$purchaseMode           = $em->getRepository('BusinessBundle:BusinessPurchase')->reportPurchaseModeOverview($user,$data);

		return $this->render('BusinessBundle:Report:purchase/overview.html.twig', array(
			'option'                            => $user->getGlobalOption() ,
			'purchaseCashOverview'              => $purchaseCashOverview ,
			'transactionCash'                   => $transactionCash ,
			'purchaseMode'                      => $purchaseMode ,
			'stockMode'                         => '' ,
			'searchForm'                        => $data,

		));
	}

	public function purchaseVendorAction()
	{
		$em = $this->getDoctrine()->getManager();
		$data = $_REQUEST;
		$user = $this->getUser();
		$entities = $em->getRepository('BusinessBundle:BusinessPurchase')->purchaseVendorReport($user,$data);
		return $this->render('BusinessBundle:Report:purchase/purchaseVendor.html.twig', array(
			'option'                => $user->getGlobalOption() ,
			'entities'              => $entities ,
			'searchForm'            => $data,
		));
	}


	public function purchaseVendorDetailsAction()
	{
		$data = $_REQUEST;
		$globalOption = $this->getUser()->getGlobalOption();
		$entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->findWithSearch($globalOption,$data);
		$pagination = $this->paginate($entities);
		$overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->accountPurchaseOverview($globalOption,$data);
		$accountHead = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->getChildrenAccountHead($parent =array(5));
		$transactionMethods = $this->getDoctrine()->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status'=>1),array('name'=>'asc'));
		return $this->render('BusinessBundle:Report:purchase/purchase.html.twig', array(
			'globalOption' => $globalOption,
			'entities' => $pagination,
			'accountHead' => $accountHead,
			'transactionMethods' => $transactionMethods,
			'searchForm' => $data,
			'overview' => $overview,
		));
	}



	public function productPurchaseStockSalesAction()
	{

		$em = $this->getDoctrine()->getManager();
		$data = $_REQUEST;
		$user = $this->getUser();
		$config = $user->getGlobalOption()->getMedicineConfig();
		$entities = $em->getRepository('BusinessBundle:BusinessPurchase')->productPurchaseStockSalesReport($user,$data);
		$pagination = $this->paginate($entities);
		$racks = $this->getDoctrine()->getRepository('BusinessBundle:MedicineParticular')->findBy(array('medicineConfig'=> $config,'particularType'=>'1'));
		$modeFor = $this->getDoctrine()->getRepository('BusinessBundle:MedicineParticularType')->findBy(array('modeFor'=>'brand'));
		return $this->render('BusinessBundle:Report:purchase/purchaseVendorStockSales.html.twig', array(
			'option'                => $user->getGlobalOption() ,
			'pagination'            => $pagination ,
			'racks'                 => $racks,
			'modeFor'               => $modeFor,
			'searchForm'            => $data,
		));
	}


	public function productPurchaseStockSalesPriceAction()
	{

		$em = $this->getDoctrine()->getManager();
		$data = $_REQUEST;
		$user = $this->getUser();
		$config = $user->getGlobalOption()->getMedicineConfig();
		$entities = $em->getRepository('BusinessBundle:BusinessPurchase')->productPurchaseStockSalesPriceReport($user,$data);
		$pagination = $this->paginate($entities);
		$racks = $this->getDoctrine()->getRepository('BusinessBundle:MedicineParticular')->findBy(array('medicineConfig'=> $config,'particularType'=>'1'));
		$modeFor = $this->getDoctrine()->getRepository('BusinessBundle:MedicineParticularType')->findBy(array('modeFor'=>'brand'));
		return $this->render('BusinessBundle:Report:purchase/purchaseVendorStockSales.html.twig', array(
			'option'                => $user->getGlobalOption() ,
			'pagination'              => $pagination ,
			'racks' => $racks,
			'modeFor' => $modeFor,
			'searchForm'            => $data,
		));
	}


	public function purchaseBrandStockSalesAction()
	{

		$em = $this->getDoctrine()->getManager();
		$data = $_REQUEST;
		$user = $this->getUser();
		$entities = $em->getRepository('BusinessBundle:BusinessPurchase')->purchaseVendorStockReport($user,$data);
		return $this->render('BusinessBundle:Report:purchase/purchaseVendorStockSales.html.twig', array(
			'option'                => $user->getGlobalOption() ,
			'entities'              => $entities ,
			'searchForm'            => $data,
		));
	}


	public function purchaseBrandAction()
	{
		$em = $this->getDoctrine()->getManager();
		$data = $_REQUEST;
		$user = $this->getUser();
		$entities = $em->getRepository('BusinessBundle:BusinessPurchase')->purchaseVendorReport($user,$data);
		return $this->render('BusinessBundle:Report:purchase/purchaseBrand.html.twig', array(
			'option'                => $user->getGlobalOption() ,
			'entities'              => $entities ,
			'searchForm'            => $data,
		));
	}

	public function purchaseDetailsAction()
	{

		$em = $this->getDoctrine()->getManager();
		$data = $_REQUEST;
		$user = $this->getUser();
		$entities = $em->getRepository('BusinessBundle:BusinessPurchase')->purchaseReport($user,$data);
		$pagination = $this->paginate($entities);
		$cashOverview = $em->getRepository('BusinessBundle:BusinessPurchase')->reportPurchaseOverview($user,$data);
		$transactionMethods = $em->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status' => 1), array('name' => 'ASC'));
		return $this->render('BusinessBundle:Report:purchase/purchase.html.twig', array(
			'option'                => $user->getGlobalOption() ,
			'entities'              => $pagination ,
			'cashOverview'          => $cashOverview,
			'transactionMethods'    => $transactionMethods ,
			'searchForm'            => $data,
		));
	}

    public function vendorCommissionBaseSalesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $entities = $em->getRepository('BusinessBundle:BusinessInvoiceParticular')->reportVendorCommissionSalesItem($user,$data);
        $pagination = $this->paginate($entities);
        $vendors = $this->getDoctrine()->getRepository('AccountingBundle:AccountVendor')->findBy(['globalOption' => $this->getUser()->getGlobalOption()],['companyName'=>'ASC']);



        if(empty($data['pdf'])){

            return $this->render('BusinessBundle:Report:sales/vendorCommissionSalesItem.html.twig', array(
                'option'  => $user->getGlobalOption() ,
                'entities' => $pagination,
                'vendors' => $vendors,
                'searchForm' => $data,
            ));

        }else{

            $html = $this->renderView(
                'BusinessBundle:Report:sales/vendorCommissionSalesItemPdf.html.twig', array(
                    'option'  => $user->getGlobalOption() ,
                    'entities' => $pagination,
                    'vendors' => $vendors,
                    'searchForm' => $data,
                )
            );
            $this->downloadPdf($html,'vendor-commission-sales-item.pdf');

        }

    }

    public function customerCommissionBaseSalesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $entities = $em->getRepository('BusinessBundle:BusinessInvoiceParticular')->reportCustomerSalesItem($user,$data);
        $pagination = $this->paginate($entities);
        $type = $this->getDoctrine()->getRepository('BusinessBundle:BusinessParticularType')->findBy(array('status'=>1));
        $category = $this->getDoctrine()->getRepository('BusinessBundle:Category')->findBy(array('status'=>1));

        return $this->render('BusinessBundle:Report:sales/customerSalesItem.html.twig', array(
            'option'  => $user->getGlobalOption() ,
            'entities' => $pagination,
            'types' => $type,
            'categories' => $category,
            'branches' => $this->getUser()->getGlobalOption()->getBranches(),
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
        return new Response('');
    }


}
