<?php

namespace Appstore\Bundle\InventoryBundle\Controller;

use Appstore\Bundle\InventoryBundle\Entity\ItemGallery;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseVendorItem;
use Appstore\Bundle\InventoryBundle\Form\ItemSearchType;
use Appstore\Bundle\InventoryBundle\Form\ItemWebType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\InventoryBundle\Entity\Item;
use Appstore\Bundle\InventoryBundle\Form\ItemType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Item controller.
 *
 */
class ItemController extends Controller
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
     * Lists all Item entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities = $em->getRepository('InventoryBundle:Item')->findWithSearch($inventory,$data);
        $pagination = $this->paginate($entities);

        //$formSearch = $this->searchCreateForm(new Item());
        return $this->render('InventoryBundle:Item:index.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data
            //'search' => $formSearch->createView(),
        ));
    }

    /**
     * Creates a form to create a Item entity.
     *
     * @param Item $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function searchCreateForm(Item $entity)
    {
        $em = $this->getDoctrine()->getRepository('InventoryBundle:ItemTypeGrouping');
        $inventoryConfig = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $form = $this->createForm(new ItemSearchType($inventoryConfig,$em), $entity, array(
            'action' => $this->generateUrl('item'),
            'method' => 'GET'
        ));
        return $form;
    }

    /**
     * Creates a new Item entity.
     *
     */
    public function createAction(Request $request)
    {
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $em = $this->getDoctrine()->getManager();
        $entity = new Item();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $data = $request->request->all();

        if ($form->isValid()) {
            $checkData = $this->getDoctrine()->getRepository('InventoryBundle:Item')->checkDuplicateSKU($inventory,$data);
            if($checkData == 0 ) {

                $entity->setInventoryConfig($inventory);
                $entity->setName($entity->getMasterItem()->getName());
                $em->persist($entity);
                $em->flush();
                $this->get('session')->getFlashBag()->add(
                    'success', "Item has been added successfully"
                );
                return $this->redirect($this->generateUrl('item_new', array('id' => $entity->getId())));

            }else{

                $this->get('session')->getFlashBag()->add(
                    'notice',"Item already exist, Please change add another item name"
                );
                return $this->render('InventoryBundle:Item:new.html.twig', array(
                    'entity' => $entity,
                    'inventory' => $inventory,
                    'form'   => $form->createView(),
                ));
            }


        }

        return $this->render('InventoryBundle:Item:new.html.twig', array(
            'entity' => $entity,
            'inventory' => $inventory,
            'form'   => $form->createView(),
        ));
    }


    /**
     * Creates a form to create a Item entity.
     *
     * @param Item $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Item $entity)
    {
        $em = $this->getDoctrine()->getRepository('InventoryBundle:ItemTypeGrouping');
        $inventoryConfig = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $form = $this->createForm(new ItemType($inventoryConfig,$em), $entity, array(
            'action' => $this->generateUrl('item_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }


    /**
     * Displays a form to create a new Item entity.
     *
     */
    public function newAction()
    {
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $em = $this->getDoctrine()->getManager();
        $entity = new Item();
        $form   = $this->createCreateForm($entity);
        return $this->render('InventoryBundle:Item:new.html.twig', array(
            'entity' => $entity,
            'inventory' => $inventory,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Item entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:Item')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Item entity.');
        }

       return $this->render('InventoryBundle:Item:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * Displays a form to edit an existing Item entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entity = $em->getRepository('InventoryBundle:Item')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Item entity.');
        }
        $editForm = $this->createEditForm($entity);
        return $this->render('InventoryBundle:Item:new.html.twig', array(

            'entity'        => $entity,
            'inventory'     => $inventory,
            'form'          => $editForm->createView(),

        ));
    }


    /**
     * Creates a form to edit a Item entity.
     *
     * @param Item $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Item $entity)
    {
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $form = $this->createForm(new ItemType($inventory), $entity, array(
            'action' => $this->generateUrl('item_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to edit an existing Item entity.
     *
     */
    public function editWebAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:Item')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Item entity.');
        }
        $editForm = $this->createWebForm($entity);
        return $this->render('InventoryBundle:Item:web.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),

        ));
    }

    /**
     * Creates a form to edit a Item entity.
     *
     * @param Item $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createWebForm(Item $entity)
    {
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $form = $this->createForm(new ItemWebType($inventory), $entity, array(
            'action' => $this->generateUrl('item_web_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Edits an existing Item entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $entity = $em->getRepository('InventoryBundle:Item')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Item entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        if ($editForm->isValid()) {

            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been changed successfully"
            );
            $this->getDoctrine()->getRepository('InventoryBundle:ItemGallery')->insertProductGallery($entity,$data);
            return $this->redirect($this->generateUrl('item'));
        }

        return $this->render('InventoryBundle:Item:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Edits an existing Item entity.
     *
     */
    public function updateWebAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $entity = $em->getRepository('InventoryBundle:Item')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Item entity.');
        }

        $editForm = $this->createWebForm($entity);
        $editForm->handleRequest($request);
        if ($editForm->isValid()) {

            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been changed successfully"
            );
            $this->getDoctrine()->getRepository('InventoryBundle:ItemGallery')->insertProductGallery($entity,$data);
            return $this->redirect($this->generateUrl('item_edit_web', array('id' => $entity->getId())));
        }

        return $this->render('InventoryBundle:Item:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Deletes a Item entity.
     *
     */
    public function deleteAction(Item $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find item entity.');
        }

        try {

            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'error',"Data has been deleted successfully"
            );

        } catch (ForeignKeyConstraintViolationException $e) {
            $this->get('session')->getFlashBag()->add(
                'notice',"Data has been relation another Table"
            );
        }
        return $this->redirect($this->generateUrl('item'));
    }


    /**
     * Status a Page entity.
     *
     */
    public function statusAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('InventoryBundle:Item')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find District entity.');
        }
        $status = $entity->isStatus();
        if($status == 1){
            $entity->setStatus(0);
        } else{
            $entity->setStatus(1);
        }
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',"Status has been changed successfully"
        );
        return $this->redirect($this->generateUrl('item'));
    }

    public function barcodeAction(Request $request)
    {
        $data = $request->request->get('check');
        $em = $this->getDoctrine()->getManager();
        if(!empty($data)) {
            foreach ($data as $row) {
                $entities[] = $em->getRepository('InventoryBundle:Item')->find($row);
            }
        }
        return $this->render('InventoryBundle:Item:pre-barcode.html.twig', array(
            'entities'      => $entities
        ));

    }

    public function uploadItemImageAction(PurchaseVendorItem $item)
    {
        $entity = new ItemGallery();
        $option = $this->getUser()->getGlobalOption();
        $entity ->upload($option->getId(),$item->getId());
    }

    public function skuUpdateAction(Item $item)
    {
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $this->getDoctrine()->getRepository('InventoryBundle:Item')->skuUpdate($inventory,$item);
        return new JsonResponse($item->getName());
    }

    public function autoSearchAction(Request $request)
    {
        $item = $_REQUEST['q'];
        if ($item) {
            $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
            $item = $this->getDoctrine()->getRepository('InventoryBundle:Item')->searchAutoComplete($item,$inventory);
        }
        return new JsonResponse($item);
    }

    public function autoSearchItemAllAction(Request $request)
    {
        $item = $_REQUEST['q'];
        if ($item) {
            $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
            $item = $this->getDoctrine()->getRepository('InventoryBundle:Item')->searchAutoCompleteAllItem($item,$inventory);
        }
        return new JsonResponse($item);
    }

    public function priceAction(Request $request)
    {
        $item = $request->request->get('item');
        $entity = $this->getDoctrine()->getRepository('InventoryBundle:Item')->find($item);
        return new JsonResponse(array('salesPrice' => $entity->getSalesPrice(),'webPrice'=>$entity->getWebPrice()));
    }

    public function updateWebPriceAction(Request $request)
    {
	    $config = $this->getUser()->getGlobalOption()->getInventoryConfig();
    	$data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('InventoryBundle:Item')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Item entity.');
        }

	    if($data['name'] == 'Barcode') {
		    $existBarcode = $this->getDoctrine()->getRepository( 'InventoryBundle:Item' )->findBy( array('inventoryConfig'=>$config, 'barcode' => $data['value'] ) );
		    if ( empty( $existBarcode ) ) {
			    $entity->setBarcode( $data['value'] );
			    $em->flush();
		    }
	    }elseif($data['name'] == 'Sku'){
		    $existSku = $this->getDoctrine()->getRepository('InventoryBundle:Item')->findBy(array('inventoryConfig'=>$config,'sku' => $data['value']));
		    if(empty($existSku)){
			    $entity->setSku($data['value']);
			    $em->flush();
		    }
	    }else{
		    $process = 'set'.$data['name'];
		    $entity->$process($data['value']);
		    $em->flush();
	    }

        exit;
    }

    /**
     * Status a Page entity.
     *
     */
    public function webStatusAction(Item $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $status = $entity->getIsWeb();
        if($status == 1){
            $entity->setIsWeb(0);
        } else{
            $entity->setIsWeb(1);
        }
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',"Status has been changed successfully"
        );
        return $this->redirect($this->generateUrl('item'));
    }

    public function vendorItemAction($vendor)
    {
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities = $this->getDoctrine()->getRepository('InventoryBundle:Item')->findBy(
            array(
                'inventoryConfig'=>$inventory,
                'vendor'=>$vendor)
        );
        $items = array();
        foreach ($entities as $entity):
            $item =$entity->getName();
            $items[]=array('value' => $entity->getId(),'text'=> $item);
        endforeach;
        return new JsonResponse($items);

    }

    public function updatePurchaseQuantity()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $items = $this->getDoctrine()->getRepository('InventoryBundle:PurchaseItem')->findItemWithPurchaseQuantity($inventory);
        foreach ($items as $row){
           $item = $this->getDoctrine()->getRepository('InventoryBundle:Item')->find($row['item']);
           $item->setPurchaseQuantity($row['quantity']);
           $em->flush();
        }
        return $this->redirect($this->generateUrl('item'));
    }

    public function updateStockQuantityAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $items = $this->getDoctrine()->getRepository('InventoryBundle:PurchaseItem')->findItemWithPurchaseQuantity($inventory);
        foreach ($items as $row){
            $item = $this->getDoctrine()->getRepository('InventoryBundle:Item')->find($row['itemId']);
            $salesQnt = $this->getDoctrine()->getRepository('InventoryBundle:StockItem')->getItemQuantity($row['itemId'],'sales');
            $salesReturnQnt = $this->getDoctrine()->getRepository('InventoryBundle:StockItem')->getItemQuantity($row['itemId'],'salesReturn');
            $purchaseReturnQnt = $this->getDoctrine()->getRepository('InventoryBundle:StockItem')->getItemQuantity($row['itemId'],'purchaseReturn');
            $damageQnt = $this->getDoctrine()->getRepository('InventoryBundle:StockItem')->getItemQuantity($row['itemId'],'damage');
            $item->setPurchaseQuantity($row['quantity']);
            $item->setSalesQuantity($salesQnt);
            $item->setSalesQuantityReturn($salesReturnQnt);
            $item->setPurchaseQuantityReturn($purchaseReturnQnt);
            $item->setPurchaseQuantityReturn($damageQnt);
            $remainingQnt = ($item->getPurchaseQuantity() + $item->getSalesQuantityReturn()) - ($item->getSalesQuantity() + $item->getPurchaseQuantityReturn()+$item->getDamageQuantity());
            $item->setRemainingQnt($remainingQnt);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('item'));
    }

	public function inlineUpdateAction(Request $request)
	{
		$data = $request->request->all();
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('InventoryBundle:PurchaseItem')->find($data['pk']);
		if (!$entity) {
			throw $this->createNotFoundException('Unable to find PurchaseItem entity.');
		}
		if($data['name'] == 'SalesPrice' and 0 < (float)$data['value']){
			$process = 'set'.$data['name'];
			$entity->$process((float)$data['value']);
			$entity->setSalesSubTotal((float)$data['value'] * $entity->getQuantity());
			$em->flush();
		}

		if($data['name'] == 'PurchasePrice' and 0 < (float)$data['value']){
			$entity->setPurchasePrice((float)$data['value']);
			$entity->setPurchaseSubTotal((float)$data['value'] * $entity->getQuantity());
			$em->flush();
			$em->getRepository('InventoryBundle:Purchase')->purchaseSimpleUpdate($entity->getPurchase());
		}
		$salesQnt = $this->getDoctrine()->getRepository('InventoryBundle:StockItem')->getPurchaseItemQuantity($entity,array('sales','damage','purchaseReturn'));
		if($data['name'] == 'Quantity' and $salesQnt <= (int)$data['value']){
			$entity->setQuantity((int)$data['value']);
			$entity->setPurchaseSubTotal((int)$data['value'] * $entity->getPurchasePrice());
			$entity->setSalesSubTotal((int)$data['value'] * $entity->getSalesPrice());
			$em->flush();
			$em->getRepository('InventoryBundle:Purchase')->purchaseSimpleUpdate($entity->getPurchase());
		}

		if($data['name'] == 'Barcode'){
			$existBarcode = $this->getDoctrine()->getRepository('InventoryBundle:PurchaseItem')->findBy(array('barcode' => $data['value']));
			if(empty($existBarcode)){
				$process = 'set'.$data['name'];
				$entity->$process($data['value']);
				$em->flush();
			}
		}
		exit;

	}


}
