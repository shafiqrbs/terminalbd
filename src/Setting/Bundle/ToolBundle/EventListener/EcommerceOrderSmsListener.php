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
use Setting\Bundle\ToolBundle\Event\EcommerceOrderSmsEvent;
use Setting\Bundle\ToolBundle\Service\SmsGateWay;


class EcommerceOrderSmsListener extends BaseSmsAwareListener
{

    /**
     * @var EntityManager
     */
    protected  $em;
    
    /**
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    private $doctrine;

    protected $gateway;

    public function  __construct(Registry $doctrine, SmsGateWay $gateway)
    {
        $this->doctrine = $doctrine;
        $this->gateway = $gateway;
        $this->em = $doctrine->getManager();
    }


    public function sendSms(EcommerceOrderSmsEvent $event)
    {

            /**
             * @var EcommerceOrderSmsEvent $event
             */

            $post = $event->getOrder();

            $shopName = $post->getGlobalOption()->getName();
            $domainName = 'www.'.$post->getGlobalOption()->getDomain();
            $customerMobile = "88".$post->getCreatedBy()->getProfile()->getMobile();
            $administratorMobile = "+88".$post->getGlobalOption()->getNotificationConfig()->getMobile();

            $deliveryDate = $post->getDeliveryDate()->format('d-m-Y');
            $created = $post->getCreated()->format('d-m-Y');
            $invoice = $post->getInvoice();
            $items = $post->getItem();
            $amount = $post->getGrandTotalAmount();
            //  $mobile = "8801828148148";

            $customerMsg = "Dear Sir, We have received your order no. $invoice for ($items) items totaling to BDT $amount and delivery date $deliveryDate. Thanks for using $domainName.";
            $administratorMsg = "You have received new order $invoice on $shopName for $items item(s) totaling $amount and delivery date $deliveryDate";

            if(!empty($post->getGlobalOption()->getSmsSenderTotal()) and $post->getGlobalOption()->getSmsSenderTotal()->getRemaining() > 0 and $post->getGlobalOption()->getNotificationConfig()->getSmsActive() == 1 and $post->getGlobalOption()->getNotificationConfig()->getOnlineOrder() == 1){

                if(!empty($customerMobile)){
                    $status =  $this->gateway->send($customerMsg, $customerMobile);
                    $this->em->getRepository('SettingToolBundle:SmsSender')->insertEcommerceSenderSms($post,$status);
                }
                if(!empty($post->getGlobalOption()->getNotificationConfig()->getMobile())) {
                    $status = $this->gateway->send($administratorMsg, $administratorMobile);
                    $this->em->getRepository('SettingToolBundle:SmsSender')->insertAdminEcommerceSenderSms($post,$administratorMobile,$status);
                }
            }

    }

    public function sendConfirm(EcommerceOrderSmsEvent $event)
    {
        /**
         * @var EcommerceOrderSmsEvent $event
         */

        $post = $event->getOrder();
        $domainName = 'www.'.$post->getGlobalOption()->getDomain();
        $deliveryDate = $post->getDeliveryDate()->format('d-m-Y');
        $invoice = $post->getInvoice();

        $customerMobile = "88".$post->getCreatedBy()->getProfile()->getMobile();

        $msg = "Dear Customer, Your order $invoice is confirmed  and Delivery Date $deliveryDate . Thanks for using $domainName.";

        if(!empty($post->getGlobalOption()->getSmsSenderTotal()) and $post->getGlobalOption()->getSmsSenderTotal()->getRemaining() > 0 and $post->getGlobalOption()->getNotificationConfig()->getSmsActive() == 1 and $post->getGlobalOption()->getNotificationConfig()->getOnlineOrder() == 1){

            if(!empty($post->getGlobalOption()->getNotificationConfig()->getMobile())) {
                $status =  $this->gateway->send($msg, $customerMobile);
                $this->em->getRepository('SettingToolBundle:SmsSender')->insertEcommerceOrderConfirmSms($post->getGlobalOption(), $customerMobile , $msg,$status);
            }
        }

    }

    public function sendComment(EcommerceOrderSmsEvent $event)
    {
        /**
         * @var EcommerceOrderSmsEvent $event
         */

        $post = $event->getOrder();
        $domainName = 'www.'.$post->getGlobalOption()->getDomain();
        $invoice = $post->getInvoice();
        $comment = $post->getComment();

        $customerMobile = "88".$post->getCreatedBy()->getProfile()->getMobile();

        $msg = "Dear Customer, Your order $invoice. $comment . Thanks for using $domainName.";

        if(!empty($post->getGlobalOption()->getSmsSenderTotal()) and $post->getGlobalOption()->getSmsSenderTotal()->getRemaining() > 0 and $post->getGlobalOption()->getNotificationConfig()->getSmsActive() == 1 and $post->getGlobalOption()->getNotificationConfig()->getOnlineOrder() == 1){

            if(!empty($post->getGlobalOption()->getNotificationConfig()->getMobile())) {
                $status =  $this->gateway->send($msg, $customerMobile);
                $this->em->getRepository('SettingToolBundle:SmsSender')->insertEcommerceOrderConfirmSms($post->getGlobalOption(), $customerMobile , $msg,$status);
            }
        }
    }
}