<?php
/**
 * Created by PhpStorm.
 * User: hasan
 * Date: 8/20/19
 * Time: 11:07 AM
 */

namespace Appstore\Bundle\TallyBundle\Controller;


use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\Response;
use TCPDF;

class TallyReportController extends Controller
{

    public function mushukFourThreeAction(){

        $html = $this->renderView('TallyBundle:Report:mushok_4_3.html.twig');
        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/local/bin/wkhtmltopdf --header-html';
        $snappy = new Pdf($wkhtmltopdfPath);
        $snappy->setOption('page-size', 'A4');
        $snappy->setOption('orientation', 'Landscape');

        return new Response(
            $snappy->getOutputFromHtml($html),
            200,
            array(
                'Content-Type'          => 'application/pdf',
//                'Content-Disposition'   => 'attachment; filename="file.pdf"'

            )
        );


    }

    public function mushukSixOneAction(){

       // $purchaseItem = $this->getDoctrine()->getRepository('TallyBundle:PurchaseItem')->find(50);
        $item = $this->getDoctrine()->getRepository('TallyBundle:Item')->find(8);
        $purchaseItems = $this->getDoctrine()->getRepository('TallyBundle:PurchaseItem')->findBy(array('item'=>8,'mode'=>'purchase'),array('updated'=>"DESC"));
        $html = $this->renderView(
            'TallyBundle:Report:mushok_6_1.html.twig', array(
                'purchaseItems' => $purchaseItems,
                'item' => $item,
            )
        );

        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy          = new Pdf($wkhtmltopdfPath);
        $snappy->setOption('orientation', 'Landscape');
        $snappy->setOption('page-size', 'A4');
        $pdf             = $snappy->getOutputFromHtml($html);
        header('Content-Type: application/pdf');
//        header('Content-Disposition: attachment; filename="monthlyIncomePdf.pdf"');
        echo $pdf;
        return new Response('');
    }

    public function mushukSixTwoAction(){

        $item = $this->getDoctrine()->getRepository('TallyBundle:Item')->find(8);
        $salesItem = $this->getDoctrine()->getRepository('TallyBundle:StockItem')->findBy(array('item' => 8 ,'mode'=>'sales'),array('updated'=>"DESC"));
        $html = $this->renderView(
            'TallyBundle:Report:mushok_6_2.html.twig', array(
                'salesItems' => $salesItem,
                'item' => $item,
            )
        );
        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy          = new Pdf($wkhtmltopdfPath);
        $snappy->setOption('orientation', 'Landscape');
        $snappy->setOption('page-size', 'A4');
        $pdf             = $snappy->getOutputFromHtml($html);
        header('Content-Type: application/pdf');
//        header('Content-Disposition: attachment; filename="monthlyIncomePdf.pdf"');
        echo $pdf;
        return new Response('');
    }

    public function mushukSixTwoOneAction(){

        $html = $this->renderView('TallyBundle:Report:mushok_6_2_1.html.twig');
        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/local/bin/wkhtmltopdf --header-html';
        $snappy = new Pdf($wkhtmltopdfPath);
        $snappy->setOption('page-size', 'A4');
        $snappy->setOption('orientation', 'Landscape');

        return new Response(
            $snappy->getOutputFromHtml($html),
            200,
            array(
                'Content-Type'          => 'application/pdf',
//                'Content-Disposition'   => 'attachment; filename="file.pdf"'

            )
        );


    }

    public function mushukSixThreeAction(){

        $html = $this->renderView('TallyBundle:Report:mushok_6_3.html.twig');
        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/local/bin/wkhtmltopdf --header-html';
        $snappy = new Pdf($wkhtmltopdfPath);
        $snappy->setOption('page-size', 'A4');
        $snappy->setOption('orientation', 'Landscape');

        return new Response(
            $snappy->getOutputFromHtml($html),
            200,
            array(
                'Content-Type'          => 'application/pdf',
//                'Content-Disposition'   => 'attachment; filename="file.pdf"'

            )
        );


    }


}