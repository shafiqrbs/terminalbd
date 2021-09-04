<?php

namespace Appstore\Bundle\BusinessBundle\Controller;
use Appstore\Bundle\BusinessBundle\Entity\BusinessConfig;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoiceReturnItem;
use Appstore\Bundle\BusinessBundle\Entity\BusinessStore;
use Appstore\Bundle\BusinessBundle\Entity\BusinessStoreLedger;
use Knp\Snappy\Pdf;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoice;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoiceParticular;
use Appstore\Bundle\BusinessBundle\Entity\BusinessParticular;
use Appstore\Bundle\BusinessBundle\Form\InvoiceType;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * BusinessInvoiceController controller.
 *
 */
class DistributorController extends Controller
{


    /**
     * @Secure(roles="ROLE_BUSINESS_INVOICE,ROLE_DOMAIN");
     */
    public function storePaymentAction(Request $request, BusinessInvoice $invoice)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $storeId = $data['store'];
        $transaction = $data['transactionType'];
        $amount = $data['amount'];
        $store = $this->getDoctrine()->getRepository("BusinessBundle:BusinessStore")->find($storeId);
        $entity = new BusinessStoreLedger();
        $entity->setBusinessConfig($invoice->getBusinessConfig());
        $entity->setInvoice($invoice);
        $entity->setAmount($amount);
        $entity->setStore($store);
        $entity->setTransactionType($transaction);
        if($transaction == 'Receive' || $transaction == 'Adjustment') {
            $entity->setAmount("-{$entity->getAmount()}");
            $entity->setCredit(abs($entity->getAmount()));
        }else{
            $entity->setDebit($entity->getAmount());
        }
        $accountConfig = $this->getUser()->getGlobalOption()->getAccountingConfig()->isAccountClose();
        if($accountConfig == 1){
            $datetime = new \DateTime("yesterday 23:30:30");
            $entity->setCreated($datetime);
            $entity->setUpdated($datetime);
        }else{
            $datetime = new \DateTime("now");
            $entity->setUpdated($datetime);
        }
        $em->persist($entity);
        $em->flush();
        $data = $this->render("BusinessBundle:BusinessStore:store-ledger.html.twig", array(
            'entity' => $invoice
        ));
        return new Response($data);

    }

    /**
     * @Secure(roles="ROLE_BUSINESS_INVOICE,ROLE_DOMAIN");
     */
    public function salesReturnItemAction(Request $request, BusinessInvoice $invoice)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $itemId = $data['itemId'];
        $quantity = $data['quantity'];
        $amount = $data['amount'];
        $item = $this->getDoctrine()->getRepository("BusinessBundle:BusinessParticular")->find($itemId);
        $entity = new BusinessInvoiceReturnItem();
        $entity->setInvoice($invoice);
        $entity->setParticular($item);
        $entity->setQuantity($quantity);
        $entity->setPrice($amount/$quantity);
        $entity->setSubTotal($amount);
        $em->persist($entity);
        $em->flush();
        $data = $this->render("BusinessBundle:Distribution:invoice-return-item.html.twig", array(
            'entity' => $invoice
        ));
        return new Response($data);

    }

    /**
     * @Secure(roles="ROLE_BUSINESS_INVOICE,ROLE_DOMAIN");
     */
    public function deleteLedgerAction(BusinessInvoice $invoice, BusinessStoreLedger $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $em->remove($entity);
        $em->flush();
        $data = $this->render("BusinessBundle:Distribution:store-ledger.html.twig", array(
            'entity' => $invoice
        ));
        return new Response($data);
    }


    /**
     * @Secure(roles="ROLE_BUSINESS_INVOICE,ROLE_DOMAIN");
     */
    public function storeCreateAction(Request $request)
    {
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $name = $data['store'];
        $area = $data['area'];
        $mobile = $data['storeMobile'];
        $customer = $data['dsm'];
        $customerId = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->find($customer);
        $areaId = $this->getDoctrine()->getRepository('BusinessBundle:BusinessArea')->find($area);
        $mobileno = $this->get('settong.toolManageRepo')->specialExpClean($mobile);
        $find = $this->getDoctrine()->getRepository('BusinessBundle:BusinessStore')->findOneBy(array('businessConfig' => $config,'area'=> $area,'name'=>$name));
        if(empty($find)){
            $entity = new BusinessStore();
            $entity->setName($name);
            $entity->setMobileNo($mobileno);
            $entity->setCustomer($customerId);
            $entity->setArea($areaId);
            $em->persist($entity);
            $em->flush();
        }
        $areas = $this->getDoctrine()->getRepository('BusinessBundle:BusinessArea')->findBy(array('businessConfig'=>$config),array('name'=>'ASC'));
        $select= "";
        foreach ($areas as $area){
            $select .="<optgroup label='{$area->getName()}'>";
            if($area->getStores()){
                foreach ($area->getStores() as $store){
                    $select .="<option value='{$store->getId()}'>{$store->getName()}</option>";
                }
            }
            $select .="</optgroup>";
        }
        return new Response($select);
    }



}

