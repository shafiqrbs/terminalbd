<?php

namespace Appstore\Bundle\AccountingBundle\Controller;

use Appstore\Bundle\AccountingBundle\Entity\AccountHead;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\AccountingBundle\Entity\Transaction;
use Appstore\Bundle\AccountingBundle\Form\TransactionType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Transaction controller.
 *
 */
class TransactionController extends Controller
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
     * Lists all Transaction entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('AccountingBundle:Transaction')->getGroupByAccountHead($globalOption);
        $pagination = $this->paginate($entities);
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->transactionOverview($globalOption,$data);
        $accountHead = $em->getRepository('AccountingBundle:AccountHead')->findBy(array('status'=>1),array('name'=>'asc'));
        return $this->render('AccountingBundle:Transaction:index.html.twig', array(
            'entities' => $pagination,
            'accountHead' => $accountHead,
            'overview' => $overview,
        ));
    }
   
    /**
     * Finds and displays a Transaction entity.
     *
     */
    public function showAction(AccountHead $entity )
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->specificAccountHead($globalOption,$entity->getId());
        $pagination = $this->paginate($entities);
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->transactionOverview($globalOption,$entity->getId());
        $accountHead = $em->getRepository('AccountingBundle:AccountHead')->findBy(array('status'=> 1),array('name'=>'asc'));
        return $this->render('AccountingBundle:Transaction:show.html.twig', array(
            'entity' => $entity,
            'entities' => $pagination,
            'accountHead' => $accountHead,
            'overview' => $overview,
        ));

    }

    /**
     * Lists all Transaction entities.
     *
     */
    public function transactionCashOverviewAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;

        $globalOption = $this->getUser()->getGlobalOption();
        $overviews = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->transactionCashOverview($globalOption,$data);
        $transactionBankCashOverviews = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->transactionBankCashOverview($globalOption,$data);
        $transactionMobileBankCashOverviews = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->transactionBkashCashOverview($globalOption,$data);
        $transactionAccountHeadCashOverviews = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->transactionAccountHeadCashOverview($globalOption,$data);
        return $this->render('AccountingBundle:Transaction:cashoverview.html.twig', array(
            'overviews'                             => $overviews,
            'transactionBankCashOverviews'          => $transactionBankCashOverviews,
            'transactionBkashCashOverviews'         => $transactionMobileBankCashOverviews,
            'transactionAccountHeadCashOverviews'   => $transactionAccountHeadCashOverviews,
        ));

    }

   /**
     * Lists all Transaction entities.
     *
     */
    public function cashAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;

        $user = $this->getUser();
        $transactionMethods = array(1,4);
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->findWithSearch($user,$transactionMethods,$data);
        $pagination = $this->paginate($entities);

        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->accountCashOverview($user,$transactionMethods,$data);
        $processHeads = $this->getDoctrine()->getRepository('AccountingBundle:ProcessHead')->findBy(array('status'=>1));
        return $this->render('AccountingBundle:Transaction:cash.html.twig', array(
            'entities' => $pagination,
            'overview' => $overview,
            'processHeads' => $processHeads,
            'searchForm' => $data,
        ));

    }

    /**
     * Lists all Transaction entities.
     *
     */
    public function bankAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;

        $user = $this->getUser();
        $globalOption = $user->getGlobalOption();
        $transactionMethods = array(2);
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->findWithSearch($user,$transactionMethods,$data);
        $pagination = $this->paginate($entities);
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->accountCashOverview($user,2,$data);
        $processHeads = $this->getDoctrine()->getRepository('AccountingBundle:ProcessHead')->findBy(array('status'=>1));
        $accountBanks = $this->getDoctrine()->getRepository('AccountingBundle:AccountBank')->findBy(array('globalOption'=>$globalOption,'status'=>1));

        return $this->render('AccountingBundle:Transaction:bank.html.twig', array(
            'entities' => $pagination,
            'overview' => $overview,
            'processHeads' => $processHeads,
            'accountBanks' => $accountBanks,
            'searchForm' => $data,
        ));

    }

    /**
     * Lists all Transaction entities.
     *
     */
    public function mobileBankAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $globalOption = $user->getGlobalOption();
        $transactionMethods = array(3);
        $entities = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->findWithSearch($user,$transactionMethods,$data);
        $pagination = $this->paginate($entities);
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->accountCashOverview($user,3,$data);
        $processHeads = $this->getDoctrine()->getRepository('AccountingBundle:ProcessHead')->findBy(array('status'=>1));
        $accountMobileBanks = $this->getDoctrine()->getRepository('AccountingBundle:AccountMobileBank')->findBy(array('globalOption' => $globalOption,'status'=>1));
        return $this->render('AccountingBundle:Transaction:mobilebank.html.twig', array(
            'entities' => $pagination,
            'overview' => $overview,
            'processHeads' => $processHeads,
            'accountMobileBanks' => $accountMobileBanks,
            'searchForm' => $data,
        ));

    }





}
