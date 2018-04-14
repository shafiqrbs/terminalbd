<?php

namespace Appstore\Bundle\InventoryBundle\Controller;

use Appstore\Bundle\AccountingBundle\Entity\AccountPurchase;
use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Appstore\Bundle\InventoryBundle\Entity\Item;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseItem;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseVendorItem;
use Appstore\Bundle\InventoryBundle\Form\ItemType;
use Appstore\Bundle\InventoryBundle\Form\PurchaseApproveType;
use Appstore\Bundle\InventoryBundle\Form\PurchaseItemSimpleType;
use Appstore\Bundle\InventoryBundle\Form\PurchaseItemType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\InventoryBundle\Entity\Purchase;
use Appstore\Bundle\InventoryBundle\Form\PurchaseType;
use Symfony\Component\HttpFoundation\Response;

/**
 * PurchaseOrder controller.
 *
 */
class PurchaseSimpleController extends Controller
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
     * Lists all PurchaseOrder entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities = $em->getRepository('InventoryBundle:Purchase')->findWithSearch($inventory,$data);
        $purchaseOverview = $this->getDoctrine()->getRepository('InventoryBundle:Purchase')->purchaseOverview($inventory,$data);
        $pagination = $this->paginate($entities);

        return $this->render('InventoryBundle:PurchaseSimple:index.html.twig', array(
            'entities' => $pagination,
            'purchaseOverview' => $purchaseOverview,
            'searchForm' => $data
        ));
    }


    /**
     * Displays a form to create a new Purchase entity.
     *
     */
    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new Purchase();
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entity->setInventoryConfig($inventory);
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('inventory_purchasesimple_edit', array('id' => $entity->getId())));

    }

    /**
     * Finds and displays a Purchase entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('InventoryBundle:Purchase')->find($id);
        $processItem = $this->getDoctrine()->getRepository('InventoryBundle:StockItem')->getPurchaseItemSalesQuantity($entity,array('sales','damage','purchaseReturn'));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Purchase entity.');
        }
        return $this->render('InventoryBundle:PurchaseSimple:show.html.twig', array(
            'entity'      => $entity,
            'processItem'      => $processItem,
        ));
    }

    /**
     * Displays a form to edit an existing Purchase entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $purchase = $em->getRepository('InventoryBundle:Purchase')->find($id);
        $processItem = $this->getDoctrine()->getRepository('InventoryBundle:StockItem')->getPurchaseItemSalesQuantity($purchase,array('sales','damage','purchaseReturn'));
        if (!$purchase) {
            throw $this->createNotFoundException('Unable to find Purchase entity.');
        }
        $purchaseItem = new PurchaseItem();
        $purchaseItemForm = $this->createPurchaseItemForm($purchaseItem,$purchase);
        $editForm = $this->createEditForm($purchase);
        $em->getRepository('InventoryBundle:Purchase')->purchaseSimpleUpdate($purchase);
        return $this->render('InventoryBundle:PurchaseSimple:new.html.twig', array(
            'entity'      => $purchase,
            'processItem'      => $processItem,
            'purchaseItemForm'   => $purchaseItemForm->createView(),
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Purchase entity.
    *
    * @param Purchase $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Purchase $entity)
    {
        $inventoryConfig =  $this->getUser()->getGlobalOption()->getInventoryConfig();
        $form = $this->createForm(new PurchaseType($inventoryConfig), $entity, array(
            'action' => $this->generateUrl('inventory_purchasesimple_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'id' => 'purchaseForm',
                'class' => 'horizontal-form purchase',
                'novalidate' => 'novalidate',
            )

        ));
        return $form;
    }

    /**
    * Creates a form to edit a Purchase entity.
    *
    * @param Purchase $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createPurchaseItemForm(PurchaseItem $purchaseItem , Purchase $entity)
    {
        $inventoryConfig =  $this->getUser()->getGlobalOption()->getInventoryConfig();
        $form = $this->createForm(new PurchaseItemSimpleType($inventoryConfig), $purchaseItem, array(
            'action' => $this->generateUrl('inventory_purchasesimple_create', array('purchase' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'id' => 'purchaseItemForm',
                'class' => 'horizontal-form purchase',
                'novalidate' => 'novalidate',
            )

        ));
        return $form;
    }

    /**
     * Edits an existing Purchase entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:Purchase')->find($id);
        $editForm = $this->createEditForm($entity);
        $purchaseItem = new PurchaseItem();
        $em->getRepository('InventoryBundle:PurchaseItem')->generatePurchaseVendorItem($entity);
        $purchaseItemForm = $this->createPurchaseItemForm($purchaseItem,$entity);
        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            $entity->setDueAmount($entity->getTotalAmount() - $entity->getPaymentAmount());
            $entity->upload();
            $em->flush();
            if($entity->getProcess() == 'approved' ){
                $this->approveAction($entity);
                return $this->redirect($this->generateUrl('inventory_purchasesimple_show', array('id' => $id)));
            }elseif($entity->getProcess() == 'complete' ){
                return $this->redirect($this->generateUrl('inventory_purchasesimple_edit', array('id' => $id)));
            }else{
                return $this->redirect($this->generateUrl('inventory_purchasesimple', array('id' => $id)));
            }
        }
        return $this->render('InventoryBundle:PurchaseSimple:new.html.twig', array(
            'entity'      => $entity,
            'purchaseItemForm'   => $purchaseItemForm->createView(),
            'form'   => $editForm->createView(),

        ));
    }

    public function approveAction(Purchase $purchase)
    {

        set_time_limit(0);
        $em = $this->getDoctrine()->getManager();
        $purchase->setApprovedBy($this->getUser());
        $purchase->setProcess('approved');
        $em->persist($purchase);
        $em->flush();
        $em->getRepository('InventoryBundle:PurchaseItem')->generatePurchaseVendorItem($purchase);
        $em->getRepository('InventoryBundle:StockItem')->insertPurchaseStockItem($purchase);
        $em->getRepository('InventoryBundle:Item')->getItemUpdatePriceQnt($purchase);
        if($purchase->getAsInvestment() == 1){
            $journal = $em->getRepository('AccountingBundle:AccountJournal')->insertAccountPurchaseJournal($purchase);
            $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->insertAccountCash($journal,'Journal');
            $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->insertAccountJournalTransaction($journal);
        }
        $accountPurchase = $em->getRepository('AccountingBundle:AccountPurchase')->insertAccountPurchase($purchase);
        $em->getRepository('AccountingBundle:Transaction')->purchaseTransaction($purchase,$accountPurchase);
        return new Response(json_encode(array('success'=>'success')));

    }

    /**
     * Deletes a Purchase entity.
     *
     */
    public function deleteAction(Purchase $purchase)
    {
        if($purchase){
            $em = $this->getDoctrine()->getManager();
            $em->remove($purchase);
            $em->flush();
            return new Response('success');
        }else{
            return new Response('failed');
        }

        exit;
    }


    public function  updatePurchaseStatus(Purchase $entity,$process){

        $em = $this->getDoctrine()->getManager();
        $entity->setProcess($process);
        $em->persist($entity);
        $em->flush();
    }

    public function autoSearchAction(Request $request)
    {
        $item = $_REQUEST['q'];
        if ($item) {
            $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
            $item = $this->getDoctrine()->getRepository('InventoryBundle:Purchase')->searchAutoComplete($inventory,$item);
        }
        return new JsonResponse($item);
    }

    public function searchNameAction($grn)
    {
        return new JsonResponse(array(
            'id'=> $grn,
            'text'=> $grn
        ));
    }


    /**
     * Displays a form to edit an existing Purchase entity.
     *
     */
    public function editApproveAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:Purchase')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Purchase entity.');
        }
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $em->getRepository('InventoryBundle:Purchase')->getSumPurchase($this->getUser(),$inventory);
        $editForm = $this->createEditApproveForm($entity);

        return $this->render('InventoryBundle:PurchaseSimple:editApprove.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Purchase entity.
     *
     * @param Purchase $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditApproveForm(Purchase $entity)
    {
        $inventoryConfig =  $this->getUser()->getGlobalOption()->getInventoryConfig();
        $form = $this->createForm(new PurchaseApproveType($inventoryConfig), $entity, array(
            'action' => $this->generateUrl('inventory_purchasesimple_update_approve', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form purchase',
                'novalidate' => 'novalidate',
                'id' => 'purchaseForm',
            )

        ));
        return $form;
    }

    /**
     * Edits an existing Purchase entity.
     *
     */
    public function updateApproveAction(Request $request, $id)
    {
        set_time_limit(0);
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:Purchase')->find($id);
        $editForm = $this->createEditApproveForm($entity);
        $editForm->handleRequest($request);


        if ($editForm->isValid()) {

            $entity->setApprovedBy($this->getUser());
            $entity->setProcess('approved');
            $due = $entity->getTotalAmount() - ($entity->getPaymentAmount() + $entity->getCommissionAmount());
            $entity->setDueAmount($due);
            $em->flush();
            $em->getRepository('InventoryBundle:Item')->getItemUpdatePriceQnt($entity);
            if($entity->getAsInvestment() == 1){
                $journal = $em->getRepository('AccountingBundle:AccountJournal')->insertAccountPurchaseJournal($entity);
                $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->insertAccountCash($journal,'Journal');
                $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->insertAccountJournalTransaction($journal);
            }
            $accountPurchase = $em->getRepository('AccountingBundle:AccountPurchase')->insertAccountPurchase($entity);
            $em->getRepository('AccountingBundle:Transaction')->purchaseTransaction($entity,$accountPurchase);
            $this->get('session')->getFlashBag()->add(
                'success', "Purchase invoice approved successfully"
            );
            return $this->redirect($this->generateUrl('inventory_purchasesimple'));
        }

        return $this->render('InventoryBundle:PurchaseSimple:editApprove.html.twig', array(
            'entity'        => $entity,
            'form'          => $editForm->createView(),

        ));

    }



    public function approvedPurchaseDeletedAction(Purchase $purchase)
    {

        set_time_limit(0);
        $em = $this->getDoctrine()->getManager();

        $this->getDoctrine()->getRepository('InventoryBundle:Item')->purchaseItemReverseUpdateQnt($purchase);
        $this->getDoctrine()->getRepository('InventoryBundle:StockItem')->purchaseItemStockRemoveQnt($purchase);
        $this->getDoctrine()->getRepository('AccountingBundle:AccountJournal')->removeApprovedPurchaseJournal($purchase);
        $this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->removeApprovedAccountPurchase($purchase);
        $em->remove($purchase);
        $em->flush();
        return $this->redirect($this->generateUrl('purchase'));

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
         *
         * */
    }


    /**
     * Creates a new Purchase entity.
     *
     */
    public function createPurchaseItemAction(Request $request, Purchase $purchase)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $purchaseItem = new PurchaseItem();
        $purchaseItemForm = $this->createPurchaseItemForm($purchaseItem,$purchase);
        $editForm = $this->createEditForm($purchase);
        $purchaseItemForm->handleRequest($request);
        if ($purchaseItemForm->isValid()) {
            $purchaseItem->setPurchase($purchase);
            $purchaseItem->setName($purchaseItem->getItem()->getName());
            $purchaseSubTotal = ($purchaseItem->getQuantity() * $purchaseItem->getPurchasePrice());
            $purchaseItem->setPurchaseSubTotal($purchaseSubTotal);
            $salesSubTotal = ($purchaseItem->getQuantity() * $purchaseItem->getSalesPrice());
            $purchaseItem->setSalesSubTotal($salesSubTotal);
            $em->persist($purchaseItem);
            $em->flush();
            $this->getDoctrine()->getRepository('InventoryBundle:PurchaseItem')->generatePurchaseVendorItem($purchase);
            $em->getRepository('InventoryBundle:Purchase')->purchaseSimpleUpdate($purchase);
            return $this->redirect($this->generateUrl('inventory_purchasesimple_edit', array('id' => $purchase->getId())));
        }

        return $this->render('InventoryBundle:PurchaseSimple:new.html.twig', array(
            'entity' => $purchase,
            'form'   => $editForm->createView(),
            'purchaseItemForm'   => $purchaseItemForm->createView(),
        ));

    }

    public function purchaseItemDeleteAction(Purchase $purchase, PurchaseItem $purchaseItem)
    {
        $em = $this->getDoctrine()->getManager();
        $salesQnt = $this->getDoctrine()->getRepository('InventoryBundle:StockItem')->getPurchaseItemQuantity($purchaseItem, array('sales','damage','purchaseReturn'));
        if($purchaseItem and $salesQnt == 0 ) {
            $em->remove($purchaseItem);
            $em->flush();
            $em->getRepository('InventoryBundle:Purchase')->purchaseSimpleUpdate($purchase);
            $this->getDoctrine()->getRepository('InventoryBundle:PurchaseItem')->generatePurchaseVendorItem($purchase);
            $this->get('session')->getFlashBag()->add(
                'error', "Data has been deleted successfully"
            );
        }
        exit;

    }


    public function createStockItemAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();

        /* @var InventoryConfig $inventory*/

        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        if(!empty($data['masterItem'])){

            $entity = new Item();
            $existMasterItem = $em->getRepository('InventoryBundle:Product')->findOneBy(array('name'=> $data['masterItem']));
            if(empty($existMasterItem)){
                $existMasterItem = $em->getRepository('InventoryBundle:Product')->createNewProduct($inventory,$data['masterItem']);
            }
            $checkData = $this->getDoctrine()->getRepository('InventoryBundle:Item')->checkInstantDuplicateSKU($inventory,$data);
            if($checkData == 0 ) {
                $entity->setInventoryConfig($inventory);
                $entity->setMasterItem($existMasterItem);
                $entity->setName($existMasterItem->getName());
                if($inventory->getIsColor() == 1) {
                    $color = $em->getRepository('InventoryBundle:ItemColor')->findOneBy(array('name'=> $data['color'] ));
                    $entity->setColor($color);
                }
                if($inventory->getIsSize() == 1) {
                    $size = $em->getRepository('InventoryBundle:ItemSize')->findOneBy(array('name'=> $data['size'] ));
                    $entity->setSize($size);
                }
                if($inventory->getIsBrand() == 1) {
                    $brand = $em->getRepository('InventoryBundle:ItemBrand')->findOneBy(array('name'=> $data['brand'] ));
                    $entity->setBrand($brand);
                }
                if($inventory->getIsVendor() == 1) {
                    $vendorName = $data['vendor'];
                    $vendor = $em->getRepository('InventoryBundle:Vendor')->findOneBy(array('companyName'=> $vendorName));
                    $entity->setVendor($vendor);
                }

                $em->persist($entity);
                $em->flush();
                $msg = "Item has been added successfully";
                $status ='valid';
            }else{
                $status ='invalid';
                $msg = "Item already exist, Please change add another item name";

            }
            return new Response(json_encode(array('status' => $status,'message' => $msg)));

        }
        exit;

    }

    public function reverseAction(Purchase $purchase)
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
         *
         * */

        set_time_limit(0);
        $em = $this->getDoctrine()->getManager();
        $this->getDoctrine()->getRepository('InventoryBundle:StockItem')->purchaseItemStockRemoveQnt($purchase);
        $this->getDoctrine()->getRepository('InventoryBundle:Item')->purchaseItemReverseUpdateQnt($purchase);
        if($purchase->getAsInvestment() == 1 ) {
            $this->getDoctrine()->getRepository('AccountingBundle:AccountJournal')->removeApprovedPurchaseJournal($purchase);
        }
        $this->getDoctrine()->getRepository('AccountingBundle:AccountPurchase')->accountPurchaseReverse($purchase);
      // $em->getRepository('InventoryBundle:Purchase')->purchaseSimpleUpdate($purchase);
        $purchase->setRevised(true);
        $purchase->setProcess('created');
        $em->flush();
        $template = $this->get('twig')->render('InventoryBundle:Reverse:purchaseReverse.html.twig', array(
            'entity' => $purchase,
            'inventoryConfig' => $purchase->getInventoryConfig(),
        ));
        $em->getRepository('InventoryBundle:Reverse')->insertPurchase($purchase, $template);
        return $this->redirect($this->generateUrl('inventory_purchasesimple_edit',array('id' => $purchase->getId())));
    }


}
