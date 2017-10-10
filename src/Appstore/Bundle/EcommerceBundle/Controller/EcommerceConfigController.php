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
