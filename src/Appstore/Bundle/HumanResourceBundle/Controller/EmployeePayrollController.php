<?php

namespace Appstore\Bundle\HumanResourceBundle\Controller;



use Appstore\Bundle\HumanResourceBundle\Entity\EmployeePayroll;
use Appstore\Bundle\HumanResourceBundle\Entity\EmployeePayrollParticular;
use Appstore\Bundle\HumanResourceBundle\Form\EmployeePayrollType;
use Core\UserBundle\Entity\User;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


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
        $particulars = $this->getDoctrine()->getRepository('HumanResourceBundle:PayrollSetting')->findBy(array('globalOption'=>$entity->getGlobalOption()));
        return $this->render('HumanResourceBundle:EmployeePayroll:new.html.twig', array(
            'user'  => $user,
            'entity'  => $entity,
            'particulars' => $particulars,
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
        $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $form = $this->createForm(new EmployeePayrollType($option,$location), $entity, array(
            'action' => $this->generateUrl('employee_payroll_create',array('id'=> $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    public function updateAction(Request $request ,EmployeePayroll $entity)
    {
        /* @var $entity EmployeePayroll */

        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $data = $request->request->all();
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->getProfile()->upload();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been inserted successfully"
            );
            $this->getDoctrine()->getRepository('HumanResourceBundle:EmployeePayroll')->insertUpdateParticular($entity,$data);
            $this->getDoctrine()->getRepository('HumanResourceBundle:EmployeePayroll')->insertUpdate($entity);
            return $this->redirect($this->generateUrl('employee_payroll_add',array('user' => $entity->getEmployee()->getId())));
        }
        $particulars = $this->getDoctrine()->getRepository('HumanResourceBundle:PayrollSetting')->findBy(array('globalOption'=>$entity->getGlobalOption()));
        return $this->render('HumanResourceBundle:EmployeePayroll:new.html.twig', array(
            'user'  => $entity->getEmployee(),
            'entity' => $entity,
            'particulars' => $particulars,
            'form'   => $form->createView(),
        ));
    }

    public function particularDeleteAction(EmployeePayrollParticular $entity)
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



}
