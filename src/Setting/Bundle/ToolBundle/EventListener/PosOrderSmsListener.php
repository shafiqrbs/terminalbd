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
        $msg = "Dear Custmer your invoice no is".$sales->getInvoice().'and status is'.$sales->getProcess();
        $mobile = "+88".$sales->getCustomer()->getMobile();
        if($sales->getInventoryConfig()->getGlobalOption()->getSmsSenderTotal()->getRemaining() > 0 ){
            $status = $this->gateway->send($msg, $mobile);
            $this->em->getRepository('SettingToolBundle:SmsSender')->insertSenderSms($sales,$status);
        }

    }

}