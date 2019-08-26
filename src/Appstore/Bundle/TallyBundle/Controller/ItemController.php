<?php

namespace Appstore\Bundle\TallyBundle\Controller;


use Appstore\Bundle\TallyBundle\Entity\Item;
use Appstore\Bundle\TallyBundle\Form\ItemEditType;
use Appstore\Bundle\TallyBundle\Form\ItemType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

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
        $pagination->setTemplate('SettingToolBundle:Widget:pagination.html.twig');
        return $pagination;
    }

    /**
     * Lists all Item entities.
     */

    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $option = $this->getUser()->getGlobalOption()->getTallyConfig()->getId();
        $entities = $em->getRepository('TallyBundle:Item')->findWithSearch($option,$data);
        $pagination = $this->paginate($entities);
        return $this->render('TallyBundle:Item:index.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data
        ));
    }

    /**
     * Creates a new Item entity.
     *
     */

    public function createAction(Request $request)
    {
        $config = $this->getUser()->getGlobalOption()->getTallyConfig();
        $em = $this->getDoctrine()->getManager();
        $entity = new Item();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $data = $request->request->all();

        if ($form->isValid()) {
            $checkData = $this->getDoctrine()->getRepository('TallyBundle:Item')->checkDuplicateSKU($config->getId(),$data);
            if($checkData == 0 ) {
                if($entity->getVatName()){
                    $vatId =  explode("-",$entity->getVatName());
                    $vat = $this->getDoctrine()->getRepository('TallyBundle:TaxTariff')->findOneBy(array('hsCode'=>$vatId[0]));
                    $entity->setVatProduct($vat);
                }
                $entity->setConfig($config);
                $entity->upload();
                $em->persist($entity);
                $em->flush();
                $this->get('session')->getFlashBag()->add(
                    'success', "Item has been added successfully"
                );
                return $this->redirect($this->generateUrl('tallyitem_new'));

            }else{

                $this->get('session')->getFlashBag()->add(
                    'notice',"Item already exist, Please change add another item name"
                );
                return $this->render('TallyBundle:Item:new.html.twig', array(
                    'entity' => $entity,
                    'form'   => $form->createView(),
                ));
            }
        }
        return $this->render('TallyBundle:Item:new.html.twig', array(
            'entity' => $entity,
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
        $em = $this->getDoctrine()->getRepository('TallyBundle:Category');
        $config = $this->getUser()->getGlobalOption()->getTallyConfig();
        $form = $this->createForm(new ItemType($config,$em), $entity, array(
            'action' => $this->generateUrl('tallyitem_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
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
        $inventory = $this->getUser()->getGlobalOption();
        $em = $this->getDoctrine()->getManager();
        $entity = new Item();
        $form   = $this->createCreateForm($entity);
        return $this->render('TallyBundle:Item:new.html.twig', array(
            'entity' => $entity,
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

        $entity = $em->getRepository('TallyBundle:Item')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Item entity.');
        }

       return $this->render('TallyBundle:Item:show.html.twig', array(
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
        $entity = $em->getRepository('TallyBundle:Item')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Item entity.');
        }
        $editForm = $this->createEditForm($entity);
        return $this->render('TallyBundle:Item:new.html.twig', array(

            'entity'        => $entity,
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
        $em = $this->getDoctrine()->getRepository('TallyBundle:Category');
        $config = $this->getUser()->getGlobalOption()->getTallyConfig();
        $form = $this->createForm(new ItemType($config,$em), $entity, array(
            'action' => $this->generateUrl('tallyitem_update', array('id' => $entity->getId())),
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
        $entity = $em->getRepository('TallyBundle:Item')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Item entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            if($entity->getVatName()){
                $vatId =  explode("-",$entity->getVatName());
                $vat = $this->getDoctrine()->getRepository('TallyBundle:TaxTariff')->findOneBy(array('hsCode'=>$vatId[0]));
                $entity->setVatProduct($vat);
            }
            if(!empty($entity->upload())){ $entity->removeUpload(); }
            $entity->upload();
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been changed successfully"
            );
             return $this->redirect($this->generateUrl('tallyitem'));
        }

        return $this->render('TallyBundle:Item:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }


    /**
     * Displays a form to edit an existing Item entity.
     *
     */
    public function editAssetsAction(Item $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Item entity.');
        }
        $editForm = $this->createWebForm($entity);
        return $this->render('TallyBundle:Item:assetsProduct.html.twig', array(
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
        $em = $this->getDoctrine()->getRepository('TallyBundle:Category');
        $config = $this->getUser()->getGlobalOption()->getTallyConfig();
        $form = $this->createForm(new ItemEditType($config,$em), $entity, array(
            'action' => $this->generateUrl('tallyitem_web_update', array('id' => $entity->getId())),
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
    public function updateAssetsAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $entity = $em->getRepository('TallyBundle:Item')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Item entity.');
        }
        $editForm = $this->createWebForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            if($entity->getVatName()){
                $vatId =  explode("-",$entity->getVatName());
                $vat = $this->getDoctrine()->getRepository('TallyBundle:TaxTariff')->findOneBy(array('hsCode'=>$vatId[0]));
                $entity->setVatProduct($vat);
            }
            if(!empty($entity->upload())){ $entity->removeUpload(); }
            $entity->upload();
            $em->flush();
            $this->getDoctrine()->getRepository('TallyBundle:ItemMetaAttribute')->insertProductCategoryMeta($entity,$data);
         //   $this->getDoctrine()->getRepository('TallyBundle:ItemKeyValue')->insertProductCategoryMeta($entity,$data);
            $this->get('session')->getFlashBag()->add('success',"Data has been changed successfully");
            return $this->redirect($this->generateUrl('tallyitem_edit_web', array('id' => $entity->getId())));

        }

        return $this->render('TallyBundle:Item:assetsProduct.html.twig', array(
            'entity'    => $entity,
            'form'      => $editForm->createView(),
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
        $entity = $em->getRepository('TallyBundle:Item')->find($id);

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

    public function isWebAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('TallyBundle:Item')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find District entity.');
        }

        $status = $entity->isWeb();

        if($status == 1){
            $entity->setIsWeb(0);
        } else{
            $entity->setIsWeb(1);
        }
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',"Web status has been changed successfully"
        );
        return $this->redirect($this->generateUrl('item'));
    }


    public function barcodeAction(Request $request)
    {
        $data = $request->request->get('check');
        $em = $this->getDoctrine()->getManager();
        if(!empty($data)) {
            foreach ($data as $row) {
                $entities[] = $em->getRepository('TallyBundle:Item')->find($row);
            }
        }
        return $this->render('TallyBundle:Item:pre-barcode.html.twig', array(
            'entities'      => $entities
        ));

    }

    public function uploadItemImageAction($item)
    {


        $entity = new ItemGallery();
        $option = $this->getUser()->getGlobalOption();
        $entity ->upload($option->getId(),$item);
    }

    public function autoSearchAction(Request $request)
    {
        $item = $_REQUEST['q'];
        if ($item) {
            $inventory = $this->getUser()->getGlobalOption()->getTallyConfig()->getId();
            $item = $this->getDoctrine()->getRepository('TallyBundle:Item')->searchAutoComplete($item,$inventory);
        }
        return new JsonResponse($item);
    }

    public function autoSearchItemAllAction(Request $request)
    {
        $item = $_REQUEST['q'];
        if ($item) {
            $inventory = $this->getUser()->getGlobalOption()->getTallyConfig()->getId();
            $item = $this->getDoctrine()->getRepository('TallyBundle:Item')->searchAutoCompleteAllItem($item,$inventory);
        }
        return new JsonResponse($item);
    }

    public function priceAction(Request $request)
    {
        $item = $request->request->get('item');
        $entity = $this->getDoctrine()->getRepository('TallyBundle:Item')->find($item);
        return new JsonResponse(array('salesPrice' => $entity->getSalesPrice(),'webPrice'=>$entity->getWebPrice()));
    }

    public function updateSalesPriceAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('TallyBundle:Item')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Item entity.');
        }
        $process = 'set'.$data['name'];
        $entity->$process($data['value']);
        $em->flush();
        if($data['name'] =='SalesPrice'){
            $this->getDoctrine()->getRepository('TallyBundle:PurchaseItem')->updateSalesPrice($entity);
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
        $entities = $this->getDoctrine()->getRepository('TallyBundle:Item')->findBy(
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

    public function discountSelectAction()
    {
        $getEcommerceConfig = $this->getUser()->getGlobalOption()->getEcommerceConfig();
        $entities = $this->getDoctrine()->getRepository('EcommerceBundle:Discount')->findBy(
            array('ecommerceConfig'=>$getEcommerceConfig,'status'=>1)
        );
        $type = '';
        $items = array();
        $items[]=array('value' => '','text'=> '-Discount-');
        foreach ($entities as $entity):
            if($entity->getType() == "percentage"){
                $type ='%';
            }
            $items[]=array('value' => $entity->getId(),'text'=> $entity->getName().'('.$entity->getDiscountAmount().')'.$type);
        endforeach;
        return new JsonResponse($items);


    }

    public function tagSelectAction()
    {
        $getEcommerceConfig = $this->getUser()->getGlobalOption()->getEcommerceConfig();
        $entities = $this->getDoctrine()->getRepository('EcommerceBundle:Promotion')->getTypeBasePromotion($getEcommerceConfig->getId(),'Promotion');
        $items = array();
        foreach ($entities as $entity):
            $items[]=array('value' => $entity->getId(),'text'=> $entity->getName());
        endforeach;
        return new JsonResponse($items);


    }



    public function inlineItemUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('TallyBundle:Item')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseItem entity.');
        }
        $discount = $em->getRepository('EcommerceBundle:Discount')->find($data['value']);
        $em->getRepository('TallyBundle:Item')->getCulculationDiscountPrice($entity,$discount);
        exit;

    }


	public function stockExcelAction()
	{
		set_time_limit(0);
		ignore_user_abort(true);
		$em = $this->getDoctrine()->getManager();
		$data = $_REQUEST;
		$inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
		$entities = $em->getRepository('TallyBundle:Item')->getInventoryExcel($inventory,$data);
		$phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

		$phpExcelObject->setActiveSheetIndex(0)
		               ->setCellValue('A1', 'SKU')
		               ->setCellValue('B1', 'Name')
		               ->setCellValue('C1', 'Category')
		               ->setCellValue('D1', 'Brand')
		               ->setCellValue('E1', 'Purchase Qnt.')
		               ->setCellValue('F1', 'Purchase Return Qnt.')
		               ->setCellValue('G1', 'Sales Qnt.')
		               ->setCellValue('H1', 'Sales Return Qnt.')
		               ->setCellValue('I1', 'Online Sales Qnt.')
		               ->setCellValue('J1', 'Online Sales Return Qnt.')
		               ->setCellValue('K1', 'Damage Qnt.')
		               ->setCellValue('L1', 'Ongoing Qnt.')
		               ->setCellValue('M1', 'Remaining Qnt.')
		               ->setCellValue('N1', 'DP Price.')
		               ->setCellValue('O1', 'MRP');
		$rowNo =2;

		/* @var $entity Item */

		foreach ($entities as  $entity){

			$category = '';
			if(!empty($entity->getMasterItem()->getCategory())){
				$category = $entity->getMasterItem()->getCategory()->getName();
			}

			$brand = '';
			if(!empty($entity->getMasterItem()->getBrand())){
				$brand = $entity->getMasterItem()->getBrand()->getName();
			}

			$phpExcelObject->setActiveSheetIndex(0)
			               ->setCellValue("A{$rowNo}", $entity->getGpSku())
			               ->setCellValue("B{$rowNo}", $entity->getMasterItem()->getName() )
			               ->setCellValue("C{$rowNo}", $category)
			               ->setCellValue("D{$rowNo}", $brand)
			               ->setCellValue("E{$rowNo}", $entity->getPurchaseQuantity())
			               ->setCellValue("F{$rowNo}", $entity->getPurchaseQuantityReturn())
			               ->setCellValue("G{$rowNo}", $entity->getSalesQuantity())
			               ->setCellValue("H{$rowNo}", $entity->getSalesQuantityReturn())
			               ->setCellValue("I{$rowNo}", $entity->getOnlineOrderQuantity())
			               ->setCellValue("J{$rowNo}", $entity->getOnlineOrderQuantityReturn())
			               ->setCellValue("K{$rowNo}", $entity->getDamageQuantity())
			               ->setCellValue("L{$rowNo}", $entity->getOrderCreateItem())
			               ->setCellValue("M{$rowNo}", $entity->getRemainingQuantity())
			               ->setCellValue("N{$rowNo}", $entity->getSalesDistributorPrice())
			               ->setCellValue("O{$rowNo}", $entity->getSalesPrice())
			;
			$rowNo++;
		}
		$phpExcelObject->getActiveSheet()->setTitle('GP-Center ProductGroup Details');
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$phpExcelObject->setActiveSheetIndex(0);

		// create the writer
		$writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel2007');
		// create the response
		$response = $this->get('phpexcel')->createStreamedResponse($writer);
		// adding headers
		$dispositionHeader = $response->headers->makeDisposition(
			ResponseHeaderBag::DISPOSITION_ATTACHMENT,
			'inventory-stock.xlsx'
		);
		$response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
		$response->headers->set('Pragma', 'public');
		$response->headers->set('Cache-Control', 'maxage=1');
		$response->headers->set('Content-Disposition', $dispositionHeader);

		return $response;
	}

    public function stockQuantityUpdateAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption();
        $data = [];
        $entities = $this->getDoctrine()->getRepository('TallyBundle:Item')->findWithSearch($config,$data);
        $pagination = $this->paginate($entities);

        /* @var Item $item */

        foreach ($pagination as $item){
            $this->getDoctrine()->getRepository('TallyBundle:Item')->updateRemovePurchaseQuantity($item,'');
            $this->getDoctrine()->getRepository('TallyBundle:Item')->updateRemovePurchaseQuantity($item,'opening');
            $this->getDoctrine()->getRepository('TallyBundle:Item')->updateRemovePurchaseQuantity($item,'sales');
            $this->getDoctrine()->getRepository('TallyBundle:Item')->updateRemovePurchaseQuantity($item,'sales-return');
            $this->getDoctrine()->getRepository('TallyBundle:Item')->updateRemovePurchaseQuantity($item,'purchase-return');
            $this->getDoctrine()->getRepository('TallyBundle:Item')->updateRemovePurchaseQuantity($item,'damage');
        }
        exit;
        return $this->redirect($this->generateUrl('medicine_stock'));
    }





}
