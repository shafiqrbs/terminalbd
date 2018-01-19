<?php

namespace Appstore\Bundle\DmsBundle\Controller;

use Appstore\Bundle\DmsBundle\Entity\DmsConfig;
use Appstore\Bundle\DmsBundle\Form\ConfigType;
use Appstore\Bundle\DmsBundle\Form\InvoiceType;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use Frontend\FrontentBundle\Service\MobileDetect;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * DmsConfigController.
 *
 */
class DmsConfigController extends Controller
{


    public function manageAction()
    {

        $entity = $this->getUser()->getGlobalOption()->getDmsConfig();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity preparation.');
        }
        $form = $this->createEditForm($entity);
        return $this->render('DmsBundle:Config:manage.html.twig', array(
            'entity' => $entity,
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
    private function createEditForm(DmsConfig $entity)
    {

        $hospitalConfig = $this->getUser()->getGlobalOption()->getDmsConfig();
        $form = $this->createForm(new ConfigType($hospitalConfig), $entity, array(
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

        $entity = $this->getUser()->getGlobalOption()->getDmsConfig();

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

            return $this->redirect($this->generateUrl('dms_config_manage'));
        }

        return $this->render('DmsBundle:Config:manage.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

}

