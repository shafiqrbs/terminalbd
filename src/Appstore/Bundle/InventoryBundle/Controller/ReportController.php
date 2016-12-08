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
        $purchaseQuantity   = $em->getRepository('InventoryBundle:StockItem')->getGroupPurchaseItemStock($inventory,$data);

        return $this->render('InventoryBundle:Report:purchase.html.twig', array(
            'entities' => $entities,
            'searchForm' => $data,
            'purchaseQuantity' => $purchaseQuantity,
        ));
    }

    public function salesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $pagination = $em->getRepository('InventoryBundle:StockItem')->getVendorStock($inventory,$data);
        return $this->render('InventoryBundle:Report:brand.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
        ));
    }

    public function productAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $pagination = $em->getRepository('InventoryBundle:StockItem')->getVendorStock($inventory,$data);
        return $this->render('InventoryBundle:Report:product.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
        ));
    }

    public function colorAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $pagination = $em->getRepository('InventoryBundle:StockItem')->getVendorStock($inventory,$data);
        return $this->render('InventoryBundle:Report:color.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
        ));
    }

    public function sizeAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $pagination = $em->getRepository('InventoryBundle:StockItem')->getVendorStock($inventory,$data);
        return $this->render('InventoryBundle:Report:size.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
        ));
    }

    public function categoryAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $pagination = $em->getRepository('InventoryBundle:StockItem')->getVendorStock($inventory,$data);
        return $this->render('InventoryBundle:Report:category.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
        ));
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
