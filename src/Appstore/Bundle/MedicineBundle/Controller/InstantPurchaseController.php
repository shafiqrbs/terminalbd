<?php

namespace Appstore\Bundle\MedicineBundle\Controller;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchase;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchaseItem;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSales;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSalesItem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Vendor controller.
 *
 */
class InstantPurchaseController extends Controller
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

        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineInstantPurchase')->findBy(array('medicineConfig' => $config,'mode'=>'medicine'),array('created'=>'DESC'));
        $pagination = $this->paginate($entities);

        return $this->render('MedicineBundle:InstantPurchase:index.html.twig', array(
            'entities' => $pagination,
        ));
    }

    public function returnInstantPurchaseData(){

        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $invoiceParticulars = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseItem')->getInstantPurchaseItem($config);
        $data = '';
        foreach ($invoiceParticulars as $instant){
            $quantity = $instant->getQuantity();
            $purchasePrice = $instant->getPurchasePrice();
            $salesPrice = $instant->getSalesPrice();
            $subTotal = $instant->getSalesPrice()* $instant->getQuantity();

            $data .='<tr id="remove-{ $instant->getMedicinePurchase()->getId() }">';
            $data .="<td class='numeric'>{$instant->getMedicinePurchase()->getMedicineVendor()->getCompanyName()}</td>";
            $data .="<td class='numeric' >{$instant->getMedicinePurchase()->getCreatedBy()}</td>";
            $data .="<td class='numeric' >{$instant->getMedicinePurchase()->getGrn()}</td>";
            $data .="<td class='numeric' >{$instant->getMedicineStock()->getName()}</td>";
            $data .="<td class='numeric' >{$purchasePrice}</td>";
            $data .="<td class='numeric' >{$salesPrice}</td>";
            $data .="<td class='numeric' >{$subTotal}</td>";
            $data .="<td class='numeric' >{$quantity}</td>";
            $data .="<td class='numeric' >";
            $data .='<div class="input-append">';
            $data .='<input type="number" id="quantity-{$instant->getId()}"  style="height: 20px!important;" name="quantity" max="{ $instant->getQuantity() }" required="required" class="span5 td-input input-number input" placeholder="quantity" aria-required="true">';
            $data .='<button type="button" class="btn blue mini $instantSales" style="height: 25px" data-id="{$instant->getId()}"  data-url="/medicine/sales/{$instant->getId()}/{$instant->getId()}/medicine-instant-sales"> <span class="fa fa-save"></span> Add</button>';
            $data .='<button type="button" class="btn red mini $instantDelete" style="height: 25px" id="{ $instant->getMedicinePurchase()->getId()}" data-url="/medicine/instant-purchase/{$instant->getMedicinePurchase()->getId()}/medicine-instant-purchase-delete"> <span class="fa fa-trash"></span></button>';
            $data .='</div>';
            $data .='</td>';
            $data .='</tr>';
        }
        $data = array(
            'invoiceParticulars' => $invoiceParticulars ,
            'success' => 'success'
        );

        return $data;

    }

    public function addParticularAction(Request $request)
    {
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $entity = new MedicinePurchase();
        $vendor = $this->getDoctrine()->getRepository('MedicineBundle:MedicineVendor')->checkInInsert($config,$data['vendor']);
        $entity->setMedicineConfig($config);
        $entity->setMedicineVendor($vendor);
        $entity->setInstantPurchase(1);
        $entity->setSubTotal($data['purchasePrice'] * $data['quantity']);
        $entity->setNetTotal($entity->getSubTotal());
        $entity->setApprovedBy($this->getUser());
        $entity->setDue($entity->getSubTotal());
        $purchaseBy = $this->getDoctrine()->getRepository('UserBundle:User')->findOneBy(array('username'=>$data['purchasesBy']));
        $entity->setPurchaseBy($purchaseBy);
        $entity->setProcess('Done');
        $em->persist($entity);
        $em->flush();
        $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseItem')->insertPurchaseItems($entity,$data);
        $msg = 'Medicine added successfully';
        $result = $this->returnInstantPurchaseData($entity);
        return new Response(json_encode($result));
        exit;
    }


    public function instantPurchaseLoadAction(MedicineSales $sales)
    {
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $instantPurchase = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchaseItem')->getInstantPurchaseItem($config);
        $html = $this->renderView('MedicineBundle:Sales:instant-purchase.html.twig',
            array(
                'instantPurchase' => $instantPurchase,
                'sales' => $sales
            )
        );
        return New Response($html);
    }

    public function instantPurchaseDeleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $purchase = $this->getDoctrine()->getRepository('MedicineBundle:MedicinePurchase')->find($id);
        /* @var $item MedicinePurchaseItem */
        foreach ($purchase->getMedicinePurchaseItems() as $item ){
          if(empty($item->getSalesQuantity())) {
              $this->removeInstantPurchaseItem($item);
              $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->updateRemovePurchaseQuantity($item->getMedicineStock());
          }
        }
        $em->remove($purchase);
        $em->flush();
        exit;
    }

    public function removeInstantPurchaseItem(MedicinePurchaseItem $item){
        $em = $this->getDoctrine()->getManager();
        $em->remove($item);
        $em->flush();
    }




}
