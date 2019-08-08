<?php

namespace Appstore\Bundle\AccountingBundle\Controller;

use Appstore\Bundle\AccountingBundle\Entity\AccountProfit;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;


/**
 * AccountSalesAdjustment controller.
 *
 */
class AccountProfitController extends Controller
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
     * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_JOURNAL,ROLE_DOMAIN")
     */

    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $entities = $em->getRepository('AccountingBundle:AccountProfit')->findWithSearch( $this->getUser()->getGlobalOption(),$data);
        $pagination = $this->paginate($entities);
        return $this->render('AccountingBundle:AccountProfit:index.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
        ));
    }


    /**
     * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_JOURNAL,ROLE_DOMAIN")
     */

    public function newAction()
    {

        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportMonthlyProfitLoss($this->getUser());
        var_dump($overview);
        $sales = round($overview['sales'] + $overview['salesAdjustment']['sales']);
        $purchase = round($overview['purchase'] + $overview['salesAdjustment']['purchase']);
        $expenditure = round ($overview['expenditure']);
        $revenue = round ($overview['operatingRevenue']);

        $profit = ($sales + $revenue) - ($purchase + $expenditure);

        $option = $this->getUser()->getGlobalOption();
        $em = $this->getDoctrine()->getManager();
        $entity = new AccountProfit();
        $entity->setGlobalOption($option);
        $entity->setSales($sales);
        $entity->setPurchase($purchase);
        $entity->setExpenditure($expenditure);
        $entity->setRevenue($revenue);
        if($profit > 0 ){
            $entity->setProfit($profit);
        }else{
            $entity->setLoss(abs($profit));
        }
        $em->persist($entity);
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',"Data has been added successfully"
        );
        return $this->redirect($this->generateUrl('account_profit'));
    }




}
