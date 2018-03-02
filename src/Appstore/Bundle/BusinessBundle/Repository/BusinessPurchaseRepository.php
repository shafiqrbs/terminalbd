<?php

namespace Appstore\Bundle\BusinessBundle\Repository;
use Appstore\Bundle\BusinessBundle\Entity\BusinessPurchase;
use Doctrine\ORM\EntityRepository;


/**
 * BusinessPurchaseRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BusinessPurchaseRepository extends EntityRepository
{

    public function updatePurchaseTotalPrice(BusinessPurchase $entity)
    {
        $em = $this->_em;
        $total = $em->createQueryBuilder()
            ->from('BusinessBundle:BusinessPurchaseItem','si')
            ->select('sum(si.purchaseSubTotal) as total')
            ->where('si.dmsPurchase = :entity')
            ->setParameter('entity', $entity ->getId())
            ->getQuery()->getSingleResult();

        if($total['total'] > 0){

            $entity->setSubTotal($total['total']);
            $entity->setNetTotal($entity->getSubTotal() - $entity->getDiscount());
            $entity->setDue($entity->getNetTotal() - $entity->getPayment() );

        }else{

            $entity->setSubTotal(0);
            $entity->setNetTotal(0);
            $entity->setDue(0);
            $entity->setDiscount(0);
        }

        $em->persist($entity);
        $em->flush();

        return $entity;

    }

}
