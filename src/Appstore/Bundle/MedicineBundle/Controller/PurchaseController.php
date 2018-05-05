<?php

namespace Appstore\Bundle\MedicineBundle\Controller;


use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchase;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchaseItem;
use Appstore\Bundle\MedicineBundle\Entity\MedicineStock;
use Appstore\Bundle\MedicineBundle\Form\MedicineStockItemType;
use Appstore\Bundle\MedicineBundle\Form\PurchaseItemType;
use Appstore\Bundle\MedicineBundle\Form\PurchaseType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncode;

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
        $data = $_REQUEST;
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchase')->findWithSearch($config,$data);
        $pagination = $this->paginate($entities);
        return $this->render('MedicineBundle:Purchase:index.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
        ));
    }
    /**
     * Lists all Vendor entities.
     *
     */
    public function purchaseItemAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseItem')->findWithSearch($config,$data);
        $pagination = $this->paginate($entities);
        $racks = $this->getDoctrine()->getRepository('MedicineBundle:MedicineParticular')->findBy(array('medicineConfig'=> $config,'particularType'=>'1'));
        return $this->render('MedicineBundle:Purchase:purchaseItem.html.twig', array(
            'entities' => $pagination,
            'racks' => $racks,
            'searchForm' => $data,
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
        $stockItemForm = $this->createStockItemForm(new MedicineStock(), $entity);
        $purchaseItemForm = $this->createPurchaseItemForm(new MedicinePurchaseItem() , $entity);
        $editForm = $this->createEditForm($entity);
        return $this->render('MedicineBundle:Purchase:new.html.twig', array(
            'entity' => $entity,
            'purchaseItem' => $purchaseItemForm->createView(),
            'stockItemForm' => $stockItemForm->createView(),
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
                'id' => 'purchaseForm',
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

    private function createStockItemForm(MedicineStock $entity, MedicinePurchase $purchase )
    {
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $form = $this->createForm(new MedicineStockItemType($config), $entity, array(
            'action' => $this->generateUrl('medicine_stock_item_create', array('id' => $purchase->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'id' => 'stockItemForm',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }


    public function stockItemCreateAction(Request $request,MedicinePurchase $purchase)
    {
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $medicineId = $data['medicineStock']['name'];
        $medicine = $this->getDoctrine()->getRepository('MedicineBundle:MedicineBrand')->find($medicineId);
        $checkStockMedicine = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->checkDuplicateStockMedicine($config,$medicine);
        if(empty($checkStockMedicine)){
            $entity = new MedicineStock();
            $form = $this->createStockItemForm($entity, $purchase);
            $form->handleRequest($request);
            $entity->setMedicineConfig($config);
            $name = $medicine->getMedicineForm() . ' ' . $medicine->getName() . ' ' . $medicine->getStrength();
            $entity->setName($name);
            $entity->setMedicineBrand($medicine);
            $entity->setBrandName($medicine->getMedicineCompany()->getName());
            $entity->setMode('medicine');
            $em->persist($entity);
            $em->flush();
            $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseItem')->insertStockPurchaseItems($purchase, $entity, $data);
            $invoice = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchase')->updatePurchaseTotalPrice($purchase);
            $msg = 'Medicine added successfully';
            $result = $this->returnResultData($invoice, $msg);
            return new Response(json_encode($result));
        }
        exit;
    }


    public function particularSearchAction(MedicineStock $particular)
    {
        return new Response(json_encode(array('purchasePrice'=> $particular->getPurchasePrice(), 'salesPrice'=> $particular->getSalesPrice(),'quantity'=> 1)));
    }

    public function returnResultData(MedicinePurchase $entity,$msg=''){

        $invoiceParticulars = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseItem')->getPurchaseItems($entity);
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

    public function addParticularAction(Request $request, MedicinePurchase $invoice)
    {

        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();

        $expirationStartDate = ($data['purchaseItem']['expirationStartDate']);
        $expirationEndDate = ($data['purchaseItem']['expirationEndDate']);
        $entity = new MedicinePurchaseItem();
        $form = $this->createPurchaseItemForm($entity,$invoice);
        $form->handleRequest($request);
        $entity->setMedicinePurchase($invoice);
        $stockItem = ($data['purchaseItem']['stockName']);
        $entity->setMedicineStock($this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->find($stockItem));
        $entity->setPurchaseSubTotal($entity->getPurchasePrice() * $entity->getQuantity());
        $entity->setRemainingQuantity($entity->getQuantity());
        if(!empty($expirationStartDate)){
            $expirationStartDate = (new \DateTime($expirationStartDate));
            $entity->setExpirationStartDate($expirationStartDate);
        }
        if(!empty($expirationEndDate)) {
            $expirationEndDate = (new \DateTime($expirationEndDate));
            $entity->setExpirationEndDate($expirationEndDate);
        }
        $em->persist($entity);
        $em->flush();
        $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->updateRemovePurchaseQuantity($entity->getMedicineStock());
        $invoice = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchase')->updatePurchaseTotalPrice($invoice);
        $msg = 'Medicine added successfully';
        $result = $this->returnResultData($invoice,$msg);
        return new Response(json_encode($result));
        exit;
    }

    public function invoiceParticularDeleteAction(MedicinePurchase $invoice, MedicinePurchaseItem $particular){

        $em = $this->getDoctrine()->getManager();
        if (!$particular) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }
        $em->remove($particular);
        $em->flush();
        $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->updateRemovePurchaseQuantity($particular->getMedicineStock());
        $invoice = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchase')->updatePurchaseTotalPrice($invoice);
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
        $result = $this->returnResultData($purchase);
        return new Response(json_encode($result));
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
            $deliveryDateTime = $data['medicinepurchase']['receiveDate'];
            $receiveDate = (new \DateTime($deliveryDateTime));
            $entity->setReceiveDate($receiveDate);
            $entity->setProcess('Complete');
            $entity->setDue($entity->getNetTotal() - $entity->getPayment());
            $em->flush();
            $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->getPurchaseUpdateQnt($entity);
            return $this->redirect($this->generateUrl('medicine_purchase_show', array('id' => $entity->getId())));
        }
        $purchaseItemForm = $this->createPurchaseItemForm(new MedicinePurchaseItem() , $entity);
        $stockItemForm = $this->createStockItemForm(new MedicineStock(), $entity);
        return $this->render('MedicineBundle:Purchase:new.html.twig', array(
            'entity' => $entity,
            'purchaseItem' => $purchaseItemForm->createView(),
            'stockItemForm' => $stockItemForm->createView(),
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
            $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->getPurchaseUpdateQnt($purchase);
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
        $entity = $em->getRepository('MedicineBundle:MedicineVendor')->find($id);

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

    public function inlineUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('MedicineBundle:MedicinePurchaseItem')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseItem entity.');
        }
        if($data['name'] == 'SalesPrice' and 0 < (float)$data['value']){
            $process = 'set'.$data['name'];
            $entity->$process((float)$data['value']);
            $em->flush();
        }

        if($data['name'] == 'PurchasePrice' and 0 < (float)$data['value']){
            $entity->setPurchasePrice((float)$data['value']);
            $entity->setPurchaseSubTotal((float)$data['value'] * $entity->getQuantity());
            $em->flush();
            $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchase')->updatePurchaseTotalPrice($entity->getMedicinePurchase());
        }
        $salesQnt = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesItem')->salesPurchaseStockItemUpdate($entity);
        if($data['name'] == 'Quantity' and $salesQnt <= (int)$data['value']){
            $entity->setQuantity((int)$data['value']);
            $entity->setPurchaseSubTotal((int)$data['value'] * $entity->getPurchasePrice());
            $em->flush();
            $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->updateRemovePurchaseQuantity($entity->getMedicineStock());
            $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchase')->updatePurchaseTotalPrice($entity->getMedicinePurchase());
        }
        exit;
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
