<?php

namespace Core\UserBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends EntityRepository
{
    public function getAll()
    {
        return $this->findAll();
    }

    public function create($data)
    {
        $this->_em->persist($data);
        $this->_em->flush();
    }

    public function delete($data)
    {
        $this->_em->remove($data);
        $this->_em->flush();
    }

    public function update($data)
    {
        $this->_em->persist($data);
        $this->_em->flush();
        return $this->_em;
    }

    public function searchAutoComplete($q, GlobalOption $globalOption)
    {
        $query = $this->createQueryBuilder('e');

        $query->select('e.username as id');
        $query->addSelect('e.username as text');
        $query->where($query->expr()->like("e.username", "'$q%'"  ));
        $query->andWhere("e.globalOption = :globalOption");
        $query->setParameter('globalOption', $globalOption->getId());
        $query->groupBy('e.id');
        $query->orderBy('e.username', 'ASC');
        $query->setMaxResults( '10' );
        return $query->getQuery()->getResult();

    }
}
