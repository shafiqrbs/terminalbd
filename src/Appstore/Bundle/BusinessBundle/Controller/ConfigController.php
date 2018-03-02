<?php

namespace Appstore\Bundle\BusinessBundle\Controller;

use Appstore\Bundle\BusinessBundle\Entity\DentalService;
use Appstore\Bundle\BusinessBundle\Entity\BusinessConfig;
use Appstore\Bundle\BusinessBundle\Entity\BusinessService;
use Appstore\Bundle\BusinessBundle\Form\ConfigType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * BusinessConfigController.
 *
 */
class ConfigController extends Controller
{


    public function manageAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $this->getUser()->getGlobalOption()->getBusinessConfig();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity preparation.');
        }
        $form = $this->createEditForm($entity);
        $pagination = $em->getRepository('BusinessBundle:BusinessService')->getServiceForPrescription($entity);
        return $this->render('BusinessBundle:Config:manage.html.twig', array(
            'entity' => $entity,
            'pagination' => $pagination,
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
    private function createEditForm(BusinessConfig $entity)
    {

        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $form = $this->createForm(new ConfigType($config), $entity, array(
            'action' => $this->generateUrl('dms_config_update'),
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
    public function updateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $pagination = $em->getRepository('BusinessBundle:BusinessService')->getServiceForPrescription($entity);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Particular entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Report has been created successfully"
            );
            $data = $request->request->all();
            $this->getDoctrine()->getRepository('BusinessBundle:BusinessService')->prescriptionServiceUpdate($entity,$data);
            if($entity->isCustomPrescription() == 1){
               /*// $print = '../src/Appstore/Bundle/BusinessBundle/Resources/views/Print/print.html.twig';
               // $copy = '../src/Appstore/Bundle/BusinessBundle/Resources/views/Print/'.$entity->getGlobalOption()->getSlug().'.html.twig';

                $srcfile='src/Appstore/Bundle/BusinessBundle/Resources/views/Print/print.html.twig';
                echo $dstfile='src/Appstore/Bundle/BusinessBundle/Resources/views/Print/'.$entity->getGlobalOption()->getSlug().'.html.twig';
               // mkdir(dirname($dstfile), 0777, true);
              //  copy($srcfile, $dstfile);


               // copy($print,$copy);
               // chmod($copy,0777);*/
            }
            return $this->redirect($this->generateUrl('dms_config_manage'));
        }

        return $this->render('BusinessBundle:Config:manage.html.twig', array(
            'entity'        => $entity,
            'pagination'    => $pagination,
            'form'          => $editForm->createView(),
        ));
    }

    public function resetDefaultServiceAction()
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getBusinessConfig();
        $entities = $this->getDoctrine()->getRepository('BusinessBundle:DentalService')->findBy(array('status'=>1));
        /* @var $entity DentalService */
        foreach ($entities as $entity){
            $exist = $this->getDoctrine()->getRepository('BusinessBundle:BusinessService')->findOneBy(array('dmsConfig'=>$config,'dentalService'=>$entity));
            if(empty($exist)){
                $service = new BusinessService();
                $service->setName(trim($entity->getName()));
                $service->setDentalService($entity);
                $service->setSlug(trim($entity->getSlug()));
                $service->setServiceFormat(trim($entity->getSlug()));
                $service->setStatus(1);
                $service->setBusinessConfig($config);
                $em->persist($service);
                $em->flush();
            }

        }
        return $this->redirect($this->generateUrl('dms_config_manage'));
    }

}

