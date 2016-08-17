<?php

namespace Appstore\Bundle\EcommerceBundle\Repository;
use Appstore\Bundle\EcommerceBundle\Entity\PreOrder;
use Doctrine\ORM\EntityRepository;

/**
 * PreOrderRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PreOrderRepository extends EntityRepository
{
    public function updatePreOder(PreOrder $entity)
    {
        $em = $this->_em;
        $total = $em->createQueryBuilder()
            ->from('EcommerceBundle:PreOrderItem','e')
            ->select('sum(e.total) as total , sum(e.totalDollar) as dollar , sum(e.shippingCharge) as shippingCharge , count(e.id) as item, sum(e.quantity) as quantity')
            ->where('e.preOrder = :preOrder')
            ->andWhere('e.status = :status')
            ->setParameter('preOrder', $entity ->getId())
            ->setParameter('status', 1)
            ->getQuery()->getSingleResult();

        $entity->setTotal($total['total']);
        $entity->setDollar($total['dollar']);
        $entity->setShippingCharge($total['shippingCharge']);
        $entity->setItem($total['item']);
        $entity->setQuantity($total['quantity']);
        $entity->setGrandTotal($total['total'] + $total['shippingCharge']);
        $em->persist($entity);
        $em->flush();
    }
}
