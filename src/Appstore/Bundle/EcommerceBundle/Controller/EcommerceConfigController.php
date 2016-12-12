<?php

namespace Appstore\Bundle\EcommerceBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\EcommerceBundle\Entity\EcommerceConfig;
use Appstore\Bundle\EcommerceBundle\Form\EcommerceConfigType;

/**
 * EcommerceConfig controller.
 *
 */
class EcommerceConfigController extends Controller
{

    /**
     * Lists all EcommerceConfig entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('EcommerceBundle:EcommerceConfig')->findAll();

        return $this->render('EcommerceBundle:EcommerceConfig:index.html.twig', array(
            'entities' => $entities,
        ));
    }


    /**
    * Creates a form to edit a EcommerceConfig entity.
    *
    * @param EcommerceConfig $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(EcommerceConfig $entity)
    {
        $form = $this->createForm(new EcommerceConfigType(), $entity, array(
            'action' => $this->generateUrl('ecommerce_config_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing EcommerceConfig entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EcommerceBundle:EcommerceConfig')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EcommerceConfig entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('ecommerce_config_modify'));
        }

        return $this->render('EcommerceBundle:EcommerceConfig:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),

        ));
    }
    /**
     * Displays a form to edit an existing EcommerceConfig entity.
     *
     */
    public function modifyAction()
    {

        $entity = $this->getUser()->getGlobalOption()->getEcommerceConfig();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EcommerceConfig entity.');
        }
        $editForm = $this->createEditForm($entity);
        return $this->render('EcommerceBundle:EcommerceConfig:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }
}
