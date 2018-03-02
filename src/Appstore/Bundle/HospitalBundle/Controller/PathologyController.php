<?php

namespace Appstore\Bundle\HospitalBundle\Controller;

use Appstore\Bundle\HospitalBundle\Entity\HmsMasterDiagnosticReport;
use Appstore\Bundle\HospitalBundle\Entity\HmsMasterDiagnosticReportFormat;
use Appstore\Bundle\HospitalBundle\Entity\Particular;
use Appstore\Bundle\HospitalBundle\Entity\PathologicalReport;
use Appstore\Bundle\HospitalBundle\Form\ParticularType;
use Appstore\Bundle\HospitalBundle\Form\PathologicalReportType;
use Appstore\Bundle\HospitalBundle\Form\PathologyType;
use Appstore\Bundle\MedicineBundle\Entity\DiagnosticReport;
use Appstore\Bundle\MedicineBundle\Entity\DiagnosticReportFormat;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * Pathology controller.
 *
 */
class PathologyController extends Controller
{

    public function paginate($entities)
    {
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            50  /*limit per page*/
        );
        $pagination->setTemplate('SettingToolBundle:Widget:pagination.html.twig');
        return $pagination;
    }


    /**
     * Lists all Particular entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $config = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $entities = $em->getRepository('HospitalBundle:Particular')->findWithSearch($config , $service = 1, $data);
        $pagination = $this->paginate($entities);
        $categories = $this->getDoctrine()->getRepository('HospitalBundle:HmsCategory')->findBy(array('parent'=>2),array('name' =>'asc' ));
        $departments = $this->getDoctrine()->getRepository('HospitalBundle:HmsCategory')->findBy(array('parent'=>7),array('name' =>'asc' ));
        return $this->render('HospitalBundle:Pathology:index.html.twig', array(
            'entities' => $pagination,
            'categories' => $categories,
            'departments' => $departments,
            'searchForm' => $data,
        ));

    }

    /**
     * Creates a new Particular entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Particular();
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createCreateForm($entity,$globalOption);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setHospitalConfig($globalOption -> getHospitalConfig());
            $service = $this->getDoctrine()->getRepository('HospitalBundle:Service')->find(1);
            $entity->setService($service);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('hms_pathology_new', array('id' => $entity->getId())));
        }

        return $this->render('HospitalBundle:Pathology:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Particular entity.
     *
     * @param Particular $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Particular $entity, $globalOption)
    {

        $em = $this->getDoctrine()->getRepository('HospitalBundle:HmsCategory');
        $form = $this->createForm(new PathologyType($em,$globalOption), $entity, array(
            'action' => $this->generateUrl('hms_pathology_create', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new Particular entity.
     *
     */
    public function newAction()
    {
        $entity = new Particular();
        $globalOption = $this->getUser()->getGlobalOption();
        $form   = $this->createCreateForm($entity,$globalOption);

        return $this->render('HospitalBundle:Pathology:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Particular entity.
     *
     */
    public function showAction($id)
    {

    }

    /**
     * Displays a form to edit an existing Particular entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('HospitalBundle:Particular')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Particular entity.');
        }
        $globalOption = $this->getUser()->getGlobalOption();
        $editForm = $this->createEditForm($entity,$globalOption);

        return $this->render('HospitalBundle:Pathology:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }


    /**
     * Creates a form to edit a Particular entity.
     *
     * @param Particular $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Particular $entity,$globalOption)
    {
        $em = $this->getDoctrine()->getRepository('HospitalBundle:HmsCategory');
        $form = $this->createForm(new PathologyType($em,$globalOption), $entity, array(
            'action' => $this->generateUrl('hms_pathology_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
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

        $entity = $em->getRepository('HospitalBundle:Particular')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Particular entity.');
        }

        $globalOption = $this->getUser()->getGlobalOption();
        $editForm = $this->createEditForm($entity,$globalOption);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('hms_pathology'));
        }

        return $this->render('HospitalBundle:Pathology:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a Particular entity.
     *
     */
    public function deleteAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('HospitalBundle:Particular')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Particular entity.');
        }
        $entity->setIsDelete(1);
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'error',"Data has been deleted successfully"
        );
        return $this->redirect($this->generateUrl('hms_pathology'));
    }

   
    /**
     * Status a Page entity.
     *
     */
    public function statusAction(Particular $entity)
    {

        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find District entity.');
        }

        $status = $entity->getStatus();
        if($status == 1){
            $entity->setStatus(0);
        } else{
            $entity->setStatus(1);
        }
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',"Status has been changed successfully"
        );
        return $this->redirect($this->generateUrl('hms_pathology'));
    }

    public function resetPathologyTestWithReportAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getHospitalConfig();
        $entities = $this->getDoctrine()->getRepository('HospitalBundle:HmsMasterDiagnosticReport')->findBy(array('status'=>1));

        /* @var $entity HmsMasterDiagnosticReport */
        foreach ($entities as $entity)
        {
            $exist = $this->getDoctrine()->getRepository('HospitalBundle:Particular')->findOneBy(array('hospitalConfig' => $config,'hmsMasterDiagnosticReport' => $entity));
            if(empty($exist)){
                $pathological = New Particular();
                $pathological->setHospitalConfig($config);
                $pathological->setHmsMasterDiagnosticReport($entity);
                $pathological->setName($entity->getName());
                if(!empty($entity->getCategory())){
                    $pathological->setCategory($entity->getCategory());
                }
                if(!empty($entity->getDepartment())) {
                    $pathological->setDepartment($entity->getDepartment());
                }
                $pathological->setStatus(1);
                $pathological->setService($this->getDoctrine()->getRepository('HospitalBundle:Service')->findOneBy(array('slug'=>'diagnostic')));
                $pathological->setSepcimen($entity->getSepcimen());
                $pathological->setInstruction($entity->getInstruction());
                $pathological->setTestDuration($entity->isTestDuration());
                $pathological->setReportFormat($entity->isReportFormat());
                $em->persist($pathological);
                $em->flush();
                $this->insertMasterReport($entity,$pathological);

            }


        }
        return $this->redirect($this->generateUrl('hms_pathology'));
    }

    public function insertMasterReport(DiagnosticReport $entity,Particular $pathological)
    {

        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        /* @var $report DiagnosticReportFormat */

        foreach ($entity->getDiagnosticReportFormats() as $report){

            $format = New PathologicalReport();
            $format->setParticular($pathological);
            $format->setParent($report->getParent());
            $format->setName($report->getName());
            $format->setReferenceValue($report->getReferenceValue());
            $format->setUnit($report->getUnit());
            $format->setSorting($report->getSorting());
            $format->setStatus(1);
            $em->persist($format);
            $em->flush();

        }
    }
}
