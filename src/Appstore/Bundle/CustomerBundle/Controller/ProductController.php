<?php

namespace Appstore\Bundle\CustomerBundle\Controller;

use Appstore\Bundle\EcommerceBundle\Entity\Item;
use Appstore\Bundle\EcommerceBundle\Entity\ItemSub;
use Appstore\Bundle\EcommerceBundle\Entity\Order;
use Appstore\Bundle\EcommerceBundle\Form\OrderType;
use Appstore\Bundle\InventoryBundle\Entity\GoodsItem;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseVendorItem;
use Appstore\Bundle\MedicineBundle\Entity\MedicineStock;
use Frontend\FrontentBundle\Service\Cart;
use Frontend\FrontentBundle\Service\MobileDetect;
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
        $detect = new MobileDetect();
        if( $detect->isMobile() or  $detect->isTablet() ) {
            $pagination->setTemplate('SettingToolBundle:Widget:mobile-pagination.html.twig');
        }else{
            $pagination->setTemplate('SettingToolBundle:Widget:pagination.html.twig');
        }
        return $pagination;
    }


    public function allProductAction(Request $request)
    {
        $cart = new Cart($request->getSession());
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $config = $this->getUser()->getglobalOption()->getEcommerceConfig();
        $entities = $em->getRepository('EcommerceBundle:Item')->findAllProductWithSearch($config,$data);
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
        $cartProducts = array();
        if($cart->contents()){
            foreach ($cart->contents() as $c){
                $cartProducts[$c['id']]  = $c;
            }
        }

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $em->getRepository('SettingToolBundle:GlobalOption')->findOneBy(array('slug' => $shop));
        $config = $globalOption->getEcommerceConfig();
        $domainType =  $globalOption->getDomainType();
        $entities = $em->getRepository('EcommerceBundle:Item')->findFrontendProductWithSearch($config,$data);
        $pagination = $this->paginate($entities);
        if( $globalOption->getDomainType() == 'medicine' ) {
            $detect = new MobileDetect();
            if( $detect->isMobile() or  $detect->isTablet() ) {
                $theme = 'medicine/mobile';
            }else{
                $theme = 'medicine';
            }
        }else{
            $theme = 'generic';
        }
        return $this->render("CustomerBundle:Product/{$theme}:index.html.twig", array(
            'globalOption' => $globalOption,
            'entities' => $pagination,
            'searchForm' => $data,
            'cart' => $cart,
            'cartProducts' => $cartProducts,
        ));

    }



    public function productAddCartSubitemAction(Request $request , ItemSub $subitem)
    {

        $cart = new Cart($request->getSession());

        $product = $subitem->getItem();
        $cartId = $product->getId()."-".$subitem->getId();
        $quantity = $request->request->get('quantity');
        $unit = ($product->getProductUnit()) ? $product->getProductUnit()->getName() :'';
        $brand = ($product->getBrand()) ? $product->getBrand()->getName() :'';
        $category = ($product->getCategory()) ? $product->getCategory()->getName() :'';
        $size = !empty($subitem->getSize()) ? $subitem->getSize()->getName(): '';
        $data = array(
            'id' => $cartId,
            'name'=> $product->getName(),
            'size'=> $size,
            'brand'=> $brand,
            'productUnit'=> $unit,
            'category'=> $category,
            'price'=> $product->getSalesPrice(),
            'quantity' => $quantity,
            'subtotal' => ($quantity * $product->getSalesPrice()),
        );

        $cart->insert($data);
        $cartTotal = $cart->total();
        $totalItems = $cart->total_items();
        $cartResult = $cartTotal.'('.$totalItems.')';
        return new Response($cartTotal);

    }

    public function productUpdateCartSubitemAction(Request $request , $id)
    {
        $em = $this->getDoctrine()->getManager();
        $cart = new Cart($request->getSession());
        $quantity = (int)$_REQUEST['quantity'];
        if (!empty($id) and  $quantity){
            $data = array(
                'rowid' => $id,
                'quantity' => $quantity,
            );
            $cart->update($data);
            $cartTotal = (string)$cart->total();
            $totalItems = (string)$cart->total_items();
            $items = (string)count($cart->contents());
            $cartResult = $cartTotal.'('.$totalItems.')';
            $array =(json_encode(array('process'=>'success','cartResult' => $cartResult,'cartTotal' => $cartTotal,'totalItem' => $totalItems,'items' => $items)));

        }else{
            $cart->remove($id);
            $array =(json_encode(array('process'=>'invalid')));
        }
        echo $array;
        exit;
    }

    public function productAddCartAction(Request $request , ItemSub $product)
    {

        $cart = new Cart($request->getSession());
        $quantity = $request->request->get('quantity');
        $unit = ($product->getProductUnit()) ? $product->getProductUnit()->getName() :'';
        $brand = ($product->getBrand()) ? $product->getBrand()->getName() :'';
        $category = ($product->getCategory()) ? $product->getCategory()->getName() :'';
        $size = !empty($product->getSize()) ? $product->getSize()->getName(): '';
        $data = array(

            'id' => $product->getId(),
            'name'=> $product->getName(),
            'brand'=> $brand,
            'size'=> $size,
            'productUnit'=> $unit,
            'category'=> $category,
            'price'=> $product->getSalesPrice(),
            'quantity' => $quantity,
            'subtotal' => ($quantity * $product->getSalesPrice()),
        );

        $cart->insert($data);
        $cartTotal = $cart->total();
        $totalItems = $cart->total_items();
        $cartResult = $cartTotal.'('.$totalItems.')';
        return new Response($cartTotal);

    }

    public function medicineAddCartAction(Request $request , Item $product)
    {

        $cart = new Cart($request->getSession());
        $quantity = $request->request->get('quantity');
        $unit = ($product->getProductUnit()) ? $product->getProductUnit()->getName() :'';
        $brand = ($product->getBrand()) ? $product->getBrand()->getName() :'';
        $category = ($product->getCategory()) ? $product->getCategory()->getName() :'';
        $size = !empty($product->getSize()) ? $product->getSize()->getName(): '';
        $data = array(

            'id' => $product->getId(),
            'name'=> $product->getName(),
            'brand'=> $brand,
            'size'=> $size,
            'productUnit'=> $unit,
            'category'=> $category,
            'price'=> $product->getSalesPrice(),
            'quantity' => $quantity,
            'subtotal' => ($quantity * $product->getSalesPrice()),
        );

        $cart->insert($data);
        $cartTotal = $cart->total();
        $totalItems = $cart->total_items();
        $cartResult = $cartTotal.'('.$totalItems.')';
        return new Response($cartTotal);

    }

    public function productUpdateCartAction(Request $request , $id)
    {
        $em = $this->getDoctrine()->getManager();
        $cart = new Cart($request->getSession());
        $quantity = (int)$_REQUEST['quantity'];
        if (!empty($id) and  $quantity){
            $data = array(
                'rowid' => $id,
                'quantity' => $quantity,
            );
            $cart->update($data);
            $cartTotal = (string)$cart->total();
            $totalItems = (string)$cart->total_items();
            $items = (string)count($cart->contents());
            $cartResult = $cartTotal.'('.$totalItems.')';
            $array =(json_encode(array('process'=>'success','cartResult' => $cartResult,'cartTotal' => $cartTotal,'totalItem' => $totalItems,'items' => $items)));

        }else{
            $cart->remove($id);
            $array =(json_encode(array('process'=>'invalid')));
        }
        echo $array;
        exit;
    }

    public function cartItemRemoveAction(Request $request , $id)
    {
        $cart = new Cart($request->getSession());
        $cart->remove($id);
        $cartTotal = (string)$cart->total();
        $totalItems = (string)$cart->total_items();
        $items = (string)count($cart->contents());
        $cartResult = $cartTotal.'('.$totalItems.')';
        $array =(json_encode(array('process'=>'success','cartResult' => $cartResult,'cartTotal' => $cartTotal,'totalItem' => $totalItems,'items' => $items)));
        return new Response($array);
    }


    public function cartRemoveAction(Request $request)
    {
        $cart = new Cart($request->getSession());
        $cart->destroy();
        return new Response('success');
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
