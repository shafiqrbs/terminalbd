<?php

namespace Appstore\Bundle\DomainUserBundle\Repository;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * DomainUserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DomainUserRepository extends EntityRepository
{
    public function updateDomainUser($entity)
    {
        $em = $this->_em;

        $user       =  $entity->getUser();
        $profile    =  $entity->getProfile();

        $user->setDomainUser($entity);
        $user->setEnabled(true);
        $user->setRoles(array($entity->getRole()));
        $em->persist($user);
        $profile->setUser($user);
        $profile->setDomainUser($entity);
        $em->flush();

    }

    public function updateSalesTarget( User $user,$month = 0 , $year =0)
    {
        $em = $this->_em;
        $entity = $this->findOneBy(array('user' => $user));
        $entity->setMonthlySales($month);
        $entity->setYearlySales($year);
        $em->persist($entity);
        $em->flush();
    }


    public function getSalesUser(GlobalOption $option)
    {
       $qb = $this->createQueryBuilder('e');
       $qb->join('e.user','u');
       $qb->select('e.sales');
       $qb->addSelect('e.monthlySales');
       $qb->addSelect('e.yearlySales');
       $qb->addSelect('u.id as id');
       $qb->addSelect('u.username as username');
       $qb->where('e.globalOption='.$option->getId());
       $qb->andWhere('e.monthlySales IS NOT NULL');
       $qb->andWhere('e.yearlySales IS NOT NULL');
       $result = $qb->getQuery()->getArrayResult();
       return $result;
    }
}
