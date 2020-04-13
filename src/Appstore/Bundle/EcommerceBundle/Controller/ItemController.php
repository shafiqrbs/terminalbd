<?php

namespace Appstore\Bundle\EcommerceBundle\Controller;

use Appstore\Bundle\EcommerceBundle\Entity\Item;
use Appstore\Bundle\EcommerceBundle\Entity\ItemSub;
use Appstore\Bundle\EcommerceBundle\Entity\ItemGallery;
use Appstore\Bundle\EcommerceBundle\Entity\ItemKeyValue;
use Appstore\Bundle\EcommerceBundle\Form\EcommerceProductEditType;
use Appstore\Bundle\EcommerceBundle\Form\EcommerceProductSubItemType;
use Appstore\Bundle\EcommerceBundle\Form\EcommerceProductType;
use Appstore\Bundle\EcommerceBundle\Form\ProductImageType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;

/**
 * Item controller.
 *
 */
class ItemController extends Controller
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
	 * Lists all Item entities.
	 *
	 * @Secure(roles = "ROLE_DOMAIN_ECOMMERCE,ROLE_DOMAIN")
	 */

	public function indexAction()
	{
		$em = $this->getDoctrine()->getManager();
		$data = $_REQUEST;
		$config = $this->getUser()->getGlobalOption()->getEcommerceConfig();
		$entities = $em->getRepository('EcommerceBundle:Item')->findGoodsWithSearch($config,$data);
		$pagination = $this->paginate($entities);
		$promotions = $this->getDoctrine()->getRepository('EcommerceBundle:Promotion')->findBy(array('ecommerceConfig'=>$config,'status'=> 1,'type'=>'Promotion'));
        if($this->getUser()->getGlobalOption()->getDomainType() == "medicine"){
            $theme = 'medicine';
        }else{
            $theme = 'index';
        }
		return $this->render("EcommerceBundle:Item:{$theme}.html.twig", array(
			'promotions' => $promotions,
			'pagination' => $pagination,
            'searchForm' => $data,
		));
	}
	/**
	 * Creates a new Item entity.
	 *
	 *
	 */
	public function createAction(Request $request)
	{
		$entity = new Item();
		$inventory = $this->getUser()->getGlobalOption()->getEcommerceConfig();
		$form = $this->createCreateForm($entity);
		$form->handleRequest($request);
		if ($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$entity->setEcommerceConfig($inventory);
			$entity->setMasterQuantity($entity->getQuantity());
			$entity->setSource('goods');
			$entity->setSubProduct(true);
			$entity->setIsWeb(true);
			$entity->upload();
			$em->persist($entity);
			$em->flush();
			$this->get('session')->getFlashBag()->add(
				'success',"Data has been inserted successfully"
			);
			$this->getDoctrine()->getRepository('EcommerceBundle:ItemSub')->initialInsertSubProduct($entity);
			return $this->redirect($this->generateUrl('ecommerce_item_edit',array('id' => $entity->getId())));
		}
		return $this->render('EcommerceBundle:Item:new.html.twig', array(
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
		$config = $this->getUser()->getGlobalOption()->getEcommerceConfig();
		$em = $this->getDoctrine()->getRepository('ProductProductBundle:Category');
		$form = $this->createForm(new EcommerceProductType($em,$config), $entity, array(
			'action' => $this->generateUrl('ecommerce_item_create'),
			'method' => 'POST',
			'attr' => array(
				'class' => 'action',
				'novalidate' => 'novalidate',
			)
		));
		return $form;
	}


	/**
	 * Displays a form to create a new Item entity.
	 * @Secure(roles = "ROLE_DOMAIN_ECOMMERCE,ROLE_DOMAIN")
	 */
	public function newAction()
	{

		$entity = new Item();
		$form   = $this->createCreateForm($entity);
		$config = $this->getUser()->getGlobalOption()->getEcommerceConfig();
		return $this->render('EcommerceBundle:Item:new.html.twig', array(
			'entity' => $entity,
			'ecommerceConfig' => $config,
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

		$entity = $em->getRepository('EcommerceBundle:Item')->find($id);

		if (!$entity) {
			throw $this->createNotFoundException('Unable to find Item entity.');
		}

		return $this->render('EcommerceBundle:Item:show.html.twig', array(
			'entity'      => $entity,
		));
	}
	/**
	 * Displays a form to edit an existing Item entity.
	 * @Secure(roles = "ROLE_DOMAIN_ECOMMERCE,ROLE_DOMAIN")
	 */

	public function editAction(Item $entity)
	{
		$em = $this->getDoctrine()->getManager();
		$goodsItem = new ItemSub();
		$goodsItemForm = $this->createSubItemForm($goodsItem,$entity);
		if (!$entity) {
			throw $this->createNotFoundException('Unable to find Item entity.');
		}
		$editForm = $this->inventoryEditForm($entity);
		$config = $this->getUser()->getGlobalOption()->getEcommerceConfig();
		return $this->render('EcommerceBundle:Item:edit.html.twig', array(
			'entity'            => $entity,
			'ecommerceConfig'   => $config,
			'form'              => $editForm->createView(),
			'goodsItemForm'     => $goodsItemForm->createView(),
		));
	}

	/**
	 * Displays a form to edit an existing Item entity.
	 * @Secure(roles = "ROLE_DOMAIN_ECOMMERCE,ROLE_DOMAIN")
	 */

	public function uploadAction(Item $entity)
	{
		$em = $this->getDoctrine()->getManager();
		$editForm = $this->uploadEditForm($entity);
		$config = $this->getUser()->getGlobalOption()->getEcommerceConfig();
		return $this->render('EcommerceBundle:Item:upload.html.twig', array(
			'entity'            => $entity,
			'ecommerceConfig'   => $config,
			'form'              => $editForm->createView(),

		));
	}

    /**
     * Creates a form to edit a Item entity.
     *
     * @param Item $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function uploadEditForm(Item $entity)
    {
        $inventory = $this->getUser()->getGlobalOption()->getEcommerceConfig();
        $em = $this->getDoctrine()->getRepository('ProductProductBundle:Category');
        $form = $this->createForm(new ProductImageType($em,$inventory), $entity, array(
            'action' => $this->generateUrl('ecommerce_item_upload_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'action',
                'novalidate' => 'novalidate',
                'enctype' => 'multipart/form-data',
            )
        ));
        return $form;
    }

    /**
     * Edits an existing Item entity.
     *
     */

    public function uploadUpdateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $file = $request->files->all();

        $entity = $em->getRepository('EcommerceBundle:Item')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Item entity.');
        }
        $editForm = $this->uploadEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            if($file['item']['file']){
                $entity->removeUpload();
                $img = $file['item']['file'];
                $fileName = $img->getClientOriginalName();
                $imgName = uniqid() . '.' . $fileName;
                $path = $entity->getUploadDir() . $imgName;
                if (!file_exists($entity->getUploadDir())) {
                    mkdir($entity->getUploadDir(), 0777, true);
                }
                $this->get('helper.imageresizer')->resizeImage(512, $path, $img);
                $entity->setPath($imgName);
            }
            $em->flush();
             $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('ecommerce_item'));
        }
        $config = $this->getUser()->getGlobalOption()->getEcommerceConfig();
        return $this->render('EcommerceBundle:Item:upload.html.twig', array(
            'entity'            => $entity,
            'ecommerceConfig'   => $config,
            'form'              => $editForm->createView(),
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
		$inventory = $this->getUser()->getGlobalOption()->getEcommerceConfig();
		$em = $this->getDoctrine()->getRepository('ProductProductBundle:Category');
		$form = $this->createForm(new EcommerceProductEditType($em,$inventory), $entity, array(
			'action' => $this->generateUrl('ecommerce_item_update', array('id' => $entity->getId())),
			'method' => 'PUT',
			'attr' => array(
				'class' => 'action',
				'novalidate' => 'novalidate',
			)
		));
		return $form;
	}


	/**
	 * Creates a form to edit a Item entity.
	 *
	 * @param Item $entity The entity
	 *
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createSubItemForm(ItemSub $entity, Item $item)
	{

		$form = $this->createForm(new EcommerceProductSubItemType(), $entity, array(
			'action' => $this->generateUrl('inventory_vendoritem_subproduct', array('id' => $item->getId())),
			'method' => 'PUT',
			'attr' => array(
				'id' => 'subItemForm',
				'class' => 'action',
				'novalidate' => 'novalidate',
			)
		));
		return $form;
	}

	/**
	 * Creates a form to edit a Item entity.
	 *
	 * @param Item $entity The entity
	 *
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function inventoryEditForm(Item $entity)
	{
		$inventory = $this->getUser()->getGlobalOption()->getEcommerceConfig();
		$em = $this->getDoctrine()->getRepository('ProductProductBundle:Category');
		$form = $this->createForm(new EcommerceProductEditType($em,$inventory), $entity, array(
			'action' => $this->generateUrl('ecommerce_item_update', array('id' => $entity->getId())),
			'method' => 'PUT',
			'attr' => array(
				'class' => 'action',
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
		$entity = $em->getRepository('EcommerceBundle:Item')->find($id);
		$goodsItem = new ItemSub();
		$goodsItemForm = $this->createSubItemForm($goodsItem,$entity);
		if (!$entity) {
			throw $this->createNotFoundException('Unable to find Item entity.');
		}
		$editForm = $this->createEditForm($entity);
		$editForm->handleRequest($request);

		if ($editForm->isValid()) {

			if($entity->upload() && !empty($entity->getFile())){
				$entity->removeUpload();
			}
			$entity->upload();
			$em->flush();
			$this->getDoctrine()->getRepository('EcommerceBundle:ItemMetaAttribute')->insertProductAttribute($entity,$data);
			$this->getDoctrine()->getRepository('EcommerceBundle:ItemKeyValue')->insertItemKeyValue($entity,$data);
			$this->getDoctrine()->getRepository('EcommerceBundle:ItemGallery')->insertProductGallery($entity,$data);
			$this->getDoctrine()->getRepository('EcommerceBundle:ItemSub')->initialUpdateSubProduct($entity);
			$this->getDoctrine()->getRepository('EcommerceBundle:Item')->updateMasterProductQuantity($entity);
			$this->get('session')->getFlashBag()->add(
				'success',"Data has been updated successfully"
			);
			return $this->redirect($this->generateUrl('ecommerce_item_edit', array('id' => $id)));
		}
		$ecommerceConfig = $this->getUser()->getGlobalOption()->getEcommerceConfig();
		return $this->render('EcommerceBundle:Item:edit.html.twig', array(
			'entity'                => $entity,
			'goodsItemForm'         => $goodsItemForm->createView(),
			'ecommerceConfig'       => $ecommerceConfig,
			'form'                  => $editForm->createView(),
		));
	}




	/**
	 * Deletes a Item entity.
	 * @Secure(roles = "ROLE_DOMAIN_ECOMMERCE,ROLE_DOMAIN")
	 */
	public function deleteAction(Item $vendorItem)
	{

		$em = $this->getDoctrine()->getManager();
		if (!$vendorItem) {
			throw $this->createNotFoundException('Unable to find Product entity.');
		}

		try {
			$em = $this->getDoctrine()->getManager();
			$vendorItem->deleteImageDirectory();
			$em->remove($vendorItem);
			$em->flush();
			$this->get('session')->getFlashBag()->add(
				'error',"Data has been deleted successfully"
			);
			return new Response('success');

		} catch (ForeignKeyConstraintViolationException $e) {
			$this->get('session')->getFlashBag()->add(
				'notice',"Data has been relation another Table"
			);
			return new Response('failed');
		}catch (\Exception $e) {
			$this->get('session')->getFlashBag()->add(
				'notice', 'Please contact system administrator further notification.'
			);
			return new Response('failed');
		}
		exit;

	}

	/**
	 * Status a Item entity.
	 *
	 */
	public function webStatusAction($id)
	{

		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('InventoryBundle:Item')->find($id);

		if (!$entity) {
			throw $this->createNotFoundException('Unable to find District entity.');
		}

		$status = $entity->getStatus();
		if($status == 1){
			$entity->setStatus(0);
		} else{
			$entity->setStatus(1);
		}
		$em->flush();
		$this->get('session')->getFlashBag()->add(
			'success',"Status has been changed successfully"
		);
		return $this->redirect($this->generateUrl('ecommerce_item'));
	}

	public function addSubProductAction(Request $request, Item $entity)
	{

		$data = $request->request->all();
		$this->getDoctrine()->getRepository('EcommerceBundle:ItemSub')->insertSubProduct($entity,$data);
		$subItem = $this->renderView( 'EcommerceBundle:Item:subItem.html.twig', array(
			'entity'           => $entity,
		));
		return new Response($subItem);

	}

	public function updateSubProductAction(Request $request, ItemSub $entity)
	{

		$data = $request->request->all();
		$this->getDoctrine()->getRepository('EcommerceBundle:ItemSub')->updateSubProduct($entity,$data);
		return new Response('success');

	}

	public function subItemDeleteAction(ItemSub $goodsItem)
	{
		if($goodsItem){
			$em = $this->getDoctrine()->getManager();
			$em->remove($goodsItem);
			$em->flush();
			return new Response('success');
		}else{
			return new Response('failed');
		}
	}




	public function uploadItemImageAction(Item $item)
	{
		$entity = new ItemGallery();
		$option = $this->getUser()->getGlobalOption();
		$entity ->upload($option->getId(),$item->getId());
	}


	public function keyValueDeleteAction(ItemKeyValue $itemKeyValue)
	{
		if($itemKeyValue){
			$em = $this->getDoctrine()->getManager();
			$em->remove($itemKeyValue);
			$em->flush();
			return new Response('success');
		}else{
			return new Response('failed');
		}
	}

	public function itemCopyAction(Item $copyEntity)
	{
		$em = $this->getDoctrine()->getManager();
		$entity = new Item();
		$entity->setName($copyEntity->getName());
		$entity->setCategory($copyEntity->getCategory());
		$entity->setWebName($copyEntity->getWebName());
		$entity->setSubProduct(true);
		$entity->setQuantity($copyEntity->getQuantity());
		$entity->setMasterQuantity($copyEntity->getMasterQuantity());
		$entity->setPurchasePrice($copyEntity->getPurchasePrice());
		$entity->setSalesPrice($copyEntity->getSalesPrice());
		$entity->setOverHeadCost($copyEntity->getOverHeadCost());
		$entity->setSize($copyEntity->getSize());
		$entity->setItemColors($copyEntity->getItemColors());
		$entity->setBrand($copyEntity->getBrand());
		$entity->setDiscount($copyEntity->getDiscount());
		$entity->setDiscountPrice($copyEntity->getDiscountPrice());
		$entity->setContent($copyEntity->getContent());
		$entity->setTag($copyEntity->getTag());
		$entity->setPromotion($copyEntity->getPromotion());
		$entity->setCountry($copyEntity->getCountry());
		$entity->setEcommerceConfig($copyEntity->getEcommerceConfig());
		$entity->setProductUnit($copyEntity->getProductUnit());
		$entity->setWarningLabel($copyEntity->getWarningLabel());
		$entity->setWarningText($copyEntity->getWarningText());
		$entity->setIsWeb(true);
		$entity->setStatus(true);
		$entity->setSource('goods');
		$em->persist($entity);
		$em->flush();
		$this->getDoctrine()->getRepository('EcommerceBundle:ItemMetaAttribute')->insertCopyProductAttribute($entity,$copyEntity);
		$this->getDoctrine()->getRepository('EcommerceBundle:ItemKeyValue')->insertCopyItemKeyValue($entity,$copyEntity);
		$this->getDoctrine()->getRepository('EcommerceBundle:ItemSub')->insertCopySubProduct($entity,$copyEntity);
		return $this->redirect($this->generateUrl('ecommerce_item_edit', array('id' => $entity->getId())));


	}


	public function quantityApplicableAction(Item $item)
	{
        $em = $this->getDoctrine()->getManager();
	    if($item->isQuantityApplicable() == 1){
            $item->setQuantityApplicable(0);
        }else{
            $item->setQuantityApplicable(1);
        }
        $em->persist($item);
        $em->flush();
        exit;

	}

    public function discountSelectAction()
    {
        $config = $this->getUser()->getGlobalOption()->getEcommerceConfig();
        $entities = $this->getDoctrine()->getRepository('EcommerceBundle:Discount')->findBy(
            array('ecommerceConfig' => $config,'status'=>1)
        );
        $type = '';
        $items = array();
        $items[]=array('value' => '','text'=> '---Add Discount---');
        foreach ($entities as $entity):
            if($entity->getType() == "percentage"){
                $type ='%';
            }
            $items[]=array('value' => $entity->getId(),'text'=> $entity->getName().'('.$entity->getDiscountAmount().')'.$type);
        endforeach;
        $items[]=array('value' => '0','text'=> 'Empty Discount');
        return new JsonResponse($items);


    }

    public function tagSelectAction()
    {
        $getEcommerceConfig = $this->getUser()->getGlobalOption()->getEcommerceConfig();
        $entities = $this->getDoctrine()->getRepository('EcommerceBundle:Promotion')->getTypeBasePromotion($getEcommerceConfig->getId(),'Promotion');
        $items = array();
        $items[] = array('value' => '','text'=> '---Add Promotion---');
        foreach ($entities as $entity):
            $items[] = array('value' => $entity->getId(),'text'=> $entity->getName());
        endforeach;
        $items[]=array('value' => '0','text'=> 'Empty Promotion');
        return new JsonResponse($items);

    }

    public function categorySelectAction()
    {
        $config = $this->getUser()->getGlobalOption()->getEcommerceConfig();
        $categoryTree = $this->getDoctrine()->getRepository('ProductProductBundle:Category')->getFlatEcommerceCategoryTree($config);
        $items = array();
        $items[]=array('value' => '','text'=> '-- Add Category --');
        foreach ($categoryTree as $entity):
            $items[]=array('value' => $entity['id'],'text'=> $entity['name']);
        endforeach;
        $items[]=array('value' => '0','text'=> 'Empty Category');
        return new JsonResponse($items);

    }


    public function inlineItemUpdateAction(Request $request)
	{
		$data = $request->request->all();
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('EcommerceBundle:Item')->find($data['pk']);
		if (!$entity) {
			throw $this->createNotFoundException('Unable to find PurchaseItem entity.');
		}
		if($data['name'] == 'Discount'){

			$discount = $em->getRepository('EcommerceBundle:Discount')->find($data['value']);
			if($discount){
                $discountPrice = $em->getRepository('EcommerceBundle:Item')->getCulculationDiscountPrice($entity,$discount);
                if($discountPrice > 0){
                    $entity->setDiscountPrice($discountPrice);
                    $entity->setDiscount($discount);
                    $em->getRepository('EcommerceBundle:ItemSub')->subItemDiscountPrice($entity,$entity->getDiscount());
                }
            }else{
                $entity->setDiscount(NULL);
                $entity->setDiscountPrice(NULL);
            }

		}elseif($data['name'] == 'Promotion'){
			$setValue = $em->getRepository('EcommerceBundle:Promotion')->find($data['value']);
			if($setValue){
                $entity->setPromotion($setValue);
            }else {
                $entity->setPromotion(NULL);
            }
		}elseif($data['name'] == 'Category'){
			$setValue = $em->getRepository('ProductProductBundle:Category')->find($data['value']);
			if($setValue){
                $entity->setCategory($setValue);
            }else {
                $entity->setCategory(NULL);
            }
		}else{
            $entity->$setName($data['value']);
        }
		$em->persist($entity);
		$em->flush();
		exit;

	}

	public function inlineSubItemUpdateAction(Request $request)
	{
		$data = $request->request->all();
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('EcommerceBundle:ItemSub')->find($data['pk']);
		if (!$entity) {
			throw $this->createNotFoundException('Unable to find PurchaseItem entity.');
		}
		$setName = 'set'.$data['name'];
		$setValue = $data['value'];
		$entity->$setName($setValue);
		$em->persist($entity);
		$em->flush($entity);
		exit;

	}

	public function keyValueSortedAction(Request $request)
	{
		$data = $request ->request->get('menuItem');
		$this->getDoctrine()->getRepository('InventoryBundle:ItemKeyValue')->setDivOrdering($data);
		exit;

	}

    public function statusAction(Item $entity)
    {
        $inventory = $this->getUser()->getGlobalOption()->getEcommerceConfig();
        $em = $this->getDoctrine()->getManager();
        $status = $entity->getStatus();
        if ($inventory != $entity->getEcommerceConfig()) {
            throw $this->createNotFoundException('Unable to find PreOrder entity.');
        }
        if($status == 1){
            $entity->setStatus(0);
        } else{
            $entity->setStatus(1);
        }
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',"Status has been changed successfully"
        );
        return $this->redirect($this->generateUrl('ecommerce_item'));
    }


}
