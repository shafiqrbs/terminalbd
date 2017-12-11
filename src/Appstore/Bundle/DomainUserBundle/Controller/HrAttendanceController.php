<?php

namespace Appstore\Bundle\DomainUserBundle\Controller;

use Appstore\Bundle\DomainUserBundle\Entity\HrAttendance;
use Appstore\Bundle\DomainUserBundle\Entity\HrAttendanceMonth;
use Core\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Appstore\Bundle\DomainUserBundle\Form\CustomerType;
use Symfony\Component\Validator\Constraints\Date;

/**
 * Customer controller.
 *
 */
class HrAttendanceController extends Controller
{

    public function paginate($entities)
    {

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            50  /*limit per page*/
        );
        return $pagination;
    }


    /**
     * Lists all Customer entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('DomainUserBundle:HrAttendance')->findByMonth($globalOption,$data);
        $pagination = $this->paginate($entities);
        return $this->render('DomainUserBundle:HrAttendance:index.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
        ));
    }


    /**
     * Displays a form to create a new Customer entity.
     *
     */
    public function monthAction()
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $blackoutdate ='';
        //$employees = $em->getRepository('UserBundle:User')->getEmployees($globalOption);
        $calendarBlackout = $em->getRepository('DomainUserBundle:HrBlackout')->findOneBy(array('globalOption' => $globalOption));
        $blackOutDate =  $calendarBlackout ->getBlackOutDate();
        if($blackOutDate){
            $blackoutdate = (array_map('trim',array_filter(explode(',',$blackOutDate))));
        }
        return $this->render('DomainUserBundle:HrAttendance:attendance.html.twig', array(
            'globalOption'  => $globalOption,
            'blackoutdate'  => $blackoutdate,
        ));

    }

    /**
     * Finds and displays a Customer entity.
     *
     */
    public function showAction($month)
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $calendarBlackout = $em->getRepository('DomainUserBundle:HrBlackout')->findOneBy(array('globalOption' => $globalOption));
        $blackOutDate =  $calendarBlackout ->getBlackOutDate();
        if($blackOutDate){
            $blackoutdate = (array_map('trim',array_filter(explode(',',$blackOutDate))));
        }
        $attendances = $this->getDoctrine()->getRepository('DomainUserBundle:HrAttendanceMonth')->findBy(array('globalOption' => $globalOption));

        return $this->render('DomainUserBundle:HrAttendance:show.html.twig', array(
            'globalOption' => $globalOption,
            'blackoutdate' => $blackoutdate,
            'attendances' => $attendances,
        ));

    }

    /**
     * Displays a form to edit an existing Customer entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('DomainUserBundle:Customer')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Customer entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('DomainUserBundle:Customer:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

     /**
     * Deletes a Customer entity.
     *
     */
    public function deleteAction(HrAttendanceMonth $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Customer entity.');
        }
        $em->remove($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('customer'));
    }

    /**
     * Creates a form to delete a Customer entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('customer_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
            ;
    }

    public function autoSearchAction(Request $request)
    {
        $item = $_REQUEST['q'];
        if ($item) {
            $go = $this->getUser()->getGlobalOption();
            $type= 'pos';
            $item = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->searchAutoComplete($go,$item,$type);
        }
        return new JsonResponse($item);
    }



    public function searchCustomerNameAction($customer)
    {
        return new JsonResponse(array(
            'id'=> $customer,
            'text' => $customer
        ));
    }

    public function autoCodeSearchAction(Request $request)
    {

        /* $item = $_REQUEST['q'];
        if ($item) {
            $go = $this->getUser()->getGlobalOption();
            $item = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->searchAutoCompleteCode($go,$item);
        }
        return new JsonResponse($item);*/

        $q = $_REQUEST['term'];
        $option = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository('DomainUserBundle:Customer')->searchAutoCompleteCode($option,$q);
        $items = array();
        foreach ($entities as $entity):
            $items[]=array('id' => $entity['customer'],'value' => $entity['text']);
        endforeach;
        return new JsonResponse($items);

    }



    public function searchCodeAction($customer)
    {
        return new JsonResponse(array(
            'id'=> $customer,
            'text' => $customer
        ));
    }


    public function addAttendanceAction(User $user)
    {
        $present = $_REQUEST['present'];
        $em = $this->getDoctrine()->getManager();
        $datetime = new \DateTime("now");
        $today  = $datetime->format('d');
        $month  = $datetime->format('m');
        $year   = $datetime->format('y');
        $this->getDoctrine()->getRepository('DomainUserBundle:HrAttendanceMonth')->findOneBy(array('user' => $user));

        $entity = New HrAttendanceMonth();
        $entity->setUser($user);
        $entity->setGlobalOption($user->getGlobalOption());
        if($present > 0 ){
            $entity->setPresent(true);
            $entity->setPresentDay($present);
            $entity->setPresentIn(true);
            $entity->setPresentOut(true);
        }else{
            $entity->setPresent(false);
            $entity->setPresentDay(null);
            $entity->setPresentIn(true);
            $entity->setPresentOut(true);
        }
        $entity->setMonth($month);
        $entity->setYear($year);
        $em->persist($entity);
        $em->flush();
        exit;

    }


}
