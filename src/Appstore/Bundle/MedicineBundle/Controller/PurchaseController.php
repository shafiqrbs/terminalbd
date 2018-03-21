<?php

namespace Appstore\Bundle\MedicineBundle\Controller;


use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchase;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchaseItem;
use Appstore\Bundle\MedicineBundle\Entity\MedicineStock;
use Appstore\Bundle\MedicineBundle\Form\PurchaseItemType;
use Appstore\Bundle\MedicineBundle\Form\PurchaseType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Vendor controller.
 *
 */
class PurchaseController extends Controller
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


    /**
     * Lists all Vendor entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchase')->findBy(array('medicineConfig' => $config,'mode'=>'medicine'),array('created'=>'DESC'));
        $pagination = $this->paginate($entities);

        return $this->render('MedicineBundle:Purchase:index.html.twig', array(
            'entities' => $pagination,
        ));
    }
    /**
     * Creates a new Vendor entity.
     *
     */
    public function createAction(Request $request)
    {
       
    }


    public function newAction()
    {

        $em = $this->getDoctrine()->getManager();
        $entity = new MedicinePurchase();
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entity->setMedicineConfig($config);
        $entity->setCreatedBy($this->getUser());
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('medicine_purchase_edit', array('id' => $entity->getId())));

    }


    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entity = $em->getRepository('MedicineBundle:MedicinePurchase')->findOneBy(array('medicineConfig' => $config , 'id' => $id));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $purchaseItemForm = $this->createPurchaseItemForm(new MedicinePurchaseItem() , $entity);
        $editForm = $this->createEditForm($entity);
        return $this->render('MedicineBundle:Purchase:new.html.twig', array(
            'entity' => $entity,
            'purchaseItem' => $purchaseItemForm->createView(),
            'form' => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Invoice entity.wq
     *
     * @param Invoice $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(MedicinePurchase $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new PurchaseType($globalOption), $entity, array(
            'action' => $this->generateUrl('medicine_purchase_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'id' => 'posForm',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    private function createPurchaseItemForm(MedicinePurchaseItem $purchaseItem , MedicinePurchase $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new PurchaseItemType($globalOption), $purchaseItem, array(
            'action' => $this->generateUrl('medicine_purchase_particular_add', array('invoice' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'id' => 'purchaseItemForm',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    public function particularSearchAction(MedicineStock $particular)
    {
        return new Response(json_encode(array('purchasePrice'=> $particular->getPurchasePrice(), 'salesPrice'=> $particular->getSalesPrice(),'quantity'=> 1)));
    }

    public function returnResultData(MedicinePurchase $entity,$msg=''){

        $invoiceParticulars = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseItem')->getSalesItems($entity);
        $subTotal = $entity->getSubTotal() > 0 ? $entity->getSubTotal() : 0;
        $netTotal = $entity->getNetTotal() > 0 ? $entity->getNetTotal() : 0;
        $payment = $entity->getPayment() > 0 ? $entity->getPayment() : 0;
        $due = $entity->getDue();
        $discount = $entity->getDiscount() > 0 ? $entity->getDiscount() : 0;
        $data = array(
            'subTotal' => $subTotal,
            'netTotal' => $netTotal,
            'payment' => $payment ,
            'due' => $due,
            'vat' => $vat,
            'discount' => $discount,
            'invoiceParticulars' => $invoiceParticulars ,
            'success' => 'success'
        );

        return $data;

    }

    public function addParticularAction(Request $request, MedicinePurchase $invoice)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = new MedicinePurchaseItem();
        $form = $this->createPurchaseItemForm($entity,$invoice);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setMedicinePurchase($invoice);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('medicine_stock'));
        }
        $invoice = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchase')->updatePurchaseTotalPrice($invoice);
        $invoiceParticulars = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseItem')->getPurchaseItems($invoice);
        $msg = 'Particular added successfully';

        $subTotal = $invoice->getSubTotal() > 0 ? $invoice->getSubTotal() : 0;
        $grandTotal = $invoice->getNetTotal() > 0 ? $invoice->getNetTotal() : 0;
        $dueAmount = $invoice->getDue() > 0 ? $invoice->getDue() : 0;

        return new Response(json_encode(array('subTotal' => $subTotal,'grandTotal' => $grandTotal,'dueAmount' => $dueAmount, 'vat' => '','invoiceParticulars' => $invoiceParticulars, 'msg' => $msg )));
        exit;
    }

    public function invoiceParticularDeleteAction(MedicinePurchase $invoice, MedicinePurchaseItem $particular){

        $em = $this->getDoctrine()->getManager();
        if (!$particular) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }

        $em->remove($particular);
        $em->flush();
        $invoice = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchase')->updatePurchaseTotalPrice($invoice);
        $invoiceParticulars = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseItem')->getPurchaseItems($invoice);

        $msg = 'Particular deleted successfully';
        $subTotal = $invoice->getSubTotal() > 0 ? $invoice->getSubTotal() : 0;
        $grandTotal = $invoice->getNetTotal() > 0 ? $invoice->getNetTotal() : 0;
        $dueAmount = $invoice->getDue() > 0 ? $invoice->getDue() : 0;
        return new Response(json_encode(array('subTotal' => $subTotal,'grandTotal' => $grandTotal,'dueAmount' => $dueAmount, 'vat' => '','invoiceParticular' => $invoiceParticulars, 'msg' => $msg )));
        exit;


    }

    public function invoiceDiscountUpdateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $discount = $request->request->get('discount');
        $purchase = $request->request->get('invoice');

        $purchase = $em->getRepository('MedicineBundle:MedicinePurchase')->find($purchase);
        $total = ($purchase->getSubTotal() - $discount);
        $vat = 0;
        if($total > $discount ){

            $purchase->setDiscount($discount);
            $purchase->setNetTotal($total + $vat);
            $purchase->setDue($total + $vat);
            $em->persist($purchase);
            $em->flush();
        }

        $invoiceParticulars = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseItem')->getPurchaseItems($purchase);
        $subTotal = $purchase->getSubTotal() > 0 ? $purchase->getSubTotal() : 0;
        $grandTotal = $purchase->getNetTotal() > 0 ? $purchase->getNetTotal() : 0;
        $dueAmount = $purchase->getDue() > 0 ? $purchase->getDue() : 0;
        return new Response(json_encode(array('subTotal' => $subTotal,'grandTotal' => $grandTotal,'dueAmount' => $dueAmount, 'vat' => '','invoiceParticulars' => $invoiceParticulars, 'msg' => 'Discount updated successfully' , 'success' => 'success')));
        exit;
    }

    public function updateAction(Request $request, MedicinePurchase $entity)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            $data = $request->request->all();
            $deliveryDateTime = $data['appstore_bundle_medicinepurchase']['receiveDate'];
            $receiveDate = (new \DateTime($deliveryDateTime));
            $entity->setReceiveDate($receiveDate);
            $entity->setProcess('Done');
            $entity->setDue($entity->getNetTotal() - $entity->getPayment());
            $em->flush();
            return $this->redirect($this->generateUrl('medicine_purchase_show', array('id' => $entity->getId())));
        }
        $particulars = $em->getRepository('MedicineBundle:Particular')->getMedicineParticular($entity->getMedicineConfig());
        return $this->render('MedicineBundle:Purchase:new.html.twig', array(
            'entity' => $entity,
            'particulars' => $particulars,
            'form' => $editForm->createView(),
        ));
    }


    /**
     * Finds and displays a Vendor entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MedicineBundle:MedicinePurchase')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendor entity.');
        }
        return $this->render('MedicineBundle:Purchase:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    public function approvedAction(MedicinePurchase $purchase)
    {
        $em = $this->getDoctrine()->getManager();
        if (!empty($purchase)) {
            $em = $this->getDoctrine()->getManager();
            $purchase->setProcess('Approved');
            $purchase->setApprovedBy($this->getUser());
            $em->flush();
            $this->getDoctrine()->getRepository('MedicineBundle:MedicineParticular')->getPurchaseUpdateQnt($purchase);
            $accountPurchase = $em->getRepository('AccountingBundle:AccountPurchase')->insertMedicineAccountPurchase($purchase);
            $em->getRepository('AccountingBundle:Transaction')->purchaseGlobalTransaction($accountPurchase);
            return new Response('success');
        } else {
            return new Response('failed');
        }
        exit;
    }



    /**
     * Deletes a Vendor entity.
     *
     */
    public function deleteAction(MedicinePurchase $entity)
    {

        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendor entity.');
        }

        $em->remove($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('medicine_purchase'));
    }


    /**
     * Status a Page entity.
     *
     */
    public function statusAction(Request $request, $id)
    {


        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('MedicineBundle:HmsVendor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find District entity.');
        }

        $status = $entity->isStatus();
        if($status == 1){
            $entity->setStatus(false);
        } else{
            $entity->setStatus(true);
        }
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',"Status has been changed successfully"
        );
        return $this->redirect($this->generateUrl('medicine_vendor'));
    }

    public function autoSearchAction(Request $request)
    {
        $item = $_REQUEST['q'];
        if ($item) {
            $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
            $item = $this->getDoctrine()->getRepository('MedicineBundle:HmsVendor')->searchAutoComplete($item,$inventory);
        }
        return new JsonResponse($item);
    }

    public function searchVendorNameAction($vendor)
    {
        return new JsonResponse(array(
            'id'=>$vendor,
            'text'=>$vendor
        ));
    }

}
