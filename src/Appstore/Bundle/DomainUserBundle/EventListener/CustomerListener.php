<?php

namespace Appstore\Bundle\DomainUserBundle\EventListener;

use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Doctrine\ORM\Event\LifecycleEventArgs;

class CustomerListener
{
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->createCode($args);
    }

    public function createCode(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        // perhaps you only want to act on some "Purchase" entity
        if ($entity instanceof Customer) {

             $lastCode = $this->getLastCode($args,$entity);

            $entity->setCode($lastCode+1);
            $entity->setCustomerId(sprintf("%s%s",$entity->getGlobalOption()->getId(),str_pad($entity->getCode(),6, '0', STR_PAD_LEFT)));
        }
    }

    /**
     * @param LifecycleEventArgs $args
     * @param $datetime
     * @param $entity
     * @return int|mixed
     */
    public function getLastCode(LifecycleEventArgs $args,$entity)
    {

        $entityManager = $args->getEntityManager();
        $qb = $entityManager->getRepository('DomainUserBundle:Customer')->createQueryBuilder('s');

        $qb
            ->select('MAX(s.code)')
            ->where('s.inventoryConfig = :inventory')
            ->setParameter('inventory', $entity->getInventoryConfig());
            $lastCode = $qb->getQuery()->getSingleScalarResult();

        if (empty($lastCode)) {
            return 0;
        }
        return $lastCode;
    }
}