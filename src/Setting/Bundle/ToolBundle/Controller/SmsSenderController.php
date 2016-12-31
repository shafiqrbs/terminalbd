<?php

namespace Setting\Bundle\ToolBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Setting\Bundle\ToolBundle\Entity\SmsSender;
use Setting\Bundle\ToolBundle\Form\SmsSenderType;

/**
 * SmsSender controller.
 *
 */
class SmsSenderController extends Controller
{

    /**
     * Lists all SmsSender entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SettingToolBundle:SmsSender')->findAll();

        return $this->render('SettingToolBundle:SmsSender:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Deletes a SmsSender entity.
     *
     */
    public function deleteAction()
    {
        $globalOption  = $this->getUser()->getGlobalOption()->getSenderSms();
        $em = $this->getDoctrine()->getManager();
        $em->remove($globalOption);
        $em->flush();
        return $this->redirect($this->generateUrl('smssender'));
    }


}
