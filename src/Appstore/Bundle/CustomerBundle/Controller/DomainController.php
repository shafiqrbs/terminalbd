<?php

namespace Appstore\Bundle\CustomerBundle\Controller;

use Appstore\Bundle\EcommerceBundle\Entity\Order;
use Appstore\Bundle\EcommerceBundle\Form\OrderType;
use Frontend\FrontentBundle\Service\Cart;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Setting\Bundle\ToolBundle\Form\DomainSearchType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DomainController extends Controller
{


    public function paginate($entities)
    {

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            25  /*limit per page*/
        );
        return $pagination;
    }

    public function indexAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = new GlobalOption();
        $form   = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $entities = $em->getRepository('SettingToolBundle:GlobalOption')->getActiveDomainList($form);
        $pagination = $this->paginate($entities);
        $shop = $request->request->get('shop');
        if(!empty($globalOption)){
            $globalOption = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('slug' => $shop));
        }else{
            $globalOption ='';
        }


        return $this->render('CustomerBundle:Domain:index.html.twig', array(
            'entities' => $pagination,
            'user' => $this->getUser(),
            'form'   => $form->createView(),
            'entity' => $entity,
            'globalOption' => $globalOption,

        ));


    }

    public function domainBookmarkAction($generated)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('uniqueCode'=>$generated));
        $entities = $em->getRepository('EcommerceBundle:Order')->findBy(array('createdBy' => $user), array('updated' => 'desc'));
        $pagination = $this->paginate($entities);
        return $this->render('CustomerBundle:Customer:domainBookmark.html.twig', array(
            'entities' => $pagination,
            'globalOption' => $globalOption,
        ));

    }

    /**
     * Creates a form to create a User entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */

    private function createCreateForm(GlobalOption $entity)
    {
        $em = $this->getDoctrine()->getRepository('SettingToolBundle:Syndicate');
        $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $form = $this->createForm(new DomainSearchType($em,$location), $entity, array(
            'action' => $this->generateUrl('customer_domain'),
            'method' => 'GET',
            'attr' => array(
                'id' => '',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }


    public function showAction(Order $entity)
    {

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Expenditure entity.');
        }
        return $this->render('CustomerBundle:Domain:show.html.twig', array(
            'entity' => $entity,
        ));

    }

    public function paymentAction($id)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('EcommerceBundle:Order')->findOneBy(array('createdBy' => $user,'id' => $id));
        $editForm = $this->createEditForm($entity);
        return $this->render('CustomerBundle:Customer:payment.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));

    }

    /**
     * Creates a form to edit a Order entity.
     *
     * @param Order $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Order $entity)
    {
        $globalOption = $entity->getGlobalOption();
        $form = $this->createForm(new OrderType($globalOption), $entity, array(
            'action' => $this->generateUrl('order_process', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }


    public function processAction(Request $request , Order $order)
    {

            $data = $request->request->all();

            $editForm = $this->createEditForm($order);
            $editForm->handleRequest($request);
            if ($editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->getRepository('EcommerceBundle:OrderItem')->itemOrderUpdate($order,$data);
            $totalAmount = $em->getRepository('EcommerceBundle:OrderItem')->totalItemAmount($order);
            $order->setTotalAmount($totalAmount);
            $vat = $em->getRepository('EcommerceBundle:Order')->getCulculationVat($order->getGlobalOption(),$totalAmount);
            $grandTotal = $totalAmount + $order->getShippingCharge() + $vat;
            $order->setVat($vat);
            $order->setGrandTotalAmount($grandTotal);
            if($order->getPaidAmount() > $grandTotal ){
                $order->setReturnAmount(($order->getPaidAmount() + $order->getDiscountAmount()) - $grandTotal);
                $order->setDueAmount(0);
            }elseif($totalAmount < $grandTotal ){
                $order->setReturnAmount(0);
                $order->setDueAmount($grandTotal - ($totalAmount+$order->getDiscountAmount()));
            }
            $order->setProcess('wfc');
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success', "Order has been process successfully"
            );
            }
            return $this->redirect($this->generateUrl('domain_customer_payment',array('id' => $order->getId())));

    }

    public function payAction(Request $request ,Order $order)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        if(!empty( $data['transactionMethod'])){
            $transactionMethod =     $paymentTypes = $this->getDoctrine()->getRepository('SettingToolBundle:TransactionMethod')->find($data['transactionMethod']);
            $order->setTransactionMethod($transactionMethod);
            if($transactionMethod  == 2 ){
                $bank = $this->getDoctrine()->getRepository('AccountingBundle:AccountBank')->find($data['accountBank']);
                $order->setAccountBank($bank);
            }
            if($transactionMethod == 3 ){
                $accountMobileBank = $this->getDoctrine()->getRepository('AccountingBundle:AccountMobileBank')->find($data['accountMobileBank']);
                $order->setAccountMobileBank($accountMobileBank);
            }
            $order->setProcess('wfc');
            $date = strtotime($data['deliveryDate']);
            $deliveryDate = date('d-m-Y H:i:s',$date);
            $order->setDeliveryDate(new \DateTime(($deliveryDate)));
            $em->persist($order);
            $em->flush();
            return new Response('success');
        }
    }

    public function deleteAction(Order $entity)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Expenditure entity.');
        }
        $em->remove($entity);
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'error',"Data has been deleted successfully"
        );
        return new Response('success');
    }

    public function itemDeleteAction(Order $entity)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Expenditure entity.');
        }
        $em->remove($entity);
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'error',"Data has been deleted successfully"
        );
        return new Response('success');
    }

    public function pdfAction($invoice)
    {

        $order = $this->getDoctrine()->getRepository('EcommerceBundle:Order')->findOneBy(array('createdBy'=>$this->getUser(),'invoice'=>$invoice));

        $html = $this->renderView(
            'CustomerBundle:Customer:invoice.html.twig', array(
                'entity' => $order,
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

    public function printAction($invoice)
    {

        $order = $this->getDoctrine()->getRepository('EcommerceBundle:Order')->findOneBy(array('createdBy'=>$this->getUser(),'invoice'=>$invoice));
        return $this->render('CustomerBundle:Customer:invoice.html.twig', array(
            'entity' => $order,
            'print' => '<script>window.print();</script>'
        ));

    }



}
