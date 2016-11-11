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


    public function barCoder($barcoder){

        $barcode = new BarcodeGenerator();
        $barcode->setText($barcoder->getBarcode());
        $barcode->setType(BarcodeGenerator::Code128);
        $barcode->setScale(1);
        $barcode->setThickness(30);
        $barcode->setFontSize(8);
        $code = $barcode->generate();
        $data = '';
        $data .='<div class="barcode-block">';
        $data .='<div class="centered">';
        $data .='<p><span class="left">'.$barcoder->getItem()->getColor()->getName().'-'.$barcoder->getItem()->getSize()->getName().'</span><span class="right">'.$barcoder->getItem()->getVendor()->getVendorCode().'</span></p>';
        $data .='<div class="clearfix"></div>';
        $data .='<img src="data:image/png;base64,'.$code.'" />';
        $data .='<p><span class="center">TK '.$barcoder->getSalesPrice().' including VAT</span></p>';
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
            return $this->redirect($this->generateUrl('item'));
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

        $barCoder = $this->get('session')->get('barcodeQ');
        return $this->render('InventoryBundle:Barcode:print.html.twig', array(
            'barCoder'      => $barCoder
        ));
    }


}
