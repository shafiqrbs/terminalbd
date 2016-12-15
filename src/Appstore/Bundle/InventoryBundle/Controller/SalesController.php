<?php

namespace Appstore\Bundle\InventoryBundle\Controller;

use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Appstore\Bundle\InventoryBundle\Entity\SalesItem;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Appstore\Bundle\InventoryBundle\Entity\Sales;
use Appstore\Bundle\InventoryBundle\Form\SalesType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sales controller.
 *
 */
class SalesController extends Controller
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
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities = $em->getRepository('InventoryBundle:Sales')->salesLists($inventory,$data);
        $pagination = $this->paginate($entities);
        return $this->render('InventoryBundle:Sales:index.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
        ));
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $sales = $request->request->get('sales');
        $barcode = $request->request->get('barcode');
        $sales = $em->getRepository('InventoryBundle:Sales')->find($sales);
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $purchaseItem = $em->getRepository('InventoryBundle:PurchaseItem')->returnPurchaseItemDetails($inventory,$barcode);
        $checkQuantity = $this->getDoctrine()->getRepository('InventoryBundle:SalesItem')->checkPurchaseQuantity($purchaseItem);
        $itemDetails= '';
        $salesItems = '';
        $salesTotal = '';
        if($purchaseItem && $checkQuantity == true ){
            //$itemDetails = $em->getRepository('InventoryBundle:PurchaseItem')->findBarcode($purchaseItem);
            $this->getDoctrine()->getRepository('InventoryBundle:SalesItem')->insertSalesItems($sales,$purchaseItem);
            $salesTotal = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->updateSalesTotalPrice($sales);
            $salesItems = $em->getRepository('InventoryBundle:SalesItem')->getSalesItems($sales);
        }else{
            $salesTotal = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->updateSalesTotalPrice($sales);
            $salesItems = $em->getRepository('InventoryBundle:SalesItem')->getSalesItems($sales);
        }

        //return new Response($salesItems);
        return new Response(json_encode(array('salesTotal'=>$salesTotal,'purchaseItem' => $itemDetails ,'salesItem'=>$salesItems)));
        exit;
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function salesItemUpdateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $salesItemId = $request->request->get('salesItemId');
        $quantity = $request->request->get('quantity');
        $salesPrice = $request->request->get('salesPrice');
        $customPrice = $request->request->get('customPrice');

        $salesItem = $em->getRepository('InventoryBundle:SalesItem')->find($salesItemId);
        if ($salesItem->getPurchaseItem()->getQuantity() >= $quantity){

            $salesItem->setQuantity($quantity);
            $salesItem->setSalesPrice($salesPrice);
            if (!empty($customPrice)) {
                $salesItem->setCustomPrice($customPrice);
            }
            $salesItem->setSubTotal($quantity * $salesPrice);
            $em->persist($salesItem);
            $em->flush();
            $salesTotal = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->updateSalesTotalPrice($salesItem->getSales());
            return new Response(json_encode(array('salesTotal' => $salesTotal,'msg' => 'success')));

        }else{

            $salesTotal = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->updateSalesTotalPrice($salesItem->getSales());
            return new Response(json_encode(array('salesTotal' => $salesTotal,'msg' => 'invalid')));
        }
        exit;
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new Sales();
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entity->setInventoryConfig($inventory);
        $entity->setSalesBy($this->getUser());
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('inventory_sales_edit', array('code' => $entity->getInvoice())));

    }

    /**
     * Finds and displays a Sales entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:Sales')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sales entity.');
        }
        return $this->render('InventoryBundle:Sales:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function editAction($code)
    {
        $em = $this->getDoctrine()->getManager();
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entity = $em->getRepository('InventoryBundle:Sales')->findOneBy(array('inventoryConfig'=>$inventory , 'invoice'=>$code));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sales entity.');
        }

        $editForm = $this->createEditForm($entity);
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $todaySales = $em->getRepository('InventoryBundle:Sales')->todaySales($inventory);
        $todaySalesOverview = $em->getRepository('InventoryBundle:Sales')->todaySalesOverview($inventory);
        return $this->render('InventoryBundle:Sales:new.html.twig', array(
            'entity'      => $entity,
            'todaySales'      => $todaySales,
            'todaySalesOverview'      => $todaySalesOverview,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Sales entity.wq
     *
     * @param Sales $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Sales $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new SalesType($globalOption), $entity, array(
            'action' => $this->generateUrl('inventory_sales_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function updateAction(Request $request, Sales $entity)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sales entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        if ($editForm->isValid()) {

            $data = $request->request->all();
            if ($data['paymentAmount'] > 0) {

                if (!empty($data['sales']['mobile'])) {
                    $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findExistingCustomer($entity, $data['sales']['mobile']);
                    $entity->setCustomer($customer);
                } else {
                    $globalOption = $this->getUser()->getGlobalOption();
                    $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $globalOption, 'name' => 'Default'));
                    $entity->setCustomer($customer);
                }
                $entity->setSubTotal($data['paymentSubTotal']);
                if($entity->getInventoryConfig()->getVatEnable() == 1 && $entity->getInventoryConfig()->getVatPercentage() > 0 ){
                    $vat = $em->getRepository('InventoryBundle:Sales')->getCulculationVat($data['paymentTotal']);
                    $entity->setVat($vat);
                }
                $entity->setDue($data['dueAmount']);
                $entity->setDiscount($data['discount']);
                $entity->setTotal($data['paymentTotal']);
                $entity->setPayment($data['paymentTotal'] - $data['dueAmount']);
                if ($data['paymentTotal'] <= $data['paymentAmount']) {
                    $entity->setPaymentStatus('Paid');
                } else if ($data['paymentTotal'] > $data['paymentAmount']) {
                    $entity->setPaymentStatus('Due');
                }
                if (empty($data['sales']['salesBy'])){
                    $entity->setSalesBy($this->getUser());
                }
                $entity->setApprovedBy($this->getUser());
                $em->flush();

                $em->getRepository('InventoryBundle:Item')->getItemSalesUpdate($entity);
                $em->getRepository('InventoryBundle:StockItem')->insertSalesStockItem($entity);
                $em->getRepository('InventoryBundle:GoodsItem')->updateInventorySalesItem($entity);
                $accountSales = $em->getRepository('AccountingBundle:AccountSales')->insertAccountSales($entity);
                $em->getRepository('AccountingBundle:Transaction')->salesTransaction($entity, $accountSales);
                return $this->redirect($this->generateUrl('inventory_sales_new'));
            }
        }
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $todaySales = $em->getRepository('InventoryBundle:Sales')->todaySales($inventory);
        $todaySalesOverview = $em->getRepository('InventoryBundle:Sales')->todaySalesOverview($inventory);
        return $this->render('InventoryBundle:Sales:new.html.twig', array(
            'entity'      => $entity,
            'todaySales'      => $todaySales,
            'todaySalesOverview'      => $todaySalesOverview,
            'form'   => $editForm->createView(),
        ));

    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_APPROVE")
     */

    public function approveAction(Sales $entity)
    {
        if (!empty($entity)) {
            $em = $this->getDoctrine()->getManager();
            $entity->setPaymentStatus('Paid');
            $entity->setApprovedBy($this->getUser());
            $em->flush();
            $em->getRepository('InventoryBundle:Item')->getItemSalesUpdate($entity);
            $em->getRepository('InventoryBundle:StockItem')->insertSalesStockItem($entity);
            $accountSales = $em->getRepository('AccountingBundle:AccountSales')->insertAccountSales($entity);
            $em->getRepository('AccountingBundle:Transaction')->salesTransaction($entity,$accountSales);
            return new Response('success');
        } else {
            return new Response('failed');
        }
        exit;
    }


    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function deleteAction(Sales $sales)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$sales) {
            throw $this->createNotFoundException('Unable to find Sales entity.');
        }
        if(!empty($sales->getSalesImport())){
            $salesImport = $sales->getSalesImport();
            $em->remove($salesImport);
        }
        $sales->getSalesImport();
        $em->remove($sales);
        $em->flush();
        return new Response(json_encode(array('success'=>'success')));
        exit;
    }

    /**
     * Deletes a SalesItem entity.
     *
     */
    public function itemDeleteAction(Sales $sales , $salesItem)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('InventoryBundle:SalesItem')->find($salesItem);
        if (!$salesItem) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }

        $em->remove($entity);
        $em->flush();
        $salesTotal = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->updateSalesTotalPrice($sales);
        $salesTotal = $salesTotal > 0 ? $salesTotal : 0;
        return new Response(json_encode(array('salesTotal'=>$salesTotal,'success'=>'success')));
        exit;

    }

    public function itemPurchaseDetailsAction(Request $request)
    {
        $item = $request->request->get('item');
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $data = $this->getDoctrine()->getRepository('InventoryBundle:PurchaseItem')->itemPurchaseDetails($inventory,$item);
        return new Response($data);
    }

    public function printAction($code)
    {
       // echo $code;
        //$this->printWithOutEscPos();
        //$connector = new FilePrintConnector("/dev/usb/lp1");
        $connector = new NetworkPrintConnector("192.168.1.250", 9100);
        $printer = new Printer($connector);
        try {
            $printer -> text("Hello World!\n");
            $printer -> text("Hello World!\n");
//            $printer -> text("Hello World!\n");
//            $printer -> text("Hello World!\n");
//            $printer -> text("Hello World!\n");
//            $printer -> text("Hello World!\n");
//            $printer -> text("Hello World!\n");
//            $printer -> text("Hello World!\n");
//            $printer -> text("Hello World!\n");
            $printer -> cut();
            $printer -> close();

        } finally {
            $printer -> close();
        }
        exit;
    }

    public function printWithOutEscPos(){



        $texttoprint = <<<EOD
        HEADER
        --------------------
          item: price
        --------------------
         Cup  : 100
         Plate: 200
        --------------------
        Total:  300
                   Thanks
EOD;
        //$texttoprint = "RECIPT TEXT \n NEXT LINE \n MORE STUFF";
        $texttoprint = stripslashes($texttoprint);
        $fp = fsockopen("192.168.1.250", 9100, $errno, $errstr, 10);
        if (!$fp) {
            echo "$errstr ($errno)<br />\n";
        } else {
            fwrite($fp, "\033\100");
            $out = $texttoprint . "\r\n";
            fwrite($fp, $out);
            fwrite($fp, "\012\012\012\012\012\012\012\012\012\033\151\010\004\001");
            fclose($fp);
        }

    }

    public function invoicePrintAction(Sales $entity)
    {


        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sales entity.');
        }
        return $this->render('InventoryBundle:Sales:invoice.html.twig', array(
            'entity'      => $entity,
        ));
    }

    public function deleteEmptyInvoiceAction()
    {
        $entities = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->findBy(array('paymentStatus' => 'Pending'));
        $em = $this->getDoctrine()->getManager();
        foreach ($entities as $entity){
            $em->remove($entity);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('inventory_sales'));
    }

}
