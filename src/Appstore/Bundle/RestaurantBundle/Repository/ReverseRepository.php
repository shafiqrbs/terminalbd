<?php

namespace Appstore\Bundle\RestaurantBundle\Repository;
use Appstore\Bundle\RestaurantBundle\Entity\Invoice;
use Appstore\Bundle\RestaurantBundle\Entity\Reverse;
use Doctrine\ORM\EntityRepository;


/**
 * HmsReverseRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ReverseRepository extends EntityRepository
{
    public function insertInvoice(Invoice $entity,$data)
    {
        if(empty($entity->getReverse())){
            $reverse = New Reverse();
        }else{
            $reverse = $entity->getReverse();
        }
        $reverse->setName('Invoice');
        $reverse->setRestaurantConfig($entity->getRestaurantConfig());
        $reverse->setProcess('Invoice');
        $reverse->setContent($data);
        $reverse->setInvoice($entity);
        $this->_em->persist($reverse);
        $this->_em->flush($reverse);

    }


}
