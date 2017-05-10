<?php

namespace Appstore\Bundle\AccountingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\AccountingBundle\Entity\AccountSales;
use Appstore\Bundle\AccountingBundle\Form\AccountSalesType;
use Symfony\Component\HttpFoundation\Response;

/**
 * AccountSalesHospital controller.
 *
 */
class AccountSalesHospitalController extends Controller
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
     * Lists all AccountSales entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $entities = $em->getRepository('AccountingBundle:AccountSales')->findWithSearch($user,$data);
        $pagination = $this->paginate($entities);
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->salesOverview($user,$data);
        $accountHead = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->getChildrenAccountHead($parent =array(20,29));
        $transactionMethods = $this->getDoctrine()->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status'=>1),array('name'=>'asc'));
        return $this->render('AccountingBundle:AccountSalesHospital:index.html.twig', array(
            'entities' => $pagination,
            'accountHead' => $accountHead,
            'transactionMethods' => $transactionMethods,
            'searchForm' => $data,
            'overview' => $overview,
        ));
    }

}
