<?php

namespace Appstore\Bundle\MedicineBundle\Controller;


use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchase;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchaseItem;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSales;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSalesItem;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSalesTemporary;
use Appstore\Bundle\MedicineBundle\Entity\MedicineStock;
use Appstore\Bundle\MedicineBundle\Form\SalesItemType;
use Appstore\Bundle\MedicineBundle\Form\SalesType;
use Symfony\Component\HttpFoundation\JsonResponse;
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

    /**
     * Lists all Vendor entities.
     *
     */
    public function salesItemAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesItem')->salesItemLists($this->getUser(),$data);
        $pagination = $this->paginate($entities);
        $transactionMethods = $em->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status' => 1), array('name' => 'ASC'));
        return $this->render('MedicineBundle:Sales:salesItem.html.twig', array(
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
            if($item->getRemainingQuantity() > 0) {

                if(!empty($item->getExpirationEndDate()) and !empty($item->getExpirationStartDate())){
                    $expirationEndDate = $item->getExpirationEndDate()->format('d-m-y');
                    $expiration = $expirationEndDate;
                }else{
                    $expiration='Expiry empty';
                }
                $purchaseItems .= '<option value="' . $item->getId() . '">' . $item->getBarcode() . ' - ' . $expiration . '[' . $item->getRemainingQuantity() . ']</option>';
            }
        }
        return new Response(json_encode(array('purchaseItems' => $purchaseItems,'salesPrice'=> round($stock->getSalesPrice()))));
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

        $data = $request->request->all();
        $entity = new MedicineSalesItem();
        $form = $this->createMedicineSalesItemForm($entity,$invoice);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        $entity->setMedicineSales($invoice);
        $stockItem = ($data['salesitem']['stockName']);
        $stock = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->find($stockItem);
        $entity->setMedicineStock($stock);
       // $barcode = $data['salesitem']['barcode'];
       // $purchaseItem = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseItem')->find($barcode);
      //  $entity->setMedicinePurchaseItem($purchaseItem);
        $entity->setSubTotal($entity->getSalesPrice() * $entity->getQuantity());
        $entity->setPurchasePrice($stock->getPurchasePrice());
        $em->persist($entity);
        $em->flush();
      //  $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseItem')->updateRemovePurchaseItemQuantity($purchaseItem,'sales');
        $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->updateRemovePurchaseQuantity($stock,'sales');
        $invoice = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->updateMedicineSalesTotalPrice($invoice);
        $msg = 'Medicine added successfully';
        $result = $this->returnResultData($invoice,$msg);
        return new Response(json_encode($result));
        exit;
    }

    public function instantPurchaseSalesAction(MedicineSales $sales , MedicinePurchaseItem $item)
    {
        $em = $this->getDoctrine()->getManager();
        $quantity = $_REQUEST['quantity'];
        if(empty($item->getSalesQuantity())) {
            $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->updateRemovePurchaseQuantity($item->getMedicineStock());
        }
        $salesItem = new MedicineSalesItem();
        $salesItem->setMedicineSales($sales);
        $salesItem->setMedicineStock($item->getMedicineStock());
        $salesItem->setMedicinePurchaseItem($item);
        $salesItem->setQuantity($quantity);
        $salesItem->setSalesPrice($item->getSalesPrice());
        $salesItem->setSubTotal($salesItem->getSalesPrice() * $salesItem->getQuantity());
        $salesItem->setPurchasePrice($item->getMedicineStock()->getPurchasePrice());
        $em->persist($salesItem);
        $em->flush();
        $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseItem')->updateRemovePurchaseItemQuantity($item,'sales');
        $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->updateRemovePurchaseQuantity($item->getMedicineStock(),'sales');
        $invoice = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->updateMedicineSalesTotalPrice($sales);
        $msg = 'Medicine added successfully';
        $result = $this->returnResultData($invoice,$msg);
        return new Response(json_encode($result));
        exit;

    }

    public function salesItemDeleteAction(MedicineSales $invoice, MedicineSalesItem $particular){

        $em = $this->getDoctrine()->getManager();
     //   $item = $particular->getMedicinePurchaseItem();
        $stock = $particular->getMedicineStock();
	  //  $this->get('session')->set('item', $item);
	    $this->get('session')->set('stock', $stock);

	    if (!$particular) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }
        $em->remove($particular);
        $em->flush();
	 //   $item = $this->get('session')->get('item');
	    $stock = $this->get('session')->get('stock');
	 //   $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseItem')->updateRemovePurchaseItemQuantity($item,'sales');
        $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->updateRemovePurchaseQuantity($stock,'sales');
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
        $discountCal = (float)$request->request->get('discount');
        $invoice = $request->request->get('invoice');
        $entity = $em->getRepository('MedicineBundle:MedicineSales')->find($invoice);
        $subTotal = $entity->getSubTotal();
        if($discountType == 'flat'){
            $total = ($subTotal  - $discountCal);
            $discount = $discountCal;
        }else{
            $discount = ($subTotal*$discountCal)/100;
            $total = ($subTotal  - $discount);
        }
        $vat = 0;
        if($total > $discount ){
            $entity->setDiscountType($discountType);
            $entity->setDiscountCalculation($discountCal);
            $entity->setDiscount(round($discount));
            $entity->setNetTotal(round($total + $vat));
            $entity->setDue(round($total + $vat));
        }else{
	        $entity->setDiscountType('flat');
	        $entity->setDiscountCalculation(0);
	        $entity->setDiscount(0);
	        $entity->setNetTotal(round($entity->getSubTotal() + $vat));
	        $entity->setDue($entity->getNetTotal());
        }
	    $em->flush();
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
            if($data['process'] == 'hold'){
                $entity->setProcess('Hold');
            }else{
                $entity->setApprovedBy($this->getUser());
                $entity->setProcess('Done');
            }

            if ($entity->getNetTotal() <= $entity->getReceived()) {
                $entity->setReceived($entity->getNetTotal());
                $entity->setDue(0);
                $entity->setPaymentStatus('Paid');
            }else{
                $entity->setPaymentStatus('Due');
                $entity->setDue($entity->getNetTotal() - $entity->getReceived());
            }
            $em->flush();
            if($entity->getProcess() == 'Done'){
                $accountSales = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->insertMedicineAccountInvoice($entity);
                $em->getRepository('AccountingBundle:Transaction')->salesGlobalTransaction($accountSales);
            }
            if($data['process'] == 'save' or $data['process'] == 'hold' ){
                return $this->redirect($this->generateUrl('medicine_sales'));
            }else{
                return $this->redirect($this->generateUrl('medicine_sales_print_invoice', array('id' => $entity->getId())));
            }
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


    public function approvedAction(MedicineSales $sales)
    {
        $em = $this->getDoctrine()->getManager();
        if (!empty($sales)) {
            $sales->setProcess('Approved');
            $sales->setApprovedBy($this->getUser());
            $em->flush();
            $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->getSalesUpdateQnt($sales);
            $accountSales = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->insertMedicineAccountInvoice($sales);
            $em->getRepository('AccountingBundle:Transaction')->salesGlobalTransaction($accountSales);
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
		$this->allSalesItemDelete($entity);
        $em->remove($entity);
        $em->flush();
        exit;
        //return $this->redirect($this->generateUrl('medicine_sales'));
    }

	public function allSalesItemDelete(MedicineSales $invoice){

		$em = $this->getDoctrine()->getManager();

		/* @var $particular MedicineSalesItem */

		foreach ($invoice->getMedicineSalesItems() as $particular){

			$item = $particular->getMedicinePurchaseItem();
			$stock = $particular->getMedicineStock();
			$this->get('session')->set('item', $item);
			$this->get('session')->set('stock', $stock);
			$em->remove($particular);
			$em->flush();
			$item = $this->get('session')->get('item');
			$stock = $this->get('session')->get('stock');
			$this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseItem')->updateRemovePurchaseItemQuantity($item,'sales');
			$this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->updateRemovePurchaseQuantity($stock,'sales');

		}


	}



	/**
     * Status a Page entity.
     *
     */


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

    public function reverseAction(MedicineSales $sales)
    {

        /*
         * Item Remove Total quantity
         * Stock Details
         * Purchase Item
         * Purchase Vendor Item
         * Purchase
         * Account Purchase
         * Account Journal
         * Transaction
         * Delete Journal & Account Purchase
         */

        set_time_limit(0);
        $em = $this->getDoctrine()->getManager();
        $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->accountMedicineSalesReverse($sales);
        $sales->setRevised(true);
        $sales->setProcess('Created');
        $em->flush();
        $template = $this->get('twig')->render('MedicineBundle:Sales:salesReverse.html.twig', array(
            'entity' => $sales,
            'config' => $sales->getMedicineConfig(),
        ));
        $em->getRepository('MedicineBundle:MedicineReverse')->insertMedicineSales($sales, $template);
        return $this->redirect($this->generateUrl('medicine_sales_edit',array('id' => $sales->getId())));
    }

    public function reverseShowAction($id)
    {
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entity = $this->getDoctrine()->getRepository('MedicineBundle:MedicineReverse')->findOneBy(array('medicineConfig' => $config, 'medicineSales' => $id));
        return $this->render('MedicineBundle:MedicineReverse:sales.html.twig', array(
            'entity' => $entity,
        ));

    }

}
