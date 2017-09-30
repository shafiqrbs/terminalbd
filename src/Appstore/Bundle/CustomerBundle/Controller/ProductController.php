<?php

namespace Appstore\Bundle\CustomerBundle\Controller;

use Appstore\Bundle\EcommerceBundle\Entity\Order;
use Appstore\Bundle\EcommerceBundle\Form\OrderType;
use Appstore\Bundle\InventoryBundle\Entity\GoodsItem;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseVendorItem;
use Frontend\FrontentBundle\Service\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
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
    public function allProductAction(Request $request)
    {
        $cart = new Cart($request->getSession());
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $entities = $em->getRepository('InventoryBundle:PurchaseVendorItem')->findAllProductWithSearch($data);
        $pagination = $this->paginate($entities);
        return $this->render('CustomerBundle:Product:allProduct.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
            'cart' => $cart,
        ));

    }

    public function indexAction(Request $request, $shop)
    {
        $cart = new Cart($request->getSession());
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('slug' => $shop));
        $inventory = $globalOption->getInventoryConfig();
        $entities = $em->getRepository('InventoryBundle:PurchaseVendorItem')->findGoodsWithSearch($inventory,$data);
        $pagination = $this->paginate($entities);
        return $this->render('CustomerBundle:Product:index.html.twig', array(
            'globalOption' => $globalOption,
            'entities' => $pagination,
            'searchForm' => $data,
            'cart' => $cart,
        ));

    }

    public function productAddCartAction(Request $request , GoodsItem $subitem)
    {

        $cart = new Cart($request->getSession());

        $quantity = $request->request->get('quantity');
        $color = $request->request->get('color');
        $productImg = '';

        $color = !empty($color) ? $color : 0;
        if($color > 0){
            $colorName = $this->getDoctrine()->getRepository('InventoryBundle:ItemColor')->find($color)->getName();
        }else{
            $colorName ='';
        }

        $masterItem = !empty($subitem->getPurchaseVendorItem()->getMasterItem()) ? $subitem->getPurchaseVendorItem()->getMasterItem()->getName().'-':'';
        $product = $subitem->getPurchaseVendorItem();
        $data = array(

            'id' => $subitem->getId(),
            'name'=> $masterItem.'-'.$product->getName(),
            'brand'=> !empty($product->getBrand()) ? $product->getBrand()->getName():'',
            'category'=> !empty($product->getMasterItem()->getCategory()) ? $product->getMasterItem()->getCategory()->getName():'',
            'size'=>!empty($subitem->getSize()) ? $subitem->getSize()->getName():0 ,
            'color'=> $colorName ,
            'colorId'=> $color,
            'price'=> $subitem->getSalesPrice(),
            'quantity' => $quantity,
            'productImg' => $productImg
        );
        
        $cart->insert($data);
        $cartTotal = $cart->total();
        $totalItems = $cart->total_items();
        $cartResult = $cartTotal.'('.$totalItems.')';
        return new Response($cartTotal);

    }

    public function cartAction(Request $request)
    {

        $cart = new Cart($request->getSession());
        return $this->render('CustomerBundle:Order:cart.html.twig', array(
            'cart'             => $cart,
        ));

    }

    public function cartToOrderAction($shop , Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $cart = new Cart($request->getSession());
        $user = $this->getUser();
        $order = $em->getRepository('EcommerceBundle:Order')->insertNewCustomerOrder($user,$shop,$cart);
        $cart->destroy();
        return $this->redirect($this->generateUrl('order_payment',array('id' => $order->getId())));

    }

    public function showAction(Request $request ,$shop , PurchaseVendorItem $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('slug' => $shop));
        $subItem = $em->getRepository('InventoryBundle:GoodsItem')->findOneBy(array('purchaseVendorItem' => $entity->getId(),'masterItem'=>1));
        $cart = new Cart($request->getSession());
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Expenditure entity.');
        }
        return $this->render('CustomerBundle:Product:show.html.twig', array(
            'product'           => $entity,
            'subitem'           => $subItem,
            'cart'              => $cart,
            'globalOption'      => $globalOption,
        ));

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




}
