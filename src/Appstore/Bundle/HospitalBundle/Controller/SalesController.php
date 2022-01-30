<?php

namespace Appstore\Bundle\HospitalBundle\Controller;


use Appstore\Bundle\MedicineBundle\Entity\MedicineAndroidProcess;
use Appstore\Bundle\MedicineBundle\Entity\MedicineConfig;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchaseItem;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSales;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSalesItem;
use Appstore\Bundle\MedicineBundle\Entity\MedicineStock;
use Appstore\Bundle\HospitalBundle\Form\SalesItemType;
use Appstore\Bundle\HospitalBundle\Form\SalesType;
use Appstore\Bundle\MedicineBundle\Service\PosItemManager;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Mike42\Escpos\Printer;
use Proxies\__CG__\Appstore\Bundle\HospitalBundle\Entity\HospitalConfig;
use Proxies\__CG__\Appstore\Bundle\HospitalBundle\Entity\Invoice;
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
     * @Secure(roles="ROLE_DOMAIN_HOSPITAL_SALES")
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
        return $this->render('HospitalBundle:Sales:index.html.twig', array(
            'entities' => $pagination,
            'banks' => $banks,
            'mobiles' => $mobiles,
            'transactionMethods' => $transactionMethods,
            'searchForm' => $data,
        ));
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_HOSPITAL_SALES")
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
        return $this->render('HospitalBundle:Sales:hold.html.twig', array(
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
        return $this->render('HospitalBundle:Sales:salesItem.html.twig', array(
            'entities' => $pagination,
            'transactionMethods' => $transactionMethods,
            'searchForm' => $data,
        ));
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_HOSPITAL_SALES")
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
      //  $transactionMethod = $em->getRepository('SettingToolBundle:TransactionMethod')->find(1);
     //   $entity->setTransactionMethod($transactionMethod);
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('hms_sales_edit', array('id' => $entity->getId())));

    }

    /**
     * @Secure(roles="ROLE_DOMAIN_HOSPITAL_SALES")
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
        return $this->render('HospitalBundle:Sales:new.html.twig', array(
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
        $form = $this->createForm(new SalesType($globalOption), $entity, array(
            'action' => $this->generateUrl('hms_sales_update', array('id' => $entity->getId())),
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
            'action' => $this->generateUrl('hms_sales_item_add', array('invoice' => $entity->getId())),
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
        $hmsInvoice  = empty($data['hmsInvoice']) ? '' : $data['hmsInvoice'];
        $hmsConfig = $globalOption->getHospitalConfig();
        if ($editForm->isValid()) {
            if ($hmsInvoice > 0) {
                $invoice = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->findOneBy(array('hospitalConfig'=>$hmsConfig,'id'=>$hmsInvoice));
                $entity->setHmsInvoice($invoice->getInvoice());
                $entity->setCustomer($invoice->getCustomer());
            }
            $entity->setApprovedBy($this->getUser());
            $entity->setProcess('Done');
            if($entity->getInvoiceFor() == "customer"){
                if ($entity->getNetTotal() <= $entity->getReceived()) {
                    $entity->setReceived($entity->getNetTotal());
                    $entity->setDue(0);
                    $entity->setPaymentStatus('Paid');
                }else{
                    $entity->setPaymentStatus('Due');
                    $entity->setDue($entity->getNetTotal() - $entity->getReceived());
                }
            }else{
                $entity->setReceived($entity->getNetTotal());
                $entity->setDue(0);
                $entity->setPaymentStatus('Paid');
            }
            $accountConfig = $this->getUser()->getGlobalOption()->getAccountingConfig()->isAccountClose();
            if($accountConfig == 1){
                $datetime = new \DateTime("yesterday 23:30:30");
                $entity->setCreated($datetime);
                $entity->setUpdated($datetime);
            }
            $em->flush();
            if($entity->getProcess() == 'Done' and $entity->getInvoiceFor() == 'customer'){
                 $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->insertMedicineAccountInvoice($entity);
            }elseif($entity->getProcess() == 'Done' and $entity->getInvoiceFor() == 'expenditure'){
                $this->getDoctrine()->getRepository('AccountingBundle:AccountJournal')->insertPatientMedicine($entity);
            }
            return $this->redirect($this->generateUrl('hms_sales_show', array('id' => $entity->getId())));

        }
        return $this->render('HospitalBundle:Sales:new.html.twig', array(
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
        return $this->render('HospitalBundle:Sales:show.html.twig', array(
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
        return $this->render('HospitalBundle:Sales:print.html.twig', array(
            'entity'      => $entity,
            'previousDue'      => $previousDue,
        ));
    }


    /**
     * Finds and displays a Vendor entity.
     *
     */
    public function posPrintUltraAction($id)
    {
        $mode = $_REQUEST['mode'];
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
       // $path = "http://www.terminalbd.local/medicine/sales/$id/print";
       // shell_exec("wkhtmltoimage $html ~/Downloads/invoice2.png");
        if($mode == "print"){
            $print = $this->posMikePrint($entity);
        }else{
            $print = $this->renderView('HospitalBundle:Sales:posprint.html.twig', array(
                'entity'      => $entity,
                'previousDue'      => $previousDue,
            ));
        }
        return new Response($print);
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
     * @Secure(roles="ROLE_DOMAIN_HOSPITAL_SALES")
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
        //return $this->redirect($this->generateUrl('hms_sales'));
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

    public function invoiceSearchAction(Request $request)
    {
        $item = $_REQUEST['q'];
        if ($item) {
            $inventory = $this->getUser()->getGlobalOption()->getHospitalConfig();
            $item = $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->searchAutoComplete($item,$inventory);
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
        $template = $this->get('twig')->render('HospitalBundle:Sales:salesReverse.html.twig', array(
            'entity' => $sales,
            'config' => $sales->getMedicineConfig(),
        ));
        $em->getRepository('MedicineBundle:MedicineReverse')->insertMedicineSales($sales, $template);
        $em->getRepository('AccountingBundle:AccountJournal')->removePatientMedicine($sales);
        return $this->redirect($this->generateUrl('hms_sales_edit',array('id' => $sales->getId())));
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
        return $this->render('HospitalBundle:Sales:salesAndroid.html.twig', array(
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
        return $this->redirect($this->generateUrl('hms_sales'));
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
        return $this->redirect($this->generateUrl('hms_sales_android'));

    }

    private function posMikePrint(MedicineSales $entity)
    {

        $invoiceParticulars = $entity->getMedicineSalesItems();
        $connector = new \Mike42\Escpos\PrintConnectors\DummyPrintConnector();
        $printer = new Printer($connector);
        $printer -> initialize();

        $em = $this->getDoctrine()->getManager();
        $option = $this->getUser()->getGlobalOption();
        /* @var $config MedicineConfig */
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();

        $vatRegNo       = $config->getVatRegNo();
        $companyName    = $option->getName();
        // $mobile         = "Mobile -".$option->getMobile();
        $address        = $config->getAddress();
        $website        = $option->getDomain();
        $customer       = '';

        /** ===================Customer Information=================================== */

        $invoice            = $entity->getInvoice();
        $subTotal           = $entity->getSubTotal();
        $total              = $entity->getNetTotal();
        $discount           = $entity->getDiscount();
        $vat                = $entity->getVat();
        $due                = $entity->getDue();
        $payment            = $entity->getReceived();
        $transaction        = $entity->getTransactionMethod()->getName();
        $salesBy            = $entity->getSalesBy()->getProfile()->getName();
        $printMessage       = $config->getPrintFooterText();
        if($entity->getCustomer()->getName() != "Default"){
            $customer           = "Customer: {$entity->getCustomer()->getName()},Mobile: {$entity->getCustomer()->getMobile()}\n";
        }

        /** ===================Invoice Sales Item Information========================= */


        /* Date is kept the same for testing */
        $date = date('d-M-y h:i:s A');
        /* Name of shop */
        $printer -> setUnderline(Printer::UNDERLINE_NONE);
        $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> text($companyName."\n");
        $printer -> selectPrintMode();
        $printer -> text($address."\n");
        // $printer -> text($mobile."\n");
        $printer -> feed();
        /* Title of receipt */
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> setEmphasis(true);
        /*if(!empty($vatRegNo)){
            $printer -> text("Vat Reg No. ".$vatRegNo.".\n");
            $printer -> setEmphasis(false);
        }*/
        $discountPercent = $config->isPrintDiscountPercent();
        $prevDue = $config->isPrintPreviousDue();
        $transaction    = new PosItemManager('Payment Mode: '.$transaction,'','');
        $subTotal       = new PosItemManager('Sub Total: ','Tk.',number_format($subTotal));
        //  $vat            = new PosItemManager('Vat: ','Tk.',number_format($vat));
        if($discountPercent == 1 and $entity->getDiscountType() =="Percentage"){
            $percent = $entity->getDiscountCalculation();
            $discount       = new PosItemManager('Discount: ('.$percent.'%)','Tk.',number_format($discount));
        }else{
            $discount       = new PosItemManager('Discount: ','Tk.',number_format($discount));
        }
        $grandTotal     = new PosItemManager('Net Payable: ','Tk.',number_format($total));
        $previousDue = $this->getDoctrine()->getRepository("AccountingBundle:AccountSales")->customerSingleOutstanding($option,$entity->getCustomer());
        if($prevDue == 1 and $entity->getCustomer()->getName() != "Default"){
            $previous = ($previousDue - $entity->getDue());
            $previousBalance       = new PosItemManager('Previous Due: ','Tk.',number_format($previous));
        }
        $payment        = new PosItemManager('Paid: ','Tk.',number_format($payment));
        $due            = new PosItemManager('Due: ','Tk.',number_format($previousDue));

        /* Title of receipt */
        $printer -> feed();
        $printer -> setEmphasis(true);
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> text("Sales Memo No- {$entity->getInvoice()}\n");
        $printer -> setEmphasis(false);
        $printer -> setJustification(Printer::JUSTIFY_LEFT);
        if(!empty($customer)){
            $printer -> text($customer);
        }
        $printer -> feed();
        $printer -> setEmphasis(true);
        $printer->setFont(Printer::FONT_B);
        $printer -> text(new PosItemManager('Item Name', 'Qnt', 'Amount'));
        $printer -> text("---------------------------------------------------------------\n");
        $printer -> setEmphasis(false);
        $printer -> setUnderline(Printer::UNDERLINE_NONE);
        $printer -> setEmphasis(false);
        //$printer -> feed();
        $i = 1;
        if(!empty($invoiceParticulars)){
            /* @var $row MedicineSalesItem */
            foreach ($invoiceParticulars as $row){
                $qnt = sprintf("%s", str_pad($row->getQuantity(),2, '0', STR_PAD_LEFT));
                $printer -> text(new PosItemManager($i.'. '.$row->getMedicineStock()->getName(),$qnt,number_format($row->getSubTotal())));
                $i++;
            }
        }
        $printer -> text("---------------------------------------------------------------\n");
        //    $printer -> feed();
        $printer -> setUnderline(Printer::UNDERLINE_NONE);
        $printer -> setEmphasis(true);
        $printer -> text ( "\n" );
        //$printer -> setUnderline(Printer::UNDERLINE_DOUBLE);
        $printer -> text($subTotal);
        $printer -> setEmphasis(false);
        $printer -> setUnderline(Printer::UNDERLINE_NONE);
        /* if($vat){
             $printer -> setUnderline(Printer::UNDERLINE_SINGLE);
             $printer->text($vat);
             $printer->setEmphasis(false);
         }*/
        $printer -> text("---------------------------------------------------------------\n");
        $printer->text($discount);
        $printer -> setEmphasis(false);
        $printer -> text ( "\n" );

        $printer -> setEmphasis(false);
        $printer -> setUnderline(Printer::UNDERLINE_NONE);
        $printer -> text($grandTotal);
        $printer -> setEmphasis(false);
        $printer -> setUnderline(Printer::UNDERLINE_NONE);
       // $printer -> text($previousBalance);
        $printer -> setEmphasis(false);
        //  $printer -> setUnderline(Printer::UNDERLINE_DOUBLE);
        $printer -> setUnderline(Printer::UNDERLINE_NONE);
        $printer -> text($payment);
        $printer -> setEmphasis(true);
        if($previousDue > 0 and $entity->getCustomer()->getName() != "Default") {
            $printer->text($due);
        }
        //  $printer -> setUnderline(Printer::UNDERLINE_DOUBLE);
        $printer->text("\n");
        //$printer -> feed();
        //$printer->text($transaction);
        //$printer->selectPrintMode();
        /* Barcode Print */
        $printer -> feed();
        $printer -> setUnderline(Printer::UNDERLINE_NONE);
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> text("Sales By: ".$salesBy."\n");
        /*if($website){
            $printer -> text("Please visit www.".$website."\n");
        }*/
        $printer -> text($date . "\n");
        if($printMessage){
            $printer->text("{$printMessage}\n");
        }else{
            $printer->text("*Medicines once sold are not taken back*\n");
        }
        $response =  base64_encode($connector->getData());
        $printer -> close();
        return $response;
    }



}