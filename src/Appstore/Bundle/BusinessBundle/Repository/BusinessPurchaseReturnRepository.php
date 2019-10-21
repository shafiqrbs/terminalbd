<?php

namespace Appstore\Bundle\BusinessBundle\Repository;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoice;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoiceParticular;
use Appstore\Bundle\BusinessBundle\Entity\BusinessParticular;
use Appstore\Bundle\BusinessBundle\Entity\BusinessPurchaseReturn;
use Appstore\Bundle\BusinessBundle\Entity\BusinessPurchaseReturnItem;
use Doctrine\ORM\EntityRepository;


/**
 * MedicinePurchaseReturnItemRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BusinessPurchaseReturnRepository extends EntityRepository
{


    public function purchaseReturnStockUpdate(BusinessParticular $item)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('SUM(e.quantity) AS quantity');
        $qb->where('e.businessParticular = :businessParticular')->setParameter('businessParticular', $item->getId());
        $qnt = $qb->getQuery()->getOneOrNullResult();
        return $qnt['quantity'];
    }

    public function countReturnQuantity($invoice)
    {
        $em = $this->_em;
        return $total = $em->createQueryBuilder()
            ->from('BusinessBundle:BusinessInvoiceParticular','si')
            ->select('sum(si.damageQnt) as qnt')
            ->where('si.businessInvoice = :invoice')
            ->setParameter('invoice', $invoice)
            ->getQuery()->getOneOrNullResult();
    }



    public function insertInvoiceDamageItem(BusinessInvoice $invoice)
    {
        $em = $this->_em;
        $exist = $this->findOneBy(array('businessConfig' => $invoice->getBusinessConfig(),'salesInvoice'=> $invoice->getInvoice()));
        $returnItemCount = $this->countReturnQuantity($invoice->getId());
            if($exist){
                $this->insertUpdatePurchaseReturnItem($exist,$invoice);
            }elseif($returnItemCount['qnt'] > 0){
                $entity = new BusinessPurchaseReturn();
                $entity->setBusinessConfig($invoice->getBusinessConfig());
                $entity->setSalesInvoice($invoice->getInvoice());
                $entity->setCreatedBy($invoice->getCreatedBy());
                $em->persist($entity);
                $em->flush();
                $this->insertUpdatePurchaseReturnItem($entity,$invoice);
            }



    }

    public function insertUpdatePurchaseReturnItem(BusinessPurchaseReturn $entity, BusinessInvoice $invoice)
    {
        $em = $this->_em;
        /* @var $item BusinessInvoiceParticular */

        foreach ($invoice->getBusinessInvoiceParticulars() as $item):

                $exist = $em->getRepository('BusinessBundle:BusinessPurchaseReturnItem')->findOneBy(array('businessPurchaseReturn' => $entity,'salesInvoiceItem'=>$item->getId()));
                /* @var $purchaseItem BusinessPurchaseReturnItem */
                if($exist){
                    $purchaseItem = $exist;
                }elseif($item->getDamageQnt() > 0){
                    $purchaseItem = new BusinessPurchaseReturnItem();
                }
                $purchaseItem->setBusinessPurchaseReturn($entity);
                $purchaseItem->setSalesInvoiceItem($item->getId());
                $purchaseItem->setBusinessParticular($item->getBusinessParticular());
                $purchaseItem->setDamageQnt($item->getDamageQnt());
                $purchaseItem->setSpoilQnt($item->getSpoilQnt());
                $purchaseItem->setQuantity($item->getDamageQnt() + $item->getSpoilQnt());
                $purchaseItem->setPurchasePrice($item->getBusinessParticular()->getPurchasePrice());
                $purchaseItem->setSubTotal($item->getBusinessParticular()->getPurchasePrice() * $purchaseItem->getQuantity());
                $em->persist($purchaseItem);
                $em->flush();
                $em->getRepository('BusinessBundle:BusinessParticular')->updateRemoveStockQuantity($item->getBusinessParticular(),"purchase-return");

        endforeach;
    }

}
