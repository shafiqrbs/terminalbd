<?php

namespace Appstore\Bundle\MedicineBundle\Controller;


use Appstore\Bundle\MedicineBundle\Entity\MedicinePrepurchase;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePrepurchaseItem;
use Appstore\Bundle\MedicineBundle\Entity\MedicineStock;
use Appstore\Bundle\MedicineBundle\Entity\MedicineParticular;
use Appstore\Bundle\MedicineBundle\Entity\MedicineVendor;
use Appstore\Bundle\MedicineBundle\Form\MedicineStockItemType;
use Appstore\Bundle\MedicineBundle\Form\MedicineStockPreItemType;
use Appstore\Bundle\MedicineBundle\Form\PrepurchaseType;
use Appstore\Bundle\MedicineBundle\Form\PurchaseItemType;
use Appstore\Bundle\MedicineBundle\Form\PurchaseType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncode;

/**
 * Prepurchase controller.
 *
 */
class PrepurchaseController extends Controller
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
        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePrepurchase')->findWithSearch($config,$data);
        $pagination = $this->paginate($entities);
        return $this->render('MedicineBundle:Prepurchase:index.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
        ));
    }

    /**
     * Creates a new Vendor entity.
     *
     */
    public function createAction(Request $request) {

    	$data = explode( ',', $request->cookies->get( 'barcodes' ) );
  	    $em = $this->getDoctrine()->getManager();
	    if ( is_null( $data ) ) {
		    return $this->redirect( $this->generateUrl( 'medicine_stock_short_item' ) );
	    }
	    $em = $this->getDoctrine()->getManager();
	    $entity = new MedicinePrepurchase();
	    $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
	    $entity->setMedicineConfig($config);
	    $em->persist($entity);
	    $em->flush();
	    foreach ($data as $key => $value ){
		    $stock = $em->getRepository('MedicineBundle:MedicineStock')->find($value);
		    if(!empty($stock)){
			    $this->getDoctrine()->getRepository('MedicineBundle:MedicinePrepurchaseItem')->insertShortList($entity,$stock);
		    }
	    }
	    return $this->redirect($this->generateUrl('medicine_prepurchase_edit', array('id' => $entity->getId())));

    }


    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new MedicinePrepurchase();
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entity->setMedicineConfig($config);
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('medicine_prepurchase_edit', array('id' => $entity->getId())));

    }


    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entity = $em->getRepository('MedicineBundle:MedicinePrepurchase')->findOneBy(array('medicineConfig' => $config , 'id' => $id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $stockItemForm = $this->createStockItemForm(new MedicineStock(), $entity);
        $editForm = $this->createEditForm($entity);
        return $this->render('MedicineBundle:Prepurchase:new.html.twig', array(
            'entity' => $entity,

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
    private function createEditForm(MedicinePrepurchase $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new PrepurchaseType($globalOption), $entity, array(
            'action' => $this->generateUrl('medicine_prepurchase_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'id' => 'purchaseForm',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    private function createPurchaseItemForm(MedicinePrepurchaseItem $purchaseItem , MedicinePrepurchase $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new PurchaseItemType($globalOption), $purchaseItem, array(
            'action' => $this->generateUrl('medicine_prepurchase_particular_add', array('invoice' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'id' => 'purchaseItemForm',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    private function createStockItemForm(MedicineStock $entity, MedicinePrepurchase $purchase )
    {
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $form = $this->createForm(new MedicineStockPreItemType($config), $entity, array(
            'action' => $this->generateUrl('medicine_prestock_item_create', array('id' => $purchase->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'id' => 'stockItemForm',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    public function particularSearchAction(MedicineStock $particular)
    {
        $unit = !empty($particular->getUnit()->getName())?$particular->getUnit()->getName():'Pack';
        return new Response(json_encode(array('purchasePrice'=> '', 'salesPrice'=> '','quantity'=> 1,'unit' => $unit)));
    }

    public function stockItemCreateAction(Request $request,MedicinePrepurchase $purchase)
    {
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $entity = new MedicineStock();
        $form = $this->createStockItemForm($entity, $purchase);
        $form->handleRequest($request);
        $medicineName = $data['medicineStock']['name'];
        if(empty($data['medicineId'])) {
            $checkStockMedicine = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->checkDuplicateStockNonMedicine($config,$medicineName);
        }else{
            $medicine = $this->getDoctrine()->getRepository('MedicineBundle:MedicineBrand')->find($data['medicineId']);
            $checkStockMedicine = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->checkDuplicateStockMedicine($config, $medicine);
        }
        if (empty($checkStockMedicine)){
            $entity->setMedicineConfig($config);
            if(empty($data['medicineId'])){
                if($entity->getAccessoriesBrand()) {
                    $brand = $entity->getAccessoriesBrand();
                    $entity->setBrandName($brand->getName());
                }
            }else{
                $entity->setMedicineBrand($medicine);
                $name = $medicine->getMedicineForm().' '.$medicine->getName().' '.$medicine->getStrength();
                $entity->setName($name);
                $entity->setBrandName($medicine->getMedicineCompany()->getName());
                $entity->setMode('medicine');
            }
	        $entity->setPurchaseQuantity(0);
            $em->persist($entity);
            $em->persist($entity);
            $em->flush();
            $this->getDoctrine()->getRepository('MedicineBundle:MedicinePrepurchaseItem')->insertStockPurchaseItems($purchase, $entity, $data);
            $invoice = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePrepurchase')->updatePurchaseTotalPrice($purchase);
            $msg = 'Medicine added successfully';
            $result = $this->returnResultData($invoice, $msg);
            return new Response(json_encode($result));

        }else{

	        $this->getDoctrine()->getRepository('MedicineBundle:MedicinePrepurchaseItem')->insertStockPurchaseItems($purchase, $checkStockMedicine, $data);
	        $invoice = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePrepurchase')->updatePurchaseTotalPrice($purchase);
	        $msg = 'Medicine added successfully';
	        $result = $this->returnResultData($invoice, $msg);
	        return new Response(json_encode($result));
        }
        exit;
    }

    public function purchaseItemUpdateAction(Request $request)
    {

        $data = $request->request->all();
        $purchase = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePrepurchaseItem')->updatePurchaseItem($data);
        $invoice = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePrepurchase')->updatePurchaseTotalPrice($purchase);
        $result = $this->returnResultData($invoice);
        return new Response(json_encode($result));
    }

    public function returnResultData(MedicinePrepurchase $entity,$msg=''){

        $invoiceParticulars = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePrepurchaseItem')->getPurchaseItems($entity);
        $subTotal   = $entity->getSubTotal() > 0 ? $entity->getSubTotal() : 0;
        $netTotal   = $entity->getNetTotal() > 0 ? $entity->getNetTotal() : 0;
        $payment    = $entity->getPayment() > 0 ? $entity->getPayment() : 0;
        $due        = $entity->getDue();
        $discount   = $entity->getDiscount() > 0 ? $entity->getDiscount() : 0;
        $data = array(
            'msg'                   => $msg,
            'subTotal'              => $subTotal,
            'netTotal'              => $netTotal,
            'payment'               => $payment ,
            'due'                   => $due,
            'discount'              => $discount,
            'invoiceParticulars'    => $invoiceParticulars ,
            'success'               => 'success'
        );
        return $data;

    }


    public function invoiceParticularDeleteAction(MedicinePrepurchase $invoice, MedicinePrepurchaseItem $particular){

        $stock = $particular->getMedicineStock();
        $em = $this->getDoctrine()->getManager();
        if (!$particular) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }
        $em->remove($particular);
        $em->flush();
        $invoice = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePrepurchase')->updatePurchaseTotalPrice($invoice);
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
        $invoice = $request->request->get('purchase');
        $entity = $em->getRepository('MedicineBundle:MedicinePrepurchase')->find($invoice);
        $subTotal = $entity->getSubTotal();
        if($discountType == 'flat'){
            $total = ($subTotal  - $discountCal);
            $discount = $discountCal;
        }else{
            $discount = ($subTotal * $discountCal)/100;
            $total = ($subTotal  - $discount);
        }
        $vat = 0;
        if($total > $discount ){
            $entity->setDiscountType($discountType);
            $entity->setDiscountCalculation($discountCal);
            $entity->setDiscount(round($discount));
            $entity->setNetTotal(round($total + $vat));
            $entity->setDue(round($total + $vat));
            $em->flush();
        }

        $result = $this->returnResultData($entity);
        return new Response(json_encode($result));
        exit;
    }

    public function updateAction(Request $request, MedicinePrepurchase $entity)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            $entity->setProcess('Complete');
            $entity->setDue($entity->getNetTotal() - $entity->getPayment());
            $em->flush();
            return $this->redirect($this->generateUrl('medicine_prepurchase_show', array('id' => $entity->getId())));
        }
        $purchaseItemForm = $this->createPurchaseItemForm(new MedicinePrepurchaseItem() , $entity);
        return $this->render('MedicineBundle:Prepurchase:new.html.twig', array(
            'entity' => $entity,
            'purchaseItem' => $purchaseItemForm->createView(),
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

	    $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
	    $entity = $em->getRepository('MedicineBundle:MedicinePrepurchase')->findOneBy(array('medicineConfig' => $config , 'id' => $id));


	    if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendor entity.');
        }
        return $this->render('MedicineBundle:Prepurchase:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    public function approvedAction($id)
    {
        $em = $this->getDoctrine()->getManager();
	    $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
	    $purchase = $em->getRepository('MedicineBundle:MedicinePrepurchase')->findOneBy(array('medicineConfig' => $config , 'id' => $id));
	    if (!empty($purchase) and $purchase->getProcess() == "Complete" ) {
            $purchase->setProcess('Approved');
            $em->flush();
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
    public function deleteAction(MedicinePrepurchase $entity)
    {

        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendor entity.');
        }
        $em->createQuery('DELETE MedicineBundle:MedicinePrepurchaseItem e WHERE e.medicinePurchase = '.$entity->getId());
        $em->remove($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('medicine_prepurchase'));
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
        $entity = $em->getRepository('MedicineBundle:MedicinePrepurchaseItem')->find($data['pk']);
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
            $entity->setActualPurchasePrice((float)$data['value']);
            $entity->setPurchaseSubTotal((float)$data['value'] * $entity->getQuantity());
            $em->flush();
            $this->getDoctrine()->getRepository('MedicineBundle:MedicinePrepurchase')->updatePurchaseTotalPrice($entity->getMedicinePrepurchase());
        }

	    if($data['name'] == 'expirationDate' and !empty($data['value'])){
		    $expirationEndDate = $data['value'];
		    $expirationEndDate = (new \DateTime($expirationEndDate));
		    $entity->setExpirationStartDate($expirationEndDate);
		    $entity->setExpirationEndDate($expirationEndDate);
		    $em->flush();
	    }

        $salesQnt = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesItem')->salesPurchaseStockItemUpdate($entity);
        if($data['name'] == 'Quantity' and $salesQnt <= (int)$data['value']){
            $entity->setQuantity((int)$data['value']);
            $entity->setRemainingQuantity((int)$data['value']);
            $entity->setPurchaseSubTotal((int)$data['value'] * $entity->getActualPurchasePrice());
            $em->flush();
            $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->updateRemovePurchaseQuantity($entity->getMedicineStock());
            $this->getDoctrine()->getRepository('MedicineBundle:MedicinePrepurchase')->updatePurchaseTotalPrice($entity->getMedicinePrepurchase());
        }
        exit;
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

    public function reverseAction(MedicinePrepurchase $purchase)
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
        ignore_user_abort(true);

        $em = $this->getDoctrine()->getManager();
        if($purchase->getAsInvestment() == 1 ) {
            $this->getDoctrine()->getRepository('AccountingBundle:AccountJournal')->removeApprovedMedicinePrepurchaseJournal($purchase);
        }
        $this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->accountMedicinePrepurchaseReverse($purchase);
        $purchase->setRevised(true);
        $purchase->setProcess('Created');
        $em->flush();
        $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->getPurchaseUpdateQnt($purchase);
        $template = $this->get('twig')->render('MedicineBundle:Prepurchase:purchaseReverse.html.twig', array(
            'entity' => $purchase,
            'config' => $purchase->getMedicineConfig(),
        ));
        $em->getRepository('MedicineBundle:MedicineReverse')->purchase($purchase, $template);
        return $this->redirect($this->generateUrl('medicine_prepurchase_edit',array('id' => $purchase->getId())));
    }

    public function reverseShowAction($id)
    {
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entity = $this->getDoctrine()->getRepository('MedicineBundle:MedicineReverse')->findOneBy(array('medicineConfig' => $config, 'medicinePurchase' => $id));
        return $this->render('MedicineBundle:MedicineReverse:purchase.html.twig', array(
            'entity' => $entity,
        ));

    }


	public function vendorMergeAction(MedicineVendor $vendor)
    {
        set_time_limit(0);
        ignore_user_abort(true);

        $em = $this->getDoctrine()->getManager();
        $entity = new MedicinePrepurchase();
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entity->setMedicineConfig($config);
        $entity->setMedicineVendor($vendor);
        $entity->setCreatedBy($this->getUser());
        $receiveDate = new \DateTime('now');
        $entity->setReceiveDate($receiveDate);
        $transactionMethod = $em->getRepository('SettingToolBundle:TransactionMethod')->find(1);
        $entity->setTransactionMethod($transactionMethod);
        $em->persist($entity);
        $em->flush();

        $this->getDoctrine()->getRepository('MedicineBundle:MedicinePrepurchaseItem')->mergePurchaseItem($entity,$vendor);

        return $this->redirect($this->generateUrl('medicine_prepurchase_edit', array('id' => $entity->getId())));

    }



}
