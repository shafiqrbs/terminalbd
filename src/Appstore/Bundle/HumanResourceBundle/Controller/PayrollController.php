<?php

namespace Appstore\Bundle\HumanResourceBundle\Controller;



use Appstore\Bundle\HumanResourceBundle\Entity\EmployeePayroll;
use Appstore\Bundle\HumanResourceBundle\Entity\EmployeePayrollParticular;
use Appstore\Bundle\HumanResourceBundle\Entity\Payroll;
use Appstore\Bundle\HumanResourceBundle\Form\EmployeePayrollType;
use Appstore\Bundle\HumanResourceBundle\Form\PayrollType;
use Core\UserBundle\Entity\User;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


/**
 * Customer controller.
 *
 */
class PayrollController extends Controller
{


    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('HumanResourceBundle:Payroll')->findWithSearch($globalOption->getId());
        return $this->render('HumanResourceBundle:Payroll:index.html.twig', array(
            'globalOption'  => $globalOption,
            'entities'     => $entities,
        ));
    }

    /**
     * Creates a new Particular entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Payroll();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $config = $this->getUser()->getGlobalOption();
            $entity->setGlobalOption($config);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been inserted successfully"
            );
            $this->getDoctrine()->getRepository('HumanResourceBundle:PayrollSheet')->insertUpdateParticular($entity);
            return $this->redirect($this->generateUrl('payroll_sheet', array('id' => $entity->getId())));
        }

        return $this->render('HumanResourceBundle:Payroll:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }


    public function newAction()
    {
        $entity = new Payroll();
        $form = $this->createCreateForm($entity);
        return $this->render('HumanResourceBundle:Payroll:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }




    /**
     * Creates a form to create a Particular entity.
     *
     * @param Payroll $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Payroll $entity)
    {
        $option = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new PayrollType($option), $entity, array(
            'action' => $this->generateUrl('payroll_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }


    public function updateAction(Request $request , Payroll $entity)
    {
        /* @var $entity EmployeePayroll */

        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $data = $request->request->all();
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success', "Data has been inserted successfully"
            );
            $this->getDoctrine()->getRepository('HumanResourceBundle:EmployeePayroll')->insertUpdateParticular($entity, $data);
            $this->getDoctrine()->getRepository('HumanResourceBundle:EmployeePayroll')->insertUpdate($entity);
        }
        return $this->render('HumanResourceBundle:EmployeePayroll:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    public function particularDeleteAction(Payroll $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Particular entity.');
        }
        try {

            $em->remove($entity);
            $em->flush();
            return new Response('success');

        } catch (ForeignKeyConstraintViolationException $e) {
            $this->get('session')->getFlashBag()->add(
                'notice',"Data has been relation another Table"
            );
        }catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add(
                'notice', 'Please contact system administrator further notification.'
            );
        }
        return new Response('failed');


    }

    public function sheetAction(Payroll $entity)
    {
        $global = $this->getUser()->getGlobalOption()->getId();
        if($entity->getGlobalOption()->getId() == $global){
            return $this->render('HumanResourceBundle:Payroll:sheet.html.twig', array(
                'entity' => $entity,
            ));
        }

    }



}
