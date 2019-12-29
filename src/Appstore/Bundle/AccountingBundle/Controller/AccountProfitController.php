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
        $search = $_REQUEST;
        $em = $this->getDoctrine()->getManager();
        $option = $this->getUser()->getGlobalOption();
        $datetime = new \DateTime("now");
        $today = $datetime->format('d-m-Y');
        if(empty($search)){
            $date = date('Y-m-01', strtotime("-1 month -1 day", strtotime($today)));
            $month = date("m",strtotime($date));
            $year = date("Y",strtotime($date));
            $compare = new \DateTime($date);
            $data =  $compare->format("Y-m-t 22:59:59");
        }else{
            $month = $search['month'];
            $year =  $search['year'];
            $compare = new \DateTime("{$year}-{$month}-01");
            $data =  $compare->format("Y-m-t 22:59:59");
        }

        $entity = $this->getDoctrine()->getRepository('AccountingBundle:AccountProfit')->findOneBy(array('globalOption' => $option,'month' => $month ,'year' => $year));
       // $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->getCapitalInvestment($option,$entity);
        if(empty($entity)){
            $entity = $this->getDoctrine()->getRepository('AccountingBundle:AccountProfit')->insertAccountProfit($option,$month,$year,$data);
            $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountProfit')->reportMonthlyProfitLoss($entity,$data);
            $sales = round($overview['sales'] + $overview['salesAdjustment']);
            $purchase = round($overview['purchase'] + $overview['purchaseAdjustment']);
            $expenditure = round ($overview['expenditure']);
            $revenue = round ($overview['operatingRevenue']);
            $profit = ($sales + $revenue) - ($purchase + $expenditure);
            $entity->setSales($sales);
            $entity->setPurchase($purchase);
            $entity->setExpenditure($expenditure);
            $entity->setRevenue($revenue);
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

    /**
     * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_JOURNAL,ROLE_DOMAIN")
     */

    public function deleteAction(AccountProfit $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountJournal entity.');
        }
        $em->remove($entity);
        $em->flush();
        return new Response('success');
    }


}
