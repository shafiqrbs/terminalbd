<?php
/**
 * Created by PhpStorm.
 * User: dhaka
 * Date: 8/19/14
 * Time: 4:51 PM
 */

namespace Setting\Bundle\ToolBundle\EventListener;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Setting\Bundle\ToolBundle\Event\EcommerceOrderSmsEvent;
use Setting\Bundle\ToolBundle\Event\SmsEvent;


class EcommerceOrderSmsListener extends BaseSmsAwareListener
{

    public function sendSms(EcommerceOrderSmsEvent $event)
    {

        /**
         * @var EcommerceOrderSmsEvent $event
         */

        $post = $event->getOrder();
        if(!empty($post->getComment())){
            $msg = "Dear Customer, Ref invoice no:".$post->getInvoice().'. '.$post->getComment();
            //$date = new DateTime('2000-01-01');
            //echo $date->format('Y-m-d H:i:s');;
            //$mobile = "88".$post->getCreatedBy()->getProfile()->getMobile();
            $mobile = "8801828148148";
            $this->gateway->send($msg, $mobile);
        }


    }
}