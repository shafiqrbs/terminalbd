<?php

namespace Appstore\Bundle\CustomerBundle\Controller;

use Appstore\Bundle\EcommerceBundle\Entity\Order;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerController extends Controller
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

    public function indexAction()
    {
        $user = $this->getUser();

        //$em = $this->get('doctrine.orm.entity_manager');
        $em = $this->getDoctrine()->getManager();

        return $this->render('CustomerBundle:Customer:dashboard.html.twig', array(
            'user' => $user,
        ));
    }

    public function orderAction()
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('EcommerceBundle:Order')->findBy(array('createdBy' => $user), array('updated' => 'desc'));
        $pagination = $this->paginate($entities);
        return $this->render('CustomerBundle:Customer:order.html.twig', array(
            'entities' => $pagination,
        ));

    }

    public function paymentAction($id)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $order = $em->getRepository('EcommerceBundle:Order')->findOneBy(array('createdBy' => $user,'id'=>$id));
        $banks = $this->getDoctrine()->getRepository('EcommerceBundle:BankAccount')->findBy(array('status'=>1),array('name'=>'asc'));
        $bkashs = $this->getDoctrine()->getRepository('EcommerceBundle:BkashAccount')->findBy(array('status'=>1),array('name'=>'asc'));
        $paymentTypes = $this->getDoctrine()->getRepository('SettingToolBundle:PaymentType')->findBy(array('status'=>1),array('name'=>'asc'));
        return $this->render('CustomerBundle:Customer:payment.html.twig', array(
            'entity'      => $order,
            'banks'      => $banks,
            'bkashs'      => $bkashs,
            'paymentTypes'      => $paymentTypes,
        ));

    }

    public function processAction(Order $order , $process)
    {
        $data = $_REQUEST;
        $em = $this->getDoctrine()->getManager();
        $order->setProcess($process);
        if(!empty( $_GET['delivery'])){
            $address = $data['address'];
            $order->setAddress($address);
            $delivery = $_GET['delivery'];
            $order->setDelivery($delivery);
        }
        $em->persist($order);
        $em->flush();
        return new Response('success');
    }

    public function payAction(Request $request ,Order $order)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        if(!empty( $data['paymentType'])){
            $paymentType =     $paymentTypes = $this->getDoctrine()->getRepository('SettingToolBundle:PaymentType')->findOneBy(array('slug'=>$data['paymentType']));
            $order->setPaymentType($paymentType);
            if($data['paymentType'] == 'cash-on-hand'){}
            if($data['paymentType'] == 'cash-on-delivery'){}
            if($data['paymentType'] == 'cash-on-bank'){
                $bank =     $paymentTypes = $this->getDoctrine()->getRepository('EcommerceBundle:BankAccount')->find($data['bank']);
                $order->setBankAccount($bank);
            }
            if($data['paymentType'] == 'cash-on-bkash'){
                $bkash =     $paymentTypes = $this->getDoctrine()->getRepository('EcommerceBundle:BkashAccount')->find($data['bkash']);
                $order->setBankAccount($bkash);
            }
            if($data['paymentType'] == 'cash-on-mobile-bank'){}
        }

            $order->setProcess('wfc');
            $date = strtotime($data['deliveryDate']);
            $deliveryDate = date('d-m-Y H:i:s',$date);
            $order->setDeliveryDate(new \DateTime(($deliveryDate)));
        $em->persist($order);
        $em->flush();
        return new Response('success');
    }

    public function invoiceAction(Order $order)
    {
        return $this->render('EcommerceBundle:PreOrder:invoice.html.twig', array(
            'entity' => $order
        ));
    }


}
