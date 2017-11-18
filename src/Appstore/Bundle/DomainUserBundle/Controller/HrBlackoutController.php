<?php

namespace Appstore\Bundle\DomainUserBundle\Controller;
use Appstore\Bundle\DomainUserBundle\Form\HrBlackoutType;
use Appstore\Bundle\DomainUserBundle\Entity\HrBlackout;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * DomainUser controller.
 *
 */
class HrBlackoutController extends Controller
{
    /**
     * Lists all Blackout entities.
     *
     */
    public function indexAction()
    {

        $option = $this->getUser()->getGlobalOption();
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DomainUserBundle:HrBlackout')->findOneBy(array('globalOption' => $option));
        if (empty($entity)) {
            $entity = new  HrBlackout();
            $em = $this->getDoctrine()->getManager();
            $entity->setGlobalOption($option);
            $em->persist($entity);
            $em->flush();
        }
        $blackout ='';
        $editForm = $this->createEditForm($entity);
        $blackOutDate =  $entity ->getBlackOutDate();
        $blackoutdate = isset( $blackOutDate ) ? $blackOutDate :'';
        if($blackoutdate){
            $blackoutdate = (array_map('trim',array_filter(explode(',',$blackoutdate))));
            $blackout=implode("','",$blackoutdate);
            $blackout="'".$blackout."'";
        }
        return $this->render('DomainUserBundle:HrBlackout:index.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'blackout' => $blackout,
        ));


    }

    /**
     * Creates a form to edit a Blackout entity.
     *
     * @param Blackout $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(HrBlackout $entity)
    {

        $form = $this->createForm(new HrBlackoutType(), $entity, array(
            'action' => $this->generateUrl('domain_hr_blackout_update', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing Blackout entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('DomainUserBundle:HrBlackout')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Blackout entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
        }
        return $this->redirect($this->generateUrl('domain_hr_blackout'));

    }

}
