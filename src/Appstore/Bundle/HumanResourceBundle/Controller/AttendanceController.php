<?php

namespace Appstore\Bundle\HumanResourceBundle\Controller;

use Appstore\Bundle\DomainUserBundle\Entity\HrAttendance;
use Appstore\Bundle\DomainUserBundle\Entity\HrAttendanceMonth;
use Appstore\Bundle\HumanResourceBundle\Entity\DailyAttendance;
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
class AttendanceController extends Controller
{


    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $employees = $em->getRepository('UserBundle:User')->getEmployees($globalOption);
        return $this->render('HumanResourceBundle:Attendance:index.html.twig', array(
            'globalOption'  => $globalOption,
            'employees'     => $employees,
        ));
    }


    /**
     * Displays a form to create a new Customer entity.
     *
     */
    public function attendanceAction()
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
        return $this->render('HumanResourceBundle:DailyAttendance:attendance.html.twig', array(
            'globalOption'  => $globalOption,
            'employees'     => $employees,
            'blackoutdate'  => $blackoutdate,
        ));

    }


    public function addAttendanceAction(User $user)
    {
        $present = $_REQUEST['present'];
        $em = $this->getDoctrine()->getManager();
        $datetime = new \DateTime("now");
        $today  = $datetime->format('d');
        $month  = $datetime->format('F');
        $year   = $datetime->format('Y');
        $this->getDoctrine()->getRepository('HumanResourceBundle:DailyAttendance')->findOneBy(array('user' => $user));

        $entity = New DailyAttendance();
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

    public function showAction($month,$year)
    {
        $showMonth = new \DateTime("$month $year");
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $blackoutdate ='';
        $employees = $em->getRepository('UserBundle:User')->getEmployees($globalOption);
        $calendarBlackout = $em->getRepository('HumanResourceBundle:Weekend')->findOneBy(array('globalOption' => $globalOption));
        $blackOutDate =  $calendarBlackout ->getWeekendDate();
        if($blackOutDate){
            $blackoutdate = (array_map('trim',array_filter(explode(',',$blackOutDate))));
        }
        return $this->render('HumanResourceBundle:DailyAttendance:show.html.twig', array(
            'showMonth' => $showMonth,
            'globalOption'  => $globalOption,
            'employees'     => $employees,
            'blackoutdate'  => $blackoutdate,
        ));
    }


    public function employeeDetailsAction(User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $employees = $em->getRepository('UserBundle:User')->getEmployees($globalOption);
        return $this->render('HumanResourceBundle:DailyAttendance:employee.html.twig', array(
            'globalOption'  => $globalOption,
            'employees'     => $employees,
        ));
    }



    public function manualAttendanceAction()
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $employees = $em->getRepository('UserBundle:User')->getEmployees($globalOption);
        return $this->render('HumanResourceBundle:DailyAttendance:manualAttendance.html.twig', array(
            'globalOption'  => $globalOption,
            'employees'     => $employees,
        ));

    }

    public function manualAttendanceCreate(Request $request)
    {

    }


}
