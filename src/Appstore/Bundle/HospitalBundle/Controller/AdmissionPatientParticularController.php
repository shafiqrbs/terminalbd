<?php

namespace Appstore\Bundle\HospitalBundle\Controller;

use Appstore\Bundle\HospitalBundle\Entity\AdmissionPatientParticular;
use Appstore\Bundle\HospitalBundle\Entity\Invoice;
use Appstore\Bundle\HospitalBundle\Entity\InvoiceParticular;
use Appstore\Bundle\HospitalBundle\Entity\InvoiceTransaction;
use Appstore\Bundle\HospitalBundle\Entity\Particular;
use Appstore\Bundle\HospitalBundle\Form\InvoiceAdmissionType;
use Appstore\Bundle\HospitalBundle\Form\InvoiceType;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use Frontend\FrontentBundle\Service\MobileDetect;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Invoice controller.
 *
 */
class AdmissionPatientParticularController extends Controller
{

    public function invoiceAction($invoice)
    {
        $em = $this->getDoctrine()->getManager();
        $hospital = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $entity = $em->getRepository('HospitalBundle:Invoice')->findOneBy(array('hospitalConfig' => $hospital , 'invoice' => $invoice));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $services        = $em->getRepository('HospitalBundle:Particular')->getServices($hospital,array(1,2,3,4,7));
        return $this->render('HospitalBundle:InvoiceAdmission:admissionPatientParticular.html.twig', array(
            'entity' => $entity,
            'particularService' => $services,
        ));
    }

    public function admittedInvoiceAction($invoice)
    {
        $em = $this->getDoctrine()->getManager();
        $hospital = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $entity = $em->getRepository('HospitalBundle:Invoice')->findOneBy(array('hospitalConfig' => $hospital , 'invoice' => $invoice));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $code = $this->getDoctrine()->getRepository('HospitalBundle:InvoiceTransaction')->getLastCode($entity);
        $it = new InvoiceTransaction();
        $it->setCode($code + 1);
        $transactionCode = sprintf("%s", str_pad($it->getCode(),2, '0', STR_PAD_LEFT));
        $it->setTransactionCode($transactionCode);
        $transactionMethod = $em->getRepository('SettingToolBundle:TransactionMethod')->find(1);
        $it->setTransactionMethod($transactionMethod);
        $it->setProcess('Pending');
        $it->setHmsInvoice($entity);
        $it->setCreatedBy($this->getUser());
        $em->persist($it);
        $em->flush();
        return $this->redirect($this->generateUrl('hms_invoice_admission_daily_invoice_create', array('invoice' => $entity->getInvoice(),'id' => $it->getId())));

    }

    public function dailyAdmittedInvoiceAction($invoice, InvoiceTransaction $transaction)
    {


        $em = $this->getDoctrine()->getManager();
        $hospital = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $entity = $em->getRepository('HospitalBundle:Invoice')->findOneBy(array('hospitalConfig' => $hospital,'invoice' => $invoice));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        if($transaction->getProcess() == 'In-progress'){
            return $this->redirect($this->generateUrl('hms_invoice_admission_confirm', array('id' => $transaction->getHmsInvoice()->getId())));
        }

        $services        = $em->getRepository('HospitalBundle:Particular')->getServices($hospital,array(1,2,3,4,7));
        return $this->render('HospitalBundle:InvoiceAdmission:confirm.html.twig', array(
            'entity' => $entity,
            'invoiceTransaction' => $transaction,
            'particularService' => $services,
        ));
    }

    public function addParticularAction(Request $request, InvoiceTransaction $transaction)
    {

        $em = $this->getDoctrine()->getManager();
        $particularId = $request->request->get('particularId');
        $quantity = $request->request->get('quantity');
        $price = $request->request->get('price');
        $invoiceItems = array('particularId' => $particularId , 'quantity' => $quantity,'price' => $price );
        $this->getDoctrine()->getRepository('HospitalBundle:AdmissionPatientParticular')->insertInvoiceItems($transaction, $invoiceItems);
        $this->getDoctrine()->getRepository('HospitalBundle:AdmissionPatientParticular')->updateInvoiceTransactionTotalPrice($transaction);
        $msg = 'Particular added successfully';
        $result = $this->returnResultData($transaction,$msg);
        return new Response(json_encode($result));
        exit;

    }

