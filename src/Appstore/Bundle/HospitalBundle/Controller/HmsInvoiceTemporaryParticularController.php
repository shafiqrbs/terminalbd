<?php

namespace Appstore\Bundle\HospitalBundle\Controller;

use Appstore\Bundle\HospitalBundle\Entity\HmsInvoiceTemporaryParticular;
use Appstore\Bundle\HospitalBundle\Entity\Invoice;
use Appstore\Bundle\HospitalBundle\Entity\InvoiceParticular;
use Appstore\Bundle\HospitalBundle\Entity\Particular;
use Appstore\Bundle\HospitalBundle\Form\InvoiceType;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use Core\UserBundle\Entity\User;
use Frontend\FrontentBundle\Service\MobileDetect;
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
class HmsInvoiceTemporaryParticularController extends Controller
{

    public function newAction()
    {
        $user = $this->getUser();
        $hospital = $user->getGlobalOption()->getHospitalConfig();
        $entity = new Invoice();
        $em = $this->getDoctrine()->getManager();
        $form = $this->createInvoiceCustomerForm($entity);
        $services        = $em->getRepository('HospitalBundle:Particular')->getServices($hospital,array(1,8,7));
        $referredDoctors    = $em->getRepository('HospitalBundle:Particular')->findBy(array('hospitalConfig' => $hospital,'status' => 1,'service' => 6),array('name'=>'ASC'));
        $subTotal = $this->getDoctrine()->getRepository('HospitalBundle:HmsInvoiceTemporaryParticular')->getSubTotalAmount($user);
        $html = $this->renderView('HospitalBundle:Invoice:diagnostic.html.twig', array(
            'temporarySubTotal'   => $subTotal,
            'initialDiscount'   => 0,
            'user'   => $user,
            'entity'   => $entity,
            'particularService' => $services,
            'referredDoctors' => $referredDoctors,
            'form'   => $form->createView(),
        ));
        return New Response($html);
    }


    private function createInvoiceCustomerForm(Invoice $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $category = $this->getDoctrine()->getRepository('HospitalBundle:HmsCategory');
        $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $form = $this->createForm(new InvoiceType($globalOption,$category ,$location), $entity, array(
            'action' => $this->generateUrl('hms_invoice_temporary_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal',
                'id' => 'invoicePatientForm',
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
        $hospital = $option->getHospitalConfig();
        $editForm = $this->createInvoiceCustomerForm($entity);
        $editForm->handleRequest($request);
        $referredId = $request->request->get('referredId');
        $discountType = $request->request->get('discountType');
        $data = $request->request->all()['appstore_bundle_hospitalbundle_invoice'];
        $entity->setHospitalConfig($hospital);
        $service = $this->getDoctrine()->getRepository('HospitalBundle:Service')->find(1);
        $entity->setService($service);
        $referredDoctor = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->findOneBy(array('hospitalConfig' => $hospital,'name'=>'Self','service' => 6));
        $entity->setReferredDoctor($referredDoctor);
        $transactionMethod = $em->getRepository('SettingToolBundle:TransactionMethod')->find(1);
        $entity->setTransactionMethod($transactionMethod);
        $entity->setPaymentStatus('Pending');
        $entity->setInvoiceMode('diagnostic');
        $entity->setPrintFor('diagnostic');
        $entity->setCreatedBy($this->getUser());
        if (!empty($data['customer']['name'])) {

            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['customer']['mobile']);
            $customer = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->findHmsExistingCustomerDiagnostic($this->getUser()->getGlobalOption(), $mobile,$data);
            $entity->setCustomer($customer);
            $entity->setMobile($mobile);

        }
        if(!empty($data['referredDoctor']['name']) && !empty($data['referredDoctor']['mobile'])) {

            $mobile = $this->get('settong.toolManageRepo')->specialExpClean($data['referredDoctor']['mobile']);
            $referred = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->findHmsExistingCustomer($hospital , $mobile,$data);
            $entity->setReferredDoctor($referred);

        }else{

            $referred = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->findOneBy(array('hospitalConfig' => $hospital, 'service' => 6, 'id' => $referredId ));
            $entity->setReferredDoctor($referred);

        }
        $deliveryDateTime = $request->request->get('deliveryDateTime');
        $datetime = (new \DateTime("tomorrow"))->format('d-m-Y 7:30');
        $datetime = empty($deliveryDateTime) ? $datetime : $deliveryDateTime ;
        $entity->setDiscountType($discountType);
        $entity->setDeliveryDateTime($datetime);
        if($entity->getTotal() > 0 and $entity->getPayment() > $entity->getTotal() ){
	        $entity->setPayment($entity->getTotal());
	        $entity->setPaymentStatus("Paid");
	        $entity->setDue(0);
        }
	    $entity->setProcess('In-progress');
        $amountInWords = $this->get('settong.toolManageRepo')->intToWords($entity->getTotal());
        $entity->setPaymentInWord($amountInWords);
        $em->persist($entity);
        $em->flush();
        $this->getDoctrine()->getRepository('HospitalBundle:InvoiceParticular')->insertMasterParticular($user,$entity);
        $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->updateInvoiceTotalPrice($entity);
        if($entity->getTotal() > 0) {
            $this->getDoctrine()->getRepository('HospitalBundle:InvoiceTransaction')->insertTransaction($entity);
            $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->updatePaymentReceive($entity);
            $this->getDoctrine()->getRepository('HospitalBundle:Particular')->insertAccessories($entity);
        }
        if($hospital->getInitialDiagnosticShow() != 1){
            $this->getDoctrine()->getRepository('HospitalBundle:HmsInvoiceTemporaryParticular')->removeInitialParticular($user);
        }
        return new Response($entity->getId());
       // return $this->redirect($this->generateUrl('hms_doctor_invoice_confirm',array('id'=>$entity->getId())));

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

