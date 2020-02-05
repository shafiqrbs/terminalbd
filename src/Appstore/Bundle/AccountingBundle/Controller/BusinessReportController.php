<?php

namespace Appstore\Bundle\AccountingBundle\Controller;

use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BusinessReportController extends Controller
{


	public function balanceSheetAction()
	{

		$em = $this->getDoctrine()->getManager();
		$data = $_REQUEST;
		$overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportBusinessMonthlyIncome($this->getUser(),$data);
		$accountHeads = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->findBy(array('isParent'=>1),array('sorting'=>'ASC'));
		$accountHeads = $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->findBy(array('isParent'=>1),array('sorting'=>'ASC'));
		return $this->render('AccountingBundle:Report/Business:balanceSheet.html.twig', array(
			'overview' => $overview,
			'accountHeads' => $accountHeads,
			'searchForm' => $data,
		));
	}


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
        $user = $this->getUser();
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportBusinessIncome($user,$data);
        return $this->render('AccountingBundle:Report/Business:income.html.twig', array(
            'overview' => $overview,
            'searchForm' => $data,
        ));
    }

    public function pdfIncomeAction()
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $data = $_REQUEST;
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportBusinessIncome($globalOption,$data);
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
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportBusinessIncome($globalOption,$data);
        return $this->render('AccountingBundle:Report/Hms:incomePdf.html.twig', array(
            'overview' => $overview,
            'print' => '<script>window.print();</script>'
        ));

    }

    public function monthlyIncomeAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportBusinessMonthlyIncome($this->getUser(),$data);
        return $this->render('AccountingBundle:Report/Business:monthlyIncome.html.twig', array(
            'overview' => $overview,
            'searchForm' => $data,
        ));
    }




}
