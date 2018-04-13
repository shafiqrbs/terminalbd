<?php

namespace Setting\Bundle\ToolBundle\Repository;
use Doctrine\ORM\EntityRepository;

/**
 * ProductUnitRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProductUnitRepository extends EntityRepository
{
    public function searchAutoComplete($inventory,$q)
    {
        $query = $this->createQueryBuilder('e');
        $query->select('e.name as id');
        $query->addSelect('e.name as text');
        $query->where($query->expr()->like("e.name", "'$q%'"  ));
        $query->groupBy('e.id');
        $query->orderBy('e.name', 'ASC');
        $query->setMaxResults( '10' );
        return $query->getQuery()->getResult();

    }
}
