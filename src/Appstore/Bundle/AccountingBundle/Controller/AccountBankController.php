<?php

namespace Appstore\Bundle\AccountingBundle\Controller;

use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\AccountingBundle\Entity\AccountBank;
use Appstore\Bundle\AccountingBundle\Form\AccountBankType;
use Symfony\Component\HttpFoundation\Response;

/**
 * AccountBank controller.
 *
 */
class AccountBankController extends Controller
{


    /**
     * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_CONFIG_ACCOUNTING_CONFIG")
     */


    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('AccountingBundle:AccountBank')->findWithSearch($globalOption,$data);
        return $this->render('AccountingBundle:AccountBank:index.html.twig', array(
            'entities' => $entities,
            'searchForm' => $data,
           // 'overview' => $overview,
        ));
    }

    /**
     * Creates a new AccountBank entity.
     * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_CONFIG")
     */

    public function createAction(Request $request)
    {
        $entity = new AccountBank();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $entity->setGlobalOption($this->getUser()->getGlobalOption());
            $name = $entity->getBank()->getName().','.$entity->getBranch();
            $entity->setName($name);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('accountbank'));
        }

        return $this->render('AccountingBundle:AccountBank:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a AccountBank entity.
     *
     * @param AccountBank $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(AccountBank $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new AccountBankType($globalOption), $entity, array(
            'action' => $this->generateUrl('accountbank_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
      return $form;
    }

    /**
     * Displays a form to create a new AccountBank entity.
     * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_CONFIG")
     */

    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new AccountBank();
        $form   = $this->createCreateForm($entity);
        return $this->render('AccountingBundle:AccountBank:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a AccountBank entity.
     * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_CONFIG")
     */

    public function showAction($id)
    {

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AccountingBundle:AccountBank')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountBank entity.');
        }
        return $this->render('AccountingBundle:AccountBank:show.html.twig', array(
            'entity'      => $entity,
        ));

    }

    /**
     * Displays a form to edit an existing AccountBank entity.
     * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_CONFIG")
     */

    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AccountingBundle:AccountBank')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountBank entity.');
        }

        $editForm = $this->createEditForm($entity);
        return $this->render('AccountingBundle:AccountBank:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }


    /**
    * Creates a form to edit a AccountBank entity.
    *
    * @param AccountBank $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(AccountBank $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new AccountBankType($globalOption), $entity, array(
            'action' => $this->generateUrl('accountbank_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form purchase',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }


    /**
     * Edits an existing AccountBank entity.
     * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_CONFIG")
     */


    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AccountingBundle:AccountBank')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountBank entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $name = $entity->getBank()->getName().','.$entity->getBranch();
            $entity->setName($name);
            $em->flush();
            return $this->redirect($this->generateUrl('accountbank_edit', array('id' => $id)));
        }

        return $this->render('AccountingBundle:AccountBank:new.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }

    /**
     * Deletes a Expenditure entity.
     * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_CONFIG")
     */

    public function deleteAction(AccountBank $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountPurchase entity.');
        }
        $em->remove($entity);
        $em->flush();
        return new Response('success');
        exit;
    }

}
