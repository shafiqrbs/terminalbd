<?php

namespace Appstore\Bundle\InventoryBundle\Controller;

use Doctrine\Common\Util\Debug;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Appstore\Bundle\InventoryBundle\Entity\Item;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use Symfony\Component\HttpFoundation\Response;
use Hackzilla\BarcodeBundle\Utility\Barcode;

/**
 * Barcode controller.
 *
 */
class BarcodeController extends Controller
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


    public function  indexAction(){

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities = $em->getRepository('InventoryBundle:PurchaseItem')->findWithSearch($inventory,$data);
        $pagination = $this->paginate($entities);
        return $this->render('InventoryBundle:Barcode:index.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data
        ));

    }

    public function barCoder($barcoder)
    {

        if ((!empty($barcoder->getItem()->getColor()) and $barcoder->getItem()->getInventoryConfig()->getBarcodeColor() == 1) and (!empty($barcoder->getItem()->getSize()) and $barcoder->getItem()->getInventoryConfig()->getBarcodeSize() == 1)) {

            if ($barcoder->getItem()->getColor()->getName() != 'Default'){
                $color = $barcoder->getItem()->getColor()->getName();
            }else{
                $color ='';
            }
            if ($barcoder->getItem()->getSize()->getName() != 'Default'){
                $size = '-'.$barcoder->getItem()->getSize()->getName();
            }else{
                $size ='';
            }

            $sizeColor =  $color.$size;

        } elseif (!empty($barcoder->getItem()->getSize()) and $barcoder->getItem()->getInventoryConfig()->getBarcodeSize() == 1 and $barcoder->getItem()->getSize()->getName() != 'Default') {
            $sizeColor = $barcoder->getItem()->getSize()->getName();
        } elseif (!empty($barcoder->getItem()->getColor()) and $barcoder->getItem()->getInventoryConfig()->getBarcodeColor() == 1 and $barcoder->getItem()->getColor()->getName() != 'Default') {
            $sizeColor = $barcoder->getItem()->getColor()->getName();
        }else {
            $sizeColor = '';
        }

        if (!empty($barcoder->getItem()->getVendor()) and $barcoder->getItem()->getInventoryConfig()->getBarcodeBrandVendor() == 2 ){
            $vendorBrand = $barcoder->getItem()->getVendor()->getVendorCode();
        }elseif(!empty($barcoder->getItem()->getBrand()) and $barcoder->getItem()->getInventoryConfig()->getBarcodeBrandVendor() == 1){
            $vendorBrand = $barcoder->getItem()->getBrand()->getBrandCode();
        }else{
            $vendorBrand = '';
        }
        $barcodeWidth = $barcoder->getItem()->getInventoryConfig()->getBarcodeWidth().'px';
        $barcodeHeight = $barcoder->getItem()->getInventoryConfig()->getBarcodeHeight().'px';
        $barcodeMargin = $barcoder->getItem()->getInventoryConfig()->getBarcodeMargin();
        if($barcodeMargin == 0 ){
            $margin = 0;
        }else{
            $margin = $barcodeMargin.'px';
        }
        $barcodePadding = $barcoder->getItem()->getInventoryConfig()->getBarcodePadding();
        if($barcodePadding == 0 ){
            $padding = 0;
        }else{
            $padding = $barcodePadding.'px';
        }
        $barcodeBorder = $barcoder->getItem()->getInventoryConfig()->getBarcodeBorder();
        if($barcodeBorder > 0 ){
            $border = $barcodeBorder.'px';
        }else{
            $border = 0;
        }
        $scale = $barcoder->getItem()->getInventoryConfig()->getBarcodeScale();
        $fontsize = $barcoder->getItem()->getInventoryConfig()->getBarcodeFontSize();
        $thickness = $barcoder->getItem()->getInventoryConfig()->getBarcodeThickness();
        $barcode = new BarcodeGenerator();
        $barcode->setText($barcoder->getBarcode());
        $barcode->setType(BarcodeGenerator::Code128);
        $barcode->setScale($scale);
        $barcode->setThickness($thickness);
        $barcode->setFontSize($fontsize);
        $code = $barcode->generate();
        $data = '';
        $data .='<div class="barcode-block" style="width:'.$barcodeWidth.'; height:'.$barcodeHeight.'; border:'.$border.'; margin-top:'.$margin.'; padding:'.$padding.'; ">';
        $data .='<div class="centered">';
        $data .='<p><span class="left">'.$sizeColor.'</span><span class="right">'.$vendorBrand.'</span></p>';
        $data .='<div class="clearfix"></div>';
        $data .='<img src="data:image/png;base64,'.$code.'" />';
        $data .='<p><span class="center">TK '.$barcoder->getSalesPrice().' '.$barcoder->getItem()->getInventoryConfig()->getBarcodeText().'</span></p>';

        $data .='</div>';
        $data .='</div>';
        return $data;
    }

    public function createBarcodeAction(Request $request)
    {
        $id = $request->request->get('item');
        $this->getResponse()->setCookie('barcode', $id);
    }

    public function addAction(Request $request)
    {
        $data = explode(',',$request->cookies->get('barcodes'));

        $em = $this->getDoctrine()->getManager();
        if(is_null($data)) {
            return $this->redirect($this->generateUrl('inventory_barcode'));
        }
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities = $this->getDoctrine()->getRepository('InventoryBundle:PurchaseItem')->getBarcodeForPrint($inventory,$data);
        return $this->render('InventoryBundle:Barcode:add.html.twig', array(
            'entities'      => $entities
        ));

    }

    public function createAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $x = 0;
        foreach ($data['item'] as $row) {
            $barcode = $em->getRepository('InventoryBundle:PurchaseItem')->find($row);
            for ($i = 0; $data['barcodeQnt'][$x] > $i; $i++){
                $barCoder[] = $this->barCoder($barcode);
            }
            $x++;
        }
        $this->get('session')->set('barcodeQ',$barCoder);
        return $this->render('InventoryBundle:Barcode:barcode.html.twig', array(
            'barCoder'      => $barCoder
        ));

    }

    public function printAction()
    {
        $printLeftMargin = $this->getUser()->getGlobalOption()->getInventoryConfig()->getPrintLeftMargin();
        $barCoder = $this->get('session')->get('barcodeQ');
        if($printLeftMargin == 0){
            $leftMargin = 0;
        }else{
            $leftMargin = $printLeftMargin;
        }

        return $this->render('InventoryBundle:Barcode:print.html.twig', array(
            'printLeftMargin'      => $leftMargin,
            'barCoder'      => $barCoder
        ));
    }


}
