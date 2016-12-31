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

    public function  __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->em = $doctrine->getManager();
    }

    public function sendSms(PosOrderSmsEvent $event)
    {
        /**
         * @var PosOrderSmsEvent $event
         */

        $sales = $event->getSales();
        $msg = "Dear Custmer your invoice no is".$sales->getInvoice().'and status is'.$sales->getProcess();
        $mobile = "8801828148148";
        //$mobile = "+88".$sales->getCustomer()->getMobile();
        if($sales->getInventoryConfig()->getGlobalOption()->getSmsSenderTotal()->getRemaining() > 0 ){
            $status = $this->gateway->send($msg, $mobile);
            $this->em->getRepository('SettingToolBundle:SmsSender')->insertSenderSms($sales,$status);
        }

    }
   /* public function insertSenderSms(Sales $sales,$status)
    {
        $globalOption = $sales->getInventoryConfig()->getGlobalOption();

        $entity = new SmsSender();
        $entity->setMobile($sales->getCustomer()->getMobile());
        $entity->setGlobalOption($globalOption);
        $entity->setStatus($status);
        $entity->setProcess('Sales');
        $entity->setRemark($sales->getInvoice());
        $this->em->persist($entity);
        $this->em->flush();
        if($status == 'success'){
            $this->totalSendSms($globalOption);
        }
    }

    public function totalSendSms($globalOption){

        $totalSms = $this->em->getRepository('SettingToolBundle:SmsSenderTotal')->findOneBy(array('globalOption' => $globalOption));
        $totalSms->setRemaining($totalSms->getRemaining()-1);
        $totalSms->setSending($totalSms->getSending()+1);
        $this->em->persist($totalSms);
        $this->em->flush();
    }*/
}