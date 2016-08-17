<?php
/**
 * Created by PhpStorm.
 * User: dhaka
 * Date: 8/19/14
 * Time: 4:51 PM
 */

namespace Setting\Bundle\ToolBundle\EventListener;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Setting\Bundle\ToolBundle\Event\ReceiveSmsEvent;
use Setting\Bundle\ToolBundle\Event\SmsEvent;


class ReceiveSmsListener extends BaseSmsAwareListener
{

    public function receiveSms(ReceiveSmsEvent $event)
    {

        /**
         * @var ReceiveSmsEvent $event
         */

        $post = $event->getCustomerInbox();
        $domain = $event->getGlobalOption();

        $mobile     = "88".$domain->getMobile();
        $msg       = $post->getContent();
        $this->gateway->send($msg, $mobile);
    }
}