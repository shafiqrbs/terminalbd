<?php

namespace Appstore\Bundle\InventoryBundle\Controller;

use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Appstore\Bundle\InventoryBundle\Entity\SalesReturnItem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\InventoryBundle\Entity\SalesReturn;
use Appstore\Bundle\InventoryBundle\Form\SalesReturnType;
use Symfony\Component\HttpFoundation\Response;

/**
 * SalesReturn controller.
 *
 */
class SalesReturnController extends Controller
{

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities = $em->getRepository('InventoryBundle:SalesReturn')->findBy(array('inventoryConfig'=>$inventory));
        return $this->render('InventoryBundle:SalesReturn:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */
    public function searchAction(Request $request)
    {
        $invoiceDate = $request->request->get('invoiceDate');
        $invoice = date('Ymd',strtotime($invoiceDate));
        $salesId = $invoice.''.$request->request->get('salesId');
        $sales = $this->getDoctrine()->getRepository('InventoryBundle:Sales')->findBySalesReturn($salesId);
        if(!empty($sales)){
            $em = $this->getDoctrine()->getManager();
            $entity = new SalesReturn();
            $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
            $entity->setSales($sales);
            $entity->setInventoryConfig($inventory);
            $entity->setCreatedBy($this->getUser());
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('inventory_salesreturn_edit', array('id' => $entity->getId())));
        }else{
            return $this->redirect($this->generateUrl('inventory_salesreturn'));
        }

    }

    /**
     * Finds and displays a SalesReturn entity.
     *
     */
    public function showAction(SalesReturn $salesReturn )
    {
        $em = $this->getDoctrine()->getManager();
        return $this->render('InventoryBundle:SalesReturn:show.html.twig', array(
            'entity'      => $salesReturn->getSales(),
            'salesReturn'      => $salesReturn,
        ));
    }


    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function editAction(SalesReturn $salesReturn)
    {

        if (!$salesReturn) {
            throw $this->createNotFoundException('Unable to find SalesReturn entity.');
        }elseif ($salesReturn->getProcess() == 'complete'){
            return $this->redirect($this->generateUrl('inventory_salesreturn_show',array('id'=>$salesReturn->getId())));
        }

        $editForm = $this->createEditForm($salesReturn);
        return $this->render('InventoryBundle:SalesReturn:new.html.twig', array(
            'entity'      => $salesReturn->getSales(),
            'salesReturn'      => $salesReturn,
            'form'   => $editForm->createView(),

        ));
    }

    /**
    * Creates a form to edit a SalesReturn entity.
    *
    * @param SalesReturn $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(SalesReturn $entity)
    {
        $form = $this->createForm(new SalesReturnType(), $entity, array(
            'action' => $this->generateUrl('inventory_salesreturn_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        return $form;
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:SalesReturn')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SalesReturn entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $entity->setProcess('complete');
            $em->flush();
            $this->getDoctrine()->getRepository('InventoryBundle:SalesReturn')->updateSalesReturn($entity);
            $em->getRepository('InventoryBundle:Item')->getItemSalesReturnUpdate($entity);
            $em->getRepository('InventoryBundle:StockItem')->insertSalesReturnStockItem($entity);
            $accountSalesReturn = $em->getRepository('AccountingBundle:AccountSalesReturn')->insertAccountSalesReturn($entity);
            $em->getRepository('AccountingBundle:Transaction')->salesReturnTransaction($entity,$accountSalesReturn);
            return $this->redirect($this->generateUrl('inventory_salesreturn_edit', array('id' => $entity->getId())));
        }

        return $this->render('InventoryBundle:SalesReturn:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_INVENTORY_SALES")
     */

    public function salesItemAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $salesReturn = $request->request->get('salesReturn');
        $item = $request->request->get('item');
        $quantity = $request->request->get('quantity');
        $price = $request->request->get('price');
        $item = $this->getDoctrine()->getRepository('InventoryBundle:SalesItem')->find($item);
        $salesReturn = $this->getDoctrine()->getRepository('InventoryBundle:SalesReturn')->find($salesReturn);
        $currentSalesReturnItem = $this->getDoctrine()->getRepository('InventoryBundle:SalesReturn')->getSalesReturnItemSum($item);
        $totalQnt = $currentSalesReturnItem + $quantity;
        if($totalQnt > $item->getQuantity()){
            return new Response(json_encode(array('success'=>'invalid','message'=>'Sales return item already added')));
        }else{

            $entity = new SalesReturnItem();
            $entity->setSalesReturn($salesReturn);
            $entity->setSalesItem($item);
            $entity->setQuantity($quantity);
            $entity->setPrice($price);
            $entity->setSubTotal($price * $quantity);
            $em->persist($entity);
            $em->flush();
            $this->getDoctrine()->getRepository('InventoryBundle:SalesReturn')->updateSalesReturn($salesReturn);
            return new Response(json_encode(array('success'=>'success','message'=>'Sales return item added successfully')));
        }

        exit;


    }

    /**
     * Deletes a SalesReturnItem entity.
     *
     */
    public function deleteAction(SalesReturn $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sales Return entity.');
        }
        $em->remove($entity);
        $em->flush();
        return new Response(json_encode(array('success'=>'success','message'=>'Cancel done')));
        exit;

    }

    /**
     * Deletes a SalesReturnItem entity.
     *
     */
    public function itemDeleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('InventoryBundle:SalesReturnItem')->findOneBy(array('salesItem'=>$id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }
        $this->getDoctrine()->getRepository('InventoryBundle:SalesReturn')->updateSalesTotalReturnPrice($entity);
        $em->remove($entity);
        $em->flush();
        return new Response(json_encode(array('success'=>'success','message'=>'Cancel done')));
        exit;

    }
}
