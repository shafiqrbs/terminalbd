<?php

namespace Appstore\Bundle\HumanResourceBundle\Controller;

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
class DailyAttendanceController extends Controller
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
    public function dailyAttendanceAction()
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $blackoutdate ='';
        $employees = $em->getRepository('UserBundle:User')->getEmployees($globalOption);
        $calendarBlackout = $em->getRepository('HumanResourceBundle:Weekend')->findOneBy(array('globalOption' => $globalOption));
        $blackOutDate =  $calendarBlackout ->getWeekendDate();
        if($blackOutDate){
            $blackoutdate = (array_map('trim',array_filter(explode(',',$blackOutDate))));
        }
        return $this->render('DomainUserBundle:HrAttendance:attendance.html.twig', array(
            'globalOption'  => $globalOption,
            'blackoutdate'  => $blackoutdate,
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
