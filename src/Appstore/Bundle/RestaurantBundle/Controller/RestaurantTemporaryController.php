<?php

namespace Appstore\Bundle\RestaurantBundle\Controller;
use Appstore\Bundle\RestaurantBundle\Entity\Invoice;
use Appstore\Bundle\RestaurantBundle\Entity\RestaurantTemporary;
use Appstore\Bundle\RestaurantBundle\Form\RestaurantTemporaryParticularType;
use Appstore\Bundle\RestaurantBundle\Form\TemporaryType;
use Core\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * RestaurantTemporary controller.
 *
 */
class RestaurantTemporaryController extends Controller
{

    public function newAction()
    {
        $user = $this->getUser();
        $config = $user->getGlobalOption()->getRestaurantConfig();
        $entity = new Invoice();
        $form = $this->createTemporaryForm($entity);
        $itemForm = $this->createInvoiceParticularForm(New RestaurantTemporary());
        $subTotal = $this->getDoctrine()->getRepository('HospitalBundle:HmsInvoiceTemporaryParticular')->getSubTotalAmount($user);
        $html = $this->renderView('RestaurantBundle:Invoice:pos.html.twig', array(
            'temporarySubTotal'   => $subTotal,
            'initialDiscount'   => 0,
            'user'      => $user,
            'entity'    => $entity,
            'form'      => $form->createView(),
            'itemForm'  => $itemForm->createView(),
        ));
        return New Response($html);
    }

    private function createTemporaryForm(Invoice $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new TemporaryType($globalOption), $entity, array(
            'action' => $this->generateUrl('restaurant_temporary_new', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'id' => 'invoiceForm',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    private function createInvoiceParticularForm(RestaurantTemporary $entity)
    {
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        $form = $this->createForm(new RestaurantTemporaryParticularType($config), $entity, array(
            'action' => $this->generateUrl('restaurant_temporary_particular_add'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'id' => 'particularForm',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = New Invoice();
        $user = $this->getUser();
        $option = $user->getGlobalOption();
        $config = $user->getGlobalOption()->getRestaurantConfig();
        $discountType = $request->request->get('discountType');
        $data = $request->request->all()['restaurant_invoice'];
        $entity->setHospitalConfig($config);
        $transactionMethod = $em->getRepository('SettingToolBundle:TransactionMethod')->find(1);
        $entity->setTransactionMethod($transactionMethod);
        $entity->setPaymentStatus('Pending');
        if ($entity->getTotal() > 0) {
            $entity->setProcess('Kitchen');
        }
        $entity->setCreatedBy($this->getUser());
        if (!empty($data['customer']['name'])) {
            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['customer']['mobile']);
            $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findHmsExistingCustomerDiagnostic($this->getUser()->getGlobalOption(), $mobile,$data);
            $entity->setCustomer($customer);
            $entity->setMobile($mobile);
        }
        $deliveryDateTime = $request->request->get('deliveryDateTime');
        $datetime = (new \DateTime("now"))->format('d-m-Y 7:30 A');
        $datetime = empty($deliveryDateTime) ? $datetime : $deliveryDateTime ;
        $entity->setDiscountType($discountType);
        $entity->setDeliveryDateTime($datetime);
        if($entity->getTotal() > 0 and $entity->getPayment() >= $entity->getTotal() ){
	        $entity->setPayment($entity->getTotal());
	        $entity->setPaymentStatus("Paid");
	        $entity->setDue(0);
        }
        $amountInWords = $this->get('settong.toolManageRepo')->intToWords($entity->getTotal());
        $entity->setPaymentInWord($amountInWords);
        $em->persist($entity);
        $em->flush();
        $this->getDoctrine()->getRepository('RestaurantBundle:InvoiceParticular')->insertInvoiceItems($user,$entity);
        return new Response($entity->getId());
        exit;

    }

    public function invoiceDiscountUpdateAction(Request $request)
    {
        $user = $this->getUser();
        $discount = (float)$request->request->get('discount');
        $discountType = $request->request->get('discountType');
        $subTotal = $this->getDoctrine()->getRepository('HospitalBundle:HmsInvoiceTemporaryParticular')->getSubTotalAmount($user);
        if($discountType == 'flat'){
            $initialGrandTotal = ($subTotal  - $discount);
        }else{
            $discount = ($subTotal * $discount)/100;
            $initialGrandTotal = ($subTotal  - $discount);
        }

        $data = array(
            'subTotal' => $subTotal,
            'initialGrandTotal' => $initialGrandTotal,
            'initialDiscount' => $discount,
            'success' => 'success'
        );
        return new Response(json_encode($data));
        exit;

    }

    public function particularSearchAction(Particular $particular)
    {
        return new Response(json_encode(array('particularId'=> $particular->getId() ,'price'=> $particular->getPrice() , 'quantity'=> $particular->getQuantity(), 'minimumPrice'=> $particular->getMinimumPrice(), 'instruction'=> $particular->getInstruction())));
    }

    public function returnResultData(User $user,$msg=''){

        $invoiceParticulars = $this->getDoctrine()->getRepository('HospitalBundle:HmsInvoiceTemporaryParticular')->getSalesItems($user);
        $subTotal = $this->getDoctrine()->getRepository('HospitalBundle:HmsInvoiceTemporaryParticular')->getSubTotalAmount($user);
        $data = array(
           'subTotal' => $subTotal,
           'initialGrandTotal' => $subTotal,
           'invoiceParticulars' => $invoiceParticulars ,
           'msg' => $msg ,
           'success' => 'success'
       );
       return $data;

    }

    public function addParticularAction(Request $request)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $particularId = $request->request->get('particularId');
        $quantity = $request->request->get('quantity');
        $price = $request->request->get('price');
        $invoiceItems = array('particularId' => $particularId , 'quantity' => $quantity,'price' => $price );
        $this->getDoctrine()->getRepository('HospitalBundle:HmsInvoiceTemporaryParticular')->insertInvoiceItems($user, $invoiceItems);
        $msg = 'Particular added successfully';
        $result = $this->returnResultData($user,$msg);
        return new Response(json_encode($result));
        exit;

    }

    public function invoiceParticularDeleteAction(HmsInvoiceTemporaryParticular $particular){


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

