<?php

namespace Appstore\Bundle\InventoryBundle\Controller;

use Appstore\Bundle\InventoryBundle\Entity\DeliveryItem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\InventoryBundle\Entity\Delivery;
use Appstore\Bundle\InventoryBundle\Form\DeliveryType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Delivery controller.
 *
 */
class DeliveryController extends Controller
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
     * Lists all Delivery entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities = $this->getDoctrine()->getManager()->getRepository('InventoryBundle:Delivery')->findWithSearch($inventory,$data);
        $paginate = $this->paginate($entities);
        $shops = $this->getDoctrine()->getRepository('DomainUserBundle:Branches')->findBy(array('globalOption'=> $this->getUser()->getGlobalOption()),array('name'=>'ASC'));
        return $this->render('InventoryBundle:Delivery:index.html.twig', array(
            'entities' => $paginate,
            'shops' => $shops,
        ));
    }

    /**
     * Creates a new Delivery entity.
     *
     */
    public function createAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = new DeliveryItem();
        $barcode = $request->request->get('barcode');
        $quantity = $request->request->get('quantity');
        $delivery = $request->request->get('delivery');

        $purchaseItem = $this->getDoctrine()->getRepository('InventoryBundle:PurchaseItem')->findOneBy(array('barcode'=>$barcode));
        $delivery =  $em->getRepository('InventoryBundle:Delivery')->find($delivery);
        if ($purchaseItem->getQuantity() >= $quantity){

            $entity->setDelivery($delivery);
            $entity->setQuantity($quantity);
            $entity->setItem($purchaseItem->getItem());
            $entity->setPurchaseItem($purchaseItem);
            $entity->setSalesPrice($purchaseItem->getSalesPrice());
            $entity->setSubTotal($purchaseItem->getSalesPrice() * $entity->getQuantity());
            $em->persist($entity);
            $em->flush();

            $this->getDoctrine()->getRepository('InventoryBundle:Delivery')->updateDeliveryTotal($delivery);
            $deliveryItems =$this->getDoctrine()->getRepository('InventoryBundle:Delivery')->getDeliveryItems($delivery);

            return new Response(json_encode(array('deliveryItems' => $deliveryItems ,'totalAmount' => $delivery->getTotalAmount(),'totalItem' => $delivery->getTotalItem() ,'totalQuantity' => $delivery->getTotalQuantity(),'msg' => 'success')));

        }else{

            $deliveryItems =$this->getDoctrine()->getRepository('InventoryBundle:Delivery')->getDeliveryItems($delivery);
            return new Response(json_encode(array('deliveryItems' => $deliveryItems ,'totalAmount' => $delivery->getTotalAmount(),'totalItem' => $delivery->getTotalItem() ,'totalQuantity' => $delivery->getTotalQuantity() ,'msg' => 'invalid barcode or quantity')));
        }
        exit;

    }

    /**
     * Displays a form to create a new Delivery entity.
     *
     */
    public function newAction()
    {
        $entity = new Delivery();
        $shop = isset($_REQUEST['shop']) ? $_REQUEST['shop'] : '';
        $em = $this->getDoctrine()->getManager();
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        if($shop > 0 ){
            $shop = $em->getRepository('DomainUserBundle:Branches')->find($shop);
            if(!empty($shop)){
                $entity->setBranch($shop);
            }
        }
        $entity->setInventoryConfig($inventory);
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('inventory_delivery_edit', array( 'code' => $entity->getInvoice())));

    }

    /**
     * Finds and displays a Delivery entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:Delivery')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Delivery entity.');
        }
        return $this->render('InventoryBundle:Delivery:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * Displays a form to edit an existing Delivery entity.
     *
     */
    public function editAction($code)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:Delivery')->findOneBy(array('invoice' =>$code ));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Delivery entity.');
        }
        $editForm = $this->createEditForm($entity);
        return $this->render('InventoryBundle:Delivery:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Delivery entity.
    *
    * @param Delivery $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Delivery $entity)
    {
        $deliveryItem = new DeliveryItem();
        $form = $this->createForm(new DeliveryType(), $deliveryItem , array(
            'action' => $this->generateUrl('inventory_delivery_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
         return $form;
    }
    /**
     * Edits an existing Delivery entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:Delivery')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Delivery entity.');
        }
        if($request->request->get('save') == 'save'){
            $entity->setProcess('completed');
            $em->flush();
        }
        $this->get('session')->getFlashBag()->add(
            'success',"Data has been updated successfully"
        );
        return $this->redirect($this->generateUrl('inventory_delivery'));

    }
    /**
     * Deletes a Delivery entity.
     *
     */
    public function deleteAction($id)
    {

            $em = $this->getDoctrine()->getManager();
            $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
            $entity = $em->getRepository('InventoryBundle:Delivery')->findOneBy(array('inventoryConfig'=>$inventory,'id' => $id));
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Delivery entity.');
            }

            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been deleted successfully"
            );
           return $this->redirect($this->generateUrl('inventory_delivery'));
    }

    /**
     * Deletes a Delivery entity.
     *
     */
    public function deliveryItemDeleteAction(Delivery $delivery , DeliveryItem $entity)
    {

        $em = $this->getDoctrine()->getManager();
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig()->getId();
        if ($entity->getDelivery()->getInventoryConfig()->getId() == $inventory){
            $em->remove($entity);
            $em->flush();
        }
        $this->getDoctrine()->getRepository('InventoryBundle:Delivery')->updateDeliveryTotal($delivery);
        exit;
    }


    public function autoSearchAction(Request $request)
    {
        $item = $_REQUEST['q'];
        if ($item) {
            $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
            $item = $this->getDoctrine()->getRepository('InventoryBundle:Delivery')->searchAutoComplete($item,$inventory);
        }
        return new JsonResponse($item);
    }

    public function searchDeliveryNameAction($size)
    {
        return new JsonResponse(array(
            'id'=>$size,
            'text'=>$size
        ));
    }

    public function approveAction(Delivery $entity)
    {
        if (!empty($entity)) {
            $em = $this->getDoctrine()->getManager();
            $entity->setApprovedBy($this->getUser());
            $entity->setProcess('approved');
            $em->flush();
            return new Response('success');
        } else {
            return new Response('failed');
        }
        exit;
    }

    public function itemPurchaseDetailsAction(Request $request)
    {
        $item = $request->request->get('item');
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $data = $this->getDoctrine()->getRepository('InventoryBundle:Item')->itemDeliveryPurchaseDetails($inventory,$item);
        return new Response($data);
    }



    public function pdfIncomeAction()
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $data = $_REQUEST;
        $overview = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->reportIncome($globalOption,$data);
        $html = $this->renderView(
            'AccountingBundle:Report:incomePdf.html.twig', array(
                'overview' => $overview,
                'print' => ''
            )
        );
        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy          = new Pdf($wkhtmltopdfPath);
        $pdf             = $snappy->getOutputFromHtml($html);

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="incomePdf.pdf"');
        echo $pdf;

    }


    public function printAction(Delivery $entity)
    {
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        if(!empty($inventory)){
            return $this->render('InventoryBundle:Delivery:invoice.html.twig', array(
                'entity'      => $entity,
                'print' => '<script>window.print();</script>'
            ));
        }else{
            return $this->redirect($this->generateUrl('inventory_delivery'));
        }


    }
}
