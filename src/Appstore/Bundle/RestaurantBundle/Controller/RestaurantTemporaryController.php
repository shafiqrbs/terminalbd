<?php

namespace Appstore\Bundle\RestaurantBundle\Controller;
use Appstore\Bundle\RestaurantBundle\Entity\Invoice;
use Appstore\Bundle\RestaurantBundle\Entity\InvoiceParticular;
use Appstore\Bundle\RestaurantBundle\Entity\Particular;
use Appstore\Bundle\RestaurantBundle\Entity\RestaurantConfig;
use Appstore\Bundle\RestaurantBundle\Entity\RestaurantTemporary;
use Appstore\Bundle\RestaurantBundle\Form\RestaurantTemporaryParticularType;
use Appstore\Bundle\RestaurantBundle\Form\TemporaryType;
use Appstore\Bundle\RestaurantBundle\Service\PosItemManager;
use Core\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;

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
        $subTotal = $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantTemporary')->getSubTotalAmount($user);
        $vat = $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantTemporary')->generateVat($user,$subTotal);
        $html = $this->renderView('RestaurantBundle:Invoice:pos.html.twig', array(
            'temporarySubTotal'   => $subTotal,
            'initialVat'          => $vat,
            'initialDiscount'     => 0,
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
            'action' => $this->generateUrl('restaurant_temporary_create'),
            'method' => 'POST',
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
        $subTotal = $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantTemporary')->getSubTotalAmount($user);
        $data = $request->request->all()['restaurant_invoice'];
        $btn = $request->request->get('buttonType');
        $form = $this->createTemporaryForm($entity);
        $form->handleRequest($request);
        $entity->setRestaurantConfig($config);
        $entity->setPaymentStatus('Pending');
        $entity->setSubTotal($subTotal);
        $entity->setVat($data['vat']);
        $entity->setPayment($data['payment']);
        $total = round($subTotal - $entity->getDiscount() + $entity->getVat());
        $entity->setTotal($total);
        if ($entity->getTotal() > 0) {
            $entity->setProcess('Done');
        }
        $entity->setCreatedBy($this->getUser());
        $deliveryDateTime = $request->request->get('deliveryDateTime');
        $datetime = empty($deliveryDateTime) ? '' : $deliveryDateTime ;
        $entity->setDeliveryDateTime($datetime);
        $customer = $em->getRepository('DomainUserBundle:Customer')->defaultCustomer($option);
        $entity->setCustomer($customer);
        if($entity->getTotal() > 0 and $entity->getPayment() >= $entity->getTotal() ){
            $entity->setPayment($entity->getTotal());
            $entity->setPaymentStatus("Paid");
            $entity->setDue(0);
        }
        $amountInWords = $this->get('settong.toolManageRepo')->intToWords(round($entity->getTotal()));
        $entity->setPaymentInWord($amountInWords);
        $em->persist($entity);
        $em->flush();
        $returnEntity = $this->getDoctrine()->getRepository('RestaurantBundle:InvoiceParticular')->initialInvoiceItems($user,$entity);
        $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantTemporary')->removeInitialParticular($this->getUser());
        $this->getDoctrine()->getRepository('RestaurantBundle:Particular')->insertAccessories($entity);
        $accountInvoice = $em->getRepository('AccountingBundle:AccountSales')->insertRestaurantAccountInvoice($entity);
        $em->getRepository('AccountingBundle:Transaction')->restaurantSalesTransaction($entity, $accountInvoice);
        if($btn == "posBtn"){
            $pos = $this->posPrint($returnEntity->getId());
            return new Response($pos);
        }
        exit;

    }

    public function invoiceDiscountUpdateAction(Request $request)
    {
        $user = $this->getUser();
        $discount = (float)$request->request->get('discount');
        $discountType = $request->request->get('discountType');
        $subTotal = $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantTemporary')->getSubTotalAmount($user);
        if($discountType == 'flat'){
            $initialGrandTotal = ($subTotal  - $discount);
        }else{
            $discount = ($subTotal * $discount)/100;
            $initialGrandTotal = ($subTotal  - $discount);
        }
        $vat = $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantTemporary')->generateVat($user,$initialGrandTotal);
        $data = array(
            'subTotal' => $subTotal,
            'initialGrandTotal' => round($initialGrandTotal + $vat),
            'initialDiscount' => $discount,
            'initialVat' => $vat,
            'success' => 'success'
        );
        return new Response(json_encode($data));
        exit;

    }

    public function invoiceDiscountCouponAction(Request $request)
    {
        $user = $this->getUser();
        /* @var $config RestaurantConfig */
        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        $discount = $request->request->get('discount');
        $subTotal = $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantTemporary')->getSubTotalAmount($user);
        if($config->getDiscountType() == 'flat' and !empty($discount)){
            $initialGrandTotal = ($subTotal  - $config->getDiscountPercentage());
        }elseif($config->getDiscountType() == 'percentage' and !empty($discount)){
            $discount = ($subTotal * $config->getDiscountPercentage())/100;
            $initialGrandTotal = ($subTotal  - $discount);
        }
        $vat = $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantTemporary')->generateVat($user,$initialGrandTotal);
        $data = array(
            'subTotal' => $subTotal,
            'initialGrandTotal' => round($initialGrandTotal + $vat),
            'initialDiscount' => $discount,
            'initialVat' => $vat,
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

        $invoiceParticulars = $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantTemporary')->getSalesItems($user);
        $subTotal = $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantTemporary')->getSubTotalAmount($user);
        $vat = $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantTemporary')->generateVat($user,$subTotal);
        $data = array(
           'subTotal'           => $subTotal,
           'initialGrandTotal'  => round($subTotal + $vat),
           'invoiceParticulars' => $invoiceParticulars ,
           'initialVat'         => $vat ,
           'msg'                => $msg ,
           'success'            => 'success'
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
        $this->getDoctrine()->getRepository('RestaurantBundle:RestaurantTemporary')->insertInvoiceItems($user, $invoiceItems);
        $result = $this->returnResultData($user);
        return new Response(json_encode($result));
        exit;

    }

    public function invoiceParticularDeleteAction(RestaurantTemporary $particular){


        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        if (!$particular) {
            throw $this->createNotFoundException('Unable to find SalesItem entity.');
        }
        $em->remove($particular);
        $em->flush();
        $result = $this->returnResultData($user);
        return new Response(json_encode($result));
        exit;
    }

    private function posPrint($id)
    {
        $entity = $this->getDoctrine()->getRepository('RestaurantBundle:Invoice')->find($id);
        $response = $this->getDoctrine()->getRepository('RestaurantBundle:Invoice')->posPrint($entity);
        return $response;
        //return new Response($response);
    }

}

