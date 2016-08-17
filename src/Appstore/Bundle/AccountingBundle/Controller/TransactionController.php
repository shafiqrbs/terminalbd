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
        $accountHead = $em->getRepository('AccountingBundle:AccountHead')->findBy(array('status'=>1),array('name'=>'asc'));
        return $this->render('AccountingBundle:Transaction:show.html.twig', array(
            'entity' => $entity,
            'entities' => $pagination,
            'accountHead' => $accountHead,
            'overview' => $overview,
        ));

    }

   
   

}
