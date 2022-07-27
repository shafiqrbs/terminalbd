<?php

namespace Appstore\Bundle\InventoryBundle\Controller;

use Appstore\Bundle\DomainUserBundle\Entity\Branches;
use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Appstore\Bundle\InventoryBundle\Form\SalesGeneralType;
use Appstore\Bundle\InventoryBundle\Form\SalesItemType;
use Appstore\Bundle\InventoryBundle\Form\SalesOnlineType;
use Appstore\Bundle\InventoryBundle\Service\PosItemManager;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use Frontend\FrontentBundle\Service\MobileDetect;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Appstore\Bundle\InventoryBundle\Entity\SalesItem;
use Mike42\Escpos\Printer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Appstore\Bundle\InventoryBundle\Entity\Sales;
use Symfony\Component\HttpFoundation\Response;
use Hackzilla\BarcodeBundle\Utility\Barcode;
/**
 * Sales controller.
 *
 */
class SalesOnlineController extends Controller
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

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();
        $inventoryConfig = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $data = $_REQUEST;
        $entities = $em->getRepository('InventoryBundle:Sales')->salesLists( $this->getUser() , $mode='general-sales', $data);
        $pagination = $this->paginate($entities);
        $transactionMethods = $em->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status' => 1), array('name' => 'ASC'));
        return $this->render('InventoryBundle:SalesOnline:index.html.twig', array(
            'entities' => $pagination,
            'config' => $inventoryConfig,
            'transactionMethods' => $transactionMethods,
            'searchForm' => $data,
        ));
    }


    public function customerAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('DomainUserBundle:Customer')->findWithSearch($globalOption,$data);
        $pagination = $this->paginate($entities);
        $config = $globalOption->getInventoryConfig();
        return $this->render('InventoryBundle:SalesOnline:index.html.twig', array(
            'config' => $config,
            'entities' => $pagination,
            'inventory' => $globalOption->getInventoryConfig(),
            'searchForm' => $data,
        ));
    }


    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function newAction()
    {

        $em = $this->getDoctrine()->getManager();
        $entity = new Sales();
        $globalOption = $this->getUser()->getGlobalOption();
        $customer = $em->getRepository('DomainUserBundle:Customer')->defaultCustomer($globalOption);
        $entity->setCustomer($customer);
        $transactionMethod = $em->getRepository('SettingToolBundle:TransactionMethod')->find(1);
        $entity->setTransactionMethod($transactionMethod);
        $entity->setSalesMode('general-sales');
        $entity->setPaymentStatus('Pending');
        $entity->setInventoryConfig($globalOption->getInventoryConfig());
        $entity->setSalesBy($this->getUser());
        if(!empty($this->getUser()->getProfile()->getBranches())){
            $entity->setBranches($this->getUser()->getProfile()->getBranches());
        }
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('inventory_salesonline_edit', array('code' => $entity->getInvoice())));

    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function editAction($code)
    {
        $em = $this->getDoctrine()->getManager();
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entity = $em->getRepository('InventoryBundle:Sales')->findOneBy(array('inventoryConfig' => $inventory, 'invoice' => $code));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sales entity.');
        }
        $todaySales = $em->getRepository('InventoryBundle:Sales')->todaySales($this->getUser(),$mode = 'general-sales');
        $todaySalesOverview = $em->getRepository('InventoryBundle:Sales')->todaySalesOverview($this->getUser(),$mode = 'general-sales');
        if(!in_array($entity->getProcess(),array('In-progress','Created'))) {
            return $this->redirect($this->generateUrl('inventory_salesonline_show', array('id' => $entity->getId())));
        }
        $editForm = $this->createEditForm($entity);
        if($inventory->getSalesMode() == 'stock'){
            $theme = 'stock-item';
            $createItemForm = $this->createItemForm(New SalesItem(),$entity);
            return $this->render('InventoryBundle:SalesOnline:'.$theme.'.html.twig', array(
                'entity' => $entity,
                'todaySales' => $todaySales,
                'todaySalesOverview' => $todaySalesOverview,
                'itemForm' => $createItemForm->createView(),
                'form' => $editForm->createView(),
            ));
        }else{
            $editForm = $this->createEditForm($entity);
            $theme = 'purchase-item';
            return $this->render('InventoryBundle:SalesOnline:'.$theme.'.html.twig', array(
                'entity' => $entity,
                'todaySales' => $todaySales,
                'todaySalesOverview' => $todaySalesOverview,
                'form' => $editForm->createView(),
            ));
        }
    }

    private function createItemForm(SalesItem $item , Sales $entity)
    {
        $form = $this->createForm(new SalesItemType($entity->getInventoryConfig()), $item, array(
            'action' => $this->generateUrl('inventory_salesmanual_insert_item', array('sales' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Creates a form to edit a Sales entity.wq
     *
     * @param Sales $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Sales $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $form = $this->createForm(new SalesOnlineType($globalOption,$location), $entity, array(
            'action' => $this->generateUrl('inventory_salesonline_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'id' => 'salesForm',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function salesItemAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities = $em->getRepository('InventoryBundle:SalesItem')->salesItems($inventory, $data);
        $pagination = $this->paginate($entities);
        return $this->render('InventoryBundle:Sales:salesItem.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
        ));
    }


}
