<?php

namespace Appstore\Bundle\BusinessBundle\Repository;
use Appstore\Bundle\BusinessBundle\Entity\BusinessConfig;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoice;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoiceParticular;
use Appstore\Bundle\BusinessBundle\Entity\BusinessParticular;
use Appstore\Bundle\BusinessBundle\Entity\BusinessPurchaseReturn;
use Appstore\Bundle\BusinessBundle\Entity\BusinessPurchaseReturnItem;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;


/**
 * MedicinePurchaseReturnItemRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BusinessPurchaseReturnRepository extends EntityRepository
{


    protected function handleSearchBetween($qb,$data)
    {

        $grn = isset($data['grn'])? $data['grn'] :'';
        $vendor = isset($data['vendor'])? $data['vendor'] :'';
        $business = isset($data['name'])? $data['name'] :'';
        $brand = isset($data['brandName'])? $data['brandName'] :'';
        $mode = isset($data['mode'])? $data['mode'] :'';
        $vendorId = isset($data['vendorId'])? $data['vendorId'] :'';
        $startDate = isset($data['startDate'])? $data['startDate'] :'';
        $endDate = isset($data['endDate'])? $data['endDate'] :'';

        if (!empty($grn)) {
            $qb->andWhere($qb->expr()->like("e.grn", "'%$grn%'"  ));
        }
        if(!empty($business)){
            $qb->andWhere($qb->expr()->like("ms.name", "'%$business%'"  ));
        }
        if(!empty($brand)){
            $qb->andWhere($qb->expr()->like("ms.brandName", "'%$brand%'"  ));
        }
        if(!empty($mode)){
            $qb->andWhere($qb->expr()->like("ms.mode", "'%$mode%'"  ));
        }
        if(!empty($vendor)){
            $qb->join('e.vendor','v');
            $qb->andWhere($qb->expr()->like("v.companyName", "'%$vendor%'"  ));
        }
        if(!empty($vendorId)){
            $qb->join('e.vendor','v');
            $qb->andWhere("v.id = :vendorId")->setParameter('vendorId', $vendorId);
        }
        if (!empty($startDate) ) {
            $datetime = new \DateTime($data['startDate']);
            $start = $datetime->format('Y-m-d 00:00:00');
            $qb->andWhere("e.updated >= :startDate");
            $qb->setParameter('startDate', $start);
        }

        if (!empty($endDate)) {
            $datetime = new \DateTime($data['endDate']);
            $end = $datetime->format('Y-m-d 23:59:59');
            $qb->andWhere("e.updated <= :endDate");
            $qb->setParameter('endDate', $end);
        }
    }


    public function findWithSearch($config, $data = array())
    {

        $qb = $this->createQueryBuilder('e');
        $qb->where('e.businessConfig = :config')->setParameter('config', $config) ;
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('e.updated','DESC');
        $qb->getQuery();
        return  $qb;
    }

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
            ->select('sum(si.damageQnt) as damageQnt')
            ->where('si.businessInvoice = :invoice')
            ->setParameter('invoice', $invoice)
            ->getQuery()->getOneOrNullResult();
    }

    public function updatePurchaseTotalPrice(BusinessPurchaseReturn $entity)
    {
        $em = $this->_em;
        $result = $em->createQueryBuilder()
            ->from('BusinessBundle:BusinessPurchaseReturnItem','si')
            ->select('sum(si.subTotal) as total','sum(si.damageQnt) as damageQuantity','sum(si.spoilQnt) as spoilQuantity','sum(si.quantity) as quantity')
            ->where('si.businessPurchaseReturn = :entity')
            ->setParameter('entity', $entity ->getId())
            ->getQuery()->getSingleResult();

        if($result['total'] > 0){
            $subTotal = round($result['total'],2);
            $entity->setSubTotal($subTotal);
            $entity->setQuantity( $result['quantity']);
            $entity->setDamageQuantity( $result['damageQuantity']);
            $entity->setSpoilQuantity( $result['spoilQuantity']);

        }else{

            $entity->setSubTotal(0);
            $entity->setSpoilQuantity(0);
            $entity->setDamageQuantity(0);
            $entity->setQuantity(0);
        }

        $em->persist($entity);
        $em->flush();

        return $entity;

    }


    public function insertInvoiceDamageItem(BusinessInvoice $invoice)
    {
            $em = $this->_em;
            $exist = $this->findOneBy(array('businessConfig' => $invoice->getBusinessConfig(),'salesInvoice'=> $invoice->getInvoice()));
             $returnItemCount = $this->_em->getRepository('BusinessBundle:BusinessInvoice')->updateInvoiceDistributionTotalPrice($invoice);
            if($exist){
                $this->insertUpdatePurchaseReturnItem($exist,$invoice);
            }elseif($returnItemCount['damageQnt'] > 0 or $returnItemCount['spoilQnt'] > 0){
                $entity = new BusinessPurchaseReturn();
                $entity->setBusinessConfig($invoice->getBusinessConfig());
                if($invoice->getVendor()){
                    $entity->setVendor($invoice->getVendor());
                }
                $entity->setSalesInvoice($invoice->getInvoice());
                $entity->setCreatedBy($invoice->getCreatedBy());
                $amount = $em->getRepository('BusinessBundle:BusinessInvoiceParticular')->salesDamageProductAmount($invoice);
                $entity->setSubTotal($amount);
                $entity->setProcess('sales');
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

                $item->getId();
                $exist = $em->getRepository('BusinessBundle:BusinessPurchaseReturnItem')->findOneBy(array('businessPurchaseReturn' => $entity,'salesInvoiceItem' => $item->getId()));

                /* @var $purchaseItem BusinessPurchaseReturnItem */

                if($exist){
                    $purchaseItem = $exist;
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
                }elseif($item->getDamageQnt() > 0 or $item->getSpoilQnt() ) {
                    $purchaseItem = new BusinessPurchaseReturnItem();
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
                    $em->getRepository('BusinessBundle:BusinessParticular')->updateRemoveStockQuantity($item->getBusinessParticular(), "purchase-return");
                }
        endforeach;
    }

}
