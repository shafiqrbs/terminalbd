<?php
namespace  Appstore\Bundle\DomainUserBundle\EventListener;
use Appstore\Bundle\DomainUserBundle\Event\AssociationSmsEvent;
use Appstore\Bundle\ElectionBundle\Event\ElectionSmsEvent;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Setting\Bundle\ToolBundle\Entity\SmsSender;
use Setting\Bundle\ToolBundle\Service\SmsGateWay;


class AssociationSmsListener extends BaseSmsAwareListener
{

    /**
     * @var EntityManager
     */
    protected $em;

    protected $doctrine;

    protected $gateway;

    public function __construct(Registry $doctrine, SmsGateWay $gateway)
    {
        $this->doctrine = $doctrine;
        $this->gateway = $gateway;
        $this->em = $doctrine->getManager();
    }

    public function sendSms(AssociationSmsEvent $event)
    {

        /**
         * @var AssociationSmsEvent $event
         */

        $post = $event->getCustomer();
        $msg = $event->getMemberMsg();
        if($post->getCountry()->getName() == "Bangladesh"){
            $mobile = "8801828148148";
            echo $status = $this->gateway->send($msg, $mobile);
            exit;
            $this->em->getRepository('SettingToolBundle:SmsSender')->insertCustomerSenderSms($post,$msg, $status);
        }elseif($post->getCountry() and $post->getCountry()->getName() != "Bangladesh"){
            $mobile = $post->getMobile();
            $status = $this->gateway->send($msg, $mobile);
            $this->em->getRepository('SettingToolBundle:SmsSender')->insertCustomerSenderSms($post,$msg, $status);
        }

    }

    public function memberConfirmSms(AssociationSmsEvent $event)
    {

        /**
         * @var AssociationSmsEvent $event
         */

        $post = $event->getCustomer();
        $msg = $event->getMemberMsg();
        if($post->getCountry()->getName() == "Bangladesh"){
            $mobile = "+88".$post->getMobile();
            $status = $this->gateway->send($msg, $mobile);
            $this->em->getRepository('SettingToolBundle:SmsSender')->insertCustomerSenderSms($post,$msg, $status);
        }elseif($post->getCountry() and $post->getCountry()->getName() != "Bangladesh"){
            $mobile = $post->getMobile();
            $status = $this->gateway->send($msg, $mobile);
            $this->em->getRepository('SettingToolBundle:SmsSender')->insertCustomerSenderSms($post,$msg, $status);
        }

    }

}