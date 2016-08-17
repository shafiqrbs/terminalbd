<?php
/**
 * Created by PhpStorm.
 * User: dhaka
 * Date: 8/19/14
 * Time: 4:51 PM
 */

namespace Setting\Bundle\ToolBundle\EventListener;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Setting\Bundle\ToolBundle\Event\PasswordChangeSmsEvent;
use Setting\Bundle\ToolBundle\Event\SmsEvent;


class PasswordChangeSmsListener extends BaseSmsAwareListener
{

    public function sendSms(PasswordChangeSmsEvent $event)
    {

        /**
         * @var PasswordChangeSmsEvent $event
         */

        $user = $event->getUser();
        $password = $event->getPassword();

        $msg = "Your requesting new password is:".$password;
        //$mobile = "88".$user->getMobile();
        $mobile = "+8801828148148";
        $this->gateway->send($msg, $mobile);


    }
}