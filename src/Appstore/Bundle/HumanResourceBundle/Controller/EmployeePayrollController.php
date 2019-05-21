<?php

namespace Appstore\Bundle\HumanResourceBundle\Controller;



use Appstore\Bundle\HumanResourceBundle\Entity\EmployeePayroll;
use Appstore\Bundle\HumanResourceBundle\Form\EmployeePayrollType;
use Core\UserBundle\Entity\User;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;



/**
 * Customer controller.
 *
 */
class EmployeePayrollController extends Controller
{


    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $employees = $em->getRepository('UserBundle:User')->getEmployees($globalOption);
        return $this->render('HumanResourceBundle:EmployeePayroll:index.html.twig', array(
            'globalOption'  => $globalOption,
            'employees'     => $employees,
        ));
    }

    public function employeeAction(User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $this->getDoctrine()->getRepository('HumanResourceBundle:EmployeePayroll')->userInsertUpdate($user);
        $form = $this->createCreateForm($entity);
        return $this->render('HumanResourceBundle:EmployeePayroll:new.html.twig', array(
            'user'  => $user,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Vendor entity.
     *
     * @param EmployeePayroll $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(EmployeePayroll $entity)
    {
        $option = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new EmployeePayrollType(), $entity, array(
            'action' => $this->generateUrl('employee_payroll_create',array('user'=> $entity->getEmployee()->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    public function updateAction(Request $request ,User $user)
    {
        /* @var $entity EmployeePayroll */

        $entity = $user->getEmployeePayroll();
        $form = $this->createCreateForm($entity);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been inserted successfully"
            );
            return $this->redirect($this->generateUrl('employee_payroll'));
        }
        return $this->render('HumanResourceBundle:EmployeePayroll:new.html.twig', array(
            'user'  => $user,
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }



}
