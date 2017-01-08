<?php
/**
 * Created by PhpStorm.
 * User: shafiq
 * Date: 10/9/15
 * Time: 8:05 AM
 */

namespace Setting\Bundle\ToolBundle\Repository;


use Appstore\Bundle\InventoryBundle\Entity\Sales;
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
        $this->_em->persist($entity);
        $this->_em->flush();
        if($status == 'success'){
            $this->totalSendSms($globalOption);
        }
    }

    public function totalSendSms($globalOption){

        $totalSms = $this->_em->getRepository('SettingToolBundle:SmsSenderTotal')->findOneBy(array('globalOption' => $globalOption));
        $totalSms->setRemaining($totalSms->getRemaining()-1);
        $totalSms->setSending($totalSms->getSending()+1);
        $this->_em->persist($totalSms);
        $this->_em->flush();
    }
} 