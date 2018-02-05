<?php

namespace Appstore\Bundle\DmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $config = $user->getGlobalOption()->getDmsConfig();
        $salesTotalTransactionOverview = $em->getRepository('DmsBundle:DmsTreatmentPlan')->transactionOverview($config,$data);
        return $this->render('DmsBundle:Default:index.html.twig', array(
            'salesOverview' => $salesTotalTransactionOverview,
            'previousSalesTransactionOverview' => '',
            'assignDoctors' => '',
            'searchForm' => $data,
        ));
    }



}
