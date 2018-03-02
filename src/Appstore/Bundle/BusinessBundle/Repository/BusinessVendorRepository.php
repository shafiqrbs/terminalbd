<?php

namespace Appstore\Bundle\BusinessBundle\Repository;
use Appstore\Bundle\BusinessBundle\Entity\BusinessConfig;
use Doctrine\ORM\EntityRepository;


/**
 * HmsVendorRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BusinessVendorRepository extends EntityRepository
{
    public function searchAutoComplete($q, BusinessConfig $config)
    {
        $query = $this->createQueryBuilder('e');
        $query->join('e.hospitalConfig', 'ic');
        $query->select('e.companyName as id');
        $query->addSelect('e.companyName as text');
        $query->where($query->expr()->like("e.companyName", "'$q%'"  ));
        $query->andWhere("ic.id = :config");
        $query->setParameter('config', $config->getId());
        $query->groupBy('e.id');
        $query->orderBy('e.companyName', 'ASC');
        $query->setMaxResults( '30' );
        return $query->getQuery()->getResult();

    }

}
