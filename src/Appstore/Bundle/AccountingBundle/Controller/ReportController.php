<?php

namespace Appstore\Bundle\AccountingBundle\Controller;

use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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

    public function balanceAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportIncome($globalOption,$data);
        return $this->render('AccountingBundle:Report:incomePdf.html.twig', array(
            'overview' => $overview,
            'searchForm' => $data,
        ));
    }

    public function incomeAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportSalesIncome($this->getUser(),$data);
        return $this->render('AccountingBundle:Report:income.html.twig', array(
            'overview' => $overview,
            'searchForm' => $data,
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

        return new Response('');
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

    public function monthlyIncomeAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportMonthlyIncome( $this->getUser(),$data);
        return $this->render('AccountingBundle:Report:monthlyIncome.html.twig', array(
            'overview' => $overview,
            'searchForm' => $data,
        ));
    }

    public function cashReceivePaymentAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:A')->reportMonthlyIncome( $this->getUser(),$data);
        return $this->render('AccountingBundle:Report:monthlyIncome.html.twig', array(
            'overview' => $overview,
            'searchForm' => $data,
        ));
    }

	public function cashReceivePaymentDetailsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportMonthlyIncome( $this->getUser(),$data);
        return $this->render('AccountingBundle:Report:monthlyIncome.html.twig', array(
            'overview' => $overview,
            'searchForm' => $data,
        ));
    }




    public function expenditureSummaryAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:Expenditure')->expenditureOverview($user,$data);
        $parent = array(23,37);
        $expenditureHead = $em->getRepository('AccountingBundle:Transaction')->parentsAccountHead($user->getGlobalOption(),$parent,$data);
        return $this->render('AccountingBundle:Report/Expenditure:accountHead.html.twig', array(
            'overview' => $overview,
            'expenditureHead' => $expenditureHead,
            'searchForm' => $data,
        ));
    }

    public function expenditureCategoryAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $expenditureOverview = $em->getRepository('AccountingBundle:Expenditure')->reportForExpenditure($user->getGlobalOption(),$data);
        return $this->render('AccountingBundle:Report/Expenditure:category.html.twig', array(
            'expenditureOverview' => $expenditureOverview,
            'searchForm' => $data,
        ));
    }

    public function expenditureDetailsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
	    $option = $this->getUser()->getGlobalOption();
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:Expenditure')->expenditureOverview( $this->getUser(),$data);
        $entities = $em->getRepository('AccountingBundle:Expenditure')->findWithSearch( $this->getUser(),$data);
        $pagination = $this->paginate($entities);
        $transactionMethods = $this->getDoctrine()->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status'=>1),array('name'=>'asc'));
        $categories = $this->getDoctrine()->getRepository('AccountingBundle:ExpenseCategory')->findBy(array('globalOption'=> $option, 'status'=>1),array('name'=>'asc'));
        $heads = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->getExpenseAccountHead();
        return $this->render('AccountingBundle:Report/Expenditure:expenditure.html.twig', array(
            'overview' => $overview,
            'entities' => $pagination,
            'transactionMethods' => $transactionMethods,
            'heads' => $heads,
            'categories' => $categories,
            'searchForm' => $data,
        ));
    }


}
