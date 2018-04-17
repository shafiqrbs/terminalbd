<?php

namespace Appstore\Bundle\MedicineBundle\Controller;


use Appstore\Bundle\MedicineBundle\Entity\MedicineInstantPurchase;
use Appstore\Bundle\MedicineBundle\Entity\MedicineInstantPurchaseItem;
use Appstore\Bundle\MedicineBundle\Entity\MedicineStock;
use Appstore\Bundle\MedicineBundle\Form\InstantPurchaseItemType;
use Appstore\Bundle\MedicineBundle\Form\InstantPurchaseType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Vendor controller.
 *
 */
class InstantPurchaseController extends Controller
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
     * Lists all Vendor entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineInstantPurchase')->findBy(array('medicineConfig' => $config,'mode'=>'medicine'),array('created'=>'DESC'));
        $pagination = $this->paginate($entities);

        return $this->render('MedicineBundle:InstantPurchase:index.html.twig', array(
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
        $entity = new MedicineInstantPurchase();
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
        $entity = $em->getRepository('MedicineBundle:MedicineInstantPurchase')->findOneBy(array('medicineConfig' => $config , 'id' => $id));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $purchaseItemForm = $this->createInstantPurchaseItemForm(new MedicineInstantPurchaseItem() , $entity);
        $editForm = $this->createEditForm($entity);
        return $this->render('MedicineBundle:InstantPurchase:new.html.twig', array(
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
    private function createEditForm(MedicineInstantPurchase $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new InstantPurchaseType($globalOption), $entity, array(
            'action' => $this->generateUrl('medicine_purchase_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'id' => 'purchaseForm',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    private function createInstantPurchaseItemForm(MedicineInstantPurchaseItem $purchaseItem , MedicineInstantPurchase $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new InstantPurchaseItemType($globalOption), $purchaseItem, array(
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
        return new Response(json_encode(array('purchasePrice'=> $particular->getInstantPurchasePrice(), 'salesPrice'=> $particular->getSalesPrice(),'quantity'=> 1)));
    }

    public function returnResultData(MedicineInstantPurchase $entity,$msg=''){

        $invoiceParticulars = $this->getDoctrine()->getRepository('MedicineBundle:MedicineInstantPurchaseItem')->getInstantPurchaseItems($entity);
        $subTotal = $entity->getSubTotal() > 0 ? $entity->getSubTotal() : 0;
        $netTotal = $entity->getNetTotal() > 0 ? $entity->getNetTotal() : 0;
        $payment = $entity->getPayment() > 0 ? $entity->getPayment() : 0;
        $due = $entity->getDue();
        $discount = $entity->getDiscount() > 0 ? $entity->getDiscount() : 0;
        $data = array(
            'msg' => $msg,
            'subTotal' => $subTotal,
            'netTotal' => $netTotal,
            'payment' => $payment ,
            'due' => $due,
            'discount' => $discount,
            'invoiceParticulars' => $invoiceParticulars ,
            'success' => 'success'
        );

        return $data;

    }

    public function addParticularAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        var_dump($data);
        exit;
        $expirationDate = $data['expirationDate'];
        $entity = new MedicineInstantPurchase();
        $entity->setPurchasePrice($data['purchasePrice']);
        $entity->setSalesPrice($data['salesPrice']);
        $entity->setPurchaseSubTotal($entity->getPurchasePrice() * $entity->getQuantity());
        $entity->setSalesSubTotal($entity->getSalesPrice() * $entity->getQuantity());
        $expirationDate = (new \DateTime($expirationDate));
        $entity->setExpirationDate($expirationDate);
        $em->persist($entity);
        $em->flush();
        $invoice = $this->getDoctrine()->getRepository('MedicineBundle:MedicineInstantPurchase')->updateInstantPurchaseTotalPrice($invoice);
        $msg = 'Medicine added successfully';
        $result = $this->returnResultData($invoice,$msg);
        return new Response(json_encode($result));
        exit;
    }

    public function invoiceParticularDeleteAction(MedicineInstantPurchase $invoice, MedicineInstantPurchaseItem $particular){

        $em = $this->getDoctrine()->getManager();
        if (!$particular) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }
        $em->remove($particular);
        $em->flush();
        $invoice = $this->getDoctrine()->getRepository('MedicineBundle:MedicineInstantPurchase')->updateInstantPurchaseTotalPrice($invoice);
        $msg = 'Medicine added successfully';
        $result = $this->returnResultData($invoice,$msg);
        return new Response(json_encode($result));
        exit;


    }

    public function invoiceDiscountUpdateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $discount = $request->request->get('discount');
        $purchase = $request->request->get('invoice');

        $purchase = $em->getRepository('MedicineBundle:MedicineInstantPurchase')->find($purchase);
        $total = ($purchase->getSubTotal() - $discount);
        $vat = 0;
        if($total > $discount ){

            $purchase->setDiscount($discount);
            $purchase->setNetTotal($total + $vat);
            $purchase->setDue($total + $vat);
            $em->persist($purchase);
            $em->flush();
        }

        $invoiceParticulars = $this->getDoctrine()->getRepository('MedicineBundle:MedicineInstantPurchaseItem')->getInstantPurchaseItems($purchase);
        $subTotal = $purchase->getSubTotal() > 0 ? $purchase->getSubTotal() : 0;
        $grandTotal = $purchase->getNetTotal() > 0 ? $purchase->getNetTotal() : 0;
        $dueAmount = $purchase->getDue() > 0 ? $purchase->getDue() : 0;
        return new Response(json_encode(array('subTotal' => $subTotal,'grandTotal' => $grandTotal,'dueAmount' => $dueAmount, 'vat' => '','invoiceParticulars' => $invoiceParticulars, 'msg' => 'Discount updated successfully' , 'success' => 'success')));
        exit;
    }

    public function updateAction(Request $request, MedicineInstantPurchase $entity)
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
        return $this->render('MedicineBundle:InstantPurchase:new.html.twig', array(
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

        $entity = $em->getRepository('MedicineBundle:MedicineInstantPurchase')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendor entity.');
        }
        return $this->render('MedicineBundle:InstantPurchase:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    public function approvedAction(MedicineInstantPurchase $purchase)
    {
        $em = $this->getDoctrine()->getManager();
        if (!empty($purchase)) {
            $em = $this->getDoctrine()->getManager();
            $purchase->setProcess('Approved');
            $purchase->setApprovedBy($this->getUser());
            $em->flush();
            $this->getDoctrine()->getRepository('MedicineBundle:MedicineParticular')->getInstantPurchaseUpdateQnt($purchase);
            $accountInstantPurchase = $em->getRepository('AccountingBundle:AccountInstantPurchase')->insertMedicineAccountInstantPurchase($purchase);
            $em->getRepository('AccountingBundle:Transaction')->purchaseGlobalTransaction($accountInstantPurchase);
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
    public function deleteAction(MedicineInstantPurchase $entity)
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
