<?php

namespace Appstore\Bundle\MedicineBundle\Controller;

use Appstore\Bundle\MedicineBundle\Entity\MedicineSalesItem;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSalesTemporary;
use Appstore\Bundle\MedicineBundle\Form\SalesTemporaryItemType;
use Appstore\Bundle\MedicineBundle\Form\SalesTemporaryType;
use Appstore\Bundle\MedicineBundle\Form\SalesItemType;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSales;
use Core\UserBundle\Entity\User;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Invoice controller.
 *
 */
class MedicineSalesTemporaryController extends Controller
{



    public function newAction()
    {

        $user = $this->getUser();
        $entity = new MedicineSales();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MedicineSales entity.');
        }
        $salesItemForm = $this->createMedicineSalesItemForm(new MedicineSalesItem());
        $editForm = $this->createCreateForm($entity);
        $subTotal = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesTemporary')->getSubTotalAmount($user);

        $html = $this->renderView('MedicineBundle:Sales:temporary.html.twig', array(
            'entity' => $entity,
            'salesItem' => $salesItemForm->createView(),
            'form' => $editForm->createView(),
            'user'   => $user,
            'subTotal'   => $subTotal,
        ));
        return New Response($html);
    }

    /**
     * Creates a form to edit a MedicineSales entity.wq
     *
     * @param MedicineSales $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(MedicineSales $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $form = $this->createForm(new SalesTemporaryType($globalOption,$location), $entity, array(
            'action' => $this->generateUrl('medicine_sales_temporary_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'id' => 'salesTemporaryForm',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    private function createMedicineSalesItemForm(MedicineSalesItem $salesItem )
    {

        $form = $this->createForm(new SalesTemporaryItemType(), $salesItem, array(
            'action' => $this->generateUrl('medicine_sales_temporary_item_add'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'id' => 'salesTemporaryItemForm',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        if( 0 == $data['salesSubTotal']){
	        $data = array( 'sales' => '',
	                      'process' => 'save',
	                      'success' => 'invalid');
	        return new Response(json_encode($data));
	        exit;
        }
        $entity = New MedicineSales();
        $user = $this->getUser();
        $config = $user->getGlobalOption()->getMedicineConfig();
        $editForm = $this->createCreateForm($entity);
        $editForm->handleRequest($request);
        $entity->setMedicineConfig($config);

        $customer = $em->getRepository('DomainUserBundle:Customer')->defaultCustomer($user->getGlobalOption());
        $entity->setCustomer($customer);
        $globalOption = $this->getUser()->getGlobalOption();
        if (!empty($data['customerMobile'])) {
            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['customerMobile']);
            $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->newExistingCustomerForSales($globalOption, $mobile, $data);
            $entity->setCustomer($customer);

        } elseif (!empty($data['mobile'])) {

            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['mobile']);
            $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $globalOption, 'mobile' => $mobile));
            $entity->setCustomer($customer);

        }
        $entity->setSubTotal(round($data['salesSubTotal']));
        $entity->setNetTotal(round($data['salesNetTotal']));
        if ($entity->getNetTotal() <= $entity->getReceived()) {
            $entity->setReceived($entity->getNetTotal());
            $entity->setPaymentStatus('Paid');
            $entity->setDue(0);
        } else {
            $entity->setPaymentStatus('Due');
            $entity->setDue($entity->getNetTotal() - $entity->getReceived());
        }
        if ($data['process'] == 'hold') {
            $entity->setProcess('Hold');
        } else {
            $entity->setApprovedBy($this->getUser());
            $entity->setProcess('Done');
        }
        $em->persist($entity);
        $em->flush();
        $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesItem')->temporarySalesInsert($user, $entity);
        $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesTemporary')->removeSalesTemporary($this->getUser());
        if ($entity->getProcess() == 'Done'){
            $accountSales = $this->getDoctrine()->getRepository('AccountingBundle:AccountSales')->insertMedicineAccountInvoice($entity);
            $em->getRepository('AccountingBundle:Transaction')->salesGlobalTransaction($accountSales);
        }
        $data = array(
            'sales' => $entity->getId(),
            'process' => $data['process'],
            'success' => 'success'
        );
        return new Response(json_encode($data));
        exit;

    }


    public function invoiceDiscountUpdateAction(Request $request)
    {
        $user = $this->getUser();
        $discount = $request->request->get('discount');
        $discountType = $request->request->get('discountType');
        $subTotal = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesTemporary')->getSubTotalAmount($user);
        if($discountType == 'flat'){
            $initialDiscount = round($discount);
            $initialGrandTotal =round ($subTotal  - $initialDiscount);
        }else{
            $initialDiscount = round(($subTotal * $discount)/100);
            $initialGrandTotal = round($subTotal  - $initialDiscount);
        }

        $data = array(
            'subTotal' => round($subTotal),
            'initialGrandTotal' => round($initialGrandTotal),
            'initialDiscount' => $initialDiscount,
            'success' => 'success'
        );
        return new Response(json_encode($data));
        exit;
    }

    public function returnResultData(User $user,$msg=''){

        $salesItems = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesTemporary')->getSalesItems($user);
        $subTotal = $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesTemporary')->getSubTotalAmount($user);
        $data = array(
           'subTotal' => $subTotal,
           'initialGrandTotal' => $subTotal,
           'discount' => $subTotal,
           'salesItems' => $salesItems ,
           'msg' => $msg ,
           'success' => 'success'
       );
       return $data;

    }

    public function addParticularAction(Request $request)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all()['salesTemporaryItem'];
        $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesTemporary')->insertInvoiceItems($user, $data);
        $msg = 'Particular added successfully';
        $result = $this->returnResultData($user,$msg);
        return new Response(json_encode($result));
        exit;

    }

    public function invoiceItemUpdateAction(Request $request)
    {
        $user = $this->getUser();
        $data = $request->request->all();
        $this->getDoctrine()->getRepository('MedicineBundle:MedicineSalesTemporary')->updateInvoiceItems($user, $data);
        $msg = 'Particular added successfully';
        $result = $this->returnResultData($user,$msg);
        return new Response(json_encode($result));
        exit;
    }


    public function invoiceParticularDeleteAction(MedicineSalesTemporary $particular){
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        if (!$particular) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }
        $em->remove($particular);
        $em->flush();
        $msg = 'Particular deleted successfully';
        $result = $this->returnResultData($user,$msg);
        return new Response(json_encode($result));
        exit;
    }

}

