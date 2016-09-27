<?php

namespace Appstore\Bundle\InventoryBundle\Controller;

use Appstore\Bundle\InventoryBundle\Entity\GoodsItem;
use Appstore\Bundle\InventoryBundle\Entity\ItemAttribute;
use Appstore\Bundle\InventoryBundle\Entity\ItemGallery;
use Appstore\Bundle\InventoryBundle\Entity\ItemKeyValue;
use Appstore\Bundle\InventoryBundle\Entity\Purchase;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseItem;
use Appstore\Bundle\InventoryBundle\Form\GoodsType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseVendorItem;
use Symfony\Component\HttpFoundation\Response;

/**
 * PurchaseVendorItem controller.
 *
 */
class GoodsController extends Controller
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
     * Lists all PurchaseVendorItem entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $entities = $em->getRepository('InventoryBundle:PurchaseVendorItem')->findGoodsWithSearch($inventory,$data);
        $pagination = $this->paginate($entities);
        return $this->render('InventoryBundle:Goods:index.html.twig', array(
            'entities' => $pagination,
        ));
    }
    /**
     * Creates a new PurchaseVendorItem entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new PurchaseVendorItem();
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setInventoryConfig($inventory);
            $entity->setSource('goods');
            $entity->upload();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been inserted successfully"
            );
            $this->getDoctrine()->getRepository('InventoryBundle:GoodsItem')->initialInsertSubProduct($entity);
            return $this->redirect($this->generateUrl('inventory_goods_edit',array('id'=>$entity->getId())));
        }
        $ecommerceConfig = $this->getUser()->getGlobalOption()->getEcommerceConfig();
        return $this->render('InventoryBundle:Goods:new.html.twig', array(
            'entity' => $entity,
            'ecommerceConfig' => $ecommerceConfig,
            'form'   => $form->createView(),
        ));
    }

    public function checkItemQuantity(Purchase $purchase, $vendorQnt)
    {
        $item = $purchase->getTotalItem();
        $quantity = $purchase->getTotalQnt();
        $vendorItem = $this->getDoctrine()->getRepository('InventoryBundle:PurchaseVendorItem')->getPurchaseVendorItemQuantity($purchase);
        $totalQnt = ($vendorItem['totalQnt'] + $vendorQnt);
        $totalItem = ($vendorItem['totalItem'] + 1);
        if($totalQnt > $quantity or $totalItem > $item ){
            return false;
        }else{
            return true;
        }

    }

    /**
     * Creates a form to create a PurchaseVendorItem entity.
     *
     * @param PurchaseVendorItem $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(PurchaseVendorItem $entity)
    {
        $inventoryConfig = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $em = $this->getDoctrine()->getRepository('ProductProductBundle:Category');
        $form = $this->createForm(new GoodsType($em,$inventoryConfig), $entity, array(
            'action' => $this->generateUrl('inventory_goods_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'action',
                'novalidate' => 'novalidate',
            )


        ));
        return $form;
    }

    /**
     * Displays a form to create a new PurchaseVendorItem entity.
     *
     */
    public function newAction()
    {
        $entity = new PurchaseVendorItem();
        $form   = $this->createCreateForm($entity);
        $ecommerceConfig = $this->getUser()->getGlobalOption()->getEcommerceConfig();
        return $this->render('InventoryBundle:Goods:new.html.twig', array(
            'entity' => $entity,
            'ecommerceConfig' => $ecommerceConfig,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a PurchaseVendorItem entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:PurchaseVendorItem')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseVendorItem entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('InventoryBundle:Goods:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing PurchaseVendorItem entity.
     *
     */
    public function editAction(PurchaseVendorItem $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $sizes = $em->getRepository('InventoryBundle:ItemSize')->getCategoryBaseSize($entity);
        $colors = $em->getRepository('InventoryBundle:ItemColor')->findBy(array('inventoryConfig'=>$entity->getInventoryConfig(), 'status'=>1),array('name'=>'ASC'));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseVendorItem entity.');
        }

        /*$id =295;
        $entities = $em->getRepository('InventoryBundle:PurchaseVendorItem')->findBy(array('inventoryConfig'=>$entity->getInventoryConfig()));
        foreach($entities as $en)
        {
            $size = $em->getRepository('InventoryBundle:ItemSize')->find(1);
            $en->setSize($size);
            $unit = $em->getRepository('InventoryBundle:ItemUnit')->find(1);
            $en->setUnit($unit);
            $category = $em->getRepository('ProductProductBundle:Category')->find(5);
            $en->setCategory($category);
            $en->setSlug($en->getName());
            $color = $em->getRepository('InventoryBundle:ItemColor')->find(1);
            $en->setItemColors(array($color));
            $this->getDoctrine()->getRepository('InventoryBundle:GoodsItem')->initialInsertSubProduct($en);
        }
        $em->flush();
        */

        $val = '<ul>';
        /** @param $item PurchaseItem */
        $rows=array();
        foreach($entity->getPurchaseItems() as $item )
        {
            $color = array();

            if(!isset($rows[$item->getItem()->getSize()->getName()]['color'])) {
                $rows[$item->getItem()->getSize()->getName()]['color'] = array();
            }

            $rows[$item->getItem()->getSize()->getName()]['color'][$item->getItem()->getColor()->getName()] = $item->getItem()->getColor()->getName();

            if(!isset($rows[$item->getItem()->getSize()->getName()]['quantity'])){
                $rows[$item->getItem()->getSize()->getName()]['quantity'] = 0;
            }

            $rows[$item->getItem()->getSize()->getName()]['quantity'] += $item->getQuantity();

            $val .='<li>Item ID='.$item->getItem()->getId().'==Size=='.$item->getItem()->getSize()->getName().'=Color='.$item->getItem()->getColor()->getName().'===Quantity=='.$item->getQuantity().'</li>';
        }
        $val .='</ul>';

        echo $val;


        echo '<ul>';
        /** @param $item PurchaseItem */
        foreach($rows as $size => $row )
        {

            echo  '<li>==Size=='.$size.'=Color='.implode(', ', $row['color']).'===Quantity=='.$row['quantity'].'</li>';
        }
        echo '</ul>';


        exit;

        $editForm = $this->createEditForm($entity);
        $ecommerceConfig = $this->getUser()->getGlobalOption()->getEcommerceConfig();
        return $this->render('InventoryBundle:Goods:edit.html.twig', array(
            'entity'        => $entity,
            'sizes'         => $sizes,
            'colors'         => $colors,
            'ecommerceConfig' => $ecommerceConfig,
            'form'          => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a PurchaseVendorItem entity.
     *
     * @param PurchaseVendorItem $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(PurchaseVendorItem $entity)
    {
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $em = $this->getDoctrine()->getRepository('ProductProductBundle:Category');
        $form = $this->createForm(new GoodsType($em,$inventory), $entity, array(
            'action' => $this->generateUrl('inventory_goods_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'action',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing PurchaseVendorItem entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $entity = $em->getRepository('InventoryBundle:PurchaseVendorItem')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseVendorItem entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            if(!empty($entity->upload())) {
                $entity->removeUpload();
            }
            $entity->upload();
            $em->flush();
            $this->getDoctrine()->getRepository('InventoryBundle:ItemMetaAttribute')->insertProductAttribute($entity,$data);
            $this->getDoctrine()->getRepository('InventoryBundle:ItemKeyValue')->insertItemKeyValue($entity,$data);
            $this->getDoctrine()->getRepository('InventoryBundle:ItemGallery')->insertProductGallery($entity,$data);
            $this->getDoctrine()->getRepository('InventoryBundle:GoodsItem')->initialUpdateSubProduct($entity);
            $this->getDoctrine()->getRepository('InventoryBundle:GoodsItem')->insertSubProduct($entity,$data);
            return $this->redirect($this->generateUrl('inventory_goods_edit', array('id' => $id)));
        }
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $sizes = $em->getRepository('InventoryBundle:ItemSize')->findBy(array('inventoryConfig'=>$inventory, 'status'=>1),array('name'=>'ASC'));
        $colors = $em->getRepository('InventoryBundle:ItemColor')->findBy(array('inventoryConfig'=>$inventory, 'status'=>1),array('name'=>'ASC'));
        $ecommerceConfig = $this->getUser()->getGlobalOption()->getEcommerceConfig();
        return $this->render('InventoryBundle:Goods:edit.html.twig', array(
            'entity'      => $entity,
            'ecommerceConfig'      => $ecommerceConfig,
            'sizes'       => $sizes,
            'colors'      => $colors,
            'form'   => $editForm->createView(),
        ));
    }


    /**
     * Deletes a PurchaseVendorItem entity.
     *
     */
    public function deleteAction(PurchaseVendorItem $vendorItem)
    {

        if($vendorItem){
            $em = $this->getDoctrine()->getManager();
            $vendorItem->deleteImageDirectory();
            $em->remove($vendorItem);
            $em->flush();
            return new Response('success');
        }else{
            return new Response('failed');
        }
    }

    /**
     * Creates a form to delete a PurchaseVendorItem entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('inventory_goods_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
            ;
    }

    /**
     * Status a PurchaseVendorItem entity.
     *
     */
    public function webStatusAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('InventoryBundle:PurchaseVendorItem')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find District entity.');
        }

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
        return $this->redirect($this->generateUrl('inventory_purchasevendoritem'));
    }


    public function inlineItemUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('InventoryBundle:PurchaseItem')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseItem entity.');
        }
        $item =$em->getRepository('InventoryBundle:Item')->find($data['value']);
        $entity->setItem($item);
        $em->flush();
        exit;

    }

    public function uploadItemImageAction(PurchaseVendorItem $item)
    {
        $entity = new ItemGallery();
        $option = $this->getUser()->getGlobalOption();
        $entity ->upload($option->getId(),$item->getId());
    }

    public function subItemDeleteAction(GoodsItem $goodsItem)
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

    public function itemCopyAction(PurchaseVendorItem $item)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = new PurchaseVendorItem();
        $entity->setInventoryConfig($item->getInventoryConfig());
        $entity->setName($item->getName());
        $entity->setCategory($item->getCategory());
        $entity->setSubProduct(true);
        $entity->setQuantity($item->getQuantity());
        $entity->setPurchasePrice($item->getPurchase());
        $entity->setSalesPrice($item->getSalesPrice());
        $entity->setSize($item->getSize());
        $entity->setColor($item->getColor());
        $entity->setCountry($item->getCountry());
        $entity->setSource('goods');
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('inventory_goods_edit', array('id' => $entity->getId())));


    }



}
