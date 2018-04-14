<?php

namespace Appstore\Bundle\InventoryBundle\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Knp\Snappy\Pdf;


/**
 * ItemColor controller.
 *
 */
class ReportController extends Controller
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
     * Lists all StockItem entities.
     *
     */
    public function overviewAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $priceOverview = $em->getRepository('InventoryBundle:StockItem')->getStockPriceOverview($inventory,$data);
        $stockOverview = $em->getRepository('InventoryBundle:StockItem')->getStockOverview($inventory,$data);
        return $this->render('InventoryBundle:Report:stockOverview.html.twig', array(
            'priceOverview' => $priceOverview[0],
            'stockOverview' => $stockOverview,
        ));
    }


    public function tillStockAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $stockOverview = $em->getRepository('InventoryBundle:StockItem')->getStockOverview($inventory,$data);
        $entities = $em->getRepository('InventoryBundle:StockItem')->stockItem($inventory,$data);
        $pagination = $this->paginate($entities);
        $previousQuantity = $em->getRepository('InventoryBundle:StockItem')->tillStockItem($mode ='previous',$inventory,$data);
        $purchaseQuantity = $em->getRepository('InventoryBundle:StockItem')->tillStockItem($mode ='purchase',$inventory,$data);
        $saleQuantity = $em->getRepository('InventoryBundle:StockItem')->tillStockItem($mode ='sales',$inventory,$data);

        return $this->render('InventoryBundle:Report:tillStock.html.twig', array(
            'entities' => $pagination,
            'previousQuantity' => $previousQuantity,
            'purchaseQuantity' => $purchaseQuantity,
            'saleQuantity' => $saleQuantity,
            'stockOverview' => $stockOverview,
            'searchForm' => $data,
        ));
    }

    public function periodicStockAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $stockOverview = $em->getRepository('InventoryBundle:StockItem')->getStockOverview($inventory,$data);
        $entities = $em->getRepository('InventoryBundle:StockItem')->stockItem($inventory,$data);
        $pagination = $this->paginate($entities);
        $previousQuantity = $em->getRepository('InventoryBundle:StockItem')->periodicStockItem($mode ='previous',$inventory,$data);
        $purchaseQuantity = $em->getRepository('InventoryBundle:StockItem')->periodicStockItem($mode ='purchase',$inventory,$data);
        $saleQuantity = $em->getRepository('InventoryBundle:StockItem')->periodicStockItem($mode ='sales',$inventory,$data);

        return $this->render('InventoryBundle:Report:periodicStock.html.twig', array(
            'entities' => $pagination,
            'previousQuantity' => $previousQuantity,
            'purchaseQuantity' => $purchaseQuantity,
            'saleQuantity' => $saleQuantity,
            'stockOverview' => $stockOverview,
            'searchForm' => $data,
        ));

    }

    public function operationalStockAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities = $em->getRepository('InventoryBundle:StockItem')->stockItem($inventory,$data);
        $pagination = $this->paginate($entities);
        $quantity = $em->getRepository('InventoryBundle:StockItem')->operationalStockItem($inventory,$data);
        return $this->render('InventoryBundle:Report:operationalStock.html.twig', array(
            'entities' => $pagination,
            'quantity' => $quantity,
            'searchForm' => $data,
        ));

    }

    public function groupStockAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        //$stockOverview = $em->getRepository('InventoryBundle:StockItem')->getStockOverview($inventory,$data);
        $entities = $em->getRepository('InventoryBundle:StockItem')->stockGroupItemName($inventory,$data);
        $pagination = $this->paginate($entities);

        $previousQuantity = $em->getRepository('InventoryBundle:StockItem')->groupStockItem($mode ='previous',$inventory,$data);
        $purchase = $em->getRepository('InventoryBundle:StockItem')->groupStockItem($mode ='purchase',$inventory,$data);
        $purchaseReturn = $em->getRepository('InventoryBundle:StockItem')->groupStockItem($mode ='purchaseReturn',$inventory,$data);
        $sale = $em->getRepository('InventoryBundle:StockItem')->groupStockItem($mode ='sales',$inventory,$data);
        $salesReturn = $em->getRepository('InventoryBundle:StockItem')->groupStockItem($mode ='salesReturn',$inventory,$data);
        $damage = $em->getRepository('InventoryBundle:StockItem')->groupStockItem($mode ='damage',$inventory,$data);

        return $this->render('InventoryBundle:Report:productGroupStock.html.twig', array(
            'entities' => $pagination,
            'previousQuantity' => $previousQuantity,
            'purchase' => $purchase,
            'purchaseReturn' => $purchaseReturn,
            'sale' => $sale,
            'salesReturn' => $salesReturn,
            'damage' => $damage,
            'searchForm' => $data,
        ));

    }

    public function stockAction($group)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities = $em->getRepository('InventoryBundle:StockItem')->getGroupStock($inventory,$group);
        $stockOverview = $em->getRepository('InventoryBundle:StockItem')->getStockOverview($inventory,$data);
        return $this->render('InventoryBundle:Report:'.$group.'.html.twig', array(
            'entities' => $entities,
            'stockOverview' => $stockOverview,
            'searchForm' => $data,
        ));
    }

    public function stockItemAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $stockOverview = $em->getRepository('InventoryBundle:StockItem')->getStockOverview($inventory,$data);
        $entities = $em->getRepository('InventoryBundle:Item')->findWithSearch($inventory,$data);
        $pagination = $this->paginate($entities);
        return $this->render('InventoryBundle:Report:stock.html.twig', array(
            'entities' => $pagination,
            'stockOverview' => $stockOverview,
            'searchForm' => $data,
        ));
    }


    public function purchaseAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities       = $em->getRepository('InventoryBundle:StockItem')->getProcessStock($inventory,$data);
        $pagination = $this->paginate($entities);
        $purchaseQuantity   = $em->getRepository('InventoryBundle:StockItem')->getGroupPurchaseItemStock($inventory,$data);

        return $this->render('InventoryBundle:Report:purchase.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
            'purchaseQuantity' => $purchaseQuantity,
        ));
    }

    public function salesStockItemAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $salesItemOverview = $em->getRepository('InventoryBundle:StockItem')->getSalesItemOverview($inventory,$data);
        $entities = $em->getRepository('InventoryBundle:StockItem')->getSalesItem($inventory,$data);
        $pagination = $this->paginate($entities);
        return $this->render('InventoryBundle:Report:salesStock.html.twig', array(
            'entities' => $pagination,
            'overview' => $salesItemOverview,
            'branches' => $this->getUser()->getGlobalOption()->getBranches(),
            'searchForm' => $data,
        ));
    }

    public function salesTransactionOverviewAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $cashOverview = $em->getRepository('InventoryBundle:Sales')->salesOverview($inventory,$data);
        $transactionCash = $em->getRepository('InventoryBundle:Sales')->salesTransactionOverview($inventory,$data);
        $salesMode = $em->getRepository('InventoryBundle:Sales')->salesModeOverview($inventory,$data);
        $salesProcess = $em->getRepository('InventoryBundle:Sales')->salesProcessOverview($inventory,$data);
        $transactionMethods = $em->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status' => 1), array('name' => 'ASC'));
        return $this->render('InventoryBundle:Report:salesOverview.html.twig', array(
            'cashOverview'              => $cashOverview ,
            'transactionCash'           => $transactionCash ,
            'salesMode'                 => $salesMode ,
            'salesProcess'              => $salesProcess ,
            'transactionMethods'        => $transactionMethods ,
            'branches'                  => $this->getUser()->getGlobalOption()->getBranches(),
            'searchForm'                => $data ,
        ));
    }
    public function salesTransactionAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities = $em->getRepository('InventoryBundle:Sales')->salesReport($inventory,$data);
        $pagination = $this->paginate($entities);
        $transactionMethods = $em->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status' => 1), array('name' => 'ASC'));
        return $this->render('InventoryBundle:Report:sales.html.twig', array(
            'entities'      => $pagination ,
            'inventory'      => $inventory ,
            'transactionMethods'      => $transactionMethods ,
            'branches' => $this->getUser()->getGlobalOption()->getBranches(),
            'searchForm'    => $data ,
        ));
    }





    public function categoryAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;

    }

    /**
     * Lists all ItemColor entities.
     *
     */
    public function overviewinAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig()->getId();

        $entities = $em->getRepository('InventoryBundle:StockItem')->getStockPriceOverview($inventory,$data);
        return $this->render('InventoryBundle:Report:itemVendor.html.twig', array(
            'entities' => $entities,
            'searchForm' => $data
        ));
    }

    /**
     * Lists all ItemColor entities.
     *
     */
    public function overviAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $stockOverview = $em->getRepository('InventoryBundle:StockItem')->getStockPriceOverview($inventory,$data);
        if(!empty($data['group'])){
            $twig = 'stockOverview';
        }else{
            $twig = $data['group'];
        }
        return $this->render('InventoryBundle:Report:'.$twig.'.html.twig', array(
            'stockOverview' => $stockOverview,
            'searchForm' => $data
        ));
    }



    public function pdfIncomeAction()
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $data = $_REQUEST;
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportIncome($globalOption,$data);
        $html = $this->renderView(
            'AccountingBundle:Report:incomePdf.html.twig', array(
                'overview' => $overview,
                'print' => ''
            )
        );
        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy          = new Pdf($wkhtmltopdfPath);
        $pdf             = $snappy->getOutputFromHtml($html);

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="incomePdf.pdf"');
        echo $pdf;

    }


    public function printIncomeAction()
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $data = $_REQUEST;
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportIncome($globalOption,$data);
        return $this->render('AccountingBundle:Report:incomePdf.html.twig', array(
            'overview' => $overview,
            'print' => '<script>window.print();</script>'
        ));

    }






}
