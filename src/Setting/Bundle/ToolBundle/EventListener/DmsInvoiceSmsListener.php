<?php
/**
 * Created by PhpStorm.
 * User: dhaka
 * Date: 8/19/14
 * Time: 4:51 PM
 */

namespace Setting\Bundle\ToolBundle\EventListener;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\SmsSender;
use Setting\Bundle\ToolBundle\Event\DmsInvoiceSmsEvent;
use Setting\Bundle\ToolBundle\Event\DmsTreatmentPlanSmsEvent;
use Setting\Bundle\ToolBundle\Event\DmsTreatmentScheduleSmsEvent;
use Setting\Bundle\ToolBundle\Event\HmsInvoiceSmsEvent;
use Setting\Bundle\ToolBundle\Event\PosOrderSmsEvent;
use Setting\Bundle\ToolBundle\Service\SmsGateWay;


class DmsInvoiceSmsListener extends BaseSmsAwareListener
{
    /**
     * @var EntityManager
     */
    protected $em;
    /**
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    private $doctrine;

    protected $gateway;

    public function __construct(Registry $doctrine, SmsGateWay $gateway)
    {
        $this->doctrine = $doctrine;
        $this->gateway = $gateway;
        $this->em = $doctrine->getManager();
    }

    public function sendSms(DmsInvoiceSmsEvent $event)
    {
        /**
         * @var DmsInvoiceSmsEvent $event
         */

        $invoice = $event->getDmsInvoice();
        $patientMobile = "+88".$invoice->getCustomer()->getMobile();
        //$patientMobile = "+8801915109006"; //.$invoice->getCustomer()->getMobile();
        if($invoice->getCustomer()->getGender() == 'Male'){
            $msg = 'Dear Sir, We Are Always Committed To Provide Quality Service. Thank You For Being With '.$invoice->getDmsConfig()->getGlobalOption()->getName().'.';
        }else{
            $msg = 'Dear Madam, We Are Always Committed To Provide Quality Service. Thank You For Being With '.$invoice->getDmsConfig()->getGlobalOption()->getName().'.';
        }
        if (!empty($patientMobile)) {
            $status = $this->gateway->send($msg,$patientMobile);
            $this->em->getRepository('SettingToolBundle:SmsSender')->insertDmsInvoiceSenderSms($invoice, $status);
        }


    }

    public function sendPlanConfirmSms(DmsTreatmentPlanSmsEvent $event)
    {
        /**
         * @var DmsTreatmentPlanSmsEvent $event
         */
        $schedule = $event->getDmsTreatmentPlan();
        $invoice = $event->getDmsTreatmentPlan()->getDmsInvoice();
        $patientMobile = "+88".$invoice->getCustomer()->getMobile();
        if($invoice->getCustomer()->getGender() == 'Male'){
            $msg = 'Dear Sir Your Next Appointment Date and Time- ' . $schedule->getAppointmentDate() . $schedule->getAppointmentTime().'. Thank You For Being With '.$invoice->getDmsConfig()->getGlobalOption()->getName();
        }else{
            $msg = 'Dear Madam Your Next Appointment Date and Time- ' . $schedule->getAppointmentDate() . $schedule->getAppointmentTime().'. Thank You For Being With '.$invoice->getDmsConfig()->getGlobalOption()->getName();
        }
        if (!empty($patientMobile)) {
            $status = $this->gateway->send($msg,$patientMobile);
            $this->em->getRepository('SettingToolBundle:SmsSender')->insertDmsInvoiceTreatmentNotificationSenderSms($schedule,$msg, $status);
        }

    }

}