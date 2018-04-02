<?php

namespace Appstore\Bundle\InventoryBundle\Repository;
use Appstore\Bundle\InventoryBundle\Entity\Reverse;
use Appstore\Bundle\InventoryBundle\Entity\Sales;
use Doctrine\ORM\EntityRepository;


/**
 * ReverseRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ReverseRepository extends EntityRepository
{
    public function insertSales(Sales $entity,$data)
    {
        if(empty($entity->getReverse())){
            $reverse = New Reverse();
        }else{
            $reverse = $entity->getReverse();
        }
        $reverse->setName('Sales');
        $reverse->setInventoryConfig($entity->getInventoryConfig());
        $reverse->setProcess('Sales');
        $reverse->setContent($data);
        $reverse->setSales($entity);
        $this->_em->persist($reverse);
        $this->_em->flush($reverse);

    }

}
