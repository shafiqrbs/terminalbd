<?php

namespace Appstore\Bundle\InventoryBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\InventoryBundle\Entity\PurchaseItem;
use Appstore\Bundle\InventoryBundle\Form\PurchaseItemType;
use Symfony\Component\HttpFoundation\Response;

/**
 * PurchaseItem controller.
 *
 */
class PurchaseItemController extends Controller
{

    /**
     * Lists all PurchaseItem entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('InventoryBundle:PurchaseItem')->findAll();

        return $this->render('InventoryBundle:PurchaseItem:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new PurchaseItem entity.
     *
     */
    public function createAction(Request $request, $purchase )
    {
            $data = $request->request->all();

            $purchase = $this->getDoctrine()->getRepository('InventoryBundle:Purchase')->find($purchase);
            $this->checkQuantity($purchase,$data);
            $em = $this->getDoctrine()->getManager();
            $i = 0 ;
            $checkQuantity = $this->checkQuantity($purchase,$data);
            if( $this->checkQuantity($purchase,$data) == 'success' ){

                foreach($data['purchaseVendorItem'] as $row)
                {

                    $entity = new PurchaseItem();
                    $purchaseVendorItem = $this->getDoctrine()->getRepository('InventoryBundle:PurchaseVendorItem')->find($data['purchaseVendorItem'][$i]);
                    $item = $this->getDoctrine()->getRepository('InventoryBundle:Item')->find($data['item'][$i]);
                    $entity->setPurchase($purchase);
                    $entity->setPurchaseVendorItem($purchaseVendorItem);
                    $entity->setItem($item);
                    $entity->setQuantity($data['quantity'][$i]);
                    $entity->setPurchasePrice($purchaseVendorItem->getPurchasePrice());
                    $salesPrice = ($data['salesPrice'][$i] > 0 ) ? $data['salesPrice'][$i] : $purchaseVendorItem->getSalesPrice();
                    $entity->setSalesPrice($salesPrice);
                    $em->persist($entity);
                    $em->flush();
                    $i++;

                }
                $item = $purchase->getTotalItem();
                $quantity = $purchase->getTotalQnt();
                $vendorItem = $this->getDoctrine()->getRepository('InventoryBundle:PurchaseItem')->getPurchaseItemQuantity($purchase);
                if( $vendorItem['totalQnt']  ==  $quantity or $vendorItem['totalItem'] == $item ){
                    $em->getRepository('InventoryBundle:Purchase')->updateProcess($purchase,'complete');
                }else{
                    $em->getRepository('InventoryBundle:Purchase')->updateProcess($purchase,'wfs');
                }
                return $this->redirect($this->generateUrl('purchase_show', array('id' => $purchase->getId())));

            }

            //$item = $em->getRepository('InventoryBundle:PurchaseItem')->getItemList($purchase);

            return new Response($checkQuantity);


    }

    public function checkQuantity($purchase,$data)
    {

        $em = $this->getDoctrine()->getManager();
        $totalQnt = $em->getRepository('InventoryBundle:PurchaseVendorItem')->getPurchaseVendorQuantitySum($purchase);
        $itemQnt = 0;
        foreach ($data['quantity'] as $key=>$quantity) {
            $itemQnt += $quantity;
        }

        if( $totalQnt == $itemQnt ){
            $msg = 'success';
        }else{
            $msg = 'invalid';
        }

        return $msg;
    }
    /**
     * Creates a form to create a PurchaseItem entity.
     *
     * @param PurchaseItem $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(PurchaseItem $entity,$purchase)
    {
        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        $form = $this->createForm(new PurchaseItemType($inventory), $entity, array(
            'action' => $this->generateUrl('inventory_purchaseitem_create',array('purchase'=>$purchase)),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form addPurchase'

            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new PurchaseItem entity.
     *
     */
    public function newAction($purchase)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = new PurchaseItem();
        $purchaseInfo = $this->getDoctrine()->getRepository('InventoryBundle:Purchase')->find($purchase);
        $form   = $this->createCreateForm($entity,$purchase);

        $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
        if($inventory->getIsVendor() == 1 ){
            $items = $this->getDoctrine()->getRepository('InventoryBundle:Item')->findBy(array('inventoryConfig'=>$inventory,'vendor'=>$purchaseInfo->getVendor()),array('name'=>'ASC'));
        }else{
            $items = $this->getDoctrine()->getRepository('InventoryBundle:Item')->findBy(array('inventoryConfig'=>$inventory),array('name'=>'ASC'));
        }


        return $this->render('InventoryBundle:PurchaseItem:new.html.twig', array(
            'purchase' => $purchase,
            'entity' => $entity,
            'purchaseInfo' => $purchaseInfo,
            'items' => $items,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a PurchaseItem entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:PurchaseItem')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseItem entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('InventoryBundle:PurchaseItem:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing PurchaseItem entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:PurchaseItem')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseItem entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('InventoryBundle:PurchaseItem:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a PurchaseItem entity.
    *
    * @param PurchaseItem $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(PurchaseItem $entity)
    {
        $inventory =  $this->getUser()->getGlobalOption()->getInventoryConfig();
        $form = $this->createForm(new PurchaseItemType($inventory), $entity, array(
            'action' => $this->generateUrl('inventory_purchaseitem_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing PurchaseItem entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InventoryBundle:PurchaseItem')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseItem entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('inventory_purchaseitem_edit', array('id' => $id)));
        }

        return $this->render('InventoryBundle:PurchaseItem:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a PurchaseItem entity.
     *
     */
    public function deleteAction(PurchaseItem $purchaseItem)
    {

        if($purchaseItem){
            $em = $this->getDoctrine()->getManager();
            $em->remove($purchaseItem);
            $em->flush();
            return new Response('success');
        }else{
            return new Response('failed');
        }

       }

    /**
     * Creates a form to delete a PurchaseItem entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('inventory_purchaseitem_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    public function searchAutoCompleteAction(Request $request)
    {
        $item = $_REQUEST['q'];
        if ($item) {
            $inventory = $this->getUser()->getGlobalOption()->getInventoryConfig();
            $item = $this->getDoctrine()->getRepository('InventoryBundle:PurchaseItem')->searchAutoComplete($item,$inventory);
        }
        return new JsonResponse($item);
    }

    public function inlineUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('InventoryBundle:PurchaseItem')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseItem entity.');
        }
        if($entity->getPurchasePrice() < $data['value'] )
        {
            $process = 'set'.$data['name'];
            $entity->$process($data['value']);
            $em->flush();
        }
        exit;

    }

    public function inlineItemUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('InventoryBundle:PurchaseVendorItem')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseItem entity.');
        }
        $item =$em->getRepository('InventoryBundle:Product')->find($data['value']);
        $entity->setMasterItem($item);
        $em->flush();
        exit;

    }

    public function searchNameAction($barcode)
    {
        return new JsonResponse(array(
            'id'    => $barcode,
            'text'  => $barcode
        ));
    }
}
