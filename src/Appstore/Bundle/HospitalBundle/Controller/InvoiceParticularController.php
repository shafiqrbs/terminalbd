<?php

namespace Appstore\Bundle\HospitalBundle\Controller;

use Appstore\Bundle\HospitalBundle\Entity\Invoice;
use Appstore\Bundle\HospitalBundle\Entity\InvoiceParticular;
use Appstore\Bundle\HospitalBundle\Entity\InvoicePathologicalReport;
use Appstore\Bundle\HospitalBundle\Entity\Particular;
use Appstore\Bundle\HospitalBundle\Form\InvoiceParticularType;
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
use Hackzilla\BarcodeBundle\Utility\Barcode;
/**
 * InvoiceParticularcontroller.
 *
 */
class InvoiceParticularController extends Controller
{

    public function paginate($entities)
    {

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            25  /*limit per page*/
        );
        return $pagination;
    }

    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;

        $user = $this->getUser();
        $hospital = $user->getGlobalOption()->getHospitalConfig();
        $data = array('process'=>'In-progress');
        $entities = $em->getRepository('HospitalBundle:Invoice')->invoiceLists( $user , $mode = 'pathology' , $data);
        $pagination = $this->paginate($entities);
        $overview = $em->getRepository('HospitalBundle:DoctorInvoice')->findWithOverview($user,$data);
        $assignDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->getFindWithParticular($hospital,array(5));
        $referredDoctors = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->getFindWithParticular($hospital,array(6));

        return $this->render('HospitalBundle:InvoiceParticular:index.html.twig', array(
            'entities' => $pagination,
            'assignDoctors' => $assignDoctors,
            'referredDoctors' => $referredDoctors,
            'searchForm' => $data,
        ));
    }

    public function showAction(Invoice $entity)
    {

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity.');
        }
        return $this->render('HospitalBundle:InvoiceParticular:show.html.twig', array(
            'entity' => $entity,
        ));
    }


    public function preparationAction(InvoiceParticular $entity)
    {

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity preparation.');
        }
        $form = $this->createEditForm($entity);

        /** @var  $reportArr */
        $reportArr = array();

        /** @var InvoicePathologicalReport $row */

        if (!empty($entity->getInvoicePathologicalReports())){
            foreach ($entity->getInvoicePathologicalReports() as $row):
                if(!empty($row->getPathologicalReport())){
                    $reportArr[$row->getPathologicalReport()->getId()] = $row;
                }
            endforeach;
        }

        return $this->render('HospitalBundle:InvoiceParticular:new.html.twig', array(
            'entity' => $entity,
            'report' => $reportArr,
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a form to edit a Invoice entity.wq
     *
     * @param Invoice $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(InvoiceParticular $entity)
    {

        $hospitalConfig = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $form = $this->createForm(new InvoiceParticularType($hospitalConfig), $entity, array(
            'action' => $this->generateUrl('hms_invoice_particular_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Edits an existing Particular entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('HospitalBundle:InvoiceParticular')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Particular entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        $data = $request->request->all();

        if ($editForm->isValid()) {
            
            $entity->setParticularPreparedBy($this->getUser());
            $em->flush();
            $this->getDoctrine()->getRepository('HospitalBundle:InvoicePathologicalReport')->insert($entity,$data);
            $this->get('session')->getFlashBag()->add(
                'success',"Report has been created successfully"
            );

            return $this->redirect($this->generateUrl('hms_invoice_particular_preparation',array('id'=> $entity->getId())));
        }

        return $this->render('HospitalBundle:Pathology:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }


    public function deliveryAction(InvoiceParticular $entity)
    {
        if (!empty($entity)) {
            $em = $this->getDoctrine()->getManager();
            $entity->setParticularDeliveredBy($this->getUser());
            $em->flush();
        }
        return $this->redirect($this->generateUrl('hms_invoice_confirm', array('id' => $entity->getInvoice()->getId())));
    }

    public function pathologicalReportDeleteAction(InvoicePathologicalReport $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Particular entity.');
        }
        $em->remove($entity);
        $em->flush();
        return new  JsonResponse('success');
        exit;

    }

    public function reportStatusSelectAction()
    {
        $items  = array();
        $items[]= array('value' => 'In-progress','text'=>'In-progress');
        $items[]= array('value' => 'Done','text'=>'Done');
        $items[]= array('value' => 'Damage','text'=>'Damage');
        $items[]= array('value' => 'Impossible','text'=>'Impossible');
        return new JsonResponse($items);
    }


}

