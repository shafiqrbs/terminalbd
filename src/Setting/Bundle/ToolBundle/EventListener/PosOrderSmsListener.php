<?php
/**
 * Created by PhpStorm.
 * User: dhaka
 * Date: 8/19/14
 * Time: 4:51 PM
 */

namespace Setting\Bundle\ToolBundle\EventListener;
use Appstore\Bundle\InventoryBundle\Entity\Sales;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\SmsSender;
use Setting\Bundle\ToolBundle\Event\PosOrderSmsEvent;
use Setting\Bundle\ToolBundle\Service\SmsGateWay;


class PosOrderSmsListener extends BaseSmsAwareListener
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

    public function sendSms(PosOrderSmsEvent $event)
    {
        /**
         * @var PosOrderSmsEvent $event
         */


        $sales = $event->getSales();
        $customer = "Dear Customer your order is processing and you will get your product within 3 working days.";
        $administrator = "You get new order, invoice no ".$sales->getInvoice();

        $customerMobile = "+88".$sales->getCustomer()->getMobile();
        $administratorMobile = "+88".$sales->getInventoryConfig()->getGlobalOption()->getNotificationConfig()->getMobile();

        if($sales->getInventoryConfig()->getGlobalOption()->getSmsSenderTotal()->getRemaining() > 0 and $sales->getInventoryConfig()->getGlobalOption()->getNotificationConfig()->getSmsActive() == 1){
            if(!empty($customerMobile)){
                $status = $this->gateway->send($customer , $customerMobile);
                $this->em->getRepository('SettingToolBundle:SmsSender')->insertSenderSms($sales,$status);
            }
            if(!empty($administratorMobile)) {
                $status = $this->gateway->send($administrator, $administratorMobile);
                $this->em->getRepository('SettingToolBundle:SmsSender')->insertSenderSms($sales,$status);
            }
        }

    }

}