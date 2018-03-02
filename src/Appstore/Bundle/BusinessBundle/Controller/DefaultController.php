<?php

namespace Appstore\Bundle\BusinessBundle\Controller;

use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $config = $user->getGlobalOption()->getBusinessConfig();
        $salesTotalTransactionOverview = $em->getRepository('BusinessBundle:BusinessTreatmentPlan')->transactionOverview($config,$data);
        return $this->render('BusinessBundle:Default:index.html.twig', array(
            'salesOverview' => $salesTotalTransactionOverview,
            'previousSalesTransactionOverview' => '',
            'assignDoctors' => '',
            'searchForm' => $data,
        ));
    }

    public function pdfTodaySalesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $config = $user->getGlobalOption()->getBusinessConfig();
        $salesTotalTransactionOverview = $em->getRepository('BusinessBundle:BusinessTreatmentPlan')->transactionOverview($config,$data);
        $html = $this->renderView(
            'BusinessBundle:Default:today-sales-overview.html.twig', array(
                'salesOverview' => $salesTotalTransactionOverview,
                'previousSalesTransactionOverview' => '',
                'assignDoctors' => '',
                'searchForm' => $data,
            )
        );
        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy          = new Pdf($wkhtmltopdfPath);
        $pdf             = $snappy->getOutputFromHtml($html);
        $fileName ='sales-overview-'.date('d-m-Y').'.pdf';
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="'.$fileName.'"');
        echo $pdf;
        return new Response('');
    }




}
