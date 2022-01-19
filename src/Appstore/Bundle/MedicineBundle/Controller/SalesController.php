<?php

namespace Appstore\Bundle\MedicineBundle\Controller;


use Appstore\Bundle\MedicineBundle\Entity\MedicineAndroidProcess;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchaseItem;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSales;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSalesItem;
use Appstore\Bundle\MedicineBundle\Entity\MedicineStock;
use Appstore\Bundle\MedicineBundle\Form\SalesItemType;
use Appstore\Bundle\MedicineBundle\Form\SalesType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


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
     * @Secure(roles="ROLE_MEDICINE_SALES")
     */

    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->invoiceLists($this->getUser(),$data);
        $pagination = $this->paginate($entities);
        $transactionMethods = $em->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status' => 1), array('name' => 'ASC'));
        $banks = $this->getDoctrine()->getRepository('AccountingBundle:AccountBank')->findBy(array('globalOption' => $user->getGlobalOption(),'status' => 1), array('name' => 'ASC'));
        $mobiles =  $this->getDoctrine()->getRepository('AccountingBundle:AccountMobileBank')->findBy(array('globalOption' => $user->getGlobalOption() , 'status' => 1), array('name' => 'ASC'));
        return $this->render('MedicineBundle:Sales:index.html.twig', array(
            'entities' => $pagination,
            'banks' => $banks,
            'mobiles' => $mobiles,
            'transactionMethods' => $transactionMethods,
            'searchForm' => $data,
        ));
    }

    /**
     * @Secure(roles="ROLE_MEDICINE_SALES")
     */

    public function holdAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $user = $this->getUser();
        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->invoiceLists($this->getUser(),$data,"Hold");
        $pagination = $this->paginate($entities);
        $transactionMethods = $em->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status' => 1), array('name' => 'ASC'));
        $banks = $this->getDoctrine()->getRepository('AccountingBundle:AccountBank')->findBy(array('globalOption' => $user->getGlobalOption(),'status' => 1), array('name' => 'ASC'));
        $mobiles =  $this->getDoctrine()->getRepository('AccountingBundle:AccountMobileBank')->findBy(array('globalOption' => $user->getGlobalOption() , 'status' => 1), array('name' => 'ASC'));
        return $this->render('MedicineBundle:Sales:hold.html.twig', array(
            'entities' => $pagination,
            'banks' => $banks,
            'mobiles' => $mobiles,
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

    /**
     * @Secure(roles="ROLE_MEDICINE_SALES")
     */

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

    /**
     * @Secure(roles="ROLE_MEDICINE_SALES")
     */

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

                if(!empty($item->getExpirationEndDate())){
                    $expirationEndDate = $item->getExpirationEndDate()->format('d-m-y');
                    $expiration = $expirationEndDate;
                }else{
                    $expiration='Expiry Empty';
                }
                $purchaseItems .= '<option value="' . $item->getId() . '">' . $item->getBarcode() . ' - ' . $expiration . '[' . $item->getRemainingQuantity() . '] - PP Tk.'.$item->getPurchasePrice().'</option>';
            }
        }
        return new Response(json_encode(array('purchaseItems' => $purchaseItems , 'salesPrice' => $stock->getSalesPrice())));
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
        $itemPercent = ($data['salesitem']['itemPercent']);
        $salesPrice = ($data['salesitem']['salesPrice']);
        $stock = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->find($stockItem);
        $entity->setMedicineStock($stock);
        if($itemPercent > 0){
            $initialDiscount = round(($salesPrice *  $itemPercent)/100);
            $initialGrandTotal = round($salesPrice  - $initialDiscount);
            $entity->setSalesPrice( round( $initialGrandTotal, 2 ) );
        }else{
            $entity->setSalesPrice( round( $salesPrice, 2 ) );
        }
        $entity->setSubTotal($entity->getSalesPrice() * $entity->getQuantity());
        $entity->setMrpPrice($stock->getSalesPrice());
        $entity->setPurchasePrice($stock->getAveragePurchasePrice());
        $em->persist($entity);
        $em->flush();
        $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->updateRemovePurchaseQuantity($stock,'sales');
        $invoice = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->updateMedicineSalesTotalPrice($invoice);
        $msg = 'Medicine added successfully';
        $result = $this->returnResultData($invoice,$msg);
        return new Response(json_encode($result));

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
        $salesItem->setPurchasePrice($item->getMedicineStock()->getAveragePurchasePrice());
        $em->persist($salesItem);
        $em->flush();
        $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseItem')->updateRemovePurchaseItemQuantity($item,'sales');
        $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->updateRemovePurchaseQuantity($item->getMedicineStock(),'sales');
        $invoice = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->updateMedicineSalesTotalPrice($sales);
        $msg = 'Medicine added successfully';
        $result = $this->returnResultData($invoice,$msg);
        return new Response(json_encode($result));


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
            $accountConfig = $this->getUser()->getGlobalOption()->getAccountingConfig()->isAccountClose();
            if($accountConfig == 1){
                $datetime = new \DateTime("yesterday 23:30:30");
                $entity->setCreated($datetime);
                $entity->setUpdated($datetime);
            }
            $em->flush();
            if($entity->getProcess() == 'Done'){
                 $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->insertMedicineAccountInvoice($entity);
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


    /**
     * Finds and displays a Vendor entity.
     *
     */
    public function posPrintAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('MedicineBundle:MedicineSales')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendor entity.');
        }
        $option = $this->getUser()->getGlobalOption();
        $previousDue = 0;
        if($entity->getCustomer()->getName() != "Default"){
            $previousDue = $this->getDoctrine()->getRepository("AccountingBundle:AccountSales")->customerSingleOutstanding($option,$entity->getCustomer());
        }
        return $this->render('MedicineBundle:Sales:print.html.twig', array(
            'entity'      => $entity,
            'previousDue'      => $previousDue,
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
            $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->insertMedicineAccountInvoice($sales);
            return new Response('success');
        } else {
            return new Response('failed');
        }
    }

    /**
     * @Secure(roles="ROLE_MEDICINE_SALES")
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
        if($invoice->getMedicineSalesItems()){
            foreach ($invoice->getMedicineSalesItems() as $particular){

                $item = $particular->getMedicinePurchaseItem();
                $stock = $particular->getMedicineStock();
                $this->get('session')->set('item', $item);
                $this->get('session')->set('stock', $stock);
                $em->remove($particular);
                $em->flush();
                $item = $this->get('session')->get('item');
                $stock = $this->get('session')->get('stock');
                if($item){
                    $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseItem')->updateRemovePurchaseItemQuantity($item,'sales');
                }
                if($stock){
                    $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->updateRemovePurchaseQuantity($stock,'sales');
                }

            }
        }

	}

    public function autoSearchAction(Request $request)
    {
        $item = $_REQUEST['q'];
        if ($item) {
            $inventory = $this->getUser()->getGlobalOption()->getMedicineConfig();
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


    public function androidSalesAction()
    {
        $conf = $this->getUser()->getGlobalOption()->getMedicineConfig()->getId();
        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineAndroidProcess')->getAndroidSalesList($conf,"sales");
        $pagination = $this->paginate($entities);
        $sales = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->findAndroidDeviceSales($pagination);
        return $this->render('MedicineBundle:Sales:salesAndroid.html.twig', array(
            'entities' => $pagination,
            'sales' => $sales,
        ));
    }

    public function androidSalesProcessAction($device)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->androidDeviceSalesProcess($device);
        exit;
    }

    public function insertGroupApiSalesImportAction(MedicineAndroidProcess $android)
    {
        $msg = "invalid";
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();

        $removeSales = $em->createQuery("DELETE MedicineBundle:MedicineSales e WHERE e.androidProcess= {$android->getId()}");
        if(!empty($removeSales)){
            $removeSales->execute();
        }
        $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->insertApiSales($config->getGlobalOption(),$android);

        /* @var $sales MedicineSales */

        $salses = $this->getDoctrine()->getRepository("MedicineBundle:MedicineSales")->findBy(array('androidProcess' => $android));
        foreach ($salses as $sales){
            if($sales->getProcess() == "Device"){
                $sales->setProcess('Done');
                $sales->setUpdated($sales->getCreated());
                $sales->setApprovedBy($this->getUser());
                $em->flush();
                $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->insertMedicineAccountInvoice($sales);
                $msg = "valid";
            }
        }
        if($msg == "valid"){
            $android->setStatus(true);
            $em->persist($android);
            $em->flush();
            $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->updateApiSalesPurchasePrice($android->getId());
        }
        if($msg == "valid"){
            return new Response('success');
        }else{
            return new Response('failed');
	    }
    }

    public function groupReverseAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $data = array('startDate' => '2019-07-04','endateDate' => '2019-07-04');
        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->salesReverseMigration($config->getId(),$data);
        /* @var $sales MedicineSales */
        foreach ( $entities as $sales):
            $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->accountMedicineSalesReverse($sales);
            $sales->setRevised(true);
            $sales->setProcess('Complete');
            $em->flush();
        endforeach;
        exit;
        return $this->redirect($this->generateUrl('medicine_sales'));
    }

    public function groupApprovedAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $data = array('startDate' => '2019-07-04','endateDate' => '2019-07-04');
        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->salesReverseMigration($config,$data);
        /* @var $sales MedicineSales */
        foreach ( $entities as $sales):
            if (!empty($sales) and $sales->getProcess() == "Complete" ) {
                $sales->setProcess('Done');
                $sales->setUpdated($sales->getCreated());
                $sales->setApprovedBy($this->getUser());
                $em->flush();
                $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->insertMedicineAccountInvoice($sales);
            }
        endforeach;
        exit;

    }

    public function androidDuplicateSalesDeleteAction(MedicineAndroidProcess $android)
    {
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $this->getDoctrine()->getRepository('MedicineBundle:MedicineSales')->androidDuplicateSalesDelete($config,$android);
        return $this->redirect($this->generateUrl('medicine_sales_android'));

    }



}
