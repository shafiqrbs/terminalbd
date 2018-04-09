<?php

namespace Appstore\Bundle\MedicineBundle\Controller;


use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchaseItem;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSales;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSalesItem;
use Appstore\Bundle\MedicineBundle\Entity\MedicineStock;
use Appstore\Bundle\MedicineBundle\Form\SalesItemType;
use Appstore\Bundle\MedicineBundle\Form\SalesType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Vendor controller.
 *
 */
class SalesController extends Controller
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
        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->invoiceLists($this->getUser(),$data);
        $pagination = $this->paginate($entities);
        $transactionMethods = $em->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status' => 1), array('name' => 'ASC'));
        return $this->render('MedicineBundle:Sales:index.html.twig', array(
            'entities' => $pagination,
            'transactionMethods' => $transactionMethods,
            'searchForm' => $data,
        ));
    }

    public function newAction()
    {

        $em = $this->getDoctrine()->getManager();
        $entity = new MedicineSales();
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entity->setMedicineConfig($config);
        $entity->setCreatedBy($this->getUser());
        $customer = $em->getRepository('DomainUserBundle:Customer')->defaultCustomer($this->getUser()->getGlobalOption());
        $entity->setCustomer($customer);
        $transactionMethod = $em->getRepository('SettingToolBundle:TransactionMethod')->find(1);
        $entity->setTransactionMethod($transactionMethod);
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('medicine_sales_edit', array('id' => $entity->getId())));

    }
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entity = $em->getRepository('MedicineBundle:MedicineSales')->findOneBy(array('medicineConfig' => $config , 'id' => $id));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MedicineSales entity.');
        }
        $salesItemForm = $this->createMedicineSalesItemForm(new MedicineSalesItem() , $entity);
        $editForm = $this->createEditForm($entity);
        return $this->render('MedicineBundle:Sales:new.html.twig', array(
            'entity' => $entity,
            'salesItem' => $salesItemForm->createView(),
            'form' => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a MedicineSales entity.wq
     *
     * @param MedicineSales $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(MedicineSales $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $form = $this->createForm(new SalesType($globalOption,$location), $entity, array(
            'action' => $this->generateUrl('medicine_sales_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'id' => 'salesForm',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    private function createMedicineSalesItemForm(MedicineSalesItem $salesItem , MedicineSales $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new SalesItemType($globalOption), $salesItem, array(
            'action' => $this->generateUrl('medicine_sales_item_add', array('invoice' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'id' => 'salesItemForm',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    public function stockSearchAction(MedicineStock $stock)
    {
        $purchaseItems ='';
        $purchaseItems .='<option value="">--Select the Barcode--</option>';
        /* @var $item MedicinePurchaseItem */
        foreach ($stock->getMedicinePurchaseItems() as $item){
            $date = $item->getExpirationDate()->format('Y-m-d');
            $purchaseItems .= '<option value="'.$item->getId().'">'.$item->getBarcode().' - '.$date.'['.$item->getRemainingQuantity().']</option>';
        }
        return new Response(json_encode(array('purchaseItems' => $purchaseItems,'salesPrice'=> $stock->getSalesPrice())));
    }

    public function returnResultData(MedicineSales $entity,$msg=''){

        $salesItems = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesItem')->getSalesItems($entity);
        $subTotal = $entity->getSubTotal() > 0 ? $entity->getSubTotal() : 0;
        $netTotal = $entity->getNetTotal() > 0 ? $entity->getNetTotal() : 0;
        $payment = $entity->getReceived() > 0 ? $entity->getReceived() : 0;
        $due = $entity->getDue();
        $discount = $entity->getDiscount() > 0 ? $entity->getDiscount() : 0;
        $data = array(
            'msg' => $msg,
            'subTotal' => $subTotal,
            'netTotal' => $netTotal,
            'payment' => $payment ,
            'due' => $due,
            'discount' => $discount,
            'salesItems' => $salesItems ,
            'success' => 'success'
        );

        return $data;

    }

    public function addMedicineAction(Request $request, MedicineSales $invoice)
    {

        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $entity = new MedicineSalesItem();
        $form = $this->createMedicineSalesItemForm($entity,$invoice);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        $entity->setMedicineSales($invoice);
        $barcode = $data['salesitem']['barcode'];
        $purchaseItem = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseItem')->find($barcode);
        $entity->setMedicinePurchaseItem($purchaseItem);
        $entity->setSubTotal($entity->getSalesPrice() * $entity->getQuantity());
        $em->persist($entity);
        $em->flush();
        $invoice = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->updateMedicineSalesTotalPrice($invoice);
        $msg = 'Medicine added successfully';
        $result = $this->returnResultData($invoice,$msg);
        return new Response(json_encode($result));
        exit;
    }

    public function salesItemDeleteAction(MedicineSales $invoice, MedicineSalesItem $particular){

        $em = $this->getDoctrine()->getManager();
        if (!$particular) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }
        $em->remove($particular);
        $em->flush();
        $invoice = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->updateMedicineSalesTotalPrice($invoice);
        $msg = 'Medicine added successfully';
        $result = $this->returnResultData($invoice,$msg);
        return new Response(json_encode($result));
        exit;


    }

    public function invoiceDiscountUpdateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $discountType = $request->request->get('discountType');
        $discount = $request->request->get('discount');
        $invoice = $request->request->get('invoice');
        $entity = $em->getRepository('MedicineBundle:MedicineSales')->find($invoice);
        $subTotal = $entity->getSubTotal();
        if($discountType == 'flat'){
            $total = ($subTotal  - $discount);
        }else{
            $discount = ($subTotal*$discount)/100;
            $total = ($subTotal  - $discount);
        }
        $vat = 0;
        if($total > $discount ){
            $entity->setDiscount(round($discount));
            $entity->setNetTotal(round($total + $vat));
            $entity->setDue(round($total + $vat));
            $em->flush();
        }
        $msg = 'Discount successfully';
        $result = $this->returnResultData($entity,$msg);
        return new Response(json_encode($result));
        exit;
    }

    public function updateAction(Request $request, MedicineSales $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MedicineSales entity.');
        }
        $salesItemForm = $this->createMedicineSalesItemForm(new MedicineSalesItem() , $entity);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        $data = $request->request->all();
        if ($editForm->isValid()) {
            if (!empty($data['customerMobile'])) {
                $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['customerMobile']);
                $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->newExistingCustomerForSales($globalOption,$mobile,$data);
                $entity->setCustomer($customer);

            } elseif(!empty($data['mobile'])) {

                $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['mobile']);
                $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $globalOption, 'mobile' => $mobile ));
                $entity->setCustomer($customer);
            }
            if($data['process'] == 'Hold'){
                $entity->setProcess('Hold');
            }else{
                $entity->setProcess('Done');
            }
            $entity->setDue($entity->getNetTotal() - $entity->getReceived());
            if($entity->getDue() > 0){
                $entity->setPaymentStatus('Due');
            }else{
                $entity->setPayment($entity->getNetTotal());
                $entity->setDue(0);
                $entity->setPaymentStatus('Paid');
            }
            $em->flush();
            return $this->redirect($this->generateUrl('medicine_sales_show', array('id' => $entity->getId())));
        }
        return $this->render('MedicineBundle:Sales:new.html.twig', array(
            'entity' => $entity,
            'salesItemForm' => $salesItemForm->createView(),
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

        $entity = $em->getRepository('MedicineBundle:MedicineSales')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendor entity.');
        }
        return $this->render('MedicineBundle:Sales:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    public function approvedAction(MedicineSales $purchase)
    {
        $em = $this->getDoctrine()->getManager();
        if (!empty($purchase)) {
            $em = $this->getDoctrine()->getManager();
            $purchase->setProcess('Approved');
            $purchase->setApprovedBy($this->getUser());
            $em->flush();
            $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->getMedicineSalesUpdateQnt($purchase);
            $accountMedicineSales = $em->getRepository('AccountingBundle:AccountMedicineSales')->insertMedicineAccountMedicineSales($purchase);
            $em->getRepository('AccountingBundle:Transaction')->purchaseGlobalTransaction($accountMedicineSales);
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
    public function deleteAction(MedicineSales $entity)
    {

        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendor entity.');
        }

        $em->remove($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('medicine_sales'));
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
            $item = $this->getDoctrine()->getRepository('MedicineBundle:MedicineVendor')->searchAutoComplete($item,$inventory);
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