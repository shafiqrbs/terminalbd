<?php
/**
 * Created by PhpStorm.
 * User: shafiq
 * Date: 10/9/15
 * Time: 8:05 AM
 */

namespace Setting\Bundle\ToolBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\SmsSender;

class SmsSenderRepository extends EntityRepository {

    public function insertSenderSms(Sales $sales,$status)
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
    }
} 