    public function returnResultData(InvoiceTransaction $entity,$msg=''){

        $invoiceParticulars = $this->getDoctrine()->getRepository('HospitalBundle:AdmissionPatientParticular')->getSalesItems($entity);
        $data = array(
            'total' => $entity->getTotal() ,
            'invoiceParticulars' => $invoiceParticulars ,
            'msg' => $msg ,
            'success' => 'success'
        );
        return $data;

    }


    public function invoiceParticularDeleteAction(InvoiceTransaction $transaction, AdmissionPatientParticular $patientParticular)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$patientParticular) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $em->remove($patientParticular);
        $em->flush();
        $this->getDoctrine()->getRepository('HospitalBundle:AdmissionPatientParticular')->updateInvoiceTransactionTotalPrice($transaction);
        $total = $transaction->getTotal();
        return new Response(json_encode(array('success' => 'success','total'=>$total)));
        exit;
    }

    public function submitTransactionAction(Request $request , InvoiceTransaction $transaction)
    {
        $em = $this->getDoctrine()->getManager();
        $payment = $request->request->get('payment');
        $transaction->setProcess('Done');
        $transaction->setPayment($payment);
        $em->persist($transaction);
        $em->flush();
        foreach ($transaction->getAdmissionPatientParticulars() as $patientParticular ){
            $this->getDoctrine()->getRepository('HospitalBundle:InvoiceParticular')->insertInvoiceParticularMasterUpdate($patientParticular);
        }
        if($transaction->getPayment() > 0){
            $this->getDoctrine()->getRepository('HospitalBundle:InvoiceTransaction')->admissionInvoiceTransactionUpdate($transaction);
        }
        $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->updateInvoiceTotalPrice($transaction->getHmsInvoice());
        $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->updatePaymentReceive($transaction->getHmsInvoice());
        $this->getDoctrine()->getRepository('HospitalBundle:Particular')->setAdmissionPatientUpdateQnt($transaction);
        return $this->redirect($this->generateUrl('hms_invoice_admission_confirm', array('id' => $transaction->getHmsInvoice()->getId())));
    }

    public function invoiceTransactionDeleteAction(InvoiceTransaction $transaction)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$transaction) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $em->remove($transaction);
        $em->flush();
        exit;
    }

     public function invoiceTransactionApproveAction(InvoiceTransaction $transaction)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$transaction) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        $transaction->setProcess('Done');
        $em->persist($transaction);
        $em->flush();
        foreach ($transaction->getAdmissionPatientParticulars() as $patientParticular ){
            $this->getDoctrine()->getRepository('HospitalBundle:InvoiceParticular')->insertInvoiceParticularMasterUpdate($patientParticular);
        }
        if($transaction->getPayment() > 0){
            $this->getDoctrine()->getRepository('HospitalBundle:InvoiceTransaction')->admissionInvoiceTransactionUpdate($transaction);
        }
        $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->updateInvoiceTotalPrice($transaction->getHmsInvoice());
        $this->getDoctrine()->getRepository('HospitalBundle:Invoice')->updatePaymentReceive($transaction->getHmsInvoice());
        $this->getDoctrine()->getRepository('HospitalBundle:Particular')->setAdmissionPatientUpdateQnt($transaction);
        exit;
    }

    public function getBarcode($value)
    {
        $barcode = new BarcodeGenerator();
        $barcode->setText($value);
        $barcode->setType(BarcodeGenerator::Code39Extended);
        $barcode->setScale(1);
        $barcode->setThickness(25);
        $barcode->setFontSize(8);
        $code = $barcode->generate();
        $data = '';
        $data .= '<img src="data:image/png;base64,'.$code .'" />';
        return $data;
    }


    public function singleTransactionInvoicePrintAction($invoice, InvoiceTransaction $transaction)
    {
        $em = $this->getDoctrine()->getManager();
        $hospital = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $entity = $em->getRepository('HospitalBundle:Invoice')->findOneBy(array('hospitalConfig' => $hospital,'invoice' => $invoice));
        $barcode = $this->getBarcode($entity->getInvoice());
        $patientId = $this->getBarcode($entity->getCustomer()->getCustomerId());
        $inWords = $this->get('settong.toolManageRepo')->intToWords($entity->getPayment());

        $invoiceDetails = ['Pathology' => ['items' => [], 'total'=> 0, 'hasQuantity' => false ]];

        foreach ($transaction->getAdmissionPatientParticulars() as $item) {
            /** @var InvoiceParticular $item */
            $serviceName = $item->getParticular()->getService()->getName();
            $hasQuantity = $item->getParticular()->getService()->getHasQuantity();

            if(!isset($invoiceDetails[$serviceName])) {
                $invoiceDetails[$serviceName]['items'] = [];
                $invoiceDetails[$serviceName]['total'] = 0;
                $invoiceDetails[$serviceName]['hasQuantity'] = ($hasQuantity == 1);
            }

            $invoiceDetails[$serviceName]['items'][] = $item;
            $invoiceDetails[$serviceName]['total'] += $item->getSubTotal();
        }

        if(count($invoiceDetails['Pathology']['items']) == 0) {
            unset($invoiceDetails['Pathology']);
        }
        $inWordTransaction = $this->get('settong.toolManageRepo')->intToWords($transaction->getPayment());

        return $this->render('HospitalBundle:Print:payment.html.twig', array(
            'entity'                => $entity,
            'invoiceDetails'        => $invoiceDetails,
            'invoiceBarcode'        => $barcode,
            'patientBarcode'        => $patientId,
            'inWords'               => $inWords,
            'transaction'           => $transaction,
            'inWordTransaction'     => $inWordTransaction,
        ));
    }

    public function transactionInvoicePrintAction($invoice)
    {
        $em = $this->getDoctrine()->getManager();
        $hospital = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $entity = $em->getRepository('HospitalBundle:Invoice')->findOneBy(array('hospitalConfig' => $hospital,'invoice' => $invoice));

        $barcode = $this->getBarcode($entity->getInvoice());
        $patientId = $this->getBarcode($entity->getCustomer()->getCustomerId());
        $inWords = $this->get('settong.toolManageRepo')->intToWords($entity->getPayment());
        return $this->render('HospitalBundle:Print:payments.html.twig', array(
            'entity'                => $entity,
            'invoiceBarcode'        => $barcode,
            'patientBarcode'        => $patientId,
            'inWords'               => $inWords,
        ));
    }

    public function invoiceParticularPrintAction($invoice)
    {
        $em = $this->getDoctrine()->getManager();
        $hospital = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $entity = $em->getRepository('HospitalBundle:Invoice')->findOneBy(array('hospitalConfig' => $hospital,'invoice' => $invoice));

        $barcode = $this->getBarcode($entity->getInvoice());
        $patientId = $this->getBarcode($entity->getCustomer()->getCustomerId());
        $inWords = $this->get('settong.toolManageRepo')->intToWords($entity->getPayment());

        $invoiceDetails = ['Pathology' => ['items' => [], 'total'=> 0, 'hasQuantity' => false ]];

        foreach ($entity->getInvoiceParticulars() as $item) {
            /** @var InvoiceParticular $item */
            $serviceName = $item->getParticular()->getService()->getName();
            $hasQuantity = $item->getParticular()->getService()->getHasQuantity();

            if(!isset($invoiceDetails[$serviceName])) {
                $invoiceDetails[$serviceName]['items'] = [];
                $invoiceDetails[$serviceName]['total'] = 0;
                $invoiceDetails[$serviceName]['hasQuantity'] = ( $hasQuantity == 1);
            }

            $invoiceDetails[$serviceName]['items'][] = $item;
            $invoiceDetails[$serviceName]['total'] += $item->getSubTotal();
        }

        if(count($invoiceDetails['Pathology']['items']) == 0) {
            unset($invoiceDetails['Pathology']);
        }
        return $this->render('HospitalBundle:Print:invoiceParticular.html.twig', array(
            'entity'                => $entity,
            'invoiceDetails'        => $invoiceDetails,
            'invoiceBarcode'        => $barcode,
            'patientBarcode'        => $patientId,
            'inWords'               => $inWords,
        ));
    }




}

