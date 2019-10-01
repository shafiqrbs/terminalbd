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


	public function sendSms(AssociationSmsEvent $event)
	{

		/**
		 * @var ElectionSmsEvent $event
		 */

		$post = $event->getCustomer();
		$msg = $event->getMemberMsg();

		//$mobile = "88".$post->getMobile();

        $mobile = "8801828148148";
		$status = $this->gateway->send($msg, $mobile);
		$this->insertSms($status);



	}

	public function insertSms($status)
	{
		$entity = new SmsSender();
	}
}