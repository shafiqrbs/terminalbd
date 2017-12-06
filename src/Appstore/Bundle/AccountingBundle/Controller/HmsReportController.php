<?php

namespace Appstore\Bundle\AccountingBundle\Controller;

use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HmsReportController extends Controller
{
    public function balanceAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportHmsIncome($globalOption,$data);
        return $this->render('AccountingBundle:Report:incomePdf.html.twig', array(
            'overview' => $overview,
            'searchForm' => $data,
        ));
    }

    public function incomeAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportHmsIncome($globalOption,$data);
        return $this->render('AccountingBundle:Report/Hms:income.html.twig', array(
            'overview' => $overview,
            'searchForm' => $data,
        ));
    }

    public function pdfIncomeAction()
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $data = $_REQUEST;
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportHmsIncome($globalOption,$data);
        $html = $this->renderView(
            'AccountingBundle:Report/Hms:incomePdf.html.twig', array(
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

        return new Response('');
    }

    public function printIncomeAction()
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $data = $_REQUEST;
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportHmsIncome($globalOption,$data);
        return $this->render('AccountingBundle:Report/Hms:incomePdf.html.twig', array(
            'overview' => $overview,
            'print' => '<script>window.print();</script>'
        ));

    }

    public function monthlyIncomeAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportHmsMonthlyIncome( $this->getUser(),$data);
        return $this->render('AccountingBundle:Report/Hms:monthlyIncome.html.twig', array(
            'overview' => $overview,
            'searchForm' => $data,
        ));
    }

    public function expenditureSummaryAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:Expenditure')->reportHmsExpenditure( $this->getUser(),$data);
        return $this->render('AccountingBundle:Report/Hms:expenditureSummary.html.twig', array(
            'overview' => $overview,
            'searchForm' => $data,
        ));
    }

    public function expenditureDetailsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:Expenditure')->reportHmsExpenditure( $this->getUser(),$data);
        return $this->render('AccountingBundle:Report/Hms:expenditureDetails.html.twig', array(
            'overview' => $overview,
            'searchForm' => $data,
        ));
    }



}
