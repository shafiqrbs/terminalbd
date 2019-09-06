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

        $option = $this->getUser()->getGlobalOption();
        $em = $this->getDoctrine()->getManager();
        $search = $_REQUEST;
        $datetime = new \DateTime("now");
        $today = $datetime->format('d-m-Y');
        if(empty($search)){
            $startDate = date('Y-m-01 00:00:00', strtotime("-1 month -1 day", strtotime($today)));
            $endDate = date('Y-m-t 23:59:59', strtotime("-1 month -1 day", strtotime($today)));
            $data['startDate'] = $startDate;
            $data['endDate'] = $endDate;
        }else{
            $data['startDate'] = date('Y-m-d 00:00:00',strtotime($search['startDate']));
            $data['endDate'] = date('Y-m-t 23:59:59',strtotime($search['endDate']));
        }

        $month = date('m', strtotime( $data['endDate']));
        $year = date('Y', strtotime($data['endDate']));
        $entity = $this->getDoctrine()->getRepository('AccountingBundle:AccountProfit')->findOneBy(array('globalOption' => $option,'month' => $month,'year' => $year));
     //   $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->getCapitalInvestment($option,$entity);
        if(!$entity){
            $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportMonthlyProfitLoss($this->getUser(),$data);
            $sales = round($overview['sales'] + $overview['salesAdjustment']['sales']);
            $purchase = round($overview['purchase'] + $overview['salesAdjustment']['purchase']);
            $expenditure = round ($overview['expenditure']);
            $revenue = round ($overview['operatingRevenue']);
            $profit = ($sales + $revenue) - ($purchase + $expenditure);
            $entity = new AccountProfit();
            $entity->setGlobalOption($option);
            $entity->setSales($sales);
            $entity->setPurchase($purchase);
            $entity->setExpenditure($expenditure);
            $entity->setRevenue($revenue);
            $generate = new \DateTime($data['endDate']);
            $entity->setGenerateMonth($generate);
            $entity->setMonth($month);
            $entity->setYear($year);
            if($profit > 0 ){
                $entity->setProfit($profit);
            }else{
                $entity->setLoss(abs($profit));
            }
            $em->persist($entity);
            $em->flush();
            if($entity->getLoss() > 0 or $entity->getProfit() > 0){
                $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->getCapitalInvestment($option,$entity);
            }
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
        }else{
            $this->get('session')->getFlashBag()->add(
                'notice',"Already generated this {$month},{$year}"
            );
        }
        return $this->redirect($this->generateUrl('account_profit'));
    }

}
