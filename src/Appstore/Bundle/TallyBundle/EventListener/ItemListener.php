<?php

namespace Appstore\Bundle\TallyBundle\EventListener;

use Appstore\Bundle\TallyBundle\Entity\Item;
use Doctrine\ORM\Event\LifecycleEventArgs;

class ItemListener
{
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->createCode($args);
    }

    public function createCode(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        // perhaps you only want to act on some "Purchase" entity
        if ($entity instanceof Item) {

            $lastCode = $this->getLastCode($args, $entity);
            $entity->setCode($lastCode+1);
            $entity->setSku(sprintf("%s", str_pad($entity->getCode(),4, '0', STR_PAD_LEFT)));
        }
    }

    /**
     * @param LifecycleEventArgs $args
     * @param $datetime
     * @param $entity
     * @return int|mixed
     */
    public function getLastCode(LifecycleEventArgs $args, Item $entity)
    {

        $entityManager = $args->getEntityManager();
        $qb = $entityManager->getRepository('TallyBundle:Item')->createQueryBuilder('s');
        $qb
            ->select('MAX(s.code)')
	        ->where('s.globalOption = :option')->setParameter('option', $entity->getGlobalOption()->getId());
        $lastCode = $qb->getQuery()->getSingleScalarResult();
        if (empty($lastCode)) {
            return 0;
        }
        return $lastCode;
    }
}