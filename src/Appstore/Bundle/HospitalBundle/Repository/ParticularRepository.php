<?php

namespace Appstore\Bundle\HospitalBundle\Repository;
use Doctrine\ORM\EntityRepository;


/**
 * PathologyRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ParticularRepository extends EntityRepository
{
    public function findWithSearch($config,$service, $data){

        $name = isset($data['name'])? $data['name'] :'';
        $category = isset($data['category'])? $data['category'] :'';
        $department = isset($data['department'])? $data['department'] :'';

        $qb = $this->createQueryBuilder('e');
        $qb->where('e.hospitalConfig = :config')->setParameter('config', $config) ;
        $qb->andWhere('e.service = :service')->setParameter('service', $service) ;
        if (!empty($name)) {
            $qb->andWhere($qb->expr()->like("e.name", "'%$name%'"  ));
        }
        if(!empty($category)){
            $qb->andWhere("e.category = :category");
            $qb->setParameter('category', $category);
        }
        if(!empty($department)){
            $qb->andWhere("e.department = :department");
            $qb->setParameter('department', $department);
        }
        $qb->orderBy('e.name','ASC');
        $qb->getQuery();
        return  $qb;
    }

}
