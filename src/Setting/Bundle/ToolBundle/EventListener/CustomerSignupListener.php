<?php
/**
 * Created by PhpStorm.
 * User: dhaka
 * Date: 8/19/14
 * Time: 4:51 PM
 */

namespace Setting\Bundle\ToolBundle\EventListener;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Setting\Bundle\ToolBundle\Event\CustomerSignup;
use Setting\Bundle\ToolBundle\Event\UserSignup;
use Setting\Bundle\ToolBundle\Service\EasyMailer;


class CustomerSignupListener extends BaseSmsAwareListener
{
    /** @var EasyMailer */
    private $mailer;

    public function setMailer(EasyMailer $mailer)
    {
        $this->mailer = $mailer;
    }


    public function onCustomerSignup(CustomerSignup $event)
    {
        $this->sendSms($event);
        //$this->sendEmail($event);
    }


    private function sendSms($event)
    {

        /**
         * @var CustomerSignup $event
         */

        $post = $event->getUser();
        $option = $event->getGlobalOption()->getDomain();
        $mobile = "88".$post->getProfile()->getMobile();
        $msg = "Your account has been created, User name:$mobile and password:1234 . Thank you for using www.$option";
        $this->gateway->send($msg, $mobile);

    }

    private function sendEmail($event)
    {

        /**
         * @var UserSignup $event
         */

        $post = $event->getUser();

        $to         = 'shafiq@rightbrainsolution.com';
        $from       = 'shafiq@emicrograph.com';
        $subject    = 'Signup';
        $body       = 'Success';
        $this->mailer->send($to, $from, $subject, $body);

    }


}