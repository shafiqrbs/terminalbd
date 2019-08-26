<?php

namespace Appstore\Bundle\TallyBundle\Controller;

use Appstore\Bundle\DomainUserBundle\Entity\Branches;
use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Appstore\Bundle\TallyBundle\Form\IssueType;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use Frontend\FrontentBundle\Service\MobileDetect;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Appstore\Bundle\TallyBundle\Entity\SalesItem;
use Mike42\Escpos\Printer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Appstore\Bundle\TallyBundle\Entity\Sales;
use Symfony\Component\HttpFoundation\Response;
use Hackzilla\BarcodeBundle\Utility\Barcode;
/**
 * Sales controller.
 *
 */
class IssueController extends Controller
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
     * @Secure(roles="ROLE_TALLY_ISSUE,ROLE_DOMAIN")
     */

    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getTallyConfig();
        $data = $_REQUEST;
        $entities = $em->getRepository('TallyBundle:Sales')->salesLists( $this->getUser() , $mode = 'issue', $data);
        $pagination = $this->paginate($entities);
        $transactionMethods = $em->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status' => 1), array('name' => 'ASC'));
        return $this->render('TallyBundle:Issue:index.html.twig', array(
            'entities' => $pagination,
            'config' => $config,
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
        return $this->render('TallyBundle:Issue:customer.html.twig', array(
            'entities' => $pagination,
            'inventory' => $globalOption->getConfig(),
            'searchForm' => $data,
        ));
    }


    /**
     * @Secure(roles="ROLE_TALLY_ISSUE,ROLE_DOMAIN")
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
        $entity->setSalesMode('issue');
        $entity->setPaymentStatus('Pending');
        $entity->setConfig($globalOption->getTallyConfig());
        $entity->setSalesBy($this->getUser());
        if(!empty($this->getUser()->getProfile()->getBranches())){
            $entity->setBranches($this->getUser()->getProfile()->getBranches());
        }
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('tally_itemissue_edit', array('code' => $entity->getInvoice())));

    }

    /**
     * @Secure(roles="ROLE_TALLY_ISSUE,ROLE_DOMAIN")
     */

    public function editAction($code)
    {
        $em = $this->getDoctrine()->getManager();
        $inventory = $this->getUser()->getGlobalOption()->getTallyConfig();
        $entity = $em->getRepository('TallyBundle:Sales')->findOneBy(array('config' => $inventory, 'invoice' => $code));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sales entity.');
        }

        $editForm = $this->createEditForm($entity);
        $todaySalesOverview = $em->getRepository('TallyBundle:Sales')->todaySalesOverview($this->getUser(),$mode = 'general-sales');
        if(!in_array($entity->getProcess(),array('In-progress','Created'))) {
            return $this->redirect($this->generateUrl('tally_itemissue_show', array('id' => $entity->getId())));
        }
        return $this->render('TallyBundle:Issue:sales.html.twig', array(
            'entity' => $entity,
            'todaySalesOverview' => $todaySalesOverview,
            'form' => $editForm->createView(),
        ));
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
        $form = $this->createForm(new IssueType($globalOption), $entity, array(
            'action' => $this->generateUrl('tally_itemissue_update', array('id' => $entity->getId())),
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
     * Finds and displays a Sales entity.
     *
     */
    public function showAction(Sales $entity)
    {
        $inventory = $this->getUser()->getGlobalOption()->getTallyConfig();

        if ($inventory->getId() == $entity->getConfig()->getId()) {
            return $this->render('TallyBundle:Issue:show.html.twig', array(
                'entity' => $entity,
                'config' => $inventory,
            ));
        } else {
            return $this->redirect($this->generateUrl('inventory_salesonline'));
        }

    }

    /**
     * @Secure(roles="ROLE_TALLY_ISSUE,ROLE_DOMAIN")
     */

    public function updateAction(Request $request, Sales $entity)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sales entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        $data = $request->request->all();
        if($editForm->isValid()) {
            $entity->setPayment($entity->getTotal() - $entity->getDue());
            if ($entity->getNetTotal() <= $entity->getPayment() ) {
                $entity->setPaymentStatus('Paid');
            } else if ($entity->getNetTotal() - $entity->getDue()) {
                $entity->setPaymentStatus('Due');
            }
            $entity->setProcess('Done');
            if (empty($data['sales']['salesBy'])) {
                $entity->setSalesBy($this->getUser());
            }
            if ($entity->getTransactionMethod()->getId() != 4) {
                $entity->setApprovedBy($this->getUser());
            }
            $amountInWords = $this->get('settong.toolManageRepo')->intToWords($entity->getNetTotal());
            $entity->setPaymentInWord($amountInWords);
            $em->flush();

            $this->getDoctrine()->getRepository('TallyBundle:StockItem')->getSalesItemUpdate($entity);
            $this->getDoctrine()->getRepository('TallyBundle:Item')->getSalesUpdateQnt($entity);
            $accountSales = $em->getRepository('AccountingBundle:AccountSales')->insertAccountSalesTally($entity);
            $em->getRepository('AccountingBundle:Transaction')->tallySalesTransaction($entity, $accountSales);
            return $this->redirect($this->generateUrl('tally_itemissue_new'));
        }
        $inventory = $this->getUser()->getGlobalOption()->getConfig();
        $todaySales = $em->getRepository('TallyBundle:Sales')->todaySales($inventory,$mode='issue');
        $todaySalesOverview = $em->getRepository('TallyBundle:Sales')->todaySalesOverview($inventory , $mode='issue');
        return $this->render('TallyBundle:Issue:sales.html.twig', array(
            'entity' => $entity,
            'todaySales' => $todaySales,
            'todaySalesOverview' => $todaySalesOverview,
            'form' => $editForm->createView(),
        ));

    }


    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_APPROVE")
     */

    public function approveAction(Sales $entity)
    {
        if (!empty($entity) and $entity->getProcess() == "Done") {
            $em = $this->getDoctrine()->getManager();
            $entity->setPaymentStatus('Paid');
            $entity->setPayment($entity->getPayment() + $entity->getDue());
            $entity->setDue($entity->getTotal() - $entity->getPayment());
            $em->flush();
            $this->getDoctrine()->getRepository('TallyBundle:StockItem')->getSalesItemUpdate($entity);
            $this->getDoctrine()->getRepository('TallyBundle:Item')->getSalesUpdateQnt($entity);
            $accountSales = $em->getRepository('AccountingBundle:AccountSales')->insertAccountSalesTally($entity);
            $em->getRepository('AccountingBundle:Transaction')->tallySalesTransaction($entity, $accountSales);
            return new Response('success');

        } else {

            return new Response('failed');
        }

    }


    /**
     * @Secure(roles="ROLE_TALLY_ISSUE,ROLE_DOMAIN")
     */

    public function deleteAction(Sales $sales)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$sales) {
            throw $this->createNotFoundException('Unable to find Sales entity.');
        }
        $em->remove($sales);
        $em->flush();
        return new Response(json_encode(array('success' => 'success')));
    }



    public function approvedOrder(Sales $entity)
    {
        if (!empty($entity)) {

            $em = $this->getDoctrine()->getManager();
            $entity->setPaymentStatus('Paid');
            $entity->setPayment($entity->getPayment() + $entity->getDue());
            $entity->setDue($entity->getTotal() - $entity->getPayment());
            $em->flush();
            $this->getDoctrine()->getRepository('TallyBundle:StockItem')->getSalesItemUpdate($entity);
            $this->getDoctrine()->getRepository('TallyBundle:Item')->getSalesUpdateQnt($entity);
            $accountSales = $em->getRepository('AccountingBundle:AccountSales')->insertAccountSalesTally($entity);
            $em->getRepository('AccountingBundle:Transaction')->tallySalesTransaction($entity, $accountSales);
            return new Response('success');

        } else {

            return new Response('failed');
        }

    }



    public function salesSelectAction()
    {
        $items  = array();
        $items[]= array('value' => 'Done','text'=>'Done');
        $items[]= array('value' => 'In-progress','text'=>'In-progress');
        $items[]= array('value' => 'Courier','text'=>'Courier');
        $items[]= array('value' => 'Returned','text'=>'Returned');
        return new JsonResponse($items);
    }

    public function invoicePrintAction(Sales $entity)
    {
        $barcode = $this->getBarcode($entity->getInvoice());
        return $this->render('TallyBundle:SalesGeneral:invoice.html.twig', array(
            'entity'      => $entity,
            'barcode'     => $barcode,
        ));
    }

    public function getBarcode($invoice)
    {
        $barcode = new BarcodeGenerator();
        $barcode->setText($invoice);
        $barcode->setType(BarcodeGenerator::Code128);
        $barcode->setScale(1);
        $barcode->setThickness(34);
        $barcode->setFontSize(8);
        $code = $barcode->generate();
        $data = '';
        $data .= '<img src="data:image/png;base64,' . $code . '" />';
        return $data;
    }

    public function reverseAction($invoice)
    {
        $inventory = $this->getUser()->getGlobalOption()->getTallyConfig();
        $entity = $this->getDoctrine()->getRepository('TallyBundle:Sales')->findOneBy(array('config' => $inventory,'invoice' => $invoice));
        $em = $this->getDoctrine()->getManager();
        $em->getRepository('TallyBundle:StockItem')->saleaItemStockReverse($entity);
        $em->getRepository('TallyBundle:Item')->getSalesItemReverse($entity);
        $em->getRepository('TallyBundle:GoodsItem')->ecommerceItemReverse($entity);
        $em->getRepository('AccountingBundle:AccountSales')->accountSalesReverse($entity);
        $em = $this->getDoctrine()->getManager();
        $entity->setRevised(true);
        $entity->setProcess('In-progress');
        $entity->setRevised(true);
        $entity->setTotal($entity->getSubTotal());
        $entity->setPaymentStatus('Due');
        $entity->setDiscount(null);
        $entity->setDue($entity->getSubTotal());
        $entity->setPaymentInWord(null);
        $entity->setPayment(null);
        $entity->setPaymentStatus('Pending');
        $em->flush();
        $template = $this->get('twig')->render('TallyBundle:Reverse:salesReverse.html.twig', array(
            'entity' => $entity,
            'config' => $inventory,
        ));
        $em->getRepository('TallyBundle:Reverse')->insertSales($entity, $template);
        return $this->redirect($this->generateUrl('tally_itemissue_edit', array('code' => $entity->getInvoice())));
    }


}
